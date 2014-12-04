<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

/**
  * The main administrative settings class.
  *
  * The purpose of this class, or instances of this class is to
  * read/write settings values to and from the database WITHOUT
  * doing a lot of database calls when reading values while at
  * the same time without compromising memory.
  *
  * When writing values to the options table ( of either the
  * root site or the appropriate subsites ), this class will write
  * to the appropriate options field on the options table and
  * simultaneously update the "site_group_head_settings options"
  * which is an array of every possible settings that exist.
  *
  * When reading values from the database settings, the whole
  * site_group_head_settings will be read. But instead of having
  * rather huge object with default values, the array will only
  * fields with actual values.
  *
  * @todo Simplify setter functions. Functions that save the same kind of data
  *       to the options table have to be consolidated into one to reduce
  *       our code mass.
  * @todo Simplify argument checking. Currently PHP does not support
  *       type hinting for primitives. Figure out a work around to reduce our
  *       code mass.
  */

class Settings_Admin implements Settings_Admin_Interface {

  private $admin_settings;

	public function __construct( Settings &$settings ) {
    $this->admin_settings = $settings;
  }

  public function get( $unprefixed_option_name ) {
    return $this->admin_settings->get( $unprefixed_option_name );
  }

  public function get_geoip_db() {
    return (int) $this->admin_settings->get( 'geoip_db' );
  }

  public function set_geoip_db( $option_value ) {
    if ( ! in_array( $option_value, range( 1, 3 ) ) ) {
      throw new \InvalidArgumentException( 'set_geoip_db expects $option_value to be in the range, 1 to 3, ' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'geoip_db', $option_value );
  }

  public function get_geoip_web_service() {
    return (int) $this->admin_settings->get( 'geoip_web_service' );
  }

  public function set_geoip_web_service( $option_value ) {

    if ( ! ( in_array( $option_value, range( 1, 3 ) ) || '' == $option_value ) ) {
      throw new \InvalidArgumentException( 'set_geoip_web_service expects $option_value to be in the range, 1 to 3, ' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'geoip_web_service', $option_value );
  }

  public function get_persistent_redirect_status() {
    return (string) $this->admin_settings->get( 'persistent_redirect_status' );
  }

  public function set_persistent_redirect_status( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'get_persistent_redirect_status expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! in_array( $option_value, array( GEOIPSL_OFF_STATUS, GEOIPSL_ON_STATUS ) ) ) {
      throw new \InvalidArgumentException( 'get_persistent_redirect_status expects $option_value to be either be, "' . GEOIPSL_OFF_STATUS . '" or "' . GEOIPSL_ON_STATUS . '"' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'persistent_redirect_status', $option_value );
  }

  public function get_persistence_interval() {
    return (int) $this->admin_settings->get( 'persistence_interval' );
  }

  public function set_persistence_interval( $option_value ) {
    if ( ! is_int( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_persistence_interval expects $option_value to be of type int, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'persistence_interval', $option_value );
  }

  public function get_lightbox_as_location_chooser_status() {
    return (string) $this->admin_settings->get( 'lightbox_as_location_chooser_status' );
  }

  public function set_lightbox_as_location_chooser_status( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_lightbox_as_location_chooser_status expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! in_array( $option_value, array( GEOIPSL_OFF_STATUS, GEOIPSL_ON_STATUS ) ) ) {
      throw new \InvalidArgumentException( 'set_lightbox_as_location_chooser_status expects $option_value to be either be, "' . GEOIPSL_OFF_STATUS . '" or "' . GEOIPSL_ON_STATUS . '"' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'lightbox_as_location_chooser_status', $option_value );
  }

  public function get_lightbox_trigger_element() {
    return (string) $this->admin_settings->get( 'lightbox_trigger_element' );
  }

  public function set_lightbox_trigger_element( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_lightbox_trigger_element expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'lightbox_trigger_element', $option_value );
  }

  public function get_mobile_high_accuracy_status() {
    return (string) $this->admin_settings->get( 'mobile_high_accuracy_status' );
  }

  public function set_mobile_high_accuracy_status( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_mobile_high_accuracy_status expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! in_array( $option_value, array( GEOIPSL_OFF_STATUS, GEOIPSL_ON_STATUS ) ) ) {
      throw new \InvalidArgumentException( 'set_mobile_high_accuracy_status expects $option_value to be either be, "' . GEOIPSL_OFF_STATUS . '" or "' . GEOIPSL_ON_STATUS . '"' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'mobile_high_accuracy_status', $option_value );
  }

  public function get_distance_limit() {
    return (int) $this->admin_settings->get( 'distance_limit' );
  }

  public function set_distance_limit( $option_value ) {
    if ( ! is_int( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_distance_limit expects $option_value to be of type int, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'distance_limit', $option_value );
  }

  public function get_query_proxies_status() {
    return (string) $this->admin_settings->get( 'query_proxies_status' );
  }

  public function set_query_proxies_status( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_query_proxies_status expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! in_array( $option_value, array( GEOIPSL_OFF_STATUS, GEOIPSL_ON_STATUS ) ) ) {
      throw new \InvalidArgumentException( 'set_query_proxies_status expects $option_value to be either be, "' . GEOIPSL_OFF_STATUS . '" or "' . GEOIPSL_ON_STATUS . '"' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'query_proxies_status', $option_value );
  }

  public function get_geoip_test_status() {
    return (string) $this->admin_settings->get( 'geoip_test_status' );
  }

  public function set_geoip_test_status( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_geoip_test_status expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! in_array( $option_value, array( GEOIPSL_OFF_STATUS, GEOIPSL_ON_STATUS ) ) ) {
      throw new \InvalidArgumentException( 'set_geoip_test_status expects $option_value to be either be, "' . GEOIPSL_OFF_STATUS . '" or "' . GEOIPSL_ON_STATUS . '"' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'geoip_test_status', $option_value );
  }

  public function get_redirect_after_load_status() {
    return (string) $this->admin_settings->get( 'redirect_after_load_status' );
  }

  public function set_redirect_after_load_status( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_redirect_after_load_status expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! in_array( $option_value, array( GEOIPSL_OFF_STATUS, GEOIPSL_ON_STATUS ) ) ) {
      throw new \InvalidArgumentException( 'set_redirect_after_load_status expects $option_value to be either be, "' . GEOIPSL_OFF_STATUS . '" or "' . GEOIPSL_ON_STATUS . '"' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'redirect_after_load_status', $option_value );
  }

  public function get_geoip_test_database_or_service() {
    return (int) $this->admin_settings->get( 'geoip_test_database_or_service' );
  }

  public function set_geoip_test_database_or_service( $option_value ) {
    if ( ! is_int( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_geoip_test_database_or_service expects $option_value to be of type int, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! in_array( $option_value, range( 1, 6 ) ) ) {
      throw new \InvalidArgumentException( 'set_geoip_test_database_or_service expects $option_value to be in the range, 1 to 6, ' . $option_value . ' given.' );
    }

    $this->admin_settings->set( 'geoip_test_database_or_service', $option_value );
  }

  public function get_geoip_test_ip()  {
    return (string) $this->admin_settings->get( 'geoip_test_ip' );
  }

  public function set_geoip_test_ip( $option_value )  {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_geoip_test_ip expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $option_value = IP::is_reserved_ipv4( $option_value  ) ? GEOIPSL_RESERVED_IP : $option_value;
    $option_value = '' == $option_value || filter_var( $option_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $option_value : GEOIPSL_INVALID_IP;

    if ( GEOIPSL_RESERVED_IP == $option_value ) {
      throw new \InvalidArgumentException( 'set_geoip_test_ip expects $option_value to be non-reserved IPv4 address, ' . $option_value .  ' given is reserved IP address.' );
    }

    if ( GEOIPSL_INVALID_IP == $option_value ) {
      throw new \InvalidArgumentException( 'set_geoip_test_ip expects $option_value to be a valid IPv4 address, invalid IP address given.' );
    }

    $this->admin_settings->set( 'geoip_test_ip', $option_value );
  }

  public function get_test_mobile_coords_from()  {
    return (string) $this->admin_settings->get( 'test_mobile_coords_from' );
  }

  public function set_test_mobile_coords_from( $option_value )  {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_test_mobile_coords_from expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $option_value = str_replace( ' ', '', $option_value );

    if ( '' == $option_value ) {
      $this->admin_settings->set( 'test_mobile_coords_from', $option_value );
      return;
    }

    $option_values = explode( ',', $option_value );

    if ( 2 != count( $option_values ) && '' != $option_value ) {
      throw new \InvalidArgumentException( 'set_test_mobile_coords_from expects $option_value to have two float values separated by commas, ' . count( $option_values )  . ' value(s) given.');
    }

    if ( ! is_numeric( trim( $option_values[0] ) ) ) {
      throw new \InvalidArgumentException( 'set_test_mobile_coords_from expects first part of $option_value to be numeric, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! is_numeric( trim( $option_values[1] ) ) ) {
      throw new \InvalidArgumentException( 'set_test_mobile_coords_from expects second part of $option_value to be numeric, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'test_mobile_coords_from', $option_value );
  }

  public function get_test_coords_to()  {
    return (string) $this->admin_settings->get( 'test_mobile_coords_to' );
  }

  public function set_test_coords_to( $option_value )  {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_test_coords_to expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $option_value = str_replace( ' ', '', $option_value );

    if ( '' == $option_value ) {
      $this->admin_settings->set( 'test_mobile_coords_to', $option_value );
      return;
    }

    $option_values = explode( ',', $option_value );

    if ( 2 != count( $option_values ) && '' != $option_value ) {
      throw new \InvalidArgumentException( 'set_test_coords_to expects $option_value to have two float values separated by commas, ' . count( $option_values )  . ' value(s) given.');
    }

    if ( ! is_numeric( trim( $option_values[0] ) ) ) {
      throw new \InvalidArgumentException( 'set_test_coords_to expects first part of $option_value to be numeric, ' . gettype( $option_value ) . ' given.' );
    }

    if ( ! is_numeric( trim( $option_values[1] ) ) ) {
      throw new \InvalidArgumentException( 'set_test_coords_to expects second part of $option_value to be numeric, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'test_mobile_coords_to', $option_value );
  }

  public function get_maxmind_user_id() {
    return (string) $this->admin_settings->get( 'maxmind_user_id' );
  }

  public function set_maxmind_user_id( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_maxmind_user_id expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'maxmind_user_id', $option_value );
  }

  public function get_maxmind_license_key() {
    return (string) $this->admin_settings->get( 'maxmind_license_key' );
  }

  public function set_maxmind_license_key( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_maxmind_license_key expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'maxmind_license_key', $option_value );
  }

  public function get_google_gdm_client_id() {
    return (string) $this->admin_settings->get( 'google_gdm_client_id' );
  }

  public function set_google_gdm_client_id( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_google_gdm_client_id expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'google_gdm_client_id', $option_value );
  }

  public function get_google_gdm_client_id_crypto_key() {
    return (string) $this->admin_settings->get( 'google_gdm_client_id_crypto_key' );
  }

  public function set_google_gdm_client_id_crypto_key( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_google_gdm_client_id_crypto_key expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'google_gdm_client_id_crypto_key', $option_value );
  }

  public function get_google_grgc_api_key() {
    return (string) $this->admin_settings->get( 'google_grgc_api_key' );
  }

  public function set_google_grgc_api_key( $option_value ) {
    if ( ! is_string( $option_value ) ) {
      throw new \InvalidArgumentException( 'set_google_grgc_api_key expects $option_value to be of type string, ' . gettype( $option_value ) . ' given.' );
    }

    $this->admin_settings->set( 'google_grgc_api_key', $option_value );
  }
}
