<?php

namespace HBLPaymentForWooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Request to Himalayan Bank.
 */
class Request {

	/**
	 * Pointer to gateway making the request.
	 *
	 * @since 2.0.0
	 *
	 * @var WC_Gateway_HBL_Payment
	 */
	protected $gateway;

	/**
	 * Endpoint for requests from Himalayan Bank.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $notify_url;

	/**
	 * Endpoint for requests to Himalayan Bank.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Gateway_HBL_Payment $gateway Gateway class.
	 */
	public function __construct( $gateway ) {
		$this->gateway    = $gateway;
		$this->notify_url = WC()->api_request_url( 'WC_Gateway_HBL_Payment' );
	}

	/**
	 * Get the Himalayan Bank request URL for an order or receive a response.
	 *
	 * @param  WC_Order $order   Order object.
	 *
	 * @since 2.0.0
	 *
	 * @return array The result of the request.
	 */
	public function result( $order ) {

		$test_mode = $this->gateway->get_option( 'test_mode' );

		$this->endpoint  = 'yes' === $test_mode ? 'https://core.demo-paco.2c2p.com/api/1.0/Payment/nonUi' : 'https://core.paco.2c2p.com/api/1.0/Payment/nonUi';
		$hbl_payment_args = $this->get_hbl_payment_args( $order );

		\WC_Gateway_HBL_Payment::log( 'Himalayan Bank Payment Request Args for order ' . $order->get_order_number() . ': ' . wc_print_r( $hbl_payment_args, true ) );

		$body = wp_json_encode( $hbl_payment_args );

		$options = array(
			'body'        => $body,
			'headers'     => array(
				'Content-Type' => 'application/json',
				'apiKey' => $this->gateway->get_option( 'merchant_password' )
			),
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
		);

		$response = wp_remote_post( $this->endpoint, $options );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			\WC_Gateway_HBL_Payment::log( 'Response error for order ' . $order->get_order_number() . ': ' . wc_print_r( $error_message, true ) );
		} else {
			$body = wp_remote_retrieve_body( $response );

			$body = json_decode( $body );
			
			error_log( print_r( $body, true ) );

			\WC_Gateway_HBL_Payment::log( 'Response details for ' . $order->get_order_number() . ': ' . wc_print_r( $body, true ) );

			return $body;
		}
	}

	/**
	 * Get Himalayan Bank Pay Args for passing to Himalayan Bank Pay.
	 *
	 * @param  WC_Order $order Order object.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_hbl_payment_args( $order ) {

		\WC_Gateway_HBL_Payment::log( 'Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url );

		return [
            "apiRequest" => [
                "requestMessageID" => $this->Guid(),
                "requestDateTime" => time(),
                "language" => "en-US",
            ],
            "officeId" => $this->gateway->get_option( 'merchant_id' ),
            "orderNo" => $order->get_order_number(),
            "productDescription" => "product desc.",
            "paymentType" => "CC",
            "paymentCategory" => "ECOM",
            "creditCardDetails" => [
                "cardNumber" => "4404670000020994",
                "cardExpiryMMYY" => "0426",
                "cvvCode" => "829",
                "payerName" => "Demo Sample"
            ],
            "storeCardDetails" => [
                "storeCardFlag" => "N",
                "storedCardUniqueID" => "{{guid}}"
            ],
            "installmentPaymentDetails" => [
                "ippFlag" => "N",
                "installmentPeriod" => 0,
                "interestType" => null
            ],
            "mcpFlag" => "N",
            "request3dsFlag" => "N",
            "transactionAmount" => [
                "amountText" => "000000100000",
                "currencyCode" => "USD",
                "decimalPlaces" => 2,
                "amount" => 1000
            ],
            "notificationURLs" => [
                "confirmationURL" => site_url(),
                "failedURL" => site_url(),
                "cancellationURL" => site_url(),
                "backendURL" => site_url()
            ],
            "deviceDetails" => [
                "browserIp" => "1.0.0.1",
                "browser" => "Postman Browser",
                "browserUserAgent" => "PostmanRuntime/7.26.8 - not from header",
                "mobileDeviceFlag" => "N"
            ],
            "purchaseItems" => [
                [
                    "purchaseItemType" => "ticket",
                    "referenceNo" => "2322460376026",
                    "purchaseItemDescription" => "Bundled insurance",
                    "purchaseItemPrice" => [
                        "amountText" => "000000100000",
                        "currencyCode" => "USD",
                        "decimalPlaces" => 2,
                        "amount" => 1000
                    ],
                    "subMerchantID" => "string",
                    "passengerSeqNo" => 1
                ]
            ]
        ];
	}

	/**
     * Creates a GUID
     *
     * @return string
     */
    private function Guid(): string
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            $charId = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $guid = substr($charId, 0, 8) . $hyphen
                . substr($charId, 8, 4) . $hyphen
                . substr($charId, 12, 4) . $hyphen
                . substr($charId, 16, 4) . $hyphen
                . substr($charId, 20, 12);
            return strtolower($guid);
        }
    }
}
