<?php
namespace b2tcourses\acf;

function save_product_fields( $path ){
  return ANDALU_DIR . '/lib/acf-json/';
}
add_filter( 'acf/settings/save_json/key=group_6407e1c0795e2', __NAMESPACE__ . '\\save_product_fields' );

function load_product_fields( $path ){
  return ANDALU_DIR . '/lib/acf-json/';
}
add_filter( 'acf/settings/load_json/key=group_6407e1c0795e2', __NAMESPACE__ . '\\load_product_fields' );