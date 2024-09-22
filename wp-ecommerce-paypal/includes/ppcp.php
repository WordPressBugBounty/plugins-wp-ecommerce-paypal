<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wpecpp_ppcp_status() {
	global $wpecppPpcpStatus;

	if ( !isset( $wpecppPpcpStatus ) ) {
		$wpecppPpcpStatus = false;

		$options = wpecpp_free_options();
		$mode = intval( $options['mode'] );
		$env = $mode === 1 ? 'sandbox' : 'live';
        $onboarding = isset( $options['ppcp_onboarding'][$env] ) ? $options['ppcp_onboarding'][$env] : [];

		if ( !empty( $onboarding['seller_id'] ) ) {
			$args = [
				'env' => $env,
				'onboarding' => $onboarding
			];
			$transient = md5( json_encode( $args ) );
			$wpecppPpcpStatus = get_transient( $transient );
			if ( $wpecppPpcpStatus === false ) {
				$response = wp_remote_get( WPECPP_FREE_PPCP_API . 'get-status?' . http_build_query( $args ) );
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );
				if ( is_array( $data ) && !empty( $data['mode'] ) ) {
					set_transient( $transient, $data, HOUR_IN_SECONDS );
					$wpecppPpcpStatus = $data;
				}
			}
		} elseif ( !empty( $onboarding ) ) {
			$response = wp_remote_get( WPECPP_FREE_PPCP_API . 'find-seller-id?' . http_build_query( [
                'env' => $env,
                'onboarding' => $onboarding
            ] ) );
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
            if ( is_array( $data ) && !empty( $data['mode'] ) ) {
                wpecpp_ppcp_onboarding_save( sanitize_text_field( $data['env'] ), sanitize_text_field( $data['seller_id'] ) );
                $wpecppPpcpStatus = $data;
            } elseif ( $onboarding['timestamp'] + 3600 < time() ) {
	            unset( $options['ppcp_onboarding'][$env] );
	            wpecpp_free_options_update( $options );
            }
		}
	}

	return $wpecppPpcpStatus;
}

add_action( 'wp_ajax_wpecpp-ppcp-onboarding-start', 'wpecpp_ppcp_onboarding_start_ajax' );
function wpecpp_ppcp_onboarding_start_ajax() {
	if ( !wp_verify_nonce( $_GET['nonce'], 'ppcp-onboarding-start' ) ) {
		echo '<script>window.close();</script>';
		die();
	}

    $env = !empty( $_GET['sandbox'] ) ? 'sandbox' : 'live';
    $country = sanitize_text_field( $_GET['country'] );
	$accept_cards = !empty( $_GET['accept-cards'] ) ? 1 : 0;

	$response = wp_remote_post(
		WPECPP_FREE_PPCP_API . 'signup',
        [
	        'timeout' => 60,
	        'body' => [
                'env' => $env,
		        'return_url' => wpecpp_ppcp_connect_tab_url(),
		        'email' => get_bloginfo( 'admin_email' ),
                'country' => $country,
                'accept_cards' => $accept_cards
            ]
        ]
    );

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( empty( $data['action_url'] ) || empty( $data['tracking_id'] ) ) {
		echo '<script>window.close();</script>';
        die();
	}

	$options = wpecpp_free_options();
    $options['ppcp_onboarding'][$env] = [
        'timestamp' => time(),
        'tracking_id' => $data['tracking_id'],
        'country' => $country,
        'accept_cards' => $accept_cards,
        'seller_id' => ''
    ];

	$options['mode'] = $env === 'sandbox' ? '1' : '2';

	wpecpp_free_options_update( $options );

    header( "Location: {$data['action_url']}" );
    die();
}

function wpecpp_ppcp_connect_tab_url() {
    return add_query_arg(
        [
            'page' => 'wpecpp-settings',
            'tab' => '3'
        ],
        admin_url('admin.php')
    );
}

function wpecpp_ppcp_onboarding_save( $env, $seller_id ) {
    $options = wpecpp_free_options();

    if ( $env === 'sandbox' && isset( $options['sandboxaccount'] ) ) {
        unset( $options['sandboxaccount'] );
    } elseif ( $env === 'live' && isset( $options['liveaccount'] ) ) {
	    unset( $options['liveaccount'] );
    }

	$options['ppcp_onboarding'][$env]['seller_id'] = $seller_id;
	$options['ppcp_notice_dismissed'] = 0;

	wpecpp_free_options_update( $options );
}

add_action( 'wp_ajax_wpecpp-ppcp-disconnect', 'wpecpp_ppcp_disconnect_ajax' );
function wpecpp_ppcp_disconnect_ajax() {
	if ( !wp_verify_nonce( $_POST['nonce'], 'wpecpp-request' ) ) {
		wp_send_json_error( [
			'message' => __( 'The request has not been authenticated. Please reload the page and try again.' )
		] );
	}

	$options = wpecpp_free_options();
	$mode = intval( $options['mode'] );
	$env = $mode === 1 ? 'sandbox' : 'live';
	$onboarding = isset( $options['ppcp_onboarding'][$env] ) ? $options['ppcp_onboarding'][$env] : [];

	if ( empty( $onboarding ) ) {
		wp_send_json_error( [
			'message' => __( 'An error occurred while processing your account disconnection request. Please contact our support service.' )
		] );
	}

	$args = [
		'env' => $env,
		'onboarding' => $onboarding
	];

	$response = wp_remote_post(
		WPECPP_FREE_PPCP_API . 'disconnect',
		[
			'timeout' => 60,
			'body' => $args
		]
	);

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( empty( $data['success'] ) ) {
		wp_send_json_error( [
			'message' => __( 'An error occurred while processing your account disconnection request. Please contact our support service.' )
		] );
	}

	unset( $options['ppcp_onboarding'][$env] );
	wpecpp_free_options_update( $options );

	$transient = md5( json_encode( $args ) );
	delete_transient( $transient );

    ob_start();
	wpecpp_ppcp_status_markup();
    $html = ob_get_clean();

	wp_send_json_success( [
        'statusHtml' => $html
    ] );
}