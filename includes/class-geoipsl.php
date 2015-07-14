<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

/**
 * Main plugin class.
 *
 * This class handles redirects. Users coming from desktops will be redirected
 &* based on their IP address using the MaxMind GeoIP database or premium web
 * service. Visitors coming from mobile devices the support HTML5 Geolocaiton
 * API will be redirected using that APi. Visitors coming mobile devices
 * without the HTML5 Geolocation will not be redirected, but instead will be
 * given the option to select the site location they wish to be redirected to
 * ( requires theme integration ).
 *
 * @since 0.1.0
 */
class Site_Locations {

	/**
	 * Initialise the program after everything is ready.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function init() {

		/**
	 * ALWAYS make sure the plugin version is up-to-date.
	 */
		update_option( geoipsl( 'plugin_version' ), GEOIPSL_PLUGIN_VERSION );

		/**
	 * DO NOT automatically update the database version. We need the old value
	 * for incremental database updates.
	 */
		add_option( geoipsl( 'database_version' ), GEOIPSL_DATABASE_VERSION );

		/**
	 * Every NONEMPTY setting that exists about this plugin.
	 */
		add_option( geoipsl( 'settings' ), array() );

		add_action( 'template_redirect',                              array( __CLASS__, 'redirect_to_geoip_subsite' ) );
		add_action( 'wp_ajax_ajax_redirect_to_geoip_subsite',         array( __CLASS__, 'ajax_redirect_to_geoip_subsite' ) );
		add_action( 'wp_ajax_nopriv_ajax_redirect_to_geoip_subsite',  array( __CLASS__, 'ajax_redirect_to_geoip_subsite' ) );

		/**
	 * Hack to allow for redirection.
	 */
		ob_start();
	}

	/**
	 * Checks program environment to see if all dependencies are available. If at least one
	 * dependency is absent, deactivate the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function maybe_deactivate() {

		global $wp_version;

		load_plugin_textdomain( 'geoipsl' );

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( version_compare( $wp_version, GEOIPSL_MINIMUM_WP_VERSION, '<' ) ) {

			deactivate_plugins( GEOIPSL_PLUGIN_NAME );

			$message = sprintf( esc_html__( 'GeoIP Site Locations %s requires WordPress %s or higher.', 'geoipsl' ), GEOIPSL_PLUGIN_VERSION, GEOIPSL_MINIMUM_WP_VERSION );

			wp_die( $message );
			exit;
		}

		if ( ! is_multisite() ) {

			deactivate_plugins( GEOIPSL_PLUGIN_NAME );

			$message = __( 'This plugin must only be installed on a WordPress Multisite.', 'geoipsl' );

			wp_die( $message );
			exit;
		}
	}

	/**
	 * When plugin is deleted from admin, ask the user if
	 * they want to delete the database tables and other data as well.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function maybe_uninstall() {
	}

	/**
	 * Install fresh database tables on first install or update the database if this is a
	 * update for the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function maybe_update() {
		$dbversion = get_option( geoipsl( 'database_version' ) );

		$dbversion = abs( $dbversion );

		if ( $dbversion < GEOIPSL_DATABASE_VERSION ) {

			require_once( GEOIPSL_PLUGIN_DIR . 'includes/database.php' );

			return true;
		}

		return false;
	}

	/**
	 * Redirect to appropriate GeoIP subsite.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function redirect_to_geoip_subsite() {

		global $geoipsl_settings;
		global $mobile_detect;

		/**
	 * Load the client side cookie and tracking management system regardless
	 * of whether we are on the root site or subsite.
	 */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_cookie_js' ) );

		/**
	 * If the visitor has visited the site more than once, determine the site
	 * to serve based on our client side tracking script. The result of this
	 * script, which will be the blog ID of the blog to serve, is stored on a
	 * cookie.
	 *
	 * The tracking cookie will have one and only one blog id.
	 */
		$tracking_info = Cookies::get_tracking_cookie();
		$tracking_info = Cookies::parse_tracking_cookie( $tracking_info );

		if ( is_int( $tracking_info ) ) {
			$blog_id = $tracking_info;
			$tracking_info = array(
			'href' => get_site_url( intval( $blog_id ) ),
			'remember' => 1,
			);
		}

		/**
	 * Ensure that the tracking info has the correct array keys.
	 */
		$tracking_info = wp_parse_args( $tracking_info, array(
			'href' => '',
			'remember' => '',
		) );


		if ( self::is_on_site_entry_point( get_current_blog_id() ) && 'none' != $geoipsl_settings->get( 'visitor_tracking' ) && $tracking_info['href'] && $tracking_info['remember'] ) {
			wp_redirect( esc_url( $tracking_info['href'] ) );
			exit;
		}

		/**
	 * Only redirect if we are on the root site.
	 */
		if ( self::is_on_site_entry_point( get_current_blog_id() ) ) {

			if ( is_user_logged_in() && 'on' == $geoipsl_settings->get( 'geoip_test_status' ) ) {
				return;
			}

			if ( $mobile_detect->isMobile() || $mobile_detect->isTablet() ) {
				if ( 'manual' != $geoipsl_settings->get( 'use_geolocation' ) ) {
					add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_mobile_app' ), 1 );
				}
			} else {
				if ( 'h5' == $geoipsl_settings->get( 'use_geolocation' ) ) {
					add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_mobile_app' ), 1 );
				} elseif ( 'ip' == $geoipsl_settings->get( 'use_geolocation' ) ) {
					self::redirect_to_geoip_desktop_subsite();
				}
			}
		} else {
			if ( 'write' == $geoipsl_settings->get( 'visitor_tracking' ) ) {
				Cookies::write_tracking_cookie();
			}
		}

		return;
	}

	/**
	 * Load our front-end assets for geolocation using the MaxMind JavaScript API.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function load_maxmind_js_app() {
		global $geoipsl_settings;

		wp_register_script( 'geoipslmaxmindjsapi', '//js.maxmind.com/js/apis/geoip2/v2.1/geoip2.js', null, null );
		wp_register_script( 'geoipslmaxmindapp', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoipslmaxmindapp.js', array( 'jquery' ), null );

		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}
		wp_enqueue_script( 'geoipslmaxmindjsapi' );
		wp_enqueue_script( 'geoipslmaxmindapp' );
	}

	/**
	 * Load our front-end assets for Geolocation on mobile and tablet devices.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function load_mobile_app() {

		global $geoipsl_settings;

		wp_register_script( 'geoipslapp', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoipslapp.js', array( 'jquery' ), null );

		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		wp_enqueue_script( 'geoipslapp' );
	}

	public static function load_cookie_js() {
		global $geoipsl_settings;

		$current_site = get_current_site();
		$current_site = $current_site->domain;

		wp_register_script( 'geoipslpos', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoPosition.js', null, null );
		wp_register_script( 'geoipsl-cookie', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoipsl-cookie.js', array( 'jquery' ), null );
		wp_localize_script( 'geoipsl-cookie', 'geoipsltracker', array(
			'readCookies' => true,
			'currentBlog' => site_url(),
			'currentSite' => $current_site,
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'triggerElement' => $geoipsl_settings->get( 'lightbox_trigger_element' ),
		) );

		wp_enqueue_script( 'geoipslpos' );
		wp_enqueue_script( 'geoipsl-cookie' );
	}

	/**
	 * AJAX callback function for determining which site to serve.
	 *
	 * @since 0.1.0
	 *
	 * @param none * @return void
	 */
	public static function ajax_redirect_to_geoip_subsite() {
		global $geoipsl_settings;

		if ( ! isset( $_POST[ 'lat_from' ] ) ) {
			exit;
		}

		if ( ! isset( $_POST[ 'lang_from' ] ) ) {
			exit;
		}

		$lat_from  = floatval( $_POST[ 'lat_from' ] );
		$lang_from = floatval( $_POST[ 'lang_from' ] );
		$coords    = array();
		$blog_ids  = array( 0 => 1 );

		if ( 'on' == $geoipsl_settings->get( 'geoip_test_status' ) ) {
			if ( '' != $geoipsl_settings->get( 'test_mobile_coords_from' ) ) {
				$coords = explode( ',', str_replace( ' ', '', $geoipsl_settings->get( 'test_mobile_coords_from' ) ) );
			}
		} else {
			$coords[0] = $lat_from;
			$coords[1] = $lang_from;
		}

		if ( 2 == count( $coords ) ) {
			$blog_ids  = Distance::get_closest_site( floatval( $coords[0] ), floatval( $coords[1] ), 1000 * floatval( $geoipsl_settings->get( 'distance_limit' ) ) );
		}

		$blog_urls = array();

		foreach ( $blog_ids as $key => $blog_id ) {
			$blog_urls[] = array( $blog_id, get_site_url( $blog_id ), $lat_from, $lang_from );
		}

		echo json_encode( $blog_urls );

		unset( $lat_from, $lang_from, $blog_ids, $blog_urls );

		exit;
	}

	/**
	 * Redirect users who are using desktop devices.
	 *
	 * @since 0.1.0
	 *
	 * @param none
	 * @return void
	 */
	public static function redirect_to_geoip_desktop_subsite( ) {

		global $geoipsl_settings;
		global $geoipsl_reader;

		if ( IP::is_reserved_ipv4( IP::get_visitor_ip( 'ip' ) ) ) {
			return GEOIPSL_RESERVED_IP;
		}

		if ( 0 != (int) IP::get_visitor_ip( 'proxy_score' ) && 'off' == $geoipsl_settings->get( 'query_proxies_status' ) ) {
			return GEOIPSL_MAYBE_PROXY;
		}

		if ( preg_match( '/\?welcome=home/', geoipsl_array_value( $_SERVER, 'REQUEST_URI' ) ) ) {
			return;
		}

		wp_redirect( GEOIPSL_PLUGIN_URL . 'redirect.php' );
		exit;
	}

	/**
	 * Whether we are on selected entry point on the root site or
	 * whether we are on the root site or not.
	 *
	 * The entry point will typically be the home or front page of the
	 * root site. We have this function for a future feature where we can specify
	 * some other entry point aside from the home or front page.
	 *
	 * @since 0.1.0
	 *
	 * @param int $blog_id
	 */
	public static function is_on_site_entry_point( $blog_id ) {

		global $geoipsl_settings;
		global $post;

		if ( ! is_int( $blog_id ) ) {
			return false;
		}

		if ( ! $geoipsl_settings->get( 'site_entry_page' ) ) {
			return ( is_main_site( $blog_id ) ) && ( is_home() || is_front_page() );
		}

		if ( is_main_site( $blog_id ) && ( $post->ID === (int) $geoipsl_settings->get( 'site_entry_page' ) ) ) {
			return true;
		}

		return false;
	}
}
