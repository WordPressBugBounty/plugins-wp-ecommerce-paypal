<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpecpp_stripe_connection_status() {
	global $wpecppStripeConnectionStatus;

	if ( !isset( $wpecppStripeConnectionStatus ) ) {
		$wpecppStripeConnectionStatus = false;

        $options = wpecpp_free_options();
        $mode = intval( $options['mode_stripe'] ) === 2 ? 'live' : 'sandbox';
        $account_id = $options['acct_id_' . $mode];
        $token = $options['stripe_connect_token_' . $mode];

		if ( !empty( $account_id ) ) {
			$url = WPECPP_FREE_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query(
				[
					'action' => 'checkStatus',
					'mode' => $mode,
					'account_id' => $account_id,
					'token' => $token
				]
			);

			$args = [
				'timeout' => 30,
				'headers' => [
					'Referer' => site_url( $_SERVER['REQUEST_URI'] )
				]
			];

			$response = wp_remote_get( $url, $args );

			if ( is_wp_error( $response ) ) {
				return $wpecppStripeConnectionStatus;
			}

			$body = wp_remote_retrieve_body( $response );
			if ( empty( $body ) ) {
				return $wpecppStripeConnectionStatus;
			}

			$account = json_decode( $body, true );
			if ( !is_array( $account ) || !isset( $account['payouts_enabled'] ) ) {
				return $wpecppStripeConnectionStatus;
			}

			if ( intval( $account['payouts_enabled'] ) === 1 ) {
				$wpecppStripeConnectionStatus = [
					'email' => $account['email'],
					'display_name' => $account['display_name'],
					'mode' => $mode,
					'account_id' => $account_id,
					'token' => $token
				];
			}
		}
	}

	return $wpecppStripeConnectionStatus;
}

function wpecpp_stripe_connection_status_html() {
	$connected = wpecpp_stripe_connection_status();

	if ( $connected ) {
		$reconnect_mode = $connected['mode'] === 'sandbox' ? 'live' : 'sandbox';

        // Build the status message in a clearer way
        $account_info = sprintf(
            '<div class="notice inline notice-success wpecpp-stripe-connect">
                <p><strong>%s</strong><br>%s — %s</p>
                <p>%s</p>
            </div>',
            esc_html( $connected['display_name'] ),
            esc_html( $connected['email'] ),
            esc_html__( 'Administrator (Owner)', 'wp-ecommerce-paypal' ),
            esc_html__( 'Pay as you go pricing: 2% per-transaction fee + Stripe fees.', 'wp-ecommerce-paypal' )
        );
        
        $connection_status = sprintf(
            '<div>%s <strong>%s</strong> %s, %s <a href="%s">%s</a> %s <a href="%s">%s</a>.</div>',
            esc_html__( 'Your Stripe account is connected in', 'wp-ecommerce-paypal' ),
            esc_html( $connected['mode'] ),
            esc_html__( 'mode', 'wp-ecommerce-paypal' ),
            esc_html__( 'Connect in', 'wp-ecommerce-paypal' ),
            esc_url( wpecpp_stripe_connect_url( $reconnect_mode ) ),
            esc_html( $reconnect_mode ),
            esc_html__( 'or', 'wp-ecommerce-paypal' ),
            esc_url( wpecpp_stripe_disconnect_url( $connected['account_id'], $connected['token'] ) ),
            esc_html__( 'disconnect this account', 'wp-ecommerce-paypal' )
        );
        
        $result = $account_info . $connection_status;
		


		if ( empty( $connected['email'] ) ) {
			$result .= '<p>
                <strong>' . esc_html__( 'Please review the warnings below and resolve them in your account settings or by contacting support.', 'wp-ecommerce-paypal' ) . '</strong>
            </p>
            <ul class="ppcp-list ppcp-list-warning">
                <li>' . esc_html__( 'Can\'t read connected account email address', 'wp-ecommerce-paypal' ) . '</li>
            </ul>';
		}
	} else {
		$result = sprintf(
			'<a href="%s"" class="stripe-connect-btn">
				<span>' . __( 'Connect with Stripe', 'wp-ecommerce-paypal' ) . '</span>
			</a>
			<br />
			<br />
			' . __( 'You only pay the standard Stripe fees + 2%%. Have questions about connecting with Stripe?
			Please see the', 'wp-ecommerce-paypal' ) . ' <a target="_blank" href="https://wpplugin.org/documentation/stripe-connect/">' . __( 'documentation', 'wp-ecommerce-paypal' ) . '</a>.',
			wpecpp_stripe_connect_url()
		);
	}

	return $result;
}

function wpecpp_stripe_connect_url( $mode = false ) {
	if ( $mode === false ) {
        $options = wpecpp_free_options();
        $mode = intval( $options['mode_stripe'] ) === 2 ? 'live' : 'sandbox';
	}

	return WPECPP_FREE_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query(
		[
			'action' => 'connect',
			'mode' => $mode,
			'return_url' => wpecpp_stripe_connect_tab_url()
		]
	);
}

function wpecpp_stripe_disconnect_url( $account_id, $token ) {
	$options = wpecpp_free_options();
    $mode = intval( $options['mode_stripe'] ) === 2 ? 'live' : 'sandbox';

	return WPECPP_FREE_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query(
		[
			'action' => 'disconnect',
			'mode' => $mode,
			'return_url' => wpecpp_stripe_connect_tab_url(),
			'account_id' => $account_id,
			'token' => $token
		]
	);
}

function wpecpp_stripe_connect_tab_url() {
    $args =  [
        'page' => 'wpecpp-settings',
        'tab' => '4',
		'wpecpp_free_nonce' 	=> wp_create_nonce('wpecpp_free_stripe')
    ];

	return add_query_arg(
        $args,
        admin_url( 'admin.php' )
    );
}

add_action( 'plugins_loaded', 'wpecpp_stripe_connect_completion' );
function wpecpp_stripe_connect_completion() {
	if ( empty( $_GET['wpecpp_stripe_connect_completion'] ) ||
		intval( $_GET['wpecpp_stripe_connect_completion'] ) !== 1 ||
		empty( $_GET['mode'] ) ||
		empty( $_GET['account_id'] ) ||
		empty( $_GET['token'] ) ||
		!current_user_can( 'manage_options' ) ) return;
		
		
	// nonce check
	if (!isset($_GET['wpecpp_free_nonce']) || !wp_verify_nonce($_GET['wpecpp_free_nonce'], 'wpecpp_free_stripe')) {
		wp_die(__('Security check failed', 'wp-ecommerce-paypal'));
	}


	$options = wpecpp_free_options();
    $mode = $_GET['mode'] === 'live' ? 'live' : 'sandbox';
	$mode_stripe = $mode === 'live' ? 2 : 1;
    $options['acct_id_' . $mode] = sanitize_text_field( $_GET['account_id'] );
    $options['stripe_connect_token_' . $mode] = sanitize_text_field( $_GET['token'] );
    $options['mode_stripe'] = $mode_stripe;
	$options['stripe_connect_notice_dismissed'] = 0;
	wpecpp_free_options_update( $options );

	$return_url = wpecpp_stripe_connect_tab_url();

	/**
	 * Filters the URL users are returned to after Stripe connect completed
	 *
	 * @since 1.7.4
	 *
	 * @param $return_url URL to return to.
	 */
	$return_url = apply_filters( 'wpecpp_stripe_connect_return_url', $return_url );

	wp_redirect( $return_url );
}

add_action( 'plugins_loaded', 'wpecpp_stripe_disconnected' );
function wpecpp_stripe_disconnected() {
	if ( empty( $_GET['wpecpp_stripe_disconnected'] ) ||
		intval( $_GET['wpecpp_stripe_disconnected'] ) !== 1 ||
		empty( $_GET['mode'] ) ||
		empty( $_GET['account_id'] ) ||
		!current_user_can( 'manage_options' ) ) return;

	// nonce check
	if (!isset($_GET['wpecpp_free_nonce']) || !wp_verify_nonce($_GET['wpecpp_free_nonce'], 'wpecpp_free_stripe')) {
		wp_die(__('Security check failed', 'wp-ecommerce-paypal'));
	}


	$options = wpecpp_free_options();
    $mode = $_GET['mode'] === 'live' ? 'live' : 'sandbox';
	$mode_stripe = $mode === 'live' ? 2 : 1;
    if ( $options['acct_id_' . $mode] === $_GET['account_id'] ) {
	    $options['acct_id_' . $mode] = '';
	    $options['stripe_connect_token_' . $mode] = '';
        $options['mode_stripe'] = $mode_stripe;
	    wpecpp_free_options_update( $options );
    }

	$return_url = wpecpp_stripe_connect_tab_url();

	/**
	 * Filters the URL users are returned to after Stripe disconnect completed
	 *
	 * @since 2.1.11
	 *
	 * @param $return_url URL to return to.
	 */
	$return_url = apply_filters( 'wpecpp_stripe_disconnect_return_url', $return_url );

	wp_redirect( $return_url );
}

/**
 * Create Stripe checkout session
 * @since 1.7.4
 */
add_action( 'wp_ajax_wpecpp_stripe_checkout_session', 'wpecpp_stripe_checkout_session' );
add_action( 'wp_ajax_nopriv_wpecpp_stripe_checkout_session', 'wpecpp_stripe_checkout_session' );
function wpecpp_stripe_checkout_session() {
	if ( !wp_verify_nonce( $_POST['nonce'], 'wpecpp-frontend-request' ) ) {
		wp_send_json_error( [
			'message' => esc_html__( 'Security error. The payment has not been made. Please reload the page and try again.', 'wp-ecommerce-paypal' )
		] );
	}

	parse_str( $_POST['data'], $data );

	$stripe_account_data = wpecpp_stripe_account_data();
	if ( empty( $stripe_account_data ) ) {
		wp_send_json_error( [
			'message' => esc_html__( 'Stripe connection error. Please contact the site administrator.', 'wp-ecommerce-paypal' )
		] );
	}

	$options = wpecpp_free_options();

	$currency = strtolower( wpecpp_currency_code_to_iso( $options['currency'] ) );
	$unit_amount = wpecpp_amount_to_stripe_unit_amount( $data['amount'], $currency );
	$name = sanitize_text_field( $data['item_name'] );

	$line_items = [
		[
			'price_data' => [
				'currency' => $currency,
				'unit_amount' => $unit_amount,
				'product_data' => [
					'name' => $name
				],
			],
			'quantity' => isset($data['quantity']) && intval($data['quantity']) > 0 ? intval($data['quantity']) : 1
		]
	];

	$current_url = sanitize_url( $_POST['location'] );
	$success_url = !empty( $options['return'] ) ?
		$options['return'] :
		add_query_arg(
			[
				'wpecpp_stripe_success' => 1
			],
			$current_url
		);
    $cancel_url = !empty( $options['cancel'] ) ?
	    $options['cancel'] :
	    add_query_arg(
		    [
			    'wpecpp_stripe_success' => 0,
			    'payment_cancelled' => 1
		    ],
		    $current_url
	    );

	$stripe_connect_url = WPECPP_FREE_STRIPE_CONNECT_ENDPOINT . '?' . http_build_query( [
		'action' 						=> 'checkoutSession',
		'mode' 							=> $stripe_account_data['mode'],
		'billing_address_collection' 	=> 'auto',
		'line_items' 					=> $line_items,
		'success_url' 					=> $success_url,
		'cancel_url' 					=> $cancel_url,
		'notice_url'					=> '',
		'account_id' 					=> $stripe_account_data['account_id'],
		'token' 						=> $stripe_account_data['token']
	] );

	$response = wp_remote_get( $stripe_connect_url, [
		'timeout' => 30,
		'headers' => [
			'Referer' => site_url( $_SERVER['REQUEST_URI'] )
		]
	] );

	$body 				= wp_remote_retrieve_body( $response );
	$checkout_session 	= json_decode( $body );

	if ( empty( $checkout_session->session_id ) || empty( $checkout_session->stripe_key ) ) {
		wp_send_json_error( [
			'message' => !empty( $checkout_session->message ) ? $checkout_session->message : esc_html__( 'An unexpected error occurred. Please try again.', 'wp-ecommerce-paypal' )
		] );
	}

	wp_send_json_success( [
		'sessionId' => $checkout_session->session_id,
		'stripeKey' => $checkout_session->stripe_key,
		'accountId' => $stripe_account_data['account_id']
	] );
}

/**
 * Open Stripe checkout in new tab
 */
add_action( 'init', 'wpecpp_open_stripe_checkout_in_new_tab' );
function wpecpp_open_stripe_checkout_in_new_tab() {
	if ( !empty( $_GET['wpecpp-stripe-checkout-redirect'] ) &&
		!empty( $_GET['sk'] ) &&
		!empty( $_GET['ai'] ) &&
		!empty( $_GET['si'] ) &&
		!empty( $_GET['rf'] )
	) {
        ?>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            try {
                const stripe = Stripe('<?php echo sanitize_text_field($_GET['sk']); ?>', {
                    stripeAccount: '<?php echo sanitize_text_field($_GET['ai']); ?>'
                });
                stripe.redirectToCheckout({
                    sessionId: '<?php echo sanitize_text_field($_GET['si']); ?>'
                });
            } catch (error) {
            }
        </script>
        <?php
		die();
	}
}

function wpecpp_amount_to_stripe_unit_amount( $amount, $currency ) {
    // Zero-decimal currencies
    $zero_decimal_currencies = [
	    'BIF',
        'CLP',
        'DJF',
        'GNF',
        'JPY',
        'KMF',
        'KRW',
        'MGA',
        'PYG',
        'RWF',
        'UGX',
        'VND',
        'VUV',
        'XAF',
        'XOF',
        'XPF'
    ];

    $amount = floatval( $amount );
    if ( !in_array( strtoupper( $currency ), $zero_decimal_currencies ) ) {
	    $amount = $amount * 100;
    }

    return round( $amount );
}