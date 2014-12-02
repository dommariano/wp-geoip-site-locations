<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

class Cookies {

  public static function get_tracking_cookie() {
    return geoipsl_array_value( $_COOKIE, 'wp_geoipsl', '' );
  }

  /**
    * Create wp_geoipsl visitor location tracking cookie.
    *
    * @since 0.1.0
    *
    * @param int $blog_id The current blog id.
    * @return void
    */
  public static function set_location_cookie( $blog_id = 1, $limit = 30, $_time_code = 0 ) {

    global $post;

    $time_code = 0;  // number used to transcode time so we have smaller numbers to work with
    $post_id   = is_null( $post->ID ) ? 0 : $post->ID;

    $wp_geoipsl = (string) self::get_tracking_cookie();
    $domain     = preg_replace( "/^http(s?):\/\//", '', trim( get_site_url( 1 ) ) );

    if ( empty( $wp_geoipsl ) ) {
      $time_code  = $_time_code;
      $wp_geoipsl = sprintf( "0-%d-%d", $limit, $time_code );
    } else {
      $time_code = self::get_tracking_time_code( $wp_geoipsl );
    }

    $data_size  = intval( self::get_tracking_count( $wp_geoipsl ) );

    if ( $data_size >= $limit ) {
      $wp_geoipsl = self::remove_data_entry( $wp_geoipsl );
    }

    $wp_geoipsl = self::set_tracking_count( $wp_geoipsl, 1 );
    $wp_geoipsl .= sprintf( ".%d-%d-%d", self::code_time( $time_code ), abs( $blog_id ), abs( $post_id ) );

    setcookie( 'wp_geoipsl', $wp_geoipsl, 0, '/', sprintf( ".%s", $domain ) );
  }

  /**
    * Get tracking info time code.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return string
    */
  public static function get_tracking_time_code( $wp_geoipsl_cookie_string ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) )
      return '';

    preg_match( "/^\d+-(\d+)-(\d+)/", $wp_geoipsl_cookie_string, $matches );
    return $matches[2];
  }

  /**
    * Get tracking info limit.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return string
    */
  public static function get_tracking_limit( $wp_geoipsl_cookie_string ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) )
      return '';

    preg_match( "/^\d+-(\d+)/", $wp_geoipsl_cookie_string, $matches );
    return $matches[1];
  }

  /**
    * Get tracking info counter.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return string
    */
  public static function get_tracking_count( $wp_geoipsl_cookie_string ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) )
      return '';

    preg_match( "/^(\d+)-/", $wp_geoipsl_cookie_string, $matches );
    return $matches[1];
  }

  /**
    * Set tracking info counter.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return string
    */
  public static function set_tracking_count( $wp_geoipsl_cookie_string, $offset = 1 ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) )
      return '';

    $count = (int) self::get_tracking_count( $wp_geoipsl_cookie_string );
    $count = $count + $offset;

    return preg_replace( "/^(\d+)/", $count, $wp_geoipsl_cookie_string );
  }

  /**
    * Determine which site to serve for repeat visitors based on
    * cookie information.
    *
    * @since 0.1.0
    *
    * @param none
    * @return void
    */
  public static function infer_site_preference( $tracking_info ) {

    $wp_geoipsl = $tracking_info;
    $wp_geoipsl = self::parse_tracking_data( $wp_geoipsl );

    if ( empty( $wp_geoipsl ) ) {
      return 1;
    }

    if ( ! isset( $wp_geoipsl['time'][0] ) ) {
      return 1;
    }

    if ( ! isset( $wp_geoipsl['blog'][0] ) ) {
      return 1;
    }

    if ( ! isset( $wp_geoipsl['page'][0] ) ) {
      return 1;
    }

    $blogs           = array_values( array_unique( $wp_geoipsl['blog'] ) );
    $blogs_info      = array_count_values( $wp_geoipsl['blog'] );

    if ( 1 == count( $blogs ) ) {
      return $blogs;
    }

    foreach ( $blogs_info as $blog_id => $blog_freq ) {
      $num_unique_pages = count( array_unique( self::parse_tracking_data_page( $wp_geoipsl, $blog_id ) ) );
      $time_data        = self::parse_tracking_data_time( $wp_geoipsl, $blog_id );
      $time_sum         = self::parse_tracking_data_time_sum( $time_data );

      $blogs_info[ $blog_id ] = $blog_freq * $num_unique_pages * sqrt( $time_sum );
    }

    unset( $blog_id, $blog_freq, $num_unique_pages, $time_data, $time_sum );

    $site_preference = array_keys( $blogs_info, max( $blogs_info ) );

    if ( 1 == count( $site_preference ) )
      return (int) $site_preference[0];

    // when two sites have the same site_preference score
    // --- need a way to choose which one wins
    // --- perhaps the most recent one wins?
    return $site_preference[0];
  }

  /**
    * Sum time data by combining differences between two recorded site visits.
    *
    * The recorded site visits for a particular blog may or may not be contiguous ---
    * that is different subsites may be visited in no particular order.
    *
    * @since 0.1.0
    *
    * @param int $interval_limit Maximum number of seconds allowed difference between two recorded time stamps.
    *            This limit is needed as a corrective method for the ranking algorithm used to infer site preference.
    * @return int $sum Sum of tracked time data.
    */
  public static function parse_tracking_data_time_sum( array $time_data, $interval_limit = 86400 ) {

    if ( ! is_numeric( $interval_limit ) ) {
      throw new InvalidArgumentException( 'parse_tracking_data_time_sum expects $interval_limit to be integer, ' . gettype( $interval_limit ) . ' given.' );
    }

    $time_data      = array_values( $time_data );
    $interval_limit = abs( $interval_limit );
    $sum            = 0;

    sort( $time_data, SORT_NUMERIC );

    // not enough data to work with
    if ( count( $time_data ) < 2 )
      return 0;

    foreach ( $time_data as $key => $coded_timestamp ) {
      if ( 0 == $key )
        continue;

      $sum += abs( $coded_timestamp - $time_data[ ( $key - 1 ) ] );
    }

    unset( $coded_timestamp );

    return $sum;
  }

  /**
    * Get time info from tracking data, given blog id.
    *
    * @since 0.1.0
    *
    * @param array $wp_geoipsl Associative array of values in a predefined format.
    * @param int $blog_id Blog ID.
    * @return array Array of time values.
    */
  public static function parse_tracking_data_time( array $wp_geoipsl, $blog_id = 1 ) {
    if ( empty( $wp_geoipsl ) )
      return array();

    if ( ! isset( $wp_geoipsl['time'][0] ) )
      return array();

    if ( ! isset( $wp_geoipsl['blog'][0] ) )
      return array();

    if ( ! isset( $wp_geoipsl['page'][0] ) )
      return array();

    if ( ! is_numeric( $blog_id ) )
      return;

    if ( ! is_int( $blog_id ) ) {
      throw new InvalidArgumentException( 'parse_tracking_data_time_sum expects $interval_limit to be integer, ' . gettype( $interval_limit ) . ' given.' );
    }

    if ( $blog_id < 1 ) {
      throw new InvalidArgumentException( "parse_tracking_data_time_sum expects $interval_limit to be positive integer, $interval_limit given." );
    }

    $blog_id_keys  = array_keys( $wp_geoipsl['blog'], $blog_id );
    $bleeding_keys = array();

    foreach ( $blog_id_keys as $index => $blog_id_key ) {
      if ( 0 == $index )
        continue;

      if ( abs( $blog_id_key - $blog_id_keys[ $index - 1 ] ) > 1 ) {
        $bleeding_keys[] = $blog_id_keys[ $index - 1 ] + 1;
      }
    }

    $blog_id_keys = array_values( array_merge( $blog_id_keys, $bleeding_keys ) );

    unset( $index, $blog_id_key );

    return array_intersect_key( $wp_geoipsl['time'], array_flip( $blog_id_keys ) );
  }

  /**
    * Get page info from tracking data, given blog id.
    *
    * @since 0.1.0
    *
    * @param array $wp_geoipsl Associative array of values in a predefined format.
    * @param int $blog_id Blog ID.
    * @return array Array of page values.
    */
  public static function parse_tracking_data_page( array $wp_geoipsl, $blog_id = 1 ) {
    if ( empty( $wp_geoipsl ) )
      return;

    if ( ! isset( $wp_geoipsl['time'][0] ) )
      return;

    if ( ! isset( $wp_geoipsl['blog'][0] ) )
      return;

    if ( ! isset( $wp_geoipsl['page'][0] ) )
      return;

    if ( ! is_int( $blog_id ) ) {
      throw new InvalidArgumentException( 'parse_tracking_data_page expects $interval_limit to be integer, ' . gettype( $interval_limit ) . ' given.' );
    }

    if ( $blog_id < 1 ) {
      throw new InvalidArgumentException( "parse_tracking_data_page expects $interval_limit to be positive integer, $interval_limit given." );
    }

    $blog_id_keys = array_keys( $wp_geoipsl['blog'], $blog_id );

    return array_intersect_key( $wp_geoipsl['page'], array_flip( $blog_id_keys ) );
  }

  public static function code_time( $time_code = 0 ) {

    return abs( time() - $time_code );
  }

  public static function remove_data_entry( $wp_geoipsl_cookie_string, $index = 1 ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new InvalidArgumentException( 'remove_data_entry expects $wp_geoipsl_cookie_string to be string, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    if ( ! is_int( $index ) ) {
      throw new InvalidArgumentException( 'remove_data_entry expects $index to be int, ' . gettype( $index ) . ' given.' );
    }

    if ( $index < 1 ) {
      throw new InvalidArgumentException( "remove_data_entry expects $index to be positive integer $index given." );
    }

    $wp_geoipsl_cookie_string = preg_replace( "/^(\d+)-(\d+)-(\d+)\.(\d+)-(\d+)-(\d+)/", "$1-$2-$3", $wp_geoipsl_cookie_string );
    $wp_geoipsl_cookie_string = self::set_tracking_count( $wp_geoipsl_cookie_string, -1 );

    return $wp_geoipsl_cookie_string;
  }

  /**
    * Get tracking data from site cookie.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return obj $info
    */
  public static function parse_tracking_data( $wp_geoipsl_cookie_string ) {
    $count    = self::get_tracking_count( $wp_geoipsl_cookie_string );
    $data_raw = preg_replace( "/^\d+-(\d+)-(\d+)\./", '', $wp_geoipsl_cookie_string );
    $data     = explode( ".", $data_raw );

    if ( empty( $data ) )
      return;

    $info = array(
      'time' => array(),
      'blog' => array(),
      'page' => array(),
    );

    foreach ( $data as $tracking_info ) {
      preg_match( "/^(\d+)-(\d+)-(\d+)/", (string) $tracking_info, $matches );

      if ( ! isset( $matches[1] ) )
        continue;

      if ( ! isset( $matches[2] ) )
        continue;

      if ( ! isset( $matches[3] ) )
        continue;

      $info['time'][] = $matches[1];
      $info['blog'][] = $matches[2];
      $info['page'][] = $matches[3];

    }

    return $info;
  }
}