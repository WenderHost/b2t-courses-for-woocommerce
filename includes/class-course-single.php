<?php

// Handles course product single view


class Andalu_Woo_Courses_Single {
	static $posted;

	static function init() {
		// Register rewrite rules for course registration
		add_action( 'init', __CLASS__ . '::rewrite_rule' );
		add_filter( 'query_vars', __CLASS__ . '::query_vars' );

		// Load correct template for course registration
		add_filter( 'wc_get_template_part', __CLASS__ . '::registration_template', 10, 3 );
		add_filter( 'woocommerce_locate_template', __CLASS__ . '::locate_template', 10, 3 );

		// Customize single product view
		add_action( 'woocommerce_before_single_product', __CLASS__ . '::course_product' );
		add_action( 'woocommerce_before_single_course_register', __CLASS__ . '::course_product_register' );

		// Load all necessary styles and scripts
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_styles_scripts' );


		// Load cart data per page load
		add_filter( 'woocommerce_get_cart_item_from_session', __CLASS__ . '::get_cart_item_from_session', 20, 2 );

		// Add item data to the cart
		add_filter( 'woocommerce_add_cart_item_data', __CLASS__ . '::add_cart_item_data', 10, 2 );

		// Validate when adding to cart
		add_filter( 'woocommerce_add_to_cart_validation', __CLASS__ . '::validate_add_cart_item', 10, 3 );

		// Get item data to display in cart
		add_filter( 'woocommerce_get_item_data', __CLASS__ . '::get_item_data', 10, 2 );

		// Sell individually
		add_filter( 'woocommerce_is_sold_individually', __CLASS__ . '::sold_individually', 10, 2 );

		// Check for unavailable items in the cart
		add_action( 'woocommerce_cart_loaded_from_session', __CLASS__ . '::unavailable' );

	}

	// Register rewrite rules for course registration
	public static function rewrite_rule() {
		$permalinks = get_option( 'woocommerce_permalinks' );
		if ( empty( $permalinks['product_regex'] ) ) {

			// Default product rewrite rules
			add_rewrite_rule( 'product/([^/]+)/register/?$', 'index.php?product=$matches[1]&course_register=virtual' , 'top' );
			add_rewrite_rule( 'product/([^/]+)/register/([^/]+)/?$', 'index.php?product=$matches[1]&course_register=$matches[2]' , 'top' );

		} else {

			// Rewrite rules with product_base
			$parts = intval( $permalinks['product_regex_parts'] );
			add_rewrite_rule( $permalinks['product_regex'] . 'register/?$', 'index.php?product=$matches[' . $parts . ']&course_register=virtual' , 'top' );
			add_rewrite_rule( $permalinks['product_regex'] . 'register/([^/]+)/?$', 'index.php?product=$matches[' . $parts . ']&course_register=$matches[' . ( 1 + $parts ) . ']' , 'top' );

		}
	}

	// Register query var for course registration
	public static function query_vars( $query_vars ) {
		$query_vars[] = 'course_register';
		return $query_vars;
	}

	// Load correct template for course registration
	public static function registration_template( $template, $slug, $name ) {
		global $product;

		if ( 'content' == $slug && 'single-product' == $name && get_query_var( 'course_register' ) && $product->is_type( Andalu_Woo_Courses::$product_type ) ) {

			// Locate template (theme is priority)
			$template = locate_template( 'content-single-product-register.php' );
			if ( ! $template ) {
				return trailingslashit( Andalu_Woo_Courses::$dir ) . 'templates/content-single-product-register.php';
			}
		}

		return $template;
	}

	// Load templates from plugin if they are available
	public static function locate_template( $template, $template_name, $template_path ) {
		$_template = $template;

		if ( ! $template_path ) $template_path = WC()->template_url;
		$plugin_path = Andalu_Woo_Courses::$dir . '/templates/';

		// Look within passed path within the theme - this is priority
		$template = locate_template( array( $template_path . $template_name, $template_name ) );

		// Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;

		// Use default template
		if ( ! $template )
			$template = $_template;

		return $template;
	}

	// Virtual registration form content
	public static function virtual_registration_form() {
		global $product, $post;
		$parent_product_post = $post;

		echo wpautop( sprintf(
			__( 'You are registering for the LIVE Virtual version of %s. Please choose your times for the following components of this course:', 'andalu_woo_courses' ),
			get_the_title()
		) );

		foreach( $product->children as $child_id ) {
			$product = wc_get_product( $child_id );
			$post = $product->post;
			setup_postdata( $post );

			self::class_table( true );
		}

		// Reset to parent product
		$post    = $parent_product_post;
		$product = wc_get_product( $parent_product_post->ID );
		setup_postdata( $parent_product_post );

	}

	// Registration fields
	public static function registration_fields() {
		return array(
			'first_name' => array(
				'label'       => __( 'First Name', 'andalu_woo_courses' ),
				'required'    => true,
				'class'       => array( 'form-row-first' ),
			),
			'last_name' => array(
				'label'       => __( 'Last Name', 'andalu_woo_courses' ),
				'required'    => true,
				'class'       => array( 'form-row-last' ),
				'clear'       => true,
			),
			'company' => array(
				'label'       => __( 'Company', 'andalu_woo_courses' ),
				'class'       => array( 'form-row-wide' ),
			),
			'title' => array(
				'label'       => __( 'Title', 'andalu_woo_courses' ),
				'class'       => array( 'form-row-wide' ),
			),
			'country' => array(
				'label'       => __( 'Country', 'andalu_woo_courses' ),
				'required'    => true,
				'type'        => 'country',
				'class'       => array( 'form-row-wide', 'address-field' ),
			),
			'address_1' => array(
				'label'       => __( 'Address', 'andalu_woo_courses' ),
				'placeholder' => __( 'Street address', 'andalu_woo_courses' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' ),
			),
			'address_2' => array(
				'placeholder' => __( 'Apartment, suite, unit etc. (optional)', 'andalu_woo_courses' ),
				'required'    => false,
				'class'       => array( 'form-row-wide', 'address-field' ),
			),
			'billing_city' => array(
				'label'       => __( 'Town / City', 'andalu_woo_courses' ),
				'placeholder' => __( 'Town / City', 'andalu_woo_courses' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' ),
			),
			'billing_state' => array(
				'label'       => __( 'State / County', 'andalu_woo_courses' ),
				'type'        => 'state',
				'required'    => true,
				'class'       => array( 'form-row-first', 'address-field' ),
			),
			'billing_postcode' => array(
				'label'       => __( 'Postcode / Zip', 'andalu_woo_courses' ),
				'placeholder' => __( 'Postcode / Zip', 'andalu_woo_courses' ),
				'required'    => true,
				'class'       => array( 'form-row-last', 'address-field' ),
				'clear'       => true,
				'validate'    => array( 'postcode' ),
			),
			'phone' => array(
				'label'       => __( 'Phone', 'andalu_woo_courses' ),
				'required'    => true,
				'type'        => 'tel',
				'class'       => array( 'form-row-wide' ),
				'validate'    => array( 'phone' ),
			),
			'email' => array(
				'label'       => __( 'Email', 'andalu_woo_courses' ),
				'required'    => true,
				'type'        => 'email',
				'class'       => array( 'form-row-first' ),
				'validate'    => array( 'email' ),
			),
			'email_again' => array(
				'label'       => __( 'Email Again', 'andalu_woo_courses' ),
				'required'    => true,
				'type'        => 'email',
				'class'       => array( 'form-row-last' ),
				'validate'    => array( 'email' ),
				'clear'       => true,
			),
			'optin' => array(
				'label'       => __( 'I am interested in participating in the B2T Business Analyst Certification Program.', 'andalu_woo_courses' ),
				'type'        => 'checkbox',
				'class'       => array( 'form-row-wide' ),
			),
		);
	}

	// Registration form content
	public static function registration_form() {
		wp_enqueue_script( 'andalu_woo_courses_registration' );
	?>

		<?php if ( 'virtual' == get_query_var( 'course_register' ) ) : ?>

		<p><?php _e( 'NOTE - Valid Shipping Address Required: The following registration is for a Live Virtual Class. Be sure to include a valid shipping address (no P.O. Boxes) where we can ship course materials for the student you are registering.', 'andalu_woo_courses' ); ?></p>

		<?php endif; ?>

		<input type="hidden" name="course_registration" value="<?php echo get_query_var( 'course_register' ); ?>" />

		<?php
			foreach ( self::registration_fields() as $key => $field ) {
				$value = ( isset( $_POST[ $key ] ) ? $_POST[ $key ] : ( $key == 'country' ? WC()->checkout->get_value( 'billing_country' ) : '' ) );
				woocommerce_form_field( $key, $field, $value );
			}
		?>

		<p><span class="required">*</span> <?php _e( 'indicates a required field', 'andalu_woo_courses' ); ?></p>
		<p><?php _e( ' (Note: State/Province and Zip Code are not required for students outside of the US and Canada.)', 'andalu_woo_courses' ); ?></p>

	<?php
	}


	// Customize product view for courses
	public static function course_product() {
		global $product;
		if ( ! $product->is_type( Andalu_Woo_Courses::$product_type ) ) return;

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );

		add_action( 'woocommerce_single_product_summary', __CLASS__ . '::product_meta', 7 );
		add_action( 'woocommerce_after_single_product_summary', __CLASS__ . '::class_table', 7 );
		add_action( 'woocommerce_after_single_product_summary', __CLASS__ . '::sub_class_table', 7 );

		// Add course outline tab
		add_filter( 'woocommerce_product_tabs', __CLASS__ . '::product_tabs' );

		// Change product description heading
		add_filter( 'woocommerce_product_description_heading', __CLASS__ . '::description_tab_heading' );
	}

	// Customize product view for courses registration
	public static function course_product_register() {
		add_action( 'woocommerce_course_before_registration_form', 'woocommerce_template_single_title', 10 );

		// Registration form content
		add_action( 'woocommerce_course_virtual_registration_form', __CLASS__ . '::virtual_registration_form' );
		add_action( 'woocommerce_course_registration_form', __CLASS__ . '::registration_form' );
	}

	public static function product_meta() {
		global $product;
	?>
		<table class="course_meta product_meta">
			<tr>
				<td class="meta_label"><?php _e( 'Length', 'andalu_woo_courses' ); ?></td>
				<td class="meat_value"><?php echo $product->course_duration; ?></td>
			</tr>
			<tr>
				<td class="meta_label"><?php _e( 'PDUs', 'andalu_woo_courses' ); ?></td>
				<td class="meta_value"><?php echo $product->course_pdus; ?></td>
			</tr>
			<tr>
				<td class="meta_label"><?php _e( 'Intended Audience', 'andalu_woo_courses' ); ?></td>
				<td class="meta_value"><?php echo wpautop( $product->course_audience ); ?></td>
			</tr>
			<tr>
				<td class="meta_label"><?php _e( 'Prerequisites', 'andalu_woo_courses' ); ?></td>
				<td class="meta_value"><?php echo $product->course_prerequisites; ?></td>
			</tr>

			<?php if ( ! empty( $product->course_study_guide ) ) : ?>
			<tr>
				<td class="meta_label"><?php _e( 'Study Guide', 'andalu_woo_courses' ); ?></td>
				<td class="meta_value"><?php printf( '<a href="%s">%s</a>', get_permalink( $product->course_study_guide ), get_the_title( $product->course_study_guide ) ); ?></td>
			</tr>
			<?php endif; ?>


			<?php foreach( $product->course_endorsements as $label => $value ) : ?>
			<?php if ( ! $value ) continue; ?>
			<tr>
				<td class="meta_label" colspan="2"><?php printf( __( '%s endorsement', 'andalu_woo_courses' ), $label ); ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	<?php
	}

	public static function sub_class_table( $select = false ) {
		global $product;

		if ( $product->has_child() ) : ?>

		<div class="sub_courses_schedule">
			<h2><?php the_title(); ?></h2>
			<h3><?php _e( 'Sub Courses', 'andalu_woo_courses' ); ?></h3>

			<?php foreach( $product->get_children() as $child_id ) { self::class_table( $select, $child_id ); } ?>

		</div>

		<?php endif;
	}

	public static function class_table( $select = false, $product = false ) {

		// Use global product if one is not provided
		if ( ! $product ) { global $product; }

		if ( ! is_object( $product ) ) { $product = wc_get_product( $product ); }

		require_once( Andalu_Woo_Courses::$dir . '/lib/http_build_url.php' );
		$date_format = get_option( 'date_format' );
		$locations = Andalu_Woo_Courses_Class::get_locations();

		$css_classes = $select ? ' selectable' : '';

	?>
	<div class="course_schedule<?php echo $css_classes; ?>">
		<div class="schedule_header">
			<div class="course_title">
				<a href="<?php echo get_the_permalink( $product->id ); ?>"><h2><?php echo get_the_title( $product->id ); ?></h2></a>
				<?php if ( ! empty( $product->course_duration ) ) : ?>
					<span class="course_duration"><?php printf( __( 'Course Length: %s', 'andalu_woo_courses' ), $product->course_duration ); ?></span>
				<?php endif; ?>
			</div>
			<div class="course_cost"><?php echo $product->get_price_html(); ?></div>
		</div>

		<?php if ( $product->has_child() ) :
			$url = parse_url( get_permalink() );
			$url['path'] = trailingslashit( $url['path'] ) . 'register/';
			$select_dates = http_build_url( $url );
		?>
		<table class="course_classes live_classes">
			<tr>
				<td class="date"><?php _e( 'Various dates', 'andalu_woo_courses' ); ?></td>
				<td class="location"><?php _e( 'Live Virtual Course', 'andalu_woo_courses' ); ?></td>
				<td class="register"><a href="<?php echo $select_dates; ?>" class="btn btn-default"><?php _e( 'Select Dates', 'andalu_woo_courses' ); ?></a></td>
			</tr>
		</table>
		<?php endif; ?>

		<?php if ( ! $select && $product->has_child() ) : ?>
		<h4><?php _e( 'On site classes', 'andalu_woo_courses' ); ?></h4>
		<?php endif; ?>

		<?php	if ( ! empty( $product->course_classes ) ) : ?>
		<table class="course_classes onsite_classes">
			<?php foreach( $product->course_classes as $class_id ) :
				$class = wc_get_product( $class_id );
				if ( empty( $class ) ) continue;

				$class_dates = date( $date_format, $class->start_timestamp );
				if ( ! empty( $class->end_timestamp ) ) { $class_dates .= ' - ' . date( $date_format, $class->end_timestamp ); }
				$class_dates = apply_filters( 'andalu_woo_courses_class_dates', $class_dates, $class->start_timestamp, $class->end_timestamp, $date_format );

				$url = parse_url( get_the_permalink( $product->id ) );
				$url['path'] = trailingslashit( $url['path'] ) . 'register/' . $class->post->post_name;
				$class_registration = http_build_url( $url );

				$selected_class = empty( $_REQUEST['course_select'][ $product->id ] ) ? 0 : $_REQUEST['course_select'][ $product->id ];
			?>
			<tr>
				<?php if ( $select ) : ?>
				<td class="select">
					<?php if ( $class->is_available() ) : ?>
					<input type="radio" name="course_select[<?php echo $product->id; ?>]" value="<?php echo $class_id; ?>" <?php checked( $selected_class, $class_id ); ?> />
					<?php else : ?>
					<span class="full"><?php _e( 'Full', 'andalu_woo_courses' ); ?></span>
					<?php endif; ?>
				</td>
				<?php endif; ?>

				<td class="date"><?php echo $class_dates; ?></td>

				<td class="location"><?php if ( ! empty( $locations[ $class->location ] ) ) { echo $locations[ $class->location ]; } ?></td>

				<?php if ( ! empty( $class->time ) ) : ?>
				<td class="time"><?php echo $class->time; ?></td>
				<?php endif; ?>

				<?php if ( ! $select ) : ?>
				<td class="register">
					<?php if ( $class->is_available() ) : ?>
					<a href="<?php echo $class_registration; ?>" class="btn btn-default"><?php _e( 'Register', 'andalu_woo_courses' ); ?></a>
					<?php else : ?>
					<span class="full"><?php _e( 'This class is full', 'andalu_woo_courses' ); ?></span>
					<?php endif; ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>
	</div>
	<?php
	}


	public static function product_tabs( $tabs ) {
		global $product;

		if ( ! empty( $product->course_outlines ) ) {
			$tabs['course_outline'] = array(
				'title' 	=> __( 'Outline', 'andalu_woo_courses' ),
				'priority' 	=> 20,
				'callback' 	=> __CLASS__ . '::course_outline_tab'
			);
		}

		return $tabs;
	}

	public static function description_tab_heading( $heading ) {
		return get_the_title();
	}

	public static function course_outline_tab() {
		global $product;

		echo '<h2>' . __( 'Course Outline', 'andalu_woo_courses' ) . '</h2>';

		foreach( $product->course_outlines as $outline ) : ?>

		<div class="course_outline">
			<h3><?php echo $outline['name']; ?><?php if ( ! empty( $outline['duration'] ) ) { echo '<span class="outline_duration">' . $outline['duration'] . '</span>'; } ?></h3>
			<?php echo wpautop( $outline['content'] ); ?>
		</div>

		<?php endforeach;

	}

	// Load all necessary styles and scripts
	public static function enqueue_styles_scripts() {
		global $post, $product;

		$product = wc_get_product( $post->ID );
		if ( ! empty( $product ) && $product->is_type( Andalu_Woo_Courses::$product_type ) ) {
			wp_enqueue_style( 'andalu_woo_courses', Andalu_Woo_Courses::$url . '/assets/css/course.css', array(), '1.0' );
			wp_register_script( 'andalu_woo_courses_registration', Andalu_Woo_Courses::$url . '/assets/js/course-registration.js', array( 'wc-country-select', 'wc-address-i18n' ), '1.0' );
		}

	}

	public static function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['course_registration'] ) ) {
			$cart_item['course_registration'] = $values['course_registration'];
		}
		return $cart_item;
	}

	protected static function validate_virtual_classes( $post_data ) {
		$product = empty( $post_data['add-to-cart'] ) ? false : wc_get_product( $post_data['add-to-cart'] );

		if ( $product && $product->has_child() ) {
			foreach( $product->get_children() as $child_id ) {
				if ( empty( $post_data['course_select'][ $child_id ] ) ) {
					wc_add_notice( sprintf( __( 'You must select a class for "%s".', 'andalu_woo_courses' ), get_the_title( $child_id ) ), 'error' );
				} else {
					self::$posted['courses'][ $child_id ] = $post_data['course_select'][ $child_id ];
				}
			}
		} else {
			wc_add_notice( __( 'Invalid virtual course.', 'andalu_woo_courses' ), 'error' );
		}

	}

	protected static function validate_registration_fields( $post_data ) {

		// Get registration fields and do validation
		foreach ( self::registration_fields() as $key => $field ) {

			if ( ! isset( $field['type'] ) ) { $field['type'] = 'text'; }

			// Get Value
			switch ( $field['type'] ) {
				case "checkbox" :
					self::$posted[ $key ] = isset( $post_data[ $key ] ) ? 1 : 0;
				break;
				case "multiselect" :
					self::$posted[ $key ] = isset( $post_data[ $key ] ) ? implode( ', ', array_map( 'wc_clean', $post_data[ $key ] ) ) : '';
				break;
				case "textarea" :
					self::$posted[ $key ] = isset( $post_data[ $key ] ) ? wp_strip_all_tags( wp_check_invalid_utf8( stripslashes( $post_data[ $key ] ) ) ) : '';
				break;
				default :
					self::$posted[ $key ] = isset( $post_data[ $key ] ) ? ( is_array( $post_data[ $key ] ) ? array_map( 'wc_clean', $post_data[ $key ] ) : wc_clean( $post_data[ $key ] ) ) : '';
				break;
			}

			// Validation: Required fields
			if ( isset( $field['required'] ) && $field['required'] && empty( self::$posted[ $key ] ) ) {
				wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is a required field.', 'andalu_woo_courses' ), 'error' );
			}

			if ( ! empty( self::$posted[ $key ] ) ) {

				// Validation rules
				if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					foreach ( $field['validate'] as $rule ) {
						switch ( $rule ) {
							case 'postcode' :
								self::$posted[ $key ] = strtoupper( str_replace( ' ', '', self::$posted[ $key ] ) );

								if ( ! WC_Validation::is_postcode( self::$posted[ $key ], $post_data[ 'country' ] ) ) :
									wc_add_notice( __( 'Please enter a valid postcode/ZIP.', 'andalu_woo_courses' ), 'error' );
								else :
									self::$posted[ $key ] = wc_format_postcode( self::$posted[ $key ], $post_data[ 'country' ] );
								endif;
							break;
							case 'phone' :
								self::$posted[ $key ] = wc_format_phone_number( self::$posted[ $key ] );

								if ( ! WC_Validation::is_phone( self::$posted[ $key ] ) )
									wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'andalu_woo_courses' ), 'error' );
							break;
							case 'email' :
								self::$posted[ $key ] = strtolower( self::$posted[ $key ] );

								if ( ! is_email( self::$posted[ $key ] ) )
									wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'andalu_woo_courses' ), 'error' );

								if ( $key == 'email_again' && self::$posted[ $key ] != self::$posted[ 'email' ] )
									wc_add_notice( __( 'The email addresses do not match.', 'andalu_woo_courses' ), 'error' );
							break;
							case 'state' :
								// Get valid states
								$valid_states = WC()->countries->get_states( isset( $post_data[ 'country' ] ) ? $post_data[ 'country' ] : WC()->customer->get_country() );

								if ( ! empty( $valid_states ) && is_array( $valid_states ) ) {
									$valid_state_values = array_flip( array_map( 'strtolower', $valid_states ) );

									// Convert value to key if set
									if ( isset( $valid_state_values[ strtolower( self::$posted[ $key ] ) ] ) ) {
										 self::$posted[ $key ] = $valid_state_values[ strtolower( self::$posted[ $key ] ) ];
									}
								}

								// Only validate if the country has specific state options
								if ( ! empty( $valid_states ) && is_array( $valid_states ) && sizeof( $valid_states ) > 0 ) {
									if ( ! in_array( self::$posted[ $key ], array_keys( $valid_states ) ) ) {
										wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not valid. Please enter one of the following:', 'andalu_woo_courses' ) . ' ' . implode( ', ', $valid_states ), 'error' );
									}
								}
							break;
						}
					}
				}
			}
		}
	}

	public static function add_cart_item_data( $cart_item_meta, $product_id, $post_data = null ) {
		if ( is_null( $post_data ) ) { $post_data = $_POST;	}

		if ( ! empty( $post_data['course_registration'] ) ) {

			if ( 'virtual' == $post_data['course_registration'] ) {
				$class_id = 0;
				self::validate_virtual_classes( $post_data );
			} else {
				$class_id = Andalu_Woo_Courses_Class::get_class_id( $post_data['course_registration'] );
			}

			self::validate_registration_fields( $post_data );
			if ( ! wc_notice_count( 'error' ) ) {
				$cart_item_meta['course_registration'] = self::$posted;
				if ( $class_id ) { $cart_item_meta['course_registration']['class'] = $class_id; }

				// Clear course registration fields
				foreach( array( 'first_name', 'last_name', 'email', 'email_again' ) as $key ) {
					unset( $_POST[ $key ] );
				}
			}

		}
		return $cart_item_meta;
	}

	static function validate_add_cart_item( $passed, $product_id, $qty, $post_data = null ) {
		if ( is_null( $post_data ) ) { $post_data = $_POST; }

		if ( ! empty( $post_data['course_registration'] ) ) {

			if ( 'virtual' == $post_data['course_registration'] ) {
				self::validate_virtual_classes( $post_data );
			} else {
				$class_id = Andalu_Woo_Courses_Class::get_class_id( $post_data['course_registration'] );
				$class = wc_get_product( $class_id );
				if ( $class && ! $class->is_available() ) {
					wc_add_notice( __( 'Sorry, this class is not available for registration', 'andalu_woo_courses' ), 'error' );
				}
			}

			self::validate_registration_fields( $post_data );
			if ( wc_notice_count( 'error' ) ) {
				return false;
			}

			$cart_item_meta['course_registration'] = self::$posted;

		}
		return $passed;
	}

	static function get_item_data( $other_data, $cart_item ) {
		if ( ! empty( $cart_item['course_registration'] ) ) {

			if ( ! empty( $cart_item['course_registration']['class'] ) ) {
				$other_data[] = array(
					'name'    => __( 'Class', 'andalu_woo_courses' ),
					'value'   => $cart_item['course_registration']['class'],
					'display' => get_the_title( $cart_item['course_registration']['class'] ),
				);

				$class = wc_get_product( $cart_item['course_registration']['class'] );
				if ( $class && 'Virtual' != $class->get_location() ) {
					$other_data[] = array(
						'name'    => __( 'Location', 'andalu_woo_courses' ),
						'display' => $class->get_location(),
					);
				}
			}

			if ( ! empty( $cart_item['course_registration']['courses'] ) ) {
				$classes = array();
				foreach( $cart_item['course_registration']['courses'] as $course_id => $class_id ) {
					$classes[] = get_the_title( $course_id ) . ' - ' . get_the_title( $class_id );
				}

				$other_data[] = array(
					'name'    => __( 'Classes', 'andalu_woo_courses' ),
					'value'   => $cart_item['course_registration']['courses'],
					'display' => implode( '<br/>', $classes ),
				);
			}

			foreach ( self::registration_fields() as $key => $field ) {

				// Skip display of several fields
				if ( in_array( $key, array( 'email_again', 'optin' ) ) ) { continue; }

				if ( ! empty( $cart_item['course_registration'][ $key ] ) ) {
					$other_data[] = array(
						'name'  => $field['label'],
						'value' => $cart_item['course_registration'][ $key ],
					);
				}
			}

		}
		return $other_data;
	}

	// Sell individually
	static function sold_individually( $return, $product ) {
		if ( $product->is_type( Andalu_Woo_Courses::$product_type ) ) {
			return true;
		}
		return $return;
	}

	public static function unavailable() {
		$cart = WC()->cart;
		foreach ( $cart->cart_contents as $cart_id => $item ) {

			// Skip other items
			if ( empty( $item['course_registration']['class'] ) ) { continue; }

			$class = wc_get_product( $item['course_registration']['class'] );
			if ( $class && ! $class->is_available( true ) ) {
				self::remove_unavailable_item( $cart_id, $cart );
			}
		}
	}

	protected static function remove_unavailable_item( $cart_id, $cart = null ) {
		$expired_cart_notice = sprintf( __( "Sorry, '%s' was removed from your cart because it is no longer available.", 'andalu_woo_courses' ), $cart->cart_contents[ $cart_id ][ 'data' ]->get_title() );
		wc_add_notice( $expired_cart_notice, 'error' );
		unset( $cart->cart_contents[ $cart_id ] );
		WC()->session->set( 'cart', $cart->cart_contents );
	}


}
Andalu_Woo_Courses_Single::init();