<?php

namespace AndaluWooCourses\multilingual;

function get_class_pricing( $course_id ){
  $pricing = [];
  $course = wc_get_product( $course_id );
  $pricing = [
    'current_price' => $course->get_price(),
    'regular_price' => $course->get_regular_price(),
  ];
  $pricing['on_sale'] = ( $pricing['current_price'] != $pricing['regular_price'] )? true : false ;

  $lang = get_locale();
  $pricing['symbol'] = ( 'es_ES' == $lang )? '€' : '$' ;
  switch( $lang ){
    case 'es_ES':
      $format = '%2$s %1$s';
      $pricing['formatted']['current_price'] = sprintf( $format, $pricing['symbol'], number_format( $pricing['current_price'], 2, ',', '.' ) );
      $pricing['formatted']['regular_price'] = sprintf( $format, $pricing['symbol'], number_format( $pricing['regular_price'], 2, ',', '.' ) );
      break;

    default:
      $format = '%1$s%2$s';
      $pricing['formatted']['current_price'] = sprintf( $format, $pricing['symbol'], $pricing['current_price'] );
      $pricing['formatted']['regular_price'] = sprintf( $format, $pricing['symbol'], $pricing['regular_price'] );
  }

  return $pricing;
}
