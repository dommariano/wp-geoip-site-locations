<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

interface Settings_Admin_Interface {
	public function get( $unprefixed_option_name );

	public function set_geoip_test_ip( $option_value );

	public function set_test_mobile_coords_from( $option_value );

	public function set_test_coords_to( $option_value );

	public function set_maxmind_user_id( $option_value );

	public function set_maxmind_license_key( $option_value );
}
