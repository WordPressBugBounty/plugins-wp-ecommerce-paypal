<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'wp_ajax_wpecpp-ppcp-order-create', 'wpecpp_ppcp_order_create_ajax' );
add_action( 'wp_ajax_nopriv_wpecpp-ppcp-order-create', 'wpecpp_ppcp_order_create_ajax' );
function wpecpp_ppcp_order_create_ajax() {
	if ( !wp_verify_nonce( $_POST['nonce'], 'wpecpp-frontend-request' ) ) {
		wp_send_json_error( [
			'message' => __( 'The request has not been authenticated. Please reload the page and try again.' )
		] );
	}

	$options = wpecpp_free_options();
	$mode = intval( $options['mode'] );
	$env = $mode === 1 ? 'sandbox' : 'live';
	$onboarding = isset( $options['ppcp_onboarding'][$env] ) ? $options['ppcp_onboarding'][$env] : [];

	$response = wp_remote_post(
		WPECPP_FREE_PPCP_API . 'create-order',
		[
			'timeout' => 60,
			'body' => [
				'env' => $env,
				'seller_id' => $onboarding['seller_id'],
				'items' => [
					[
						'name' => sanitize_text_field( $_POST['name'] ),
						'price' => floatval( $_POST['price'] )
					]
				],
				'currency' => wpecpp_currency_code_to_iso( $options['currency'] ),
				'intent' => 'capture',
				'address' => $options['address']
			]
		]
	);

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( empty( $data['success'] ) ) {
		wp_send_json_error( [
			'message' => !empty( $data['message'] ) ? $data['message'] : __( "Can't create an order." )
		] );
	}

	wp_send_json_success( [
		'order_id' => $data['order_id']
	] );
}

add_action( 'wp_ajax_wpecpp-ppcp-order-finalize', 'wpecpp_ppcp_order_finalize_ajax' );
add_action( 'wp_ajax_nopriv_wpecpp-ppcp-order-finalize', 'wpecpp_ppcp_order_finalize_ajax' );
function wpecpp_ppcp_order_finalize_ajax() {
	if ( !wp_verify_nonce( $_POST['nonce'], 'wpecpp-frontend-request' ) ) {
		wp_send_json_error( [
			'message' => __( 'The request has not been authenticated. Please reload the page and try again.' )
		] );
	}

	$options = wpecpp_free_options();
	$mode = intval( $options['mode'] );
	$env = $mode === 1 ? 'sandbox' : 'live';
	$onboarding = isset( $options['ppcp_onboarding'][$env] ) ? $options['ppcp_onboarding'][$env] : [];

	$response = wp_remote_post(
		WPECPP_FREE_PPCP_API . 'finalize-order',
		[
			'timeout' => 60,
			'body' => [
				'env' => $env,
				'seller_id' => $onboarding['seller_id'],
				'order_id' => sanitize_text_field( $_POST['order_id'] ),
				'intent' => 'capture',
				'acdc' => !empty( $_POST['acdc'] )
			]
		]
	);

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	$payment_status = empty( $data['success'] ) ? 'failed' : 'completed';
	$payer_email = !empty( $data['payer_email'] ) ? $data['payer_email'] : '';
	do_action( 'wpecpp_ppcp_order_finalize', $payment_status, $payer_email );

	if ( empty( $data['success'] ) ) {
		wp_send_json_error( $data );
	}

	wp_send_json_success( $data );
}