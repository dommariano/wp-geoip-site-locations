<?php
if ( ! is_multisite() ) {
  wp_die( __( 'Multisite support is not enabled.' ) );
}

if ( ! current_user_can( 'manage_sites' ) ) {
  wp_die( __( 'You do not have sufficient permissions to edit this site.' ) );
}

$files = isset( $_REQUEST['files'] ) ? explode( ',', $_REQUEST['files'] ) : '';
?>

  <form method="post" action="<?php self_admin_url(); ?>">
  <?php wp_nonce_field( 'geoipsl_settings' ); ?>

  <?php if ( count( $files ) ) { foreach( $files as $file ) { ?>
  <input type="hidden" name="files[]" value="<?php echo $file; ?>">
  <?php } } ?>

  <?php unset( $file ); ?>

  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">
  <input type="hidden" name="tab" value="databases">
  <input type="hidden" name="tab-content" value="databases-delete">
  <p><?php _e( 'You are about to remove the following GeoIP databases: ', 'geoipsl' ); ?>
  <?php if ( count( $files ) ) { ?>
  <ul class="ul-disc">
  <?php foreach( $files as $file ) { ?>
    <li><?php echo $file; ?></li>
  <?php } ?>
  </ul>
  <?php } ?>
  <p><?php _e( 'Are you sure you want to delete these files?' , 'geoipsl' ); ?></p>
  <?php submit_button( __( 'Delete', 'geoipsl' ), 'button', 'geoipsl_delete_mmdb_zip', false ); ?>
  <?php submit_button( __( 'Cancel', 'geoipsl' ), 'button', 'geoipsl_cancel_del_mmdb_zip', false ); ?>
</form>
