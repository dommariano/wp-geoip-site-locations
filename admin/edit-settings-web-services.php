<form id="geoipsl-settings-web-services" action="" method="get">
  <?php wp_nonce_field( 'geoipsl_settings' ); ?>
  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="web-services">

  <?php
    $headers = array(
      'check'                   =>    '&nbsp;',
      'web_servide'             => __( 'Service', 'geoipsl'),
      'used_for'                => __( 'Detects', 'geoipsl'),
      'queries'                 => __( 'Queries', 'geoipsl'),
      'queries_left'            => __( 'Queries Left', 'geoipsl'),
    );

    $rows = array(
      array(
        'check'                 => '<input type="radio" name="geoip_web_service" group="geoip_web_service" value="1">', //geoip-database
        'web_servide'           => sprintf( __( 'GeoIP2 Precision Country', 'geoipsl' ), '<b style="color: #d46f15;">', '</b>' ),
        'used_for'              => wpautop( __( 'country', 'geoipsl' ) ),
        'queries'               => 250000,
        'queries_left'          => 250000,
      ),
      array(
        'check'                 => '<input type="radio" name="geoip_web_service" group="geoip_web_service" value="2">', //geoip-database
        'web_servide'           => sprintf( __( 'GeoIP2 Precision City', 'geoipsl' ), '<b style="color: #d46f15;">', '</b>' ),
        'used_for'              => wpautop( __( 'country, subdivisions, city, postal code, latitude, longitude', 'geoipsl' ) ),
        'queries'               => 250000,
        'queries_left'          => 250000,
      ),
      array(
        'check'                 => '<input type="radio" name="geoip_web_service" group="geoip_web_service" value="3">', //geoip-database
        'web_servide'           => sprintf( __( 'GeoIP2 Precision Insights', 'geoipsl' ), '<b style="color: #d46f15;">', '</b>' ),
        'used_for'              => wpautop( __( 'country, subdivisions, city, postal code, latitude, longitude, accuracy radius', 'geoipsl' ) ),
        'queries'               => 250000,
        'queries_left'          => 250000,
      ),
    );

    foreach ( $rows as $index => $row ) {
      $value = preg_match( "/value\=\"(\d+)\"/i", $row['check'], $matches );
      $value = isset( $matches[1] ) ? $matches[1] : '';

      $default = $geoipsl_admin_settings->get_geoip_web_service();

      if ( $value == $default ) {
        $rows[ $index ]['check'] = preg_replace( "/\>$/", 'checked="checked" >', $row['check'] );
      }
    }
  ?>

  <table class="wp-list-table widefat fixed geoipsl-settings-databases">
    <?php
      for ($table_head = 0; $table_head < 2; $table_head++ ) {
        $tag = ( 0 == $table_head ) ? 'thead' : 'tfoot';
        echo "<$tag><tr>";
        foreach ( $headers as $header => $text ) {  ?>
          <th scope="row" class="<?php echo $header . '-column'; ?>">
            <?php echo $text; ?>
          </th>
        <?php }
        echo "<tr></$tag>";
      }
    ?>

    <tbody>
      <?php foreach ( $rows as $row_index => $row ) { //class="check-column" ?>
        <tr  class="<?php echo ( $row_index % 2 ) ? 'alternate' : ''; ?>">
          <?php foreach ( $row as $column => $text ) {
            $tag = ( 'check' == $column ) ? 'th' : 'td';
            echo "<$tag scope=\"row\" class=\"$column" . "-column\"" . ">$text</$tag>";
          } ?>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <br>

  <?php submit_button( __( 'Update', 'geoipsl' ), 'primary', 'geoipsl_save_web_service', false ); ?>
  <?php submit_button( __( 'Clear and Save', 'geoipsl' ), 'secondary', 'geoipsl_clear_web_service', false ); ?>
</form>