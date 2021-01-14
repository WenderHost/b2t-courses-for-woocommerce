<?php

namespace Andalu\WooCourses\enqueues;

function enqueue_scripts(){
  wp_register_script( 'class-calendar', plugin_dir_url( __FILE__ ) . '../js/class-calendar.js', ['jquery'], filemtime( plugin_dir_path( __FILE__ ) . '../js/class-calendar.js' ), true );
  wp_localize_script( 'class-calendar', 'wpvars', [ 'locale' => get_locale() ] );
  wp_register_style( 'woo-courses', plugin_dir_url( __FILE__ ) . '../css/woo-courses.css', null, filemtime( plugin_dir_path( __FILE__ ) . '../css/woo-courses.css' ) );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts' );