<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

/**
  * GeoIPSL\Cookie class is for reading, writing and interpreting tracking coo-
  * kie information to/from the browser.
  *
  * The cookie information that this class reads and writes is about the brow-
  * sing behavior of the site visitor. This tracking information contains only
  * the site ID of the visited site.
  *
  * Previously, the tracking information is much more complex and is coded
  * in the following format:
  *
  * [size]-[limit]-[firstAccessTime].[codedAccessTime]-[siteID]-[pageID]
  *
  * When version 0.1.0 of this plugin was developed, the design is to track
  * site visits and record the information on the cookie. This is the purpose
  * of the [size] and [limit]. However, testing on the live server revealed
  * problems on this approach. While the plugin correctly tracks the last 30
  * visits on the staging server, this does not happen on the live server with
  * caching turned on.
  *
  * The new approach now is to record just one access information on the
  * cookie, which keeps the cookie size small. The user can browse though any
  * subsite or subdomain of the WordPress install and the cookie information
  * will not be updated. However, if the visitor leaves the site and the last
  * subsite visited is not the one recorded on the cookie, the visitor will be
  * prompted if the browser should "remember" this new information instead.
  *
  * Next time the visitor visits the site entry point ( e.g., the root site ),
  * the visitor will be redirected to this new "remembered" site, or whatever
  * site is recorded on the cookie.
  *
  * @package GeoIPSL
  * @author Dominique Mariano <dominique.acpal.mariano@gmail.com>
  *
  * @todo Handle the case in GeoIPSL\Cookie::infer_site_preference() when two
  * or more sites have the same score.
  */
class Cookies {

  /**
   * Get wp_geoipsl cookie value from site visitor.
   *
   * @since 0.1.0
   * @return string
   */
  public static function get_tracking_cookie() {
    return geoipsl_array_value( $_COOKIE, 'wp_geoipsl', '' );
  }

  /**
   * Create wp_geoipsl cookie with visitor browsing behaviour tracking
   * information.
   *
   * @since 0.1.0
   * @todo Handle case for updating limits.
   *
   * @param int $blog_id The current blog id.
   * @param int $limit The number of site visits to track in the wp_geoipsl cookie.
   * @param int $_time_code The time ( UNIX timestamp ) that visitor accessed the site.
   * @return void
   */
  public static function set_location_cookie( $blog_id = 1 ) {

    global $post;

    $blog_id    = abs( $blog_id );
    $wp_geoipsl = str_replace( ' ', '', (string) self::get_tracking_cookie() );
    $domain     = preg_replace( "/^http(s?):\/\//", '', trim( get_site_url( 1 ) ) );

    /**
     * Only record the cookie info first time visitor, then on subsequent
     * visits, the cookie change will be handled by Javascript on the client
     * side. This should allow for more complicated tracking without compro-
     * mising the cookie size.
     */
    if ( '' == $wp_geoipsl ) {
      $time_code  = $_time_code;
      $wp_geoipsl = $blog_id;
    }

    setcookie( 'wp_geoipsl', $wp_geoipsl, 0, '/', sprintf( ".%s", $domain ) );
  }
