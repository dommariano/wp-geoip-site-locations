<?php

use Ivory\GoogleMap;
use Geocoder\HttpAdapter\BuzzHttpAdapter;
use Geocoder\Geocoder;
use Geocoder\Provider\GoogleMapsProvider;

global $geoipsl_settings;
global $geoipsl_admin_settings;

include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
// this screen is only for users who are administrators or at least users who can manage options
if ( ! current_user_can( 'manage_options' ) )
  wp_die( __( 'Cheatin&#8217; uh?' ) );

// set the page default tab
$current_tab = 'sites';

// if user specified a tab to view, use it
if ( isset( $_REQUEST['tab'] ) && in_array( $_REQUEST['tab'] , array( 'sites', 'databases', 'web-services', 'keys', 'tests', 'config' ) ) ) {
  $current_tab = $_REQUEST['tab'];
}

// any alternate content other than the default
if ( isset( $_REQUEST['tab-content'] ) && in_array( $_REQUEST['tab-content'] , array( 'site-info', 'databases-upload', 'databases-delete', 'databases-unzip' ) ) ) {
  $current_content = $_REQUEST['tab-content'];
}

// we return to this file after every submit
$parent_file = 'admin.php?page=geoip-site-locations/geoip-site-locations.php/';

// our navigation tabs
$navigation_tabs = array(
  array(
    'href'  => add_query_arg( array( 'tab' => 'sites' ) , admin_url( $parent_file ) ),
    'class' => 'nav-tab ' . ( ( 'sites' == $current_tab ) ? 'nav-tab-active' : '' ),
    'text'  => __( 'Sites', 'geoipsl' ),
  ),

  array(
    'href'  => add_query_arg( array( 'tab' => 'databases' ) , admin_url( $parent_file ) ),
    'class' => 'nav-tab ' . ( ( 'databases' == $current_tab ) ? 'nav-tab-active' : '' ),
    'text'  => __( 'Databases', 'geoipsl' ),
  ),

  array(
    'href'  => add_query_arg( array( 'tab' => 'web-services' ) ,admin_url( $parent_file ) ),
    'class' => 'nav-tab ' . ( ( 'web-services' == $current_tab ) ? 'nav-tab-active' : '' ),
    'text'  => __( 'Web Services', 'geoipsl' ),
  ),

  array(
    'href'  => add_query_arg( array( 'tab' => 'keys' ) , admin_url( $parent_file ) ),
    'class' => 'nav-tab ' . ( ( 'keys' == $current_tab ) ? 'nav-tab-active' : '' ),
    'text'  => __( 'API Keys', 'geoipsl' ),
  ),

  array(
    'href'  => add_query_arg( array( 'tab' => 'config' ) , admin_url( $parent_file ) ),
    'class' => 'nav-tab ' . ( ( 'config' == $current_tab ) ? 'nav-tab-active' : '' ),
    'text'  => __( 'Config', 'geoipsl' ),
  ),

  array(
    'href'  => add_query_arg( array( 'tab' => 'tests' ) , admin_url( $parent_file ) ),
    'class' => 'nav-tab ' . ( ( 'tests' == $current_tab ) ? 'nav-tab-active' : '' ),
    'text'  => __( 'Tests', 'geoipsl' ),
  ),
);

$actions = array(
  'geoipsl_save_database',
  'geoipsl_save_api_keys',
  'geoipsl_save_web_service',
  'geoipsl_clear_web_service',
  'geoipsl_clear_api_keys',
  'geoipsl_execute_test',
  'geoipsl_clear_test',
  'geoipsl_save_debug',
  'geoipsl_site_info_save',
  'geoipsl_site_info_reverse_geocode',
  'geoipsl_site_info_clear_and_save',
  'geoipsl_config_save',
  'geoipsl_upload_mmdb_zip',
  'geoipsl_delete_mmdb_zip',
  'geoipsl_cancel_del_mmdb_zip',
);

// set the action to the first encountered action
$do_action = '';
foreach ( $actions as $action_ ) {
 if ( isset( $_REQUEST[ $action_ ] ) ) {
    $do_action = $action_;
    break;
  }
}

// if action is present, check if none is valid and user is coming from an admin screen
if ( $do_action ) {

  $really = check_admin_referer( 'geoipsl_settings' );

  // construct the send back url
  $send_back = remove_query_arg( array_keys( $actions ), wp_get_referer( ) );

  if ( ! $send_back ) {
    $send_back = self_admin_url( 'admin.php?page=geoip-site-locations/geoip-site-locations.php/' );
  }

  switch ( $do_action ) {
    case 'geoipsl_cancel_del_mmdb_zip':
      $query_args = array();

      $query_args[] = 'tab-content';

      $send_back = remove_query_arg( $query_args, $send_back );

      break;
    case 'geoipsl_delete_mmdb_zip':
      $files = isset( $_REQUEST['files'] ) ? $_REQUEST['files'] : array();
      $deleted = array();
      $failed = array();

      if ( ! is_array( $files ) ) {
        $files = (array) $files;
      }

      foreach ( $files as $file ) {
        if ( unlink( geoipsl_get_file_path( $file, 'data' ) ) ) {
          $deleted[] = $file;
        } else {
          $failed[] = $file;
        }
      }

      unset( $files, $file );

      $query_args = array();

      $query_args['deleted'] = implode( ',', $deleted );
      $query_args['not_deleted'] = implode( ',', $failed );

      $send_back = add_query_arg( $query_args, $send_back );

      $query_args = array();

      $query_args[] = 'tab-content';

      $send_back = remove_query_arg( $query_args, $send_back );

      break;

    case 'geoipsl_upload_mmdb_zip':
      // upload the file to the uploads folder
      $file_upload = new File_Upload_Upgrader( 'mmdbzip', 'package' );

      // the file name without the .gz extension
      $file_name = str_replace( array( '.gz' ), '', $file_upload->filename );

      // acceptable file names to be uploaded
      $limit_files_to = array(
        'GeoIP2-City.mmdb',
        'GeoIP2-Country.mmdb',
        'GeoLite2-City.mmdb',
      );

      $query_args = array();

      if ( in_array( $file_name, $limit_files_to ) ) {
        // extract it to the data/ folder
        geoipsl_unzip_file( $file_name, $file_upload->package );
        $query_args['upload_status'] = 'success';
        $query_args['file'] = $file_upload->filename;
      } else {
        $query_args['upload_status'] = 'error';
        $query_args['file'] = $file_upload->filename;
      }

      // make sure you delete the source file after moving it to the data/ folder
      $file_upload->cleanup();

      $send_back = add_query_arg( $query_args, $send_back );

      break;

    case 'geoipsl_save_database':
      $geoipsl_admin_settings->set_geoip_db( geoipsl_request_value( 'geoip_db', 1 ) );
      break;

    case 'geoipsl_save_api_keys':
      $geoipsl_admin_settings->set_maxmind_user_id( geoipsl_request_value( 'maxmind_user_id' ) );
      $geoipsl_admin_settings->set_maxmind_license_key( geoipsl_request_value( 'maxmind_license_key' ) );
      $geoipsl_admin_settings->set_google_gdm_client_id( geoipsl_request_value( 'google_gdm_client_id' ) );
      $geoipsl_admin_settings->set_google_gdm_client_id_crypto_key( geoipsl_request_value( 'google_gdm_client_id_crypto_key' ) );
      $geoipsl_admin_settings->set_google_grgc_api_key( geoipsl_request_value( 'google_grgc_api_key' ) );
      break;

    case 'geoipsl_save_web_service':
      $geoipsl_admin_settings->set_geoip_web_service( geoipsl_request_value( 'geoip_web_service' ) );
      break;

    case 'geoipsl_clear_web_service':
      $geoipsl_admin_settings->set_geoip_web_service( '' );
      break;

    case 'geoipsl_clear_api_keys':
      $geoipsl_admin_settings->set_maxmind_user_id( '' );
      $geoipsl_admin_settings->set_maxmind_license_key( '' );
      $geoipsl_admin_settings->set_google_gdm_client_id( '' );
      $geoipsl_admin_settings->set_google_gdm_client_id_crypto_key( '' );
      $geoipsl_admin_settings->set_google_grgc_api_key( '' );
      break;

    case 'geoipsl_execute_test':
      $query_args = array();

      if ( isset( $_REQUEST[ 'geoip_test_database_or_service' ] ) ) {
        $option_value = geoipsl_request_value( 'geoip_test_database_or_service', 1 );
        $option_value = in_array( $option_value, array( 1,2,3,4,5,6 ) ) ? $option_value : 1;
        $query_args[ 'geoip_test_database_or_service' ] = $option_value;
      }

      if ( isset( $_REQUEST[ 'geoip_test_ip' ] ) ) {
        $option_value = geoipsl_request_value( 'geoip_test_ip' );
        $option_value = '' != $option_value && GeoIPSL\IP::is_reserved_ipv4( $option_value  ) ? GEOIPSL_INVALID_IP : $option_value;
        $option_value = '' == $option_value || filter_var( $option_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $option_value : GEOIPSL_INVALID_IP;
        $query_args[ 'geoip_test_ip' ] = $option_value;
      }

      if ( isset( $_REQUEST[ 'test_mobile_coords_from' ] ) ) {
        $option_value = geoipsl_request_value( 'test_mobile_coords_from' );

        $option_value = str_replace( ' ', '', $option_value );

        if ( '' == $option_value ) {
          $geoipsl_admin_settings->set_test_mobile_coords_from( $option_value );
        } else {
          $option_values = explode( ',', $option_value );

          if ( 2 != count( $option_values ) && '' != $option_value ) {
            $option_value = GEOIPSL_INVALID_TEST_COORDINATE;
          }

          if ( ! is_numeric( trim( $option_values[0] ) ) ) {
            $option_value = GEOIPSL_INVALID_TEST_COORDINATE;
          }

          if ( ! is_numeric( trim( $option_values[1] ) ) ) {
            $option_value = GEOIPSL_INVALID_TEST_COORDINATE;
          }
        }

        $query_args[ 'test_mobile_coords_from' ] = $option_value;
      }

      if ( isset( $_REQUEST[ 'test_coords_to' ] ) ) {
        $option_value = str_replace( ' ', '', geoipsl_request_value( 'test_coords_to' ) );

        if ( '' != $option_value ) {
          $option_values = explode( "\n", $option_value );

          foreach ( $option_values as $coordinate_pair ) {

            $coordinate_pair = str_replace( ' ', '', $coordinate_pair );
            $coordinate_pair = explode( ",", $coordinate_pair );

            if ( 0 != count( $coordinate_pair ) % 2 && '' != $coordinate_pair ) {
              $option_value = GEOIPSL_INVALID_TEST_COORDINATE;
              break;
            }

            if ( ! is_numeric( trim( $coordinate_pair[0] ) ) ) {
              $option_value = GEOIPSL_INVALID_TEST_COORDINATE;
              break;
            }

            if ( ! is_numeric( trim( $coordinate_pair[1] ) ) ) {
              $option_value = GEOIPSL_INVALID_TEST_COORDINATE;
              break;
            }
          }
        }

        $query_args[ 'test_coords_to' ] = base64_encode( $option_value );
      }

      if ( isset( $_REQUEST['geoipsl_test_case'] ) ) {
              $query_args['geoipsl_test_case'] = $_REQUEST['geoipsl_test_case'];
      }

      $query_args['action'] = 'geoipsl_execute_test';

      $send_back = add_query_arg( $query_args, $send_back );
      break;

    case 'geoipsl_save_debug':
      $query_args = array();

      $geoipsl_admin_settings->set_geoip_test_status( 'on' );

      if ( isset( $_REQUEST[ 'geoip_test_database_or_service' ] ) ) {
        // validate database/webservice choice
        $option_value = isset( $_REQUEST[ 'geoip_test_database_or_service' ] ) ? $_REQUEST[ 'geoip_test_database_or_service' ] : '';
        $option_value = in_array( $option_value, array( 1,2,3,4,5,6 ) ) ? $option_value : 1;
        $query_args[ 'geoip_test_database_or_service' ] = $option_value;
        $geoipsl_admin_settings->set_geoip_test_database_or_service( (int) $option_value );
      }

      if ( isset( $_REQUEST[ 'geoip_test_ip' ] ) ) {
        // validate IP
        $option_value = isset( $_REQUEST[ 'geoip_test_ip' ] ) ? $_REQUEST[ 'geoip_test_ip' ] : '';
        $option_value = GeoIPSL\IP::is_reserved_ipv4( $option_value  ) ? GEOIPSL_RESERVED_IP : $option_value;
        $option_value = '' == $option_value || filter_var( $option_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $option_value : GEOIPSL_INVALID_IP;
        $query_args[ 'geoip_test_ip' ] = $option_value;

        if ( ! in_array( $option_value, array( GEOIPSL_RESERVED_IP, GEOIPSL_INVALID_IP ) ) ) {
          $geoipsl_admin_settings->set_geoip_test_ip( $option_value );
        }
      }

      if ( isset( $_REQUEST[ 'test_mobile_coords_from' ] ) ) {
        $option_value = isset( $_REQUEST[ 'test_mobile_coords_from' ] ) ? $_REQUEST[ 'test_mobile_coords_from' ] : '';
        $geoipsl_admin_settings->set_test_mobile_coords_from( $option_value );
        $query_args[ 'test_mobile_coords_from' ] = $option_value;
      }

      if ( isset( $_REQUEST[ 'test_coords_to' ] ) ) {
        $option_value = isset( $_REQUEST[ 'test_coords_to' ] ) ? $_REQUEST[ 'test_coords_to' ] : '';
        $geoipsl_admin_settings->set_test_coords_to( $option_value );
        $query_args[ 'test_coords_to' ] = $option_value;
      }

      $send_back = add_query_arg( $query_args, $send_back );
      break;

    case 'geoipsl_clear_test':
      $query_args = array();

      $geoipsl_admin_settings->set_geoip_test_status( 'off' );
      $query_args[] = 'geoip_test_on';

      $geoipsl_admin_settings->set_geoip_test_database_or_service( 1 );
      $query_args[] = 'geoip_test_database_or_service';

      $geoipsl_admin_settings->set_geoip_test_ip( '' );
      $query_args[] = 'geoip_test_ip';

      $geoipsl_admin_settings->set_test_mobile_coords_from( '' );
      $query_args[] = 'test_mobile_coords_from';

      $geoipsl_admin_settings->set_test_coords_to( '' );
      $query_args[] = 'test_coords_to';

      $send_back = remove_query_arg( $query_args, $send_back );
      break;

    case 'geoipsl_site_info_save':
      $blog_id  = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
      $location = isset( $_REQUEST['location'] ) ? $_REQUEST['location'] : null;

      // temporary solution. we will need to create a separate table
      // to account for extremely large multi-site setups ( millions of separate WordPress blogs )
      geoipsl_activate_location( (int) $blog_id, (array) $location );

      $send_back = remove_query_arg( array( 'location' ), $send_back );
      break;

    case 'geoipsl_site_info_reverse_geocode':
      $query_args = array();
      $location = isset( $_REQUEST['location'] ) ? $_REQUEST['location'] : array();

      if ( ! $location )
        break;

      if ( isset( $_REQUEST['location']['latitude'] ) ) {
        $latitude = $_REQUEST['location']['latitude'];
      }

      if ( isset( $_REQUEST['location']['longitude'] ) ) {
        $longitude = $_REQUEST['location']['longitude'];
      }

      $adapter = new BuzzHttpAdapter();
      $geocoder = new Geocoder();
      $geocoder->registerProviders( array(
        new GoogleMapsProvider( $adapter, 'en_US' ),
      ) );
      $geocode = $geocoder->reverse( $latitude, $longitude );

      $query_args[ 'location' ][ 'latitude'      ] = $latitude;
      $query_args[ 'location' ][ 'longitude'     ] = $longitude;
      $query_args[ 'location' ][ 'street_number' ] = $geocode->getStreetNumber();
      $query_args[ 'location' ][ 'street_name'   ] = $geocode->getStreetName();
      $query_args[ 'location' ][ 'city'          ] = $geocode->getCity();
      $query_args[ 'location' ][ 'city_district' ] = $geocode->getCityDistrict();
      $query_args[ 'location' ][ 'postal_code'   ] = $geocode->getZipcode();
      $query_args[ 'location' ][ 'county'        ] = $geocode->getCounty();
      $query_args[ 'location' ][ 'county_code'   ] = $geocode->getCountyCode();
      $query_args[ 'location' ][ 'region'        ] = $geocode->getRegion();
      $query_args[ 'location' ][ 'region_code'   ] = $geocode->getRegionCode();
      $query_args[ 'location' ][ 'timezone'      ] = $geocode->getTimezone();
      $query_args[ 'location' ][ 'country'       ] = $geocode->getCountry();
      $query_args[ 'location' ][ 'country_code'  ] = $geocode->getCountryCode();

      $query_args[ 'location' ] = array_map( 'urlencode', $query_args[ 'location' ] );

      $send_back = add_query_arg( $query_args, $send_back );
      break;

    case 'geoipsl_site_info_clear_and_save':
      $blog_id = (int) geoipsl_array_value( $_REQUEST, 'id', 0 );

      geoipsl_deactivate_location( $blog_id );

      $send_back = remove_query_arg( array( 'location' ), $send_back );
      break;

    case 'geoipsl_config_save':
      $remove_args = array();

      $option_value = geoipsl_request_value( 'persistent_redirect_status', GEOIPSL_OFF_STATUS );
      $geoipsl_admin_settings->set_persistent_redirect_status( (string) $option_value );
      $remove_args[] = 'persistent_redirect_status';

      $option_value = geoipsl_request_value( 'persistence_interval', GEOIPSL_PERSISTENCE_INTERVAL );
      $geoipsl_admin_settings->set_persistence_interval( (int) $option_value );
      $remove_args[] = 'persistence_interval';

      $option_value = geoipsl_request_value( 'lightbox_as_location_chooser_status', GEOIPSL_OFF_STATUS );
      $geoipsl_admin_settings->set_lightbox_as_location_chooser_status( (string) $option_value );
      $remove_args[] = 'lightbox_as_location_chooser_status';

      $option_value = geoipsl_request_value( 'lightbox_trigger_element' );
      $geoipsl_admin_settings->set_lightbox_trigger_element( (string) $option_value );
      $remove_args[] = 'lightbox_trigger_element';

      $option_value = geoipsl_request_value( 'mobile_high_accuracy_status', GEOIPSL_OFF_STATUS );
      $geoipsl_admin_settings->set_mobile_high_accuracy_status( (string) $option_value );
      $remove_args[] = 'mobile_high_accuracy_status';

      $option_value = geoipsl_request_value( 'distance_limit', GEOIPSL_DISTANCE_LIMIT );
      $geoipsl_admin_settings->set_distance_limit( (int) $option_value );
      $remove_args[] = 'distance_limit';

      $option_value = geoipsl_request_value( 'query_proxies_status', GEOIPSL_OFF_STATUS );
      $geoipsl_admin_settings->set_query_proxies_status( (string) $option_value );
      $remove_args[] = 'query_proxies_status';

      $option_value = geoipsl_request_value( 'redirect_after_load_status', GEOIPSL_OFF_STATUS );
      $geoipsl_admin_settings->set_redirect_after_load_status( (string) $option_value );
      $remove_args[] = 'redirect_after_load_status';

      $send_back = remove_query_arg( $remove_args, $send_back );
      break;
  }

  wp_redirect( $send_back );
  exit;

} elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
  wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
  exit;
}


$geoip_test_database_or_service = geoipsl_request_or_saved_value( 'geoip_test_database_or_service' );
$geoip_test_ip                  = geoipsl_request_or_saved_value( 'geoip_test_ip', '', GEOIPSL_INVALID_IP );
$geoip_test_database_or_service = geoipsl_request_or_saved_value( 'geoip_test_database_or_service' );
$test_mobile_coords_from        = geoipsl_request_or_saved_value( 'test_mobile_coords_from', '', GEOIPSL_INVALID_TEST_COORDINATE );
$test_coords_to                 = geoipsl_request_or_saved_value( 'test_coords_to', '', GEOIPSL_INVALID_TEST_COORDINATE, 'base64_decode' );


// notification messages
$nags = $messages = array();

if ( in_array( $geoipsl_settings->get( 'geoip_db' ), array( 2, 3 ) ) && '' == $geoipsl_settings->get( 'maxmind_license_key' ) ) {
  $nags[] = sprintf( __( 'Please supply your MaxMind license key in order to use the %s database.', 'geoipsl' ), '' );
}

if ( in_array( $geoipsl_settings->get( 'service_db_to_use' ), array( 1, 2, 3 ) ) && '' == $geoipsl_settings->get( 'maxmind_license_key' ) ) {
  $nags[] = sprintf( __( 'Please supply your MaxMind license key in order to use the %s web service.', 'geoipsl' ), '' );
}

if ( in_array( $geoipsl_settings->get( 'service_db_to_use' ), array( 1, 2, 3 ) ) && '' == $geoipsl_settings->get( 'maxmind_user_id' ) ) {
  $nags[] = sprintf( __( 'Please supply your MaxMind user id in order to use the %s web service.', 'geoipsl' ), '' );
}

if ( GEOIPSL_INVALID_TEST_DATABASE_OR_SERVICE == $geoipsl_settings->get( 'geoip_test_database_or_service' ) && 'tests' == $current_tab ) {
  $nags[] = sprintf( __( 'Please choose a database or web service to test against.', 'geoipsl' ), '' );
}

if ( GEOIPSL_INVALID_IP == geoipsl_request_value( 'geoip_test_ip' ) && 'tests' == $current_tab ) {
  $nags[] = sprintf( __( 'Please provide a valid and non-reserved IP address.', 'geoipsl' ), '' );
}

if ( GEOIPSL_INVALID_TEST_COORDINATE == geoipsl_request_value( 'test_mobile_coords_from' ) && 'tests' == $current_tab ) {
  $nags[] = sprintf( __( 'Please provide an valid coordinate pair in the following format: <code>latitude, longitude</code>.', 'geoipsl' ), '' );
}

if ( GEOIPSL_INVALID_TEST_COORDINATE == geoipsl_request_value( 'test_coords_to', '', NULL, 'base64_decode' ) && 'tests' == $current_tab ) {
  $nags[] = sprintf( __( 'Please provide an valid coordinate pair in the following format: <code>latitude, longitude</code>. If you have multiple pairs, separate them by a new line.', 'geoipsl' ), '' );
}

// TODO: should be global warning no matter where you are
if ( $geoipsl_settings->get( 'geoip_test_ip' ) ) {
  $nags[] = sprintf( __( 'Debugging is turned on. This means that your site will be geolocated based on the fixed IP you provided %shere%s.', 'geoipsl' ), '<a href="' . admin_url( 'admin.php?page=geoip-site-locations/geoip-site-locations.php/&tab=tests' ) . '">', '</a>' );
}

if ( 'error' == geoipsl_request_value( 'upload_status' ) ) {
  $nags[] = sprintf( __( 'Cannot upload file %s. Please upload a valid MMDB file, or MMDB ZIP file.', 'geoipsl' ), geoipsl_request_value( 'file' ) );
}

if ( 'success' == geoipsl_request_value( 'upload_status' ) ) {
  $messages[] = sprintf( __( 'Uploaded file %s', 'geoipsl' ), geoipsl_request_value( 'file' ) );
}

if ( geoipsl_request_value( 'deleted' ) ) {
  $messages[] = sprintf( __( 'Deleted %s.', 'geoipsl' ), str_replace( ',', ', ', geoipsl_request_value( 'deleted' ) ) );
}

foreach ( $nags as  $nag ) {
  echo '<div class="update-nag">' . $nag . '</div>';
}

foreach ( $messages as $message ) {
  echo '<div class="updated">' . wpautop( $message ) . '</div>';
}
?>
<div class="wrap">

<h2><?php _e( 'GeoIP Site Locations', 'geoipsl' ); ?><span class="title-count geoipsl-count"><?php echo geoipsl_get_active_loc_count(); ?></span></h2>

  <p><?php _e( 'Detect user location based on IP or cookie information and redirect to the appropriate geo-targetted version of your site.' ); ?></p>

  <h2 class="nav-tab-wrapper">
    <?php
      foreach ( $navigation_tabs as  $navigation_tab ) {
       ?><a href="<?php echo $navigation_tab['href']; ?>" class="<?php echo $navigation_tab['class']; ?>"><?php echo $navigation_tab['text']; ?></a><?php
      }
    ?>
  </h2>

  <?php require_once( GEOIPSL_PLUGIN_DIR . 'admin/edit-settings-' . ( isset( $current_content ) ? $current_content : $current_tab  ) . '.php' ); ?>

  <div id="ajax-response"></div>
  <br class="clear" />
</div>
