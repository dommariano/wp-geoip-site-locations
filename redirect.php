<?php

require_once( '../../../wp-blog-header.php' );

global $wpdb;

$ip 						= GeoIPSL\IP::get_visitor_ip( 'ip' );
$proxy_score 		= (int) GeoIPSL\IP::get_visitor_ip( 'proxy_score' );
$tracking_info 	= GeoIPSL\Cookies::get_tracking_cookie();

$ip = GeoIPSL\IP::is_reserved_ipv4( $ip  ) ? GEOIPSL_RESERVED_IP : $ip;
$ip = '' == $ip || filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $ip : GEOIPSL_INVALID_IP;

if ( in_array( $ip, array( '', GEOIPSL_RESERVED_IP, GEOIPSL_INVALID_IP ) ) || ! is_int( $proxy_score ) ) {
	wp_redirect( add_query_arg( array( 'welcome' => 'home' ), get_home_url() ) );
	exit;
}

if ( '' == $tracking_info ) {
	$result   = $geoipsl_reader->query_city( $ip );
	$lat      = $result->location->latitude;
  $long     = $result->location->longitude;
  $blog_ids = GeoIPSL\Distance::get_closest_site( $lat, $long, 1000 * floatval( $geoipsl_settings->get( 'distance_limit' ) ) );

  if ( $geoipsl_reader->is_using_remote_db() ) {
    $remaining_queries = $geoipsl_settings->get( 'maxmind_remaining_queries' );
    $remaining_queries = (int) isset( $result->maxmind->queriesRemaining ) ? $result->maxmind->queriesRemaining : $remaining_queries;
		$geoipsl_settings->set( 'maxmind_remaining_queries', $remaining_queries );	
	}
} else {
  $blog_ids = GeoIPSL\Cookies::infer_site_preference( $tracking_info );
}

// when we cannot decide on which closest site to serve
if ( count( $blog_ids ) > 1 ) {
  // self::load_desktop_switcher( $blog_ids );
  // self::load_mobile_app();
}

// when we are sure there is only one closes site
$blog_id 			= ( $blog_ids[0] ) ? $blog_ids[0] : 1;
$blog_id 			= intval( $blog_id );
$current_site = get_current_site();

// get_site_url( $blog_id ) will not work inside this file
// we we do an actual wordpress database query
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

if ( $blog_id == $current_site->id ) {
	$request_uri = add_query_arg( array( 'welcome' => 'home' ), $request_uri );
}

if ( ! GeoIPSL\Site_Locations::is_on_site_entry_point( $blog_id ) ) {

  // incomplete, needs time interval implementation
  if ( GEOIPSL_ON_STATUS == $geoipsl_settings->get( 'persistent_redirect_status' ) ) {
  	wp_redirect( esc_url( $request_uri ) );
    exit;
  } elseif ( '' == GeoIPSL\Cookies::get_tracking_cookie() ) {
    wp_redirect( esc_url( $request_uri ) );
    exit;
  }
}
