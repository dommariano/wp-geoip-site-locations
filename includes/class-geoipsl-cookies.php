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
    return geoipsl_array_value( $_COOKIE, 'wp_geoipsl_tracker', '' );
  }

  public static function parse_tracking_cookie( $wp_geoipsl ) {
    return $wp_geoipsl = json_decode( stripslashes( $wp_geoipsl ), true );
  }
}
