<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

interface Reader_Interface {
	public static function get_path_to_geoip_db_reader( $id );
	public function set_geoip_db_reader( \GeoIp2\Database\Reader $_local_reader );
	public function set_remote_db_reader( \GeoIp2\WebService\Client $_remote_reader );
	public function query_city( $ip );
	public function query_country( $ip );
	public function query_insights( $ip );
	public function set_to_use_geoip_db();
	public function set_to_use_remote_db();
	public function is_using_geoip_db();
	public function is_using_remote_db();
}