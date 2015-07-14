<?php
/**
 * The pluggable WordPress shortcodes for end-user usage.
 *
 * @since 0.4.0
 */

add_shortcode( 'geoipsl_remember_me', 'geoipsl_remember_me_form' );
function geoipsl_remember_me_form( $atts ) {
	global $geoipsl_admin_settings;
	global $geoipsl_settings;

	$args = shortcode_atts( array(
		'class' => '',
	), $atts );

	extract( $args );

	$output  = '<input ';
	$output .= 'type="checkbox" ';
	$output .= 'name="geoipsl-remember-me" ';
	$output .= 'id="geoipsl-remember-me" ';
	$output .= sprintf( 'class="%s" ', esc_attr( $class ) );
	$output .= '>';

	$before  = '<form ';
	$before .= 'class="geoipsl-remember-me-form" ';
	$before .= 'name="geoipsl-remember-me-form" ';
	$before .= '>';

	$before  = apply_filters( 'geoipsl_remember_me_before', $before );

	$after   = '<label ';
	$after  .= 'for="geoipsl-remember-me">';
	$after  .= __( 'Remember this location', 'geoipsl' );
	$after  .= '</label>';
	$after  .= '</form>';

	$after  = apply_filters( 'geoipsl_remember_me_after', $after );

	$output = $before . $output . $after;

	if ( 'none' == $geoipsl_settings->get( 'visitor_tracking' ) ) {
		$output  = 'The GeoIPSL plugin is not configured to read cookies from ';
		$output .= 'this site. Please update your settings.';

		$output  = __( $output, 'geoipsl' );
	}

	if ( 'write' == $geoipsl_settings->get( 'visitor_tracking' ) ) {
		$output  = 'The GeoIPSL plugin is configured to automatically write cookies';
		$output .= ' from the server. Visitor input will be ignored.';

		$output  = __( $output, 'geoipsl' );
	}

	return $output;
}

add_shortcode( 'geoipsl_suggest_closest_site', 'geoipsl_suggest_closest_site' );
function geoipsl_suggest_closest_site( $atts ) {

	$args = shortcode_atts( array(
		'label' => __( 'Closest Site', 'geoipsl' ),
	), $atts );

	extract( $args );

	$before  = '<a href="" class="geoipsl-closest-site-link">';
	$after   = '</a>';
	$after  .= '<script type="text/javascript" src="' . trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/geoipsl-nearest-site.js' . '"></script>';

	$output = $before . $label . $after;

	return $output;
}
