<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Insert shortcode button
 */
add_action( 'media_buttons', 'wpecpp_insert_shortcode_button', 20 );
function wpecpp_insert_shortcode_button() {
	global $pagenow, $typenow;

    if ( !in_array( $pagenow, ['post.php', 'page.php', 'post-new.php', 'post-edit.php'] ) || $typenow === 'download' ) return;

    echo '<a href="#TB_inline?width=600&height=500&inlineId=wpecpp_popup_container" title="' . __('PayPal / Stripe Button', 'wp-ecommerce-paypal') . '" class="button thickbox">' . 
        __('PayPal / Stripe Button', 'wp-ecommerce-paypal') . 
    '</a>';

	// popup
	add_action( 'admin_footer', 'wpecpp_insert_shortcode_popup_content' );
}

function wpecpp_insert_shortcode_popup_content() {
    ?>
    <script>
        function wpecpp_InsertShortcode(){
            const wpecpp_scname = document.getElementById('wpecpp_scname').value,
                wpecpp_scprice = document.getElementById('wpecpp_scprice').value,
                wpecpp_alignmentc = document.getElementById('wpecpp_alignment'),
                wpecpp_alignmentb = wpecpp_alignmentc.options[wpecpp_alignmentc.selectedIndex].value,
                wpecpp_alignment = wpecpp_alignmentb == 'none' ? '' : ' align="' + wpecpp_alignmentb + '"';

            if (!wpecpp_scname.match(/\S/)) {
                alert("<?php echo esc_js(__('Item Name is required.', 'wp-ecommerce-paypal')); ?>");
                return false;
            }
            if (!wpecpp_scprice.match(/\S/)) {
                alert("<?php echo esc_js(__('Item Price is required.', 'wp-ecommerce-paypal')); ?>");
                return false;
            }

            document.getElementById('wpecpp_scname').value = '';
            document.getElementById('wpecpp_scprice').value = '';
            wpecpp_alignmentc.selectedIndex = null;

            window.send_to_editor('[wpecpp name="' + wpecpp_scname + '" price="' + wpecpp_scprice + '"' + wpecpp_alignment + ']');
        }
    </script>

    <div id="wpecpp_popup_container" style="display:none;">
        <h2><?php _e('Insert a Buy Now Button', 'wp-ecommerce-paypal'); ?></h2>
        <table>
            <tr>
                <td>
                    <?php _e('Item Name:', 'wp-ecommerce-paypal'); ?>
                </td>
                <td>
                    <input type="text" name="wpecpp_scname" id="wpecpp_scname" value="">
                    <?php _e('The name of the item', 'wp-ecommerce-paypal'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php _e('Item Price:', 'wp-ecommerce-paypal'); ?>
                </td>
                <td>
                    <input type="number" step="1" min="0" name="wpecpp_scprice" id="wpecpp_scprice" value="">
                    <?php _e('Example format: 6.99', 'wp-ecommerce-paypal'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php _e('Alignment:', 'wp-ecommerce-paypal'); ?>
                </td>
                <td>
                    <select name="wpecpp_alignment" id="wpecpp_alignment">
                        <option value="none"></option>
                        <option value="left"><?php _e('Left', 'wp-ecommerce-paypal'); ?></option>
                        <option value="center"><?php _e('Center', 'wp-ecommerce-paypal'); ?></option>
                        <option value="right"><?php _e('Right', 'wp-ecommerce-paypal'); ?></option>
                    </select>
                    <?php _e('Optional', 'wp-ecommerce-paypal'); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br />
                    <br />
                    <input type="button" id="wpecpp-insert" class="button-primary" onclick="wpecpp_InsertShortcode();" value="<?php _e('Insert', 'wp-ecommerce-paypal'); ?>" />
                    <br />
                    <br />
                </td>
            </tr>
        </table>
    </div>
    <?php
}