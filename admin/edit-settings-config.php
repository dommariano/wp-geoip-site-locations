<form id="geoipsl-settings-keys" action="" method="">

  <?php wp_nonce_field( 'geoipsl_settings' ); ?>

  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php">
  <input type="hidden" name="tab" value="config">

  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row"><?php _e( 'Redirects', 'geoipsl' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e( 'Redirects', 'geoipsl' ); ?></span></legend>

            <label for="use_geoip_detection">
              <input name="use_geolocation" type="radio" id="use_geoip_detection" value="use_geoip_detection" <?php echo checked( $geoipsl_settings->get( 'use_geolocation' ), GEOIPSL_ON_STATUS ); ?> >
              <?php _e( 'Estimate visitor location using retrieved IP address then serve the matching site.', 'geoipsl' ); ?>
            </label>
            <br>

            <label for="use_geoip_detection">
              <input name="use_geolocation" type="radio" id="use_geoip_detection" value="use_h5_geolocation" <?php echo checked( $geoipsl_settings->get( 'use_geolocation' ), 'use_h5_geolocation' ); ?> >
              <?php _e( 'Use HTML5 Geolocation API and then redirect to matching site as soon as page is served.', 'geoipsl' ); ?>
            </label>
            <br>

            <label for="use_manual_selection">
              <input name="use_geolocation" type="radio" id="use_manual_selection" value="use_manual_selection" <?php echo checked( $geoipsl_settings->get( 'use_geolocation' ), 'use_manual_selection' ); ?> >
              <?php _e( 'Do not estimate user location or which site to serve. Let the visitor decide.', 'geoipsl' ); ?>
            </label>
            <br>

            <p class="description">IP-based location detection will automatically fall back to HTML5-based geolocation on mobile devices.</p>
            <br>

            <label for="query_proxies_status">
              <input name="query_proxies_status" type="checkbox" id="query_proxies_status" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_settings->get( 'query_proxies_status' ), GEOIPSL_ON_STATUS ); ?>>
              <?php _e( 'When using IP-based detection, allow redirects even if visitor is behind an obvious proxy.', 'geoipsl' ); ?>
            </label>
            <br>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Cache', 'geoipsl' ); ?></th>
        <td>
          <fieldset>
            <label for="remember_last_served_site">
              <input name="geoipsl_cache_settings"  type="radio" id="remember_last_served_site" value="no_cache" <?php echo checked( $geoipsl_admin_settings->get( 'geoipsl_cache_settings' ), 'no_cache' ); ?> >
              <?php _e( 'Disable all caches. Do not track visitors.', 'geoipsl' ); ?>
            </label>
            <br>

            <label for="remember_last_served_site">
              <input name="geoipsl_cache_settings"  type="radio" id="remember_last_served_site" value="remember_last_served_site" <?php echo checked( $geoipsl_admin_settings->get( 'geoipsl_cache_settings' ), 'remember_last_served_site' ); ?> >
              <?php _e( 'Remember the last served site for return visitors and redirect accordingly.', 'geoipsl' ); ?>
            </label>
            <br>

            <label for="monitor_served_sites">
              <input name="geoipsl_cache_settings"  type="radio" id="monitor_served_sites" value="monitor_served_sites" <?php echo checked( $geoipsl_admin_settings->get( 'geoipsl_cache_settings' ), 'monitor_served_sites' ); ?> >
              <?php _e( 'Monitor user browsing behaviour of your subsites and calculate or suggest which site to remember. <span style="color: #d46f15">( experimental )</span>', 'geoipsl' ); ?>
            </label>
            <br>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Accuracy', 'geoipsl' ); ?></th>
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
        <th scope="row"><?php _e( 'Theme integration<br><span style="color: #d46f15">( deprecated )</span>', 'geoipsl' ); ?></th>
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
