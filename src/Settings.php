<?php

defined( 'ABSPATH' ) || exit;

/**
 * Settings for Parbhu Pay Gateway.
 */
return array(
	'enabled'           => array(
		'title'   => __( 'Enable/Disable', 'hbl-payment-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable Himalayan Bank Payment', 'hbl-payment-for-woocommerce' ),
		'default' => 'yes',
	),
	'title'             => array(
		'title'       => __( 'Title', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the title which the user sees during checkout.', 'hbl-payment-for-woocommerce' ),
		'default'     => __( 'Himalayan Bank', 'hbl-payment-for-woocommerce' ),
	),
	'description'       => array(
		'title'       => __( 'Description', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'hbl-payment-for-woocommerce' ),
		'default'     => __( 'Pay via Himalayan Bank. You will be redirected to Himalayan Bank website to securely pay with Himalayan Bank cards.', 'hbl-payment-for-woocommerce' ),
	),
	'merchant_id'       => array(
		'title'       => __( 'Merchant ID (Office ID)', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Please enter your Himalayan Bank Merchant ID (Office ID).', 'hbl-payment-for-woocommerce' ),
		'default'     => '',
	),
	'merchant_password' => array(
		'title'       => __( 'Secret (API) Key', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Please enter your Himalayan Bank API Key. This is needed in order to take payment.', 'hbl-payment-for-woocommerce' ),
		'default'     => '',
	),
	'test_mode'         => array(
		'title'       => __( 'Stage/Test mode', 'hbl-payment-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable Stage/Test Mode', 'hbl-payment-for-woocommerce' ),
		'default'     => 'no',
		'description' => __( 'If enabled, test mode Merchant ID and API Key should be used.' ),
	),
	'invoice_prefix'    => array(
		'title'       => __( 'Invoice prefix', 'hbl-payment-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Please enter a prefix for your invoice numbers. If you use your Himalayan Bank account for multiple stores ensure this prefix is unique as Himalayan Bank will not allow orders with the same invoice number.', 'hbl-payment-for-woocommerce' ),
		'default'     => 'WC-',
	),
	'advanced'          => array(
		'title'       => __( 'Advanced options', 'hbl-payment-for-woocommerce' ),
		'type'        => 'title',
		'description' => '',
	),
	'debug'             => array(
		'title'       => __( 'Debug log', 'hbl-payment-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable logging', 'hbl-payment-for-woocommerce' ),
		'default'     => 'no',
		/* translators: %s: Himalayan Bank log file path */
		'description' => sprintf( __( 'Log Himalayan Bank events, such as IPN requests, inside <code>%s</code>', 'hbl-payment-for-woocommerce' ), wc_get_log_file_path( 'Himalayan Bank' ) ),
	),
);
