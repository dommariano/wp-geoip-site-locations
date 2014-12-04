<form id="geoipsl-settings-databases" action="" method="get">
  <?php wp_nonce_field( 'geoipsl_settings' ); ?>
  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="databases">

  <?php
    $headers = array(
      'check'                   =>    '&nbsp;',
      'database'                => __( 'Database', 'geoipsl'),
      'used_for'                => __( 'Detects', 'geoipsl'),
      'last_downloaded'         => __( 'Last Downloaded', 'geoipsl'),
      'next_scheduled_download' => __( 'Next Download', 'geoipsl'),
      'next_source_update'      => __( 'Source Updates In', 'geoipsl'),
    );

    $rows = array(
      array(
        'check'                   => '<input type="radio" name="geoip_db" group="geoip_db" value="1">', //geoip_database
        'database'                => sprintf( __( 'GeoLite2 City', 'geoipsl' ), '<b style="color: #d46f15;">', '</b>' ),
        'used_for'                => wpautop( __( 'country, subdivisions, city, postal code, latitude, longitude', 'geoipsl' ) ),
        'last_downloaded'         => ( file_exists( geoipsl_get_file_path( 'GeoLite2-City.mmdb' ) ) ) ? date ( 'd M Y ( D )', filemtime( geoipsl_get_file_path( 'GeoLite2-City.mmdb' ) ) ) : __( 'Never.', 'geoipsl' ),
        'next_scheduled_download' => get_option( geoipsl_prefix_string( 'geolite2_city_sched_download' ), 0 ),
        'next_source_update'      => date( 'd M Y ( D )', geoipsl_next_schedule_update_for_geolite2_city( 'Tuesday' ) ),
      ),
      array(
        'check'                   => '<input type="radio" name="geoip_db" group="geoip_db"  value="2">',
        'database'                => sprintf( __( '%sGeoIP2 Country%s', 'geoipsl' ), '<b><a target="_blank" href="'. esc_attr('https://www.maxmind.com/en/country') .'">', '</a></b><br>' ) .
                                     sprintf( __( '%sRequires purchase from MaxMind.%s', 'geoipsl' ), '<span style="color: #d46f15">', '</span>' ),
        'used_for'                => wpautop( __( 'country', 'geoipsl' ) ),
        'last_downloaded'         => ( file_exists( geoipsl_get_file_path( 'GeoIP2-Country.mmdb' ) ) ) ? date ( 'd M Y ( D )', filemtime( geoipsl_get_file_path( 'GeoLite2-City.mmdb' ) ) ) : __( 'Never.', 'geoipsl' ),
        'next_scheduled_download' => get_option( geoipsl_prefix_string( 'geoip2_country_sched_download' ), 0 ),
        'next_source_update'      => date( 'd M Y ( D )', geoipsl_get_next_day_of_week( 'Tuesday' ) ),
      ),
      array(
        'check'                   => '<input type="radio" name="geoip_db" group="geoip_db"  value="3">',
        'database'                => sprintf( __( '%sGeoIP2 City%s', 'geoipsl' ), '<b><a target="_blank" href="'. esc_attr('https://www.maxmind.com/en/city') .'">', '</a></b><br>' ) .
                                    sprintf( __( '%sRequires purchase from MaxMind.%s', 'geoipsl' ), '<span style="color: #d46f15">', '</span>' ),
        'used_for'                => wpautop( __( 'country, subdivisions, city, postal code, latitude, longitude', 'geoipsl' ) ),
        'last_downloaded'         => ( file_exists( geoipsl_get_file_path( 'GeoIP2-City.mmdb' ) ) ) ? date ( 'd M Y ( D )', filemtime( geoipsl_get_file_path( 'GeoLite2-City.mmdb' ) ) ) : __( 'Never.', 'geoipsl' ),
        'next_scheduled_download' => get_option( geoipsl_prefix_string( 'geoip2_city_sched_download' ), 0 ),
        'next_source_update'      => date( 'd M Y ( D )', geoipsl_get_next_day_of_week( 'Tuesday' ) ),
      )
    );

    foreach ( $rows as $index => $row ) {
      $value = preg_match( "/value\=\"(\d+)\"/i", $row['check'], $matches );
      $value = isset( $matches[1] ) ? $matches[1] : 1;
      $value = abs( $value );

      $default = $geoipsl_admin_settings->get_geoip_db();
      $default = ( '' == $default ) ? 1 : $default;

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

  <?php submit_button( __( 'Update', 'geoipsl' ), 'primary', 'geoipsl_save_database', false ); ?>
</form>