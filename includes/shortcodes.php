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

  if ( 'on' != $geoipsl_settings->get( 'persistent_redirect_status' ) ) {
    $output  = 'The GeoIPSL plugin is not configured to read cookies from ';
    $output .= 'this site. Please update your settings.';

    $output  = __( $output, 'geoipsl' );
  }

  return $output;
}
