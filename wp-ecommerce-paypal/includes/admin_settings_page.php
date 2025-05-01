<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Returns HTML for setting page
 */
function wpecpp_settings_page() { 
    $active_tab = isset( $_REQUEST['tab'] ) ? intval( $_REQUEST['tab'] ) : 1;
?>
    <div class="wrap">

        <?php if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page. Please sign in as an administrator.', 'wp-ecommerce-paypal' ) );
        } ?>

	    <form method='post' action='<?php $_SERVER["REQUEST_URI"]; ?>' id="wpecpp-settings-form">
		    <?php wp_nonce_field( 'wpecpp-save-settings','wpecpp_save_settings_nonce' ); ?>
            <input type="hidden" id="wpecpp-auto-update" name="update" value="">
            <input type="hidden" id="active-tab" name="tab" value="<?php echo esc_attr($active_tab); ?>">

            <?php
                $options = wpecpp_free_options();

                // save and update options
                if ( !empty( $_POST['update'] ) ) {
	                if ( !empty( $_POST['wpecpp_save_settings_nonce'] ) && wp_verify_nonce( $_POST['wpecpp_save_settings_nonce'], 'wpecpp-save-settings' ) ) {
		                foreach ( array_keys( $options ) as $key ) {
			                if ( isset( $_POST[$key] ) ) {
				                $options[$key] = sanitize_text_field( $_POST[$key] );
			                }
		                }
		                wpecpp_free_options_update( $options );
		                $saved = '1';
	                } else {
		                $saved_error = '1';
	                }
                }
		    ?>

	        <?php /* tabs menu */ ?>
		    <table width='100%'>
                <tr>
                    <td>
			            <br />
			            <span style="font-size:20pt;"><?php _e( 'PayPal & Stripe Settings', 'wp-ecommerce-paypal' ); ?></span>
                    </td>
                    <td valign="bottom">
			            <input type="submit" name='btn2' class='button-primary' style='font-size: 14px;height: 30px;float: right;' value="<?php _e( 'Save Settings', 'wp-ecommerce-paypal' ); ?>">
                    </td>
                </tr>
            </table>
			
			<?php if ( !empty( $saved ) ) { ?>
                <div class='updated'><p><?php _e( 'Settings Updated.', 'wp-ecommerce-paypal' ); ?></p></div>
			<?php } elseif ( !empty( $saved_error ) ) { ?>
                <div class='error'><p><?php _e( 'Unable to update settings.', 'wp-ecommerce-paypal' ); ?></p></div>
		    <?php } ?>

            <table width="100%">
                <tr>
                    <td valign="top">
                        <script type="text/javascript">
                            function activateTab(e){
                                e.preventDefault();

                                const id = e.target.id.replace('id', '');

                                for (let i = 1; i <= 6; i++) {
                                    document.getElementById(i).style.display = 'none';
                                    document.getElementById('id' + i).classList.remove('nav-tab-active');
                                }

                                e.target.classList.add('nav-tab-active');
                                document.getElementById(id).style.display = 'block';
                                document.getElementById('active-tab').value = id;

                                return false;
                            }
                            
                            function autoSubmitModeChange(e) {
                                document.getElementById('wpecpp-auto-update').value = '1';
                                
                                // Determine which tab the radio button belongs to
                                const radioName = e.target.name;
                                if (radioName === 'mode') {
                                    // PayPal mode radio button
                                    document.getElementById('active-tab').value = '3';
                                } else if (radioName === 'mode_stripe') {
                                    // Stripe mode radio button
                                    document.getElementById('active-tab').value = '4';
                                }
                                
                                document.getElementById('wpecpp-settings-form').submit();
                            }
                        </script>

                        <h2 class="nav-tab-wrapper">
                            <a onclick='activateTab(event);' href="#" id="id1" class="nav-tab <?php echo $active_tab === 1 ? 'nav-tab-active' : ''; ?>"><?php _e( 'Getting Started', 'wp-ecommerce-paypal' ); ?></a>
                            <a onclick='activateTab(event);' href="#" id="id2" class="nav-tab <?php echo $active_tab === 2 ? 'nav-tab-active' : ''; ?>"><?php _e( 'Language & Currency', 'wp-ecommerce-paypal' ); ?></a>
                            <a onclick='activateTab(event);' href="#" id="id3" class="nav-tab <?php echo $active_tab === 3 ? 'nav-tab-active' : ''; ?>"><?php _e( 'PayPal', 'wp-ecommerce-paypal' ); ?></a>
                            <a onclick='activateTab(event);' href="#" id="id4" class="nav-tab <?php echo $active_tab === 4 ? 'nav-tab-active' : ''; ?>"><?php _e( 'Stripe', 'wp-ecommerce-paypal' ); ?></a>
                            <a onclick='activateTab(event);' href="#" id="id5" class="nav-tab <?php echo $active_tab === 5 ? 'nav-tab-active' : ''; ?>"><?php _e( 'Actions', 'wp-ecommerce-paypal' ); ?></a>
                            <a onclick='activateTab(event);' href="#" id="id6" class="nav-tab <?php echo $active_tab === 6 ? 'nav-tab-active' : ''; ?>"><?php _e( 'Shipping', 'wp-ecommerce-paypal' ); ?></a>
                        </h2>

                        <br />

                        <div id="1" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '1' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                <?php _e( 'Getting Started', 'wp-ecommerce-paypal' ); ?>
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <h3><?php _e( 'How to use this plugin', 'wp-ecommerce-paypal' ); ?></h3>
                                <br />
                                <?php _e( 'On the Buttons page you can make PayPal and Stripe buttons. It will show you the shortcode for each button. Place these shortcodes in any page or post to display the payment button for your customers.', 'wp-ecommerce-paypal' ); ?>
                                <br><br>
                                <?php _e( 'Alterntive method: In a page or post editor, you will see a new button called \'PayPal / Stripe Button\' located above the text area beside the \'Add Media\' button. By using this, you can create shortcodes that will show up as a \'Buy Now\' button on your site.', 'wp-ecommerce-paypal' ); ?>
                                <br />
                                <br />
                                <?php _e( 'You can put the \'Buy Now\' buttons as many times on a page or post as you want; there is no limit. If you want to remove a \'Buy Now\' button, just remove the shortcode text on your page or post.', 'wp-ecommerce-paypal' ); ?>
                                <br />
                                <h3><?php _e( 'Help & Documentation', 'wp-ecommerce-paypal' ); ?></h3>
                                <?php _e( 'Help and Documentation manuals can be found on our website at', 'wp-ecommerce-paypal' ); ?> <a target="_blank" href="https://wpplugin.org/documentation">wpplugin.org/documentation</a>.<br />
                                <?php _e( 'If you have are having a problem, contact support at', 'wp-ecommerce-paypal' ); ?> <a target="_blank" href="https://wpplugin.org/contact">wpplugin.org/contact</a>.
                                <br />
                                <br />
                                <br />
                                <span style="color:#777;float:right;">
                                    <i><?php _e( 'WPPlugin LLC is an offical PayPal & Stripe Partner. Various trademarks held by their respective owners.', 'wp-ecommerce-paypal' ); ?></i>
                                </span>
                                <br />
                            </div>
                        </div>

                        <div id="2" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '2' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                <?php _e( 'Language & Currency Settings', 'wp-ecommerce-paypal' ); ?>
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="2">
                                            <h3><?php _e( 'Language Settings', 'wp-ecommerce-paypal' ); ?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Language:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <select name="language" style="width: 280px">
                                                <option <?php if ($options['language'] == "default") { echo "selected"; } ?> value="default"><?php _e( 'Default', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "1") { echo "selected"; } ?> value="1"><?php _e( 'Danish', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "2") { echo "selected"; } ?> value="2"><?php _e( 'Dutch', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "3") { echo "selected"; } ?> value="3"><?php _e( 'English', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "20") { echo "selected"; } ?> value="20"><?php _e( 'English - UK', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "4") { echo "selected"; } ?> value="4"><?php _e( 'French', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "5") { echo "selected"; } ?> value="5"><?php _e( 'German', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "6") { echo "selected"; } ?> value="6"><?php _e( 'Hebrew', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "7") { echo "selected"; } ?> value="7"><?php _e( 'Italian', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "8") { echo "selected"; } ?> value="8"><?php _e( 'Japanese', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "9") { echo "selected"; } ?> value="9"><?php _e( 'Norwegian', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "10") { echo "selected"; } ?> value="10"><?php _e( 'Polish', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "11") { echo "selected"; } ?> value="11"><?php _e( 'Portuguese', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "12") { echo "selected"; } ?> value="12"><?php _e( 'Russian', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "13") { echo "selected"; } ?> value="13"><?php _e( 'Spanish', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "14") { echo "selected"; } ?> value="14"><?php _e( 'Swedish', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "15") { echo "selected"; } ?> value="15"><?php _e( 'Simplified Chinese -China only', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "16") { echo "selected"; } ?> value="16"><?php _e( 'Traditional Chinese - Hong Kong only', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "17") { echo "selected"; } ?> value="17"><?php _e( 'Traditional Chinese - Taiwan only', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "18") { echo "selected"; } ?> value="18"><?php _e( 'Turkish', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['language'] == "19") { echo "selected"; } ?> value="19"><?php _e( 'Thai', 'wp-ecommerce-paypal' ); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <?php _e( 'PayPal currently supports 18 languages.', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <br />
                                            <h3><?php _e( 'Currency Settings', 'wp-ecommerce-paypal' ); ?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Currency:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <select name="currency" style="width: 280px">
                                                <option <?php if ($options['currency'] == "1") { echo "selected"; } ?> value="1"><?php _e( 'Australian Dollar - AUD', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "2") { echo "selected"; } ?> value="2"><?php _e( 'Brazilian Real - BRL', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "3") { echo "selected"; } ?> value="3"><?php _e( 'Canadian Dollar - CAD', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "4") { echo "selected"; } ?> value="4"><?php _e( 'Czech Koruna - CZK', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "5") { echo "selected"; } ?> value="5"><?php _e( 'Danish Krone - DKK', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "6") { echo "selected"; } ?> value="6"><?php _e( 'Euro - EUR', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "7") { echo "selected"; } ?> value="7"><?php _e( 'Hong Kong Dollar - HKD', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "8") { echo "selected"; } ?> value="8"><?php _e( 'Hungarian Forint - HUF', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "9") { echo "selected"; } ?> value="9"><?php _e( 'Israeli New Sheqel - ILS', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "10") { echo "selected"; } ?> value="10"><?php _e( 'Japanese Yen - JPY', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "11") { echo "selected"; } ?> value="11"><?php _e( 'Malaysian Ringgit - MYR', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "12") { echo "selected"; } ?> value="12"><?php _e( 'Mexican Peso - MXN', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "13") { echo "selected"; } ?> value="13"><?php _e( 'Norwegian Krone - NOK', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "14") { echo "selected"; } ?> value="14"><?php _e( 'New Zealand Dollar - NZD', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "15") { echo "selected"; } ?> value="15"><?php _e( 'Philippine Peso - PHP', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "16") { echo "selected"; } ?> value="16"><?php _e( 'Polish Zloty - PLN', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "17") { echo "selected"; } ?> value="17"><?php _e( 'Pound Sterling - GBP', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "18") { echo "selected"; } ?> value="18"><?php _e( 'Russian Ruble - RUB', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "19") { echo "selected"; } ?> value="19"><?php _e( 'Singapore Dollar - SGD', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "20") { echo "selected"; } ?> value="20"><?php _e( 'Swedish Krona - SEK', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "21") { echo "selected"; } ?> value="21"><?php _e( 'Swiss Franc - CHF', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "22") { echo "selected"; } ?> value="22"><?php _e( 'Taiwan New Dollar - TWD', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "23") { echo "selected"; } ?> value="23"><?php _e( 'Thai Baht - THB', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "24") { echo "selected"; } ?> value="24"><?php _e( 'Turkish Lira - TRY', 'wp-ecommerce-paypal' ); ?></option>
                                                <option <?php if ($options['currency'] == "25") { echo "selected"; } ?> value="25"><?php _e( 'U.S. Dollar - USD', 'wp-ecommerce-paypal' ); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <?php _e( 'PayPal currently supports 25 currencies.', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <br />
                            </div>
                        </div>

                        <div id="3" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '3' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                <?php _e( 'PayPal Settings', 'wp-ecommerce-paypal' ); ?>
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="2">
                                            <h3><?php _e( 'PayPal Account', 'wp-ecommerce-paypal' ); ?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <br />
                                        </td>
                                    </tr>
                                </table>

                                <?php wpecpp_ppcp_status_markup(); ?>



                                <table>

                                <?php
                                if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) {
                                        echo "
                                <tr>
                                    <td colspan='2'>
                                        <h3>" . __( 'PayPal Standard', 'wp-ecommerce-paypal' ) . "</h3>
                                    </td>
                                </tr>
                                ";
                                }
                                ?>
                                
                                <?php
	                                if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) {
		                                $options = wpecpp_free_options();
	                                }
                                ?>
                                <?php if ( !empty( $options['liveaccount'] ) ) { ?>
                                    <tr>
                                        <td>
                                            <b><?php _e( 'Live Account:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <input type='text' name='liveaccount' value='<?php echo $options['liveaccount']; ?>' readonly />
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ( !empty( $options['sandboxaccount'] ) ) { ?>
                                    <tr>
                                        <td>
                                            <b><?php _e( 'Sandbox Account:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <input type='text' name='sandboxaccount' value='<?php echo $options['sandboxaccount']; ?>' readonly />
                                        </td>
                                    </tr>
                                <?php } ?>


                                <?php
                                    if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) {
                                        echo "<tr><td></td><td>" . __( 'PayPal Standard is now deprecated. You cannot modify your Standard settings. We highly recommend using PayPal Commerce.', 'wp-ecommerce-paypal' ) . "</td></tr>";
                                    }
                                ?>


                                    <tr>
                                        <td colspan="2">
                                            <h3><?php _e( 'PayPal Options', 'wp-ecommerce-paypal' ); ?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Sandbox Mode:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['mode'] == "1") { echo "checked='checked'"; } ?> type='radio' name='mode' value='1' onclick="autoSubmitModeChange(event)">
                                                <?php _e( 'On (Sandbox mode)', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                            &nbsp;
                                            &nbsp;
                                            <label>
                                                <input <?php if ($options['mode'] == "2") { echo "checked='checked'"; } ?> type='radio' name='mode' value='2' onclick="autoSubmitModeChange(event)">
                                                <?php _e( 'Off (Live mode)', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Disable PayPal:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['disable_paypal'] == "1") { echo "checked='checked'"; } ?> type='radio' name='disable_paypal' value='1'>
                                                <?php _e( 'No', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                            &nbsp;
                                            &nbsp;
                                            <label>
                                                <input <?php if ($options['disable_paypal'] == "2") { echo "checked='checked'"; } ?> type='radio' name='disable_paypal' value='2'>
                                                <?php _e( 'Yes', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                        </td>
                                    </tr>

                                    <?php if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) { ?>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Payment Action:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['paymentaction'] == "1") { echo "checked='checked'"; } ?> type='radio' name='paymentaction' value='1'>
                                                <?php _e( 'Sale (Default)', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                            &nbsp;
                                            &nbsp;
                                            <label>
                                                <input <?php if ($options['paymentaction'] == "2") { echo "checked='checked'"; } ?> type='radio' name='paymentaction' value='2'>
                                                <?php _e( 'Authorize', 'wp-ecommerce-paypal' ); ?> (<?php _e( 'Learn more', 'wp-ecommerce-paypal' ); ?> <a target='_blank' href='https://developer.paypal.com/docs/checkout/standard/customize/authorization/'>here</a>)
                                            </label>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <br />
                                <br />
                            </div>
                        </div>

                        <div id="4" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '4' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                <?php _e( 'Stripe Settings', 'wp-ecommerce-paypal' ); ?>
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table id="wpecpp-stripe-connect-table">
                                    <tr>
                                        <td colspan="2">
                                            <h3><?php _e( 'Stripe Account', 'wp-ecommerce-paypal' ); ?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='2'>
                                            <br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Connection status:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td id="stripe-connection-status-html">
						                    <?php echo wpecpp_stripe_connection_status_html(); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Width:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <input type="number" name="stripe_width" value="<?php echo esc_attr(absint($options['stripe_width'])); ?>" />
                                            <br />
                                            <?php _e( 'Button width in pixels', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='2'>
                                            <br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Sandbox Mode:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <label>
                                                <input type='radio' name='mode_stripe' value='1' <?php echo ( $options['mode_stripe'] != '2' ) ? 'checked' : ''; ?> onclick="autoSubmitModeChange(event)" />
                                                <?php _e( 'On (Sandbox mode)', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                            &nbsp; &nbsp;
                                            <label>
                                                <input type='radio' name='mode_stripe' value='2' <?php echo ( $options['mode_stripe'] == '2' ) ? 'checked' : ''; ?> onclick="autoSubmitModeChange(event)" />
                                                <?php _e( 'Off (Live mode)', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Disable Stripe:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['disable_stripe'] == "1") { echo "checked='checked'"; } ?> type='radio' name='disable_stripe' value='1'>
                                                <?php _e( 'No', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                            &nbsp; &nbsp;
                                            <label>
                                                <input <?php if ($options['disable_stripe'] == "2") { echo "checked='checked'"; } ?> type='radio' name='disable_stripe' value='2'>
                                                <?php _e( 'Yes', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                            </div>
                        </div>

                        <div id="5" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '5' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                <?php _e( 'Action Settings', 'wp-ecommerce-paypal' ); ?>
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="2">
                                            <h3><?php _e( 'Action Settings', 'wp-ecommerce-paypal' ); ?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Button opens in:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['opens'] == "1") { echo "checked='checked'"; } ?>  type='radio' name='opens' value='1'>
                                                <?php _e( 'Same window', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                            &nbsp; &nbsp;
                                            <label>
                                                <input <?php if ($options['opens'] == "2") { echo "checked='checked'"; } ?> type='radio' name='opens' value='2'>
                                                <?php _e( 'New window', 'wp-ecommerce-paypal' ); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left"></td>
                                        <td>
                                            <?php _e( 'Note: PayPal can only open in a popup window.', 'wp-ecommerce-paypal' ); ?><br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Default Cancel URL:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <input type='text' name='cancel' value='<?php echo $options['cancel']; ?>'> <?php _e( 'Optional', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left"></td>
                                        <td>
                                            <?php _e( 'If the customer goes to PayPal and clicks the cancel button, where do they go. Example:', 'wp-ecommerce-paypal' ); ?> <?php echo get_site_url(); ?> / <?php _e( 'cancel. Max length: 1,024.', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Default Return URL:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
                                            <input type='text' name='return' value='<?php echo $options['return']; ?>'> <?php _e( 'Optional', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left"></td>
                                        <td>
                                            <?php _e( 'If the customer goes to PayPal and successfully pays, where are they redirected to after. Example:', 'wp-ecommerce-paypal' ); ?> <?php echo get_site_url(); ?>/ <?php _e( 'thankyou. Max length: 1,024.', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                            </div>
                        </div>

                        <div id="6" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '6' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                <?php _e( 'Shipping Settings', 'wp-ecommerce-paypal' ); ?>
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="3">
                                            <h3><?php _e( 'Shipping Options', 'wp-ecommerce-paypal' ); ?></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b><?php _e( 'Require Shipping Address:', 'wp-ecommerce-paypal' ); ?></b>
                                        </td>
                                        <td>
	                                        <?php
	                                        if (empty($options['address'])) {
		                                        $options['address'] = "0";
	                                        }
	                                        ?>
                                            <select name="address" id="address">
		                                        <?php if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) { ?>
                                                    <option value="0" <?php if ($options['address'] == "0") { echo "SELECTED"; } ?>><?php _e( 'Prompt for an address, but do not require one (default)', 'wp-ecommerce-paypal' ); ?></option>
                                                    <option value="1" <?php if ($options['address'] == "1") { echo "SELECTED"; } ?>><?php _e( 'Do not prompt for an address', 'wp-ecommerce-paypal' ); ?></option>
                                                    <option value="2" <?php if ($options['address'] == "2") { echo "SELECTED"; } ?>><?php _e( 'Prompt for an address, and require one', 'wp-ecommerce-paypal' ); ?></option>
		                                        <?php } else { ?>
                                                    <option value="1" <?php if ($options['address'] == "1") { echo "SELECTED"; } ?>><?php _e( 'Do not prompt for an address', 'wp-ecommerce-paypal' ); ?></option>
                                                    <option value="2" <?php if ( in_array( $options['address'], ['0', '2'] ) ) { echo "SELECTED"; } ?>><?php _e( 'Prompt for an address, and require one', 'wp-ecommerce-paypal' ); ?></option>
		                                        <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <?php _e( 'Optional - Should the customer be asked for a shipping address at PayPal checkout.', 'wp-ecommerce-paypal' ); ?>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                            </div>
                        </div>
                    </td>
                    <td width="3%"></td>
                    <td valign="top" width="24%" style="padding-top: 68px;">
                        <div style="background-color:#E4E4E4;padding:8px;color:#464646;font-size:15px;font-weight:bold;border:1px solid #CCC;border-bottom: none">
                            &nbsp; <?php _e( 'PayPal Buy Now Button Pro', 'wp-ecommerce-paypal' ); ?>
                        </div>

                        <div style="background-color:#fff;border: 1px solid #CCC;padding:8px;">
                            <center><label style="font-size:14pt;"><?php _e( 'With the Pro version you\'ll <br /> be able to:', 'wp-ecommerce-paypal' ); ?></label></center>
                            <br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'No PayPal 2% per-transaction fee', 'wp-ecommerce-paypal' ); ?><br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'No Stripe 2% per-transaction fee', 'wp-ecommerce-paypal' ); ?><br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'View Sales In Your Dashboard', 'wp-ecommerce-paypal' ); ?><br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'Send out Email Notifications', 'wp-ecommerce-paypal' ); ?><br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'Inventory Management', 'wp-ecommerce-paypal' ); ?><br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'Separate PayPal Accounts', 'wp-ecommerce-paypal' ); ?><br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'Add Discounts', 'wp-ecommerce-paypal' ); ?><br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div><?php _e( 'Fast & Professional Support', 'wp-ecommerce-paypal' ); ?><br />

                            <center><p style="font-size: 14px; color: #666; margin-bottom: 10px;"><?php _e( 'Developed by an official PayPal and Stripe Partner', 'wp-ecommerce-paypal' ); ?></p></center>
                            <center><a target='_blank' href="https://wpplugin.org/downloads/easy-paypal-buy-now-button/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'><?php _e( 'Learn More', 'wp-ecommerce-paypal' ); ?></a></center>
                            <br />
                        </div>
                        <br />
                        <br />
                        <div style="background-color:#E4E4E4;padding:8px;color:#464646;font-size:15px;font-weight:bold;border:1px solid #CCC;border-bottom: none">
                            &nbsp; <?php _e( 'Quick Links', 'wp-ecommerce-paypal' ); ?>
                        </div>
                        <div style="background-color:#fff;border: 1px solid #CCC;padding:8px;">
                            <br />
                            <div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wordpress.org/support/plugin/wp-ecommerce-paypal"><?php _e( 'Support Forum', 'wp-ecommerce-paypal' ); ?></a> <br />
                            <div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/documentation/"><?php _e( 'FAQ', 'wp-ecommerce-paypal' ); ?></a> <br />
                            <div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/downloads/easy-paypal-buy-now-button/"><?php _e( 'PayPal Button Pro', 'wp-ecommerce-paypal' ); ?></a> <br />
                        </div>
                    </td>
                </tr>
            </table>

		    <input type='hidden' name='update' value='1'>
	    </form>
	</div>
	<?php
}

function wpecpp_ppcp_status_markup() {
	$options = wpecpp_free_options();
	$status = wpecpp_ppcp_status();
	if ( $status ) {
        if ( in_array( $status['mode'], ['advanced', 'express'] ) ) {
            if ( empty( $status['warnings'] ) ) {
	            $notice_type = 'success';
	            $show_links = false;
            } else {
	            $notice_type = 'warning';
	            $show_links = true;
            }
	        $show_settings = true;
        } else {
	        $notice_type = 'error';
	        $show_links = true;
	        $show_settings = false;
        }
        ?>
        <div id="ppcp-status-table">
            <table>
                <tr>
                    <td class="wpecpp-cell-left">
                        <b><?php _e( 'Connection status:', 'wp-ecommerce-paypal' ); ?></b>
                    </td>
                    <td>
                        <div class="notice inline notice-<?php echo $notice_type; ?>">
                            <p>
                                <?php if ( !empty( $status['legal_name'] ) ) { ?>
                                <strong><?php echo $status['legal_name']; ?></strong>
                                <br>
                                <?php } ?>
	                            <?php echo !empty( $status['primary_email'] ) ? $status['primary_email'] . ' — ' : ''; ?><?php _e( 'Administrator (Owner)', 'wp-ecommerce-paypal' ); ?>
                            </p>
                            <p><?php _e( 'Pay as you go pricing: 2% per-transaction fee + PayPal fees.', 'wp-ecommerce-paypal' ); ?></p>
                        </div>
                        <div>
                            <?php $reconnect_mode = $status['env'] === 'live' ? 'sandbox' : 'live'; ?>
                            <?php _e( 'Your PayPal account is connected in', 'wp-ecommerce-paypal' ); ?> <strong><?php echo $status['env']; ?></strong> <?php _e( 'mode.', 'wp-ecommerce-paypal' ); ?>
                            <a href="#TB_inline?&inlineId=ppcp-setup-account-modal" class="ppcp-onboarding-start thickbox" data-connect-mode="<?php echo $reconnect_mode; ?>">
                                <?php _e( 'Connect in', 'wp-ecommerce-paypal' ); ?> <strong><?php echo $reconnect_mode; ?></strong> <?php _e( 'mode', 'wp-ecommerce-paypal' ); ?>
                            </a> <?php _e( 'or', 'wp-ecommerce-paypal' ); ?> <a href="#" id="ppcp-disconnect"><?php _e( 'disconnect this account', 'wp-ecommerce-paypal' ); ?></a>.
                        </div>

                        <?php if ( $status['mode'] === 'error' ) { ?>
                            <p>
                                <strong><?php _e( 'There were errors connecting your PayPal account. Resolve them in your account settings, by contacting support or by reconnecting your PayPal account.', 'wp-ecommerce-paypal' ); ?></strong>
                            </p>
                            <?php if ( !empty( $status['errors'] ) ) { ?>
                                <p>
                                    <strong><?php _e( 'See below for more details.', 'wp-ecommerce-paypal' ); ?></strong>
                                </p>
                                <ul class="ppcp-list ppcp-list-error">
                                    <?php foreach ( $status['errors'] as $error ) { ?>
                                        <li><?php echo $error; ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        <?php } elseif ( !empty( $status['warnings'] ) ) { ?>
                            <p>
                                <strong><?php _e( 'Please review the warnings below and resolve them in your account settings or by contacting support.', 'wp-ecommerce-paypal' ); ?></strong>
                            </p>
                            <ul class="ppcp-list ppcp-list-warning">
		                        <?php foreach ( $status['warnings'] as $warning ) { ?>
                                    <li><?php echo $warning; ?></li>
		                        <?php } ?>
                            </ul>
                        <?php } ?>

	                    <?php if ( $show_links ) { ?>
                            <ul class="ppcp-list">
                                <li><a href="https://www.paypal.com/myaccount/settings/"><?php _e( 'PayPal account settings', 'wp-ecommerce-paypal' ); ?></a></li>
                                <li><a href="https://www.paypal.com/us/smarthelp/contact-us"><?php _e( 'PayPal support', 'wp-ecommerce-paypal' ); ?></a></li>
                            </ul>
	                    <?php } ?>
                    </td>
                </tr>
            </table>

	        <?php if ( $show_settings ) { ?>
                <table>
                    <tr>
                        <td colspan="2">
                            <br />
                            <h3><?php _e( 'Payments Methods Accepted', 'wp-ecommerce-paypal' ); ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'PayPal:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_paypal" value="1" <?php echo !empty( $options['ppcp_funding_paypal'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'On', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_paypal" value="0" <?php echo empty( $options['ppcp_funding_paypal'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'Off', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'PayPal PayLater:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_paylater" value="1" <?php echo !empty( $options['ppcp_funding_paylater'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'On', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_paylater" value="0" <?php echo empty( $options['ppcp_funding_paylater'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'Off', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Venmo:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_venmo" value="1" <?php echo !empty( $options['ppcp_funding_venmo'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'On', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_venmo" value="0" <?php echo empty( $options['ppcp_funding_venmo'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'Off', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Local Alternative Payment Methods:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_alternative" value="1" <?php echo !empty( $options['ppcp_funding_alternative'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'On', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_alternative" value="0" <?php echo empty( $options['ppcp_funding_alternative'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'Off', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Credit & Debit Cards:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_cards" value="1" <?php echo !empty( $options['ppcp_funding_cards'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'On', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_cards" value="0" <?php echo empty( $options['ppcp_funding_cards'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'Off', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>

                    <?php if ( $status['mode'] === 'advanced' ) { ?>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Advanced Credit & Debit Cards (ACDC):', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_advanced_cards" value="1" <?php echo !empty( $options['ppcp_funding_advanced_cards'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'On', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_advanced_cards" value="0" <?php echo empty( $options['ppcp_funding_advanced_cards'] ) ? 'checked ' : ''; ?>/>
                                <?php _e( 'Off', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'ACDC Button text:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <input type="text" name="ppcp_acdc_button_text" value="<?php echo $options['ppcp_acdc_button_text']; ?>" />
                            <br />
                            <?php _e( 'Payment button text', 'wp-ecommerce-paypal' ); ?>
                        </td>
                    </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="2">
                            <br />
                            <h3><?php _e( 'PayPal Checkout Buttons', 'wp-ecommerce-paypal' ); ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Layout:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_layout" value="horizontal" <?php echo $options['ppcp_layout'] === 'horizontal' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Horizontal', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_layout" value="vertical" <?php echo $options['ppcp_layout'] === 'vertical' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Vertical', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Color:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_color" value="gold" <?php echo $options['ppcp_color'] === 'gold' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Gold', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="blue" <?php echo $options['ppcp_color'] === 'blue' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Blue', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="black" <?php echo $options['ppcp_color'] === 'black' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Black', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="silver" <?php echo $options['ppcp_color'] === 'silver' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Silver', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="white" <?php echo $options['ppcp_color'] === 'white' ? 'checked ' : ''; ?>/>
                                <?php _e( 'White', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Shape:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_shape" value="rect" <?php echo $options['ppcp_shape'] === 'rect' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Rectangle', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_shape" value="pill" <?php echo $options['ppcp_shape'] === 'pill' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Pill', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Label:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_label" value="paypal" <?php echo $options['ppcp_label'] === 'paypal' ? 'checked ' : ''; ?>/>
                                <?php _e( 'PayPal', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="pay" <?php echo $options['ppcp_label'] === 'pay' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Pay with', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="subscribe" <?php echo $options['ppcp_label'] === 'subscribe' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Subscribe', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="checkout" <?php echo $options['ppcp_label'] === 'checkout' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Checkout', 'wp-ecommerce-paypal' ); ?>
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="buynow" <?php echo $options['ppcp_label'] === 'buynow' ? 'checked ' : ''; ?>/>
                                <?php _e( 'Buy Now', 'wp-ecommerce-paypal' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Height:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <input type="number" name="ppcp_height" value="<?php echo $options['ppcp_height']; ?>" min="25" max="55" />
                            <br />
                            <?php _e( '25 - 55, a value around 40 is recommended', 'wp-ecommerce-paypal' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b><?php _e( 'Width:', 'wp-ecommerce-paypal' ); ?></b>
                        </td>
                        <td>
                            <input type="number" name="ppcp_width" value="<?php echo $options['ppcp_width']; ?>" />
                            <br />
                            <?php _e( 'Max buttons width in pixels', 'wp-ecommerce-paypal' ); ?>
                        </td>
                    </tr>
                </table>
                <br />
	        <?php } ?>
        </div>
		<?php
	} else { ?>
        <table id="ppcp-status-table" class="ppcp-initial-view-table">
            <tr>
                <td>
                    <img class="ppcp-paypal-logo" src="<?php echo WPECPP_FREE_URL; ?>assets/images/paypal-logo.png" alt="paypal-logo" />
                </td>
                <td class="ppcp-align-right ppcp-icons">
                    <img class="ppcp-paypal-methods" src="<?php echo WPECPP_FREE_URL; ?>assets/images/paypal-express.png" alt="paypal-expresss" />
                    <img class="ppcp-paypal-methods" src="<?php echo WPECPP_FREE_URL; ?>assets/images/paypal-advanced.png" alt="paypal-advanced" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3 class="ppcp-title"><?php _e( 'PayPal: The all-in-one checkout solution', 'wp-ecommerce-paypal' ); ?></h3>
                    <ul class="ppcp-list">
                        <li><?php _e( 'Help drive conversion by offering customers a seamless checkout experience', 'wp-ecommerce-paypal' ); ?></li>
                        <li><?php _e( 'Securely accepts all major credit/debit cards and local payment methods with the strength of the PayPal network', 'wp-ecommerce-paypal' ); ?></li>
                        <li><?php _e( 'You only pay the standard PayPal fees + 2%.', 'wp-ecommerce-paypal' ); ?></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="#TB_inline?&inlineId=ppcp-setup-account-modal" class="ppcp-button ppcp-onboarding-start thickbox" data-connect-mode="<?php echo $options['mode'] == 1 ? 'sandbox' : 'live'; ?>"><?php _e( 'Get started', 'wp-ecommerce-paypal' ); ?></a>
                </td>
                <td class="ppcp-align-right">
                    <a href="https://www.paypal.com/us/webapps/mpp/merchant-fees#statement-2" class="ppcp-link" target="_blank"><?php _e( 'View our simple and transparent pricing', 'wp-ecommerce-paypal' ); ?></a>
                </td>
            </tr>
        </table>
		<?php
	}

	if ( !wp_doing_ajax() ) {
		add_thickbox(); ?>
        <div id="ppcp-setup-account-modal" class="ppcp-modal">
            <div class="ppcp-setup-account">
                <h3><?php _e( 'Setup PayPal Account', 'wp-ecommerce-paypal' ); ?></h3>

                <div class="ppcp-field">
                    <label for="ppcp-country">
                        <?php _e( 'Select your country', 'wp-ecommerce-paypal' ); ?>
                    </label>
                    <select id="ppcp-country">
                        <option value="US"><?php _e( 'United States', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="AU"><?php _e( 'Australia', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="CA"><?php _e( 'Canada', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="UK"><?php _e( 'United Kingdom', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="DE"><?php _e( 'Germany', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="FR"><?php _e( 'France', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="IT"><?php _e( 'Italy', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="ES"><?php _e( 'Spain', 'wp-ecommerce-paypal' ); ?></option>
                        <option value="other"><?php _e( 'Other', 'wp-ecommerce-paypal' ); ?></option>
                    </select>
                </div>

                <div class="ppcp-field ppcp-checkbox-field">
                    <label class="ppcp-readonly">
                        <input type="checkbox" id="ppcp-accept-paypal" checked disabled /> <span class="ppcp-cb-view"></span><img src="<?php echo WPECPP_FREE_URL; ?>assets/images/paypal-accept-paypal.png" alt="paypal-accept-paypal" /> <?php _e( 'Accept PayPal', 'wp-ecommerce-paypal' ); ?>
                    </label>
                </div>

                <div class="ppcp-field ppcp-checkbox-field">
                    <label data-title="<?php _e( 'PayPal does not currently support PayPal Advanced Card Payments in your country.', 'wp-ecommerce-paypal' ); ?>">
                        <input type="checkbox" id="ppcp-accept-cards" /> <span class="ppcp-cb-view"></span> <img src="<?php echo WPECPP_FREE_URL; ?>assets/images/paypal-accept-cards.png" alt="paypal-accept-cards" /> <?php _e( 'Accept Credit and Debit Card Payments with PayPal', 'wp-ecommerce-paypal' ); ?>
                    </label>
                    <div class="ppcp-checkbox-note">* <?php _e( 'Direct Credit Card option will require a PayPal Business account and additional vetting.', 'wp-ecommerce-paypal' ); ?></div>
                </div>

                <div class="ppcp-field ppcp-checkbox-field">
                    <label>
                        <input type="checkbox" id="ppcp-sandbox" /> <span class="ppcp-cb-view"></span> <?php _e( 'Sandbox', 'wp-ecommerce-paypal' ); ?>
                    </label>
                </div>

                <div class="ppcp-buttons">
                    <script>
                        (function(d, s, id){
                            var js, ref = d.getElementsByTagName(s)[0]; if (!d.getElementById(id)){
                                js = d.createElement(s); js.id = id; js.async = true;
                                js.src =
                                    "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";
                                ref.parentNode.insertBefore(js, ref); }
                        }(document, "script", "paypal-js"));
                    </script>
                    <a
                            id="ppcp-onboarding-start-btn"
                            class="ppcp-button"
                            data-paypal-button="true"
                            href="<?php echo add_query_arg(
								[
									'action' => 'wpecpp-ppcp-onboarding-start',
									'nonce' => wp_create_nonce( 'ppcp-onboarding-start' ),
									'country' => 'US'
								],
								admin_url( 'admin-ajax.php' )
							); ?>"
                            target="PPFrame"
                    ><?php _e( 'Connect', 'wp-ecommerce-paypal' ); ?></a>
                    <button id="ppcp-setup-account-close-btn" class="ppcp-button ppcp-button-white"><?php _e( 'Cancel', 'wp-ecommerce-paypal' ); ?></button>
                </div>
            </div>
        </div>
	<?php }
}