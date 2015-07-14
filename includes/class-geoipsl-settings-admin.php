<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

/**
 * The main administrative settings class.
 *
 * The purpose of this class, or instances of this class is to read/write
 * settings values to and from the database WITHOUT doing a lot of database
 * calls when reading values while at the same time without compromising
 * memory.
 *
 * When writing values to the options table ( of either the root site or the
 * appropriate subsites ), this class will write to the appropriate options
 * field on the options table and simultaneously update the
 * "site_group_head_settings options" which is an array of every possible
 * settings that exist.
 *
 * When reading values from the database settings, the whole
 * site_group_head_settings will be read. But instead of having rather huge
 * object with default values, the array will only fields with actual values.
 *
 * @todo Simplify setter functions. Functions that save the same kind of data
 *       to the options table have to be consolidated into one to reduce
 *       our code mass.
 *
 * @todo Simplify argument checking. Currently PHP does not support
 *       type hinting for primitives. Figure out a work around to reduce our
 *       code mass.
 */
class Settings_Admin implements Settings_Admin_Interface {

	private $admin_settings;

	public function __construct( Settings &$settings ) {
		$this->admin_settings = $settings;
	}

	public function get( $unprefixed_option_name ) {
		return $this->admin_settings->get( $unprefixed_option_name );
	}

	public function set_geoip_test_ip( $option_value ) {
		if ( ! is_string( $option_value ) ) {
			return;
		}

		$option_value = IP::is_reserved_ipv4( $option_value ) ? GEOIPSL_RESERVED_IP : $option_value;
		$option_value = '' == $option_value || filter_var( $option_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $option_value : GEOIPSL_INVALID_IP;

		if ( GEOIPSL_RESERVED_IP == $option_value ) {
			return;
		}

		if ( GEOIPSL_INVALID_IP == $option_value ) {
			return;
		}

		$this->admin_settings->set( 'geoip_test_ip', $option_value );
	}

	public function set_test_mobile_coords_from( $option_value ) {
		if ( ! is_string( $option_value ) ) {
			return;
		}

		$option_value = str_replace( ' ', '', $option_value );

		if ( '' == $option_value ) {
			$this->admin_settings->set( 'test_mobile_coords_from', $option_value );
			return;
		}

		$option_values = explode( ',', $option_value );

		if ( 2 != count( $option_values ) && '' != $option_value ) {
			return;
		}

		if ( ! is_numeric( trim( $option_values[0] ) ) ) {
			return;
		}

		if ( ! is_numeric( trim( $option_values[1] ) ) ) {
			return;
		}

		$this->admin_settings->set( 'test_mobile_coords_from', $option_value );
	}

	public function set_test_coords_to( $option_value ) {
		if ( ! is_string( $option_value ) ) {
			return;
		}

		$option_value = str_replace( ' ', '', $option_value );

		if ( '' == $option_value ) {
			$this->admin_settings->set( 'test_mobile_coords_to', $option_value );
			return;
		}

		$option_values = explode( ',', $option_value );

		if ( 2 != count( $option_values ) && '' != $option_value ) {
			return;
		}

		if ( ! is_numeric( trim( $option_values[0] ) ) ) {
			return;
		}

		if ( ! is_numeric( trim( $option_values[1] ) ) ) {
			return;
		}

		$this->admin_settings->set( 'test_mobile_coords_to', $option_value );
	}

	public function set_maxmind_user_id( $option_value ) {
		if ( ! is_string( $option_value ) ) {
			return;
		}

		$this->admin_settings->set( 'maxmind_user_id', $option_value );
	}

	public function set_maxmind_license_key( $option_value ) {
		if ( ! is_string( $option_value ) ) {
			return;
		}

		$this->admin_settings->set( 'maxmind_license_key', $option_value );
	}

	public function set_maxmind_remaining_queries( $option_value ) {
		if ( ! is_int( $option_value ) ) {
			return;
		}

		$this->admin_settings->set( 'maxmind_remaining_queries', $option_value );
	}
}
