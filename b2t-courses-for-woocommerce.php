<?php
/*
Plugin Name: B2T Courses for WooCommerce
Plugin URI: https://github.com/WenderHost/b2t-courses-for-woocommerce
Description: Adds a custom WC Product type called Courses
Version: 3.5.1
Author: TheWebist
Author URI: https://mwender.com
*/
define( 'ANDALU_DIR', dirname( __FILE__ ) );
define( 'ANDALU_LANG', get_locale() );
define( 'ANDALU_DEV_ENV', stristr( site_url(), '.local' ) );

// Load Composer dependencies
require_once('vendor/autoload.php');

// Load Functions
require_once('lib/fns/acf.php');
require_once('lib/fns/body-class.php');
require_once('lib/fns/enqueues.php');
require_once('lib/fns/gravityforms.php');
require_once('lib/fns/handlebars.php');
require_once('lib/fns/http_build_url.php');
require_once('lib/fns/multilingual.php');
require_once('lib/fns/shortcodes.php');
require_once('lib/fns/taxonomies.php');
require_once('lib/fns/utilities.php');

if ( ! class_exists( 'Andalu_Woo_Courses' ) ) :
class Andalu_Woo_Courses {

	public static $url, $dir;

	public static $product_type = 'course';
	public static $endorsements = array( 'PMI', 'IIBA' );

	static function init() {
		self::$url = plugins_url('', __FILE__);
		self::$dir = plugin_dir_path(__FILE__);

		load_plugin_textdomain( 'andalu_woo_courses', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		if ( is_admin() ) {
			require_once( 'lib/classes/admin.php' );
		}

		add_filter( 'woocommerce_product_type_query', [ 'Andalu_Woo_Courses', 'filter_product_type_query'], 10, 2 );
		add_filter( 'woocommerce_data_stores', [ 'Andalu_Woo_Courses', 'set_course_class_data_store'] );

		require_once( 'lib/classes/course-class.php' );
		require_once( 'lib/classes/course-single.php' );
		require_once( 'lib/classes/course-order.php' );
		require_once( 'lib/classes/cart-reserve.php' );

		add_action( 'plugins_loaded', __CLASS__ . '::create_product_types' );
	}

	static function create_product_types() {
		require_once( 'lib/classes/course-product.php' );
		require_once( 'lib/classes/course-class-product.php' );
		require_once( 'lib/classes/class-data-store.php' );
	}

	static function filter_product_type_query( $bool = false, $product_id = null ){
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

if( ! function_exists( 'uber_log' ) ){
	/**
	 * Enhanced logging.
	 *
	 * @param      string  $message  The log message
	 */
	function uber_log( $message = null ){
	  static $counter = 1;

	  $bt = debug_backtrace();
	  $caller = array_shift( $bt );

	  if( 1 == $counter )
	    error_log( "\n\n" . str_repeat('-', 25 ) . ' STARTING DEBUG [' . date('h:i:sa', current_time('timestamp') ) . '] ' . str_repeat('-', 25 ) . "\n\n" );
	  error_log( "\n" . $counter . '. ' . basename( $caller['file'] ) . '::' . $caller['line'] . "\n" . $message . "\n---\n" );
	  $counter++;
	}
}

$mailtrap = dirname( __FILE__ ) . '/mailtrap.php';
if( file_exists( $mailtrap ) )
    require( $mailtrap );