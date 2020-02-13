<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://rnlab.io
 * @since      1.0.0
 *
 * @package    Rnlab_App_Control
 * @subpackage Rnlab_App_Control/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Rnlab_App_Control
 * @subpackage Rnlab_App_Control/includes
 * @author     RNLAB <ngocdt@rnlab.io>
 */
class Rnlab_App_Control_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'rnlab-app-control',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
