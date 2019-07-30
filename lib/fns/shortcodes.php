<?php

namespace AndaluWooCourses\shortcodes;

function elementor_public_classes( $atts ){
  //error_log("\n" . str_repeat( '_', 40 ) . ' mwender_public_classes() ' . str_repeat( '-', 40 ) . "\n" );
  $args = shortcode_atts([
    'id' => null
  ],$atts);

  if( is_null( $args['id'] ) ){
    //if( ! $product )
      global $product;

    if( ! is_object( $product ) )
      $product = wc_get_product($product);

    if( empty( $product ) )
      return;

    $has_classes = $product->has_classes();
    //error_log('[ANDALU WOOCOURSES] $has_classes = ' . $has_classes );

    if( ! $has_classes )
      return '<code>No public classes currently scheduled for this course.</code>';
  }

  $date_format = 'M j, Y';
  $locations = \Andalu_Woo_Courses_Class::get_locations();

  $course_id = $product->get_id();

  $data = [];
  $data['title'] = get_the_title( $course_id );
  if( ! empty( $product->course_duration ) )
    $data['course_length'] = sprintf( __('Course Length: %s', 'andalu_woo_courses'), $product->course_duration );

  $data['cost'] = $product->get_price_html();

  $classes = [];
  if( $product->has_classes() && ! empty( $product->course_classes ) ){

    foreach ( $product->course_classes as $class_id ) {
      $class_data = [];
      $class = wc_get_product( $class_id );
      if( empty( $class ) )
        continue;

      // Don't show classes whose start_date is <= today
      $today = date( 'Y-m-d', current_time( 'timestamp' ) );
      $class_start_date = date( 'Y-m-d', $class->start_timestamp );
      if( $today >= $class_start_date )
        continue;

      $class_dates = date( $date_format, $class->start_timestamp );
      if ( ! empty( $class->end_timestamp ) ) { $class_dates .= ' - ' . date( $date_format, $class->end_timestamp ); }
      $class_dates = apply_filters( 'andalu_woo_courses_class_dates', $class_dates, $class->start_timestamp, $class->end_timestamp, $date_format );
      $class_data['class_dates'] = $class_dates;

      $class_data['register_link'] = \AndaluWooCourses\utilities\get_register_link( $product->get_id(), $class_id );

      $class_data['css_classes'] = '';
      if( $class->confirmed )
        $class_data['css_classes'].= ' confirmed';
      $term_location = get_term( $class->location, 'class_location' );
      if( $term_location ){
        $class_data['location'] = [
          'description' => $term_location->description,
          'name' => $term_location->name
        ];
      } else {
        $class_data['location'] = [
          'description' => '',
          'name' => 'TBA'
        ];
      }

      $class_data['is_available'] = $class->is_available();

      $classes[$class_id] = $class_data;
    } //
  }
  $data['classes'] = $classes;

  $html = \AndaluWooCourses\handlebars\render_template('public_classes',$data);
  return $html;
}
add_shortcode( 'elementor_public_classes', __NAMESPACE__ . '\\elementor_public_classes' );

function public_class_calendar( $atts ){
  $args = shortcode_atts([
    'foo' => 'bar'
  ], $atts );
  $html = '<code>Public class calendar goes here.</code>';

  $query_args = [
    'post_type' => 'course_class',
    'posts_per_page' => -1,
    'post_status' => 'inherit',
    'order' => 'ASC',
    'orderby' => 'meta_value_date',
    'meta_query' => [
      [
        'key' => '_start_date',
        'type' => 'DATE',
        'compare' => '>=',
        'value' => \date('Y-m-d H:i:s'),
      ]
    ]
  ];

  $classes = get_posts( $query_args );
  if( is_array( $classes ) && 0 < count( $classes ) ){
    wp_enqueue_style( 'flexboxgrid' );
    $x = 0;
    foreach( $classes as $class ){
      if( ! $class->post_parent )
        continue;

      $data = [];
      $data['course_title'] = get_the_title( $class->post_parent );
      $data['course_url'] = get_the_permalink( $class->post_parent );

      $data['css_classes'] = '';
      if( $x % 2 )
        $data['css_classes'].= ' alt';
      $x++;

      $start_date = get_post_meta( $class->ID, '_start_date', true );
      $start_date_obj = date_create( $start_date );
      $start_month = $start_date_obj->format( 'm' );
      $start_year = $start_date_obj->format( 'Y' );

      $end_date = get_post_meta( $class->ID, '_end_date', true );
      $end_date_obj = date_create( $end_date );
      $end_month = $end_date_obj->format( 'm' );
      $end_year = $end_date_obj->format( 'Y' );

      if( $start_month != $end_month ){
        $days = $start_date_obj->format( 'M j' ) . ' &ndash; ' . $end_date_obj->format( 'M j' );
      } else {
        $days = $start_date_obj->format( 'M j' ) . ' &ndash; ' . $end_date_obj->format( 'j' );
      }

      $data['times'] = get_post_meta( $class->ID, '_time', true );

      $data['days'] = $days;
      $data['year'] = $start_year;
      $data['register_url'] = \AndaluWooCourses\utilities\get_register_link( $class->post_parent, $class->ID );

      $parent_course_product = wc_get_product( $class->post_parent );
      $data['price'] = get_woocommerce_currency_symbol() . $parent_course_product->get_price();
      $class_obj = wc_get_product( $class->ID );
      $data['location'] = $class_obj->location_term->name;

      $classes_data[] = $data;
    }
    $data['classes'] = $classes_data;
  }
  //$html = '<pre>$classes_data = ' . print_r($classes_data, true ) . '</pre>';
  $html = \AndaluWooCourses\handlebars\render_template('public_class_calendar',$data);

  return $html;
}
add_shortcode('public_class_calendar', __NAMESPACE__ . '\\public_class_calendar' );