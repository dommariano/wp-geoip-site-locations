<?php

// this screen is only for users who can admins or at least users who can manage options.
if ( ! current_user_can( 'manage_options' ) )
  wp_die( __( 'Cheatin&#8217; uh?' ) );

// get instance of class Geoipsl_Supported_Locations_List_Table
$supported_location_list_table = geoipsl_get_list_table( 'Geoipsl_Supported_Locations_List_Table' );

// get the current page number.
$page_num = $supported_location_list_table->get_pagenum();

// get the current action: action, action2, delete_all, delete_all2
$do_action = $supported_location_list_table->current_action();


// if action is present, check if none is valid and user is coming from an admin screen
if ( $do_action ) {

  check_admin_referer( 'bulk-' . $supported_location_list_table->_args['plural']  );

  // remove these query args from current url
  $send_back = remove_query_arg( array( 'activated', 'reactivated', 'deactivated', 'ids' ), wp_get_referer() );

  // specify the parent file to use when sending back to this page
  $parent_file = $supported_location_list_table->_args['screen']->parent_file . '/locations';

  // make sur we're not coming from nowhere when query args are removed as done above
  if ( ! $send_back )
    $send_back = admin_url( $parent_file );

  // make sure the user stays on the current paged page when you send them back to where they came from
  $send_back = add_query_arg( 'paged', $page_num, $send_back );

  // clear form input and start over again
  if ( 'reset_query' == $do_action ) {
    $send_back = remove_query_arg( array( 'orderby', 'order', 'locations-query-submit', 'action', 'action2', 'paged', 'activated', 'reactivated', 'deactivated', 'ids', 'continent', 'country', 'region', 'city' ), wp_get_referer() );
    wp_redirect( $send_back );
  }

  if ( 'back_query' == $do_action ) {
      $supported_location_list_table->_get_selection_filter();

      if (  2 == $supported_location_list_table->get_search_level() ) {
        $send_back = remove_query_arg( array( 'locations-query-back', 'continent', 'country' ), wp_get_referer() );
        wp_redirect( $send_back );
      }

      if (  3 == $supported_location_list_table->get_search_level() ) {
        $send_back = remove_query_arg( array( 'locations-query-back', 'country', 'region' ), wp_get_referer() );
        wp_redirect( $send_back );
      }

      if (  4 == $supported_location_list_table->get_search_level() ) {
        $send_back = remove_query_arg( array( 'locations-query-back', 'region', 'city' ), wp_get_referer() );
        wp_redirect( $send_back );
      }

  }

  // reactivate all is the only action that needs to retrieve values from the database
  if ( 'reactivate_all' == $do_action ) {

    // sanitize location_status value from request
    $location_status = preg_replace('/[^a-z0-9_-]+/i', '', $_REQUEST['location_status']);

    // deactivated locations are locations that have been previously been active
    $location_ids = get_option( geoipsl_prefix_string( 'deactivated_locations' ) );

    // convert to comma separated list of values
    if ( is_array( $location_ids ) ) {
        $location_ids = implode( ',' , $location_ids );
    }

  // else, for any other action, just retrieve the ids we need to work on with
  } elseif ( isset( $_REQUEST['ids'] ) ) {
    $location_ids = $_REQUEST['ids'];

  } elseif ( ! empty( $_REQUEST['location'] ) ) {
    $location_ids = array_map('intval', $_REQUEST['location'] );
  }

  // if we don't have data to work with, to where you came from
  if ( ! isset( $location_ids ) ) {
    wp_redirect( $send_back );
    exit;
  }

  // ensure we only have a comma separated list of natural numbers
  $location_ids = preg_replace( '/[^0-9,]+/i', '',  $location_ids );

  switch ( $do_action ) {
    case 'enable':

      $activated = 0;

      foreach ( $location_ids as $location_id ) {
        if ( ! geoipsl_activate_location( $location_id ) )
          wp_die( __('Error activating GeoIP location.') );

        $activated++;
      }

      $send_back = add_query_arg( array('activated' => $activated, 'ids' => join(',', $location_ids) ), $send_back );
      break;

    case 'renable':
      $reactivated = 0;

      foreach ( $location_ids as $location_id ) {
        if ( ! geoipsl_activate_location( $location_id ) )
          wp_die( __('Error activating GeoIP location.') );

        $reactivated++;
      }

      $send_back = add_query_arg( array('reactivated' => $reactivated, 'ids' => join(',', $location_ids) ), $send_back );

      break;

    case 'disable':

      $deactivated = 0;

      foreach ( $location_ids as $location_id ) {
        if ( ! geoipsl_deactivate_location( $location_id ) )
          wp_die( __('Error deactivating GeoIP location.') );

        $deactivated++;
      }

      $send_back = add_query_arg( array('deactivated' => $deactivated, 'ids' => join(',', $location_ids) ), $send_back );
      break;
      break;
  }

  wp_redirect( $send_back );
  exit;

// else if no bulk action is present, and user comes from _wp_http_referer, redirect and exit script
} else if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
   wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
   exit;
}

// setup supported locations data and pagination variables.
$supported_location_list_table->prepare_items();

// fill up the bulk actions count.
$bulk_counts = array(
  'activated'     => isset( $_REQUEST['activated'] )   ? absint( $_REQUEST['activated'] )       : 0,
  'deactivated'   => isset( $_REQUEST['deactivated'] ) ? absint( $_REQUEST['deactivated'] )     : 0,
  'reactivated'   => isset( $_REQUEST['reactivated'] ) ? absint( $_REQUEST['reactivated'] )     : 0,
);

// setup the default bulk messages.
$bulk_messages = array(
  'activated'     => _n( '%s GeoIP site location activated.',   '%s GeoIP site locations activated.',   $bulk_counts['activated'] ),
  'deactivated'   => _n( '%s GeoIP site location deactivated.', '%s GeoIP site locations deactivated.', $bulk_counts['deactivated'] ),
  'reactivated'   => _n( '%s GeoIP site location reactivated.', '%s GeoIP site locations reactivated.', $bulk_counts['reactivated'] ),
);

// cstomize bulk messages.
$bulk_messages = apply_filters( 'bulk_geoipsl_updated_messages', $bulk_messages, $bulk_counts );

// remove zero entries.
$bulk_counts = array_filter( $bulk_counts );
?>

<div class="wrap geoipsl-sl">

<h2><?php _e('Supported Locations', 'geoipsl'); ?></h2>
<?php

// If we have a bulk message to issue:
$messages = array();

foreach ( $bulk_counts as $message => $count ) {
  if ( isset( $bulk_messages[ $message ] ) ) {
    $messages[] = sprintf( $bulk_messages[ $message ], number_format_i18n( $count ) );
  }

  if ( $message == 'deactivated' && isset( $_REQUEST['ids'] ) ) {
    $ids = preg_replace( '/[^0-9,]/', '', $_REQUEST['ids'] );
    $messages[] = '<a href="' . esc_url( wp_nonce_url( "", 'bulk-' . $supported_location_list_table->_args['plural'] ) ) . '">' . __('Undo') . '</a>';
  }
}

if ( $messages )
  echo '<div id="message" class="updated"><p>' . join( ' ', $messages ) . '</p></div>';
unset( $messages );

$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'activated', 'deactivated', 'reactivated' ), $_SERVER['REQUEST_URI'] );


?>

<?php $supported_location_list_table->views(); ?>

<form id="supported-locations-filter" action="" method="">
  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/locations">
  <?php $supported_location_list_table->display(); ?>
</form>

<div id="ajax-response"></div>
<br class="clear" />
</div>

<style type="text/css">
  [disabled="disabled"] {
    opacity: 0.75;
    filter: alpha(opacity=75);
    background: rgba( 235, 235, 235, 0.75 );
  }

  #locations-query-submit, #locations-query-back, #locations-query-reset {
  margin: 1px 8px 0 0;
  }

  .active-status {
    color: green;
    font-weight: bold;
  }

  .inactive-status {
    color: red;
    font-weight: bold;
  }
</style>
