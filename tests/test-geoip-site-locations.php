<?php

require_once( GEOIPSL_PLUGIN_DIR . 'includes/class-geoip-site-locations.php' );
require_once( GEOIPSL_PLUGIN_DIR . 'vendor/autoload.php');

use GeoIp2\Database\Reader;
use GeoIp2\WebService\Client;

class GeoIP_Site_Locations_Test extends WP_UnitTestCase {

	function setUp(){
		parent::setUp();
	}

	function tearDown(){
    delete_option( geoipsl_prefix_string( 'plugin_version' ) );
    delete_option( geoipsl_prefix_string( 'database_version' ) );
    delete_option( geoipsl_prefix_string( 'geoip_db' ) );
    delete_option( geoipsl_prefix_string( 'service_db_to_use' ) );
    delete_option( geoipsl_prefix_string( 'maxmind_user_id' ) );
    delete_option( geoipsl_prefix_string( 'maxmind_license_key' ) );
    delete_option( geoipsl_prefix_string( 'google_gdm_client_id' ) );
    delete_option( geoipsl_prefix_string( 'query_proxies' ) );
    delete_option( geoipsl_prefix_string( 'google_gdm_client_id_crypto_key' ) );
    delete_option( geoipsl_prefix_string( 'first_time_setup_complete' ) );
    delete_option( geoipsl_prefix_string( 'last_update_geoip_lite2_city' ) );
    delete_option( geoipsl_prefix_string( 'last_update_geoip2_country' ) );
    delete_option( geoipsl_prefix_string( 'last_update_geoip2_city' ) );
    delete_option( geoipsl_prefix_string( 'geoip_test_on' ) );
    delete_option( geoipsl_prefix_string( 'geoip_test_ip' ) );
    delete_option( geoipsl_prefix_string( 'geoip_test_coordinates_to' ) );
    delete_option( geoipsl_prefix_string( 'geoip_test_database_or_service' ) );
    delete_option( geoipsl_prefix_string( 'google_grgc_api_key' ) );
    delete_option( geoipsl_prefix_string( 'activated_locations' ) );

		parent::tearDown();
	}

	public function property_data_provider() {
		return array(
			array( 'geoip_db', 1 ),
			array( 'service_db_to_use', 0 ),
			array( 'maxmind_user_id', '' ),
			array( 'maxmind_license_key', '' ),
			array( 'google_gdm_client_id', '' ),
			array( 'google_gdm_client_id_crypto_key', '' ),
			array( 'query_proxies', 1 ),
			array( 'cookie_storage_limit', 30 ),
			array( 'all_active_locations', array() ),
			array( 'geoip_test_on', false ),
			array( 'geoip_test_ip', '' ),
			array( 'geoip_test_coordinates_from', '' ),
			array( 'geoip_test_coordinates_to', '' ),
			array( 'geoip_test_database_or_service', 1 ),
			array( 'google_grgc_api_key', 'AIzaSyA1uvtFpOSRnXqWjrg2pFITfvpzyFiNxE4' ),
		);
	}

	public function proxy_ip_data_provider() {
		return array(
			array( '58.30.231.153' ),
			array( '61.156.35.2' ),
			array( '199.200.120.36' ),
			array( '183.221.217.60' ),
			array( '103.11.116.46' ),
		);
	}

	public function local_ip_data_provider() {
		return array(
			array( '112.203.131.46' ),
		);
	}

	public function ip_data_provider() {
		return array(
			array( '205.174.143.71' ),
			array( '138.26.72.17' ),
		);
	}

	public function test_variable_geoip_db() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'geoip_db', $geoipsl, 'message');
	}

	public function test_variable_service_db_to_use() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'service_db_to_use', $geoipsl, 'message');
	}

	public function test_variable_maxmind_user_id() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'maxmind_user_id', $geoipsl, 'message');
	}

	public function test_variable_maxmind_license_key() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'maxmind_license_key', $geoipsl, 'message');
	}

	public function test_variable_google_gdm_client_id() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'google_gdm_client_id', $geoipsl, 'message');
	}

	public function test_variable_google_gdm_client_id_crypto_key() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'google_gdm_client_id_crypto_key', $geoipsl, 'message');
	}

	public function test_variable_query_proxies() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'query_proxies', $geoipsl, 'message');
	}

	public function test_variable_cookie_storage_limit() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'cookie_storage_limit', $geoipsl, 'message');
	}

	public function test_variable_all_active_locations() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'all_active_locations', $geoipsl, 'message');
	}

	public function test_variable_geoip_test_ip() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'geoip_test_ip', $geoipsl, 'message');
	}

	public function test_variable_geoip_test_coordinates_from() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'geoip_test_coordinates_from', $geoipsl, 'message');
	}

	public function test_variable_geoip_test_coordinates_to() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'geoip_test_coordinates_to', $geoipsl, 'message');
	}

	public function test_variable_geoip_test_database_or_service() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'geoip_test_database_or_service', $geoipsl, 'message');
	}

	public function test_variable_google_grgc_api_key() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$this->assertObjectHasAttribute( 'google_grgc_api_key', $geoipsl, 'message');
	}

	public function test_init() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	  * @expectedException WPDieException
	  */
	public function test_maybe_deactivate_unsupported_version() {
		global $wp_version;
		$wp_version = '2.0';

		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );
		$geoipsl->maybe_deactivate();
	}

	/**
	  * @expectedException WPDieException
	  */
	public function test_maybe_deactivate_not_multisite() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array( 'is_multisite' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'is_multisite' )
								 ->willReturn( false );

		$geoipsl_stub->maybe_deactivate();
	}

	/**
	  * @expectedException WPDieException
	  */
	public function test_maybe_deactivate_no_plugin_version() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array( 'has_plugin_version' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'has_plugin_version' )
								 ->willReturn( false );

		$geoipsl_stub->maybe_deactivate();
	}

	/**
	  * @expectedException WPDieException
	  */
	public function test_maybe_deactivate_no_db_version() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array( 'has_db_version' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'has_db_version' )
								 ->willReturn( false );

		$geoipsl_stub->maybe_deactivate();
	}

	public function test_maybe_uninstall() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_maybe_update_db_version_empty() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );

		delete_option( geoipsl_prefix_string( 'database_version' ) );

		$this->assertTrue( $geoipsl->maybe_update() );
	}

	public function test_maybe_update_db_version_outdated() {
		update_option( geoipsl_prefix_string( 'database_version' ), 2 );
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array( 'get_plugin_db_version' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'get_plugin_db_version' )
								 ->willReturn( 3 );

		$this->assertTrue( $geoipsl_stub->maybe_update() );
	}

	public function test_maybe_update_db_version_up_to_date() {
		update_option( geoipsl_prefix_string( 'database_version' ), 2 );
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array( 'get_plugin_db_version' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'get_plugin_db_version' )
								 ->willReturn( 2 );

		$this->assertFalse( $geoipsl_stub->maybe_update() );
	}

	public function test_setup_hooks() {
		$this->markTestSkipped();
	}

	/**
	  * @dataProvider property_data_provider
	  */
	public function test_get_property( $property, $value ) {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );

		$this->assertEquals( $value, $geoipsl->get_property( $property ) );
	}

	public function test_initialize_options() {
		$this->markTestSkipped();
	}

	public function test_desktop_redirect_to_geoip_subsite_not_home() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array( 'is_true_home' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'is_true_home' )
								 ->willReturn( false );

		$this->assertEquals( -1, $geoipsl_stub->desktop_redirect_to_geoip_subsite() );
	}

	public function test_desktop_redirect_to_geoip_subsite_not_reserved_ip() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array( 'is_reserved_ipv4', 'is_true_home' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'is_true_home' )
								 ->willReturn( true );

		$geoipsl_stub->method( 'is_reserved_ipv4' )
								 ->willReturn( true );

		$this->assertEquals( -2, $geoipsl_stub->desktop_redirect_to_geoip_subsite() );
	}

	/**
	  * @dataProvider ip_data_provider
	  */
	public function test_desktop_redirect_to_geoip_subsite_correct_redirect( $ip ) {

		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_db_file_to_use  = GeoIPSL_Reader::get_path_to_geoip_db_reader( 1 );

		if ( file_exists( $geoipsl_db_file_to_use ) ) {
			$reader = new Reader( $geoipsl_db_file_to_use );
			$geoipsl_reader->set_geoip_db_reader( $reader );
		}

		$geoipsl_stub = $this->getMock( 'GeoIP_Site_Locations', array(  'get_closest_site', 'is_on_site_entry_point', 'is_reserved_ipv4', 'is_true_home', 'get_visitor_ip', 'is_first_time_visitor' ), array( $geoipsl_reader ) );

		$geoipsl_stub->method( 'is_true_home' )
								 ->willReturn( true );

		$geoipsl_stub->method( 'is_on_site_entry_point' )
								 ->willReturn( false );

		$geoipsl_stub->method( 'is_reserved_ipv4' )
								 ->willReturn( false );

		$geoipsl_stub->method( 'is_first_time_visitor' )
								 ->willReturn( true );

		$geoipsl_stub->method( 'get_closest_site' )
								 ->willReturn( 2 );

		$geoipsl_stub->method( 'get_visitor_ip' )
								 ->willReturn( array( 'ip' => $ip, 'proxy_score' => 0 ) );

		$this->assertTrue( @$geoipsl_stub->desktop_redirect_to_geoip_subsite() );

	}

	public function test_infer_site_preference() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_tracking_data_time_sum() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_tracking_data_time() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_tracking_data_page() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_location_cookie() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function cookie_string_data_provider() {
		return array(
			array( 'a' ),
			array( 'b' ),
			array( 'c' ),
		);
	}

	/**
	  * @dataProvider cookie_string_data_provider
	  */
	public function test_set_tracking_cookie( $cookie ) {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_db_file_to_use  = GeoIPSL_Reader::get_path_to_geoip_db_reader( 1 );

		if ( file_exists( $geoipsl_db_file_to_use ) ) {
			$reader = new Reader( $geoipsl_db_file_to_use );
			$geoipsl_reader->set_geoip_db_reader( $reader );
		}

		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );

		$geoipsl->set_tracking_cookie( $cookie );

		$this->assertEquals( $cookie, $geoipsl->get_tracking_cookie( ) );
	}

	public function test_set_location_cookie_first_time() {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_db_file_to_use  = GeoIPSL_Reader::get_path_to_geoip_db_reader( 1 );

		if ( file_exists( $geoipsl_db_file_to_use ) ) {
			$reader = new Reader( $geoipsl_db_file_to_use );
			$geoipsl_reader->set_geoip_db_reader( $reader );
		}

		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );

		$new_post_object 			= new stdClass();
		$new_post_object->ID 	= 1;
		$blog_id 							= 1;
		$time_orig 						= time();
		$time_code 						= time();
		$coded_time 					= abs( $time_orig - $time_code );
		$post_id 							= abs( $new_post_object->ID );

		$geoipsl->set_global_post( $new_post_object );
		$geoipsl->set_tracking_cookie( '' );
		@$geoipsl->set_location_cookie( 1, 30, $time_orig );

		$this->assertEquals( "1-30-$time_orig.$coded_time-$blog_id-$post_id", $geoipsl->get_tracking_cookie() );
	}

	public function cookie_location_data_provider_on_at_most_limit() {
		$cookie_info 							= array();
		$cookie_info_entry 				= array();
		$cookie_info_data_entry 	= array();

		for ( $i = 1;  $i <= 30;  $i++ ) {
			for ( $k = 1; $k <= $i ; $k++ ) {
				$cookie_info_entry = array();
				$cookie_info_data_entry = array();
				$cookie_info_entry[] = $i; // limit
				$cookie_info_entry[] = $k; // data_size
				$cookie_info_entry[] = 1415617735; // arbitrary seconds for Jan 1, 1970 UTC

				for ( $h=1; $h <= $k ; $h++ ) {
					$cookie_info_data_entry[] = array( $i * $k * $h, rand( 1, 100 ), rand( 1, 100 ) );
				}

				$cookie_info_entry[] = $cookie_info_data_entry;
				$cookie_info[] = $cookie_info_entry;
			}
		}

		return $cookie_info;
	}

	public function cookie_location_data_provider_above_limit() {
		$cookie_info 							= array();
		$cookie_info_entry 				= array();
		$cookie_info_data_entry 	= array();

		for ( $i = 1;  $i <= 30;  $i++ ) {
			for ( $k = $i + 1; $k <= $i + 5; $k++ ) {
				$cookie_info_entry = array();
				$cookie_info_data_entry = array();
				$cookie_info_entry[] = $i; // limit
				$cookie_info_entry[] = $k; // data_size
				$cookie_info_entry[] = 1415617735; // arbitrary seconds for Jan 1, 1970 UTC

				for ( $h=1; $h <= $k ; $h++ ) {
					$cookie_info_data_entry[] = array( $i * $k * $h, rand( 1, 100 ), rand( 1, 100 ) );
				}

				$cookie_info_entry[] = $cookie_info_data_entry;
				$cookie_info[] = $cookie_info_entry;
			}
		}

		return $cookie_info;
	}

	/**
	  * @dataProvider cookie_location_data_provider_on_at_most_limit
	  */
	public function test_set_location_cookie_repeat_on_limit( $limit, $data_size, $time, array $data ) {
		$geoipsl_reader 			  = new GeoIPSL_Reader();
		$geoipsl_db_file_to_use	= GeoIPSL_Reader::get_path_to_geoip_db_reader( 1 );

		if ( file_exists( $geoipsl_db_file_to_use ) ) {
			$reader = new Reader( $geoipsl_db_file_to_use );
			$geoipsl_reader->set_geoip_db_reader( $reader );
		}

		$geoipsl_mock    = $this->getMock( 'GeoIP_Site_Locations', array( 'code_time' ), array( $geoipsl_reader ) );
		$post            = new stdClass();
		$expected        = sprintf( "%d-%d-%d", $data_size, $limit, $time );
		$returnValues 	 = array();

		foreach ( $data as $index => $data_entry ) {
			$returnValues[] = $data_entry[0];
		}

		$geoipsl_mock->method( 'code_time' )
			->will( new PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls( $returnValues ) );

		unset( $index, $data_entry );

		$geoipsl_mock->set_tracking_cookie( '' );

		foreach ( $data as $index => $data_entry ) {
			$post->ID = $data_entry[2];
			$geoipsl_mock->set_global_post( $post );

			$coded_time = $data_entry[0];
			$blog_id 		= $data_entry[1];
			$post_id 		= $data_entry[2];

			$expected .= sprintf( ".%d-%d-%d", $coded_time, $blog_id, $post_id );

			@$geoipsl_mock->set_location_cookie( $blog_id, $limit, $time );
		}

		$actual = $geoipsl_mock->get_tracking_cookie();
		$actual = $actual;

		$this->assertEquals( $expected, $actual );
	}

	/**
	  * @dataProvider cookie_location_data_provider_above_limit
	  */
	public function test_set_location_cookie_repeat_above_limit( $limit, $data_size, $time, array $data ) {
		$geoipsl_reader 			  = new GeoIPSL_Reader();
		$geoipsl_db_file_to_use	= GeoIPSL_Reader::get_path_to_geoip_db_reader( 1 );

		if ( file_exists( $geoipsl_db_file_to_use ) ) {
			$reader = new Reader( $geoipsl_db_file_to_use );
			$geoipsl_reader->set_geoip_db_reader( $reader );
		}

		$geoipsl_mock    = $this->getMock( 'GeoIP_Site_Locations', array( 'code_time' ), array( $geoipsl_reader ) );
		$post            = new stdClass();
		$expected        = sprintf( "%d-%d-%d", $limit, $limit, $time );
		$returnValues 	 = array();

		foreach ( $data as $index => $data_entry ) {
			$returnValues[] = $data_entry[0];
		}

		$geoipsl_mock->method( 'code_time' )
			->will( new PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls( $returnValues ) );

		unset( $index, $data_entry );

		$geoipsl_mock->set_tracking_cookie( '' );

		foreach ( $data as $index => $data_entry ) {
			$post->ID = $data_entry[2];
			$geoipsl_mock->set_global_post( $post );

			$blog_id 		= $data_entry[1];

			@$geoipsl_mock->set_location_cookie( $blog_id, $limit, $time );
		}

		unset( $blog_id, $index, $data_entry );

		$data = array_slice( $data, -$limit );

		foreach ( $data as $index => $data_entry ) {
			$coded_time = $data_entry[0];
			$blog_id 		= $data_entry[1];
			$post_id 		= $data_entry[2];

			$expected .= sprintf( ".%d-%d-%d", $coded_time, $blog_id, $post_id );
		}

		$actual = $geoipsl_mock->get_tracking_cookie();
		$actual = $actual;

		$this->assertEquals( $expected, $actual );
	}

	public function remove_data_entry_data_provider() {
		$old = array(
			array( '30-30-1415631637.0-25-1.4-25-1.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '29-30-1415631637.4-25-1.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '28-30-1415631637.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '27-30-1415631637.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '26-30-1415631637.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '25-30-1415631637.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '24-30-1415631637.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '23-30-1415631637.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '22-30-1415631637.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '21-30-1415631637.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '20-30-1415631637.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '19-30-1415631637.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '18-30-1415631637.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '17-30-1415631637.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '16-30-1415631637.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '15-30-1415631637.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '14-30-1415631637.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '13-30-1415631637.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '12-30-1415631637.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '11-30-1415631637.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '10-30-1415631637.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '9-30-1415631637.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '8-30-1415631637.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '7-30-1415631637.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '6-30-1415631637.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '5-30-1415631637.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '4-30-1415631637.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '3-30-1415631637.1411-25-1.1413-25-1.1414-25-1' ),
			array( '2-30-1415631637.1413-25-1.1414-25-1' ),
			array( '1-30-1415631637.1414-25-1' ),

		);

		$new = array(
			array( '29-30-1415631637.4-25-1.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '28-30-1415631637.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '27-30-1415631637.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '26-30-1415631637.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '25-30-1415631637.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '24-30-1415631637.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '23-30-1415631637.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '22-30-1415631637.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '21-30-1415631637.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '20-30-1415631637.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '19-30-1415631637.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '18-30-1415631637.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '17-30-1415631637.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '16-30-1415631637.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '15-30-1415631637.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '14-30-1415631637.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '13-30-1415631637.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '12-30-1415631637.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '11-30-1415631637.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '10-30-1415631637.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '9-30-1415631637.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '8-30-1415631637.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '7-30-1415631637.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '6-30-1415631637.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '5-30-1415631637.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '4-30-1415631637.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( '3-30-1415631637.1411-25-1.1413-25-1.1414-25-1' ),
			array( '2-30-1415631637.1413-25-1.1414-25-1' ),
			array( '1-30-1415631637.1414-25-1' ),
			array( '0-30-1415631637' ),
		);

		foreach ( $new as $key => $value ) {
			$old[ $key ][1] = $value[0];
		}

		return $old;
	}


	public function tracking_count_data_provider() {
		return array(
			array( 30, '30-30-1415631637.0-25-1.4-25-1.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 29, '29-30-1415631637.4-25-1.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 28, '28-30-1415631637.7-25-1.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 27, '27-30-1415631637.804-25-1.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 26, '26-30-1415631637.806-25-1.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 25, '25-30-1415631637.807-25-1.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 24, '24-30-1415631637.1344-25-1.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 23, '23-30-1415631637.1346-25-1.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 22, '22-30-1415631637.1347-25-1.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 21, '21-30-1415631637.1348-25-1.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 20, '20-30-1415631637.1349-25-1.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 19, '19-30-1415631637.1386-25-1.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 18, '18-30-1415631637.1387-25-1.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 17, '17-30-1415631637.1388-25-1.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 16, '16-30-1415631637.1389-25-1.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 15, '15-30-1415631637.1390-25-1.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 14, '14-30-1415631637.1391-25-1.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 13, '13-30-1415631637.1393-25-1.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 12, '12-30-1415631637.1395-25-1.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 11, '11-30-1415631637.1397-25-1.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 10, '10-30-1415631637.1399-25-1.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 9, '9-30-1415631637.1400-25-1.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 8, '8-30-1415631637.1402-25-1.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 7, '7-30-1415631637.1404-25-1.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 6, '6-30-1415631637.1408-25-1.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 5, '5-30-1415631637.1409-25-1.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 4, '4-30-1415631637.1410-25-1.1411-25-1.1413-25-1.1414-25-1' ),
			array( 3, '3-30-1415631637.1411-25-1.1413-25-1.1414-25-1' ),
			array( 2, '2-30-1415631637.1413-25-1.1414-25-1' ),
			array( 1, '1-30-1415631637.1414-25-1' ),
			array( 0, '0-30-1415631637' ),
		);
	}

	/**
	  * @dataProvider remove_data_entry_data_provider
	  */
	public function test_set_location_cookie_remove_data_entry( $old, $new ) {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_db_file_to_use  = GeoIPSL_Reader::get_path_to_geoip_db_reader( 1 );

		if ( file_exists( $geoipsl_db_file_to_use ) ) {
			$reader = new Reader( $geoipsl_db_file_to_use );
			$geoipsl_reader->set_geoip_db_reader( $reader );
		}

		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );

		$this->assertEquals( $new, $geoipsl->remove_data_entry( $old ) );
	}

	public function test_get_tracking_limit() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	  * @dataProvider tracking_count_data_provider
	  */
	public function test_get_tracking_count( $expected_count, $tracking_info ) {
		$geoipsl_reader = new GeoIPSL_Reader();
		$geoipsl_db_file_to_use  = GeoIPSL_Reader::get_path_to_geoip_db_reader( 1 );

		if ( file_exists( $geoipsl_db_file_to_use ) ) {
			$reader = new Reader( $geoipsl_db_file_to_use );
			$geoipsl_reader->set_geoip_db_reader( $reader );
		}

		$geoipsl = new GeoIP_Site_Locations( $geoipsl_reader );

		$this->assertEquals( $expected_count, $geoipsl->get_tracking_count( $tracking_info ) );
	}

	public function test_set_tracking_count() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_tracking_data() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_closest_site() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_is_first_time_visitor() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_mobile_redirect_to_geoip_subsite() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_geocode_request_uri() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_redirect_to_geoip_subsite() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_visitor_ip() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_is_reserved_ipv4() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_cidr_match_ipv4() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	  * @covers ping_ipv4
	  * @dataProvider local_ip_data_provider
	  */
	public function test_ping_ipv4_local( $ip ) {
		//$geoipsl = new GeoIP_Site_Locations();
		//$this->assertFalse( $geoipsl->ping_ipv4( $ip ) );
		$this->markTestSkipped( 'You cannot reliable use ping data to determine proxies.' );
	}

	/**
	  * @covers ping_ipv4
	  * @dataProvider proxy_ip_data_provider
	  */
	public function test_ping_ipv4_proxy( $ip ) {
		//$geoipsl = new GeoIP_Site_Locations();
		//$this->assertTrue( $geoipsl->ping_ipv4( $ip) );
		$this->markTestSkipped( 'You cannot reliable use ping data to determine proxies.' );
	}

	public function test_great_circle_distance() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_geodesic() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_travel_distance() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}
}