<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpecpp_currency_code_to_iso( $code ) {
	$currencies = [
		'1' => 'AUD',
		'2' => 'BRL',
		'3' => 'CAD',
		'4' => 'CZK',
		'5' => 'DKK',
		'6' => 'EUR',
		'7' => 'HKD',
		'8' => 'HUF',
		'9' => 'ILS',
		'10' => 'JPY',
		'11' => 'MYR',
		'12' => 'MXN',
		'13' => 'NOK',
		'14' => 'NZD',
		'15' => 'PHP',
		'16' => 'PLN',
		'17' => 'GBP',
		'18' => 'RUB',
		'19' => 'SGD',
		'20' => 'SEK',
		'21' => 'CHF',
		'22' => 'TWD',
		'23' => 'THB',
		'24' => 'TRY',
		'25' => 'USD'
	];

	return !empty( $currencies[$code] ) ? $currencies[$code] : 'USD';
}

function wpecpp_language_to_locale( $code ) {
	switch ( $code ) {
		// Danish
		case '1':
			$language = "da_DK";
			$image = "https://www.paypalobjects.com/da_DK/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/da_DK/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/da_DK/DK/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Dutch
		case '2':
			$language = "nl_BE";
			$image = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// English
		case '3':
			$language = "en_US";
			$image = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// French
		case '4':
			$language = "fr_CA";
			$image = "https://www.paypalobjects.com/fr_CA/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/fr_CA/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/fr_CA/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// German
		case '5':
			$language = "de_DE";
			$image = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Hebrew
		case '6':
			$language = "he_IL";
			$image = "https://www.paypalobjects.com/he_IL/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/he_IL/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/he_IL/IL/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Italian
		case '7':
			$language = "it_IT";
			$image = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Japanese
		case '8':
			$language = "ja_JP";
			$image = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Norwegian
		case '9':
			$language = "no_NO";
			$image = "https://www.paypalobjects.com/no_NO/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/no_NO/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/no_NO/NO/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Polish
		case '10':
			$language = "pl_PL";
			$image = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Portuguese
		case '11':
			$language = "pt_BR";
			$image = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// russian
		case '12':
			$language = "ru_RU";
			$image = "https://www.paypalobjects.com/ru_RU/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/ru_RU/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/ru_RU/RU/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Spanish
		case '13':
			$language = "es_ES";
			$image = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Swedish
		case '14':
			$language = "sv_SE";
			$image = "https://www.paypalobjects.com/sv_SE/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/sv_SE/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/sv_SE/SE/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Simplified Chinese - China
		case '15':
			$language = "zh_CN";
			$image = "https://www.paypalobjects.com/zh_XC/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/zh_XC/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/zh_XC/C2/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Traditional Chinese - Hong Kong
		case '16':
			$language = "zh_HK";
			$image = "https://www.paypalobjects.com/zh_HK/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/zh_HK/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/zh_HK/HK/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Traditional Chinese - Taiwan
		case '17':
			$language = "zh_TW";
			$image = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Turkish
		case '18':
			$language = "tr_TR";
			$image = "https://www.paypalobjects.com/tr_TR/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/tr_TR/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/tr_TR/TR/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Thai
		case '19':
			$language = "th_TH";
			$image = "https://www.paypalobjects.com/th_TH/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/th_TH/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/th_TH/TH/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// English - UK
		case '20':
			$language = "en_GB";
			$image = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
			break;

		// Default
		default:
			$language = 'default';
			$image = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif";
			$imageb = "https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif";
			$imagecc = "https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif";
			$imagenew = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/buy-logo-medium.png";
	}

	return [
		'locale' => $language,
		'image' => $image,
		'imageb' => $imageb,
		'imagecc' => $imagecc,
		'imagenew' => $imagenew
	];
}

/**
 * Get PayPal account data
 * @since 1.7.4
 */
function wpecpp_paypal_connection_data( $size = false ) {
	$options = wpecpp_free_options();
	if ( intval( $options['disable_paypal'] ) === 2 ) return false;

	$connection_data = false;

	// currency
	$currency = wpecpp_currency_code_to_iso( $options['currency'] );

	// locale
	$locale = wpecpp_language_to_locale($options['language']);

	$ppcp_status = wpecpp_ppcp_status();
	if ( is_array( $ppcp_status ) && $ppcp_status ) {
		$connection_data = $ppcp_status;
		$connection_data['connection_type'] = 'ppcp';
		$connection_data['currency'] = $currency;
		$connection_data['locale'] = $locale['locale'];
		$connection_data['intent'] = 'capture';

		$connection_data['enable-funding'] = [];
		$funding = [
			'paypal' => ['paypal'],
			'paylater' => ['paylater'],
			'venmo' => ['venmo'],
			'alternative' => ['credit','bancontact','blik','eps','giropay','ideal','mercadopago','mybank','p24','sepa','sofort'],
			'cards' => ['card']
		];
		if ( $connection_data['mode'] === 'advanced' ) {
			$funding['advanced_cards'] = ['advanced_cards'];
		}
		foreach ( $funding as $k => $v ) {
			if ( !empty( $options["ppcp_funding_{$k}"] ) ) {
				if ( $k === 'advanced_cards' ) {
					$connection_data['advanced_cards'] = true;
				} else {
					$connection_data['enable-funding'] = array_merge( $connection_data['enable-funding'], $v );
				}
			}
		}

		$connection_data['layout'] = in_array( $options['ppcp_layout'], ['horizontal', 'vertical'] ) ? $options['ppcp_layout'] : 'vertical';
		$connection_data['color'] = in_array( $options['ppcp_color'], ['gold', 'blue', 'black', 'silver', 'white'] ) ? $options['ppcp_color'] : 'gold';
		$connection_data['shape'] = in_array( $options['ppcp_shape'], ['rect', 'pill'] ) ? $options['ppcp_shape'] : 'rect';
		$connection_data['label'] = in_array( $options['ppcp_label'], ['paypal', 'pay', 'subscribe', 'checkout', 'buynow'] ) ? $options['ppcp_label'] : 'buynow';

		$height = intval( $options['ppcp_height'] );
		if ( $height < 25 || $height > 55 ) {
			$height = 40;
		}
		$connection_data['height'] = $height;

		$ppcp_width = (int) $options['ppcp_width'];
		if ( $ppcp_width < 160 ) {
			$ppcp_width = 160;
		}
		$connection_data['width'] = $ppcp_width;

		$connection_data['acdc_button_text'] = $options['ppcp_acdc_button_text'];
	} else {
		// live or test mode
		if (intval($options['mode']) === 2) {
			$paypal_account = isset($options['liveaccount']) ? $options['liveaccount'] : '';
			$paypal_path = "paypal";
		} else {
			$paypal_account = isset($options['sandboxaccount']) ? $options['sandboxaccount'] : '';
			$paypal_path = "sandbox.paypal";
		}

		if ( !empty($paypal_account) ) {
			// payment action
			$paymentaction = intval($options['paymentaction']) === 2 ? 'authorization' : 'sale';

			// size
			$size = !empty($size) ? $size : $options['size'];
			switch ($size) {
				case '1':
					$img = $locale['image'];
					break;
				case '3':
					$img = $locale['imagecc'];
					break;
				case '5':
					$img = $locale['imagenew'];
					break;
				default:
					$img = $locale['imageb'];
			}

			// window action
			$target = intval($options['opens']) === 2 ? '_blank' : '';

			$connection_data = [
				'connection_type' => 'manual',
				'target' => $target,
				'path' => $paypal_path,
				'account' => $paypal_account,
				'currency' => $currency,
				'locale' => $locale['locale'] === 'default' ? 'en_US' : $locale['locale'],
				'paymentaction' => $paymentaction,
				'return' => $options['return'],
				'cancel' => $options['cancel'],
				'img' => $img
			];
		}
	}

	return $connection_data;
}

/**
 * Get Stripe account data
 * @since 1.7.4
 */
function wpecpp_stripe_account_data() {
	$options = wpecpp_free_options();

	if ( intval( $options['disable_stripe'] ) === 2 ) return false;

	$mode = intval( $options['mode_stripe'] ) === 2 ? 'live' : 'sandbox';
	$account_id = $options['acct_id_' . $mode];
	$token = $options['stripe_connect_token_' . $mode];

	if ( empty( $account_id ) || empty( $token ) ) return false;

	$width = (int) $options['stripe_width'];
	if ( $width < 160 ) {
		$width = 160;
	}

	return [
		'mode' => $mode,
		'account_id' => $account_id,
		'token' => $token,
		'width' => $width
	];
}