<?php

require_once( '../../../wp-blog-header.php' );

global $wpdb, $geoipsl_settings;

/**
 * Retrieve the visitor IP address.
 */
$ip = GeoIPSL\IP::get_visitor_ip( 'ip' );

/**
 * Check if visitor is behind a proxy we can detect.
 */
$proxy_score = (int) GeoIPSL\IP::get_visitor_ip( 'proxy_score' );

/**
 * If the visitor has visited the site more than once, determine the site
 * to serve based on our client side tracking script. The result of this
 * script, which will be the blog ID of the blog to serve, is stored on a
 * cookie.
 *
 * The tracking cookie will have one and only one blog id.
 */
$tracking_info = GeoIPSL\Cookies::get_tracking_cookie();
$tracking_info = GeoIPSL\Cookie::parse_tracking_cookie( $tracking_info );

if ( is_int( $tracking_info ) ) {
  $blog_id = $tracking_info;
  $tracking_info = array(
    'href' => get_site_url( intval( $blog_id ) ),
    'remember' => 1,
  );
}

$tracking_info = wp_parse_args( array(
  'href' => '',
  'remember' => '',
), $tracking_info );

unset( $blog_id );

/**
 * Check if the IP we obtained from the visitor is a reserved IP.
 */
$ip = GeoIPSL\IP::is_reserved_ipv4( $ip  ) ? GEOIPSL_RESERVED_IP : $ip;

/**
 * Check if the IP we obtaiend from the visitor is a valid IP.
 */
$ip = '' == $ip || filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $ip : GEOIPSL_INVALID_IP;

/**
 * If we have a reserved IP or an invalid IP, or if no IP can be detected,
 * let us redirect home.
 */
if ( 'ip' == $geoipsl_settings->get( 'use_geolocation' ) && in_array( $ip, array( '', GEOIPSL_RESERVED_IP, GEOIPSL_INVALID_IP ) ) ) {
  wp_redirect( add_query_arg( array( 'welcome' => 'home' ), get_home_url() ) );
  exit;
}

/**
 * If the plugin is configured to redirect to a geo-targetted sub-site even if
 * the visitor is obviously from behind a proxy.
 */
if ( 'ip' == $geoipsl_settings->get( 'use_geolocation' ) && $proxy_score && 'on' !=  $geoipsl_settings->get( 'query_proxies_status' ) ) {
  wp_redirect( add_query_arg( array( 'welcome' => 'home' ), get_home_url() ) );
  exit;
}

if ( 'none' != $geoipsl_settings->get( 'visitor_tracking' ) && $tracking_info['href'] && $tracking_info['remember'] ) {
  wp_redirect( esc_url( $tracking_info['href'] ) );
  exit;
}

/**
 * If we have no tracking information yet to monitor visitor browsing behavior
 * from subsite to subsite, we may use server-side geo-to-ip conversions.
 */
if ( 'on' == $geoipsl_settings->get( 'use_geoip_detection' ) ) {

  $result = $geoipsl_reader->query_city( $ip );

  if ( empty( $result ) ) {
    wp_redirect( add_query_arg( array( 'welcome' => 'home' ), get_home_url() ) );
    exit;
  }

  $lat      = $result->location->latitude;
  $long     = $result->location->longitude;
  $blog_ids = GeoIPSL\Distance::get_closest_site( $lat, $long, 1000 * floatval( $geoipsl_settings->get( 'distance_limit' ) ) );

  if ( $geoipsl_reader->is_using_remote_db() ) {
    $remaining_queries = geoipsl_get_remaining_queries( $geoipsl_settings->get( 'geoip_web_service' ) );
    $remaining_queries = (int) isset( $result->maxmind->queriesRemaining ) ? $result->maxmind->queriesRemaining : $remaining_queries;
    geoipsl_set_maxmind_queries( $geoipsl_settings->get( 'geoip_web_service' ), $remaining_queries );
  }

  /**
   * @todo Unlikely but, when we cannot decide on which closest site to serve
   * using geo-to-ip infomation (e.g., we have perfectly equidistant points to
   * the visitor location ), let the visitor decide which site to visit.
   */
  if ( is_array( $blog_ids ) && count( $blog_ids ) > 1 ) {
  }

  /**
   * When we are sure there is only one closes site based on our geo-to-ip
   * calculations.
   */
  $blog_id = ( $blog_ids[0] ) ? $blog_ids[0] : 1;
  $blog_id = intval( $blog_id );
  $current_site = get_current_site();

  /**
   * get_site_url( $blog_id ) will not work inside this file we we do an actual
   * WordPress database query. Make sure the result of this query is not being
   * cached.
   */
  $request_uri = wp_cache_get( 'request_uri' );
  if ( '' !== $request_uri ) {
    wp_cache_flush();
    $request_uri = $wpdb->get_var( $wpdb->prepare(
      " SELECT domain
        FROM $wpdb->blogs
        WHERE blog_id = %d
      ",
      $blog_id
    ) );
    wp_cache_set( 'request_uri', $request_uri, 'geoipsl', 1 );
    wp_cache_add_non_persistent_groups( 'geoipsl' );
  }

  /**
   * If the blog ID we retrieve is the same as the root site ID, let's us send
   * the visitor back to where he came from.
   */
  if ( $blog_id == $current_site->id ) {
    $request_uri = add_query_arg( array( 'welcome' => 'home' ), $request_uri );
  }

  if ( ! GeoIPSL\Site_Locations::is_on_site_entry_point( $blog_id ) ) {
    wp_redirect( esc_url( $request_uri ) );
    exit;
  }
}
