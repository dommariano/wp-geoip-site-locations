<?php
if ( ! is_multisite() ) {
	wp_die( __( 'Multisite support is not enabled.' ) );
}

if ( ! current_user_can( 'manage_sites' ) ) {
	wp_die( __( 'You do not have sufficient permissions to edit this site.' ) );
}

?>
<div class="geoipsl-upload-db">
  <p class="geoipsl-upload-help"><?php _e( 'Upload an MMDB file or an MMDB zip file.' ); ?></p>
  <form method="post" enctype="multipart/form-data" class="wp-upload-form" action="<?php echo self_admin_url( 'admin.php?page=geoip-site-locations/geoip-site-locations.php/' ); ?>">
	<?php wp_nonce_field( 'geoipsl_settings' ); ?>
    <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/" />
    <input type="hidden" name="tab" value="databases" />
    <input type="hidden" name="tab-content" value="databases-upload" />
    <label class="screen-reader-text" for="mmdbzip"><?php _e( 'MMDB zip file or MMDB file.', 'geoipsl' ); ?></label>
    <input type="file" id="mmdbzip" name="mmdbzip" />
    <?php submit_button( __( 'Upload', 'geoipsl' ), 'button', 'geoipsl_upload_mmdb_zip', false ); ?>
  </form>
</div>
