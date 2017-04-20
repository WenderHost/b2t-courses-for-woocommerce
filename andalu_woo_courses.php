<?php
/*

Plugin Name: ANDA.lu Woo Courses
Plugin URI: http://anda.lu/design

Description: Add a custom WC Product type called Courses

Version: 2.0.0
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

		add_filter( 'woocommerce_product_type_query', [ 'Andalu_Woo_Courses', 'filter_product_type_query'], 10, 2 );
		add_filter( 'woocommerce_data_stores', [ 'Andalu_Woo_Courses', 'set_course_class_data_store'] );

		require_once( 'includes/class-course-class.php' );
		require_once( 'includes/class-course-single.php' );
		require_once( 'includes/class-course-order.php' );
		require_once( 'includes/class-course-cart-reserve.php' );

		add_action( 'plugins_loaded', __CLASS__ . '::create_product_types' );
	}

	static function create_product_types() {
		require_once( 'includes/class-course-product.php' );
		require_once( 'includes/class-course-class-product.php' );
		require_once( 'includes/class-course-class-product-data-store.php' );
	}

	static function filter_product_type_query( $bool = false, $product_id ){
		$post_type = get_post_type( $product_id );
		if( 'course_class' == $post_type )
			return 'course_class';
	}

	static function set_course_class_data_store( $stores ){
		$stores['course_class'] = 'WC_Product_Course_Class_Data_Store';
		return $stores;
	}

}
Andalu_Woo_Courses::init();
endif;

$mailtrap = dirname( __FILE__ ) . '/mailtrap.php';
if( file_exists( $mailtrap ) )
    require( $mailtrap );