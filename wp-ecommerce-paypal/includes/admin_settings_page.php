<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Returns HTML for setting page
 */
function wpecpp_settings_page() { ?>
    <div class="wrap">

        <?php if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page. Please sign in as an administrator.' ) );
        } ?>

	    <form method='post' action='<?php $_SERVER["REQUEST_URI"]; ?>'>
		    <?php wp_nonce_field( 'wpecpp-save-settings','wpecpp_save_settings_nonce' ); ?>

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
			            <span style="font-size:20pt;">PayPal & Stripe Settings</span>
                    </td>
                    <td valign="bottom">
			            <input type="submit" name='btn2' class='button-primary' style='font-size: 14px;height: 30px;float: right;' value="Save Settings">
                    </td>
                </tr>
            </table>
			
			<?php if ( !empty( $saved ) ) { ?>
                <div class='updated'><p>Settings Updated.</p></div>
			<?php } elseif ( !empty( $saved_error ) ) { ?>
                <div class='error'><p>Unable to update settings.</p></div>
		    <?php } ?>
		
		    <?php
			    $active_tab = isset( $_REQUEST['tab'] ) ? intval( $_REQUEST['tab'] ) : 1;
		    ?>

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
                        </script>

                        <h2 class="nav-tab-wrapper">
                            <a onclick='activateTab(event);' href="#" id="id1" class="nav-tab <?php echo $active_tab === 1 ? 'nav-tab-active' : ''; ?>">Getting Started</a>
                            <a onclick='activateTab(event);' href="#" id="id2" class="nav-tab <?php echo $active_tab === 2 ? 'nav-tab-active' : ''; ?>">Language & Currency</a>
                            <a onclick='activateTab(event);' href="#" id="id3" class="nav-tab <?php echo $active_tab === 3 ? 'nav-tab-active' : ''; ?>">PayPal</a>
                            <a onclick='activateTab(event);' href="#" id="id4" class="nav-tab <?php echo $active_tab === 4 ? 'nav-tab-active' : ''; ?>">Stripe</a>
                            <a onclick='activateTab(event);' href="#" id="id5" class="nav-tab <?php echo $active_tab === 5 ? 'nav-tab-active' : ''; ?>">Actions</a>
                            <a onclick='activateTab(event);' href="#" id="id6" class="nav-tab <?php echo $active_tab === 6 ? 'nav-tab-active' : ''; ?>">Shipping</a>
                        </h2>

                        <br />

                        <div id="1" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '1' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                Getting Started
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <h3>How to use this plugin</h3>
                                <br />
                                In a page or post editor, you will see a new button called 'PayPal / Stripe Button' located above the text area beside the 'Add Media' button. By using this, you can create shortcodes that will show up as a 'Buy Now' button on your site.
                                <br />
                                <br />
                                You can put the 'Buy Now' buttons as many times on a page or post as you want; there is no limit. If you want to remove a 'Buy Now' button, just remove the shortcode text on your page or post.
                                <br />
                                <h3>Help & Documentation</h3>
                                Help and Documentation manuals can be found on our website at <a target="_blank" href="https://wpplugin.org/documentation">wpplugin.org/documentation</a>.<br />
                                If you have are having a problem, contact support at <a target="_blank" href="https://wpplugin.org/contact">wpplugin.org/contact</a>.
                                <br />
                                <br />
                                <br />
                                <span style="color:#777;float:right;">
                                    <i>WPPlugin LLC is an offical PayPal & Stripe Partner. Various trademarks held by their respective owners.</i>
                                </span>
                                <br />
                            </div>
                        </div>

                        <div id="2" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '2' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                Language & Currency Settings
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="2">
                                            <h3>Language Settings</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Language:</b>
                                        </td>
                                        <td>
                                            <select name="language" style="width: 280px">
                                                <option <?php if ($options['language'] == "default") { echo "selected"; } ?> value="default">Default</option>
                                                <option <?php if ($options['language'] == "1") { echo "selected"; } ?> value="1">Danish</option>
                                                <option <?php if ($options['language'] == "2") { echo "selected"; } ?> value="2">Dutch</option>
                                                <option <?php if ($options['language'] == "3") { echo "selected"; } ?> value="3">English</option>
                                                <option <?php if ($options['language'] == "20") { echo "selected"; } ?> value="20">English - UK</option>
                                                <option <?php if ($options['language'] == "4") { echo "selected"; } ?> value="4">French</option>
                                                <option <?php if ($options['language'] == "5") { echo "selected"; } ?> value="5">German</option>
                                                <option <?php if ($options['language'] == "6") { echo "selected"; } ?> value="6">Hebrew</option>
                                                <option <?php if ($options['language'] == "7") { echo "selected"; } ?> value="7">Italian</option>
                                                <option <?php if ($options['language'] == "8") { echo "selected"; } ?> value="8">Japanese</option>
                                                <option <?php if ($options['language'] == "9") { echo "selected"; } ?> value="9">Norwegian</option>
                                                <option <?php if ($options['language'] == "10") { echo "selected"; } ?> value="10">Polish</option>
                                                <option <?php if ($options['language'] == "11") { echo "selected"; } ?> value="11">Portuguese</option>
                                                <option <?php if ($options['language'] == "12") { echo "selected"; } ?> value="12">Russian</option>
                                                <option <?php if ($options['language'] == "13") { echo "selected"; } ?> value="13">Spanish</option>
                                                <option <?php if ($options['language'] == "14") { echo "selected"; } ?> value="14">Swedish</option>
                                                <option <?php if ($options['language'] == "15") { echo "selected"; } ?> value="15">Simplified Chinese -China only</option>
                                                <option <?php if ($options['language'] == "16") { echo "selected"; } ?> value="16">Traditional Chinese - Hong Kong only</option>
                                                <option <?php if ($options['language'] == "17") { echo "selected"; } ?> value="17">Traditional Chinese - Taiwan only</option>
                                                <option <?php if ($options['language'] == "18") { echo "selected"; } ?> value="18">Turkish</option>
                                                <option <?php if ($options['language'] == "19") { echo "selected"; } ?> value="19">Thai</option>
                                            </select>
                                        </td>
                                        <td>
                                            PayPal currently supports 18 languages.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <br />
                                            <h3>Currency Settings</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Currency:</b>
                                        </td>
                                        <td>
                                            <select name="currency" style="width: 280px">
                                                <option <?php if ($options['currency'] == "1") { echo "selected"; } ?> value="1">Australian Dollar - AUD</option>
                                                <option <?php if ($options['currency'] == "2") { echo "selected"; } ?> value="2">Brazilian Real - BRL</option>
                                                <option <?php if ($options['currency'] == "3") { echo "selected"; } ?> value="3">Canadian Dollar - CAD</option>
                                                <option <?php if ($options['currency'] == "4") { echo "selected"; } ?> value="4">Czech Koruna - CZK</option>
                                                <option <?php if ($options['currency'] == "5") { echo "selected"; } ?> value="5">Danish Krone - DKK</option>
                                                <option <?php if ($options['currency'] == "6") { echo "selected"; } ?> value="6">Euro - EUR</option>
                                                <option <?php if ($options['currency'] == "7") { echo "selected"; } ?> value="7">Hong Kong Dollar - HKD</option>
                                                <option <?php if ($options['currency'] == "8") { echo "selected"; } ?> value="8">Hungarian Forint - HUF</option>
                                                <option <?php if ($options['currency'] == "9") { echo "selected"; } ?> value="9">Israeli New Sheqel - ILS</option>
                                                <option <?php if ($options['currency'] == "10") { echo "selected"; } ?> value="10">Japanese Yen - JPY</option>
                                                <option <?php if ($options['currency'] == "11") { echo "selected"; } ?> value="11">Malaysian Ringgit - MYR</option>
                                                <option <?php if ($options['currency'] == "12") { echo "selected"; } ?> value="12">Mexican Peso - MXN</option>
                                                <option <?php if ($options['currency'] == "13") { echo "selected"; } ?> value="13">Norwegian Krone - NOK</option>
                                                <option <?php if ($options['currency'] == "14") { echo "selected"; } ?> value="14">New Zealand Dollar - NZD</option>
                                                <option <?php if ($options['currency'] == "15") { echo "selected"; } ?> value="15">Philippine Peso - PHP</option>
                                                <option <?php if ($options['currency'] == "16") { echo "selected"; } ?> value="16">Polish Zloty - PLN</option>
                                                <option <?php if ($options['currency'] == "17") { echo "selected"; } ?> value="17">Pound Sterling - GBP</option>
                                                <option <?php if ($options['currency'] == "18") { echo "selected"; } ?> value="18">Russian Ruble - RUB</option>
                                                <option <?php if ($options['currency'] == "19") { echo "selected"; } ?> value="19">Singapore Dollar - SGD</option>
                                                <option <?php if ($options['currency'] == "20") { echo "selected"; } ?> value="20">Swedish Krona - SEK</option>
                                                <option <?php if ($options['currency'] == "21") { echo "selected"; } ?> value="21">Swiss Franc - CHF</option>
                                                <option <?php if ($options['currency'] == "22") { echo "selected"; } ?> value="22">Taiwan New Dollar - TWD</option>
                                                <option <?php if ($options['currency'] == "23") { echo "selected"; } ?> value="23">Thai Baht - THB</option>
                                                <option <?php if ($options['currency'] == "24") { echo "selected"; } ?> value="24">Turkish Lira - TRY</option>
                                                <option <?php if ($options['currency'] == "25") { echo "selected"; } ?> value="25">U.S. Dollar - USD</option>
                                            </select>
                                        </td>
                                        <td>
                                            PayPal currently supports 25 currencies.
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <br />
                            </div>
                        </div>

                        <div id="3" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '3' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                PayPal Settings
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="2">
                                            <h3>PayPal Account</h3>
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
                                        <h3>PayPal Standard</h3>
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
                                            <b>Live Account:</b>
                                        </td>
                                        <td>
                                            <input type='text' name='liveaccount' value='<?php echo $options['liveaccount']; ?>' readonly />
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ( !empty( $options['sandboxaccount'] ) ) { ?>
                                    <tr>
                                        <td>
                                            <b>Sandbox Account:</b>
                                        </td>
                                        <td>
                                            <input type='text' name='sandboxaccount' value='<?php echo $options['sandboxaccount']; ?>' readonly />
                                        </td>
                                    </tr>
                                <?php } ?>


                                <?php
                                    if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) {
                                        echo "<tr><td></td><td>PayPal Standard is now deprecated. You cannot modify your Standard settings. We highly recommend using PayPal Commerce.</td></tr>";
                                    }
                                ?>


                                    <tr>
                                        <td colspan="2">
                                            <h3>PayPal Options</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Sandbox Mode:</b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['mode'] == "1") { echo "checked='checked'"; } ?> type='radio' name='mode' value='1'>
                                                On (Sandbox mode)
                                            </label>
                                            &nbsp;
                                            &nbsp;
                                            <label>
                                                <input <?php if ($options['mode'] == "2") { echo "checked='checked'"; } ?> type='radio' name='mode' value='2'>
                                                Off (Live mode)
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Disable PayPal:</b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['disable_paypal'] == "1") { echo "checked='checked'"; } ?> type='radio' name='disable_paypal' value='1'>
                                                No
                                            </label>
                                            &nbsp;
                                            &nbsp;
                                            <label>
                                                <input <?php if ($options['disable_paypal'] == "2") { echo "checked='checked'"; } ?> type='radio' name='disable_paypal' value='2'>
                                                Yes
                                            </label>
                                        </td>
                                    </tr>

                                    <?php if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) { ?>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Payment Action:</b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['paymentaction'] == "1") { echo "checked='checked'"; } ?> type='radio' name='paymentaction' value='1'>
                                                Sale (Default)
                                            </label>
                                            &nbsp;
                                            &nbsp;
                                            <label>
                                                <input <?php if ($options['paymentaction'] == "2") { echo "checked='checked'"; } ?> type='radio' name='paymentaction' value='2'>
                                                Authorize (Learn more <a target='_blank' href='https://developer.paypal.com/docs/checkout/standard/customize/authorization/'>here</a>)
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
                                Stripe Settings
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table id="wpecpp-stripe-connect-table">
                                    <tr>
                                        <td colspan="2">
                                            <h3>Stripe Account</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='2'>
                                            <br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Connection status: </b>
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
                                            <b>Width:</b>
                                        </td>
                                        <td>
                                            <input type="number" name="stripe_width" value="<?php echo $options['stripe_width']; ?>" />
                                            <br />
                                            Max button width in pixels
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='2'>
                                            <br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Sandbox Mode:</b>
                                        </td>
                                        <td>
                                            <label>
                                                <input type='radio' name='mode_stripe' value='1' <?php echo ( $options['mode_stripe'] != '2' ) ? 'checked' : ''; ?> />
                                                On (Sandbox mode)
                                            </label>
                                            &nbsp; &nbsp;
                                            <label>
                                                <input type='radio' name='mode_stripe' value='2' <?php echo ( $options['mode_stripe'] == '2' ) ? 'checked' : ''; ?> />
                                                Off (Live mode)
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Disable Stripe:</b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['disable_stripe'] == "1") { echo "checked='checked'"; } ?> type='radio' name='disable_stripe' value='1'>
                                                No
                                            </label>
                                            &nbsp; &nbsp;
                                            <label>
                                                <input <?php if ($options['disable_stripe'] == "2") { echo "checked='checked'"; } ?> type='radio' name='disable_stripe' value='2'>
                                                Yes
                                            </label>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                            </div>
                        </div>

                        <div id="5" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '5' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                Action Settings
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="2">
                                            <h3>Action Settings</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Button opens in:</b>
                                        </td>
                                        <td>
                                            <label>
                                                <input <?php if ($options['opens'] == "1") { echo "checked='checked'"; } ?>  type='radio' name='opens' value='1'>
                                                Same window
                                            </label>
                                            &nbsp; &nbsp;
                                            <label>
                                                <input <?php if ($options['opens'] == "2") { echo "checked='checked'"; } ?> type='radio' name='opens' value='2'>
                                                New window
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left"></td>
                                        <td>
                                            Note: PayPal can only open in a popup window.<br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Default Cancel URL:</b>
                                        </td>
                                        <td>
                                            <input type='text' name='cancel' value='<?php echo $options['cancel']; ?>'> Optional
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left"></td>
                                        <td>
                                            If the customer goes to PayPal and clicks the cancel button, where do they go. Example: <?php echo get_site_url(); ?>/cancel. Max length: 1,024.<br /><br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Default Return URL:</b>
                                        </td>
                                        <td>
                                            <input type='text' name='return' value='<?php echo $options['return']; ?>'> Optional
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left"></td>
                                        <td>
                                            If the customer goes to PayPal and successfully pays, where are they redirected to after. Example: <?php echo get_site_url(); ?>/thankyou. Max length: 1,024.
                                        </td>
                                    </tr>
                                </table>
                                <br />
                            </div>
                        </div>

                        <div id="6" style="display:none;border: 1px solid #CCCCCC;<?php echo $active_tab == '6' ? 'display:block;' : ''; ?>">
                            <div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
                                Shipping Settings
                            </div>
                            <div style="background-color:#fff;padding:8px;">
                                <table>
                                    <tr>
                                        <td colspan="3">
                                            <h3>Shipping Options</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="wpecpp-cell-left">
                                            <b>Require Shipping Address: </b>
                                        </td>
                                        <td>
	                                        <?php
	                                        if (empty($options['address'])) {
		                                        $options['address'] = "0";
	                                        }
	                                        ?>
                                            <select name="address" id="address">
		                                        <?php if ( !empty( $options['liveaccount'] ) || !empty( $options['sandboxaccount'] ) ) { ?>
                                                    <option value="0" <?php if ($options['address'] == "0") { echo "SELECTED"; } ?>>Prompt for an address, but do not require one (default)</option>
                                                    <option value="1" <?php if ($options['address'] == "1") { echo "SELECTED"; } ?>>Do not prompt for an address</option>
                                                    <option value="2" <?php if ($options['address'] == "2") { echo "SELECTED"; } ?>>Prompt for an address, and require one</option>
		                                        <?php } else { ?>
                                                    <option value="1" <?php if ($options['address'] == "1") { echo "SELECTED"; } ?>>Do not prompt for an address</option>
                                                    <option value="2" <?php if ( in_array( $options['address'], ['0', '2'] ) ) { echo "SELECTED"; } ?>>Prompt for an address, and require one</option>
		                                        <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            Optional - Should the customer be asked for a shipping address at PayPal checkout.
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
                            &nbsp; PayPal Buy Now Button Pro
                        </div>

                        <div style="background-color:#fff;border: 1px solid #CCC;padding:8px;">
                            <center><label style="font-size:14pt;">With the Pro version you'll <br /> be able to: </label></center>
                            <br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Manage Buttons In One Place<br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> View Sales In Your Dashboard<br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Send out Email Notifications<br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Inventory Management<br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Separate PayPal Accounts<br />
                            <div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Add Discounts <br />
                            <br />
                            <center><a target='_blank' href="https://wpplugin.org/downloads/easy-paypal-buy-now-button/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Learn More</a></center>
                            <br />
                        </div>
                        <br />
                        <br />
                        <div style="background-color:#E4E4E4;padding:8px;color:#464646;font-size:15px;font-weight:bold;border:1px solid #CCC;border-bottom: none">
                            &nbsp; Quick Links
                        </div>
                        <div style="background-color:#fff;border: 1px solid #CCC;padding:8px;">
                            <br />
                            <div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wordpress.org/support/plugin/wp-ecommerce-paypal">Support Forum</a> <br />
                            <div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/documentation/">FAQ</a> <br />
                            <div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/downloads/easy-paypal-buy-now-button/">PayPal Button Pro</a> <br />
                        </div>
                    </td>
                </tr>
            </table>

		    <input type='hidden' name='update' value='1'>
		    <input type='hidden' name='tab' id="active-tab" value="<?php echo $active_tab; ?>">
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
                        <b>Connection status: </b>
                    </td>
                    <td>
                        <div class="notice inline notice-<?php echo $notice_type; ?>">
                            <p>
                                <?php if ( !empty( $status['legal_name'] ) ) { ?>
                                <strong><?php echo $status['legal_name']; ?></strong>
                                <br>
                                <?php } ?>
	                            <?php echo !empty( $status['primary_email'] ) ? $status['primary_email'] . ' — ' : ''; ?>Administrator (Owner)
                            </p>
                            <p>Pay as you go pricing: 2% per-transaction fee + PayPal fees.</p>
                        </div>
                        <div>
                            <?php $reconnect_mode = $status['env'] === 'live' ? 'sandbox' : 'live'; ?>
                            Your PayPal account is connected in <strong><?php echo $status['env']; ?></strong> mode.
                            <a href="#TB_inline?&inlineId=ppcp-setup-account-modal" class="ppcp-onboarding-start thickbox" data-connect-mode="<?php echo $reconnect_mode; ?>">
                                Connect in <strong><?php echo $reconnect_mode; ?></strong> mode
                            </a> or <a href="#" id="ppcp-disconnect">disconnect this account</a>.
                        </div>

                        <?php if ( $status['mode'] === 'error' ) { ?>
                            <p>
                                <strong>There were errors connecting your PayPal account. Resolve them in your account settings, by contacting support or by reconnecting your PayPal account.</strong>
                            </p>
                            <?php if ( !empty( $status['errors'] ) ) { ?>
                                <p>
                                    <strong>See below for more details.</strong>
                                </p>
                                <ul class="ppcp-list ppcp-list-error">
                                    <?php foreach ( $status['errors'] as $error ) { ?>
                                        <li><?php echo $error; ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        <?php } elseif ( !empty( $status['warnings'] ) ) { ?>
                            <p>
                                <strong>Please review the warnings below and resolve them in your account settings or by contacting support.</strong>
                            </p>
                            <ul class="ppcp-list ppcp-list-warning">
		                        <?php foreach ( $status['warnings'] as $warning ) { ?>
                                    <li><?php echo $warning; ?></li>
		                        <?php } ?>
                            </ul>
                        <?php } ?>

	                    <?php if ( $show_links ) { ?>
                            <ul class="ppcp-list">
                                <li><a href="https://www.paypal.com/myaccount/settings/">PayPal account settings</a></li>
                                <li><a href="https://www.paypal.com/us/smarthelp/contact-us">PayPal support</a></li>
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
                            <h3>Payments Methods Accepted</h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>PayPal:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_paypal" value="1" <?php echo !empty( $options['ppcp_funding_paypal'] ) ? 'checked ' : ''; ?>/>
                                On
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_paypal" value="0" <?php echo empty( $options['ppcp_funding_paypal'] ) ? 'checked ' : ''; ?>/>
                                Off
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>PayPal PayLater:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_paylater" value="1" <?php echo !empty( $options['ppcp_funding_paylater'] ) ? 'checked ' : ''; ?>/>
                                On
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_paylater" value="0" <?php echo empty( $options['ppcp_funding_paylater'] ) ? 'checked ' : ''; ?>/>
                                Off
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Venmo:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_venmo" value="1" <?php echo !empty( $options['ppcp_funding_venmo'] ) ? 'checked ' : ''; ?>/>
                                On
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_venmo" value="0" <?php echo empty( $options['ppcp_funding_venmo'] ) ? 'checked ' : ''; ?>/>
                                Off
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Local Alternative Payment Methods:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_alternative" value="1" <?php echo !empty( $options['ppcp_funding_alternative'] ) ? 'checked ' : ''; ?>/>
                                On
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_alternative" value="0" <?php echo empty( $options['ppcp_funding_alternative'] ) ? 'checked ' : ''; ?>/>
                                Off
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Credit & Debit Cards:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_cards" value="1" <?php echo !empty( $options['ppcp_funding_cards'] ) ? 'checked ' : ''; ?>/>
                                On
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_cards" value="0" <?php echo empty( $options['ppcp_funding_cards'] ) ? 'checked ' : ''; ?>/>
                                Off
                            </label>
                        </td>
                    </tr>

                    <?php if ( $status['mode'] === 'advanced' ) { ?>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Advanced Credit & Debit Cards (ACDC):</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_funding_advanced_cards" value="1" <?php echo !empty( $options['ppcp_funding_advanced_cards'] ) ? 'checked ' : ''; ?>/>
                                On
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_funding_advanced_cards" value="0" <?php echo empty( $options['ppcp_funding_advanced_cards'] ) ? 'checked ' : ''; ?>/>
                                Off
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>ACDC Button text:</b>
                        </td>
                        <td>
                            <input type="text" name="ppcp_acdc_button_text" value="<?php echo $options['ppcp_acdc_button_text']; ?>" />
                            <br />
                            Payment button text
                        </td>
                    </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="2">
                            <br />
                            <h3>PayPal Checkout Buttons</h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Layout:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_layout" value="horizontal" <?php echo $options['ppcp_layout'] === 'horizontal' ? 'checked ' : ''; ?>/>
                                Horizontal
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_layout" value="vertical" <?php echo $options['ppcp_layout'] === 'vertical' ? 'checked ' : ''; ?>/>
                                Vertical
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Color:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_color" value="gold" <?php echo $options['ppcp_color'] === 'gold' ? 'checked ' : ''; ?>/>
                                Gold
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="blue" <?php echo $options['ppcp_color'] === 'blue' ? 'checked ' : ''; ?>/>
                                Blue
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="black" <?php echo $options['ppcp_color'] === 'black' ? 'checked ' : ''; ?>/>
                                Black
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="silver" <?php echo $options['ppcp_color'] === 'silver' ? 'checked ' : ''; ?>/>
                                Silver
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_color" value="white" <?php echo $options['ppcp_color'] === 'white' ? 'checked ' : ''; ?>/>
                                White
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Shape:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_shape" value="rect" <?php echo $options['ppcp_shape'] === 'rect' ? 'checked ' : ''; ?>/>
                                Rectangle
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_shape" value="pill" <?php echo $options['ppcp_shape'] === 'pill' ? 'checked ' : ''; ?>/>
                                Pill
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Label:</b>
                        </td>
                        <td>
                            <label>
                                <input type="radio" name="ppcp_label" value="paypal" <?php echo $options['ppcp_label'] === 'paypal' ? 'checked ' : ''; ?>/>
                                PayPal
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="pay" <?php echo $options['ppcp_label'] === 'pay' ? 'checked ' : ''; ?>/>
                                Pay with
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="subscribe" <?php echo $options['ppcp_label'] === 'subscribe' ? 'checked ' : ''; ?>/>
                                Subscribe
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="checkout" <?php echo $options['ppcp_label'] === 'checkout' ? 'checked ' : ''; ?>/>
                                Checkout
                            </label>
                            &nbsp;
                            &nbsp;
                            <label>
                                <input type="radio" name="ppcp_label" value="buynow" <?php echo $options['ppcp_label'] === 'buynow' ? 'checked ' : ''; ?>/>
                                Buy Now
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
                            <b>Height:</b>
                        </td>
                        <td>
                            <input type="number" name="ppcp_height" value="<?php echo $options['ppcp_height']; ?>" min="25" max="55" />
                            <br />
                            25 - 55, a value around 40 is recommended
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td class="wpecpp-cell-left">
                            <b>Width:</b>
                        </td>
                        <td>
                            <input type="number" name="ppcp_width" value="<?php echo $options['ppcp_width']; ?>" />
                            <br />
                            Max buttons width in pixels
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
                    <h3 class="ppcp-title">PayPal: The all-in-one checkout solution</h3>
                    <ul class="ppcp-list">
                        <li>Help drive conversion by offering customers a seamless checkout experience</li>
                        <li>Securely accepts all major credit/debit cards and local payment methods with the strength of the PayPal network</li>
                        <li>You only pay the standard PayPal fees + 2%.</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="#TB_inline?&inlineId=ppcp-setup-account-modal" class="ppcp-button ppcp-onboarding-start thickbox" data-connect-mode="<?php echo $options['mode'] == 1 ? 'sandbox' : 'live'; ?>">Get started</a>
                </td>
                <td class="ppcp-align-right">
                    <a href="https://www.paypal.com/us/webapps/mpp/merchant-fees#statement-2" class="ppcp-link" target="_blank">View our simple and transparent pricing</a>
                </td>
            </tr>
        </table>
		<?php
	}

	if ( !wp_doing_ajax() ) {
		add_thickbox(); ?>
        <div id="ppcp-setup-account-modal" class="ppcp-modal">
            <div class="ppcp-setup-account">
                <h3>Setup PayPal Account</h3>

                <div class="ppcp-field">
                    <label for="ppcp-country">
                        Select your country
                    </label>
                    <select id="ppcp-country">
                        <option value="US">United States</option>
                        <option value="AU">Australia</option>
                        <option value="CA">Canada</option>
                        <option value="UK">United Kingdom</option>
                        <option value="DE">Germany</option>
                        <option value="FR">France</option>
                        <option value="IT">Italy</option>
                        <option value="ES">Spain</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="ppcp-field ppcp-checkbox-field">
                    <label class="ppcp-readonly">
                        <input type="checkbox" id="ppcp-accept-paypal" checked disabled /> <span class="ppcp-cb-view"></span><img src="<?php echo WPECPP_FREE_URL; ?>assets/images/paypal-accept-paypal.png" alt="paypal-accept-paypal" /> Accept PayPal
                    </label>
                </div>

                <div class="ppcp-field ppcp-checkbox-field">
                    <label data-title="PayPal does not currently support PayPal Advanced Card Payments in your country.">
                        <input type="checkbox" id="ppcp-accept-cards" /> <span class="ppcp-cb-view"></span> <img src="<?php echo WPECPP_FREE_URL; ?>assets/images/paypal-accept-cards.png" alt="paypal-accept-cards" /> Accept Credit and Debit Card Payments with PayPal
                    </label>
                    <div class="ppcp-checkbox-note">* Direct Credit Card option will require a PayPal Business account and additional vetting.</div>
                </div>

                <div class="ppcp-field ppcp-checkbox-field">
                    <label>
                        <input type="checkbox" id="ppcp-sandbox" /> <span class="ppcp-cb-view"></span> Sandbox
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
                    >Connect</a>
                    <button id="ppcp-setup-account-close-btn" class="ppcp-button ppcp-button-white">Cancel</button>
                </div>
            </div>
        </div>
	<?php }
}