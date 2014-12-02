<?php
if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

if ( is_admin() ) {
  add_action( 'maxmind_geoip_lite2_city_database', 'geoipsl_download_file', 10, 2 );
  add_action( 'maxmind_geoip2_city_database', 'geoipsl_download_file', 10, 2 );
  add_action( 'maxmind_geoip2_country_database', 'geoipsl_download_file', 10, 2 );

  add_action( 'all_admin_notices', 'geoipsl_admin_notices_on_fresh_install' );
  /**
    * Message to display after FRESH install of this plugin.
    *
    * @since 1.0.0
    *
    * @return void
    */
  function geoipsl_admin_notices_on_fresh_install() {

    // have we just finished the installation process?
    $is_fresh_instal_complete = get_option( geoipsl_prefix_string( 'first_time_setup_complete' ), 0 );

    // track install progress with a counter
    $count = 0;

    // we also need to check if we have a GeoIP database to work with and this one is ALL we ever need
    if ( file_exists( geoipsl_get_file_path( 'GeoLite2-City.mmdb' ) ) ) {
      $count++;
    }

    // calculate setup progress
    $progress = ( $count / 1 ) * 100;

    // display a ready message withing 5 seconds of plugin setup completion or if this notification hasn't been seen yet
    if ( $is_fresh_instal_complete < 0 || abs( $is_fresh_instal_complete - abs( time() ) ) < 5 ) {
      update_option( geoipsl_prefix_string( 'first_time_setup_complete' ), time() );
      ?>
      <div class="updated">
        <?php geoipsl_wpautop_e( sprintf( __( 'Installation complete for %sGeoIP Site Locations%s. You may now begin using this plugin to redirect your site visitors to appropriate version of your website.', 'geoipsl' ), '<b>', '</b>' ) ); ?>
      </div>
      <?php
    }

    // if initial setup is complete, exit
    if ( $is_fresh_instal_complete ) {
      return;
    }

    // if the initial setup is complete, remember it
    if ( 100 == $progress ) {
      update_option( geoipsl_prefix_string( 'first_time_setup_complete' ), -1 * time() );
    }

    // display a nag notification while update is not yet complete
    if ( ! abs( $is_fresh_instal_complete ) ) { ?>
      <div class="update-nag">
          <?php printf( __( ' %sGeoIP Site Locations%s is currently setting itself up and making configurations. Please %srefresh%s this page to check on progress.', 'geoipsl' ), '<b>', '</b>', '<a href="">', '</a>' ); ?>
      </div>
    <?php }
  }

  add_action( 'admin_menu', 'geoipsl_add_plugin_pages', 10 );
  add_action( 'network_admin_menu', 'geoipsl_add_plugin_pages', 10 );
  function geoipsl_add_plugin_pages( ) {
    // only the network admin should have access to plugin pages
    if ( ! is_super_admin( get_current_user_id( ) ) )
      return;

    // the plugin pages must only be visible to the root site
    if ( defined( 'SITE_ID_CURRENT_SITE' ) && SITE_ID_CURRENT_SITE != get_current_blog_id() )
      return;

    add_menu_page(
      __( 'GeoIP Sites Locations', 'geoipsl' ),
      __( 'GeoIP Sites', 'geoipsl' ),
      'manage_options',
      GEOIPSL_PLUGIN_NAME,
      'geoipsl_create_plugin_menu_page',
      '',
      1
    );
  }

  function geoipsl_create_plugin_menu_page( ) {
    require_once( GEOIPSL_PLUGIN_DIR . 'admin/edit-settings.php' );
  }
}