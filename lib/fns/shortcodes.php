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

  require_once( 'http_build_url.php' );
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

      $url = parse_url( get_the_permalink( $product->get_id() ) );
      $class_post = get_post($class_id);
      $url['path'] = trailingslashit( $url['path'] ) . 'register/' . $class_post->post_name; // $class->post->post_name
      $class_data['register_link'] = http_build_url( $url );

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