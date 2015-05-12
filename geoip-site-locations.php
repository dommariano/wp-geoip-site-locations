<?php

error_reporting( E_ALL );

/*
Plugin Name: GeoIP Site Locations
Description: Detect user location based on IP or cookie information and redirect
to the appropriate geo-targetted version of your site.
Version: 0.3.0
Author: Dominique Mariano
Author URI: http://www.twitter.com/miniQueue
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: geoipsl
Domain Path: /languages
*/

/*
Copyright 2014  Dominique Mariano ( dominique.acpal.mariano@gmail.com )

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

define( 'GEOIPSL_PLUGIN_NAME', plugin_basename( __FILE__ ) );
define( 'GEOIPSL_PREFIX', 'geoipsl_' );
define( 'GEOIPSL_PLUGIN_VERSION', '0.1.0' );
define( 'GEOIPSL_DATABASE_VERSION', 1 );
define( 'GEOIPSL_MINIMUM_WP_VERSION', '3.9.2' );
define( 'GEOIPSL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GEOIPSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GEOIPSL_OFF_STATUS','off' );
define( 'GEOIPSL_ON_STATUS', 'on' );
define( 'GEOIPSL_DISTANCE_LIMIT', 1 );
define( 'GEOIPSL_PERSISTENCE_INTERVAL', 0 );
define( 'GEOIPSL_INVALID_IP', -1 );
define( 'GEOIPSL_RESERVED_IP', -2 );
define( 'GEOIPSL_INVALID_TEST_DATABASE_OR_SERVICE', -1 );
define( 'GEOIPSL_INVALID_TEST_COORDINATE', 'invalid_coordinate' );
define( 'GEOIPSL_MAYBE_PROXY', 'maybe_proxy' );
define( 'GEOIPSL_CRON_JOBS', 1 );

require_once( GEOIPSL_PLUGIN_DIR . 'vendor/autoload.php' );
require_once( GEOIPSL_PLUGIN_DIR . 'includes/shortcodes.php' );

register_activation_hook(   __FILE__, array( 'GeoIPSL\Site_Locations', 'maybe_deactivate' ) );
register_activation_hook(   __FILE__, array( 'GeoIPSL\Site_Locations', 'maybe_update'     ) );
register_deactivation_hook( __FILE__, array( 'GeoIPSL\Site_Locations', 'maybe_uninstall'  ) );
add_action( 'init', array( 'GeoIPSL\Site_Locations', 'init' ) );
