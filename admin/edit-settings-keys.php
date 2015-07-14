<form id="geoipsl-settings-keys" action="" method="">

  <?php wp_nonce_field( 'geoipsl_settings' ); ?>

  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="keys">

  <h3><?php _e( 'MaxMind Account Info' ); ?></h3>
  <p><?php _e( 'Please supply your MaxMind License Key in order to use MaxMind Web Services.' ); ?></p>

  <table class="wp-list-table widefat fixed">
    <tbody>
      <tr>
        <td width="100"><?php _e( 'User ID' ); ?></td>
        <td><input type="text" name="maxmind_user_id" placeholder="User ID" value="<?php echo esc_attr( $geoipsl_settings->get( 'maxmind_user_id' ) ); ?>"></td>
      </tr>

      <tr>
        <td width="100"><?php _e( 'License Key' ); ?></td>
        <td><input type="text" name="maxmind_license_key" placeholder="License Key" value="<?php echo esc_attr( $geoipsl_settings->get( 'maxmind_license_key' ) ); ?>"></td>
      </tr>
    </tbody>
  </table>

  <h3><?php _e( 'Google Reverse Geo-Coding API' ); ?></h3>
  <p><?php _e( 'You may optionally specify your API Key for use in Google Reverse GeoCoding API. This API is only used on the admin area for turning the latitudes and longitudes you provided into a human-readable address.' ); ?></p>

  <table class="wp-list-table widefat fixed">
    <tbody>
      <tr>
        <td width="180"><?php _e( 'Client ID' ); ?></td>
        <td><input type="text" name="google_grgc_api_key" placeholder="API Key" value="<?php echo esc_attr( $geoipsl_settings->get( 'google_grgc_api_key' ) ); ?>"></td>
      </tr>
  </table>

  <br>

	<?php submit_button( __( 'Save', 'geoipsl' ), 'primary', 'geoipsl_save_api_keys', false ); ?>
	<?php submit_button( __( 'Clear and Save', 'geoipsl' ), 'secondary', 'geoipsl_clear_api_keys', false ); ?>
</form>
