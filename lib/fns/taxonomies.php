<?php

namespace AndaluWooCourses\taxonomies;

function register_taxonomies(){
  $labels = array(
    'name'                  => _x( 'Roles', 'Taxonomy Roles', 'text-domain' ),
    'singular_name'         => _x( 'Role', 'Taxonomy Role', 'text-domain' ),
    'search_items'          => __( 'Search Roles', 'text-domain' ),
    'popular_items'         => __( 'Popular Roles', 'text-domain' ),
    'all_items'             => __( 'All Roles', 'text-domain' ),
    'parent_item'           => __( 'Parent Role', 'text-domain' ),
    'parent_item_colon'     => __( 'Parent Role', 'text-domain' ),
    'edit_item'             => __( 'Edit Role', 'text-domain' ),
    'update_item'           => __( 'Update Role', 'text-domain' ),
    'add_new_item'          => __( 'Add New Role', 'text-domain' ),
    'new_item_name'         => __( 'New Role Name', 'text-domain' ),
    'add_or_remove_items'   => __( 'Add or remove Roles', 'text-domain' ),
    'choose_from_most_used' => __( 'Choose from most used Roles', 'text-domain' ),
    'menu_name'             => __( 'Roles', 'text-domain' ),
  );

  $args = array(
    'labels'            => $labels,
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_admin_column' => false,
    'hierarchical'      => true,
    'show_tagcloud'     => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => true,
    'query_var'         => true,
    'capabilities'      => array(),
  );

  register_taxonomy( 'role', array( 'product' ), $args );

  $labels = array(
    'name'                  => _x( 'Sub Categories', 'Taxonomy Sub Categories', 'text-domain' ),
    'singular_name'         => _x( 'Sub Category', 'Taxonomy Sub Category', 'text-domain' ),
    'search_items'          => __( 'Search Sub Categories', 'text-domain' ),
    'popular_items'         => __( 'Popular Sub Categories', 'text-domain' ),
    'all_items'             => __( 'All Sub Categories', 'text-domain' ),
    'parent_item'           => __( 'Parent Sub Category', 'text-domain' ),
    'parent_item_colon'     => __( 'Parent Sub Category', 'text-domain' ),
    'edit_item'             => __( 'Edit Sub Category', 'text-domain' ),
    'update_item'           => __( 'Update Sub Category', 'text-domain' ),
    'add_new_item'          => __( 'Add New Sub Category', 'text-domain' ),
    'new_item_name'         => __( 'New Sub Category Name', 'text-domain' ),
    'add_or_remove_items'   => __( 'Add or remove Sub Categories', 'text-domain' ),
    'choose_from_most_used' => __( 'Choose from most used Sub Categories', 'text-domain' ),
    'menu_name'             => __( 'Sub Category', 'text-domain' ),
  );

  $args = array(
    'labels'            => $labels,
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_admin_column' => false,
    'hierarchical'      => true,
    'show_tagcloud'     => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => true,
    'query_var'         => true,
    'capabilities'      => array(),
  );

  register_taxonomy( 'sub_category', ['product'], $args );

  $labels = array(
    'name'                  => _x( 'Certifications', 'Taxonomy Certifications', 'text-domain' ),
    'singular_name'         => _x( 'Certification', 'Taxonomy Certification', 'text-domain' ),
    'search_items'          => __( 'Search Certifications', 'text-domain' ),
    'popular_items'         => __( 'Popular Certifications', 'text-domain' ),
    'all_items'             => __( 'All Certifications', 'text-domain' ),
    'parent_item'           => __( 'Parent Certification', 'text-domain' ),
    'parent_item_colon'     => __( 'Parent Certification', 'text-domain' ),
    'edit_item'             => __( 'Edit Certification', 'text-domain' ),
    'update_item'           => __( 'Update Certification', 'text-domain' ),
    'add_new_item'          => __( 'Add New Certification', 'text-domain' ),
    'new_item_name'         => __( 'New Certification Name', 'text-domain' ),
    'add_or_remove_items'   => __( 'Add or remove Certifications', 'text-domain' ),
    'choose_from_most_used' => __( 'Choose from most used Certifications', 'text-domain' ),
    'menu_name'             => __( 'Certification', 'text-domain' ),
  );

  $args = array(
    'labels'            => $labels,
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_admin_column' => false,
    'hierarchical'      => true,
    'show_tagcloud'     => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => true,
    'query_var'         => true,
    'capabilities'      => array(),
  );

  register_taxonomy( 'certification', ['product'], $args );
}
add_action( 'init', __NAMESPACE__ . '\\register_taxonomies' );