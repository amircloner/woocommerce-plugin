<?php

/**
 * REST API endpoint for WooCommerce Payment via Razorpay Standard.
 *
 * @since      1.1.0
 *
 *
 * @package    Rnlab_App_Control
 * @subpackage Rnlab_App_Control/includes/gateways
 * @author     Ngoc Dang
 */

class Rnlab_App_Control_Gateway_Razorpay {

	/**
	 * The ID of the corresponding WooCommerce Payment Gateway.
	 *
	 * @since    1.1.0
	 * @var      string    $id    The ID of the corresponding Gateway.
	 *
	 * @author Ngoc Dang
	 *
	 */
	public $gateway_id = 'razorpay';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1.0
	 * @var      string    $version    The current version of corresponding Gateway.
	 *
	 * @author Ngoc Dang
	 */
	private $gateway_version = '2.3.1';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

	}

	/**
	 * Add Action to precheck, prepare params before the Gateway calls 'process_payment'
	 *
	 * @since 1.4.0
	 * @author Ngoc Dang
	 */
	public function rnlab_pre_process_payment($parameters) {

		WC()->session = new WC_Session_Handler();
		WC()->session->init();

		// $_POST['payment_method'] = $this->gateway_id;

		// do_action('woocommerce_api_' . $this->gateway_id);

		$parameters['pre_process_result'] = true;
		return $parameters;
	}

}
