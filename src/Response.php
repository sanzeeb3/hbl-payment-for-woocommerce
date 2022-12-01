<?php

namespace HBLPaymentForWooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Response from Himalayan Bank.
 *
 * @since 2.0.0
 */
class Response {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Gateway_HBL_Payment $gateway Gateway class.
	 */
	public function __construct( $gateway ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		if ( empty( $_REQUEST['status'] ) || empty( $_REQUEST['message'] ) || empty( $_REQUEST['transactionId'] ) || empty( $_REQUEST['invoiceNo'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

			return;
		}

		\WC_Gateway_HBL_Payment::log( 'Checking Response...' );

		$status         = isset( $_REQUEST['status'] ) ? wc_clean( wp_unslash( $_REQUEST['status'] ) ) : ''; //phpcs:ignore
		$message        = isset( $_REQUEST['message'] ) ? wc_clean( wp_unslash( $_REQUEST['message'] ) ) : ''; //phpcs:ignore
		$transaction_id = isset( $_REQUEST['transactionId'] ) ? wc_clean( wp_unslash( $_REQUEST['transactionId'] ) ) : ''; //phpcs:ignore
		$invoice_no     = isset( $_REQUEST['invoiceNo'] ) ? wc_clean( wp_unslash( $_REQUEST['invoiceNo'] ) ) : ''; //phpcs:ignore

		$test_mode = $gateway->get_option( 'test_mode' );

		$endpoint = 'yes' === $test_mode ? 'https://core.demo-paco.2c2p.com/api/1.0/Inquiry/transactionStatus' : 'https://core-paco.2c2p.com/api/1.0/Inquiry/transactionStatus';

		$body = array(
			'merchantId'    => $gateway->get_option( 'merchant_id' ),
			'invoiceNo'     => sanitize_text_field( $invoice_no ),
			'transactionId' => sanitize_text_field( $transaction_id ),
			'password'      => $gateway->get_option( 'merchant_password' ),
		);

		$options = array(
			'body'        => wp_json_encode( $body ),
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
		);

		$response = wp_remote_post( $endpoint, $options );

		if ( is_wp_error( $response ) ) {
			\WC_Gateway_HBL_Payment::log( 'Error response: ' . $response->get_error_message() );
			return;
		}

		// Check to see if the request was valid.
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr( strtoupper( $response['body'] ), 'SUCCESS' ) ) {
			\WC_Gateway_HBL_Payment::log( 'Received valid response from Himalayan Bank' );
		}

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body );

		if ( isset( $body->status ) && '00' === $body->status ) {

			$invoice_no     = isset( $body->data->invoiceNo ) ? $body->data->invoiceNo : $invoice_no;
			$transaction_id = isset( $body->data->transactionId ) ? $body->data->transactionId : $transaction_id;

			$order = $this->get_order_by_invoice_id( $invoice_no, $gateway );

			if ( $order ) {

				// Assuming complete.
				$order->payment_complete( $transaction_id );
			}
		}

		WC()->cart->empty_cart();

	}

	/**
	 * Get order by Invoice ID.
	 *
	 * @param string $invoice_id Invoice ID.
	 *
	 * @param string $gateway Gateway.
	 *
	 * @since 2.0.0
	 *
	 * @return bool|WC_Order object
	 */
	public function get_order_by_invoice_id( $invoice_id, $gateway ) {

		$prefix   = $gateway->get_option( 'invoice_prefix' );
		$order_id = ltrim( $invoice_id, $prefix );
		$order    = wc_get_order( $order_id );

		if ( ! $order ) {

			// Nothing was found.
			\WC_Gateway_HBL_Payment::log( 'Order not found.', 'error' );
		}

		return $order;
	}
}
