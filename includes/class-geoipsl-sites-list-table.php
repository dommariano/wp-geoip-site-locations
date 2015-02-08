<?php namespace GeoIPSL;

if ( ! function_exists( 'add_action' ) && ! function_exists( 'add_filter' ) ) {
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}

/**
 * GeoIP Sites List Table class.
 *
 * Patterned from WP_MS_Sites_List_Table class.
 *
 * @since 0.1.0
 * @todo Add search functionality for easy navigation.
 */
class Sites_List_Table extends \WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array(
			'plural' => 'sites-list',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
	}

	public function ajax_user_can() {
		return current_user_can( 'manage_sites' );
	}

	public function prepare_items() {
		global $s, $mode, $wpdb;

		$current_site = get_current_site();

		$mode = ( empty( $_REQUEST['mode'] ) ) ? 'list' : $_REQUEST['mode'];

		$per_page = $this->get_items_per_page( 'sites_network_per_page' );

		$pagenum = $this->get_pagenum();

		$s = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST[ 's' ] ) ) : '';
		$wild = '';
		if ( false !== strpos($s, '*') ) {
			$wild = '%';
			$s = trim($s, '*');
		}

		/*
		 * If the network is large and a search is not being performed, show only
		 * the latest blogs with no paging in order to avoid expensive count queries.
		 */
		if ( !$s && wp_is_large_network() ) {
			if ( !isset($_REQUEST['orderby']) )
				$_GET['orderby'] = $_REQUEST['orderby'] = '';
			if ( !isset($_REQUEST['order']) )
				$_GET['order'] = $_REQUEST['order'] = 'DESC';
		}

		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";

		if ( empty($s) ) {
			// Nothing to do.
		} elseif ( preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $s ) ||
					preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.?$/', $s ) ||
					preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.?$/', $s ) ||
					preg_match( '/^[0-9]{1,3}\.$/', $s ) ) {
			// IPv4 address
			$sql = $wpdb->prepare( "SELECT blog_id FROM {$wpdb->registration_log} WHERE {$wpdb->registration_log}.IP LIKE %s", $wpdb->esc_like( $s ) . $wild );
			$reg_blog_ids = $wpdb->get_col( $sql );

			if ( !$reg_blog_ids )
				$reg_blog_ids = array( 0 );

			$query = "SELECT *
				FROM {$wpdb->blogs}
				WHERE site_id = '{$wpdb->siteid}'
				AND {$wpdb->blogs}.blog_id IN (" . implode( ', ', $reg_blog_ids ) . ")";
		} else {
			if ( is_numeric($s) && empty( $wild ) ) {
				$query .= $wpdb->prepare( " AND ( {$wpdb->blogs}.blog_id = %s )", $s );
			} elseif ( is_subdomain_install() ) {
				$blog_s = str_replace( '.' . $current_site->domain, '', $s );
				$blog_s = $wpdb->esc_like( $blog_s ) . $wild . $wpdb->esc_like( '.' . $current_site->domain );
				$query .= $wpdb->prepare( " AND ( {$wpdb->blogs}.domain LIKE %s ) ", $blog_s );
			} else {
				if ( $s != trim('/', $current_site->path) ) {
					$blog_s = $wpdb->esc_like( $current_site->path . $s ) . $wild . $wpdb->esc_like( '/' );
				} else {
					$blog_s = $wpdb->esc_like( $s );
				}
				$query .= $wpdb->prepare( " AND  ( {$wpdb->blogs}.path LIKE %s )", $blog_s );
			}
		}

		$order_by = isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '';
		if ( $order_by == 'registered' ) {
			$query .= ' ORDER BY registered ';
		} elseif ( $order_by == 'lastupdated' ) {
			$query .= ' ORDER BY last_updated ';
		} elseif ( $order_by == 'blogname' ) {
			if ( is_subdomain_install() )
				$query .= ' ORDER BY domain ';
			else
				$query .= ' ORDER BY path ';
		} elseif ( $order_by == 'blog_id' ) {
			$query .= ' ORDER BY blog_id ';
		} else {
			$order_by = null;
		}

		if ( isset( $order_by ) ) {
			$order = ( isset( $_REQUEST['order'] ) && 'DESC' == strtoupper( $_REQUEST['order'] ) ) ? "DESC" : "ASC";
			$query .= $order;
		}

		// Don't do an unbounded count on large networks
		if ( ! wp_is_large_network() )
			$total = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT( blog_id )', $query ) );

		$query .= " LIMIT " . intval( ( $pagenum - 1 ) * $per_page ) . ", " . intval( $per_page );
		$this->items = $wpdb->get_results( $query, ARRAY_A );

		if ( wp_is_large_network() )
			$total = count($this->items);

		$this->set_pagination_args( array(
			'total_items' => $total,
			'per_page' => $per_page,
		) );
	}

	public function no_items() {
		_e( 'No sites found.' );
	}

	protected function get_bulk_actions() {
		return array();
	}

	protected function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which )
			$this->view_switcher( $mode );
	}

	protected function get_column_headers( $screen ) {
		if ( is_string( $screen ) )
			$screen = convert_to_screen( $screen );

		static $column_headers = array();

		if ( ! isset( $column_headers[ $screen->id ] ) || empty( $column_headers[ $screen->id ] ) ) {

			$column_headers[ $screen->id ] = apply_filters( "manage_{$screen->id}_columns", array() );
		}

		return $column_headers[ $screen->id ];
	}

	protected function get_column_info() {
		if ( isset( $this->_column_headers ) )
			return $this->_column_headers;

		$columns = $this->get_column_headers( $this->screen );
		$hidden = get_hidden_columns( $this->screen );

		$sortable_columns = $this->get_sortable_columns();

		$_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $sortable_columns );

		$sortable = array();
		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) )
				continue;

			$data = (array) $data;
			if ( !isset( $data[1] ) )
				$data[1] = false;

			$sortable[$id] = $data;
		}

		$this->_column_headers = array( $columns, $hidden, $sortable );

		return $this->_column_headers;
	}

	public function get_columns() {
		$blogname_columns = ( is_subdomain_install() ) ? __( 'Domain' ) : __( 'Path' );
		$sites_columns = array(
			'blogname'    => $blogname_columns,
			'latitude' => __( 'Latitude' ),
			'longitude'  => __( 'Longitude' ),
			'address'       => __( 'Address' ),
		);

		if ( has_filter( 'wpmublogsaction' ) )
			$sites_columns['plugins'] = __( 'Actions' );

		return $sites_columns;
	}

	protected function get_sortable_columns() {
		return array(
			'blogname'    => 'blogname',
			'latitude' => 'longitude',
			'longitude'  => 'address',
		);
	}

	public function display_rows() {
		global $mode;

		$status_list = array(
			'archived' => array( 'site-archived', __( 'Archived' ) ),
			'spam'     => array( 'site-spammed', _x( 'Spam', 'site' ) ),
			'deleted'  => array( 'site-deleted', __( 'Deleted' ) ),
			'mature'   => array( 'site-mature', __( 'Mature' ) )
		);

		if ( 'list' == $mode ) {
			$date = 'Y/m/d';
		} else {
			$date = 'Y/m/d \<\b\r \/\> g:i:s a';
		}

		$class = '';
		foreach ( $this->items as $blog ) {
			$class = ( 'alternate' == $class ) ? '' : 'alternate';
			reset( $status_list );

			$blog_states = array();
			foreach ( $status_list as $status => $col ) {
				if ( get_blog_status( $blog['blog_id'], $status ) == 1 ) {
					$class = $col[0];
					$blog_states[] = $col[1];
				}
			}
			$blog_state = '';
			if ( ! empty( $blog_states ) ) {
				$state_count = count( $blog_states );
				$i = 0;
				$blog_state .= ' - ';
				foreach ( $blog_states as $state ) {
					++$i;
					( $i == $state_count ) ? $sep = '' : $sep = ', ';
					$blog_state .= "<span class='post-state'>$state$sep</span>";
				}
			}
			echo "<tr class='$class'>";

			$blogname = ( is_subdomain_install() ) ? str_replace( '.' . get_current_site()->domain, '', $blog['domain'] ) : $blog['path'];

			list( $columns, $hidden ) = $this->get_column_info();

			$blog_location = get_option( geoipsl( 'activated_locations' ), array() );

			foreach ( $columns as $column_name => $column_display_name ) {
				$style = '';
				if ( in_array( $column_name, $hidden ) )
					$style = ' style="display:none;"';

				switch ( $column_name ) {
					case 'id':?>
						<th scope="row">
							<?php echo $blog['blog_id'] ?>
						</th>
					<?php
					break;

					case 'blogname':
						echo "<td class='column-$column_name $column_name'$style>"; ?>
							<a href="<?php echo esc_url( network_admin_url( 'site-info.php?id=' . $blog['blog_id'] ) ); ?>" class="edit"><?php echo $blogname . $blog_state; ?></a>
							<?php
							if ( 'list' != $mode ) {
								switch_to_blog( $blog['blog_id'] );
								echo '<p>' . sprintf( _x( '%1$s &#8211; <em>%2$s</em>', '%1$s: site name. %2$s: site tagline.' ), get_option( 'blogname' ), get_option( 'blogdescription ' ) ) . '</p>';
								restore_current_blog();
							}

							// Preordered.
							$actions = array(
								'edit' => '',
								'backend' => '',
								'visit' => '',
							);

							$actions['edit']	= '<span class="edit"><a href="' . esc_url( network_admin_url( 'admin.php?page=geoip-site-locations/geoip-site-locations.php/&tab=sites&tab-content=site-info&id=' . $blog['blog_id'] ) ) . '">' . __( 'Edit' ) . '</a></span>';
							$actions['backend']	= "<span class='backend'><a href='" . esc_url( get_admin_url( $blog['blog_id'] ) ) . "' class='edit'>" . __( 'Dashboard' ) . '</a></span>';
							$actions['visit']	= "<span class='view'><a href='" . esc_url( get_home_url( $blog['blog_id'], '/' ) ) . "' rel='permalink'>" . __( 'Visit' ) . '</a></span>';

							$actions = apply_filters( 'manage_sites_action_links', array_filter( $actions ), $blog['blog_id'], $blogname );
							echo $this->row_actions( $actions );
					?>
						</td>
					<?php
					break;

					case 'latitude':
						echo "<td class='$column_name column-$column_name'$style>";
						if ( isset( $blog_location[ $blog['blog_id'] ] ) ) {
							echo $blog_location[ $blog['blog_id'] ]['latitude'];
						}
						?>
						</td>
					<?php
					break;
				case 'longitude':
						echo "<td class='$column_name column-$column_name'$style>";
						if ( isset( $blog_location[ $blog['blog_id'] ] ) ) {
							echo $blog_location[ $blog['blog_id'] ]['longitude'];
						}
						?>
						</td>
					<?php
					break;
				case 'address':
						echo "<td class='$column_name column-$column_name'$style>";

						if ( isset( $blog_location[ $blog['blog_id'] ] ) ) {
							$blog_location_info = $blog_location[ $blog['blog_id'] ];
							$location_defaults = array(
							  'latitude'      => '',
							  'longitude'     => '',
							  'street_number' => '',
							  'street_name'   => '',
							  'city'          => '',
							  'city_district' => '',
							  'postal_code'   => '',
							  'county'        => '',
							  'county_code'   => '',
							  'region'        => '',
							  'region_code'   => '',
							  'country'       => '',
							  'country_code'  => '',
							  'timezone'      => '',
							);

							$blog_location_info = wp_parse_args( $blog_location_info, $location_defaults );

						  $street_number 	= $blog_location_info['street_number'];
						  $street_name 		= $blog_location_info['street_name'];
						  $city_district 	= $blog_location_info['city_district'];
						  $county 				= $blog_location_info['county'];
						  $region 				= $blog_location_info['region'];
						  $country 				= $blog_location_info['country'];
						  $postal_code 		= $blog_location_info['postal_code'];

						  unset( $blog_location_info );

							printf("%s %s, %s, %s, %s, %s %s", $street_number, $street_name, $city_district, $county, $region, $country, $postal_code );
						}

						?>
						</td>
					<?php
					break;

				default:
					echo "<td class='$column_name column-$column_name'$style>";

					do_action( 'manage_sites_custom_column', $column_name, $blog['blog_id'] );
					echo "</td>";
					break;
				}
			}
			?>
			</tr>
			<?php
		}
	}
}
