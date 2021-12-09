<?php

namespace AndaluWooCourses\multilingual;

function get_class_pricing( $course_id, $class_id = null ){
  $class_price = ( ! is_null( $class_id ) )? get_post_meta( $class_id, '_price', true ) : null ;

  $pricing = [];
  $course = wc_get_product( $course_id );

  if( ! is_null( $class_price ) ){
    $pricing = [
      'current_price' => $class_price,
      'regular_price' => null,
      'on_sale'       => false,
    ];
  } else {
    $current_price = ( method_exists( $course, 'get_price') )? $course->get_price() : '0.00' ;
    $regular_price = ( method_exists( $course, 'get_regular_price') )? $course->get_regular_price() : '0.00' ;
    $pricing = [
      'current_price' => $current_price,
      'regular_price' => $regular_price,
    ];
    $pricing['on_sale'] = ( $pricing['current_price'] != $pricing['regular_price'] )? true : false ;
  }

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

/**
 * Replaces English month abbreviations with Spanish equivalents.
 *
 * @param      string  $string  The string
 *
 * @return     string  String with month abbreviations in Spanish.
 */
function months_to_spanish( $string = null ){
  $months_en = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  $months_es = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
  $string = str_replace( $months_en, $months_es, $string );
  return $string;
}