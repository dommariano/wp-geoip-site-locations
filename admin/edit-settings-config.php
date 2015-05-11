<form id="geoipsl-settings-keys" action="" method="">

  <?php wp_nonce_field( 'geoipsl_settings' ); ?>

  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php">
  <input type="hidden" name="tab" value="config">

  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row"><?php _e( 'Redirect settings', 'geoipsl' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e( 'Redirect settings', 'geoipsl' ); ?></span></legend>

            <label for="use_geoip_detection">
              <input name="use_geoip_detection" type="checkbox" id="use_geoip_detection" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_admin_settings->get( 'use_geoip_detection' ), GEOIPSL_ON_STATUS ); ?> >
              <?php _e( 'Detect visitor location using Geo-IP information and redirect accordingly.', 'geoipsl' ); ?>
            </label>
            <br>

            <label for="remember_last_served_site">
              <input name="remember_last_served_site" type="checkbox" id="remember_last_served_site" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_admin_settings->get( 'remember_last_served_site' ), GEOIPSL_ON_STATUS ); ?> >
              <?php _e( 'Remember the last served site for repeat visitors and redirect accordingly.', 'geoipsl' ); ?>
            </label>
            <br>

            <label for="query_proxies_status">
              <input name="query_proxies_status" type="checkbox" id="query_proxies_status" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_settings->get( 'query_proxies_status' ), GEOIPSL_ON_STATUS ); ?>>
              <?php _e( 'Allow Geo-IP redirects even if visitor is behind an obvious proxy.', 'geoipsl' ); ?>

            </label>
            <br>

          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Accuracy settings', 'geoipsl' ); ?></th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><span><?php _e( 'Distance logic', 'geoipsl' ); ?></span></legend>
              <label for="distance_limit">
                <?php _e( 'Serve nearest site within', 'geoipsl' ); ?>
                <input name="distance_limit" type="number" id="distance_limit" value="<?php echo esc_attr( $geoipsl_admin_settings->get( 'distance_limit' ) ); ?>" min="1" max="1000">
                <?php _e( 'kilometer(s) of visitor location.', 'geoipsl' ); ?>
              </label>
              <br>

            </fieldset>
          </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Theme integration<br><span style="color: #d46f15">(Deprecated)</span>', 'geoipsl' ); ?></th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><span><?php _e( 'Location chooser', 'geoipsl' ); ?></span></legend>
              <label for="lightbox_trigger_element">
                <input name="lightbox_trigger_element" type="text" id="lightbox_trigger_element" value="<?php echo esc_attr( $geoipsl_admin_settings->get( 'lightbox_trigger_element' ) ); ?>">
                <?php _e( 'Specify trigger element selector.', 'geoipsl' ); ?>
              </label>
              <br>

              <p class="description"><?php _e( 'Requires theme implementation of your own lightbox. This feature is now deprecated and will be removed in later versions of this plugin.', 'geoipsl' ); ?></p>

            </fieldset>
          </td>
      </tr>

    </tbody>
  </table>

  <br>

  <?php submit_button( __( 'Save', 'geoipsl' ), 'primary', 'geoipsl_config_save', false ); ?>
</form>
