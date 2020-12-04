<?php

namespace Andalu\WooCourses\enqueues;

function enqueue_scripts(){
  wp_register_style( 'class-calendar', plugin_dir_url( __FILE__ ) . '../css/class-calendar.css', null, filemtime( plugin_dir_path( __FILE__ ) . '../css/class-calendar.css' ) );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts' );