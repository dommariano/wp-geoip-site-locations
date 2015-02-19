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
  */

class Settings implements Settings_Interface {
  private $settings;

  public function __construct( array $settings ) {
    $this->settings = $settings;
  }

  public function get( $unprefixed_option_name ) {
    if ( ! is_string( $unprefixed_option_name ) ) {
      throw new InvalidArgumentException( 'get expects $unprefixed_option_name to be string, ' . gettype( $unprefixed_option_name ) . ' given.' );
    }

    return ( isset( $this->settings[ $unprefixed_option_name ] ) ) ? $this->settings[ $unprefixed_option_name ] : '';
  }

  public function set( $unprefixed_option_name, $option_value ) {
    if ( ! is_string( $unprefixed_option_name ) ) {
      throw new InvalidArgumentException( 'set expects $unprefixed_option_name to be string, ' . gettype( $unprefixed_option_name ) . ' given.' );
    }

    update_option( geoipsl( $unprefixed_option_name ), $option_value );

    $this->settings[ $unprefixed_option_name ] = $option_value;

    update_option( geoipsl( 'site_group_head_settings' ), $this->settings );
  }
}
