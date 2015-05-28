<?php
if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

/**
 * Get number of active locations.
 *
 * @since 0.3.0
 *
 * @return int Number of active locations.
 * @todo Re-implement this function to retrieve count directly from the
 * database. For performance issues.
 */
function geoipsl_get_active_loc_count() {

  $activated_locations = get_option( geoipsl( 'activated_locations' ), array() );

  return count( $activated_locations );
}

/**
 * Activate a given GeoIP location.
 *
 * @since 0.1.0
 *
 * @param string $geolocation_id The string to be prefixed.
 * @return array $activated_locations Array of activated locations.
 * @todo Save the accents instead of removing them.
 */
function geoipsl_activate_location( $blog_id, array $location ) {

  if ( ! is_int( $blog_id ) )
    $blog_id = 0;

  $activated_locations = get_option( geoipsl( 'activated_locations' ) );

  if ( ! is_array( $activated_locations ) ) {
    $activated_locations = array();
  }

  // do not store if a duplicate is found
  if ( ! in_array( $blog_id, $activated_locations ) ) {
    $activated_locations[ $blog_id ] = $location;

    update_option( geoipsl( 'activated_locations' ), $activated_locations );

    return $activated_locations;
  }

  return $activated_locations;
}

/**
 * Deactivate a given GeoIP location.
 *
 * @since 0.1.0
 *
 * @param string $geolocation_id The string to be prefixed.
 * @return bool|int Boolean FALSE if current user is not allowed to do this or
 * interger number of active locations on success.
 */
function geoipsl_deactivate_location( $blog_id ) {

  if ( ! is_int( $blog_id ) ) {
    throw new InvalidArgumentException( 'deactivate_location expects $blog_id
       to be integer, ' . gettype( $blog_id ) . ' given.' );
  }

  $activated_locations = get_option( geoipsl( 'activated_locations' ) );

  if ( ! is_array( $activated_locations ) ) {
    $activated_locations = array();
  }

  // remove array in the list of active locations
  if ( array_key_exists( $blog_id, $activated_locations ) ) {
    unset ( $activated_locations[ $blog_id ] );
    update_option( geoipsl( 'activated_locations' ), $activated_locations );
  }

  return get_option( geoipsl( 'activated_locations' ) );
}
