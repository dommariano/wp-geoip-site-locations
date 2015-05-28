<form id="geoipsl-settings-databases" action="" method="get">

  <?php wp_nonce_field( 'geoipsl_settings' ); ?>

  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="databases">

  <?php
  // visible table column headers
  $headers = array(
    'check'         => '&nbsp;',
    'database'      => __( 'Database', 'geoipsl' ),
    'used_for'      => __( 'Detects', 'geoipsl' ),
    'last_uploaded' => __( 'Last Downloaded', 'geoipsl' ),
    'src_update'    => __( 'Source Updates In', 'geoipsl' ),
  );

  // disallow display of this data
  $hidden_columns = array(
    'database_name',
    'database_edition',
    'database_zipped',
    'row_actions',
  );

  // data array for the databases table.
  $rows = array();

  // data for the GeoLite2 City database.
  $data = array();

  $data['check'] = '<input type="radio" name="geoip_db" group="geoip_db" value="1">'; //geoip_database
  $data['database'] = 'GeoLite2 City';
  $data['database_name'] = 'GeoLite2-City.mmdb';
  $data['database_zipped'] = 'GeoLite2-City.mmdb.gz';
  $data['row_actions']['download'] = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';

  // delete .mmdb and mmdb.gz, upload, then unzip in that order.
  $data['row_actions']['upload'] = add_query_arg( array(
    'page' => 'geoip-site-locations/geoip-site-locations.php/',
    'tab' => 'databases',
    'tab-content' => 'databases-upload'
  ), network_admin_url( 'admin.php' ) );

  // if the mmdb file or the zipped file exists, enable deletion.
  if ( geoipsl_data_files_exist( array(
    $data['database_name'],
    $data['database_zipped'],
  ) ) ) {
    $files = array();

    if ( geoipsl_data_files_exist( $data['database_name'] ) )
      $files[] = $data['database_name'];

    if ( geoipsl_data_files_exist( $data['database_zipped'] ) )
      $files[] = $data['database_zipped'];

    $data['row_actions']['delete'] = add_query_arg( array(
      'page' => 'geoip-site-locations/geoip-site-locations.php/',
      'tab' => 'databases',
      'tab-content' => 'databases-delete',
      'files' => json_encode( $files ),
    ), network_admin_url( 'admin.php' ) );
  }

  // if the zipped file exists, enable unzipping.
  // delete mmdb file first before unzipping.
  if ( geoipsl_data_files_exist( $data['database_zipped'] ) ) {
    $data['row_actions']['unzip'] = add_query_arg( array(
      'page' => 'geoip-site-locations/geoip-site-locations.php/',
      'tab' => 'databases',
      'tab-content' => 'databases-unzip',
      'files' => array( $data['database_zipped'] ),
    ), network_admin_url( 'admin.php' ) );
  }

  $data['database'] .= geoipsl_row_actions( $data['row_actions'] );
  $data['used_for'] = wpautop( __( 'country, subdivisions, city, postal code, latitude, longitude', 'geoipsl' ) );
  $data['last_uploaded'] = geoipsl_last_uploaded('GeoLite2-City.mmdb' );
  $data['src_update'] = date( 'd M Y ( D )',  geoipsl_next_schedule_update_for_geolite2_city() );

  // data for GeoIP2 Country
  $rows[] = $data;
  $data = array();

  $data['check'] = '<input type="radio" name="geoip_db" group="geoip_db"  value="2">';
  $data['database'] = 'GeoIP2 Country';
  $data['database'] .= sprintf( wpautop( __( '%sRequires purchase from MaxMind.%s', 'geoipsl' ) ), '<span style="color: #d46f15">', '</span>' );
  $data['database_name'] = 'GeoIP2-Country.mmdb';
  $data['database_zipped'] = 'GeoIP2-Country.mmdb.gz';
  $data['database_edition'] = 'GeoIP2-Country';

  $data['row_actions']['upload'] = add_query_arg( array(
    'page' => 'geoip-site-locations/geoip-site-locations.php/',
    'tab' => 'databases',
    'tab-content' => 'databases-upload'
  ), network_admin_url( 'admin.php' ) );

  // if the mmdb file or the zipped file exists, enable deletion.
  if ( geoipsl_data_files_exist( array(
    $data['database_name'],
    $data['database_zipped'],
  ) ) ) {
    $files = array();

    if ( geoipsl_data_files_exist( $data['database_name'] ) )
      $files[] = $data['database_name'];

    if ( geoipsl_data_files_exist( $data['database_zipped'] ) )
      $files[] = $data['database_zipped'];

    $data['row_actions']['delete'] = add_query_arg( array(
      'page' => 'geoip-site-locations/geoip-site-locations.php/',
      'tab' => 'databases',
      'tab-content' => 'databases-delete',
      'files' => json_encode( $files ),
    ), network_admin_url( 'admin.php' ) );
  }

  // if the zipped file exists, enable unzipping.
  // delete mmdb file first before unzipping.
  if ( geoipsl_data_files_exist( $data['database_zipped'] ) ) {
    $data['row_actions']['unzip'] = add_query_arg( array(
      'page' => 'geoip-site-locations/geoip-site-locations.php/',
      'tab' => 'databases',
      'tab-content' => 'databases-unzip',
      'files' => array( $data['database_zipped'] ),
    ), network_admin_url( 'admin.php' ) );
  }

  $data['database'] .= geoipsl_row_actions( $data['row_actions'] );
  $data['used_for'] = wpautop( __( 'country', 'geoipsl' ) );
  $data['last_uploaded'] = geoipsl_last_uploaded( 'GeoIP2-Country.mmdb' );
  $data['src_update'] = date( 'd M Y ( D )', geoipsl_get_next_day_of_week( 'Tuesday' ) );

  // data for GeoIP2 City
  $rows[] = $data;
  $data = array();

  $data['check'] = '<input type="radio" name="geoip_db" group="geoip_db"  value="3">';
  $data['database'] = 'GeoIP2 City';
  $data['database'] .= sprintf( wpautop(  __( '%sRequires purchase from MaxMind.%s', 'geoipsl' ) ), '<span style="color: #d46f15">', '</span>' );
  $data['database_name'] = 'GeoIP2-City.mmdb';
  $data['database_zipped'] = 'GeoIP2-City.mmdb.gz';
  $data['database_edition'] = 'GeoIP2-City';

  $data['row_actions']['upload'] = add_query_arg( array(
    'page' => 'geoip-site-locations/geoip-site-locations.php/',
    'tab' => 'databases',
    'tab-content' => 'databases-upload'
  ), network_admin_url( 'admin.php' ) );

  // if the mmdb file or the zipped file exists, enable deletion.
  if ( geoipsl_data_files_exist( array(
    $data['database_name'],
    $data['database_zipped'],
  ) ) ) {
    $files = array();

    if ( geoipsl_data_files_exist( $data['database_name'] ) )
      $files[] = $data['database_name'];

    if ( geoipsl_data_files_exist( $data['database_zipped'] ) )
      $files[] = $data['database_zipped'];

    $data['row_actions']['delete'] = add_query_arg( array(
      'page' => 'geoip-site-locations/geoip-site-locations.php/',
      'tab' => 'databases',
      'tab-content' => 'databases-delete',
      'files' => json_encode( $files ),
    ), network_admin_url( 'admin.php' ) );
  }

  // if the zipped file exists, enable unzipping.
  // delete mmdb file first before unzipping.
  if ( geoipsl_data_files_exist( $data['database_zipped'] ) ) {
    $data['row_actions']['unzip'] = add_query_arg( array(
      'page' => 'geoip-site-locations/geoip-site-locations.php/',
      'tab' => 'databases',
      'tab-content' => 'databases-unzip',
      'files' => array( $data['database_zipped'] ),
    ), network_admin_url( 'admin.php' ) );
  }

  $data[ 'database' ]      .= geoipsl_row_actions( $data['row_actions'] );
  $data[ 'used_for' ]       = wpautop( __( 'country, subdivisions, city, postal code, latitude, longitude', 'geoipsl' ) );
  $data[ 'last_uploaded' ]  = geoipsl_last_uploaded('GeoIP2-City.mmdb' );
  $data[ 'src_update' ]     = date( 'd M Y ( D )', geoipsl_get_next_day_of_week( 'Tuesday' ) );

  // data for GeoIP2 City
  $rows[] = $data;
  unset( $data );

  foreach ( $rows as $index => $row ) {
    $value = preg_match( "/value\=\"(\d+)\"/i", $row['check'], $matches );
    $value = isset( $matches[1] ) ? $matches[1] : 1;
    $value = abs( $value );

    $default = $geoipsl_admin_settings->get('geoip_db');
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
            if ( in_array( $column, $hidden_columns ) ) {
             continue;
            }

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
