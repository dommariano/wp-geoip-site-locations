<?php global $geoipsl_settings, $geoipsl_reader; ?>

<form id="geoipsl-settings-keys" action="" method="">

	<?php wp_nonce_field( 'geoipsl_settings' ); ?>

  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="tests">

  <h3>Data Sources</h3>

  <table class="wp-list-table widefat fixed">
    <tbody>
      <!-- <tr>
        <td>
			<?php _e( 'Pick a GeoIP database or service to use.', 'geoipsl' ); ?>
        </td>

        <?php
		  $geoip_database_or_service_list = array(
			1 => __( 'GeoLite2 City',              'geoipsl' ),
			2 => __( 'GeoIP2 Country',             'geoipsl' ),
			3 => __( 'GeoIP2 City',                'geoipsl' ),
			4 => __( 'GeoIP2 Precision Country',   'geoipsl' ),
			5 => __( 'GeoIP2 Precision City',      'geoipsl' ),
			6 => __( 'GeoIP2 Precision Insights',  'geoipsl' ),
		  );
		?>

        <td>
          <select name="geoip_test_database_or_service">
            <?php foreach ( $geoip_database_or_service_list as $id => $text ) { ?>
                <option value="<?php echo esc_attr( $id ); ?>"  <?php selected( $geoip_test_database_or_service, $id ); ?>><?php echo esc_html( $text ); ?></option>
            <?php } unset( $id, $text ); ?>
          </select>
        </td>
      </tr> -->

      <tr class="alternate">
        <td>
			<?php _e( 'Input an IP to check for testing desktop redirects.', 'geoipsl' ); ?>
        </td>
        <td>
          <input value="<?php echo esc_attr( $geoip_test_ip ); ?>" name="geoip_test_ip" placeholder=" IP">
        </td>
      </tr>

      <tr>
        <td>
			<?php _e( 'Input starting point coordinates for testing mobile redirects.', 'geoipsl' ); ?>
        </td>
        <td>
          <input value="<?php echo ( $test_mobile_coords_from ) ? $test_mobile_coords_from : ''; ?>" name="test_mobile_coords_from" placeholder=" Latitude, Longitude">
        </td>
      </tr>

      <!-- <tr class="alternate">
        <td>
			<?php _e( 'Input destination point coordinates, each pair being separated by a new line. Otherwise, coordinates from your actual site options will be used as destination points.', 'geoipsl' ); ?>
        </td>
        <td><textarea name="test_coords_to" placeholder=" Latitude, Longitude"><?php echo $test_coords_to; ?></textarea></td>
      </tr> -->
    </tbody>
  </table>

  <br>

	<?php submit_button( __( 'Reset', 'geoipsl' ), 'secondary', 'geoipsl_clear_test', false ); ?>
	<?php submit_button( __( 'Save for Debugging', 'geoipsl' ), 'primary', 'geoipsl_save_debug', false ); ?>

  <h3>Test Cases</h3>

  <table class="wp-list-table widefat fixed">
    <tbody>
      <tr>
        <td>
			<?php
			$geoipsl_test_case = array(
							''																										=> __( 'Select a test case to execute.',																							'geoipsl' ),
			  // 'geoipsl_test_geocode_my_ip'                          => __( 'Covert IP to geolocation information. ',                                      'geoipsl' ),
			  // 'geoipsl_test_reverse_geocode_coords'                 => __( 'Reverse geocode starting point coordinates.',                                 'geoipsl' ),
			  'geoipsl_test_ip_to_destination_coords'               => __( 'Calculate distance of IP to available destination coordinates.',              'geoipsl' ),
			  'geoipsl_test_starting_coords_to_destination_coords'  => __( 'Calculate distance of starting coords to available destination coordinates.', 'geoipsl' ),
			);
			?>
          <select name="geoipsl_test_case">
            <?php
			foreach ( $geoipsl_test_case as $id => $text ) { ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, geoipsl_array_value( $_REQUEST, 'geoipsl_test_case', '' ) ); ?>><?php echo esc_attr( $text ); ?></option>
				<?php }
			  unset( $id, $text );
			?>
          </select>
			<?php submit_button( __( 'Test', 'geoipsl' ), 'secondary', 'geoipsl_execute_test', false ); ?>
        </td>
      </tr>
    </tbody>
  </table>

  <br>

<?php if ( 'geoipsl_execute_test' == geoipsl_array_value( $_REQUEST, 'action', '' ) ) {
	switch ( geoipsl_array_value( $_REQUEST, 'geoipsl_test_case', '' ) ) {
		case 'geoipsl_test_geocode_my_ip':
			require_once( GEOIPSL_PLUGIN_DIR . 'admin/edit-settings-tests-geocode-ip.php' );
			break;
		case 'geoipsl_test_reverse_geocode_coords':
			require_once( GEOIPSL_PLUGIN_DIR . 'admin/edit-settings-tests-reverse-geocode.php' );
			break;
		case 'geoipsl_test_ip_to_destination_coords':
			require_once( GEOIPSL_PLUGIN_DIR . 'admin/edit-settings-tests-ip-distances.php' );
			break;
		case 'geoipsl_test_starting_coords_to_destination_coords':
			require_once( GEOIPSL_PLUGIN_DIR . 'admin/edit-settings-tests-coords-distances.php' );
			break;
		default:
			break;
	}
} ?>
</form>
