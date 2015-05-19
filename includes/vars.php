<?php
/**
 * Creates common globals for the rest of WordPress GeoIP Site Location.
 *
 * @package GeoIPSL
 */

$current_site = get_current_site();
$current_site = $current_site->id;

switch_to_blog( $current_site );

global $post, $geoipsl_reader, $geoipsl_settings, $geoipsl_admin_settings, $mobile_detect;

// setup our plugin
$geoipsl_reader = new GeoIPSL\Reader();
$geoipsl_settings = new GeoIPSL\Settings( (array) get_option( geoipsl( 'site_group_head_settings' ), array() ) );
$geoipsl_db_file_to_use  = GeoIPSL\Reader::get_path_to_geoip_db_reader( $geoipsl_settings->get( 'geoip_db' ) );

/**
 * Provide default values to configuration options.
 */
if ( '' == $geoipsl_settings->get( 'use_geolocation' ) ) {
  $geoipsl_settings->set( 'use_geolocation', 'manual' );
}

if ( '' == $geoipsl_settings->get( 'visitor_tracking' ) ) {
  $geoipsl_settings->set( 'visitor_tracking', 'none' );
}

/**
 * If the geipdb file does not exist, do not set the plugin to read from it.
 */
if ( file_exists( $geoipsl_db_file_to_use ) ) {
  $reader = new GeoIp2\Database\Reader( $geoipsl_db_file_to_use );
  $geoipsl_reader->set_geoip_db_reader( $reader );
}

// if fully available let's use the web service
if ( $geoipsl_settings->get( 'geoip_web_service' ) &&
     $geoipsl_settings->get( 'maxmind_user_id' ) &&
     $geoipsl_settings->get( 'maxmind_license_key' ) ) {
  $geoipsl_reader->set_remote_db_reader( new GeoIp2\WebService\Client(
    $geoipsl_settings->get( 'maxmind_user_id' ),
    $geoipsl_settings->get( 'maxmind_license_key' )
  ) );

  // if were are running out of queries, let's go back to using local dbs
  if ( geoipsl_get_remaining_queries( $geoipsl_settings->get( 'geoip_web_service' ) ) >= 100 ) {
    $geoipsl_reader->set_to_use_remote_db();
  } else {
    $geoipsl_reader->set_to_use_geoip_db();
  }
}

$mobile_detect = new Mobile_Detect();

if ( is_admin() ) {
  $geoipsl_admin_settings = new GeoIPSL\Settings_Admin( $geoipsl_settings );
}

restore_current_blog();
