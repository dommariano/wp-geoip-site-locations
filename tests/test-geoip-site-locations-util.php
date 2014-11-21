<?php

require_once( GEOIPSL_PLUGIN_DIR . 'includes/class-geoip-site-locations-util.php' );
require_once( GEOIPSL_PLUGIN_DIR . 'includes/class-geoip-sites-list-table.php' );

class GeoIP_Site_Locations_Util_Test extends WP_UnitTestCase {

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

	public function data_provider_non_positive_integers() {
		return array(
			array( '' 						),
			array( '1' 						),
			array( 'string' 			),
			array( array() 				),
			array( new stdClass() ),
		);
	}

	public function data_provider_non_arrays() {
		return array(
			array( '' 						),
			array( '1' 						),
			array( 'string' 			),
			array( 1        			),
			array( 3.1415    			),
			array( new stdClass() ),
		);
	}

	public function data_provider_non_strings() {
		return array(
			array( false 			    ),
			array( true 					),
			array( array() 				),
			array( new stdClass()	),
		);
	}

	public function data_provider_prefixes() {
		return array(
			array( 'string', 'geoipsl_string' ),
			array( -1      , 'geoipsl_-1'     ),
			array( 0       , 'geoipsl_0'      ),
			array( 1       , 'geoipsl_1'      ),
			array( 0.33    , 'geoipsl_033'    ),
		);
	}

	public function data_provider_valid_data_file_names() {
		return array(
			array( 'a.ext' 	 , '/srv/www/wordpress-develop/src/wp-content/plugins/geoip-site-locations/data/a.ext' ),
			array( '/a.ext'	 , '/srv/www/wordpress-develop/src/wp-content/plugins/geoip-site-locations/data/a.ext' ),
			array( '/a.ext/' , '/srv/www/wordpress-develop/src/wp-content/plugins/geoip-site-locations/data/a.ext' ),
		);
	}

	public function data_provider_valid_dir() {
		return array(
			array( 'a.ext' , 'dir'     , '/srv/www/wordpress-develop/src/wp-content/plugins/geoip-site-locations/dir/a.ext'        ),
			array( 'a.ext' , 'dir/dir' , '/srv/www/wordpress-develop/src/wp-content/plugins/geoip-site-locations/dir/dir/a.ext' ),
		);
	}

	public function data_provider_invalid_dir() {
		return array(
			array( 'a.ext' , '\dir'        ),
			array( 'a.ext' , '\dir\\'      ),
			array( 'a.ext' , '\dir\dir'    ),
			array( 'a.ext' , 'dir\dir\\'   ),
			array( 'a.ext' , '\dir\dir\\'  ),
			array( 'a.ext' , '?dir'        ),
			array( 'a.ext' , '?dir?'       ),
			array( 'a.ext' , '?dir?dir'    ),
			array( 'a.ext' , 'dir?dir?'    ),
			array( 'a.ext' , '?dir?dir?'   ),
			array( 'a.ext' , '%dir'        ),
			array( 'a.ext' , '%dir%'       ),
			array( 'a.ext' , '%dir%dir'    ),
			array( 'a.ext' , 'dir%dir%'    ),
			array( 'a.ext' , '%dir%dir%'   ),
			array( 'a.ext' , '*dir'        ),
			array( 'a.ext' , '*dir*'       ),
			array( 'a.ext' , '*dir*dir'    ),
			array( 'a.ext' , 'dir*dir*'    ),
			array( 'a.ext' , '*dir*dir*'   ),
			array( 'a.ext' , ':dir'        ),
			array( 'a.ext' , ':dir:'       ),
			array( 'a.ext' , ':dir:dir'    ),
			array( 'a.ext' , 'dir:dir:'    ),
			array( 'a.ext' , ':dir:dir:'   ),
			array( 'a.ext' , '|dir'        ),
			array( 'a.ext' , '|dir|'       ),
			array( 'a.ext' , '|dir|dir'    ),
			array( 'a.ext' , 'dir|dir|'    ),
			array( 'a.ext' , '|dir|dir|'   ),
			array( 'a.ext' , '"dir'        ),
			array( 'a.ext' , '"dir"'       ),
			array( 'a.ext' , '"dir"dir'    ),
			array( 'a.ext' , 'dir"dir"'    ),
			array( 'a.ext' , '"dir"dir"'   ),
			array( 'a.ext' , '<dir'        ),
			array( 'a.ext' , '<dir<'       ),
			array( 'a.ext' , '<dir<dir'    ),
			array( 'a.ext' , 'dir<dir<'    ),
			array( 'a.ext' , '"dir<dir<'   ),
			array( 'a.ext' , '>dir'        ),
			array( 'a.ext' , '>dir>'       ),
			array( 'a.ext' , '>dir>dir'    ),
			array( 'a.ext' , 'dir>dir>'    ),
			array( 'a.ext' , '"dir>dir>'   ),
		);
	}

	public function data_provider_invalid_data_file_names() {
		return array(
			array( 'dir/dir' 	     ),
			array( 'dir/dir/dir'   ),
			array( '\dir'          ),
			array( '\dir\\'        ),
			array( '\dir\dir'      ),
			array( 'dir\dir\\'     ),
			array( '\dir\dir\\'    ),
			array( '?dir'          ),
			array( '?dir?'         ),
			array( '?dir?dir'      ),
			array( 'dir?dir?'      ),
			array( '?dir?dir?'     ),
			array( '%dir'          ),
			array( '%dir%'         ),
			array( '%dir%dir'      ),
			array( 'dir%dir%'      ),
			array( '%dir%dir%'     ),
			array( '*dir'          ),
			array( '*dir*'         ),
			array( '*dir*dir'      ),
			array( 'dir*dir*'      ),
			array( '*dir*dir*'     ),
			array( ':dir'          ),
			array( ':dir:'         ),
			array( ':dir:dir'      ),
			array( 'dir:dir:'      ),
			array( ':dir:dir:'     ),
			array( '|dir'          ),
			array( '|dir|'         ),
			array( '|dir|dir'      ),
			array( 'dir|dir|'      ),
			array( '|dir|dir|'     ),
			array( '"dir'          ),
			array( '"dir"'         ),
			array( '"dir"dir'      ),
			array( 'dir"dir"'      ),
			array( '"dir"dir"'     ),
			array( '<dir'          ),
			array( '<dir<'         ),
			array( '<dir<dir'      ),
			array( 'dir<dir<'      ),
			array( '"dir<dir<'     ),
			array( '>dir'          ),
			array( '>dir>'         ),
			array( '>dir>dir'      ),
			array( 'dir>dir>'      ),
			array( '"dir>dir>'   	 ),
			array( 'dir/a.ext'     ),
			array( 'dir/dir/a.ext' ),
		);
	}

	/**
	  * @covers geoipsl_get_prefix
	  */
	public function test_get_prefix() {

		$this->assertEquals( 'geoipsl_', geoipsl_get_prefix() );
	}

	/**
	  * @dataProvider data_provider_prefixes
	  * @covers geoipsl_prefix_string
	  */
	public function test_prefix_string( $string, $expected ) {

		$this->assertEquals( $expected, geoipsl_prefix_string( $string ) );
	}

	/**
	  * @expectedException InvalidArgumentException
	  * @dataProvider data_provider_non_strings
	  */
	public function test_prefix_string_exception( $string ) {

		geoipsl_prefix_string( $string );
	}

	/**
	  * @covers geoipsl_get_list_table
	  */
	public function test_get_list_table() {

		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	  * @covers geoipsl_activate_location
	  */
	public function test_activate_location_is_empty() {

		$current_locations = get_option( geoipsl_prefix_string( 'activated_locations' ) );

		$this->assertEmpty( $current_locations, 'Expecting ' . geoipsl_prefix_string( 'activated_locations' ) . ' to be empty. But value found.' );

		unset( $current_locations );
	}

	/**
	  * @covers geoipsl_activate_location
	  */
	public function test_activate_location_unique_entries() {

		for ( $i=1; $i <= 10; $i++ ) {
			$activated_locations = geoipsl_activate_location( $i, array() );
			$this->assertEquals( $i, count( $activated_locations ) );
		}

		unset( $activated_locations );
	}

	/**
	  * @covers geoipsl_activate_location
	  * @dataProvider data_provider_non_positive_integers
	  * @expectedException InvalidArgumentException
	  */
	public function test_activate_location_invalid_blog_id( $blog_id ) {

		$activated_locations = geoipsl_activate_location( $blog_id, array() );

		unset( $activated_locations );
	}

	/**
	  * @covers geoipsl_activate_location
	  * @dataProvider data_provider_non_arrays
	  * @expectedException InvalidArgumentException
	  */
	public function test_activate_location_invalid_location( $location ) {

		$activated_locations = geoipsl_activate_location( 1, $location );

		unset( $activated_locations );
	}

	/**
	  * @covers geoipsl_activate_location
	  */
	public function test_activate_location_repeat_entries() {

		for ( $i=1; $i <= 2; $i++ ) {
			$activated_locations = geoipsl_activate_location( 100, array() );
			$this->assertEquals( 1, count( $activated_locations ) );
		}

		unset( $activated_locations );
	}

	/**
	  * @covers geoipsl_deactivate_location
	  */
	public function test_deactivate_location_empty_location() {

		$deactivated_locations = geoipsl_deactivate_location( 1 );

		$this->assertEmpty( $deactivated_locations, 'Expecting ' . geoipsl_prefix_string( 'activated_locations' ) . ' to be empty. But value found.' );

		unset( $deactivated_locations );
	}

	/**
	  * @covers geoipsl_deactivate_location
	  */
	public function test_deactivate_location_non_existent_location() {
		$activated_locations = geoipsl_activate_location( 1, array() );
		$deactivated_locations = geoipsl_deactivate_location( 2 );

		$this->assertEquals( 1, count( get_option( geoipsl_prefix_string( 'activated_locations' ) ) ) );
	}

	/**
	  * @covers geoipsl_deactivate_location
	  */
	public function test_deactivate_location_existent_location() {
		$activated_locations = geoipsl_activate_location( 1, array() );
		$deactivated_locations = geoipsl_deactivate_location( 1 );

		$this->assertEquals( 0, count( get_option( geoipsl_prefix_string( 'activated_locations' ) ) ) );
	}

	/**
	  * @covers geoipsl_deactivate_location
	  * @dataProvider data_provider_non_positive_integers
	  * @expectedException InvalidArgumentException
	  */
	public function test_deactivate_location_invalid_id( $blog_id ) {

		$deactivated_locations = geoipsl_deactivate_location( $blog_id );
	}

	/**
	  * @covers geoipsl_wpautop_e
	  */
	public function test_wpautop_e() {

		$this->markTestSkipped( 'Function will be deleted soon.' );
	}

	/**
	  * @covers geoipsl_get_file_path
	  * @dataProvider data_provider_non_strings
	  * @expectedException InvalidArgumentException
	  */
	public function test_get_file_path_non_string_input( $destination_file_name ) {
		geoipsl_get_file_path( $destination_file_name );
	}

	/**
	  * @covers geoipsl_get_file_path
	  * @dataProvider data_provider_valid_data_file_names
	  */
	public function test_get_file_path_valid_destination_file_name( $destination_file_name, $expected ) {
		$this->assertEquals( $expected, geoipsl_get_file_path( $destination_file_name ) );
	}

	/**
	  * @covers geoipsl_get_file_path
	  * @dataProvider data_provider_valid_dir
	  */
	public function test_get_file_path_valid_destination_dir( $destination_file_name, $dir, $expected ) {
		$this->assertEquals( $expected, geoipsl_get_file_path( $destination_file_name, $dir ) );
	}

	/**
	  * @covers geoipsl_get_file_path
	  * @dataProvider data_provider_invalid_dir
	  * @expectedException InvalidArgumentException
	  */
	public function test_get_file_path_invalid_destination_dir( $destination_file_name, $dir ) {
		geoipsl_get_file_path( $destination_file_name, $dir );
	}

	public function test_download_file() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_admin_notices_on_fresh_install() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_date_of_day_on_week() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_next_day_of_week() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_all_days() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_these_days() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_date_of_day_on_month() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_next_month() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_get_prev_month() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	public function test_next_schedule_update_for_geolite2_city() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}
}