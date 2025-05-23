<?php

/*
Plugin Name: Easy PayPal & Stripe Button
Description: Add a PayPal or Stripe Buy Now Button to your website and start selling today. No Coding Required. Official PayPal & Stripe Partner.
Plugin URI: https://wpplugin.org/easy-paypal-button/
Tags: PayPal payment, PayPal, button, payment, online payments, pay now, buy now, ecommerce, gateway, paypal button, paypal buy now button, paypal plugin
Author: Scott Paterson
Author URI: https://wpplugin.org
License: GPL2
Version: 2.0.1
Text Domain: wp-ecommerce-paypal
Domain Path: /languages
*/

/*  Copyright 2014-2025 Scott Paterson

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Load plugin textdomain
function wpecpp_load_textdomain() {
	load_plugin_textdomain('wp-ecommerce-paypal', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'wpecpp_load_textdomain');

define( 'WPECPP_FREE_VERSION_NUM', '2.0.1' );

define( 'WPECPP_FREE_STRIPE_CONNECT_ENDPOINT', 'https://wpplugin.org/stripe-wpecpp/connect.php' );
define( 'WPECPP_FREE_PPCP_API', 'https://wpplugin.org/ppcp-wpecpp/' );

define( 'WPECPP_FREE_URL', plugin_dir_url( __FILE__ ) );

// check if pro version is attempting to be activated - if so, then deactivate this plugin
function WPECPP_check_if_pro_version_is_active() {
	if ( is_plugin_active( 'easy-paypal-button-pro/easy-paypal-button-pro.php' ) ) {
		deactivate_plugins( 'easy-paypal-button-pro/easy-paypal-button-pro.php' );
	}
}
add_action( 'admin_init', 'WPECPP_check_if_pro_version_is_active' );

/*
 * Get plugin options
 */
function wpecpp_free_options() {
	$default = [
		'currency' => '25',
		'language' => 'default',
		'disable_paypal' => '1',
		'mode' => '2',
		'disable_stripe' => '1',
		'mode_stripe' => '2',
		'acct_id_live' => '',
		'stripe_connect_token_live' => '',
		'acct_id_sandbox' => '',
		'stripe_connect_token_sandbox' => '',
		'opens' => '2',
		'cancel' => '',
		'return' => '',
		'activation_notice_shown' => 0,
		'stripe_connect_notice_dismissed' => 1,
		'ppcp_onboarding' => [
			'live' => [],
			'sandbox' => []
		],
		'ppcp_funding_paypal' => 1,
		'ppcp_funding_paylater' => 0,
		'ppcp_funding_venmo' => 0,
		'ppcp_funding_alternative' => 0,
		'ppcp_funding_cards' => 0,
		'ppcp_funding_advanced_cards' => 0,
		'ppcp_layout' => 'vertical',
		'ppcp_color' => 'gold',
		'ppcp_shape' => 'rect',
		'ppcp_label' => 'buynow',
		'ppcp_height' => 40,
		'ppcp_notice_dismissed' => 1,
		'updated_time' => 0,
		'ppcp_width' => 300,
		'stripe_width' => 300,
		'ppcp_acdc_button_text' => __( 'PLACE ORDER', 'wp-ecommerce-paypal' ),
		'address' => '2'
	];
	$options = (array) get_option( 'wpecpp_settings' );

	return array_merge( $default, $options );
}

/*
 * Update plugin options
 */
function wpecpp_free_options_update( $options ) {
	$options['updated_time'] = time();
	update_option( 'wpecpp_settings', $options );
}

/*
 * Register activation hook
 */
register_activation_hook( __FILE__, 'wpecpp_free_activate' );
function wpecpp_free_activate() {
	$pro_plugin = 'easy-paypal-button-pro/easy-paypal-button-pro.php';
	if ( is_plugin_active( $pro_plugin ) ) {
		deactivate_plugins( $pro_plugin );
	}

	$options = wpecpp_free_options();

	// copy options from old free plugin and pro plugin
	$search_options = array_merge(
		array_keys( $options ),
		[
			'liveaccount',
			'sandboxaccount',
			'size',
			'paymentaction'
		]
	);

	// old free plugin options
	$old_free_options = (array) get_option( 'wpecpp_settingsoptions' );
	if ( !empty( $old_free_options ) ) {
		foreach ( $search_options as $option ) {
			if ( isset( $old_free_options[$option] ) ) {
				$options[$option] = $old_free_options[$option];
			}
		}

		delete_option( 'wpecpp_settingsoptions' );
		delete_option( 'wpecpp_my_plugin_notice_shown' );
		delete_option( 'wpecpp_admin_notice_shown' );
	}

	// pro plugin options
	$pro_options = (array) get_option( 'wpplugin_paypal_button_plugin_admin_settings' );
	if ( isset( $pro_options['updated_time'] ) && $pro_options['updated_time'] > $options['updated_time'] ) {
		foreach ( $search_options as $option ) {
			if ( isset( $pro_options[$option] ) ) {
				$options[$option] = $pro_options[$option];
			}
		}
	}

	// Set notices as dismissed
	$options['stripe_connect_notice_dismissed'] = 1;
	$options['ppcp_notice_dismissed'] = 1;

	wpecpp_free_options_update( $options );

	// Set transient for admin notice
	set_transient('wpecpp_activation_notice', true, 5);
}

/*
 * Copy old free plugin options
 */
$old_free_options = get_option( 'wpecpp_settingsoptions' );
if ( !empty( $old_free_options ) ) {
	$old_free_options = (array) $old_free_options;
	$options = wpecpp_free_options();

	// copy options from old free plugin
	$search_options = array_merge(
		array_keys( $options ),
		[
			'liveaccount',
			'sandboxaccount',
			'size',
			'paymentaction'
		]
	);

	foreach ( $search_options as $option ) {
		if ( isset( $old_free_options[$option] ) ) {
			$options[$option] = $old_free_options[$option];
		}
	}

	delete_option( 'wpecpp_settingsoptions' );
	delete_option( 'wpecpp_my_plugin_notice_shown' );
	delete_option( 'wpecpp_admin_notice_shown' );

	wpecpp_free_options_update( $options );
}

/*
 * Register deactivation hooks
 */
register_deactivation_hook( __FILE__, 'wpecpp_free_deactivate' );
function wpecpp_free_deactivate() {

}

/*
 * Set links for plugins page
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpecpp_free_plugins_page_links' );
function wpecpp_free_plugins_page_links( $links ) {
	if ( isset( $links['edit'] ) ) {
		unset( $links['edit'] );
	}

	$links[] = '<a target="_blank" href="https://wpplugin.org/documentation/">' . __( 'Support', 'wp-ecommerce-paypal' ) . '</a>';
	$links[] = '<a target="_blank" href="https://wpplugin.org/downloads/easy-paypal-buy-now-button/">' . __( 'Pro Version', 'wp-ecommerce-paypal' ) . '</a>';
	$links[] = '<a href="admin.php?page=wpecpp-settings">' . __( 'Settings', 'wp-ecommerce-paypal' ) . '</a>';

	return $links; 
}

/*
 * Admin enqueue
 */
add_action( 'admin_enqueue_scripts', 'wpecpp_free_admin_enqueue' );
function wpecpp_free_admin_enqueue() {
	wp_enqueue_style( 'wpecpp-admin-css', plugins_url( '/assets/css/wpecpp-admin.css', __FILE__ ), [],WPECPP_FREE_VERSION_NUM );
	wp_enqueue_script( 'wpecpp-admin-js', plugins_url( '/assets/js/wpecpp-admin.js', __FILE__ ), ['jquery'], WPECPP_FREE_VERSION_NUM,true );
	wp_localize_script( 'wpecpp-admin-js', 'wpecpp', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce( 'wpecpp-request' )
	] );
}

/*
 * Frontend enqueue
 */
add_action( 'wp_enqueue_scripts', 'wpecpp_free_frontend_enqueue' );
function wpecpp_free_frontend_enqueue() {
	$options = wpecpp_free_options();
	wp_enqueue_style( 'wpecpp', plugins_url( '/assets/css/wpecpp.css', __FILE__ ), [],WPECPP_FREE_VERSION_NUM );
	wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v3/', [], null,true );
	wp_enqueue_script( 'wpecpp', plugins_url( '/assets/js/wpecpp.js', __FILE__ ), ['jquery', 'stripe-js'], WPECPP_FREE_VERSION_NUM,true );
	wp_localize_script( 'wpecpp', 'wpecpp', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce( 'wpecpp-frontend-request' ),
		'opens' => $options['opens'],
		'cancel' => $options['cancel'],
		'return' => $options['return']
	] );
}

// Add deactivation survey
function wpecpp_enqueue_deactivation_survey() {
	if (get_current_screen() && get_current_screen()->id === 'plugins') {
		wp_enqueue_script('wpecpp-deactivation-survey', plugins_url('assets/js/deactivation-survey.js', __FILE__), array('jquery'), WPECPP_FREE_VERSION_NUM, true);
		wp_localize_script('wpecpp-deactivation-survey', 'wpecppDeactivationSurvey', array(
			'pluginVersion' => WPECPP_FREE_VERSION_NUM,
			'deactivationOptions' => array(
				'upgraded_to_pro' => __('I upgraded to the Pro version', 'wp-ecommerce-paypal'),
				'no_longer_needed' => __('I no longer need the plugin', 'wp-ecommerce-paypal'),
				'found_better' => __('I found a better plugin', 'wp-ecommerce-paypal'),
				'not_working' => __('The plugin is not working', 'wp-ecommerce-paypal'),
				'fees_expensive' => __('The fees are too high', 'wp-ecommerce-paypal'),
				'temporary' => __('It\'s a temporary deactivation', 'wp-ecommerce-paypal'),
				'other' => __('Other', 'wp-ecommerce-paypal')
			),
			'strings' => array(
				'title' => __('Easy PayPal & Stripe Button Deactivation', 'wp-ecommerce-paypal'),
				'description' => __('If you have a moment, please let us know why you are deactivating. All submissions are anonymous and we only use this feedback to improve this plugin.', 'wp-ecommerce-paypal'),
				'otherPlaceholder' => __('Please tell us more...', 'wp-ecommerce-paypal'),
				'skipButton' => __('Skip & Deactivate', 'wp-ecommerce-paypal'),
				'submitButton' => __('Submit & Deactivate', 'wp-ecommerce-paypal'),
				'cancelButton' => __('Cancel', 'wp-ecommerce-paypal'),
				'betterPluginQuestion' => __('What is the name of the plugin?', 'wp-ecommerce-paypal'),
				'notWorkingQuestion' => __('We\'re sorry to hear that. Can you describe the issue?', 'wp-ecommerce-paypal'),
				'errorRequired' => __('Error: Please complete the required field.', 'wp-ecommerce-paypal')
			)
		));
	}
}
add_action('admin_enqueue_scripts', 'wpecpp_enqueue_deactivation_survey');

/*
 * Incudes
 */
// Functions
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

// Admin settings page
require_once plugin_dir_path( __FILE__ ) . 'includes/admin_settings_page.php';

// Admin menu
require_once plugin_dir_path( __FILE__ ) . 'includes/admin_menu.php';

// Admin includes
require_once plugin_dir_path( __FILE__ ) . 'includes/admin_notices.php';

// Add button to page or post editor
require_once plugin_dir_path( __FILE__ ) . 'includes/admin_media_button.php';

// Include Stripe connect
require_once plugin_dir_path( __FILE__ ) . 'includes/stripe_connect.php';

// Include PayPal Commerce Platform
require_once plugin_dir_path( __FILE__ ) . 'includes/ppcp.php';

// Include PayPal Commerce Platform - Frontend
require_once plugin_dir_path( __FILE__ ) . 'includes/ppcp_frontend.php';

// Shortcode
require_once plugin_dir_path( __FILE__ ) . 'includes/public_shortcode.php';

// Shortcode manager
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode_manager.php';

// Add admin notice
function wpecpp_admin_notice() {
	if (get_transient('wpecpp_activation_notice')) {
		$settings_url = admin_url('admin.php?page=wpecpp-settings');
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php printf(
					__('Thank you for installing Easy PayPal & Stripe Button! Please %s to start accepting payments.', 'wp-ecommerce-paypal'),
					sprintf(
						'<a href="%s">%s</a>',
						esc_url($settings_url),
						__('configure your settings', 'wp-ecommerce-paypal')
					)
				); ?>
			</p>
		</div>
		<?php
		delete_transient('wpecpp_activation_notice');
	}
}
add_action('admin_notices', 'wpecpp_admin_notice');