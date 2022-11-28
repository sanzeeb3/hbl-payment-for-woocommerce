<?php
/**
 * Payment gateway - HBL
 *
 * Provides a HBL Payment Gateway.
 *
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Gateway_HBL_Payment Class.
 */
class WC_Gateway_HBL_Payment extends WC_Payment_Gateway {

	/**
	 * Whether or not logging is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @var boolean
	 */
	public static $log_enabled = false;

	/**
	 * A log object returned by wc_get_logger().
	 *
	 * @since 1 .0.0
	 *
	 * @var boolean
	 */
	public static $log = false;

	/**
	 * Constructor for the gateway.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->id                 = 'hbl-payment';
		$this->icon               = apply_filters( 'hbl_payment_for_woocommerce_icon', plugins_url( 'assets/hbl-payment.png', HBL_PAYMENT_FOR_WOOCOMMERCE_PLUGIN_FILE ) );
		$this->has_fields         = false;
		$this->order_button_text  = __( 'Proceed to HBL Payment', 'hbl-payment-for-woocommerce' );
		$this->method_title       = __( 'Himalayan Bank Payment', 'hbl-payment-for-woocommerce' );
		$this->method_description = __( 'Take payments via Himalayan Bank - sends customers to Himalayan Bank to enter their payment information.', 'hbl-payment-for-woocommerce' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->debug       = 'yes' === $this->get_option( 'debug', 'no' );
		$this->merchant_id = $this->get_option( 'merchant_id' );

		// Enable logging for events.
		self::$log_enabled = $this->debug;

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = 'no';
		} elseif ( $this->merchant_id ) {
			include_once HBL_PAYMENT_FOR_WOOCOMMERCE_PLUGIN_PATH . '/src/Response.php';
			// new \HBLPaymentForWooCommerce\Response( $this );
		}
	}

	/**
	 * Return whether or not this gateway still requires setup to function.
	 *
	 * When this gateway is toggled on via AJAX, if this returns true a
	 * redirect will occur to the settings page instead.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function needs_setup() {
		return empty( $this->merchant_id );
	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level Optional, defaults to info, valid levels:
	 *                      emergency|alert|critical|error|warning|notice|info|debug.
	 *
	 * @since 2.0.0
	 */
	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->log( $level, $message, array( 'source' => 'hbl-payment' ) );
		}
	}

	/**
	 * Processes and saves options.
	 * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
	 *
	 * @since 2.0.0
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		$saved = parent::process_admin_options();

		// Maybe clear logs.
		if ( 'yes' !== $this->get_option( 'debug', 'no' ) ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->clear( 'hbl-payment' );
		}

		return $saved;
	}

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {
		return in_array( get_woocommerce_currency(), apply_filters( 'hbl_payment_for_woocommerce_supported_currencies', array( 'USD', 'NPR' ) ), true );
	}

	/**
	 * Admin Panel Options.
	 * - Options for bits like 'title' and availability on a country-by-country basis.
	 *
	 * @since 2.0.0
	 */
	public function admin_options() {
		if ( $this->is_valid_for_use() ) {
			parent::admin_options();
		} else {
			?>
			<div class="inline error">
				<p>
					<strong><?php esc_html_e( 'Gateway Disabled', 'hbl-payment-for-woocommerce' ); ?></strong>: <?php esc_html_e( 'Himalayan Bank does not support your store currency. Go to the general settings and setup Nepalese Ruppee currency to enable Himalayan Bank Payment.', 'hbl-payment-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 *
	 * @since 2.0.0
	 */
	public function init_form_fields() {
		$this->form_fields = include HBL_PAYMENT_FOR_WOOCOMMERCE_PLUGIN_PATH . '/src/Settings.php';
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function process_payment( $order_id ) {
		include_once HBL_PAYMENT_FOR_WOOCOMMERCE_PLUGIN_PATH . '/src/Request.php';

		$order = wc_get_order( $order_id );

		// $request = new \HBLPaymentForWooCommerce\Request( $this );

		// $result = $request->result( $order );

		if ( isset( $result->data->redirectionUrl ) ) {

			// Assuming success.
			return array(
				'result'   => 'success',
				'redirect' => esc_url( $result->data->redirectionUrl ),
			);
		}

		if ( isset( $result->message ) ) {

			wc_add_notice( 'ERROR: ' . esc_html( $result->message ), 'error' );

			// Failed with error.
			return;
		}

		// Failed anyway.
		return; //phpcs:ignore Squiz.PHP.NonExecutableCode.ReturnNotRequired.
	}
}
