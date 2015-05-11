<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

/**
  * GeoIPSL\IP contains all utility functions for dealing with IPs.
  *
  * @package GeoIPSL
  * @author Dominique Mariano <dominique.acpal.mariano@gmail.com>
  */
class IP {

  /**
    * Attempt to get the visitor WAN/LAN IP using $_SERVER variables AND
    * detect if user is using a proxy.
    *
    * This function cannot differentiate between a distorting proxy and a
    * transparent proxy. This also cannot differentiate between a high
    * anonymous proxy and user no proxy at all. Thus, this function can only
    * determine some distorting or transparent open proxies. Anonymous proxy IP
    * and true user IP is treated the same.
    *
    * @since 0.1.0
    *
    * @param string $only Filter to isolate results. 'ip' will return only the
    * IP address, 'proxy_score' will return only the proxy score. Anything else
    * will return an array containing both.
    * @return string | array IP address or proxy score or an array containing
    * both.
    */
  public static function get_visitor_ip( $only = '' ) {

    global $geoipsl_settings;

    $ip_info = array(
      'ip' => '',
      'proxy_score' => 0,
    );

    // Default value of 0 assumes no proxy is used
    $proxy_score = 0;

    // if debugging is on
    if ( GEOIPSL_ON_STATUS == $geoipsl_settings->get( 'geoip_test_status' ) ) {

      $ip_info['ip']          = $geoipsl_settings->get( 'geoip_test_ip' );
      $ip_info['proxy_score'] = $proxy_score;

    } else {

      // HTTP_VIA can be set by a distorting proxy, transparent proxy
      if ( isset( $_SERVER['HTTP_VIA'] ) && ! empty( $_SERVER['HTTP_VIA'] ) ) {
        $proxy_score  += 2;
      }

      // HTTP_X_FORWARDED_FOR can be set by a distorting proxy, transparent proxy
      if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $proxy_score  += 3;
      }

      $ip_info['ip']          = geoipsl_array_value( $_SERVER, 'REMOTE_ADDR', '' );
      $ip_info['proxy_score'] = $proxy_score;
    }

    unset( $proxy_score );

    switch ( $only ) {
      case 'ip':
        return $ip_info['ip'];
        break;
      case 'proxy_score':
        return $ip_info['proxy_score'];
        break;
      default:
        return $ip_info;
        break;
    }
  }

  /**
    * Checks if IPv4 IP is in a given range specified in CIDR format.
    *
    * @since 0.1.0
    *
    * @param string $ip A valid non-reserved IPv4 IP
    * @param string $range A valid non-reserved IPv4 CIDR
    * @return bool Boolean false if IP is not valid IPv4, or CIRD is not valid
    * IPv4 CIDR
    */
  public static function cidr_match_ipv4( $ip, $range ) {
    list ( $subnet, $bits ) = explode( '/', $range );

    if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
      return FALSE;
    }

    if ( ! filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
      return FALSE;
    }

    $ip     = ip2long( $ip );
    $subnet = ip2long( $subnet );
    $mask   = -1 << ( 32 - $bits );

    $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
    return ( $ip & $mask ) == $subnet;
  }

  /**
    * Checks if given IP is a reserved IPv4 IP.
    *
    * @since 0.1.0
    *
    * @param string $ip A valid non-reserved IPv4 IP
    * @return bool|int Boolean true if IP is reserved, false if not reserved,
    * 0 if invalid IP is given.
    */
  public static function is_reserved_ipv4( $ip ) {

    if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
      return FALSE;
    }

    // List of CIRDs from WikiPedia http://en.wikipedia.org/wiki/Reserved_IP_addresses
    $reserved_ip = array(
      '0.0.0.0/8',
      '10.0.0.0/8',
      '100.64.0.0/10',
      '127.0.0.0/8',
      '169.254.0.0/16',
      '172.16.0.0/12',
      '192.0.0.0/29',
      '192.0.2.0/24',
      '192.88.99.0/24',
      '192.168.0.0/16',
      '198.18.0.0/15',
      '198.51.100.0/24',
      '203.0.113.0/24',
      '224.0.0.0/4',
      '240.0.0.0/4',
      '255.255.255.255/32',
    );

    // If a match is found, the IP given is a reserved IP
    foreach ( $reserved_ip as $range ) {
      if ( isset( $match_found ) && $match_found ) {
        break;
      }

      $match_found = self::cidr_match_ipv4( $ip, $range );
    }

    unset( $reserved_ip, $range );

    return $match_found;
  }
}
