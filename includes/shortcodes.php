<?php
/**
 * The pluggable WordPress shortcodes for end-user usage.
 *
 * @since 0.4.0
 */

add_shortcode( 'geoipsl_remember_last_site', 'geoipsl_remember_last_site_form' );
function geoipsl_remember_last_site_form( $atts ) {
  global $geoipsl_admin_settings;
  global $geoipsl_settings;

  $args = shortcode_atts( array(
    'class' => ''
  ), $atts );

  extract( $args );

  $output  = '<input ';
  $output .= 'type="checkbox" ';
  $output .= 'name="geoipsl-record-last-visit" ';
  $output .= 'id="geoipsl-record-last-visit" ';
  $output .= sprintf( 'class="%s" ', esc_attr( $class ) );
  $output .= '>';

  $before  = '<form ';
  $before .= 'class="geoipsl-record-last-visit-form" ';
  $before .= 'name="geoipsl-record-last-visit-form" ';
  $before .= '>';

  $before  = apply_filters( 'geoipsl_remember_last_site_before', $before );

  $after   = '<label ';
  $after  .= 'for="geoipsl-record-last-visit">';
  $after  .= __( 'Remember this location', 'geoipsl' );
  $after  .= '</label>';
  $after  .= '</form>';

  $after  = apply_filters( 'geoipsl_remember_last_site_after', $after );

  $output = $before . $output . $after;

  if ( 'none' == $geoipsl_settings->get( 'visitor_tracking' ) ) {
    $output  = 'The GeoIPSL plugin is not configured to read cookies from ';
    $output .= 'this site. Please update your settings.';

    $output  = __( $output, 'geoipsl' );
  }

  return $output;
}

add_shortcode( 'geoipsl_suggest_closest_site', 'geoipsl_suggest_closest_site' );
function geoipsl_suggest_closest_site( $atts ) {
  $before  = '<a href="" class="geoipsl-closest-site-link">';
  $after   = '</a>';
  $after  .= '<script type="text/javascript" src="' . trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoipsl-nearest-site.js' . '"></script>';

  $output = $before . __( 'Closest Site', 'geoipsl' ) . $after;

  return $output;
}
