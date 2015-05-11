<?php

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

function geoipsl_array_value( $array, $key, $default = '' ) {
  return ( isset( $array[ $key ] ) ) ? $array[ $key ] : $default;
}

function geoipsl_post_value( $key, $default = '' ) {
  return geoipsl_array_value( $_POST, $key, $default );
}

function geoipsl_request_value( $key, $default = '', $blacklist = NULL, $filter = NULL ) {
  global $geoipsl_settings;

  $request = geoipsl_array_value( $_REQUEST, $key, $default );

  if ( ! is_null( $filter ) ) {
    $request = call_user_func( $filter, $request );
  }

  if ( ! is_null( $blacklist ) ) {
    $request = ( $blacklist == $request ) ? '' : $request;
  }

  return ( $request ) ? $request : $geoipsl_settings->get( $key );
}

function geoipsl_request_or_saved_value( $key, $default = '', $blacklist = NULL, $filter = NULL ) {
  global $geoipsl_settings;

  $request = geoipsl_array_value( $_REQUEST, $key, $default );

  if ( ! is_null( $filter ) ) {
    $request = call_user_func( $filter, $request );
  }

  if ( ! is_null( $blacklist ) ) {
    $request = ( $blacklist == $request ) ? '' : $request;
  }

  return ( $request ) ? $request : $geoipsl_settings->get( $key );
}
