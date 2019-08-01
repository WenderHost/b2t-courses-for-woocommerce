<?php

namespace AndaluWooCourses\taxonomies;

function register_taxonomies(){
  $labels = array(
    'name'                  => _x( 'Delivery Modes', 'Taxonomy delivery modes', 'text-domain' ),
    'singular_name'         => _x( 'Delivery Mode', 'Taxonomy delivery mode', 'text-domain' ),
    'search_items'          => __( 'Search Delivery Modes', 'text-domain' ),
    'popular_items'         => __( 'Popular Delivery Modes', 'text-domain' ),
    'all_items'             => __( 'All Delivery Modes', 'text-domain' ),
    'parent_item'           => __( 'Parent Delivery Mode', 'text-domain' ),
    'parent_item_colon'     => __( 'Parent Delivery Mode', 'text-domain' ),
    'edit_item'             => __( 'Edit Delivery Mode', 'text-domain' ),
    'update_item'           => __( 'Update Delivery Mode', 'text-domain' ),
    'add_new_item'          => __( 'Add New Delivery Mode', 'text-domain' ),
    'new_item_name'         => __( 'New Delivery Mode Name', 'text-domain' ),
    'add_or_remove_items'   => __( 'Add or remove Delivery Modes', 'text-domain' ),
    'choose_from_most_used' => __( 'Choose from most used Delivery Modes', 'text-domain' ),
    'menu_name'             => __( 'Delivery Mode', 'text-domain' ),
  );

  $args = array(
    'labels'            => $labels,
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_admin_column' => false,
    'hierarchical'      => false,
    'show_tagcloud'     => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => true,
    'query_var'         => true,
    'capabilities'      => array(),
  );

  register_taxonomy( 'delivery_mode', array( 'product' ), $args );

}
add_action( 'init', __NAMESPACE__ . '\\register_taxonomies' );