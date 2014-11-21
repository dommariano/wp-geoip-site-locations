<form id="geoipsl-settings-sources" action="" method="get">
  <?php wp_nonce_field( 'update_data_files' ); ?>
  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/sources">
  <input type="hidden" name="tab" value="sources">


  <table class="wp-list-table widefat fixed geoipsl-settings-databases">
    <thead>
      <tr>
        <th><?php _e( 'Database', 'geoipsl'); ?></th>
        <th><?php _e( 'File Size', 'geoipsl'); ?></th>
        <th><?php _e( 'Last Updated', 'geoipsl'); ?></th>
        <th><?php _e( 'Next Update', 'geoipsl'); ?></th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <th><?php _e( 'Database', 'geoipsl'); ?></th>
        <th><?php _e( 'File Size', 'geoipsl'); ?></th>
        <th><?php _e( 'Last Updated', 'geoipsl'); ?></th>
        <th><?php _e( 'Next Update', 'geoipsl'); ?></th>
      </tr>
    </tfoot>

    <tbody>
      <tr class="alternate">
        <td>
          <b><?php _e( 'Countries, Regions, Cities and Postal Areas', 'geoipsl' ); ?></b>
        </td>

        <td>18.5 MB</td>

        <td><?php echo time(); ?></td>

        <td><b><?php _e( 'Rarely.', 'geoipsl' ); ?></b></td>
      </tr>
    </tbody>

  </table>

  <br>

  <?php submit_button( __( 'Update and Synchronize', 'geoipsl' ), 'primary', 'synchronize_with_maxmind_geoip_city_list', false ); ?>
</form>