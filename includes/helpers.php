<?php
if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

add_action( 'all_admin_notices', 'geoipsl_admin_notices_on_fresh_install' );

/**
 * Prefix a given string with the defined plugin prefix.
 *
 * @since 0.1.0
 *
 * @param string $string The string to be prefixed.
 * @return string The prefixed string.
 */
function geoipsl( $string ) {
  if ( !  is_string( $string ) )
    return false;

  // ensure string is fit for use as array key
  $string = sanitize_key( $string );

  return GEOIPSL_PREFIX . $string;
}

/**
 * Generate row actions HTML code.
 *
 * @since 0.3.0
 *
 * @param array $actions Array of classes mapped to URLs.
 * @return string $output HTML code of row actions.
 */
function geoipsl_row_actions( $actions ) {
  if ( ! is_array( $actions ) || empty( $actions ) )
    return '';

  $output = '<div class="row-actions">';
  $count  = 0;
  $len    = count( $actions );

  foreach( $actions as $key => $url ) {
    $count++;
    $output .= sprintf( '<span class="%s"><span class="%s"><a href="%s">%s</a></span></span>', esc_attr( $key ), esc_attr( $key ), esc_url( $url ), ucwords( $key ) );

    if ( $count < $len ) {
      $output .= " | ";
    }
  }

  $output .= '</div>';

  return $output;
}

/**
 * Fetch an instance of a WP_List_Table class.
 *
 * Taken from the get_list_table() function WordPress.
 *
 * @since 0.1.0
 *
 * @param string $class The type of the list table, which is the class name.
 * @param array $args Optional. Arguments to pass to the class.
 *   Accepts 'screen'.
 * @return object | bool Object on success, false if the class does not exist.
 */
function geoipsl_get_list_table( $class, $args = array() ) {
  $list_table_classes = array(
    'GeoIPSL\Sites_List_Table' => 'sites',
  );

  if ( isset( $list_table_classes[ $class ] ) ) {
    foreach ( (array) $list_table_classes[ $class ] as $required ) {
      require_once( GEOIPSL_PLUGIN_DIR . 'includes/class-geoipsl-' . $required .
        '-list-table.php' );
    }

    if ( isset( $args['screen'] ) )
      $args['screen'] = convert_to_screen( $args['screen'] );
    elseif ( isset( $GLOBALS['hook_suffix'] ) )
      $args['screen'] = get_current_screen();
    else
      $args['screen'] = null;

    return new $class( $args );
  }

  return false;
}

/**
 * Echoes the return value of wpautop
 *
 * @since 0.1.0
 * @todo Remove this. Use sprintf() instead with <p> as parameter.
 *
 * @param string $string  The text to be formatted.
 * @param bool $br  Preserve line breaks. When set to true, any line breaks
 *        remaining after paragraph conversion are converted to HTML <br />.
 *        Line breaks within script and style sections are not affected.
 * @return void
 */
function geoipsl_wpautop_e( $string, $br = true ) {
  echo wpautop( $string, $br );
}
