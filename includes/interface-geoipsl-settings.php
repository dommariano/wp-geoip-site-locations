<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

interface Settings_Interface {
	public function get( $unprefixed_option_name );
	public function set( $unprefixed_option_name, $option_value, $type, $filter );
}
