<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

class Distance {
  /**
    *
    * Calculate the great circle distance between a two coordinates on the
    * surface of a sphere the size of Earth.
    *
    * This works for small distances. At long lines however, it is better to
    * use Vicenty's formula.
    *
    * @since 0.1.0
    * @see http://en.wikipedia.org/wiki/Haversine_formula
    * @see http://www.movable-type.co.uk/scripts/latlong.html
    *
    * @param float $latitude1 The latitude of the first coordinate.
    * @param float $longitude1 The longitude of the first coordinate.
    * @param float $latitude2 The latitude of the second coordinate.
    * @param float $longitude2 The longitude of the second coordinate.
    * @return float The great circle distance between the two coordinates given.
    */
  public static function great_circle_distance( $latitude1, $longitude1, $latitude2, $longitude2 ) {
    $R = 6378.137; // Equatorial radius of the Earth

    $latitude1              = deg2rad( $latitude1 );
    $longitude1             = deg2rad( $longitude1 );
    $latitude2              = deg2rad( $latitude2 );
    $longitude2             = deg2rad( $longitude2 );
    $haversine_lats         = pow( ( sin( $latitude1 - $latitude2 ) / 2 ), 2 );
    $haversine_longs        = pow( ( sin( $longitude1 - $longitude2 ) / 2 ), 2 );
    $haversine              = ( $haversine_lats + ( cos( $latitude1 ) * cos( $latitude2 ) * $haversine_longs ) );

    // $haversine must be in this range [0,1]
    if ( $haversine < 0 || $haversine > 1 ) {
      return 0;
    }

    return 2 * $R * asin( sqrt( $haversine ) ) * pow( 10, 3 );
  }

  /**
    *
    * Calculate the geodesic ( in meters ) between two coordinates on the
    * surface of a spheroid the size of Earth using Vicenty's formula.
    *
    * This function is a PHP implementation of the reverse geodesic problem
    * @see http://en.wikipedia.org/wiki/Vincenty%27s_formulae.
    * At nearly antipodal points, this algorithm will iterate more than 1000
    * times to converge. At actual antipodal points, however,
    * we fail to converge at all.
    *
    * @since 0.1.0
    * @see http://geographiclib.sourceforge.net/geod-addenda.html
    * @todo Implement faster Newtons Method as described by Karney.
    *
    * @param float $latitude1 The latitude of the first coordinate.
    * @param float $longitude1 The longitude of the first coordinate.
    * @param float $latitude2 The latitude of the second coordinate.
    * @param float $longitude2 The longitude of the second coordinate.
    * @return float | int The geodesic between the two coordinates given.
    * Int -1 when we have antipodal points. Int -2 when the algorithm fails to
    * converge.
    */
  public static function geodesic( $latitude1, $longitude1, $latitude2, $longitude2 ) {

    // Convert all inputs to radians.
    $latitude1              = deg2rad( floatval( $latitude1  ) );
    $longitude1             = deg2rad( floatval( $longitude1 ) );
    $latitude2              = deg2rad( floatval( $latitude2  ) );
    $longitude2             = deg2rad( floatval( $longitude2 ) );

    $a        = 6378137.0;                                  // length of semi-major axis of the ellipsoid (radius at equator)
    $f        = 1/298.257223563;                            // flattening of the ellipsoid
    $b        = ( 1 - $f ) * $a;                            // length of semi-minor axis of the ellipsoid (radius at the poles);
    $U_1      = atan( ( 1 - $f ) * tan( $latitude1 ) );     // reduced latitude (latitude on the auxiliary sphere)
    $U_2      = atan( ( 1 - $f ) * tan( $latitude2 ) );     // reduced latitude (latitude on the auxiliary sphere)
    $L        = $longitude2 - $longitude1;                  // difference in longitude of two points
    $a_1      = 0;                                          // forward azimuths at the points;
    $a_2      = 0;                                          // azimuth at the equator
    $s        = 0;                                          // ellipsoidal distance between the two points
    $o        = 0;                                          // arc length between points on the auxiliary sphere
    $Y        = $L;                                         // initial value until $Y converges
    $Y_delta  = 0;                                          // change in $Y for every iteration
    $i        = 0;                                          // number of iterations so we don't get stuck in an infinite loop

    // $Y has to converge in at most 10000 iterations
    do {
      $sin_o    = sqrt( pow( cos( $U_2 ) * sin( $Y ), 2 ) + pow( cos( $U_1 ) * sin( $U_2 ) - sin( $U_1 ) * cos( $U_2 ) * cos( $Y ), 2 ) );
      $cos_o    = sin( $U_1 ) * sin( $U_2 ) + cos( $U_1 ) * cos( $U_2 ) * cos( $Y );
      $o        = atan2( $sin_o, $cos_o );

      // to and from the same place
      if ( 0 == sin( $sin_o ) ) {
        return 0;
      }

      $sin_a    = ( cos( $U_1 ) * cos( $U_2 ) * sin( $Y ) ) / sin( $sin_o );
      $cos_2_a  = 1 - pow( $sin_a , 2 );

      if ( 0 == $cos_2_a ) {
        return -1; // the algorithm failed to converge, e.g. at (0,0) to (0,180) antipodal points
      }

      $cos_2_om = $cos_o - ( ( 2 * sin( $U_1 ) * sin( $U_2 ) ) / $cos_2_a );
      $C        = ( $f / 16 ) * $cos_2_a * ( 4 + ( $f * ( 4 - ( 3 * $cos_2_a )  ) ) );
      $Y_delta  = $Y;
      $Y        = $L + ( ( 1 - $C ) * $f * $sin_a * ( $o + $C * $sin_o * ( $cos_2_om + $C * $cos_o * ( -1 + 2 * pow( $cos_2_om, 2 ) ) ) ) );
    } while ( abs( $Y - $Y_delta ) > pow( 10, -12 ) && ++$i <= 1000 );

    if ( $i > 10000 ) {
      return -2; // the algorithm failed to converge
    }

    $u_2        = $cos_2_a * ( ( pow( $a, 2 ) - pow( $b, 2 ) )  / pow( $b, 2 ) );

    // Vicenty's modification to simplify A and B instead of using the longer original polynomials of the algorithm
    $k_1        = ( sqrt( 1 + $u_2 ) - 1 ) / ( sqrt( 1 + $u_2 ) + 1 );
    $A          = ( 1 + ( 0.25 * pow( $k_1, 2 ) ) ) / ( 1 - $k_1 );
    $B          = $k_1 * ( 1 - ( 3 / 8 ) * pow( $k_1, 2 ) );

    $delta_o    = $B * $sin_o * ( $cos_2_om + 0.25 * $B * ( $cos_o * ( -1 + 2 * pow( $cos_2_om, 2 ) ) - ( 1 / 6 ) * $B * $cos_2_om * ( -3 + 4 * pow( $sin_o, 2 ) ) * ( -3 + 4 * pow( $cos_2_om, 2 ) ) ) );
    $s          = $b * $A * abs( $o - $delta_o );

    return $s;
  }

  /**
    * Determine the site closest to the retrieved IP,
    * given the coordinate supplied for the subsite.
    *
    * @since 0.1.0
    *
    * @param float $lat_from Latitude.
    * @param float $long_from Longitude.
    * @return int $closest_site
    */
  public static function get_closest_site( $lat_from, $long_from, $limit = 100000 ) {

    $lat_from = floatval( $lat_from );
    $long_from = floatval( $long_from );

    $site_locations = get_option( geoipsl( 'activated_locations' ), array() );
    $site_distances = array();
    $closest_sites = array( 1 );

    if ( empty( $site_locations ) )
      return $closest_sites;

    foreach ( $site_locations as $blog_id => $blog_location ) {
      $lat_to  = (float) $blog_location['latitude'];
      $long_to = (float) $blog_location['longitude'];

      $site_distances[ $blog_id ] = self::geodesic( $lat_from, $long_from, $lat_to, $long_to );
    }

    // if the lowest distance from us is above a certain limit
    if ( min( $site_distances ) >= $limit ) {
      return $closest_sites;
    }

    $closest_sites = array_keys( $site_distances, min( $site_distances ) );

    unset( $site_locations, $site_distances, $blog_id, $blog_location );

    return $closest_sites; // equidistant sites
  }
}
