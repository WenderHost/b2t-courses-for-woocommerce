<?php

class WC_Product_Course extends WC_Product {

	var $course_duration;
	var $course_pdus;
	var $course_audience;
	var $course_prerequisites;
	var $course_endorsements;

	var $course_outlines;
	var $course_classes;

	var $children;

	public function __construct( $product ) {
		$this->product_type = Andalu_Woo_Courses::$product_type;
		parent::__construct( $product );

		// Load all meta fields
		$this->product_custom_fields = get_post_meta( $this->id );

		// Convert selected meta fields for easy access
		if ( ! empty( $this->product_custom_fields['_course_duration'][0] ) ) {
			$this->course_duration = $this->product_custom_fields['_course_duration'][0];
		}
		if ( ! empty( $this->product_custom_fields['_course_pdus'][0] ) ) {
			$this->course_pdus = $this->product_custom_fields['_course_pdus'][0];
		}
		if ( ! empty( $this->product_custom_fields['_course_audience'][0] ) ) {
			$this->course_audience = $this->product_custom_fields['_course_audience'][0];
		}
		if ( ! empty( $this->product_custom_fields['_course_prerequisites'][0] ) ) {
			$this->course_prerequisites = $this->product_custom_fields['_course_prerequisites'][0];
		}

		$this->course_endorsements = ( ! isset( $this->product_custom_fields['_course_endorsements'][0] ) ) ? array() : maybe_unserialize( $this->product_custom_fields['_course_endorsements'][0] );

		$this->course_outlines = ( ! isset( $this->product_custom_fields['_course_outlines'][0] ) ) ? array() : maybe_unserialize( $this->product_custom_fields['_course_outlines'][0] );

		// Load course classes
		$this->get_classes();

		// Load sub courses
		$this->get_children();
	}

	/**
	 * Return the products children posts.
	 *
	 * @access public
	 * @return array
	 */
	public function get_children() {
		if ( ! is_array( $this->children ) || empty( $this->children ) ) {
        	$transient_name = 'wc_product_children_' . $this->id;
			$this->children = array_filter( array_map( 'absint', (array) get_transient( $transient_name ) ) );

        	if ( empty( $this->children ) ) {

        		$args = apply_filters( 'woocommerce_course_children_args', array(
        			'post_parent' 	=> $this->id,
        			'post_type'		=> 'product',
        			'orderby'		=> 'menu_order',
        			'order'			=> 'ASC',
        			'fields'       => 'ids',
        			'post_status'	=> 'publish',
        			'numberposts'	=> -1,
        		) );

				$this->children = get_posts( $args );
				set_transient( $transient_name, $this->children, DAY_IN_SECONDS * 30 );
			}
		}
		return (array) $this->children;
	}

	/**
	 * Returns whether or not the product has any child product.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_child() {
		return sizeof( $this->get_children() ) ? true : false;
	}

	/**
	 * Return the product's `course_class` CPTs.
	 *
	 * Will only return classes that have a `_start_date`
	 * >= NOW.
	 *
	 * @access public
	 * @return array
	 */
	public function get_classes() {
		if ( ! is_array( $this->course_classes ) || empty( $this->course_classes ) ) {
        	$transient_name = 'wc_product_classes_' . $this->id;
			$this->course_classes = array_filter( array_map( 'absint', (array) get_transient( $transient_name ) ) );

        	if ( empty( $this->course_classes ) ) {

        		$args = apply_filters( 'woocommerce_course_classes_args', array(
        			'post_parent' 	=> $this->id,
        			'post_type'		=> 'course_class',
        			'orderby'		=> 'menu_order',
        			'order'			=> 'ASC',
        			'fields'        => 'ids',
        			'post_status'	=> 'inherit',
        			'numberposts'	=> -1,
        		) );
        		if( ! is_admin() ){
        			$args['meta_query'] = array(
						array(
							'key' => '_start_date',
							'value' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
							'compare' => '>=',
							'type' => 'DATE',
						),
    				);
	        	}
				$this->course_classes = get_posts( $args );
				set_transient( $transient_name, $this->course_classes, DAY_IN_SECONDS * 30 );
			}
		}
		return (array) $this->course_classes;
	}

	/**
	 * Returns whether or not the product has any child class.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_classes() {
		return sizeof( $this->get_classes() ) ? true : false;
	}

}