<?php

namespace AndaluWooCourses\multilingual;

function get_class_pricing( $course_id ){
  $pricing = [];
  $course = wc_get_product( $course_id );
  $current_price = ( method_exists( $course, 'get_price') )? $course->get_price() : '0.00' ;
  $regular_price = ( method_exists( $course, 'get_regular_price') )? $course->get_regular_price() : '0.00' ;
  $pricing = [
    'current_price' => $current_price,
    'regular_price' => $regular_price,
  ];
  $pricing['on_sale'] = ( $pricing['current_price'] != $pricing['regular_price'] )? true : false ;

  $lang = get_locale();
  $pricing['symbol'] = ( 'es_ES' == $lang )? '€' : '$' ;
  switch( $lang ){
    case 'es_ES':
      $format = '%2$s %1$s';
      $current_price = ( is_float( $pricing['current_price'] ) )? number_format( $pricing['current_price'], 2, ',', '.' ) : $pricing['current_price'] ;
      $pricing['formatted']['current_price'] = sprintf( $format, $pricing['symbol'], $current_price );
      $regular_price = ( is_float( $pricing['regular_price'] ) )? number_format( $pricing['regular_price'], 2, ',', '.' ) : $pricing['regular_price'] ;
      $pricing['formatted']['regular_price'] = sprintf( $format, $pricing['symbol'], $regular_price );
      break;

    default:
      $format = '%1$s%2$s';
      $pricing['formatted']['current_price'] = sprintf( $format, $pricing['symbol'], $pricing['current_price'] );
      $pricing['formatted']['regular_price'] = sprintf( $format, $pricing['symbol'], $pricing['regular_price'] );
  }

  return $pricing;
}
