<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

/**
  * GeoIPSL\Cookie class is for reading, writing and interpreting tracking cookie information to/from the browser.
  *
  * The cookie information that this class reads and writes is about the browsing behavior of the site visitor.
  * This tracking information is in the following format:
  *
  *     [dataSize]-[dataSizeLimit]-[firstAccessTime].[codedAccessTime]-[siteID]-[pageID]
  *
  * with new .[codedAccessTime]-[siteID]-[pageID] blocks being appeded to the end of the cookie string for each
  * site visit. At the same time, every time the site is visited, [dataSize] is incremented by one. If the number
  * of times a particular site has been visited has already exceeded the [dataSizeLimit], that is if
  * [dataSize] is greater than [dataSizeLimit], [dataSize] will be decremented by one and the first occurence of
  * the .[codedAccessTime]-[siteID]-[pageID] block will be removed from the cookie string ( FIFO ).
  *
  * An example tracking info, sent for the first time, is the following:
  *
  *     1-30-1417618892.0-37-1
  *
  * If the visitor loads the site again, or visits another page within the same site, the new tracking info will be
  * something like:
  *
  *     1-30-1417618892.0-37-1.0-37-3
  *
  * If the visitor decides to visito a page on another subsite, the tracking info will be something like:
  *
  *     1-30-1417618892.0-37-1.0-37-3.0-70-18
  *
  * This tracking information is used to speed up Geolocation lookups. Instead of waiting for the users location
  * information ( IP for desktop, coordinates for mobile ) to be retrieved and doing the calculations, we simply
  * read the cookie and decide which site to serve based on browsing behaviour.
  *
  * The reason I decided to base this on browsing behaviour is to account for the accuracy limitations of geolocation
  * information in the first place. IP to geolocation info will never be 100% accurate. If our system ever fails to
  * match a geotargetted site to the users actual location, the site visitor can simply manually change the site location
  * ( assuming the web developer provides an option to change or choose site location ) and the cookie will remember that,
  * thus solving the accuracy issue on subsequent visits.
  *
  * The solution for this accuracy issue is implemented in the method GeoIPSL\Cookie::infer_site_preference()
  *
  * We look at the last [dataSizeLimit] site visits ( in our case, 30 too keep the cookie size very small ). We then
  * "infer" the geo-targetted site which the user will more likely visit given the past 30 visits. We do this by
  * implementing a simple ranking algorithm:
  *
  *     Given $cookie_string = [dataSize]-[dataSizeLimit]-[firstAccessTime].[codedAccessTime]-[siteID]-[pageID][...],
  *     where [...] refers to any additional [codedAccessTime]-[siteID]-[pageID] block and where the total
  *     number of [codedAccessTime]-[siteID]-[pageID] blocks is equal to [dataSize] and at most [dataSizeLimit],
  *     the method infer_site_preference( $cookie_string ) will return the [siteID] in $cookie_string
  *     with the highest score computed as:
  *
  *     ( frequency of [siteID] ) x
  *     ( number of unique [pageID]'s associated with [siteID] ) x
  *     ( square root of the number of seconds spent on all pages visited in [siteID] )
  *
  *     Should two or more [siteID] have the same rank, the program SHOULD return them all. Currenlty, in
  *     this situation it will simply returh the first it will find.
  *
  * @package GeoIPSL
  * @author Dominique Mariano <dominique.acpal.mariano@gmail.com>
  * @todo Handle the case in GeoIPSL\Cookie::infer_site_preference() when two or more sites have the same score.
  */

class Cookies {

  /**
    * Get wp_geoipsl cookie value from site visitor.
    *
    * @since 0.1.0
    * @return string
    */
  public static function get_tracking_cookie() {
    return geoipsl_array_value( $_COOKIE, 'wp_geoipsl', '' );
  }

  /**
    * Create wp_geoipsl cookie with visitor browsing behaviour tracking information.
    *
    * @since 0.1.0
    * @todo Handle case for updating limits.
    *
    * @param int $blog_id The current blog id.
    * @param int $limit The number of site visits to track in the wp_geoipsl cookie.
    * @param int $_time_code The time ( UNIX timestamp ) that visitor accessed the site.
    * @return void
    */
  public static function set_location_cookie( $blog_id = 1, $limit = 30, $_time_code = 0 ) {

    if ( ! is_int( $blog_id ) ) {
      throw new \InvalidArgumentException( 'set_location_cookie expects $blog_id to be of type int, ' . gettype( $blog_id ) . ' given.' );
    }

    if ( ! is_int( $limit ) ) {
      throw new \InvalidArgumentException( 'set_location_cookie expects $limit to be of type int, ' . gettype( $limit ) . ' given.' );
    }

    if ( ! is_int( $_time_code ) ) {
      throw new \InvalidArgumentException( 'set_location_cookie expects $_time_code to be of type int, ' . gettype( $_time_code ) . ' given.' );
    }

    if ( $blog_id < 1 ) {
      throw new \InvalidArgumentException( 'set_location_cookie expects $blog_id to be a natural number, ' . $blog_id . ' given.' );
    }

    if ( $limit < 0 ) {
      throw new \InvalidArgumentException( 'set_location_cookie expects $limit to be a whole number, ' . $limit . ' given.' );
    }

    if ( $_time_code < 0 ) {
      throw new \InvalidArgumentException( 'set_location_cookie expects $_time_code to be a whole number, ' . $_time_code . ' given.' );
    }

    global $post;

    $time_code    = 0; // number used to transcode time so we have smaller numbers to work with
    $post_id      = is_null( $post->ID ) ? 0 : $post->ID; // post ID of 0 is for 404 page
    $wp_geoipsl   = str_replace( ' ', '', (string) self::get_tracking_cookie() );
    $domain       = preg_replace( "/^http(s?):\/\//", '', trim( get_site_url( 1 ) ) );

    // first time visitor
    if ( '' == $wp_geoipsl ) {
      $time_code  = $_time_code;
      $wp_geoipsl = sprintf( "0-%d-%d", $limit, $time_code );
    } else {
      $time_code = self::get_tracking_time_code( $wp_geoipsl );
    }

    $data_size = intval( self::get_tracking_count( $wp_geoipsl ) );

    // we implement a first-in-first-out approach when removing data entries on the cookie
    if ( $data_size >= $limit ) {
      $wp_geoipsl = self::remove_data_entry( $wp_geoipsl );
    }

    $wp_geoipsl  = self::set_tracking_count( $wp_geoipsl, 1 );
    $wp_geoipsl .= sprintf( ".%d-%d-%d", self::code_time( $time_code ), $blog_id, $post_id );

    setcookie( 'wp_geoipsl', $wp_geoipsl, 0, '/', sprintf( ".%s", $domain ) );
  }

  /**
    * Get tracking info time code.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return int
    */
  public static function get_tracking_time_code( $wp_geoipsl_cookie_string ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new \InvalidArgumentException( 'get_tracking_time_code expects $wp_geoipsl_cookie_string to be of type int, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    preg_match( "/^\d+-(\d+)-(\d+)/", $wp_geoipsl_cookie_string, $matches );
    return (int) $matches[2];
  }

  /**
    * Get tracking info limit.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return int
    */
  public static function get_tracking_limit( $wp_geoipsl_cookie_string ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new \InvalidArgumentException( 'get_tracking_limit expects $wp_geoipsl_cookie_string to be of type int, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    preg_match( "/^\d+-(\d+)/", $wp_geoipsl_cookie_string, $matches );
    return (int) $matches[1];
  }

  /**
    * Get tracking info counter.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string the current cookie value.
    * @return int
    */
  public static function get_tracking_count( $wp_geoipsl_cookie_string ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new \InvalidArgumentException( 'get_tracking_count expects $wp_geoipsl_cookie_string to be of type int, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    preg_match( "/^(\d+)-/", $wp_geoipsl_cookie_string, $matches );
    return (int) $matches[1];
  }

  /**
    * Set tracking info counter.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string The current cookie value.
    * @param int $offset The offset to add to the current cookie tracking count.
    * @return string
    */
  public static function set_tracking_count( $wp_geoipsl_cookie_string, $offset = 1 ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new \InvalidArgumentException( 'set_tracking_count expects $wp_geoipsl_cookie_string to be of type int, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    if ( ! is_int( $offset ) ) {
      throw new \InvalidArgumentException( 'set_tracking_count expects $offset to be of type int, ' . gettype( $offset ) . ' given.' );
    }

    $count = $offset + self::get_tracking_count( $wp_geoipsl_cookie_string );

    return preg_replace( "/^(\d+)/", $count, $wp_geoipsl_cookie_string );
  }

  /**
    * Determine which site to serve for repeat visitors based on
    * cookie information.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string The current cookie value.
    * @return void
    */
  public static function infer_site_preference( $wp_geoipsl_cookie_string ) {

    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new \InvalidArgumentException( 'infer_site_preference expects $wp_geoipsl_cookie_string to be of type int, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    $wp_geoipsl = self::parse_tracking_data( $wp_geoipsl_cookie_string );

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

    if ( 1 == count( $site_preference ) ) {
      return (int) $site_preference[0];
    }

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
    * @param array $time_data Array of time data.
    * @param int $interval_limit Maximum number of seconds allowed difference between two recorded time stamps.
    *            This limit is needed as a corrective method for the ranking algorithm used to infer site preference.
    * @return int $sum Sum of tracked time data.
    */
  public static function parse_tracking_data_time_sum( array $time_data, $interval_limit = 86400 ) {

    if ( ! is_numeric( $interval_limit ) ) {
      throw new \InvalidArgumentException( 'parse_tracking_data_time_sum expects $interval_limit to be integer, ' . gettype( $interval_limit ) . ' given.' );
    }

    if ( $interval_limit < 0 ) {
      throw new \InvalidArgumentException( 'parse_tracking_data_time_sum expects $interval_limit to be whole number, ' . $interval_limit . ' given.' );
    }

    $time_data  = array_values( $time_data ); // make sure our array is 0-indexed
    $sum        = 0;

    sort( $time_data, SORT_NUMERIC );

    // not enough data to work with
    if ( count( $time_data ) < 2 ) {
      return 0;
    }

    foreach ( $time_data as $key => $coded_timestamp ) {
      if ( 0 == $key ) {
        continue;
      }

      $sum += abs( $coded_timestamp - $time_data[ ( $key - 1 ) ] );
    }

    unset( $key, $coded_timestamp );

    return $sum;
  }

  /**
    * Get time info from tracking data, given blog id.
    *
    * @since 0.1.0
    *
    * @param array $wp_geoipsl Associative array of values in a predefined format.
    * @param int $blog_id Current blog ID. Defaults to 1 if nothing is provided.
    * @return array 0-indexed array of coded-time values.
    */
  public static function parse_tracking_data_time( array $wp_geoipsl, $blog_id = 1 ) {

    if ( empty( $wp_geoipsl ) ) {
      return array();
    }

    if ( ! isset( $wp_geoipsl['time'][0] ) ) {
      return array();
    }

    if ( ! isset( $wp_geoipsl['blog'][0] ) ) {
      return array();
    }

    if ( ! isset( $wp_geoipsl['page'][0] ) ) {
      return array();
    }

    if ( ! is_int( $blog_id ) ) {
      throw new \InvalidArgumentException( 'parse_tracking_data_time expects $blog_id to be integer, ' . gettype( $blog_id ) . ' given.' );
    }

    if ( $blog_id < 1 ) {
      throw new \InvalidArgumentException( "parse_tracking_data_time expects $blog_id to be positive integer, $blog_id given." );
    }

    $blog_id_keys  = array_keys( $wp_geoipsl['blog'], $blog_id );
    $bleeding_keys = array();

    foreach ( $blog_id_keys as $index => $blog_id_key ) {
      if ( 0 == $index ) {
        continue;
      }

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
    * @param int $blog_id Current blog ID. Defaults to 1 if nothing is provided.
    * @return array 0-indexed array of coded-time values.
    */
  public static function parse_tracking_data_page( array $wp_geoipsl, $blog_id = 1 ) {
    if ( empty( $wp_geoipsl ) ) {
      return array();
    }

    if ( ! isset( $wp_geoipsl['time'][0] ) ) {
      return array();
    }

    if ( ! isset( $wp_geoipsl['blog'][0] ) ) {
      return array();
    }

    if ( ! isset( $wp_geoipsl['page'][0] ) ) {
      return array();
    }

    if ( ! is_int( $blog_id ) ) {
      throw new \InvalidArgumentException( 'parse_tracking_data_page expects $interval_limit to be integer, ' . gettype( $interval_limit ) . ' given.' );
    }

    if ( $blog_id < 1 ) {
      throw new \InvalidArgumentException( "parse_tracking_data_page expects $interval_limit to be positive integer, $interval_limit given." );
    }

    $blog_id_keys = array_keys( $wp_geoipsl['blog'], $blog_id );

    return array_intersect_key( $wp_geoipsl['page'], array_flip( $blog_id_keys ) );
  }

  /**
    * Get the difference between the current time ( UNIX timestamp ) and a previously recorded time in the same format.
    *
    * @since 0.1.0
    *
    * @param int $time_code Time in UNIX time stamp.
    * @return int $time_code Difference between current system time and time code.
    */
  public static function code_time( $time_code = 0 ) {
    if ( ! is_int( $time_code ) ) {
      throw new \InvalidArgumentException( 'code_time expects $time_code to be int, ' . gettype( $time_code ) . ' given.' );
    }

    if ( $time_code < 0 ) {
      throw new \InvalidArgumentException( 'code_time expects $time_code to be whole number, ' . $time_code . ' given.' );
    }

    if ( $time_code > time() ) {
      throw new \InvalidArgumentException( 'code_time expects $time_code to be less than ' . time() . ', ' . $time_code . ' given.' );
    }

    return time() - $time_code;
  }

  /**
    * Remove the first data entry [time]-[blog]-[page] at the beginning of the wp_geoipsl cookie string and
    * update the cookie's data length accordingly.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string The current cookie value.
    * @return string $wp_geoipsl_cookie_string The new cookie value.
    */
  public static function remove_data_entry( $wp_geoipsl_cookie_string ) {
    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new \InvalidArgumentException( 'remove_data_entry expects $wp_geoipsl_cookie_string to be string, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    $wp_geoipsl_cookie_string = preg_replace( "/^(\d+)-(\d+)-(\d+)\.(\d+)-(\d+)-(\d+)/", "$1-$2-$3", $wp_geoipsl_cookie_string );
    $wp_geoipsl_cookie_string = self::set_tracking_count( $wp_geoipsl_cookie_string, -1 );

    return $wp_geoipsl_cookie_string;
  }

  /**
    * Parse tracking data from wp_geoipsl site cookie into an associative array.
    *
    * @since 0.1.0
    *
    * @param string $wp_geoipsl_cookie_string The current cookie value.
    * @return array $info An associative array containing the wp_geoipsl cookie values in appropriate keys.
    */
  public static function parse_tracking_data( $wp_geoipsl_cookie_string ) {

    if ( ! is_string( $wp_geoipsl_cookie_string ) ) {
      throw new \InvalidArgumentException( 'parse_tracking_data expects $wp_geoipsl_cookie_string to be string, ' . gettype( $wp_geoipsl_cookie_string ) . ' given.' );
    }

    $count      = self::get_tracking_count( $wp_geoipsl_cookie_string );
    $data_raw   = preg_replace( "/^\d+-(\d+)-(\d+)\./", '', $wp_geoipsl_cookie_string );
    $data       = explode( ".", $data_raw );

    $info = array(
      'time' => array(),
      'blog' => array(),
      'page' => array(),
    );

    if ( empty( $data ) ) {
      unset( $count, $data_raw, $data_raw );
      return $info;
    }

    foreach ( $data as $tracking_info ) {
      preg_match( "/^(\d+)-(\d+)-(\d+)/", (string) $tracking_info, $matches );

      if ( ! isset( $matches[1] ) ) {
        continue;
      }

      if ( ! isset( $matches[2] ) ) {
        continue;
      }

      if ( ! isset( $matches[3] ) ) {
        continue;
      }

      $info['time'][] = $matches[1];
      $info['blog'][] = $matches[2];
      $info['page'][] = $matches[3];

      unset( $matches );
    }

    unset( $count, $data_raw, $data, $tracking_info );
    return $info;
  }
}
