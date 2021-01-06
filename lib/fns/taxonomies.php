<?php

namespace AndaluWooCourses\taxonomies;

function register_taxonomies(){
  $labels = array(
    'name'                  => _x( 'Roles', 'Taxonomy Roles', 'andalu_woo_courses' ),
    'singular_name'         => _x( 'Role', 'Taxonomy Role', 'andalu_woo_courses' ),
    'search_items'          => __( 'Search Roles', 'andalu_woo_courses' ),
    'popular_items'         => __( 'Popular Roles', 'andalu_woo_courses' ),
    'all_items'             => __( 'All Roles', 'andalu_woo_courses' ),
    'parent_item'           => __( 'Parent Role', 'andalu_woo_courses' ),
    'parent_item_colon'     => __( 'Parent Role', 'andalu_woo_courses' ),
    'edit_item'             => __( 'Edit Role', 'andalu_woo_courses' ),
    'update_item'           => __( 'Update Role', 'andalu_woo_courses' ),
    'add_new_item'          => __( 'Add New Role', 'andalu_woo_courses' ),
    'new_item_name'         => __( 'New Role Name', 'andalu_woo_courses' ),
    'add_or_remove_items'   => __( 'Add or remove Roles', 'andalu_woo_courses' ),
    'choose_from_most_used' => __( 'Choose from most used Roles', 'andalu_woo_courses' ),
    'menu_name'             => __( 'Roles', 'andalu_woo_courses' ),
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
    'name'                  => _x( 'Sub Categories', 'Taxonomy Sub Categories', 'andalu_woo_courses' ),
    'singular_name'         => _x( 'Sub Category', 'Taxonomy Sub Category', 'andalu_woo_courses' ),
    'search_items'          => __( 'Search Sub Categories', 'andalu_woo_courses' ),
    'popular_items'         => __( 'Popular Sub Categories', 'andalu_woo_courses' ),
    'all_items'             => __( 'All Sub Categories', 'andalu_woo_courses' ),
    'parent_item'           => __( 'Parent Sub Category', 'andalu_woo_courses' ),
    'parent_item_colon'     => __( 'Parent Sub Category', 'andalu_woo_courses' ),
    'edit_item'             => __( 'Edit Sub Category', 'andalu_woo_courses' ),
    'update_item'           => __( 'Update Sub Category', 'andalu_woo_courses' ),
    'add_new_item'          => __( 'Add New Sub Category', 'andalu_woo_courses' ),
    'new_item_name'         => __( 'New Sub Category Name', 'andalu_woo_courses' ),
    'add_or_remove_items'   => __( 'Add or remove Sub Categories', 'andalu_woo_courses' ),
    'choose_from_most_used' => __( 'Choose from most used Sub Categories', 'andalu_woo_courses' ),
    'menu_name'             => __( 'Sub Category', 'andalu_woo_courses' ),
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
    'name'                  => _x( 'Certifications', 'Taxonomy Certifications', 'andalu_woo_courses' ),
    'singular_name'         => _x( 'Certification', 'Taxonomy Certification', 'andalu_woo_courses' ),
    'search_items'          => __( 'Search Certifications', 'andalu_woo_courses' ),
    'popular_items'         => __( 'Popular Certifications', 'andalu_woo_courses' ),
    'all_items'             => __( 'All Certifications', 'andalu_woo_courses' ),
    'parent_item'           => __( 'Parent Certification', 'andalu_woo_courses' ),
    'parent_item_colon'     => __( 'Parent Certification', 'andalu_woo_courses' ),
    'edit_item'             => __( 'Edit Certification', 'andalu_woo_courses' ),
    'update_item'           => __( 'Update Certification', 'andalu_woo_courses' ),
    'add_new_item'          => __( 'Add New Certification', 'andalu_woo_courses' ),
    'new_item_name'         => __( 'New Certification Name', 'andalu_woo_courses' ),
    'add_or_remove_items'   => __( 'Add or remove Certifications', 'andalu_woo_courses' ),
    'choose_from_most_used' => __( 'Choose from most used Certifications', 'andalu_woo_courses' ),
    'menu_name'             => __( 'Certification', 'andalu_woo_courses' ),
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
    'show_in_rest'      => true,
  );

  register_taxonomy( 'certification', ['product','post'], $args );
}
add_action( 'init', __NAMESPACE__ . '\\register_taxonomies' );