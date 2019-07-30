<?php

namespace AndaluWooCourses\utilities;

function get_register_link( $product_id, $class_id ){
  $url = parse_url( get_the_permalink( $product_id ) );
  $class_post = get_post( $class_id );
  $url['path'] = trailingslashit( $url['path'] ) . 'register/' . $class_post->post_name; // $class->post->post_name
  $register_link = http_build_url( $url );
  return $register_link;
}