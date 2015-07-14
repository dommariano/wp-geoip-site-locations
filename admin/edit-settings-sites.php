<?php

if ( ! is_multisite() ) {
	wp_die( __( 'Multisite support is not enabled.' ) ); }

if ( ! current_user_can( 'manage_sites' ) ) {
	wp_die( __( 'You do not have permission to access this page.' ) ); }

$wp_list_table = geoipsl_get_list_table( 'GeoIPSL\Sites_List_Table' );
$pagenum = $wp_list_table->get_pagenum();

$parent_file = add_query_arg( array( 'page' => 'geoip-site-locations/geoip-site-locations.php/' ), network_admin_url() );

$wp_list_table->prepare_items();
?>

<form id="geoipsl-settings-form-site-list" action="" method="get">
  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="sites">
	<?php $wp_list_table->display(); ?>
</form>
