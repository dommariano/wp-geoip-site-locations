<?php

if ( ! is_multisite() ) {
  wp_die( __( 'Multisite support is not enabled.' ) );
}

if ( ! current_user_can( 'manage_sites' ) ) {
  wp_die( __( 'You do not have sufficient permissions to edit this site.' ) );
}

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
  wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( ! can_edit_network( $details->site_id ) ) {
  wp_die( __( 'You do not have permission to access this page.' ) );
}

$parsed = parse_url( $details->siteurl );
$is_main_site = is_main_site( $id );

$location_defaults = array(
  'latitude'      => '',
  'longitude'     => '',
  'street_number' => '',
  'street_name'   => '',
  'city'          => '',
  'city_district' => '',
  'postal_code'   => '',
  'county'        => '',
  'county_code'   => '',
  'region'        => '',
  'region_code'   => '',
  'country'       => '',
  'country_code'  => '',
  'timezone'      => '',
);
$location = get_option( geoipsl_prefix_string( 'activated_locations' ), array() );
$location = isset( $location[ $id ] ) ? $location[ $id ] : array();

foreach ( $location_defaults as $key => $value ) {
  if ( isset( $_REQUEST['location'][ $key ] ) ) {
    $location_defaults[ $key ] = $_REQUEST['location'][ $key ];
  }
}

unset( $key, $value );

$location = wp_parse_args( $location, $location_defaults );

$is_loc_set = false;
if ( isset( $_REQUEST['location']['latitude'] ) && isset( $_REQUEST['location']['longitude'] ) ) {
  $is_loc_set = ( ! empty( $_REQUEST['location']['latitude'] ) && ! empty( $_REQUEST['location']['longitude'] ) ) ? true : false;
}
$is_loc_saved = ( ! empty( $location['latitude'] ) && ! empty( $location['longitude'] ) ) ? true : false;
?>

<form method="get" action="">
  <?php wp_nonce_field( 'geoipsl_settings' ); ?>
  <input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="sites">
  <input type="hidden" name="tab-content" value="site-info">

  <?php
    foreach ( $location as $key => $value ) {
      printf( '<input type="hidden" name="%s" value="%s">', esc_attr( 'location[' . $key . ']' ), esc_attr( $value ) );
    }
    unset( $key, $value );
  ?>

  <table class="form-table">
    <tbody>
      <tr class="form-field form-required">
        <th scope="row"><?php _e( 'Domain' ) ?></th>
        <td><code><?php echo $parsed['scheme'] . '://' . esc_attr( $details->domain ) ?></code></td>
      </tr>
      <tr class="form-field">
        <th scope="row"><?php _e( 'Latitude' ) ?></th>
        <td><input name="location[latitude]" type="text" id="latitude" value="<?php echo esc_attr( $location['latitude'] ); ?>" size="33" <?php echo $is_loc_saved ? 'disabled="disabled"' : ''; ?> /></td>
      </tr>
      <tr class="form-field">
        <th scope="row"><?php _e( 'Longitude' ) ?></th>
        <td><input name="location[longitude]" type="text" id="longitude" value="<?php echo esc_attr( $location['longitude'] ); ?>" size="33" <?php echo $is_loc_saved ? 'disabled="disabled"' : ''; ?> /></td>
      </tr>
      <?php
        foreach ( $location as $key => $value ) {
          if ( in_array( $key, array( 'latitude', 'longitude' ) ) )
            continue;

          if ( empty( $value ) )
            continue;

          ?>
          <tr class="form-field">
            <th scope="row"><?php echo ucwords( str_replace( '_', ' ', $key ) ); ?></th>
            <td><?php echo esc_attr( $value ) ?></td>
          </tr>
          <?php
        }
      ?>
    </tbody>
  </table>

  <?php if ( $is_loc_set ) submit_button( __( 'Save', 'geoipsl' ), 'primary', 'geoipsl_site_info_save', false ); ?>
  <?php if ( ! $is_loc_saved ) submit_button( __( 'Reverse Geocode', 'geoipsl' ), 'secondary', 'geoipsl_site_info_reverse_geocode', false ); ?>
  <?php submit_button( __( 'Clear', 'geoipsl' ), 'secondary', 'geoipsl_site_info_clear_and_save', false ); ?>
</form>