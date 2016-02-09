<?php

// Handles course product order


class Andalu_Woo_Courses_Order {
	
	static function init() {

		// Add meta to order
		add_action( 'woocommerce_add_order_item_meta', __CLASS__ . '::order_item_meta', 10, 2 );

		// Reduce seats when stock is reduced
		add_action( 'woocommerce_payment_complete', __CLASS__ . '::reduce_order_seats' );
		
		// Courses do not need to be processed when purchased
		add_filter( 'woocommerce_order_item_needs_processing', __CLASS__ . '::needs_processing', 10, 3 ); 
	}
	

	// Add meta to order
	static function order_item_meta( $item_id, $values ) {
		if ( ! empty( $values['course_registration'] ) ) {

			if ( ! empty( $values['course_registration']['class'] ) ) {
				wc_add_order_item_meta( $item_id, '_class_id', $values['course_registration']['class'] );
				wc_add_order_item_meta( $item_id, __( 'Class', 'andalu_woo_courses' ), get_the_title( $values['course_registration']['class'] ) );

				$class = wc_get_product( $values['course_registration']['class'] );
				if ( $class && 'Virtual' != $class->get_location() ) {
					wc_add_order_item_meta( $item_id, __( 'Location', 'andalu_woo_courses' ), $class->get_location() );
				}
			}

			if ( ! empty( $values['course_registration']['courses'] ) ) {
				$classes = array();
				foreach( $values['course_registration']['courses'] as $course_id => $class_id ) {
					$classes[] = get_the_title( $course_id ) . ' - ' . get_the_title( $class_id );
				}
				
				wc_add_order_item_meta( $item_id, '_courses', $values['course_registration']['courses'] );
				wc_add_order_item_meta( $item_id, __( 'Classes', 'andalu_woo_courses' ), implode( '<br/>', $classes ) );
			}
			
			
			foreach ( Andalu_Woo_Courses_Single::registration_fields() as $key => $field ) {

				// Skip display of several fields		
				if ( in_array( $key, array( 'email_again', 'optin' ) ) ) { continue; }
				
				if ( ! empty( $values['course_registration'][ $key ] ) ) {
					wc_add_order_item_meta( $item_id, $field['label'], $values['course_registration'][ $key ] );
				}
			}

			if ( ! empty( $values['course_registration']['optin'] ) ) {
				wc_add_order_item_meta( $item_id, __( 'Optin', 'andalu_woo_courses' ), 'yes' );
			}

		}
	}

	// Reduce seats when classes are ordered
	public function reduce_order_seats( $order ) {
		if ( ! is_object( $order ) ) { $order = wc_get_order( $order ); }
		
		if ( 1 != get_post_meta( $order->id, '_order_seats_reduced', true ) && sizeof( $order->get_items() ) > 0 ) {
			foreach ( $order->get_items() as $item ) {

				// Reduce a single class
				if ( ! empty( $item['item_meta']['_class_id'][0] ) ) {
					$class = wc_get_product( $item['item_meta']['_class_id'][0] );
					if ( $class ) {
						$new_stock = $class->reduce_seats( $item['qty'] );
						$order->add_order_note( sprintf( __( 'Class "%s" seats reduced from %s to %s.', 'andalu_woo_courses' ), $class->post->post_title, $new_stock + $item['qty'], $new_stock) );
					}
				}

				// Reduce a list of classes
				if ( ! empty( $item['item_meta']['_courses'][0] ) ) {
					$courses = maybe_unserialize( $item['item_meta']['_courses'][0] );
					foreach( $courses as $class_id ) {
						$class = wc_get_product( $class_id );
						if ( $class ) {
							$new_stock = $class->reduce_seats( $item['qty'] );
							$order->add_order_note( sprintf( __( 'Class "%s" seats reduced from %s to %s.', 'andalu_woo_courses' ), $class->post->post_title, $new_stock + $item['qty'], $new_stock) );
						}
					}
				}
			}

			add_post_meta( $order->id, '_order_seats_reduced', '1', true );
		}
	}


	public function needs_processing( $needs_processing, $product, $order_id ) {
		if ( $product->is_type( Andalu_Woo_Courses::$product_type ) ) {
			return false;
		}
		return $needs_processing;
	}
	
}
Andalu_Woo_Courses_Order::init();