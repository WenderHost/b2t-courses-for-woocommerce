<?php

class WC_Product_Course_Class extends WC_Product {

	var $position = 0;
	var $start_timestamp = 0;
	var $start_date = '';
	var $end_timestamp = 0;
	var $end_date = '';
	var $time = '';

	var $seats = 0;
	var $confirmed = false;

	var $location = 0;
	var $location_term = false;

	var $virtual = false;

	public function __construct( $product ) {
		$this->product_type = 'course_class';
		//parent::__construct( $product ); // ???
		if ( is_numeric( $product ) && $product > 0 ) {
			$this->set_id( $product );
		} elseif ( $product instanceof self ) {
			$this->set_id( absint( $product->get_id() ) );
		} elseif ( ! empty( $product->ID ) ) {
			$this->set_id( absint( $product->ID ) );
		} else {
			$this->set_object_read( true );
		}

		// Set Data Store
		$this->data_store = WC_Data_Store::load( 'course_class' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}

		// Load position from post menu_order
		$post = get_post( $this->get_id() );
		$this->position = empty( $post->menu_order ) ? 0 : $post->menu_order;

		// Load all meta fields
		$this->product_custom_fields = get_post_meta( $this->get_id() );

		// Convert selected meta fields for easy access
		if ( ! empty( $this->product_custom_fields['_start_date'][0] ) ) {
			$this->start_timestamp = strtotime( $this->product_custom_fields['_start_date'][0] );
			$this->start_date = empty( $this->start_timestamp ) ? '' : date( 'Y-m-d', $this->start_timestamp );
		}

		if ( ! empty( $this->product_custom_fields['_end_date'][0] ) ) {
			$this->end_timestamp = strtotime( $this->product_custom_fields['_end_date'][0] );
			$this->end_date = empty( $this->end_timestamp ) ? '' : date( 'Y-m-d', $this->end_timestamp );
		}

		if ( ! empty( $this->product_custom_fields['_time'][0] ) ) {
			$this->time = $this->product_custom_fields['_time'][0];
		}

		if ( ! empty( $this->product_custom_fields['_seats'][0] ) ) {
			$this->seats = $this->product_custom_fields['_seats'][0];
		}

		if ( ! empty( $this->product_custom_fields['_confirmed'][0] ) ) {
			$this->confirmed = 'yes' == $this->product_custom_fields['_confirmed'][0] ? true : false;
		}

		// Load terms
		$this->location = $this->get_location( 'term_id' );

		// Is this a virtual class
		$this->virtual = ( $this->location_term && 'Virtual' == $this->location_term->name );

	}

	// Get location
	public function get_location( $field = 'name' ) {

		if ( empty( $this->location_term ) ) {
			$terms = get_the_terms( $this->get_id(), Andalu_Woo_Courses_Class::$location_taxonomy );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach( $terms as $this->location_term ) { break; } // Only get first term
			}
		}

		if ( ! empty( $this->location_term ) && isset( $this->location_term->$field ) ) {
			return $this->location_term->$field;
		}

		return false;
	}

	/**
	 * Get the start date for a class
	 *
	 * @param      int  $class_id  The class ID
	 *
	 * @return     string  The start date.
	 */
	public static function get_start_date( $class_id ){
		return get_post_meta( $class_id, '_start_date', true );
	}

	// Is class available for purchasing
	public function is_available( $ignore_own_cart = false ) {
		return apply_filters( 'andalu_woo_courses_class_availability', $this->seats > 0, $this, $ignore_own_cart );
	}

	public function set_seats( $amount = null, $mode = 'set' ) {
		global $wpdb;

		if ( ! is_null( $amount ) ) {

			// Ensure key exists
			add_post_meta( $this->get_id(), '_seats', 0, true );

			// Update seats in DB directly
			switch ( $mode ) {
				case 'add' :
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + %f WHERE post_id = %d AND meta_key='_seats'", $amount, $this->get_id() ) );
				break;
				case 'subtract' :
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = meta_value - %f WHERE post_id = %d AND meta_key='_seats'", $amount, $this->get_id() ) );
				break;
				default :
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = %f WHERE post_id = %d AND meta_key='_seats'", $amount, $this->get_id() ) );
				break;
			}

			// Clear caches
			wp_cache_delete( $this->get_id(), 'post_meta' );

			// Get updated value from DB
			$this->seats = get_post_meta( $this->get_id(), '_seats', true );
		}

		return $this->seats;
	}

	public function reduce_seats( $amount = 1 ) {
		return $this->set_seats( $amount, 'subtract' );
	}

	public function increase_seats( $amount = 1 ) {
		return $this->set_seats( $amount, 'add' );
	}

}