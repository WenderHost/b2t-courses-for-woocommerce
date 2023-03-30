<?php

namespace AndaluWooCource\bodyclass;

add_filter( 'body_class', function( $classes ){
  $extra_classes = [];
  if( get_query_var( 'course_register' ) )
    $extra_classes[] = 'registration-form';

  return array_merge( $classes, $extra_classes );
});