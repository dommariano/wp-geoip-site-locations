<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

interface Settings_Admin_Interface {
	public function get( $unprefixed_option_name );

	public function get_geoip_db();
	public function set_geoip_db( $option_value );

	public function get_geoip_web_service();
	public function set_geoip_web_service( $option_value );

	public function get_persistent_redirect_status();
	public function set_persistent_redirect_status( $option_value );

	public function get_persistence_interval();
	public function set_persistence_interval( $option_value );

	public function get_lightbox_as_location_chooser_status();
	public function set_lightbox_as_location_chooser_status( $option_value );

	public function get_lightbox_trigger_element();
	public function set_lightbox_trigger_element( $option_value );

	public function get_mobile_high_accuracy_status();
	public function set_mobile_high_accuracy_status( $option_value );

	public function get_distance_limit();
	public function set_distance_limit( $option_value );

	public function get_geoip_test_status();
	public function set_geoip_test_status( $option_value );

	public function get_query_proxies_status();
	public function set_query_proxies_status( $option_value );

	public function get_geoip_test_database_or_service();
	public function set_geoip_test_database_or_service( $option_value );

	public function get_geoip_test_ip();
	public function set_geoip_test_ip( $option_value );

	public function get_test_mobile_coords_from();
	public function set_test_mobile_coords_from( $option_value );

	public function get_test_coords_to();
	public function set_test_coords_to( $option_value );

	public function get_maxmind_user_id();
	public function set_maxmind_user_id( $option_value );

	public function get_maxmind_license_key();
	public function set_maxmind_license_key( $option_value );

	public function get_google_gdm_client_id();
	public function set_google_gdm_client_id( $option_value );

	public function get_google_gdm_client_id_crypto_key();
	public function set_google_gdm_client_id_crypto_key( $option_value );

	public function get_google_grgc_api_key();
	public function set_google_grgc_api_key( $option_value );
}