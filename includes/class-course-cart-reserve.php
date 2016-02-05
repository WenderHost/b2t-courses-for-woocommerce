<?php

// Decreases virtual stock when an item has been added to the cart


class Andalu_Woo_Courses_Cart_Reserve {

	static $sessions = false;

	static function init() {

		// Filter class availability
		add_filter( 'andalu_woo_courses_class_availability', __CLASS__ . '::available', 10, 3 );

		// Add expiry time when adding class item to cart
		add_filter( 'woocommerce_add_cart_item', __CLASS__ . '::add_cart_item', 90, 2 );

		// Check for expired items in the cart
		add_action( 'woocommerce_cart_loaded_from_session', __CLASS__ . '::expire_items' );

	}

	public static function available( $available, $class, $ignore_own_cart ) {
		$quantity_in_cart = self::quantity_in_carts( $class->id, $ignore_own_cart );
		return ( $class->seats - $quantity_in_cart ) > 0;
	}

	public static function add_cart_item( $item, $key ) {
		if ( isset( $item[ 'course_registration' ] ) ) {
			$item['cart_expiry_time'] = time() + ( 15 * 60 ); // Expire in 15 minutes
		}
		return $item;
	}

	public static function expire_items() {
		$cart = WC()->cart;
		foreach ( $cart->cart_contents as $cart_id => $item ) {

			// Skip other items
			if ( empty( $item['course_registration'] ) ) { continue; }

			if ( self::is_expired( $item ) ) {
				self::remove_expired_item( $cart_id, $cart );
			}
		}
	}

	protected static function remove_expired_item( $cart_id, $cart = null ) {
		$expired_cart_notice = sprintf( __( "Sorry, '%s' was removed from your cart because you didn't checkout before the expiration time.", 'andalu_woo_courses' ), $cart->cart_contents[ $cart_id ][ 'data' ]->get_title() );
		wc_add_notice( $expired_cart_notice, 'error' );
		unset( $cart->cart_contents[ $cart_id ] );
		WC()->session->set( 'cart', $cart->cart_contents );
	}

	protected static function is_expired( $item ) {
		if ( ! empty( $item['cart_expiry_time'] ) && $item['cart_expiry_time'] < time() ) {
			return true;
		}
		return false;
	}


	public static function quantity_in_carts( $id, $ignore_own_cart = false ) {
		global $wpdb;
		$quantity = 0;
		$id = intval( $id );

		if ( empty( self::$sessions ) ) {

			// Force the session to save in case we just added something to the cart
			WC()->session->save_data();

			// Get sessions containing courses from DB
			self::$sessions = $wpdb->get_results( $wpdb->prepare(
				'SELECT session_key, session_value FROM ' . $wpdb->prefix . 'woocommerce_sessions WHERE session_expiry > %d AND ( session_value LIKE %s OR session_value LIKE %s )',
				time(),
				'%:"class";i:' . $id . ';%',
				'%:"courses";a:%'
			) );
		}

		if ( ! empty( self::$sessions ) && ! is_wp_error( self::$sessions ) ) {
			foreach ( self::$sessions as $session ) {
				$own_cart = ( $session->session_key === WC()->session->get_customer_id() );
				if ( $ignore_own_cart && $own_cart ) { continue; }

				$cart_session = unserialize( $session->session_value );
				if ( isset( $cart_session[ 'cart' ] ) ) {

					$cart_items = unserialize( $cart_session[ 'cart' ] );
					foreach ( $cart_items as $item_key => $item ) {

						// Skip other / expired items
						if ( empty( $item['course_registration'] ) || self::is_expired( $item ) ) { continue; }

						// Add quantity in cart from single class
						if ( ! empty( $item['course_registration']['class'] ) && $id === $item['course_registration']['class'] ) {
							$quantity += $item[ 'quantity' ];
						}

						// Add quantity in cart from list of classes
						if ( ! empty( $item['course_registration']['courses'] ) && in_array( $id, $item['course_registration']['courses'] ) ) {
							$quantity += $item[ 'quantity' ];
						}
					}
				}

			}
		}

		return $quantity;
	}

}
Andalu_Woo_Courses_Cart_Reserve::init();