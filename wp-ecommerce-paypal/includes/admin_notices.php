<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Show admin activation notice
 * @since 1.7.4
 */
add_action( 'admin_notices', 'wpecpp_admin_activation_notice' );
function wpecpp_admin_activation_notice() {
	$options = wpecpp_free_options();
	if ( empty( $options['activation_notice_shown'] ) ) {
		echo '<div class="updated">
			<p>
				<a href="admin.php?page=wpecpp-settings">' . __( 'Click here to view the plugin settings' ) . '</a>.
			</p>
		</div>';
		$options['activation_notice_shown'] = 1;
		wpecpp_free_options_update( $options );
	}
}

/**
 * Show admin notice for Stripe Connect.
 * @since 1.7.4
 */
add_action( 'admin_notices', 'wpecpp_admin_stripe_connect_notice' );
function wpecpp_admin_stripe_connect_notice() {
	$options = wpecpp_free_options();
	$mode = intval( $options['mode_stripe'] ) === 2 ? 'live' : 'sandbox';
	$acct_id_key = 'acct_id_' . $mode;

	if ( !empty( $options[$acct_id_key] ) || !empty( $options['stripe_connect_notice_dismissed'] )  ||
		( isset( $_GET['page'] ) && $_GET['page'] == 'wpecpp-settings' && isset( $_GET['tab'] ) && $_GET['tab'] == 4 ) ) return;

	printf(
		'<div class="notice notice-error is-dismissible wpecpp-stripe-connect-notice" data-dismiss-url="%s">
			<p>%s</p>
			<p><a href="%s" class="stripe-connect-btn"><span>Connect with Stripe</span></a></p>
			<br />WPPlugin LLC is an offical Stripe Partner. Pay as you go pricing: 2%% per-transaction fee + Stripe fees.
		</div>',
		add_query_arg( 'wpecpp_admin_stripe_connect_notice_dismiss', 1, admin_url() ),
		__( '<b>Important</b> - \'Easy PayPal & Stripe Button\' now uses Stripe Connect.
		Stripe Connect improves security and allows for easier setup. <br /><br />If you use Stripe, please use Stripe Connect. Have questions: see the <a target="_blank" href="https://wpplugin.org/documentation/stripe-connect/">documentation</a>.
		' ),
		wpecpp_stripe_connect_url(),
		
	);
}

/**
 * Dismiss admin notice for Stripe Connect.
 * @since 1.7.4
 */
add_action( 'admin_init', 'wpecpp_admin_stripe_connect_notice_dismiss' );
function wpecpp_admin_stripe_connect_notice_dismiss() {
	if ( empty( $_GET['wpecpp_admin_stripe_connect_notice_dismiss'] ) ) return;

	$options = wpecpp_free_options();
	$options['stripe_connect_notice_dismissed'] = 1;
	wpecpp_free_options_update( $options );
	die();
}

/**
 * Stripe Connect error notice.
 * @since 1.7.4
 */
add_action( 'admin_notices', 'wpecpp_admin_stripe_connect_error_notice' );
function wpecpp_admin_stripe_connect_error_notice() {
	if ( empty( $_GET['wpecpp_error'] ) || $_GET['wpecpp_error'] != 'stripe-connect-handler' ) return;

	printf(
		'<div class="notice notice-error is-dismissible">
			<p>%s</p>
		</div>',
		__( 'An error occurred while interacting with our Stripe Connect interface. Please notify the author of the plugin.' )
	);
}

/**
 * Show admin notice for PayPal Commerce Platform.
 * @since 1.7.4
 */
add_action( 'admin_notices', 'wpecpp_ppcp_admin_notice' );
function wpecpp_ppcp_admin_notice() {
	$options = wpecpp_free_options();
	$env = intval( $options['mode'] ) === 2 ? 'live' : 'sandbox';
	$connected = !empty( $options['ppcp_onboarding'][$env] ) && !empty( $options['ppcp_onboarding'][$env]['seller_id'] );
	if ( $connected || !empty( $options['ppcp_notice_dismissed'] ) ||
		( isset( $_GET['page'] ) && $_GET['page'] == 'wpecpp-settings' && isset( $_GET['tab'] ) && $_GET['tab'] == 3 ) ) return;

	printf(
		'<div class="notice notice-error is-dismissible wpecpp-ppcp-connect-notice" data-dismiss-url="%s">
			<p>%s</p>
			<p><a class="wpecpp-ppcp-button" style="background-color: #fff; border: 1px solid #162c70; color:#162c70;" href="%s"><img class="cf7pp-ppcp-paypal-logo" style="max-height:25px" src="'.WPECPP_FREE_URL.'/assets/images/paypal-logo.png" alt="paypal-logo" /><br />Get Started</a></p>
			<br />WPPlugin LLC is an offical PayPal Partner. Pay as you go pricing: 2%% per-transaction fee + PayPal fees.
		</div>',
		add_query_arg( 'wpecpp_admin_ppcp_notice_dismiss', 1, admin_url() ),
		__( '<b>Important</b> - \'Easy PayPal & Stripe Button\' now uses PayPal Commerce Platform.
		<u><b>PayPal Standard is now a Legacy product.</b></u> <br /><br /> <b><u>If you use PayPal, please update to PayPal Commerce Platform.</u></b>' ),
		wpecpp_ppcp_connect_tab_url()
	);
}

/**
 * Dismiss admin notice for PayPal Commerce Platform.
 * @since 1.7.4
 */
add_action( 'admin_init', 'wpecpp_ppcp_admin_notice_dismiss' );
function wpecpp_ppcp_admin_notice_dismiss() {
	if ( empty( $_GET['wpecpp_admin_ppcp_notice_dismiss'] ) ) return;

	$options = wpecpp_free_options();
	$options['ppcp_notice_dismissed'] = 1;
	wpecpp_free_options_update( $options );
	die();
}