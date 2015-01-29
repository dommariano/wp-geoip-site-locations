<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

/**
  * Main plugin class.
  *
  * This class handles redirects. Users coming from desktops
  * will be redirected based on their IP address using the MaxMind GeoIP database or premium
  * web service. Visitors coming from mobile devices the support HTML5 Geolocaiton API will
  * be redirected using that APi. Visitors coming mobile devices without the HTML5 Geolocation
  * will not be redirected, but instead will be given the option to select the site location
  * they wish to be redirected to ( requires theme integration ).
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

    // ALWAYS make sure the plugin version is up-to-date.
    update_option( geoipsl_prefix_string( 'plugin_version' ), GEOIPSL_PLUGIN_VERSION );

    // DO NOT automatically update the database version. We need the old value for incremental database updates.
    add_option( geoipsl_prefix_string( 'database_version' ), GEOIPSL_DATABASE_VERSION );

    // Every NONEMPTY setting that exists about this plugin.
    add_option( geoipsl_prefix_string( 'settings' ), array() );

    add_action( 'template_redirect',                              array( __CLASS__, 'redirect_to_geoip_subsite'        ) );
    add_action( 'wp_ajax_ajax_redirect_to_geoip_subsite',         array( __CLASS__, 'ajax_redirect_to_geoip_subsite'   ) );
    add_action( 'wp_ajax_nopriv_ajax_redirect_to_geoip_subsite',  array( __CLASS__, 'ajax_redirect_to_geoip_subsite'   ) );

    ob_start(); // To allow for redirection.
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
    $dbversion = get_option( geoipsl_prefix_string( 'database_version' ) );

    $dbversion = abs( $dbversion );

    if ( $dbversion < GEOIPSL_DATABASE_VERSION ) {

      require_once( GEOIPSL_PLUGIN_DIR . 'includes/database.php' );

      return TRUE;
    }

    return FALSE;
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

    // only redirect if we are on the root site
    if ( self::is_on_site_entry_point( get_current_blog_id() ) ) {

      if ( is_user_logged_in() && GEOIPSL_ON_STATUS == $geoipsl_settings->get( 'geoip_test_status' ) ) {
        return 1;
      }

      if ( $mobile_detect->isMobile() || $mobile_detect->isTablet() ) {
        add_action( 'wp_enqueue_scripts', array( __CLASS__ , 'load_mobile_app' ), 1 );
      } else {
        if ( GEOIPSL_ON_STATUS == $geoipsl_settings->get( 'redirect_after_load_status' ) ) {
          add_action( 'wp_enqueue_scripts', array( __CLASS__ , 'load_maxmind_js_app' ), 1 );
        } else {
          self::redirect_to_geoip_desktop_subsite();
        }
      }
    } else {
      Cookies::set_location_cookie( get_current_blog_id(), 30, time() );
    }

    return 2;
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

    wp_register_script( 'geoipslmaxmindjsapi', '//js.maxmind.com/js/apis/geoip2/v2.1/geoip2.js', NULL, NULL );
    wp_register_script( 'geoipslmaxmindapp', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoipslmaxmindapp.js', array( 'jquery' ), NULL );
    wp_localize_script( 'geoipslmaxmindapp', 'geoipslapp', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'triggerElement' => $geoipsl_settings->get( 'lightbox_trigger_element' ),
    ) );

    if ( ! wp_script_is('jquery', 'enqueued') ) {
      wp_enqueue_script( 'jquery');
    }
    wp_enqueue_script( 'geoipslmaxmindjsapi');
    wp_enqueue_script( 'geoipslmaxmindapp');
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

    $site_id = get_current_site();
    $site_id = $site_id->id;
    $blog_id = get_current_blog_id();

    if ( $site_id != $blog_id ) {
      return 1;
    }

    wp_register_script( 'geoipslapp', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoipslapp.js', array( 'jquery' ), NULL );
    wp_register_script( 'geoipslpos', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoPosition.js', NULL, NULL );
    wp_localize_script( 'geoipslapp', 'geoipslapp', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'triggerElement' => $geoipsl_settings->get( 'lightbox_trigger_element' ),
      'enableHighAccuracy' => (bool) $geoipsl_settings->get( 'mobile_high_accuracy_status' ),
    ) );

    if ( ! wp_script_is('jquery', 'enqueued') ) {
      wp_enqueue_script( 'jquery');
    }

    wp_enqueue_script( 'geoipslpos');
    wp_enqueue_script( 'geoipslapp');
  }

  /**
    * AJAX callback function for determining which site to serve.
    *
    * @since 0.1.0
    *
    * @param none
    * @return void
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

    if ( $geoipsl_settings->get( 'geoip_test_on' ) ) {
      if ( '' !== $geoipsl_settings->get( 'test_mobile_coords_from' ) ) {
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
      $blog_urls[] = array( $blog_id, get_site_url( $blog_id ) );
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

    if ( 0 != (int) IP::get_visitor_ip( 'proxy_score' ) && GEOIPSL_OFF_STATUS == $geoipsl_settings->get( 'query_proxies_status' ) ) {
      return GEOIPSL_MAYBE_PROXY;
    }

    if ( preg_match( "/\?welcome=home/", geoipsl_array_value( $_SERVER, 'REQUEST_URI' ) ) ) {
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
      throw new \InvalidArgumentException( 'is_on_site_entry_point expects $blog_id to be integer, ' . gettype( $blog_id ) . ' given.' );
    }

    if ( ! $geoipsl_settings->get( 'site_entry_page' ) ) {
      return ( is_main_site ( $blog_id ) ) && ( is_home() || is_front_page() );
    }

    if ( is_main_site ( $blog_id ) && ( $post->ID === (int) $geoipsl_settings->get( 'site_entry_page' ) ) ) {
      return TRUE;
    }

    return FALSE;
  }
}
