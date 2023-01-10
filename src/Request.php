<?php

namespace HBLPaymentForWooCommerce;

use Carbon\Carbon as Carbon;

defined( 'ABSPATH' ) || exit;

/**
 * Request to Himalayan Bank.
 */
class Request extends \ActionRequest {

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

		parent::__construct();

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
	 * @return object The result of the request.
	 */
	public function result( $order ) {

		$test_mode = $this->gateway->get_option( 'test_mode' );

		if ( 'yes' !== $test_mode ) {
			$body = $this->handle_live_mode( $order );

			return $body;
		}

		$this->endpoint   = 'https://core.demo-paco.2c2p.com/api/1.0/Payment/nonUi';
		$hbl_payment_args = $this->get_hbl_payment_args( $order );

		$log_args = $hbl_payment_args;

		unset( $log_args['creditCardDetails'] );

		\WC_Gateway_HBL_Payment::log( 'Himalayan Bank Payment Request Args for order ' . $order->get_order_number() . ': ' . wc_print_r( $log_args, true ) );

		$body = wp_json_encode( $hbl_payment_args );

		$options = array(
			'body'        => $body,
			'headers'     => array(
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'apiKey' => $this->gateway->get_option( 'merchant_password' ),
			),
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => true,
			'data_format' => 'body',
		);

		$response = wp_remote_post( $this->endpoint, $options );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			\WC_Gateway_HBL_Payment::log( 'Response error for order ' . $order->get_order_number() . ': ' . wc_print_r( $error_message, true ) );
		} else {
			$body = wp_remote_retrieve_body( $response );

			$body = json_decode( $body );

			\WC_Gateway_HBL_Payment::log( 'Response details for ' . $order->get_order_number() . ': ' . wc_print_r( $response, true ) );

			return $body;
		}
	}

	/**
	 * Handle live mode.
	 * 
	 * @since 2.0.4
	 */
	public function handle_live_mode( $order ) {

		\WC_Gateway_HBL_Payment::log( 'Handling live mode...' );

		$request = $this->get_hbl_payment_args( $order );
        $now = Carbon::now();

		$payload = [
            "request" => $request,
            "iss" => $this->gateway->get_option( 'merchant_password' ),
            "aud" => "PacoAudience",
            "CompanyApiKey" => $this->gateway->get_option( 'merchant_password' ),
            "iat" => $now->timestamp,
            "nbf" => $now->timestamp,
            "exp" => $now->addHour()->timestamp,
        ];

		$stringPayload = json_encode($payload);

		$signingKey =  $this->GetPrivateKey( $this->gateway->get_option( 'private_signing_key' ) ); // Merchant Private Signing Key.
        $encryptingKey = $this->GetPublicKey( 'MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA6ZLups2K0iYEMxQqgASX8gY6tWhNVCp08YuDgjCsOVrGVgUHD0dh0TWFNJ7Lq2Jp0SOsGgi54+hrjwPOL2CCZxw8pKUlL57UksoD9oWUrK/KkSvEAwPU4cZqzxIXyhBcZb8O96iN4WQJILkRTg+DXLkML6qisO496fPGIs+vCoc87toucy5O9fRfaYSjcqjreyi8JDkvVJM/BeNtOEM2a0b/lcWa67RH+tN97H25k+Qez7QthLru6oBfWBgD6iIwhV+ICqLWHmp6fQ+DHQk/o+OO3yFiY9OAvMiy8MOTinvkBlFwYgYNznG3/w0Xh8U5vtudUXPDNUO6ddf4y99+6LlWDiKgJn/Th93YUg+gFH4LUJHyPrSY2JuC+Q8kksp2xyiZDTHGzi96kturwrqCui6TytCHcU4UB0VRMR+M7VRl3S2YPhcxv5U8Fh2PITqydZE5vv1Va06qhegjOlSZnEUl2xKPm5k/u+UHvUP/oq04fQLTlYqyA3JYDCe4z5Ea2SOgjeVl+qTatWYzmkUXyCONLZ4UaRrgbYCp0nCPHoTFgRQdChu8ezDbnYY9IW7cT/s2fEi5N7X1XrQttiEP4rbn0y0qVYYjN86+elfhtYGHidZTUSUS5RSTHqOkj59p5LIGwFF9iTXzCjfUqq8clnfOk76qSLY1+Kj+SMMe6Z8CAwEAAQ==' ); // Paco Encrypting Public Key.

		$body = $this->EncryptPayload($stringPayload, $signingKey, $encryptingKey);

		\WC_Gateway_HBL_Payment::log( 'Getting ready to request!' );

		$options = [
            'headers' => [
                'Accept' => 'application/jose',
                'CompanyApiKey' => $this->gateway->get_option( 'merchant_password' ),
                'Content-Type' => 'application/jose; charset=utf-8'
            ],
            'body' => $body,
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'data_format' => 'body',
		];

		$response = wp_remote_post( 'https://core.paco.2c2p.com/api/1.0/Payment/NonUi', $options );

		\WC_Gateway_HBL_Payment::log( 'Response details for ' . $order->get_order_number() . ': ' . wc_print_r( $response, true ) );

		$token = wp_remote_retrieve_body( $response );

        $decryptingKey = $this->GetPrivateKey( $this->gateway->get_option( 'private_encryption_key' ) ); // Merchant Private Encryption Key.
        $signatureVerificationKey = $this->GetPublicKey( 'MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAr0XW6QacR8GilY4nZrJZW40wnFeYu7h9aXUSqxCP6djurCWZmLqnrsYWP7/HR8WOulYPHTVpfqJesTOdVqPgY6p10H811oRbJG9jvsG8j8kn/Bk8b2wZ9qelNqdNJMDbR5WUyaytaDWW6QdI4+clqjFfwCOw76noDSe+R4pDSzgMiyCk5R4m2ECT1fv/4Axz2bvLN+DRTg5DPPIMLWpA87lgjxeaDlGyJqZCbkJozW7JX0AJVc0X7YR9kzbiTi3LVOInSKY+VHT8yCARIdvXtKc6+IWSbVQqgpNIBB8GN0OvU8xedjPNCMGZnnMtgd7XLTf/okyadbdNLAqQLTbDs/5HnIVx8FyfgiOS/zsim5ivi3ljVAW3T3ePGjkY0q1DMzr5iJ4m/WTL2d1TArlfHyQhkSpFpQPOO+pJyVQqttHJo99vMirQogdSx4lIu//aod0yJyJLpjCeiqb2Fz3Qk0AZ4S78QKeeGsxTRchTP6Wsb6okaZd+cFi6z8qbP0z/Y3xRZO7vOLB/whkqS+pMVKBQ42YzgQPRzbXXmgCkf1nCqgrD9bnIB5ovdRGfDXW86GKY8XwGVjb4BoMvql+HsbonKHAO+eGfQulpB5YfQGQU3ZXdMdfCLAk8FuqemH4k7S7diLzVvRCuisHsEx6qJ4ewxzNCvW7OGVinTR9NSQUCAwEAAQ==' ); // Paco Signing Public Key.

		$body = json_decode( $this->DecryptToken( $token, $decryptingKey, $signatureVerificationKey ) );

		\WC_Gateway_HBL_Payment::log( 'Response body details for ' . $order->get_order_number() . ': ' . wc_print_r( $body, true ) );

        return $body;
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

		return array(
			'apiRequest'                => array(
				'requestMessageID' => $this->Guid(),
				'requestDateTime'  => date( 'Y-m-d\TH:i:s.v\Z' ),
				'language'         => 'en-US',
			),
			'officeId'                  => $this->gateway->get_option( 'merchant_id' ),
			'orderNo'                   => $order->get_order_number(),
			'productDescription'        => 'product desc.',
			'paymentType'               => 'CC',
			'paymentCategory'           => 'ECOM',
			'creditCardDetails'         => array(
				'cardNumber'     => isset( $_POST['hbl-payment-card-number'] ) ? wc_clean( $_POST['hbl-payment-card-number'] ) : '',
				'cardExpiryMMYY' => isset( $_POST['hbl-payment-card-expiry'] ) ? wc_clean( str_replace( ' ', '', str_replace( '/', '', $_POST['hbl-payment-card-expiry'] ) ) ) : '',
				'cvvCode'        => isset( $_POST['hbl-payment-card-cvc'] ) ? wc_clean( $_POST['hbl-payment-card-cvc'] ) : '',
				'payerName'      => '{Your Name}',
			),
			'storeCardDetails'          => array(
				'storeCardFlag'      => 'N',
				'storedCardUniqueID' => '{{guid}}',
			),
			'installmentPaymentDetails' => array(
				'ippFlag'           => 'N',
				'installmentPeriod' => 0,
				'interestType'      => null,
			),
			'mcpFlag'                   => 'N',
			'request3dsFlag'            => 'N',
			'transactionAmount'         => array(
				'amountText'    => sprintf( '%012d', $order->get_total() * 100 ),
				'currencyCode'  => $order->get_currency(),
				'decimalPlaces' => 2,
				'amount'        => $order->get_total(),
			),
			'notificationURLs'          => array(
				'confirmationURL' => $this->notify_url,
				'failedURL'       => $this->notify_url,
				'cancellationURL' => $this->notify_url,
				'backendURL'      => $this->notify_url,
			),
			'purchaseItems'             => array(
				array(
					'purchaseItemType'        => 'ticket',
					'referenceNo'             => $order->get_order_number(),
					'purchaseItemDescription' => 'Product Description',
					'purchaseItemPrice'       => array(
						'amountText'    => sprintf( '%012d', $order->get_total() * 100 ),
						'currencyCode'  => $order->get_currency(),
						'decimalPlaces' => 2,
						'amount'        => $order->get_total(),
					),
					'subMerchantID'           => 'string',
					'passengerSeqNo'          => 1,
				),
			),
		);
	}

	/**
	 * Creates a GUID
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function Guid() {
		if ( function_exists( 'com_create_guid' ) ) {
			return com_create_guid();
		} else {
			$charId = strtoupper( md5( uniqid( rand(), true ) ) );
			$hyphen = chr( 45 );
			// "-"
			$guid = substr( $charId, 0, 8 ) . $hyphen
				. substr( $charId, 8, 4 ) . $hyphen
				. substr( $charId, 12, 4 ) . $hyphen
				. substr( $charId, 16, 4 ) . $hyphen
				. substr( $charId, 20, 12 );
			return strtolower( $guid );
		}
	}

}