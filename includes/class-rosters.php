<?php
if ( ! class_exists( 'WP_List_Table' ) ) require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );


class Andalu_Roster_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( array(
			'singular'  => 'class_roster',
			'plural'    => 'class_rosters',
			'ajax'      => false
		) );
	}

	public function column_default( $item, $column_name ) {
		$column_content = '';

		switch( $column_name ) {
			case 'course':
				$column_content = $item->class_id;
				$column_content = get_the_title( $item->course_class->post->post_parent );
				break;

			case 'dates':
				$column_content = get_the_title( $item->class_id );
				break;

			case 'location':
				$column_content = $item->course_class->get_location();
				break;

			case 'students':
				$students = $item->students;
				$available = $item->course_class->seats;
				$column_content = sprintf( __( '%d of %d', 'andalu_woo_courses' ), $students, $students + $available );
				break;

		}

		return $column_content;
	}

	public function get_columns(){

		$columns = array(
			'course'           => __( 'Course', 'andalu_woo_courses' ),
			'dates'            => __( 'Dates', 'andalu_woo_courses' ),
			'location'         => __( 'Location', 'andalu_woo_courses' ),
			'students'         => __( 'Students', 'andalu_woo_courses' ),
		);

		return $columns;
	}

	public function get_sortable_columns() {

		$sortable_columns = array(
			'course'           => array( 'course', false ),
			'dates'            => array( 'dates', false ),
		);

		return $sortable_columns;
	}

	function get_views() {
		$views = array();
		$current = ! empty( $_GET['status'] ) ? $_GET['status'] : 'upcoming';

		$url = add_query_arg( array( 'status' => 'upcoming', 'paged' => false ) );
		$class = ( $current == 'upcoming' ? ' class="current"' : '' );
		$views['upcoming'] = '<a href="' . $url . '"' . $class . '>' . __( 'Upcoming', 'andalu_woo_courses' ) . '</a>';

		$url = add_query_arg( array( 'status' => 'archived', 'paged' => false ) );
		$class = ( $current == 'archived' ? ' class="current"' : '' );
		$views['archived'] = '<a href="' . $url . '"' . $class . '>' . __( 'Archived', 'andalu_woo_courses' ) . '</a>';

		return $views;
	}

	function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
			$this->month_filter();
			submit_button( __( 'Filter' ), 'secondary', false, false );
		}
	}

	function prepare_items( $search = null ) {
		$columns = $this->get_columns();
		$hidden = array( 'id' );
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->load_from_db( $search );

		// Preload Course Class for each item
		if ( ! empty( $this->items ) ) {
			foreach( $this->items as $item_key => $item ) {
				$this->items[$item_key]->course_class = new WC_Product_Course_Class( $item->class_id );
			}
		}

	}

	function load_from_db( $search = null ) {
		global $wpdb;
		$table_name = $wpdb->posts;

		$sql = 'SELECT SQL_CALC_FOUND_ROWS oim.meta_value AS class_id, COUNT(*) AS students FROM ' . $wpdb->posts . ' o';
		$sql .= ' INNER JOIN ' . $wpdb->prefix . 'woocommerce_order_items oi ON oi.order_id = o.ID AND oi.order_item_type = "line_item"';
		$sql .= ' INNER JOIN ' . $wpdb->prefix . 'woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id AND oim.meta_key = "_class_id"';
		$sql .= ' INNER JOIN ' . $wpdb->postmeta . ' cd ON oim.meta_value = cd.post_id AND cd.meta_key = "_start_date"';
		$sql .= ' WHERE o.post_type = "shop_order" AND o.post_status = "wc-completed"';

		// Date range
		if ( empty( $_GET['m'] ) ) {

			$current = ! empty( $_GET['status'] ) ? $_GET['status'] : 'upcoming';
			switch( $current ) {
				case 'archived':
					$sql .= $wpdb->prepare( ' AND cd.meta_value <= %s', date( 'Y-m-d H:i:s' ) );
				break;

				case 'upcoming':
				default:
					$sql .= $wpdb->prepare( ' AND cd.meta_value > %s AND cd.meta_value <= %s', date( 'Y-m-d H:i:s' ), date( 'Y-m-d H:i:s', strtotime( '+7 days' ) ) );
				break;
			}

		} else {

			// Filter based on year and month
			$sql .= $wpdb->prepare( ' AND YEAR( cd.meta_value ) = %d', substr( $_GET['m'], 0, 4 ) );
			if ( strlen( $_GET['m'] ) > 5 ) {
				$sql .= $wpdb->prepare( ' AND MONTH( cd.meta_value) = %d', substr( $_GET['m'], 4, 2 ) );
			}

		}

		// Search for only specific courses
		if ( $search ) {
			$sql .= $wpdb->prepare( ' AND oi.order_item_name LIKE %s', '%' . trim( $search ) . '%' );
		}

		// Group by
		$sql .= ' GROUP BY oim.meta_value';

		// Get orderby
		$order = empty( $_GET['order'] ) ? 'desc' : $_GET['order'];
		$orderby = empty( $_GET['orderby'] ) ? 'course' : $_GET['orderby'];
		switch( $orderby ) {
			case 'dates':
				$order_field = 'cd.meta_value';
			break;
			case 'course':
			default:
				$order_field = 'oi.order_item_name';
			break;
		}

		$sql .= ' ORDER BY ' . $order_field . ' ' . $order;

		// Set pagination
		$per_page = 40;
		$current_page = $this->get_pagenum();
		$offset = $per_page * ( $current_page - 1 );
		$sql .= ' LIMIT ' . $offset . ',' . $per_page;

		// Get DB results
		$this->items = $wpdb->get_results( $sql );

		// Get total items and pagination args
		$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );
	}

	public function no_items() {
		if ( isset( $_POST['s'] ) ) :	?>
			<p><?php _e( 'No results found', 'andalu_woo_courses' ); ?></p>
		<?php else : ?>
			<p><?php _e( 'Course Classes will appear here for you to view once purchased by a customer', 'andalu_woo_courses' ); ?></p>
		<?php endif;
	}

	protected function month_filter() {
		global $wpdb, $wp_locale;

		$sql = 'SELECT DISTINCT YEAR( pm.meta_value ) AS year, MONTH( pm.meta_value ) AS month FROM ' . $wpdb->posts . ' p';
		$sql .= ' INNER JOIN ' . $wpdb->postmeta . ' pm ON pm.post_id = p.ID AND pm.meta_key = "_start_date"';
		$sql .= ' WHERE post_type = "course_class"';
		$sql .= ' ORDER BY pm.meta_value DESC';
		$months = $wpdb->get_results( $sql );
		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
?>
		<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
		<select name="m" id="filter-by-date">
			<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
<?php
		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option %s value='%s'>%s</option>\n",
				selected( $m, $year . $month, false ),
				esc_attr( $arc_row->year . $month ),
				/* translators: 1: month name, 2: 4-digit year */
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			);
		}
?>
		</select>
<?php
	}


}
