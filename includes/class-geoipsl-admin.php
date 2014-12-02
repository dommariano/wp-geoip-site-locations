<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

/**
  * Plugin class for managing the user interface.
  * @since 0.1.0
  */

class Admin extends Settings implements Admin_Settings_Interface {
}