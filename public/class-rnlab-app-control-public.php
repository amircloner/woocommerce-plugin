<?php

use \Firebase\JWT\JWT;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://generace.ir
 * @since      1.0.0
 *
 * @package    Generace_App_Control
 * @subpackage Generace_App_Control/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Generace_App_Control
 * @subpackage Generace_App_Control/public
 * @author     GENERACE <ngocdt@rnlab.io>
 */
class Generace_App_Control_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 *  Then key to encode token
	 * @since    1.0.0
	 * @access   private
	 * @var      string $key The key to encode token
	 */
	private $key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since      1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->key         = defined( 'JWT_SECRET_KEY' ) ? JWT_SECRET_KEY : "example_key";

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.2.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Blog_1_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Blog_1_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset( $_GET['mobile'] ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/checkout.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Registers a REST API route
	 *
	 * @since 1.0.0
	 */
	public function add_api_routes() {
		$namespace = $this->plugin_name . '/v' . intval( $this->version );

		register_rest_route( $namespace, 'token', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'app_token' ),
		) );

		register_rest_route( $namespace, 'login', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'login' ),
		) );

		register_rest_route( $namespace, 'login-otp', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'login_otp' ),
		) );

		register_rest_route( $namespace, 'current', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'current' ),
		) );

		register_rest_route( $namespace, 'facebook', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'login_facebook' ),
		) );

		register_rest_route( $namespace, 'google', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'login_google' ),
		) );

		register_rest_route( $namespace, 'apple', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'login_apple' ),
		) );

		register_rest_route( $namespace, 'register', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'register' ),
		) );

		register_rest_route( $namespace, 'lost-password', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'retrieve_password' ),
		) );

		register_rest_route( $namespace, 'settings', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'settings' ),
		) );

		register_rest_route( $namespace, 'change-password', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'change_password' ),
		) );

		register_rest_route( $namespace, 'zones', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'zones' ),
		) );

		register_rest_route( $namespace, 'get-continent-code-for-country', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get_continent_code_for_country' ),
		) );

		register_rest_route( $namespace, 'payment-stripe', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'payment_stripe' ),
		) );

		register_rest_route( $namespace, 'payment-hayperpay', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'payment_hayperpay' ),
		) );

		/**
		 * Add payment router
		 *
		 * @author Ngoc Dang
		 * @since 1.1.0
		 */
		register_rest_route( $namespace, 'process_payment', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'rnlab_process_payment' ),
		) );

		register_rest_field( 'post', '_categories', array(
			'get_callback' => function ( $post ) {
				$cats = array();
				foreach ( $post['categories'] as $c ) {
					$cat    = get_category( $c );
					$cats[] = $cat->name;
				}

				return $cats;
			},
		) );

		/**
		 * register rest post field
		 *
		 * @author Ngoc Dang
		 * @since 1.1.0
		 */
		register_rest_field( 'post', 'rnlab_featured_media_url',
			array(
				'get_callback'    => array( $this, 'get_featured_media_url' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);

		/**
		 * Check mobile phone number
		 *
		 * @author Ngoc Dang
		 * @since 1.2.0
		 */
		register_rest_route( $namespace, 'check-phone-number', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'mbd_check_phone_number' ),
		) );

		/**
		 * Check emai and username
		 *
		 * @author Ngoc Dang
		 * @since 1.2.0
		 */
		register_rest_route( $namespace, 'check-info', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'mbd_validate_user_info' ),
		) );

	}

	/**
	 * Check mobile phone number
	 *
	 * @author Ngoc Dang
	 * @since 1.2.0
	 */
	public function mbd_check_phone_number( $request ) {
		$phone_number = $request->get_param( 'phone_number' );
		$type         = $request->get_param( 'type' );

		$users = get_users( array(
			"meta_key"     => "phone_number",
			"meta_value"   => $phone_number,
			"meta_compare" => "="
		) );

		if ( $type == "register" ) {
			if ( count( $users ) > 0 ) {
				$error = new WP_Error();
				$error->add( 403, "شماره تماس شما قبلاً در سایت ثبت شده است.", array( 'status' => 400 ) );

				return $error;
			}

			return new WP_REST_Response( array( "data" => "Phone number not exits!" ), 200 );
		}

		// Login folow
		if ( count( $users ) == 0 ) {
			$error = new WP_Error();
			$error->add( 403, "شماره تماس شما در سایت وجود ندارد!", array( 'status' => 400 ) );

			return $error;
		}

		return new WP_REST_Response( array( "data" => "Phone number number exist!" ), 200 );
	}

	/**
	 * Change checkout template
	 *
	 * @author Ngoc Dang
	 * @since 1.2.0
	 */
	public function woocommerce_locate_template( $template, $template_name, $template_path ) {
		if ( 'checkout/form-checkout.php' == $template_name && isset( $_GET['mobile'] ) ) {
			return plugin_dir_path( __DIR__ ) . 'templates/checkout/form-checkout.php';
		}

		if ( 'checkout/thankyou.php' == $template_name && isset( $_GET['mobile'] ) ) {
			return plugin_dir_path( __DIR__ ) . 'templates/checkout/thankyou.php';
		}

		if ( 'checkout/form-pay.php' == $template_name && isset( $_GET['mobile'] ) ) {
			return plugin_dir_path( __DIR__ ) . 'templates/checkout/form-pay.php';
		}

		return $template;
	}

	/**
	 * Add product to cart before redirect to checkout page
	 *
	 * @author Ngoc Dang
	 * @since 1.2.0
	 */
	public function template_redirect() {
		if ( isset( $_GET['mobile'] ) && isset( $_GET['line_items'] ) ) {

			// Login user
			if ( isset( $_GET['token'] ) && ! is_user_logged_in() ) {
				$data = $this->decode( $_GET['token'] );
				if ( ! is_wp_error( $data ) ) {
					$user_id = $data->data->user_id;
					$user    = get_user_by( 'id', $user_id );

					wp_set_current_user( $user_id, $user->user_login );
					wp_set_auth_cookie( $user_id );
					header( "Refresh:0" );
				}
			}

			$line_items = json_decode( html_entity_decode( stripslashes( $_GET['line_items'] ) ), true );

			WC()->session->set( 'refresh_totals', true );
			WC()->cart->empty_cart();

			foreach ( $line_items as $item ) {
				WC()->cart->add_to_cart( $item['product_id'], $item['quantity'], $item['variation_id'] );
			}
		}
	}

	/**
	 * Find the selected Gateway, and process payment
	 *
	 * @author Ngoc Dang
	 * @since 1.1.0
	 */
	public function rnlab_process_payment( $request = null ) {

		// Create a Response Object
		$response = array();

		// Get parameters
		$order_id       = $request->get_param( 'order_id' );
		$payment_method = $request->get_param( 'payment_method' );

		$error = new WP_Error();

		// Perform Pre Checks
		if ( ! class_exists( 'WooCommerce' ) ) {
			$error->add( 400, __( "پرداخت ناموفق!", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

			return $error;
		}
		if ( empty( $order_id ) ) {
			$error->add( 401, __( "Order ID 'order_id' is required.", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

			return $error;
		} else if ( wc_get_order( $order_id ) == false ) {
			$error->add( 402, __( "Order ID 'order_id' is invalid. Order does not exist.", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

			return $error;
		} else if ( wc_get_order( $order_id )->get_status() !== 'pending' && wc_get_order( $order_id )->get_status() !== 'failed' ) {
			$error->add( 403, __( "Order status is '" . wc_get_order( $order_id )->get_status() . "', meaning it had already received a successful payment. Duplicate payments to the order is not allowed. The allow status it is either 'pending' or 'failed'. ", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

			return $error;
		}
		if ( empty( $payment_method ) ) {
			$error->add( 404, __( "Payment Method 'payment_method' is required.", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

			return $error;
		}

		// Find Gateway
		$avaiable_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$gateway           = $avaiable_gateways[ $payment_method ];

		if ( empty( $gateway ) ) {
			$all_gateways = WC()->payment_gateways->payment_gateways();
			$gateway      = $all_gateways[ $payment_method ];

			if ( empty( $gateway ) ) {
				$error->add( 405, __( "Failed to process payment. WooCommerce Gateway '" . $payment_method . "' is missing.", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

				return $error;
			} else {
				$error->add( 406, __( "Failed to process payment. WooCommerce Gateway '" . $payment_method . "' exists, but is not available.", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

				return $error;
			}
		} else if ( ! has_filter( 'rnlab_pre_process_' . $payment_method . '_payment' ) ) {
			$error->add( 407, __( "Failed to process payment. WooCommerce Gateway '" . $payment_method . "' exists, but 'REST Payment - " . $payment_method . "' is not available.", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

			return $error;
		} else {

			// Pre Process Payment
			$parameters = apply_filters( 'rnlab_pre_process_' . $payment_method . '_payment', array(
				"order_id"       => $order_id,
				"payment_method" => $payment_method
			) );

			if ( $parameters['pre_process_result'] === true ) {

				// Process Payment
				$payment_result = $gateway->process_payment( $order_id );
				if ( $payment_result['result'] === "success" ) {
					$response['code']    = 200;
					$response['message'] = __( "پرداخت موفق.", "rnlab-rest-payment" );
					$response['data']    = $payment_result;

					// Return Successful Response
					return new WP_REST_Response( $response, 200 );
				} else {
					return new WP_Error( 500, 'پرداخت ناموفق', $payment_result );
				}
			} else {
				return new WP_Error( 408, 'پرداخت ناموفق', $parameters['pre_process_result'] );
			}

		}

	}

	/**
	 * Registers a REST API route
	 *
	 * @since 1.0.5
	 */
	public function payment_hayperpay( $request ) {
		$response = array();

		$order_id             = $request->get_param( 'order_id' );
		$wc_gate2play_gateway = new WC_gate2play_Gateway();
		$payment_result       = $wc_gate2play_gateway->process_payment( $order_id );

		if ( $payment_result['result'] === "success" ) {
			$response['code']     = 200;
			$response['message']  = __( "پرداخت شما موفقیت آمیز بوده است", "rnlab-rest-payment" );
			$response['redirect'] = $payment_result['redirect'];
		} else {
			$response['code']    = 401;
			$response['message'] = __( "لطفاً اطلاعات کارت بانکی را وارد کنید", "rnlab-rest-payment" );
		}

		return new WP_REST_Response( $response );
	}

	public function payment_stripe( $request ) {
		$response = array();

		$order_id      = $request->get_param( 'order_id' );
		$stripe_source = $request->get_param( 'stripe_source' );

		$error = new WP_Error();

		if ( empty( $order_id ) ) {
			$error->add( 401, __( "Order ID 'order_id' is required.", 'rnlab-rest-payment' ), array( 'status' => 400 ) );

			return $error;
		} else if ( wc_get_order( $order_id ) == false ) {
			$error->add( 402, __( "Order ID 'order_id' is invalid. Order does not exist.", 'rnlab-rest-payment' ),
				array( 'status' => 400 ) );

			return $error;
		}

		if ( empty( $stripe_source ) ) {
			$error->add( 404, __( "Payment source 'stripe_source' is required.", 'rnlab-rest-payment' ),
				array( 'status' => 400 ) );

			return $error;
		}

		$wc_gateway_stripe = new WC_Gateway_Stripe();

		$_POST['stripe_source']  = $stripe_source;
		$_POST['payment_method'] = "stripe";

		// Fix empty cart in process_payment
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer( get_current_user_id(), true );
		WC()->cart     = new WC_Cart();

		$payment_result = $wc_gateway_stripe->process_payment( $order_id );

		if ( $payment_result['result'] === "success" ) {
			$response['code']    = 200;
			$response['message'] = __( "پرداخت شما موفقیت آمیز بوده است", "rnlab-rest-payment" );

			// $order = wc_get_order( $order_id );

			// set order to completed
			// if ( $order->get_status() == 'processing' ) {
			// 	$order->update_status( 'completed' );
			// }

		} else {
			$response['code']    = 401;
			$response['message'] = __( "لطفاً اطلاعات کارت بانکی را وارد کنید", "rnlab-rest-payment" );
		}

		return new WP_REST_Response( $response );
	}

	public function get_continent_code_for_country( $request ) {
		$cc         = $request->get_param( 'cc' );
		$wc_country = new WC_Countries();

		return $wc_country->get_continent_code_for_country( $cc );
	}

	public function zones() {
		$delivery_zones = (array) WC_Shipping_Zones::get_zones();

		$data = [];
		foreach ( $delivery_zones as $key => $the_zone ) {

			$shipping_methods = [];

			foreach ( $the_zone['shipping_methods'] as $value ) {

				$shipping_methods[] = array(
					'instance_id'        => $value->instance_id,
					'id'                 => $value->instance_id,
					'method_id'          => $value->id,
					'method_title'       => $value->title,
					'method_description' => $value->method_description,
					'settings'           => array(
						'cost' => array(
							'value' => $value->cost
						)
					),
				);
			}

			$data[] = array(
				'id'               => $the_zone['id'],
				'zone_name'        => $the_zone['zone_name'],
				'zone_locations'   => $the_zone['zone_locations'],
				'shipping_methods' => $shipping_methods,
			);

		}

		return $data;
	}

	public function change_password( $request ) {

		$current_user = wp_get_current_user();
		if ( ! $current_user->exists() ) {
			return new WP_Error(
				'user_not_login',
				'Please login first.',
				array(
					'status' => 403,
				)
			);
		}

		$username     = $current_user->user_login;
		$password_old = $request->get_param( 'password_old' );
		$password_new = $request->get_param( 'password_new' );

		// try login with username and password
		$user = wp_authenticate( $username, $password_old );

		if ( is_wp_error( $user ) ) {
			$error_code = $user->get_error_code();

			return new WP_Error(
				$error_code,
				$user->get_error_message( $error_code ),
				array(
					'status' => 403,
				)
			);
		}

		wp_set_password( $password_new, $current_user->ID );

		return $current_user->ID;
	}

	public function settings() {
		try {
			global $woocommerce_wpml;

			$currencies = array();

			$languages    = apply_filters( 'wpml_active_languages', array(), 'orderby=id&order=desc' );
			$default_lang = apply_filters( 'wpml_default_language', 'en' );

			$currency = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'USD';

			if ( ! empty( $woocommerce_wpml->multi_currency ) && ! empty( $woocommerce_wpml->settings['currencies_order'] ) ) {
				$currencies = $woocommerce_wpml->multi_currency->get_currencies( 'include_default = true' );
			}

			return array(
				'language'              => $default_lang,
				'languages'             => $languages,
				'currencies'            => $currencies,
				'currency'              => $currency,
				'enable_guest_checkout' => get_option( 'woocommerce_enable_guest_checkout', true ),
			);
		} catch ( Exception $e ) {
			return new WP_Error(
				'error_setting',
				'Some thing wrong.',
				array(
					'status' => 403,
				)
			);
		}
	}

	/**
	 * Create token for app
	 *
	 * @param $request
	 *
	 * @return bool|WP_Error
	 */
	public function app_token() {

		$wp_auth_user = defined( 'WP_AUTH_USER' ) ? WP_AUTH_USER : "wp_auth_user";

		$user = get_user_by( 'login', $wp_auth_user );

		if ( $user ) {
			$token = $this->generate_token( $user, array( 'read_only' => true ) );

			return $token;
		} else {
			return new WP_Error(
				'create_token_error',
				'You did not create user wp_auth_user',
				array(
					'status' => 403,
				)
			);
		}
	}

	/**
	 * Lost password for user
	 *
	 * @param $request
	 *
	 * @return bool|WP_Error
	 */
	public function retrieve_password( $request ) {
		$errors = new WP_Error();

		$user_login = $request->get_param( 'user_login' );

		if ( empty( $user_login ) || ! is_string( $user_login ) ) {
			$errors->add( 'empty_username', __( '<strong>خطا</strong>: نام کاربری یا آدرس ایمیل خود را وارد کنید.' ) );
		} elseif ( strpos( $user_login, '@' ) ) {
			$user_data = get_user_by( 'email', trim( wp_unslash( $user_login ) ) );
			if ( empty( $user_data ) ) {
				$errors->add( 'invalid_email',
					__( '<strong>خطا</strong>: با نام کاربری یا آدرس ایمیل شما هیچ حسابی کاربری ای وجود ندارد.' ) );
			}
		} else {
			$login     = trim( $user_login );
			$user_data = get_user_by( 'login', $login );
		}

		if ( $errors->has_errors() ) {
			return $errors;
		}

		if ( ! $user_data ) {
			$errors->add( 'invalidcombo',
				__( '<strong>خطا</strong>: با نام کاربری یا آدرس ایمیل شما هیچ حسابی کاربری ای وجود ندارد.' ) );

			return $errors;
		}

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key        = get_password_reset_key( $user_data );

		if ( is_wp_error( $key ) ) {
			return $key;
		}

		if ( is_multisite() ) {
			$site_name = get_network()->site_name;
		} else {
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}

		$message = __( 'شخصی درخواست بازنشانی گذرواژه برای حساب کاربری شما کرده است.' ) . "\r\n\r\n";
		/* translators: %s: site name */
		$message .= sprintf( __( 'Site Name: %s' ), $site_name ) . "\r\n\r\n";
		/* translators: %s: user login */
		$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
		$message .= __( 'اگر شما این درخواست را نداده اید ، این ایمیل را نادیده بگیرید و هیچ اتفاقی نخواهد افتاد.' ) . "\r\n\r\n";
		$message .= __( 'برای بازنشانی گذرواژه خود ، به آدرس زیر مراجعه کنید:' ) . "\r\n\r\n";
		$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ),
				'login' ) . ">\r\n";

		/* translators: Password reset notification email subject. %s: Site title */
		$title = sprintf( __( '[%s] Password Reset' ), $site_name );

		/**
		 * Filters the subject of the password reset email.
		 *
		 * @param string $title Default email title.
		 * @param string $user_login The username for the user.
		 * @param WP_User $user_data WP_User object.
		 *
		 * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
		 *
		 * @since 2.8.0
		 */
		$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

		/**
		 * Filters the message body of the password reset mail.
		 *
		 * If the filtered message is empty, the password reset email will not be sent.
		 *
		 * @param string $message Default mail message.
		 * @param string $key The activation key.
		 * @param string $user_login The username for the user.
		 * @param WP_User $user_data WP_User object.
		 *
		 * @since 2.8.0
		 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
		 *
		 */
		$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

		if ( $message && ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
			return new WP_Error(
				'send_email',
				'Possible reason: your host may have disabled the mail() function.',
				array(
					'status' => 403,
				)
			);
		}

		return true;
	}

	/**
	 *  Get current user login
	 *
	 * @param $request
	 *
	 * @return mixed
	 */
	public function current( $request ) {
		$current_user = wp_get_current_user();

		return $current_user->data;
	}

	/**
	 *  Validate user
	 *
	 * @param $request
	 *
	 * @return mixed
	 */
	public function mbd_validate_user_info( $request ) {

		$email = $request->get_param( 'email' );
		$name  = $request->get_param( 'name' );

		// Validate email
		if ( ! is_email( $email ) || email_exists( $email ) ) {
			return new WP_Error(
				"email",
				"Your input email not valid or exist in database.",
				array(
					'status' => 403,
				)
			);
		}

		// Validate username
		if ( username_exists( $name ) || empty( $name ) ) {
			return new WP_Error(
				"name",
				"Your username exist.",
				array(
					'status' => 403,
				)
			);
		}

		return array( "message" => "success!" );
	}

	/**
	 *  Register new user
	 *
	 * @param $request
	 *
	 * @return mixed
	 */
	public function register( $request ) {
		$email      = $request->get_param( 'email' );
		$name       = $request->get_param( 'name' );
		$first_name = $request->get_param( 'first_name' );
		$last_name  = $request->get_param( 'last_name' );
		$password   = $request->get_param( 'password' );
		$subscribe  = $request->get_param( 'subscribe' );

		// Validate email
		if ( ! is_email( $email ) || email_exists( $email ) ) {
			return new WP_Error(
				"email",
				"Your input email not valid or exist in database.",
				array(
					'status' => 403,
				)
			);
		}

		// Validate username
		if ( username_exists( $name ) || empty( $name ) ) {
			return new WP_Error(
				"name",
				"Your username exist.",
				array(
					'status' => 403,
				)
			);
		}

		// Validate first name
		if ( mb_strlen( $first_name ) < 4 ) {
			return new WP_Error(
				"first_name",
				"First name not valid.",
				array(
					'status' => 403,
				)
			);
		}

		// Validate last name
		if ( mb_strlen( $last_name ) < 4 ) {
			return new WP_Error(
				"last_name",
				"Last name not valid.",
				array(
					'status' => 403,
				)
			);
		}

		// Validate password
		if ( empty( $password ) ) {
			return new WP_Error(
				"password",
				"Password is required.",
				array(
					'status' => 403,
				)
			);
		}

		$user_id = wp_insert_user( array(
			"user_pass"    => $password,
			"user_email"   => $email,
			"user_login"   => $name,
			"display_name" => "$first_name $last_name",
			"first_name"   => $first_name,
			"last_name"    => $last_name

		) );

		if ( is_wp_error( $user_id ) ) {
			$error_code = $user_id->get_error_code();

			return new WP_Error(
				$error_code,
				$user_id->get_error_message( $error_code ),
				array(
					'status' => 403,
				)
			);
		}

		// Update phone phone number
		$phone_number = $request->get_param( 'phone_number' );
		if ( $phone_number ) {
			add_user_meta( $user_id, 'phone_number', $phone_number, true );
		}

		// Subscribe
		add_user_meta( $user_id, 'mbd_subscribe', $subscribe, true );

		$user = get_user_by( 'id', $user_id );

		$token = $this->generate_token( $user );
		$data  = array(
			'token' => $token,
			'user'  => $user->data,
		);

		return $data;

	}

	public function getUrlContent($url) {
	    $parts = parse_url($url);
	    $host = $parts['host'];
	    $ch = curl_init();
	    $header = array('GET /1575051 HTTP/1.1',
	        "Host: {$host}",
	        'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
	        'Accept-Language:en-US,en;q=0.8',
	        'Cache-Control:max-age=0',
	        'Connection:keep-alive',
	        'Host:adfoc.us',
	        'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
	    );

	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}

	/**
	 * Login with google
	 *
	 * @param $request
	 */
	public function login_google( $request ) {
		$idToken = $request->get_param( 'idToken' );

		$url  = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $idToken;
		$data = array( 'idToken' => $idToken );

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header' => "application/json; charset=UTF-8\r\n",
				'method' => 'GET'
			)
		);

		$context = stream_context_create( $options );
		$json    = $this->getUrlContent( $url );
		$result  = json_decode( $json );

		if ( $result === false ) {
			$error = new WP_Error();
			$error->add( 403, "Get Firebase user info error!", array( 'status' => 400 ) );

			return $error;
		}

		// Email not exist
		$email = $result->email;
		if ( ! $email ) {
			return new WP_Error(
				'email_not_exist',
				'User not provider email',
				array(
					'status' => 403,
				)
			);
		}

		$user = get_user_by( 'email', $email );

		// Return data if user exist in database
		if ( $user ) {
			$token = $this->generate_token( $user );
			$data  = array(
				'token' => $token,
				'user'  => $user->data,
			);

			return $data;
		} else {

			$user_id = wp_insert_user( array(
				"user_pass"     => wp_generate_password(),
				"user_login"    => $result->email,
				"user_nicename" => $result->name,
				"user_email"    => $result->email,
				"display_name"  => $result->name,
				"first_name"    => $result->given_name,
				"last_name"     => $result->family_name

			) );

			if ( is_wp_error( $user_id ) ) {
				$error_code = $user->get_error_code();

				return new WP_Error(
					$error_code,
					$user_id->get_error_message( $error_code ),
					array(
						'status' => 403,
					)
				);
			}

			$user = get_user_by( 'id', $user_id );

			$token = $this->generate_token( $user );
			$data  = array(
				'token' => $token,
				'user'  => $user->data,
			);

			add_user_meta( $user_id, 'mbd_login_method', 'google', true );
			add_user_meta( $user_id, 'mbd_avatar', $result->picture, true );

			return $data;
		}
	}

	/**
	 * Login With Apple
	 *
	 * @param $request
	 *
	 * @return object
	 * @throws Exception
	 */
	public function login_apple( $request ) {
		try {
			$identityToken = $request->get_param( 'identityToken' );
			$userIdentity  = $request->get_param( 'user' );

			$publicKeyDetails = Generace_App_Control_Public_Key::getPublicKey();

			$publicKey = $publicKeyDetails['publicKey'];
			$alg       = $publicKeyDetails['alg'];

			$payload = JWT::decode( $identityToken, $publicKey, [ $alg ] );

			if ( $payload->sub !== $userIdentity ) {
				return new WP_Error(
					'validate-user',
					'User not validate',
					array(
						'status' => 403,
					)
				);
			}

			$user = get_user_by( 'login', $userIdentity );

			// Return data if user exist in database
			if ( $user ) {
				$token = $this->generate_token( $user );
				$data  = array(
					'token' => $token,
					'user'  => $user->data,
				);

				return (object) $data;
			}

			$userdata = array(
				"user_pass"  => wp_generate_password(),
				"user_login" => $userIdentity,
			);
			if ( $payload->email ) {
				$userdata['email'] = $payload->email;
			}

			$user_id = wp_insert_user( $userdata );
			if ( is_wp_error( $user_id ) ) {
				$error_code = $user_id->get_error_code();

				return new WP_Error(
					$error_code,
					$user_id->get_error_message( $error_code ),
					array(
						'status' => 403,
					)
				);
			}

			$user = get_user_by( 'id', $user_id );

			$token = $this->generate_token( $user );
			$data  = array(
				'token' => $token,
				'user'  => $user->data,
			);

			add_user_meta( $user_id, 'mbd_login_method', 'apple', true );

			return (object) $data;

		} catch ( Exception $e ) {
			return new WP_Error(
				$e->getCode(),
				$e->getMessage(),
				array(
					'status' => 403,
				)
			);
		}
	}

	public function login_facebook( $request ) {
		$token = $request->get_param( 'token' );

		$fb = new \Facebook\Facebook( [
			'app_id'                => FB_APP_ID,
			'app_secret'            => FB_APP_SECRET,
			'default_graph_version' => 'v2.10',
			//'default_access_token' => '{access-token}', // optional
		] );

		try {
			// Get the \Facebook\GraphNodes\GraphUser object for the current user.
			// If you provided a 'default_access_token', the '{access-token}' is optional.
			$response = $fb->get( '/me?fields=id,first_name,last_name,name,picture,email', $token );
		} catch ( \Facebook\Exceptions\FacebookResponseException $e ) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch ( \Facebook\Exceptions\FacebookSDKException $e ) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		$me = $response->getGraphUser();

		// Email not exist
		$email = $me->getEmail();
		if ( ! $email ) {
			return new WP_Error(
				'email_not_exist',
				'User not provider email',
				array(
					'status' => 403,
				)
			);
		}

		$user = get_user_by( 'email', $email );

		// Return data if user exist in database
		if ( $user ) {
			$token = $this->generate_token( $user );
			$data  = array(
				'token' => $token,
				'user'  => $user->data,
			);

			return $data;
		} else {
			// Will create new user
			$first_name  = $me->getFirstName();
			$last_name   = $me->getLastName();
			$picture     = $me->getPicture();
			$name        = $me->getName();
			$facebook_id = $me->getId();

			$user_id = wp_insert_user( array(
				"user_pass"     => wp_generate_password(),
				"user_login"    => $email,
				"user_nicename" => $name,
				"user_email"    => $email,
				"display_name"  => $name,
				"first_name"    => $first_name,
				"last_name"     => $last_name

			) );

			if ( is_wp_error( $user_id ) ) {
				$error_code = $user->get_error_code();

				return new WP_Error(
					$error_code,
					$user_id->get_error_message( $error_code ),
					array(
						'status' => 403,
					)
				);
			}

			$user = get_user_by( 'id', $user_id );

			$token = $this->generate_token( $user );
			$data  = array(
				'token' => $token,
				'user'  => $user->data,
			);

			add_user_meta( $user_id, 'mbd_login_method', 'facebook', true );
			add_user_meta( $user_id, 'mbd_avatar', $picture, true );

			return $data;
		}

	}

	/**
	 * Do login with email and password
	 */
	public function login( $request ) {

		$username = $request->get_param( 'username' );
		$password = $request->get_param( 'password' );

		// try login with username and password
		$user = wp_authenticate( $username, $password );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		// Generate token
		$token = $this->generate_token( $user );

		// Return data
		$data = array(
			'token' => $token,
			'user'  => $user->data,
		);

		return $data;
	}

	/**
	 * Do login with with otp
	 */
	public function login_otp( $request ) {

		$idToken = $request->get_param( 'idToken' );

		$url  = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/getAccountInfo?key=' . MBD_FIREBASE_SERVER_KEY;
		$data = array( 'idToken' => $idToken );

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query( $data )
			)
		);

		$context = stream_context_create( $options );
		$json    = file_get_contents( $url, false, $context );
		$result  = json_decode( $json );

		if ( $result === false ) {
			$error = new WP_Error();
			$error->add( 403, "Get Firebase user info error!", array( 'status' => 400 ) );

			return $error;
		}

		$phone_number = $result->users[0]->phoneNumber;

		$users = get_users( array(
			"meta_key"     => "phone_number",
			"meta_value"   => $phone_number,
			"meta_compare" => "="
		) );

		if ( count( $users ) == 0 ) {
			$error = new WP_Error();
			$error->add( 403, "هیچ کاربری مطابق با شماره تماس شما یافت نشد!", array( 'status' => 400 ) );

			return $error;
		}

		$user = $users[0];

		// Generate token
		$token = $this->generate_token( $user );

		// Return data
		$data = array(
			'token' => $token,
			'user'  => $user->data,
		);

		return $data;
	}

	/**
	 *  General token
	 *
	 * @param $user
	 *
	 * @return string
	 */
	public function generate_token( $user, $data = array() ) {
		$iat = time();
		$nbf = $iat;
		$exp = $iat + ( DAY_IN_SECONDS * 30 );

		$token = array(
			'iss'  => get_bloginfo( 'url' ),
			'iat'  => $iat,
			'nbf'  => $nbf,
			'exp'  => $exp,
			'data' => array_merge( array(
				'user_id' => $user->data->ID
			), $data ),
		);

		// Generate token
		return JWT::encode( $token, $this->key );
	}

	public function determine_current_user( $user ) {
		// Run only on REST API
		$rest_url_prefix = rest_get_url_prefix();

		$valid_rest_url = strpos( $_SERVER['REQUEST_URI'], $rest_url_prefix );
		if ( ! $valid_rest_url ) {
			return $user;
		}

		$token = $this->decode();

		if ( is_wp_error( $token ) ) {
			return $user;
		}

		// only read data to
		// if (isset($token->data->read_only) && $token->data->read_only && $_SERVER['REQUEST_METHOD'] != "GET") {
		//     return $user;
		// }

		return $token->data->user_id;
	}

	/**
	 * Decode token
	 * @return array|WP_Error
	 */
	public function decode( $token = null ) {
		/*
		 * Get token on header
		 */

		if ( ! $token ) {

			$headers = $this->headers();

			if ( ! isset( $headers['Authorization'] ) ) {
				return new WP_Error(
					'no_auth_header',
					'Authorization header not found.',
					array(
						'status' => 403,
					)
				);
			}


			$match = preg_match( '/Bearer\s(\S+)/', $headers['Authorization'], $matches );

			if ( ! $match ) {
				return new WP_Error(
					'token_not_validate',
					'Token not validate format.',
					array(
						'status' => 403,
					)
				);
			}

			$token = $matches[1];

		}

		/** decode token */
		try {
			$data = JWT::decode( $token, $this->key, array( 'HS256' ) );

			if ( $data->iss != get_bloginfo( 'url' ) ) {
				return new WP_Error(
					'bad_iss',
					'The iss do not match with this server',
					array(
						'status' => 403,
					)
				);
			}
			if ( ! isset( $data->data->user_id ) ) {
				return new WP_Error(
					'id_not_found',
					'User ID not found in the token',
					array(
						'status' => 403,
					)
				);
			}

			return $data;

		} catch ( Exception $e ) {
			return new WP_Error(
				'invalid_token',
				$e->getMessage(),
				array(
					'status' => 403,
				)
			);
		}
	}

	public function get_featured_media_url( $object, $field_name, $request ) {
		$featured_media_url = '';
		$image_attributes   = wp_get_attachment_image_src(
			get_post_thumbnail_id( $object['id'] ),
			'full'
		);
		if ( is_array( $image_attributes ) && isset( $image_attributes[0] ) ) {
			$featured_media_url = (string) $image_attributes[0];
		}

		return $featured_media_url;
	}

	/**
	 * Get request headers
	 * @return array|false
	 */
	function headers() {
		if ( function_exists( 'apache_request_headers' ) ) {
			return apache_request_headers();
		} else {

			foreach ( $_SERVER as $key => $value ) {
				if ( substr( $key, 0, 5 ) == "HTTP_" ) {
					$key         = str_replace( " ", "-",
						ucwords( strtolower( str_replace( "_", " ", substr( $key, 5 ) ) ) ) );
					$out[ $key ] = $value;
				} else {
					$out[ $key ] = $value;
				}
			}

			return $out;
		}
	}
}
