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
<label for="use_geolocation_ip">
<input name="use_geolocation" type="radio" id="use_geolocation_ip" value="ip"<?php echo checked( $geoipsl_settings->get( 'use_geolocation' ), 'ip' ); ?> >
<?php _e( 'Estimate visitor location using retrieved IP address then serve the matching site.', 'geoipsl' ); ?>
</label>
<br>

<label for="use_geolocation_h5">
<input name="use_geolocation" type="radio" id="use_geolocation_h5" value="h5"<?php echo checked( $geoipsl_settings->get( 'use_geolocation' ), 'h5' ); ?> >
<?php _e( 'Use HTML5 Geolocation API and then redirect to matching site as soon as page is served.', 'geoipsl' ); ?>
</label>
<br>

<label for="use_geolocation_manual">
<input name="use_geolocation" type="radio" id="use_geolocation_manual" value="manual" <?php echo checked( $geoipsl_settings->get( 'use_geolocation' ), 'manual' ); ?> >
<?php _e( 'Do not estimate user location or which site to serve. Let the visitor decide.', 'geoipsl' ); ?>
</label>
<br>
<p class="description"><?php _e( 'IP-based location detection will automatically fall back to HTML5-based geolocation on mobile devices.', 'geoipsl' ); ?></p>
</fieldset>
</td>

</tr>
<tr>
<th scope="row"><?php _e( 'Proxies', 'geoipsl' ); ?></th>
<td>
<fieldset>
<label for="query_proxies_status">
<input name="query_proxies_status" type="checkbox" id="query_proxies_status" value=<?php printf( '"%s"', 'on' ); ?><?php echo checked( $geoipsl_settings->get( 'query_proxies_status' ), 'on' ); ?>>
<?php _e( 'When using IP-based detection, allow redirects even if visitor is behind an obvious proxy.', 'geoipsl' ); ?>
</label>
<br>
</fieldset>
</td>

</tr>
<tr>
<th scope="row"><?php _e( 'Tracking', 'geoipsl' ); ?></th>
<td>
<fieldset>
<label for="no_tracking">
<input name="visitor_tracking"  type="radio" id="no_tracking" value="none" <?php echo checked( $geoipsl_admin_settings->get( 'visitor_tracking' ), 'none' ); ?> >
<?php _e( 'Do not track visitors.', 'geoipsl' ); ?>
</label>
<br>

<label for="remember_last_served_site">
<input name="visitor_tracking"  type="radio" id="remember_last_served_site" value="last" <?php echo checked( $geoipsl_admin_settings->get( 'visitor_tracking' ), 'last' ); ?> >
<?php _e( 'Remember the last served site for return visitors and serve it instead.', 'geoipsl' ); ?>
</label>
<br>

<label for="monitor_served_sites">
<input name="visitor_tracking"  type="radio" id="monitor_served_sites" value="suggest" <?php echo checked( $geoipsl_admin_settings->get( 'visitor_tracking' ), 'suggest' ); ?> >
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
<input name="distance_limit" type="number" id="distance_limit" value="<?php echo esc_attr( $geoipsl_admin_settings->get( 'distance_limit' ) ); ?>" min="1" max="10000">
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
