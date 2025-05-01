<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register the custom post type for button shortcodes
 */
function wpecpp_register_shortcode_post_type() {
    $labels = array(
        'name'               => __( 'Buttons', 'wp-ecommerce-paypal' ),
        'singular_name'      => __( 'Button', 'wp-ecommerce-paypal' ),
        'menu_name'          => __( 'Buttons', 'wp-ecommerce-paypal' ),
        'name_admin_bar'     => _x( 'PayPal Button', 'add new on admin bar', 'wp-ecommerce-paypal' ),
        'add_new'            => __( 'Add New', 'wp-ecommerce-paypal' ),
        'add_new_item'       => __( 'Add New Button', 'wp-ecommerce-paypal' ),
        'new_item'           => __( 'New Button', 'wp-ecommerce-paypal' ),
        'edit_item'          => __( 'Edit Button', 'wp-ecommerce-paypal' ),
        'view_item'          => __( 'View Button', 'wp-ecommerce-paypal' ),
        'all_items'          => __( 'All Buttons', 'wp-ecommerce-paypal' ),
        'search_items'       => __( 'Search Buttons', 'wp-ecommerce-paypal' ),
        'not_found'          => __( 'No Buttons found', 'wp-ecommerce-paypal' ),
        'not_found_in_trash' => __( 'No Buttons found in trash', 'wp-ecommerce-paypal' ),
        'parent_item_colon'  => '',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'query_var'          => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title' )
    );

    register_post_type( 'wpplugin_pp_button', $args );
}
add_action( 'init', 'wpecpp_register_shortcode_post_type' );

/**
 * Add custom meta boxes for the button edit screen
 */
function wpecpp_add_meta_boxes() {
    add_meta_box(
        'wpecpp_price_box',
        __( 'Button Settings', 'wp-ecommerce-paypal' ),
        'wpecpp_price_meta_box_callback',
        'wpplugin_pp_button',
        'normal',
        'high'
    );
    
    add_meta_box(
        'wpecpp_shortcode_box',
        __( 'Shortcode', 'wp-ecommerce-paypal' ),
        'wpecpp_shortcode_meta_box_callback',
        'wpplugin_pp_button',
        'side',
        'high'
    );
    
    // Remove default publish box and add our customized one
    remove_meta_box( 'submitdiv', 'wpplugin_pp_button', 'side' );
    add_meta_box(
        'wpecpp_submitdiv',
        __( 'Save', 'wp-ecommerce-paypal' ),
        'wpecpp_submit_meta_box_callback',
        'wpplugin_pp_button',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wpecpp_add_meta_boxes' );

/**
 * Button settings meta box callback
 */
function wpecpp_price_meta_box_callback( $post ) {
    // Add nonce for security
    wp_nonce_field( 'wpecpp_save_meta_box_data', 'wpecpp_meta_box_nonce' );
    
    $price = get_post_meta( $post->ID, 'wpplugin_paypal_button_price', true );
    $alignment = get_post_meta( $post->ID, '_wpecpp_alignment', true );
    $quantity = get_post_meta( $post->ID, 'wpplugin_paypal_button_quantity', true );
    $disable_paypal = get_post_meta( $post->ID, 'wpplugin_paypal_button_disable_paypal', true );
    $disable_stripe = get_post_meta( $post->ID, 'wpplugin_paypal_button_disable_stripe', true );
    
    // Set defaults if not set
    if ( empty( $alignment ) ) {
        $alignment = 'left';
    }
    if ( $disable_paypal === '' ) {
        $disable_paypal = '1'; // Disabled by default
    }
    if ( $disable_stripe === '' ) {
        $disable_stripe = '1'; // Disabled by default
    }
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="wpecpp_price"><?php _e( 'Price', 'wp-ecommerce-paypal' ); ?></label>
            </th>
            <td>
                <input type="number" name="wpecpp_price" id="wpecpp_price" value="<?php echo esc_attr( $price ); ?>" class="regular-text" min="1" step="0.01" required>
                <p class="description"><?php _e( 'Example format: 6.99', 'wp-ecommerce-paypal' ); ?>. <?php _e( 'Minimum price is 1.00', 'wp-ecommerce-paypal' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="wpecpp_quantity"><?php _e( 'Quantity', 'wp-ecommerce-paypal' ); ?></label>
            </th>
            <td>
                <input type="number" name="wpecpp_quantity" id="wpecpp_quantity" value="<?php echo esc_attr( $quantity ); ?>" class="regular-text" min="1" step="1" placeholder="Optional">
                <p class="description"><?php _e( 'Leave blank to not use quantity. Whole numbers only.', 'wp-ecommerce-paypal' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="wpecpp_alignment"><?php _e( 'Alignment', 'wp-ecommerce-paypal' ); ?></label>
            </th>
            <td>
                <select name="wpecpp_alignment" id="wpecpp_alignment">
                    <option value="left" <?php selected( $alignment, 'left' ); ?>><?php _e( 'Left', 'wp-ecommerce-paypal' ); ?></option>
                    <option value="center" <?php selected( $alignment, 'center' ); ?>><?php _e( 'Center', 'wp-ecommerce-paypal' ); ?></option>
                    <option value="right" <?php selected( $alignment, 'right' ); ?>><?php _e( 'Right', 'wp-ecommerce-paypal' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Disable PayPal:', 'wp-ecommerce-paypal' ); ?>
            </th>
            <td>
                <select name="wpplugin_paypal_button_disable_paypal" class="wpecpp-button-settings-select">
                    <option value="1" <?php if($disable_paypal == "1") { echo "selected"; } ?>>No</option>
                    <option value="2" <?php if($disable_paypal == "2") { echo "selected"; } ?>>Yes</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Disable Stripe:', 'wp-ecommerce-paypal' ); ?>
            </th>
            <td>
                <select name="wpplugin_paypal_button_disable_stripe" class="wpecpp-button-settings-select">
                    <option value="1" <?php if($disable_stripe == "1") { echo "selected"; } ?>>No</option>
                    <option value="2" <?php if($disable_stripe == "2") { echo "selected"; } ?>>Yes</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Shortcode meta box callback
 */
function wpecpp_shortcode_meta_box_callback( $post ) {
    ?>
    <p><?php _e( 'Use this shortcode to display the button on your site:', 'wp-ecommerce-paypal' ); ?></p>
    <div class="shortcode-copy-container">
        <input type="text" class="shortcode-text" readonly value="[wpecpp id=&quot;<?php echo absint( $post->ID ); ?>&quot;]">
        <button type="button" class="button copy-shortcode" data-clipboard-text="[wpecpp id=&quot;<?php echo absint( $post->ID ); ?>&quot;]">
            <?php _e( 'Copy', 'wp-ecommerce-paypal' ); ?>
        </button>
    </div>
    <?php
}

/**
 * Custom submit box callback
 */
function wpecpp_submit_meta_box_callback( $post ) {
    global $action;
    
    $post_type = $post->post_type;
    $post_type_object = get_post_type_object( $post_type );
    $can_publish = current_user_can( $post_type_object->cap->publish_posts );
    ?>
    <div class="submitbox" id="submitpost">
        <div id="major-publishing-actions">
            <div id="delete-action">
                <?php
                if ( current_user_can( 'delete_post', $post->ID ) ) {
                    if ( !EMPTY_TRASH_DAYS ) {
                        $delete_text = __( 'Delete Permanently', 'wp-ecommerce-paypal' );
                    } else {
                        $delete_text = __( 'Move to Trash', 'wp-ecommerce-paypal' );
                    }
                    ?>
                    <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo $delete_text; ?></a>
                    <?php
                }
                ?>
            </div>
            
            <div id="publishing-action">
                <span class="spinner"></span>
                <?php
                if ( !in_array( $post->post_status, array( 'publish', 'future', 'private' ) ) || 0 == $post->ID ) {
                    if ( $can_publish ) {
                        ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'wp-ecommerce-paypal' ); ?>" />
                        <?php submit_button( __( 'Save Button', 'wp-ecommerce-paypal' ), 'primary button-large', 'publish', false ); ?>
                        <?php
                    }
                } else {
                    ?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'wp-ecommerce-paypal' ); ?>" />
                    <input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Update Button', 'wp-ecommerce-paypal' ); ?>" />
                    <?php
                }
                ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <?php
}

/**
 * Save the meta box data
 */
function wpecpp_save_meta_box_data( $post_id ) {
    // Check if our nonce is set
    if ( !isset( $_POST['wpecpp_meta_box_nonce'] ) ) {
        return;
    }
    
    // Verify that the nonce is valid
    if ( !wp_verify_nonce( $_POST['wpecpp_meta_box_nonce'], 'wpecpp_save_meta_box_data' ) ) {
        return;
    }
    
    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check the user's permissions
    if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Sanitize user input
    $price = isset( $_POST['wpecpp_price'] ) ? floatval( $_POST['wpecpp_price'] ) : 0;
    $alignment = isset( $_POST['wpecpp_alignment'] ) ? sanitize_text_field( $_POST['wpecpp_alignment'] ) : 'left';
    $quantity = isset( $_POST['wpecpp_quantity'] ) && $_POST['wpecpp_quantity'] !== '' ? absint( $_POST['wpecpp_quantity'] ) : '';
    $disable_paypal = isset( $_POST['wpplugin_paypal_button_disable_paypal'] ) ? sanitize_text_field( $_POST['wpplugin_paypal_button_disable_paypal'] ) : '1';
    $disable_stripe = isset( $_POST['wpplugin_paypal_button_disable_stripe'] ) ? sanitize_text_field( $_POST['wpplugin_paypal_button_disable_stripe'] ) : '1';
    
    // Validate data
    if ( $price <= 0 ) {
        $price = 1; // Set minimum price to 1 instead of 0.01
    }
    
    if ( !in_array( $alignment, array( 'left', 'center', 'right' ) ) ) {
        $alignment = 'left';
    }
    
    // Update the meta fields in the database
    update_post_meta( $post_id, 'wpplugin_paypal_button_price', $price );
    update_post_meta( $post_id, '_wpecpp_alignment', $alignment );
    update_post_meta( $post_id, 'wpplugin_paypal_button_quantity', $quantity );
    update_post_meta( $post_id, 'wpplugin_paypal_button_disable_paypal', $disable_paypal );
    update_post_meta( $post_id, 'wpplugin_paypal_button_disable_stripe', $disable_stripe );
}
add_action( 'save_post', 'wpecpp_save_meta_box_data' );

/**
 * Customize the columns displayed in the buttons list table
 */
function wpecpp_button_columns( $columns ) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __( 'Product', 'wp-ecommerce-paypal' );
    $new_columns['price'] = __( 'Price', 'wp-ecommerce-paypal' );
    $new_columns['quantity'] = __( 'Quantity', 'wp-ecommerce-paypal' );
    $new_columns['shortcode'] = __( 'Shortcode', 'wp-ecommerce-paypal' );
    
    // Remove the 'Disable PayPal' and 'Disable Stripe' columns
    unset($columns['disable_paypal']);
    unset($columns['disable_stripe']);
    
    return $new_columns;
}
add_filter( 'manage_wpplugin_pp_button_posts_columns', 'wpecpp_button_columns' );

/**
 * Display the custom column data in the buttons list table
 */
function wpecpp_button_column_data( $column, $post_id ) {
    switch ( $column ) {
        case 'price':
            $price = get_post_meta( $post_id, 'wpplugin_paypal_button_price', true );
            echo !empty( $price ) ? esc_html( number_format( (float) $price, 2 ) ) : '0.00';
            break;
            
        case 'quantity':
            $quantity = get_post_meta( $post_id, 'wpplugin_paypal_button_quantity', true );
            echo !empty( $quantity ) ? esc_html( $quantity ) : '';
            break;
            
        case 'shortcode':
            ?>
            <div class="shortcode-copy-container">
                <input type="text" class="shortcode-text" readonly value="[wpecpp id=&quot;<?php echo absint( $post_id ); ?>&quot;]">
                <button type="button" class="button copy-shortcode" data-clipboard-text="[wpecpp id=&quot;<?php echo absint( $post_id ); ?>&quot;]">
                    <?php _e( 'Copy', 'wp-ecommerce-paypal' ); ?>
                </button>
            </div>
            <?php
            break;
    }
}
add_action( 'manage_wpplugin_pp_button_posts_custom_column', 'wpecpp_button_column_data', 10, 2 );

/**
 * Make the price column sortable
 */
function wpecpp_button_sortable_columns( $columns ) {
    $columns['price'] = __('price', 'wp-ecommerce-paypal');
    $columns['quantity'] = __('quantity', 'wp-ecommerce-paypal');
    return $columns;
}
add_filter( 'manage_edit-wpplugin_pp_button_sortable_columns', 'wpecpp_button_sortable_columns' );

/**
 * Add custom CSS for the shortcode button list table
 */
function wpecpp_admin_head_styles() {
    global $current_screen;
    
    if ( $current_screen && $current_screen->post_type === 'wpplugin_pp_button' ) {
        ?>
        <style type="text/css">
            .shortcode-copy-container {
                display: flex;
                align-items: center;
            }
            
            .shortcode-text {
                flex: 1;
                margin-right: 5px;
                font-family: monospace;
                height: 28px;
                background-color: #f6f6f6;
                border: 1px solid #ddd;
                padding: 0 5px;
            }
            
            .copy-shortcode {
                min-width: 55px;
                text-align: center;
            }
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Make the title field required
                $('#title').prop('required', true);
                
                // Add validation to prevent empty title submissions
                $('form#post').on('submit', function(e) {
                    if ($('#title').val().trim() === '') {
                        e.preventDefault();
                        alert('<?php _e("Button name is required.", "wp-ecommerce-paypal"); ?>');
                        $('#title').focus();
                        return false;
                    }
                });
            });
        </script>
        <?php
    }
}
add_action( 'admin_head', 'wpecpp_admin_head_styles' );

/**
 * Add CSS for the buttons list page
 */
function wpecpp_admin_enqueue_scripts( $hook ) {
    global $post_type;
    
    // Only on the buttons edit page
    if ( 'edit.php' === $hook && 'wpplugin_pp_button' === $post_type ) {
        wp_add_inline_style( 'list-tables', '
            .column-price { width: 10%; }
            .column-quantity { width: 10%; }
            .column-shortcode { width: 25%; }
        ' );
    }
}
add_action( 'admin_enqueue_scripts', 'wpecpp_admin_enqueue_scripts' );

/**
 * AJAX handler for shortcode preview
 */
function wpecpp_preview_shortcode_ajax() {
    // Check nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wpecpp_preview_shortcode' ) ) {
        wp_die( 'Invalid security token' );
    }
    
    $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $price = isset( $_POST['price'] ) ? floatval( $_POST['price'] ) : 0;
    $alignment = isset( $_POST['alignment'] ) ? sanitize_text_field( $_POST['alignment'] ) : 'left';
    
    if ( empty( $name ) || $price <= 0 ) {
        echo '<p class="description">' . __('Please enter a valid name and price.', 'wp-ecommerce-paypal') . '</p>';
        wp_die();
    }
    
    $shortcode = '[wpecpp name="' . esc_attr( $name ) . '" price="' . esc_attr( $price ) . '" align="' . esc_attr( $alignment ) . '"]';
    echo do_shortcode( $shortcode );
    
    wp_die();
}
add_action( 'wp_ajax_wpecpp_preview_shortcode', 'wpecpp_preview_shortcode_ajax' ); 