<?php

use GeoIPSL\Distance;
use Ivory\GoogleMap;
use Geocoder\HttpAdapter\BuzzHttpAdapter;
use Geocoder\Geocoder;
use Geocoder\Provider\GoogleMapsProvider;

global $geoipsl_reader, $geoipsl_settings;

$alocs	= get_option( 'geoipsl_activated_locations', array() );
$dist		= array();
$cnt		= 0;
$coords = str_replace( ' ', '', $test_mobile_coords_from );
$coords = explode( ',', $coords );
$lat_to	= floatval( $coords[0] );
$lon_to	= floatval( $coords[1] );

foreach ( $alocs as $id => $location ) {
	$lat_fr	= floatval( $location['latitude'] );
	$lon_fr	= floatval( $location['longitude'] );
	$d			= Distance::geodesic( $lat_fr, $lon_fr, $lat_to, $lon_to );
	$alocs[ $id ]['distance_from_given_ip'] = $d;
	$alocs[ $id ]['id'] = $id;
	$dist[ $id ] = $d;
}

unset( $id, $location, $lat_fr, $lon_fr, $d );

$adapter	= new BuzzHttpAdapter();
$geocoder	= new Geocoder();
$geocoder
	->registerProviders( array(
		new GoogleMapsProvider( $adapter, 'en_US' ),
	) );
$geocode	= $geocoder->reverse( $lat_to, $lon_to );
?>

<p>
Given coordinate
<code>
	<?php printf( '( %s, %s )', $lat_to, $lon_to ); ?>
</code>
is located at 
<code>
<?php printf( '%d %s, %s, %s, %s %s',
	$geocode->getStreetNumber(),
	$geocode->getStreetName(),
	$geocode->getCity(),
	$geocode->getCounty(),
	$geocode->getCountry(),
$geocode->getZipcode() ); ?>
</code>.
This coordinate is
<code> 
<?php echo number_format( min( $dist ), 2, '.', ',' ) . ' meters'; ?>
</code>
away from 
<?php
$closest_site_ids = array_keys( $dist, min( $dist ) );
printf(
	_n(
		'1 nearest location',
		'%d nearest locations:',
		count( $closest_site_ids ),
		'geoipsl'
	),
	count( $closest_site_ids )
);
foreach ( $closest_site_ids as $site_id ) {
?>
<code>
<?php
$cnt++;
printf( '<a href="%s" target="_blank">%s</a>',
	get_site_url( $site_id ),
	get_site_url( $site_id )
);
?>
</code>
with site id of
<code>
<?php echo $site_id; ?>
</code>
<?php echo ( $cnt != count( $closest_site_ids ) ) ? ',' : '.';
}
// will destroy array key association
array_multisort( $dist, SORT_ASC, $alocs );

$cnt = 0;
?>
</p>
<?php unset( $blog_id, $lat_to, $lon_to ); ?>
<table class="wp-list-table widefat">

	<thead>
		<th><?php _e( 'Blog ID', 'geoipsl' ); ?></th>
		<th><?php _e( 'Blog URL', 'geoipsl' ); ?></th>
		<th><?php _e( 'Latitude', 'geoipsl' ); ?></th>
		<th><?php _e( 'Longitude', 'geoipsl' ); ?></th>
		<th><?php _e( 'Distance from IP', 'geoipsl' ); ?></th>
	</thead>

	<tfoot>
		<th><?php _e( 'Blog ID', 'geoipsl' ); ?></th>
		<th><?php _e( 'Blog URL', 'geoipsl' ); ?></th>
		<th><?php _e( 'Latitude', 'geoipsl' ); ?></th>
		<th><?php _e( 'Longitude', 'geoipsl' ); ?></th>
		<th><?php _e( 'Distance from IP', 'geoipsl' ); ?></th>
	</tfoot>

	<tbody><?php
	foreach ( $alocs as $id => $location ) {
		$cnt++;
		$class = ( $cnt % 2 ) ? 'alternate' : ''; ?>

			<tr class="<?php echo esc_attr( $class ); ?>" >
				<td><?php echo esc_attr( $location['id'] ); ?></td>
				<td><?php
					printf( '<a href="%s" target="_blank">%s</a>',
						get_site_url( $location['id'] ),
					get_site_url( $location['id'] ) );
				?></td>
				<td><?php echo esc_attr( $location['latitude'] ); ?></td>
				<td><?php echo esc_attr( $location['longitude'] ); ?></td>
				<td><?php echo esc_attr( number_format(
				$location['distance_from_given_ip'], 2, '.', ',' ) . ' m' );
				?></td>
			</tr> <?php }

			unset( $alocs, $id, $location, $rslt, $lat, $long, $cnt, $class ); ?>

	</tbody>
</table>

<br>
