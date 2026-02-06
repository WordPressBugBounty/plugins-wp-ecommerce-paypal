<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Add shortcode
 */
add_shortcode( 'wpecpp', 'wpecpp_shortcode' );
function wpecpp_shortcode( $atts ) {
	// get shortcode values
	$atts = shortcode_atts(
        [
            'name' => '',
            'price' => '',
            'size' => '',
            'align' => '',
            'id' => 0
        ],
        $atts
    );
    $atts = array_map( 'esc_attr', $atts );
    
    // Default values for payment methods
    $enable_paypal = true;
    $enable_stripe = true;
    
    // If ID is provided, get shortcode data from database
    if (!empty($atts['id']) && absint($atts['id']) > 0) {
        $post_id = absint($atts['id']);
        $post = get_post($post_id);
        
        // Check if the button exists and is published
        if (!$post || $post->post_type !== 'wpplugin_pp_button' || $post->post_status !== 'publish') {
            return '<p class="wpecpp-error">' . esc_html__('This payment button was removed. Please contact the website owner.', 'wp-ecommerce-paypal') . '</p>';
        }
        
        // Button exists and is published, so get its data
        $atts['name'] = $post->post_title;
        $atts['price'] = get_post_meta($post_id, 'wpplugin_paypal_button_price', true);
        $atts['align'] = get_post_meta($post_id, '_wpecpp_alignment', true);
        
        // Get payment method settings
        $enable_paypal_meta = get_post_meta($post_id, 'wpplugin_paypal_button_disable_paypal', true);
        $enable_stripe_meta = get_post_meta($post_id, 'wpplugin_paypal_button_disable_stripe', true);
        
        // If meta values are set, use them ('' means it's not set, so use default)
        if ($enable_paypal_meta !== '') {
            $enable_paypal = ($enable_paypal_meta === '1');
        }
        
        if ($enable_stripe_meta !== '') {
            $enable_stripe = ($enable_stripe_meta === '1');
        }

        // Check if quantity is set and valid
        $quantity = get_post_meta($post_id, 'wpplugin_paypal_button_quantity', true);
        // Only use quantity if it's set and is a valid positive number
        if ($quantity !== '' && intval($quantity) > 0) {
            $atts['quantity'] = intval($quantity);
        } else {
            // Don't set quantity if it's not provided
            unset($atts['quantity']);
        }
    }

	$rand_string = 'r' . md5(uniqid(rand(), true));

	// alignment
    switch ( $atts['align'] ) {
        case 'left':
	        $alignment = ' wpecpp-align-left';
            break;
	    case 'right':
		    $alignment = ' wpecpp-align-right';
		    break;
	    case 'center':
		    $alignment = ' wpecpp-align-center';
		    break;
        default:
	        $alignment = ' wpecpp-align-left';
    }

	// paypal account data - only if PayPal is enabled
	$paypal_connection_data = $enable_paypal ? wpecpp_paypal_connection_data( $atts['size'] ) : null;

    // stripe account data - only if Stripe is enabled
	$stripe_account_data = $enable_stripe ? wpecpp_stripe_account_data() : null;

	$output = "<div class='wpecpp-container{$alignment}'>";
	if ( empty( $paypal_connection_data ) && empty( $stripe_account_data ) ) {
		if (!$enable_paypal && !$enable_stripe) {
            $output .= __( 'No payment methods are enabled for this button.', 'wp-ecommerce-paypal' );
        } else {
            $output .= __( '(Please enter your Payment methods data on the settings pages.)', 'wp-ecommerce-paypal' );
        }
	} else {
        if ( !empty( $paypal_connection_data ) && $paypal_connection_data['connection_type'] === 'manual' ) {
	        $output .= "<form class='wpecpp-form' target='{$paypal_connection_data['target']}' id={$rand_string} action='https://www.{$paypal_connection_data['path']}.com/cgi-bin/webscr' method='post'>";
	        $output .= "<input type='hidden' name='cmd' value='_xclick' />";
	        $output .= "<input type='hidden' name='business' value='{$paypal_connection_data['account']}' />";
	        $output .= "<input type='hidden' name='currency_code' value='{$paypal_connection_data['currency']}' />";
	        $output .= "<input type='hidden' name='lc' value='{$paypal_connection_data['locale']}'>";
	        $output .= "<input type='hidden' name='no_note' value=''>";
	        $output .= "<input type='hidden' name='paymentaction' value='{$paypal_connection_data['paymentaction']}'>";
	        $output .= "<input type='hidden' name='return' value='{$paypal_connection_data['return']}' />";
	        $output .= "<input type='hidden' name='bn' value='WPPlugin_SP'>";
	        $output .= "<input type='hidden' name='cancel_return' value='{$paypal_connection_data['cancel']}' />";
	        $output .= "<input style='border: none;' class='paypalbuttonimage' type='image' src='{$paypal_connection_data['img']}' border='0' name='submit' alt='" . __('Make your payments with PayPal. It is free, secure, effective.', 'wp-ecommerce-paypal') . "' />";
	        $output .= "<img alt='' border='0' style='border:none;display:none;' src='https://www.paypal.com/{$paypal_connection_data['locale']}/i/scr/pixel.gif' width='1' height='1' />";
        } else {
	        $form_classes = ['wpecpp-form'];
	        if ( empty( $paypal_connection_data['advanced_cards'] ) ) {
		        $form_classes[] = 'wpecpp-form-disabled';
	        }
	        $form_classes = implode( ' ', $form_classes );
	        $output .= "<form class='{$form_classes}' id='{$rand_string}' action='#' method='post'>";
        }

		if ( !empty( $paypal_connection_data ) && $paypal_connection_data['connection_type'] === 'ppcp' ) {
			$output .= wpecpp_ppcp_html( $paypal_connection_data, $rand_string );
		}

		if ( !empty( $stripe_account_data ) ) {
			$message = '';
			if ( isset( $_GET['wpecpp_stripe_success'] ) ) {
				if ( $_GET['wpecpp_stripe_success'] == 1 ) {
					$message = '<span class="payment-success">' . __( 'The payment was successful', 'wp-ecommerce-paypal' ) . '</span>';
				} elseif ( $_GET['wpecpp_stripe_success'] == 0 ) {
					if ( isset($_GET['payment_cancelled']) && $_GET['payment_cancelled'] == 1 ) {
						$message = '<span class="payment-error">' . __( 'The payment was cancelled.', 'wp-ecommerce-paypal' ) . '</span>';
					} else {
						$message = '<span class="payment-error">' . __( 'An unknown payment error has occurred. Please try again', 'wp-ecommerce-paypal' ) . '</span>';
					}
				}
			}
		    $output .= "<style>.wpecpp-stripe-button-container > * {max-width: {$stripe_account_data['width']}px;}</style>";
			$output .= "<div class='wpecpp-stripe-button-container'>";
			$output .= "<a href='#' class='wpecpp-stripe-button'><span>" . __( 'Pay with Stripe', 'wp-ecommerce-paypal' ) . "</span></a>";
			$output .= "</div>";
            $output .= "<div class='wpecpp-payment-message'>{$message}</div>";
		}

		$output .= "<input type='hidden' name='item_name' value='{$atts['name']}' />";
		$output .= "<input type='hidden' name='amount' value='{$atts['price']}' />";
		// Add quantity to PayPal manual form if available
		if (isset($atts['quantity']) && $atts['quantity'] > 0) {
			$output .= "<input type='hidden' name='quantity' value='{$atts['quantity']}' />";
		}
		$output .= "</form>";
    }
	$output .= '</div>';

	return $output;
}

function wpecpp_ppcp_html( $connection_data, $rand_string ) {
    $sdk_attr = [
	    'client-id' => $connection_data['client_id'],
        'merchant-id' => $connection_data['seller_id'],
        'currency' => $connection_data['currency'],
        'intent' => $connection_data['intent'],
	    'components' => 'buttons,funding-eligibility'
    ];

	if ( !empty( $connection_data['advanced_cards'] ) ) {
		$sdk_attr['components'] .= ',hosted-fields';
	}

    if ( $connection_data['locale'] !== 'default' ) {
	    $sdk_attr['locale'] = $connection_data['locale'];
    }

	$wpecpp_paypal_funding = json_encode( $connection_data['enable-funding'] );

	$enable_funding = array_filter( $connection_data['enable-funding'], function($i) { return $i !== 'paypal'; } );
    if ( !empty( $enable_funding ) ) {
	    $sdk_attr['enable-funding'] = implode( ',', $enable_funding );
    }

    $sdk_url = add_query_arg( $sdk_attr, 'https://www.paypal.com/sdk/js' );

    ob_start();
    
    $sdk_attr_hash = md5( json_encode( $sdk_attr ) );
    ?>
    
    <!-- PayPal SDK Loader with DOM check -->
    <script>
    (function() {
        var sdkId = 'wpecpp-paypal-sdk-<?php echo $sdk_attr_hash; ?>';
        var sdkUrl = '<?php echo $sdk_url; ?>';
        
        // Check if SDK script already exists in DOM or is being loaded
        if (!document.getElementById(sdkId)) {
            var script = document.createElement('script');
            script.id = sdkId;
            script.src = sdkUrl;
            script.setAttribute('data-partner-attribution-id', '<?php echo $connection_data['bn_code']; ?>');
            <?php if ( !empty( $connection_data['advanced_cards'] ) ) { ?>
            script.setAttribute('data-client-token', '<?php echo $connection_data['client_token']; ?>');
            <?php } ?>
            document.head.appendChild(script);
        }
    })();
    </script>
    
    <style>
        .wpecpp-paypal-button-container > *,
        .wpecpp-paypal-hosted-fields-container .wpecpp-paypal-btn {
            max-width: <?php echo $connection_data['width']; ?>px;
        }
        .wpecpp-paypal-hosted-fields-container .wpecpp-paypal-btn {
            height: <?php echo $connection_data['height']; ?>px;
        }
    </style>
    
    <script>
        const wpecppPaypalFunding_<?php echo $rand_string; ?> = <?php echo $wpecpp_paypal_funding; ?>;
    </script>

    <!-- Buttons container -->
    <div id='wpecpp-paypal-button-container-<?php echo $rand_string; ?>' class='wpecpp-paypal-button-container wpecpp-<?php echo $connection_data['layout']; ?>'></div>

	<?php if ( !empty( $connection_data['advanced_cards'] ) ) { ?>
    <!-- Advanced credit and debit card payments form -->
    <div class="wpecpp-or"><span><?php _e('or', 'wp-ecommerce-paypal'); ?></span></div>
    <div id='wpecpp-paypal-hosted-fields-container-<?php echo $rand_string; ?>' class='wpecpp-paypal-button-container wpecpp-paypal-hosted-fields-container wpecpp-<?php echo $connection_data['layout']; ?>'>
        <div id="wpecpp-card-form-<?php echo $rand_string; ?>" class="wpecpp-card-form">
            <div class="card-field-wrapper">
                <div id="number-<?php echo $rand_string; ?>" class="card_field"></div>
            </div>
            <div class="card-field-wrapper">
                <div id="expirationDate-<?php echo $rand_string; ?>" class="card_field"></div>
            </div>
            <div class="card-field-wrapper">
                <div id="cvv-<?php echo $rand_string; ?>" class="card_field"></div>
            </div>
            <div class="card-field-wrapper">
                <input type="text" id="card-holder-first-name-<?php echo $rand_string; ?>" name="card-holder-first-name-<?php echo $rand_string; ?>" autocomplete="off" placeholder="First name" class="card_field" />
            </div>
            <div class="card-field-wrapper">
                <input type="text" id="card-holder-last-name-<?php echo $rand_string; ?>" name="card-holder-last-name-<?php echo $rand_string; ?>" autocomplete="off" placeholder="Last name" class="card_field" />
            </div>
            <div class="card-field-wrapper">
                <div id="postalCode-<?php echo $rand_string; ?>" class="card_field"></div>
            </div>
            <div>
                <button class="wpecpp-paypal-btn color-<?php echo $connection_data['color']; ?>"><?php echo $connection_data['acdc_button_text']; ?></button>
            </div>
        </div>
    </div>
	<?php } ?>

    <div id='wpecpp-paypal-message-<?php echo $rand_string; ?>' class='wpecpp-payment-message'></div>

    <script>
        (function() {
            const message_<?php echo $rand_string; ?> = document.getElementById('wpecpp-paypal-message-<?php echo $rand_string; ?>');
            
            // Wait for SDK to be fully loaded with polling
            function initPayPalButton_<?php echo $rand_string; ?>() {
                // Check if PayPal SDK is fully loaded with all required methods
                if ( typeof paypal === 'undefined' || 
                     typeof paypal.getFundingSources !== 'function' ||
                     typeof paypal.Buttons !== 'function' ) {
                    // SDK not loaded yet, wait and try again
                    setTimeout(initPayPalButton_<?php echo $rand_string; ?>, 100);
                    return;
                }

        paypal.getFundingSources().forEach(function (fundingSource) {
            if ( wpecppPaypalFunding_<?php echo $rand_string; ?>.indexOf(fundingSource) > -1 ) {
                const style = {
                    shape: '<?php echo $connection_data['shape']; ?>',
                    label: '<?php echo $connection_data['label']; ?>',
                    height: <?php echo $connection_data['height']; ?>
                };

                if ( fundingSource !== 'card' ) {
                    let color = '<?php echo $connection_data['color']; ?>';
                    if (fundingSource === 'venmo' && color === 'gold') {
                        color = 'blue';
                    } else if (['ideal', 'bancontact', 'giropay', 'eps', 'sofort', 'mybank', 'p24'].indexOf(fundingSource) > -1 && ['gold', 'blue'].indexOf(color) > -1) {
                        color = 'default';
                    }
                    style.color = color;
                }

                const button = paypal.Buttons({
                    fundingSource: fundingSource,
                    style: style,
                    createOrder: function() {
                        message_<?php echo $rand_string; ?>.innerHTML = '';

                        const form = document.getElementById('<?php echo $rand_string; ?>'),
                            formData = new FormData(),
                            nameInput = form.querySelector('[name="item_name"]'),
                            priceInput = form.querySelector('[name="amount"]'),
                            quantityInput = form.querySelector('[name="quantity"]');

                        formData.append('action', 'wpecpp-ppcp-order-create');
                        formData.append('nonce', wpecpp.nonce);
                        formData.append('name', nameInput ? nameInput.value : '');
                        formData.append('price', priceInput ? priceInput.value : 0);
                        if (quantityInput) {
                            formData.append('quantity', quantityInput.value);
                        }

                        return fetch(wpecpp.ajaxUrl, {
                            method: 'post',
                            body: formData
                        }).then(function(response) {
                            return response.json();
                        }).then(function(data) {
                            let orderID = false;
                            if (data.success && data.data.order_id) {
                                orderID = data.data.order_id;
                            } else {
                                throw data.data && data.data.message ? data.data.message : '<?php _e('An unknown error occurred while creating the order. Please reload the page and try again.', 'wp-ecommerce-paypal'); ?>';
                            }
                            return orderID;
                        });
                    },
                    onApprove: function(data) {
                        const formData = new FormData();

                        formData.append('action', 'wpecpp-ppcp-order-finalize');
                        formData.append('nonce', wpecpp.nonce);
                        formData.append('order_id', data.orderID);

                        return fetch(wpecpp.ajaxUrl, {
                            method: 'post',
                            body: formData
                        }).then(function(response) {
                            return response.json();
                        }).then(function(data) {
                            if (data.success) {
                                if (wpecpp.return.length) {
                                    window.location.href = wpecpp.return;
                                } else {
                                    message_<?php echo $rand_string; ?>.innerHTML = '<span class="payment-success">' + data.data.message + '</span>';
                                }
                            } else {
                                throw data.data.message;
                            }
                        });
                    },
                    onCancel: function() {
                        if (wpecpp.cancel.length) {
                            window.location.href = wpecpp.cancel;
                        } else {
                            message_<?php echo $rand_string; ?>.innerHTML = '<span class="payment-error"><?php _e('The payment was cancelled.', 'wp-ecommerce-paypal'); ?></span>';
                        }
                    },
                    onError: function (error) {
                        message_<?php echo $rand_string; ?>.innerHTML = '<span class="payment-error">' + (error ? error : '<?php echo wpecpp_free_ppcp_js_sdk_error_message(); ?>') + '</span>';
                    }
                });

                if (button.isEligible()) {
                    button.render('#wpecpp-paypal-button-container-<?php echo $rand_string; ?>');
                }
            }
        });

        <?php if ( !empty( $connection_data['advanced_cards'] ) ) { ?>
        if ( paypal.HostedFields.isEligible() ) {
            const cardForm_<?php echo $rand_string; ?> = document.querySelector("#wpecpp-card-form-<?php echo $rand_string; ?>"),
                firstName_<?php echo $rand_string; ?> = document.getElementById('card-holder-first-name-<?php echo $rand_string; ?>'),
                lastName_<?php echo $rand_string; ?> = document.getElementById('card-holder-last-name-<?php echo $rand_string; ?>');

            firstName_<?php echo $rand_string; ?>.addEventListener('input', (e) => {
                if (e.target.value.length === 0) {
                    e.target.classList.add('invalid');
                } else {
                    e.target.classList.remove('invalid');
                }
            });

            lastName_<?php echo $rand_string; ?>.addEventListener('input', (e) => {
                if (e.target.value.length === 0) {
                    e.target.classList.add('invalid');
                } else {
                    e.target.classList.remove('invalid');
                }
            });

            let orderId_<?php echo $rand_string; ?>;
            paypal.HostedFields.render({
                styles: {
                    '.invalid': {
                        'color': 'red'
                    }
                },
                fields: {
                    number: {
                        selector: "#number-<?php echo $rand_string; ?>",
                        placeholder: "<?php _e('Card Number', 'wp-ecommerce-paypal'); ?>"
                    },
                    expirationDate: {
                        selector: "#expirationDate-<?php echo $rand_string; ?>",
                        placeholder: "<?php _e('Expiration', 'wp-ecommerce-paypal'); ?>"
                    },
                    cvv: {
                        selector: "#cvv-<?php echo $rand_string; ?>",
                        placeholder: "<?php _e('CVV', 'wp-ecommerce-paypal'); ?>"
                    },
                    postalCode: {
                        selector: "#postalCode-<?php echo $rand_string; ?>",
                        placeholder: "<?php _e('Billing zip code / Postal code', 'wp-ecommerce-paypal'); ?>"
                    }
                },
                createOrder: function() {
                    if ( cardForm_<?php echo $rand_string; ?>.classList.contains('processing') ) return false;
                    cardForm_<?php echo $rand_string; ?>.classList.add('processing');

                    message_<?php echo $rand_string; ?>.innerHTML = '';

                    const form = document.getElementById('<?php echo $rand_string; ?>'),
                        formData = new FormData(),
                        nameInput = form.querySelector('[name="item_name"]'),
                        priceInput = form.querySelector('[name="amount"]'),
                        quantityInput = form.querySelector('[name="quantity"]');

                    formData.append('action', 'wpecpp-ppcp-order-create');
                    formData.append('nonce', wpecpp.nonce);
                    formData.append('name', nameInput ? nameInput.value : '');
                    formData.append('price', priceInput ? priceInput.value : 0);
                    if (quantityInput) {
                        formData.append('quantity', quantityInput.value);
                    }

                    return fetch(wpecpp.ajaxUrl, {
                        method: 'post',
                        body: formData
                    }).then(function(response) {
                        return response.json();
                    }).then(function(data) {
                        if (data.success && data.data.order_id) {
                            orderId_<?php echo $rand_string; ?> = data.data.order_id;
                        } else {
                            throw data.data && data.data.message ? data.data.message : '<?php _e('An unknown error occurred while creating the order. Please reload the page and try again.', 'wp-ecommerce-paypal'); ?>';
                        }
                        return orderId_<?php echo $rand_string; ?>;
                    });
                }
            }).then(function(cardFields) {
                cardFields.on('validityChange', function (event) {
                    const field = event.fields[event.emittedBy];
                    if (field.isEmpty || !field.isValid) {
                        cardFields.addClass(event.emittedBy, 'invalid');
                        document.getElementById(event.emittedBy + '-<?php echo $rand_string; ?>').classList.add('invalid');
                    } else {
                        cardFields.removeClass(event.emittedBy, 'invalid');
                        document.getElementById(event.emittedBy + '-<?php echo $rand_string; ?>').classList.remove('invalid');
                    }
                });

                document.querySelector("#<?php echo $rand_string; ?>").addEventListener('submit', (e) => {
                    e.preventDefault();

                    let formValid = true;

                    const state = cardFields.getState();
                    for (let k in state.fields) {
                        if (!state.fields[k].isValid) {
                            formValid = false;
                            cardFields.addClass(k, 'invalid');
                            document.getElementById(k + '-<?php echo $rand_string; ?>').classList.add('invalid');
                        } else {
                            cardFields.removeClass(k, 'invalid');
                            document.getElementById(k + '-<?php echo $rand_string; ?>').classList.remove('invalid');
                        }
                    }

                    if (firstName_<?php echo $rand_string; ?>.value.length === 0) {
                        formValid = false;
                        firstName_<?php echo $rand_string; ?>.classList.add('invalid');
                    } else {
                        firstName_<?php echo $rand_string; ?>.classList.remove('invalid');
                    }

                    if (lastName_<?php echo $rand_string; ?>.value.length === 0) {
                        formValid = false;
                        lastName_<?php echo $rand_string; ?>.classList.add('invalid');
                    } else {
                        lastName_<?php echo $rand_string; ?>.classList.remove('invalid');
                    }

                    if (!formValid) {
                        message_<?php echo $rand_string; ?>.innerHTML = '<span class="payment-error"><?php _e('Please correct the errors in the fields above.', 'wp-ecommerce-paypal'); ?></span>';
                        return false;
                    }

                    cardFields.submit({
                        cardholderName: firstName_<?php echo $rand_string; ?>.value + ' ' + lastName_<?php echo $rand_string; ?>.value
                    }).then(function () {
                        const formData = new FormData();

                        formData.append('action', 'wpecpp-ppcp-order-finalize');
                        formData.append('nonce', wpecpp.nonce);
                        formData.append('order_id', orderId_<?php echo $rand_string; ?>);
                        formData.append('acdc', true);

                        return fetch(wpecpp.ajaxUrl, {
                            method: 'post',
                            body: formData
                        }).then(function(res) {
                            return res.json();
                        }).then(function (data) {
                            if (data.success) {
                                if (wpecpp.return.length) {
                                    window.location.href = wpecpp.return;
                                } else {
                                    message_<?php echo $rand_string; ?>.innerHTML = '<span class="payment-success">' + data.data.message + '</span>';
                                }
                            } else {
                                throw {message: data.data.message};
                            }
                            cardForm_<?php echo $rand_string; ?>.classList.remove('processing');
                        })
                    }).catch(function (error) {
                        console.error(error);
                        let message = '';
                        if (error && error.details) {
                            let errors = {};
                            for (let k in error.details) {
                                let fieldName = '';
                                let messageItem = new Array();
                                if (error.details[k].field) {
                                    if (error.details[k].field.indexOf('payment_source/card/number') > -1) {
                                        cardFields.addClass('number', 'invalid');
                                        document.getElementById('number-<?php echo $rand_string; ?>').classList.add('invalid');
                                    } else if (error.details[k].field.indexOf('payment_source/card/security_code') > -1) {
                                        cardFields.addClass('cvv', 'invalid');
                                        document.getElementById('cvv-<?php echo $rand_string; ?>').classList.add('invalid');
                                    } else if (error.details[k].field.indexOf('payment_source/card/expiry') > -1) {
                                        cardFields.addClass('expirationDate', 'invalid');
                                        document.getElementById('expirationDate-<?php echo $rand_string; ?>').classList.add('invalid');
                                    }

                                    fieldName = error.details[k].field
                                        .replace('/payment_source/card/expiry', 'Expiration')
                                        .replace('/payment_source/card/security_code', 'CVV')
                                        .replace('payment_source/card/security_code', 'CVV')
                                        .replace('/payment_source/card/number', 'Card Number');
                                    messageItem.push('<strong>' + fieldName + '</strong>');
                                }
                                if (error.details[k].description) {
                                    messageItem.push(error.details[k].description);
                                }
                                errors[fieldName] = messageItem.join(': ');
                            }
                            message = Object.values(errors).join('<br>');
                        } else if ( error && error.message ) {
                            message = error.message;
                        } else {
                            message = '<?php echo wpecpp_free_ppcp_js_sdk_error_message(); ?>';
                        }
                        message_<?php echo $rand_string; ?>.innerHTML = '<span class="payment-error">' + message + '</span>';
                        cardForm_<?php echo $rand_string; ?>.classList.remove('processing');
                    });
                });
            });
        } else {
            // Hides card fields if the merchant isn't eligible
            document.querySelector("#wpecpp-card-form-<?php echo $rand_string; ?>").style = 'display: none';
        }
        <?php } ?>
            } // End initPayPalButton function
            
            // Start initialization (will poll until SDK is ready)
            initPayPalButton_<?php echo $rand_string; ?>();
        })(); // End IIFE
    </script>
    <?php

    return ob_get_clean();
}

function wpecpp_free_ppcp_js_sdk_error_message() {
    return '<strong>' . __( 'Site admin', 'wp-ecommerce-paypal' ) . '</strong>, ' . __( 'an error was detected in the plugin settings.', 'wp-ecommerce-paypal' ) . '</br>' . __( 'Please check the PayPal connection and product settings (price, name, etc.)', 'wp-ecommerce-paypal' );
}