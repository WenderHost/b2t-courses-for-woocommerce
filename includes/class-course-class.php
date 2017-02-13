<?php

// Handles course classes
class Andalu_Woo_Courses_Class {

	static $location_taxonomy = 'class_location';

	static function init() {
		add_action( 'init', __CLASS__ . '::register_class', 5 );

		// Load the correct product type
		add_filter( 'woocommerce_product_class', __CLASS__ . '::product_class', 10, 4 );

		// Create location taxonomy
		add_action( 'init', __CLASS__ . '::location_taxonomy', 5 );
		add_action( 'admin_menu', __CLASS__ . '::location_page');

		// Add taxonomy fields
		add_action( self::$location_taxonomy . '_term_new_form_tag', __CLASS__ . '::add_new_location' );
		//add_action( self::$location_taxonomy . '_add_form_fields', __CLASS__ . '::add_location_fields', 10, 2 );
		//add_action( self::$location_taxonomy . '_edit_form_fields', __CLASS__ . '::edit_location_fields', 10, 2 );
		//add_action( 'created_' . self::$location_taxonomy, __CLASS__ . '::save_location_fields', 10, 2 );
		//add_action( 'edited_' . self::$location_taxonomy, __CLASS__ . '::save_location_fields', 10, 2 );

		// Add shortcode for displaying class table
		add_shortcode( 'public_classes', __CLASS__ . '::public_classes' );
	}

	public static function register_class() {
		register_post_type( 'course_class', array(
			'label'        => __( 'Course Class', 'andalu_woo_courses' ),
			'public'       => false,
			'hierarchical' => false,
			'supports'     => false,
			'capability_type' => 'product'
		) );
	}

	public static function product_class( $classname, $product_type, $post_type, $product_id ) {
		if ( 'course_class' == $post_type ) {
			return 'WC_Product_Course_Class';
		}

		return $classname;
	}

	public static function location_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Class Locations', 'taxonomy general name', 'andalu_woo_courses' ),
			'singular_name'              => _x( 'Class Location', 'taxonomy singular name', 'andalu_woo_courses' ),
			'search_items'               => __( 'Search Locations', 'andalu_woo_courses' ),
			'popular_items'              => __( 'Popular Locations', 'andalu_woo_courses' ),
			'all_items'                  => __( 'All Locations', 'andalu_woo_courses' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Location', 'andalu_woo_courses' ),
			'update_item'                => __( 'Update Location', 'andalu_woo_courses' ),
			'add_new_item'               => __( 'Add New Location', 'andalu_woo_courses' ),
			'new_item_name'              => __( 'New Location Name', 'andalu_woo_courses' ),
			'separate_items_with_commas' => __( 'Separate locations with commas', 'andalu_woo_courses' ),
			'add_or_remove_items'        => __( 'Add or remove locations', 'andalu_woo_courses' ),
			'choose_from_most_used'      => __( 'Choose from the most used locations', 'andalu_woo_courses' ),
			'not_found'                  => __( 'No locations found.', 'andalu_woo_courses' ),
			'menu_name'                  => __( 'Class Locations' ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'class-location' ),
		);

		register_taxonomy( self::$location_taxonomy, 'course_class', $args );
	}

	public static function location_page() {
		$name = __( 'Class Locations', 'andalu_woo_courses' );
		add_submenu_page( 'edit.php?post_type=product', $name, $name, 'manage_options', 'edit-tags.php?taxonomy=' . self::$location_taxonomy . '&post_type=course_class' );

		add_filter( 'parent_file', __CLASS__ . '::location_parent_page' );
	}

	// Set the correct parent menu item when opening the edit location page
	public static function location_parent_page( $file ) {
		$screen = get_current_screen();
		if ( 'edit-tags' === $screen->base && 'course_class' === $screen->post_type) {
			$file = 'edit.php?post_type=product';
		}
		return $file;
	}

	// Add taxonomy fields
	public static function add_location_fields( $taxonomy ) { ?>
		<style type="text/css">.form-field.term-description-wrap{display:none}</style>

		<div class="form-field term-address-wrap">
			<label for="location_address"><?php _e( 'Address', 'andalu_woo_courses' ); ?></label>
			<?php wp_editor( '', 'location_address', array( 'textarea_name' => 'location_address', 'media_buttons' => false ) ); ?>
			<p><?php _e( 'Enter the location address', 'andalu_woo_courses' ); ?></p>
		</div>

		<div class="form-field term-directions-wrap">
			<label for="location_directions"><?php _e( 'Directions', 'andalu_woo_courses' ); ?></label>
			<?php wp_editor( '', 'location_directions', array( 'textarea_name' => 'location_directions', 'media_buttons' => false ) ); ?>
			<p><?php _e( 'Enter the location directions (HTML tags are allowed)', 'andalu_woo_courses' ); ?></p>
		</div>

		<div class="form-field term-lodging-wrap">
			<label for="location_lodging"><?php _e( 'Lodging', 'andalu_woo_courses' ); ?></label>
			<?php wp_editor( '', 'location_lodging', array( 'textarea_name' => 'location_lodging', 'media_buttons' => false ) ); ?>
			<p><?php _e( 'Enter the location lodging links (HTML tags are allowed) ', 'andalu_woo_courses' ); ?></p>
		</div>

		<script type="text/javascript">
			jQuery( function() {
				// Trigger save to force content to be saved before being submitted
				jQuery( '#addtag' ).on( 'mousedown', '#submit', function() {
					tinyMCE.triggerSave();
				});
			});
		</script>
	<?php
	}

	// Add new location
	public static function add_new_location() {
		// Set correct post type when adding new location
		global $post_type;
		$post_type = 'course_class';
	}

	// Edit taxonomy fields
	public static function edit_location_fields( $term, $taxonomy ) {
		$address    = get_term_meta( $term->term_id, 'address', true );
		$directions = get_term_meta( $term->term_id, 'directions', true );
		$lodging    = get_term_meta( $term->term_id, 'lodging', true );
	?>
		<tr class="form-field term-address-wrap">
			<th scope="row"><label for="location_address"><?php _e( 'Address', 'andalu_woo_courses' ); ?></label></th>
			<td>
				<?php wp_editor( $address, 'location_address', array( 'textarea_name' => 'location_address', 'media_buttons' => false ) ); ?>
				<p class="description"><?php _e( 'Enter the location address', 'andalu_woo_courses' ); ?></p>
			</td>
		</tr>
		<tr class="form-field term-directions-wrap">
			<th scope="row"><label for="location_directions"><?php _e( 'Directions', 'andalu_woo_courses' ); ?></label></th>
			<td>
				<?php wp_editor( $directions, 'location_directions', array( 'textarea_name' => 'location_directions', 'media_buttons' => false ) ); ?>
				<p class="description"><?php _e( 'Enter the location directions (HTML tags are allowed)', 'andalu_woo_courses' ); ?></p>
			</td>
		</tr>
		<tr class="form-field term-lodging-wrap">
			<th scope="row"><label for="location_lodging"><?php _e( 'Lodging', 'andalu_woo_courses' ); ?></label></th>
			<td>
				<?php wp_editor( $lodging, 'location_lodging', array( 'textarea_name' => 'location_lodging', 'media_buttons' => false ) ); ?>
				<p class="description"><?php _e( 'Enter the location lodging links (HTML tags are allowed) ', 'andalu_woo_courses' ); ?></p>
			</td>
		</tr>
    <?php
	}

	// Save location fields
	public static function save_location_fields( $term_id, $tt_id ) {
		if ( ! empty( $_POST['location_address'] ) ) {
			update_term_meta( $term_id, 'address', wp_kses_post( $_POST['location_address'] ) );
		}
		if ( ! empty( $_POST['location_directions'] ) ) {
			update_term_meta( $term_id, 'directions', wp_kses_post( $_POST['location_directions'] ) );
		}
		if ( ! empty( $_POST['location_lodging'] ) ) {
			update_term_meta( $term_id, 'lodging', wp_kses_post( $_POST['location_lodging'] ) );
		}
	}

	// Get all locations
	public static function get_locations() {
		$locations = array();
		$terms = get_terms( self::$location_taxonomy, array( 'hide_empty' => 0 ) );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach( $terms as $term ) {
				$locations[ $term->term_id ] = $term->name;
			}
		}

		return $locations;
	}

	/**
	 * Echos inline JS for class location table rows.
	 *
	 * @since 1.x.x
	 *
	 * @return void
	 */
	public static function get_location_js(){
		$script = array();
		$script[] = '<script type="text/javascript">';
		$script[] = "jQuery(document).ready(function($){
			$('.location-link').click(function(e){
				e.preventDefault();
				$(this).closest('tr').next().slideToggle();
			});
		});";
		$script[] = '</script>';

		echo implode( "\n", $script );
	}

	// Retrieve a class from slug
	public static function get_class_id( $slug, $post_type = 'course_class' ) {
		global $wpdb;
		$class_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $slug, $post_type ) );
		if ( ! empty( $class_id ) )
			return intval( $class_id );

		return 0;
	}

	// Check if we can register for this class
	public static function can_register() {
		if ( 'virtual' != get_query_var( 'course_register' ) && ( $class_id = self::get_class_id( get_query_var( 'course_register' ) ) ) ) {
			$class = wc_get_product( $class_id );
			if ( $class && ! $class->is_available() ) {
				return false;
			}
		}

		return true;
	}

	// Add shortcode for displaying class table
	public static function public_classes( $atts, $content = "", $name ) {
		add_action( 'wp_footer', array( get_called_class(), 'get_location_js' ), 9999 );
		ob_start();
		echo '<div class="public_classes woocommerce">';

		$atts = shortcode_atts( array(
			'id'         => 0,
			'categories' => '',
		), $atts, $name );

		$args = array(
			'post_type' => 'product',
			'post_parent' => 0,
			'posts_per_page' => -1,
			'fields' => 'ids',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => Andalu_Woo_Courses::$product_type,
				),
			),
		);

		// Filter courses
		if ( ! empty( $atts['id'] ) ) {
			$args['post__in'] = explode( ',', $atts['id'] );
		} elseif ( ! empty( $atts['categories'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field' => 'slug',
				'terms' => explode( ',', $atts['categories'] ),
			);
		}

		$has_classes = false;
		$products = get_posts( $args );
		if ( ! empty( $products ) ) {
			foreach( $products as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( ! empty( $product ) && $product->has_classes() ) {
					Andalu_Woo_Courses_Single::class_table( false, $product );
					Andalu_Woo_Courses_Single::sub_class_table( false, $product );
					$has_classes = true;
				}
			}
		}

		echo '</div>';

		if( true == $has_classes )
			return ob_get_clean();
	}

}
Andalu_Woo_Courses_Class::init();