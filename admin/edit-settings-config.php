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

            <!-- <label for="redirect_status">
              <input name="redirect_status" type="checkbox" id="redirect_status" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_admin_settings->get( 'redirect_status' ), GEOIPSL_ON_STATUS ); ?>>
              <?php _e( 'Redirect users to apppropriate subsite when they visit site home page.', 'geoipsl' ); ?>
            </label>
            <br> -->

            <label for="redirect_after_load_status">
              <input name="redirect_after_load_status" type="checkbox" id="redirect_after_load_status" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_admin_settings->get( 'redirect_after_load_status' ), GEOIPSL_ON_STATUS ); ?>>
              <?php _e( 'Load the site first and then redirect immedietly as soon as it can.', 'geoipsl' ); ?>
            </label>
            <br>

            <label for="persistent_redirect_status">
              <input name="persistent_redirect_status" type="checkbox" id="persistent_redirect_status" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_admin_settings->get( 'persistent_redirect_status' ), GEOIPSL_ON_STATUS ); ?> >
              <?php _e( 'Allow persistent redirect for repeat visitors.', 'geoipsl' ); ?>
              <?php
                $geoipsl_persistent_interval_select = array(
                  0       => __( 'all the time',  'geoipsl' ),
                  3600    => __( 'every hour',    'geoipsl' ),
                  86400   => __( 'every day',     'geoipsl' ),
                  604800  => __( 'every week',    'geoipsl' ),
                  2592000 => __( 'every 30 days', 'geoipsl' ),
                );
              ?>

              <!-- <select name="persistence_interval" id="persistence_interval" class="postform">
                <?php
                  foreach ( $geoipsl_persistent_interval_select as $value => $text ) {
                    printf( '<option value="%s" %s >%s</option>', esc_attr( $value ), selected( $value, $geoipsl_admin_settings->get( 'persistence_interval' ) ), esc_attr( $text ) );
                  }
                ?>
              </select> -->
            </label>
            <br>

            <label for="query_proxies_status">
              <input name="query_proxies_status" type="checkbox" id="query_proxies_status" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_admin_settings->get( 'query_proxies_status' ), GEOIPSL_ON_STATUS ); ?>>
              <?php _e( 'Allow redirection when source IP is detectable proxy.', 'geoipsl' ); ?>
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
        <th scope="row"><?php _e( 'Theme integration', 'geoipsl' ); ?></th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><span><?php _e( 'Location chooser', 'geoipsl' ); ?></span></legend>
              <label for="lightbox_as_location_chooser_status">
                <input name="lightbox_as_location_chooser_status" type="checkbox" id="lightbox_as_location_chooser_status" value=<?php printf( '"%s"', GEOIPSL_ON_STATUS ); ?> <?php echo checked( $geoipsl_admin_settings->get( 'lightbox_as_location_chooser_status' ), GEOIPSL_ON_STATUS ); ?> >
                <?php _e( 'Use a lightbox as location chooser when we cannot decide which site to serve to the visitor.', 'geoipsl' ); ?>
              </label>
              <br>
              <label for="lightbox_trigger_element">
                <input name="lightbox_trigger_element" type="text" id="lightbox_trigger_element" value="<?php echo esc_attr( $geoipsl_admin_settings->get( 'lightbox_trigger_element' ) ); ?>">
                <?php _e( 'Specify trigger element selector.', 'geoipsl' ); ?>
              </label>
              <br>

              <p class="description"><?php _e( 'Requires theme implementation of your own lightbox.', 'geoipsl' ); ?></p>

            </fieldset>
          </td>
      </tr>

    </tbody>
  </table>

  <br>

  <?php submit_button( __( 'Save', 'geoipsl' ), 'primary', 'geoipsl_config_save', false ); ?>
</form>
