<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://generace.ir
 * @since             1.0.0
 * @package           Generace_App_Control
 *
 * @wordpress-plugin
 * Plugin Name:       GENERACE - App Control
 * Plugin URI:        https://doc-oreo.rnlab.io/docs/v1/rnlab-app-control
 * Description:       Add hooks, api routers, auth and app config.
 * Version:           1.3.0
 * Author:            GENERACE
 * Author URI:        https://generace.ir
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       generace-app-control
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RNLAB_APP_CONTROL_VERSION', '1.3.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-generace-app-control-activator.php
 */
function activate_generace_app_control() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-generace-app-control-activator.php';
	Generace_App_Control_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-generace-app-control-deactivator.php
 */
function deactivate_generace_app_control() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-generace-app-control-deactivator.php';
	Generace_App_Control_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_generace_app_control' );
register_deactivation_hook( __FILE__, 'deactivate_generace_app_control' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-generace-app-control.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_generace_app_control() {

	$plugin = new Generace_App_Control();
	$plugin->run();

}
run_generace_app_control();
