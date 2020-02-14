<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://generace.ir
 * @since      1.0.0
 *
 * @package    Rnlab_App_Control
 * @subpackage Rnlab_App_Control/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rnlab_App_Control
 * @subpackage Rnlab_App_Control/includes
 * @author     GENERACE <ngocdt@rnlab.io>
 */
class Rnlab_App_Control {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rnlab_App_Control_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version = RNLAB_APP_CONTROL_VERSION;
		$this->plugin_name = 'generace-app-control';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_product_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rnlab_App_Control_Loader. Orchestrates the hooks of the plugin.
	 * - Rnlab_App_Control_i18n. Defines internationalization functionality.
	 * - Rnlab_App_Control_Admin. Defines all hooks for the admin area.
	 * - Rnlab_App_Control_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Load dependency install by composer
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-generace-app-control-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-generace-app-control-i18n.php';

		/**
		 * The class responsible for loading payment gateways
		 * @author Ngoc Dang
		 * @since 1.1.0
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-generace-app-control-gateway-paypal.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-generace-app-control-gateway-razorpay.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-generace-app-control-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-generace-app-control-public.php';

		/**
		 * The class responsible for defining all actions that occur in the product-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'product/class-generace-app-control-product.php';

		/**
		 * Load library
		 * @since 1.2.3
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/libraries/class-generace-app-control-public-key.php';

		$this->loader = new Rnlab_App_Control_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rnlab_App_Control_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rnlab_App_Control_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Rnlab_App_Control_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'add_api_routes' );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $plugin_admin, 'add_plugin_admin_menu' ) );

		// Add plugin action link point to settings page
		add_filter( 'plugin_action_links_' . $this->plugin_name . '/' . $this->plugin_name . '.php', array( $plugin_admin, 'add_plugin_action_links' ) );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rnlab_App_Control_Public( $this->get_plugin_name(), $this->get_version() );

		$rnlab_gateways = array();
		
		// Payment Gateway via PayPal Standard
		$gateway_paypal = new Rnlab_App_Control_Gateway_PayPal();
		array_push($rnlab_gateways, $gateway_paypal);

		// Payment Gateway via Razorpay Standard
		$gateway_razorpay = new Rnlab_App_Control_Gateway_Razorpay();
		array_push($rnlab_gateways, $gateway_razorpay);

		// Register Payment Endpoint for all Gateways
		foreach ($rnlab_gateways as &$rnlab_gateway) {
			$this->loader->add_filter('rnlab_pre_process_' . $rnlab_gateway->gateway_id . '_payment', $rnlab_gateway, 'rnlab_pre_process_payment');
		}

		$this->loader->add_action( 'rest_api_init', $plugin_public, 'add_api_routes' );
		$this->loader->add_filter( 'determine_current_user', $plugin_public, 'determine_current_user' );
		
		/**
		 * Fillter locate template
		 * @since 1.2.0
		 */
		$this->loader->add_filter( 'woocommerce_locate_template', $plugin_public, 'woocommerce_locate_template', 100, 3 );
		
		/**
		 * Fillter add to cart before redirect to checkout page
		 * @since 1.2.0
		 */
		$this->loader->add_action( 'template_redirect', $plugin_public, 'template_redirect' );

		/**
		 * Add style for checkout page
		 * @since 1.2.0
		 */
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_product_hooks() {

		$plugin_product = new Rnlab_App_Control_Product( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'rest_api_init', $plugin_product, 'add_api_routes' );

		// Product variation
		$this->loader->add_filter( 'woocommerce_rest_prepare_product_variation_object', $plugin_product,
			'custom_woocommerce_rest_prepare_product_variation_object' );

		$this->loader->add_filter( 'woocommerce_rest_prepare_product_variation_object', $plugin_product,
			'prepare_product_variation_images', 10, 3 );

		// Product
		$this->loader->add_filter( 'woocommerce_rest_prepare_product_object', $plugin_product,
			'custom_change_product_response', 20, 3 );

		// Category
		$this->loader->add_filter( 'woocommerce_rest_prepare_product_cat', $plugin_product,
			'custom_change_product_cat', 20, 3 );

		// Blog
		$this->loader->add_filter( 'the_title', $plugin_product,
			'custom_the_title', 20, 3 );

		$this->loader->add_filter( 'woocommerce_rest_prepare_product_object', $plugin_product,
			'prepare_product_images', 30, 3 );

		// Product Attribute
		$this->loader->add_filter( 'woocommerce_rest_prepare_product_attribute', $plugin_product,
			'custom_woocommerce_rest_prepare_product_attribute', 10, 3 );

		$this->loader->add_filter( 'woocommerce_rest_prepare_pa_color', $plugin_product, 'add_value_pa_color' );
		$this->loader->add_filter( 'woocommerce_rest_prepare_pa_image', $plugin_product, 'add_value_pa_image' );

		$this->loader->add_filter( 'wcml_client_currency', $plugin_product, 'mbd_wcml_client_currency' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rnlab_App_Control_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
