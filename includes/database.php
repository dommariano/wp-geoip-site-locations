<?php

if ( ! function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

// download the city database
if ( ! file_exists( geoipsl_get_file_path( 'GeoLite2-City.mmdb' ) ) ) {
  wp_schedule_single_event(
    time(),
    'maxmind_geoip_lite2_city_database',
    array(
      'GeoLite2-City.mmdb',
      'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',
    )
  );
}
