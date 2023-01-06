<?php
/**
 * Plugin Name: Add Himalayan Bank Payment In WooCommerce
 * Description: Accept payment from Himalayan Bank in your online store
 * Version: 2.0.3
 * Requires PHP: 7.4
 * Requires at least: 5.0
 * Author: Sanjeev Aryal
 * Author URI: https://www.sanjeebaryal.com.np
 * Text Domain: hbl-payment-for-woocommerce
 */

/**
 * Plugin.
 *
 * @package    Himalayan Bank Payment For WooCommerce
 * @author     Sanjeev Aryal
 * @since      2.0.0
 *
 * @license    GPL-3.0+ @see https://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || die();

/**
 * Plugin constants.
 *
 * @since 2.0.0
 */
define( 'HBL_PAYMENT_FOR_WOOCOMMERCE_PLUGIN_FILE', __FILE__ );
define( 'HBL_PAYMENT_FOR_WOOCOMMERCE_PLUGIN_PATH', __DIR__ );
define( 'HBL_PAYMENT_FOR_WOOCOMMERCE_VERSION', '2.0.3' );

add_action(
	'plugins_loaded',
	function() {

		// Return if WooCommerce is not installed.
		if ( ! defined( 'WC_VERSION' ) ) {
			return;
		}

		require_once __DIR__ . '/src/Plugin.php';
		require_once __DIR__ . '/src/inc/ActionRequest.php';
		require_once __DIR__ . '/src/inc/SecurityData.php';
	}
);

add_filter( 'woocommerce_payment_gateways', 'hbl_payment_gateway' );

/**
 * Add Prabhu Pay gateway to WooCommerce.
 *
 * @param  array $methods WooCommerce payment methods.
 *
 * @since 2.0.0
 *
 * @return array Payment methods including Prabhu Pay.
 */
function hbl_payment_gateway( $methods ) {
	$methods[] = 'WC_Gateway_HBL_Payment';

	return $methods;
}
