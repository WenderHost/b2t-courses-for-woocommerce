<?php
class Andalu_Woo_Courses_Admin {

	private static $list_table;

	static function init() {

		// Check page before include
		add_action( 'current_screen', __CLASS__ . '::conditional_includes', 20 );

		// Add new product type to the product select box
		add_filter( 'product_type_selector', __CLASS__ . '::product_types' );

		// Add additional tabs
		add_filter( 'woocommerce_product_data_tabs', __CLASS__ . '::data_tabs' );
		add_action( 'woocommerce_product_data_panels', __CLASS__ . '::data_tab_content' );

		// Save data fields on edit product page
		add_action( 'woocommerce_process_product_meta_' . Andalu_Woo_Courses::$product_type, __CLASS__ . '::save_data_fields' );

		// Add ajax events
		add_action( 'wp_ajax_woocommerce_add_course_outline', __CLASS__ . '::add_course_outline' );
		add_action( 'wp_ajax_woocommerce_save_course_outlines', __CLASS__ . '::save_course_outlines' );
		add_action( 'wp_ajax_woocommerce_add_course_class', __CLASS__ . '::add_course_class' );
		add_action( 'wp_ajax_woocommerce_remove_course_class', __CLASS__ . '::remove_course_class' );
		add_action( 'wp_ajax_woocommerce_load_course_classes', __CLASS__ . '::load_course_classes' );
		add_action( 'wp_ajax_woocommerce_save_course_classes', __CLASS__ . '::save_course_classes' );
		add_action( 'wp_ajax_woocommerce_json_search_course_products', __CLASS__ . '::json_search_course_products' );

		// Load all necessary admin styles and scripts
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::enqueue_styles_scripts' );

		// Add Financed Products menu page
		add_action( 'admin_menu', __CLASS__ . '::add_menu_pages', 15 );

	}

	static public function conditional_includes() {
		$screen = get_current_screen();

		switch ( $screen->id ) {
			case 'options-permalink' :
				self::save_permalinks();
			break;
		}
	}

	// Save product permalink regex to use for sub pages
	static function save_permalinks() {
		if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) && isset( $_POST['product_permalink'] ) ) {
			$permalinks = get_option( 'woocommerce_permalinks' );
			if ( ! empty( $permalinks['product_base'] ) ) {
				$permalinks['product_regex_parts'] = ( substr_count( $permalinks['product_base'], '%' ) / 2 ) + 1;
				$permalinks['product_regex'] = trailingslashit( preg_replace( '/%([^%]+)%/', '([^/]+)', trim( $permalinks['product_base'], '/' ) ) ) . '([^/]+)/';
				update_option( 'woocommerce_permalinks', $permalinks );
			} elseif ( isset( $permalinks['product_base'] ) ) {
				unset( $permalinks['product_regex'] );
				unset( $permalinks['product_regex_parts'] );
				update_option( 'woocommerce_permalinks', $permalinks );
			}
		}
	}


	// Add new product type to the product select box
	static function product_types( $types ) {
		$types[ Andalu_Woo_Courses::$product_type ] = __( 'Course', 'andalu_woo_courses' );
		return $types;
	}


	// Save general fields on edit product page
	static function save_data_fields( $post_id ) {

		$fields = array( 'reference', 'duration' , 'certification', 'pdus', 'audience', 'prerequisites' );
		foreach ( $fields as $field ) {
			$field_name = '_course_' . $field;
			update_post_meta( $post_id, $field_name, stripslashes( $_REQUEST[ $field_name ] ) );
		}
		if( $_REQUEST['_course_delivery_mode'] )
			update_post_meta( $post_id, '_course_delivery_mode', $_REQUEST['_course_delivery_mode'] );

		wp_update_post( array( 'ID' => $post_id, 'post_parent' => empty( $_REQUEST[ '_course_parent' ] ) ? 0 : intval( $_REQUEST[ '_course_parent' ] ) ) );
		update_post_meta( $post_id, '_course_study_guide', empty( $_REQUEST[ '_course_study_guide' ] ) ? '' : $_REQUEST[ '_course_study_guide' ] );
		update_post_meta( $post_id, '_course_exam', empty( $_REQUEST[ '_course_exam' ] ) ? '' : $_REQUEST[ '_course_exam' ] );

		$endorsements = array();
		foreach ( Andalu_Woo_Courses::$endorsements as $endorsement ) {
			$endorsements[ $endorsement ] = empty( $_REQUEST['_course_endorsement_' . $endorsement] ? false : true );
		}
		update_post_meta( $post_id, '_course_endorsements', $endorsements );

		self::save_course_outline_data( $post_id, $_REQUEST );
		self::save_course_classes_data( $post_id, $_REQUEST );

		self::fix_parent_price();
	}

	// Fix product parent price (WC resets it to lowest child price in WC_Meta_Box_Product_Data::save)
	static function fix_parent_price() {
		if ( empty( $_REQUEST[ '_course_parent' ] ) ) return;

		$parent_id = $_REQUEST[ '_course_parent' ];
		$regular_price = get_post_meta( $parent_id, '_regular_price', true );
		$sale_price    = get_post_meta( $parent_id, '_sale_price', true );

		if ( ! empty( $sale_price ) ) {
			$date_from = get_post_meta( $parent_id, '_sale_price_dates_from', true );
			$date_to   = get_post_meta( $parent_id, '_sale_price_dates_to', true );

			// Update price if on sale
			if ( '' == $date_to && '' == $date_from ) {
				update_post_meta( $parent_id, '_price', $sale_price );
			} elseif (
				$date_from && strtotime( $date_from ) <= strtotime( 'NOW', current_time( 'timestamp' ) )
				&& $date_to && strtotime( $date_to ) >= strtotime( 'NOW', current_time( 'timestamp' ) )
			) {
				update_post_meta( $parent_id, '_price', $sale_price );
			} elseif ( ! empty( $regular_price ) ) {
				update_post_meta( $parent_id, '_price', $regular_price );
			}

		} elseif ( ! empty( $regular_price ) ) {
			update_post_meta( $parent_id, '_price', $regular_price );
		}

	}

	// Add additional tabs
	static function data_tabs( $tabs ) {

		foreach( array( 'inventory', 'attribute', 'linked_product' ) as $tab ) {
			if ( isset( $tabs[ $tab ] ) ) { $tabs[ $tab ]['class'][] = 'hide_if_course'; }
		}

		// Extract first tabs
		$first_tabs = array();
		$first_tabs['general'] = $tabs['general'];
		unset( $tabs['general'] );

		// Add new tabs
		$new_tabs = array();
		$new_tabs['course_data'] = array(
			'label'  => __( 'Course Data', 'andalu_woo_courses' ),
			'target' => 'course_data',
			'class'  => array( 'show_if_course' ),
		);

		$new_tabs['course_links'] = array(
			'label'  => __( 'Linked Products', 'andalu_woo_courses' ),
			'target' => 'course_links_data',
			'class'  => array( 'show_if_course' ),
		);

		$new_tabs['course_outline'] = array(
			'label'  => __( 'Course Outlines', 'andalu_woo_courses' ),
			'target' => 'course_outline_data',
			'class'  => array( 'show_if_course' ),
		);

		$new_tabs['course_classes'] = array(
			'label'  => __( 'Classes', 'andalu_woo_courses' ),
			'target' => 'course_classes_data',
			'class'  => array( 'show_if_course' ),
		);

		// Return new set of tabs
		return array_merge( $first_tabs, $new_tabs, $tabs );
	}

	static function woocommerce_wp_select_multiple( $field ) {
	    global $thepostid, $post, $woocommerce;

	    $thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	    $field['value']         = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );

	    echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';

	    foreach ( $field['options'] as $key => $value ) {
	        echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
	    }
	    echo '</select> ';

	    if ( ! empty( $field['description'] ) ) {
	        if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
	            echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
	        } else {
	            echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	        }
	    }
	    echo '</p>';
	}

	// Add the content for the additional data tab
	static function data_tab_content() {
		global $post;
		$post_id = $post->ID;

	?>
		<div id="course_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php
					woocommerce_wp_text_input( array(
						'id'          => '_course_reference',
						'label'       => __( 'Reference', 'andalu_woo_courses' ),
						'placeholder' => _x( 'e.g. JJM 162', 'example reference', 'andalu_woo_courses' ),
					) );

					Andalu_Woo_Courses_Admin::woocommerce_wp_select_multiple([
						'id'				=> '_course_delivery_mode',
						'name'			=> '_course_delivery_mode[]',
						'label'			=> __( 'Delivery Mode', 'andalu_woo_courses' ),
						'options'		=> [
							'Onsite' 				=> __( 'Onsite', 'andalu_woo_courses' ),
							'Virtual' 			=> __( 'Virtual', 'andalu_woo_courses' ),
							'Face-to-Face' 	=> __( 'Face-to-Face', 'andalu_woo_courses' ),
						]
					] );

					woocommerce_wp_text_input( array(
						'id'          => '_course_duration',
						'label'       => __( 'Duration', 'andalu_woo_courses' ),
						'placeholder' => _x( 'e.g. 4 days', 'example duration', 'andalu_woo_courses' ),
					) );

					woocommerce_wp_text_input( array(
						'id'          => '_course_certification',
						'label'       => __( 'Certification', 'andalu_woo_courses' ),
						'placeholder' => _x( 'e.g. PRINCE2 Foundation Certification', 'example certification', 'andalu_woo_courses' ),
					) );

					woocommerce_wp_text_input( array(
						'id'          => '_course_pdus',
						'label'       => __( 'PDUs', 'andalu_woo_courses' ),
					) );

					woocommerce_wp_textarea_input( array(
						'id'          => '_course_audience',
						'label'       => __( 'Intended Audience', 'andalu_woo_courses' ),
						'style'       => 'min-height:200px',
					) );

					woocommerce_wp_text_input( array(
						'id'          => '_course_prerequisites',
						'label'       => __( 'Prerequisites', 'andalu_woo_courses' ),
					) );

					$endorsements = maybe_unserialize( get_post_meta( $post_id, '_course_endorsements', true ) );
					foreach ( Andalu_Woo_Courses::$endorsements as $endorsement ) {
						woocommerce_wp_checkbox( array(
							'id'    => '_course_endorsement_' . $endorsement,
							'label' => sprintf( __( '%s Endorsement', 'andalu_woo_courses' ), $endorsement ),
							'value' => empty( $endorsements[ $endorsement ] ) ? 'no' : 'yes',
						) );
					}

				?>
			</div>
		</div>

		<div id="course_links_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field">
					<label for="_course_parent"><?php _e( 'Parent Course', 'andalu_woo_courses' ); ?></label>
					<input type="hidden" class="wc-product-search" style="width: 50%;" id="_course_parent" name="_course_parent" data-placeholder="<?php esc_attr_e( 'Search for a course product&hellip;', 'andalu_woo_courses' ); ?>" data-action="woocommerce_json_search_course_products" data-allow_clear="true" data-multiple="false" data-exclude="<?php echo intval( $post_id ); ?>" data-selected="<?php
						$parent_id = absint( $post->post_parent );

						if ( $parent_id ) {
							$parent = wc_get_product( $parent_id );
							if ( is_object( $parent ) ) {
								$parent_title = wp_kses_post( html_entity_decode( $parent->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
							}

							echo esc_attr( $parent_title );
						}
					?>" value="<?php echo $parent_id ? $parent_id : ''; ?>" />
				</p>

				<p class="form-field">
					<label for="_course_study_guide"><?php _e( 'Study Guide', 'andalu_woo_courses' ); ?></label>
					<input type="hidden" class="wc-product-search" style="width: 50%;" id="_course_study_guide" name="_course_study_guide" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'andalu_woo_courses' ); ?>" data-action="woocommerce_json_search_products" data-allow_clear="true" data-multiple="false" data-exclude="<?php echo intval( $post_id ); ?>" data-selected="<?php
						$guide_id = absint( get_post_meta( $post_id, '_course_study_guide', true ) );

						if ( $guide_id ) {
							$guide = wc_get_product( $guide_id );
							if ( is_object( $guide ) ) {
								$guide_title = wp_kses_post( html_entity_decode( $guide->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
							}

							echo esc_attr( $guide_title );
						}
					?>" value="<?php echo $guide_id ? $guide_id : ''; ?>" />
				</p>

				<p class="form-field">
					<label for="_course_exam"><?php _e( 'Exam', 'andalu_woo_courses' ); ?></label>
					<input type="hidden" class="wc-product-search" style="width: 50%;" id="_course_exam" name="_course_exam" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'andalu_woo_courses' ); ?>" data-action="woocommerce_json_search_products" data-allow_clear="true" data-multiple="false" data-exclude="<?php echo intval( $post_id ); ?>" data-selected="<?php
						$exam_id = absint( get_post_meta( $post_id, '_course_exam', true ) );

						if ( $exam_id ) {
							$exam = wc_get_product( $exam_id );
							if ( is_object( $exam ) ) {
								$exam_title = wp_kses_post( html_entity_decode( $exam->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
							}

							echo esc_attr( $exam_title );
						}
					?>" value="<?php echo $exam_id ? $exam_id : ''; ?>" />
				</p>
			</div>
		</div>

		<div id="course_outline_data" class="panel wc-metaboxes-wrapper">
			<div class="toolbar toolbar-top">
				<span class="expand-close">
					<a href="#" class="expand_all"><?php _e( 'Expand', 'andalu_woo_courses' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'andalu_woo_courses' ); ?></a>
				</span>
				<button type="button" class="button add_course_outline"><?php _e( 'Add Outline', 'andalu_woo_courses' ); ?></button>
			</div>
			<div class="course_outlines wc-metaboxes">
				<?php
					$outlines = maybe_unserialize( get_post_meta( $post_id, '_course_outlines', true ) );

					if ( ! empty( $outlines ) ) {
						$outline_keys  = array_keys( $outlines );
						$outline_total = sizeof( $outline_keys );

						for ( $i = 0; $i < $outline_total; $i++ ) {

							$outline = $outlines[ $outline_keys[ $i ] ];
							$outline['position'] = empty( $outline['position'] ) ? 0  : $outline['position'];
							$outline['name']     = empty( $outline['name'] )     ? '' : $outline['name'];
							$outline['duration'] = empty( $outline['duration'] ) ? '' : $outline['duration'];
							$outline['content']  = empty( $outline['content'] )  ? '' : $outline['content'];

							include( plugin_dir_path( __FILE__ ) . '/../views/course-outline.php' );
						}
					}
				?>
			</div>
			<div class="toolbar">
				<span class="expand-close">
					<a href="#" class="expand_all"><?php _e( 'Expand', 'andalu_woo_courses' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'andalu_woo_courses' ); ?></a>
				</span>
				<button type="button" class="button save_course_outlines button-primary"><?php _e( 'Save Outlines', 'andalu_woo_courses' ); ?></button>
			</div>
		</div>

		<div id="course_classes_data" class="panel wc-metaboxes-wrapper">
			<div class="toolbar toolbar-top">
				<span class="expand-close">
					<a href="#" class="expand_all"><?php _e( 'Expand', 'andalu_woo_courses' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'andalu_woo_courses' ); ?></a>
				</span>
				<button type="button" class="button add_course_class"><?php _e( 'Add Class', 'andalu_woo_courses' ); ?></button>
			</div>
			<div class="course_classes wc-metaboxes">
				<?php self::output_classes( $post_id ); ?>
			</div>
			<div class="toolbar">
				<span class="expand-close">
					<a href="#" class="expand_all"><?php _e( 'Expand', 'andalu_woo_courses' ); ?></a> / <a href="#" class="close_all"><?php _e( 'Close', 'andalu_woo_courses' ); ?></a>
				</span>
				<button type="button" class="button save_course_classes button-primary"><?php _e( 'Save Classes', 'andalu_woo_courses' ); ?></button>
			</div>
		</div>
	<?php
	}

	public static function output_classes( $post_id ) {
		if ( ! empty( $post_id ) ) {
			// Specify product type in case the post is saved as auto-draft
			// Passing args to wc_get_product is deprecated. If you need to force a type, construct the product class directly.
			//$product = wc_get_product( $post_id, array( 'product_type' => Andalu_Woo_Courses::$product_type ) );
			$product = wc_get_product( $post_id );

			if( ! is_object( $product ) && 'course' != $product->get_type() )
				return;

			if ( ! empty( $product->course_classes ) ) {
				$class_ids  = array_values( $product->course_classes );
				$class_total = sizeof( $class_ids );
				$locations = Andalu_Woo_Courses_Class::get_locations();

				for ( $i = 0; $i < $class_total; $i++ ) {
					$class = wc_get_product( $class_ids[ $i ] );
					if( is_object( $class ) )
						include( plugin_dir_path( __FILE__ ) . '/../views/course-class.php' );
				}
			}
		}
	}

	public static function add_course_outline() {
		ob_start();

		check_ajax_referer( 'add-course-outline', 'security' );
		if ( ! current_user_can( 'edit_products' ) ) { die(-1); }

		$i = absint( $_POST['i'] );
		$outline = array(
			'position' => 0,
			'name'     => '',
			'duration' => '',
			'content'  => '',
		);

		include( plugin_dir_path( __FILE__ ) . '/../views/course-outline.php' );
		die();
	}

	public static function save_course_outlines() {
		check_ajax_referer( 'save-course-outlines', 'security' );
		if ( ! current_user_can( 'edit_products' ) ) { die(-1); }

		// Get post data
		parse_str( $_POST['data'], $data );
		$post_id = absint( $_POST['post_id'] );

		self::save_course_outline_data( $post_id, $data );

		die();
	}

	// Save Course Outlines
	private static function save_course_outline_data( $post_id, $data ) {
		$outlines = array();

		if ( ! empty( $data['outlines'] ) && is_array( $data['outlines'] ) ) {

			foreach( $data['outlines'] as $outline ) {
				$outline['position'] = intval( $outline['position'] );
				$outline['name']     = stripslashes( $outline['name'] );
				$outline['duration'] = stripslashes( $outline['duration'] );
				$outline['content']  = stripslashes( $outline['content'] );

				if ( ! empty( $outline['name'] ) ) {
					$outlines[] = $outline;
				}
			}
		}

		uasort( $outlines, __CLASS__ . '::items_cmp' );
		update_post_meta( $post_id, '_course_outlines', $outlines );
	}

	public static function add_course_class() {
		ob_start();

		check_ajax_referer( 'add-course-class', 'security' );
		if ( ! current_user_can( 'edit_products' ) ) { die(-1); }

		$i = absint( $_POST['i'] );
		$class = new stdClass();
		$class->position   = 0;
		$class->start_date = '';
		$class->end_date   = '';
		$class->time       = '';
		$class->location   = 0;
		$class->seats      = '';
		$class->confirmed  = false;

		$locations = Andalu_Woo_Courses_Class::get_locations();
		include( plugin_dir_path( __FILE__ ) . '/../views/course-class.php' );
		die();
	}

	public static function load_course_classes() {
		ob_start();

		check_ajax_referer( 'load-course-classes', 'security' );
		if ( ! current_user_can( 'edit_products' ) ) { die(-1); }

		$product_id = absint( $_POST['product_id'] );
		self::output_classes( $product_id );

		echo ob_get_clean();
		die();
	}

	public static function remove_course_class() {
		ob_start();

		check_ajax_referer( 'remove-course-class', 'security' );
		if ( ! current_user_can( 'edit_products' ) ) { die(-1); }

		if ( ! empty( $_POST['class_ids'] ) ) {
			foreach( (array) $_POST['class_ids'] as $id ) {
				$class = get_post( $id );

				if ( $class && 'course_class' == $class->post_type ) {
					wp_delete_post( $id );
				}

			}
		}

		die();
	}

	public static function save_course_classes() {
		check_ajax_referer( 'save-course-classes', 'security' );
		if ( ! current_user_can( 'edit_products' ) ) { die(-1); }

		// Get post data
		parse_str( $_POST['data'], $data );
		$post_id = absint( $_POST['post_id'] );

		self::save_course_classes_data( $post_id, $data );

		die();
	}

	// Save Course Classes
	private static function save_course_classes_data( $post_id, $data ) {
		$classes = array();

		// Extract class data
		if ( ! empty( $data['classes'] ) && is_array( $data['classes'] ) ) {
			$date_format = get_option( 'date_format' );

			foreach( $data['classes'] as $class ) {
				$class['id']         = intval( $class['id'] );
				$class['position']   = intval( $class['position'] );
				$class['start_date'] = empty( $class['start_date'] ) ? '' : strtotime( $class['start_date'] );
				$class['end_date']   = empty( $class['end_date'] ) ? '' : strtotime( $class['end_date'] );
				$class['time']       = stripslashes( $class['time'] );
				$class['seats']      = stripslashes( $class['seats'] );
				$class['location']   = intval( $class['location'] );

				if ( ! empty( $class['start_date'] ) ) {

					// Set class title
					$class['title'] = date( $date_format, $class['start_date'] );
					if ( ! empty( $class['end_date'] ) ) { $class['title'].= ' - ' . date( $date_format, $class['end_date'] ); }

					$classes[] = $class;
				}
			}
		}

		// Save each class
		foreach( $classes as $class ) {
			$class_id = empty( $class['id'] ) ? 0 : intval( $class['id'] );

			// Update or Add post
			if ( ! $class_id ) {

				$class_data = array(
					'post_title'   => $class['title'],
					'post_name'    => sanitize_title( $class['title'] ),
					'post_content' => '',
					'post_status'  => 'inherit',
					'post_author'  => get_current_user_id(),
					'post_parent'  => $post_id,
					'post_type'    => 'course_class',
					'menu_order'   => $class['position']
				);

				$class_id = wp_insert_post( $class_data );

				do_action( 'andalu_woo_courses_create_course_class', $class_id );

			} else {

				wp_update_post( array(
					'ID'         => $class_id,
					'post_title' => $class['title'],
					'post_name'  => sanitize_title( $class['title'] ),
					'menu_order' => $class['position']
				) );

				do_action( 'andalu_woo_courses_update_course_class', $class_id );

			}

			// Only continue if we have a class ID
			if ( ! $class_id ) {
				continue;
			}

			// Update post meta
			update_post_meta( $class_id, '_start_date', date( 'Y-m-d H:i:s', $class['start_date'] ) );
			update_post_meta( $class_id, '_end_date', empty( $class['end_date'] ) ? '' : date( 'Y-m-d H:i:s', $class['end_date'] ) );
			update_post_meta( $class_id, '_time', $class['time'] );

			update_post_meta( $class_id, '_seats', $class['seats'] );
			update_post_meta( $class_id, '_availability', $class['seats'] > 0 ? 'available' : 'full' );

			update_post_meta( $class_id, '_confirmed', $class['confirmed'] ? 'yes' : 'no' );

			// Update terms
			wp_set_object_terms( $class_id, intval( $class['location'] ), Andalu_Woo_Courses_Class::$location_taxonomy );
		}

		// Clear transients
		delete_transient( 'wc_product_classes_' . $post_id );
	}

	// Sort items
	private static function items_cmp( $a, $b ) {
		if ( $a['position'] == $b['position'] ) { return 0; }
		return ( $a['position'] < $b['position'] ) ? -1 : 1;
	}


	// Search for course products and return json
	public static function json_search_course_products() {
		ob_start();

		check_ajax_referer( 'search-products', 'security' );

		$term    = (string) wc_clean( stripslashes( $_GET['term'] ) );
		$exclude = array();

		if ( empty( $term ) ) {
			die();
		}

		if ( ! empty( $_GET['exclude'] ) ) {
			$exclude = array_map( 'intval', explode( ',', $_GET['exclude'] ) );
		}

		$found_products = array();

		$args = array(
			'post_type'        => 'product',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'orderby'          => 'title',
			'order'            => 'asc',
			'post_parent'      => 0,
			'suppress_filters' => 0,
			's'                => $term,
			'fields'           => 'ids',
			'exclude'          => $exclude,
			'tax_query'        => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => Andalu_Woo_Courses::$product_type,
				),
			),
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$product = wc_get_product( $post );

				if ( ! current_user_can( 'read_product', $post ) ) {
					continue;
				}

				$found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
			}
		}

		wp_send_json( $found_products );
	}



	// Load all necessary admin styles and scripts
	public static function enqueue_styles_scripts() {

		// Get admin screen id
		$screen = get_current_screen();
		$is_woocommerce_screen = ( in_array( $screen->id, array( 'product', 'edit-shop_order', 'shop_order', 'edit-shop_subscription', 'shop_subscription', 'users', 'woocommerce_page_wc-settings', 'woocommerce_page_class_rosters' ) ) ) ? true : false;

		if ( $is_woocommerce_screen ) {

			$dependencies = array( 'jquery' );

			$woocommerce_admin_script_handle = 'wc-admin-meta-boxes';

			$script_params = [];
			if ( $screen->id == 'product' ) {
				$dependencies[] = $woocommerce_admin_script_handle;
				$dependencies[] = 'wc-admin-product-meta-boxes';

				$script_params = array(
					'product_type'               => Andalu_Woo_Courses::$product_type,
					'add_course_outline_nonce'   => wp_create_nonce( 'add-course-outline' ),
					'save_course_outlines_nonce' => wp_create_nonce( 'save-course-outlines' ),
					'add_course_class_nonce'     => wp_create_nonce( 'add-course-class' ),
					'remove_course_class_nonce'  => wp_create_nonce( 'remove-course-class' ),
					'load_course_classes_nonce'  => wp_create_nonce( 'load-course-classes' ),
					'save_course_classes_nonce'  => wp_create_nonce( 'save-course-classes' ),
					'remove_outline'             => __( 'Remove this outline?', 'andalu_woo_courses' ),
					'remove_class'               => __( 'Remove this class?', 'andalu_woo_courses' ),
				);
			}

			wp_enqueue_script( 'andalu_woo_courses_admin', Andalu_Woo_Courses::$url . '/assets/js/course-admin.js', $dependencies, '1.0' );
			wp_localize_script( 'andalu_woo_courses_admin', 'Andalu_Woo_Courses', $script_params );

			wp_enqueue_style( 'andalu_woo_courses_admin', Andalu_Woo_Courses::$url . '/assets/css/course-admin.css', array(), '1.0' );
		}

	}

	public static function add_menu_pages() {
		$title = __( 'Class Rosters', 'andalu_woo_courses' );
		$page_hook = add_submenu_page( 'woocommerce', $title, $title, 'manage_woocommerce', 'class_rosters', __CLASS__ . '::roster_page' );

		// Make sure the list table is constructed (and actions processed) before the page's headers are sent
		add_action( 'load-' . $page_hook, __CLASS__ . '::get_list_table' );
	}

	public static function roster_page() {
		if ( ! empty( $_GET['class_id'] ) && intval( $_GET['class_id'] ) ) { return self::single_roster_page( intval( $_GET['class_id'] ) ); }

		$table = self::get_list_table();

		if ( isset( $_POST['s'] ) ) {
			$table->prepare_items( $_POST['s'] );
		} else {
			$table->prepare_items();
		}
	?>
	<div class="wrap">
		<div id="icon-woocommerce" class="icon32-woocommerce-users icon32"><br/></div>
		<h2><?php _e( 'Class Rosters', 'andalu_woo_courses' ); ?></h2>
		<?php $table->messages(); ?>
		<?php $table->views(); ?>
		<form method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $table->search_box( __( 'Search Courses', 'andalu_woo_courses' ), 'andalu_woo_courses_search' ); ?>
		</form>
		<form id="class-roster-filter" action="" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $table->display(); ?>
		</form>
	</div>
	<?php
	}

	public static function get_list_table() {
		if ( ! isset( self::$list_table ) ) {
			if ( ! class_exists( 'Andalu_Roster_List_Table' ) ) {
				require_once( 'class-rosters.php' );
			}
			self::$list_table = new Andalu_Roster_List_Table();
		}
		return self::$list_table;
	}

	public static function single_roster_page( $class_id ) {
		$course_class = new WC_Product_Course_Class( $class_id );
		if ( ! empty( $course_class ) && ! empty( $course_class->post ) ) {
	?>
		<div class="wrap">
			<h2><?php _e( 'Class Roster', 'andalu_woo_courses' ); ?> <span style="font-size: 14px;">(<a href="<?php menu_page_url( 'class_rosters', true ) ?>">&larr; All Rosters</a>)</span></h2>
			<p><?php printf( __( 'Course: %s', 'andalu_woo_courses' ), get_the_title( $course_class->post->post_parent ) ); ?></p>
			<p><?php printf( __( 'Dates: %s', 'andalu_woo_courses' ), get_the_title( $course_class->id ) ); ?></p>

			<?php
				global $wpdb;
				$sql = 'SELECT oi.order_item_id, oi.order_id FROM ' . $wpdb->posts . ' o';
				$sql .= ' INNER JOIN ' . $wpdb->prefix . 'woocommerce_order_items oi ON oi.order_id = o.ID AND oi.order_item_type = "line_item"';
				$sql .= ' INNER JOIN ' . $wpdb->prefix . 'woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id AND oim.meta_key = "_class_id"';
				$sql .= ' WHERE o.post_type = "shop_order" AND o.post_status = "wc-completed" AND oim.meta_value = ' . $class_id;
				$items = $wpdb->get_results( $sql );
				if ( ! empty( $items ) ) : ?>
					<table class="widefat fixed striped">
						<thead>
							<tr>
								<th><?php _e( 'Name', 'andalu_woo_courses' ); ?></th>
								<th><?php _e( 'Company', 'andalu_woo_courses' ); ?></th>
								<th><?php _e( 'Phone', 'andalu_woo_courses' ); ?></th>
								<th><?php _e( 'Email', 'andalu_woo_courses' ); ?></th>
								<th><?php _e( 'Options', 'andalu_woo_courses' ); ?></th>
							</tr>
						</thead>
						<?php foreach( $items as $item ) :
							$email = wc_get_order_item_meta( $item->order_item_id, 'Email', true );
						?>
						<tr>
							<td><?php echo wc_get_order_item_meta( $item->order_item_id, 'First Name', true ) . ' ' . wc_get_order_item_meta( $item->order_item_id, 'Last Name', true ); ?></td>
							<td><?php echo wc_get_order_item_meta( $item->order_item_id, 'Company', true ); ?></td>
							<td><?php echo wc_get_order_item_meta( $item->order_item_id, 'Phone', true ); ?></td>
							<td><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
							<td><a href="<?php echo get_edit_post_link( $item->order_id ); ?>" target="_blank">View Order #<?php echo $item->order_id ?></a></td>
						</tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>
		</div>
	<?php
		} else {
			echo '<p>' . __( 'No class roster found', 'andalu_woo_courses' ) . '</p>';
		}
	}

}
Andalu_Woo_Courses_Admin::init();