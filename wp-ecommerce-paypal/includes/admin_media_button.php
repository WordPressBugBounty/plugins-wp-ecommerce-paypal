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

    echo '<a href="#TB_inline?width=600&height=500&inlineId=wpecpp_popup_container" title="PayPal / Stripe Button" class="button thickbox">
        PayPal / Stripe Button
    </a>';

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
                alert("Item Name is required.");
                return false;
            }
            if (!wpecpp_scprice.match(/\S/)) {
                alert("Item Price is required.");
                return false;
            }

            document.getElementById('wpecpp_scname').value = '';
            document.getElementById('wpecpp_scprice').value = '';
            wpecpp_alignmentc.selectedIndex = null;

            window.send_to_editor('[wpecpp name="' + wpecpp_scname + '" price="' + wpecpp_scprice + '"' + wpecpp_alignment + ']');
        }
    </script>

    <div id="wpecpp_popup_container" style="display:none;">
        <h2>Insert a Buy Now Button</h2>
        <table>
            <tr>
                <td>
                    Item Name:
                </td>
                <td>
                    <input type="text" name="wpecpp_scname" id="wpecpp_scname" value="">
                    The name of the item
                </td>
            </tr>
            <tr>
                <td>
                    Item Price:
                </td>
                <td>
                    <input type="number" step="1" min="0" name="wpecpp_scprice" id="wpecpp_scprice" value="">
                    Example format: 6.99
                </td>
            </tr>
            <tr>
                <td>
                    Alignment:
                </td>
                <td>
                    <select name="wpecpp_alignment" id="wpecpp_alignment">
                        <option value="none"></option>
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                    Optional
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br />
                    <br />
                    <input type="button" id="wpecpp-insert" class="button-primary" onclick="wpecpp_InsertShortcode();" value="Insert" />
                    <br />
                    <br />
                </td>
            </tr>
        </table>
    </div>
    <?php
}