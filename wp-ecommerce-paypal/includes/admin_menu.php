<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Add menu items for the plugin
 */
add_action( 'admin_menu', 'wpecpp_admin_menu' );
function wpecpp_admin_menu() {
    $parent_slug = 'wpecpp-settings';
    
    // Add main menu
    add_menu_page( 
        __( 'Easy PayPal & Stripe Button', 'wp-ecommerce-paypal' ), 
        __( 'PayPal & Stripe', 'wp-ecommerce-paypal' ), 
        'manage_options', 
        $parent_slug, 
        'wpecpp_settings_page', 
        'dashicons-cart',
        '28.7' 
    );
    
    // Add Buttons submenu first
    add_submenu_page(
        $parent_slug,
        __( 'Buttons', 'wp-ecommerce-paypal' ),
        __( 'Buttons', 'wp-ecommerce-paypal' ),
        'manage_options',
        'edit.php?post_type=wpplugin_pp_button',
        null
    );
    
    // Add Settings submenu second
    add_submenu_page(
        $parent_slug,
        __( 'Settings', 'wp-ecommerce-paypal' ),
        __( 'Settings', 'wp-ecommerce-paypal' ),
        'manage_options',
        $parent_slug,
        'wpecpp_settings_page'
    );
    
    // Add New button (hidden from menu)
    add_submenu_page(
        null, // Hidden from menu
        __( 'Add New Button', 'wp-ecommerce-paypal' ),
        __( 'Add New Button', 'wp-ecommerce-paypal' ),
        'manage_options',
        'post-new.php?post_type=wpplugin_pp_button',
        null
    );
    
    // Remove the first submenu item that duplicates the main menu
    remove_submenu_page($parent_slug, $parent_slug);
}

/**
 * Fix admin menu highlighting for buttons pages
 */
add_action('admin_head', 'wpecpp_fix_menu_highlighting');
function wpecpp_fix_menu_highlighting() {
    global $parent_file, $submenu_file, $post_type, $pagenow;
    
    // Only apply to our post type
    if ($post_type !== 'wpplugin_pp_button') {
        return;
    }
    
    // Output script to fix the menu highlighting without blue styling
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Mark parent menu as open
            $('#toplevel_page_wpecpp-settings').addClass('wp-has-current-submenu wp-menu-open').removeClass('wp-not-current-submenu');
            $('#toplevel_page_wpecpp-settings > a').addClass('wp-has-current-submenu wp-menu-open').removeClass('wp-not-current-submenu');
            
            // Remove blue background while keeping the item selected
            $('#toplevel_page_wpecpp-settings .wp-submenu li a[href="edit.php?post_type=wpplugin_pp_button"]').parent().addClass('current');
        });
    </script>
    <?php
} 