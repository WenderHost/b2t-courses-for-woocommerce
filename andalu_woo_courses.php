<?php
/*

Plugin Name: ANDA.lu Woo Courses
Plugin URI: http://anda.lu/design

Description: Add a custom WC Product type called Courses

Version: 1.0
Author: ANDA.lu
Author URI: http://anda.lu/design

*/

if ( ! class_exists( 'Andalu_Woo_Courses' ) ) :
class Andalu_Woo_Courses {

	public static $url, $dir;

	public static $product_type = 'course';
	public static $endorsements = array( 'PMI', 'IIBA' );

	static function init() {
		self::$url = plugins_url('', __FILE__);
		self::$dir = plugin_dir_path(__FILE__);

		load_plugin_textdomain( 'andalu_woo_courses', false, self::$dir . '/languages' );

		if ( is_admin() ) {
			require_once( 'includes/class-course-admin.php' );
		}

		require_once( 'includes/class-course-class.php' );
		require_once( 'includes/class-course-single.php' );
		require_once( 'includes/class-course-order.php' );
		require_once( 'includes/class-course-cart-reserve.php' );

		add_action( 'plugins_loaded', __CLASS__ . '::create_product_types' );
	}

	static function create_product_types() {
		require_once( 'includes/class-course-product.php' );
		require_once( 'includes/class-course-class-product.php' );
	}

}
Andalu_Woo_Courses::init();
endif;

$mailtrap = dirname( __FILE__ ) . '/mailtrap.php';
if( file_exists( $mailtrap ) )
    require( $mailtrap );