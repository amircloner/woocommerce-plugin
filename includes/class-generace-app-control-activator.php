<?php

/**
 * Fired during plugin activation
 *
 * @link       https://generace.ir
 * @since      1.0.0
 *
 * @package    Generace_App_Control
 * @subpackage Generace_App_Control/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Generace_App_Control
 * @subpackage Generace_App_Control/includes
 * @author     GENERACE <ngocdt@rnlab.io>
 */
class Generace_App_Control_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

   		$table_name = $wpdb->prefix . "generace_template_mobile";
   		$charset_collate = $wpdb->get_charset_collate();

   		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  name VARCHAR(254) NULL DEFAULT 'Oreo Native',
		  data longtext NULL DEFAULT NULL,
		  settings longtext NULL DEFAULT NULL,
		  status TINYINT NOT NULL DEFAULT '0',
		  date_created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  date_updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
