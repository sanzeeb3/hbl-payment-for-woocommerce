<?php

defined( 'ABSPATH' ) || exit;

/**
 * Settings for Parbhu Pay Gateway.
 */
return array(
	'enabled'            => array(
		'title'   => __( 'Enable/Disable', 'hbl-payment-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable Himalayan Bank Payment', 'hbl-payment-for-woocommerce' ),
		'default' => 'yes',
	),
	'title'              => array(
		'title'       => __( 'Title', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the title which the user sees during checkout.', 'hbl-payment-for-woocommerce' ),
		'default'     => __( 'Himalayan Bank', 'hbl-payment-for-woocommerce' ),
	),
	'description'        => array(
		'title'       => __( 'Description', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'hbl-payment-for-woocommerce' ),
		'default'     => __( 'Pay via Himalayan Bank Credit Card in real-time.', 'hbl-payment-for-woocommerce' ),
	),
	'merchant_id'        => array(
		'title'       => __( 'Merchant ID (Office ID)', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Please enter your Himalayan Bank Merchant ID (Office ID).', 'hbl-payment-for-woocommerce' ),
		'default'     => '',
	),
	'merchant_password'  => array(
		'title'       => __( 'Secret (API) Key', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Please enter your Himalayan Bank API Key. This is needed in order to take payment.', 'hbl-payment-for-woocommerce' ),
		'default'     => '',
	),
	'test_mode'          => array(
		'title'       => __( 'Stage/Test mode', 'hbl-payment-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable Stage/Test Mode', 'hbl-payment-for-woocommerce' ),
		'default'     => 'no',
		'description' => __( 'If enabled, test mode Merchant ID and API Key should be used.' ),
	),
	'invoice_prefix'     => array(
		'title'       => __( 'Invoice prefix', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Please enter a prefix for your invoice numbers. If you use your Himalayan Bank account for multiple stores ensure this prefix is unique as Himalayan Bank will not allow orders with the same invoice number.', 'hbl-payment-for-woocommerce' ),
		'default'     => 'WC-',
	),
	'advanced'           => array(
		'title'       => __( 'Advanced options', 'hbl-payment-for-woocommerce' ),
		'type'        => 'title',
		'description' => '',
	),
	'debug'              => array(
		'title'       => __( 'Debug log', 'hbl-payment-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable logging', 'hbl-payment-for-woocommerce' ),
		'default'     => 'no',
		/* translators: %s: Himalayan Bank log file path */
		'description' => sprintf( __( 'Log Himalayan Bank events, such as IPN requests, inside <code>%s</code>', 'hbl-payment-for-woocommerce' ), wc_get_log_file_path( 'Himalayan Bank' ) ),
	),
	'keys'               => array(
		'title'       => __( 'Signing/Encryption Keys', 'hbl-payment-for-woocommerce' ),
		'type'        => 'title',
		'description' => sprintf(
			__( 'Himalayan Bank requires the <u> public signing and public encryption key </u> to setup the Merchant. You should generate the keys by yourself. Use <a href="%1$s">this tool</a> to generate the keys. Follow the <a href="%2$s">setup instructions</a> for more details. <br/> <p style="color:red">Only share public keys to Himalayan Bank. DO NOT SHARE THE PRIVATE KEYS WITH ANYONE.</p>', 'hbl-payment-for-woocommerce' ),
			'https://www.devglan.com/online-tools/rsa-encryption-decryption',
			'https://sanjeebaryal.com.np/accept-himalayan-bank-payment-from-your-woocommerce-site#setup'
		),
	),
	'public_signing_key'     => array(
		'title'       => __( 'Public Signing Key', 'hbl-payment-for-woocommerce' ),
		'type'        => 'textarea',
		'desc_tip'    => true,
		'description' => __( 'Share this key with the Himalayan Bank.', 'hbl-payment-for-woocommerce' ),
	),
	'private_signing_key'    => array(
		'title'       => __( 'Private Signing Key <p>(DO NOT SHARE)</p>', 'hbl-payment-for-woocommerce' ),
		'type'        => 'textarea',
		'desc_tip'    => true,
		'description' => __( 'DO NOT SHARE THIS KEY WITH ANYONE', 'hbl-payment-for-woocommerce' ),
	),
	'public_encryption_key'  => array(
		'title'       => __( 'Public Encryption Key', 'hbl-payment-for-woocommerce' ),
		'type'        => 'textarea',
		'desc_tip'    => true,
		'description' => __( 'Share this key with the Himalayan Bank.', 'hbl-payment-for-woocommerce' ),
	),
	'private_encryption_key' => array(
		'title'       => __( 'Private Encryption Key <p>(DO NOT SHARE)</p>', 'hbl-payment-for-woocommerce' ),
		'type'        => 'textarea',
		'desc_tip'    => true,
		'description' => __( 'DO NOT SHARE THIS KEY WITH ANYONE', 'hbl-payment-for-woocommerce' ),
	),
);
