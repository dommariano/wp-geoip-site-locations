<?php
if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

/**
 * Get remaining queries MaxMind web service.
 *
 * @since 0.3.0
 *
 * @param int Integer code for MaxMind service.
 */
function geoipsl_get_remaining_queries( $web_service ) {
	global $geoipsl_settings, $geoipsl_admin_settings;

	$web_service = intval( $web_service );
	$left = '';

	switch ( $web_service ) {
		case 1: // country
			$left = (int) $geoipsl_settings->get( 'country_queries_left' );
	  break;
		case 2: // precision city
			$left = (int) $geoipsl_settings->get( 'city_queries_left' );
	  break;
		case 3:
			$left = (int) $geoipsl_settings->get( 'insights_queries_left' );
	  break;
		default:
	  return 0;
		break;
	}

	return 0 == $left ? '' : $left;
}

function geoipsl_set_maxmind_queries( $web_service, $remaining ) {
	global $geoipsl_settings, $geoipsl_admin_settings;

	if ( ! is_int( $remaining ) ) {
		$remaining = (int) $remaining; }

	if ( ! is_int( $web_service ) ) {
		$web_service = (int) $web_service; }

	switch ( $web_service ) {
		case 1: // country
			$queries = $geoipsl_settings->get( 'country_queries' );
			$geoipsl_settings->set( 'country_queries', ++$queries );

	  return (int) $geoipsl_settings->set( 'country_queries_left', $remaining );
		break;
		case 2: // precision city
			$queries = $geoipsl_settings->get( 'city_queries' );
			$geoipsl_settings->set( 'city_queries', ++$queries );

	  return (int) $geoipsl_settings->set( 'city_queries_left', $remaining );
		break;
		case 3:
			$queries = $geoipsl_settings->get( 'insights_queries' );
			$geoipsl_settings->set( 'insights_queries', ++$queries );

	  return (int) $geoipsl_settings->set( 'insights_queries_left', $remaining );
		break;
	}

	return 0;

}
