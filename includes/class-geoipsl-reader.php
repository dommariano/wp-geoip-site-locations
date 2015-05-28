<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

class Reader implements Reader_Interface {

  protected $_local_reader;
  protected $_remote_reader;
  private $_is_using_local;

  public function __construct() {
    $this->set_to_use_geoip_db();
  }

  public function set_geoip_db_reader( \GeoIp2\Database\Reader $local_reader ) {
    $this->_local_reader = $local_reader;
  }

  public function set_remote_db_reader( \GeoIp2\WebService\Client $remote_reader ) {
    $this->_remote_reader = $remote_reader;
  }

  public function set_to_use_geoip_db() {
    $this->_is_using_local = TRUE;
  }

  public function set_to_use_remote_db() {
    $this->_is_using_local = FALSE;
  }

  public function is_using_geoip_db() {
    return $this->_is_using_local;
  }

  public function is_using_remote_db() {
    return ! $this->_is_using_local;
  }

  public function query_city( $ip ) {
    if ( $this->_is_using_local ) {
      if ( ! is_a( $this->_local_reader, 'GeoIp2\Database\Reader' ) ) {
          return 0;
      }

      return $this->_local_reader->city( $ip );
    }

    return $this->_remote_reader->city( $ip );
  }

  public function query_country( $ip ) {
    if ( $this->_is_using_local ) {
      if ( ! is_a( $this->_local_reader, 'GeoIp2\Database\Reader' ) ) {
          return 0;
      }
      return $this->_local_reader->country( $ip );
    }

    return $this->_remote_reader->country( $ip );
  }

  public function query_insights( $ip ) {
    if ( ! $this->_is_using_local ) {
      if ( ! is_a( $this->_local_reader, 'GeoIp2\WebService\Client' ) ) {
          return 0;
      }
      return $this->_remote_reader->insights( $ip );
    }
  }

  public static function get_path_to_geoip_db_reader( $id ) {
    switch ( $id ) {
      case 1:
        $path = geoipsl_get_file_path( 'GeoLite2-City.mmdb' );
        break;
      case 2:
        $path = geoipsl_get_file_path( 'GeoIP2-Country.mmdb' );
        break;
      case 3:
        $path = geoipsl_get_file_path( 'GeoIP2-City.mmdb' );
        break;
      default:
        $path = geoipsl_get_file_path( 'GeoLite2-City.mmdb' );
        break;
    }

    return $path;
  }
}
