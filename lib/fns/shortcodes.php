<?php

namespace AndaluWooCourses\shortcodes;

function course_details( $atts ){
  global $product;
  if( ! is_object( $product ) )
    $product = wc_get_product( $product );

  if( empty( $product ) )
    return;

  $data = [];
  $data['duration'] = get_post_meta($product->get_id(), '_course_duration', true );
  $data['reference'] = get_post_meta($product->get_id(), '_course_reference', true );
  $data['delivery_mode'] = get_post_meta($product->get_id(), '_course_delivery_mode', true );
  if( is_array( $data['delivery_mode'] ) )
    $data['delivery_mode'] = implode(', ', $data['delivery_mode'] );
  $data['certification'] = get_post_meta($product->get_id(), '_course_certification', true );
  $data['certification_link'] = get_post_meta($product->get_id(), '_course_certification_link', true );

  $html = \AndaluWooCourses\handlebars\render_template('course_details',$data);
  return $html;
}
add_shortcode( 'course_details', __NAMESPACE__ . '\\course_details' );

function elementor_public_classes( $atts ){
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

    if( ! $has_classes ){
      $data['title'] = esc_attr( get_the_title( $course_id ) );
      $html = \AndaluWooCourses\handlebars\render_template( 'no_classes_scheduled', $data );
      return $html;
    }
  }

  $date_format = 'M j, Y';
  $locations = \Andalu_Woo_Courses_Class::get_locations();

  $course_id = $product->get_id();

  $data = [];
  $data['title'] = get_the_title( $course_id );
  if( ! empty( $product->course_duration ) )
    $data['course_length'] = sprintf( __('Course Length: %s', 'andalu_woo_courses'), $product->course_duration );

  $data['cost'] = $product->get_price_html();
  $data['plugin_dir'] = plugin_dir_url( __FILE__ ) . '../../';

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

      $class_data['ID'] = $class_id;

      $class_dates = date( $date_format, $class->start_timestamp );
      if ( ! empty( $class->end_timestamp ) ) { $class_dates .= ' - ' . date( $date_format, $class->end_timestamp ); }
      $class_dates = apply_filters( 'andalu_woo_courses_class_dates', $class_dates, $class->start_timestamp, $class->end_timestamp, $date_format );
      $class_data['class_dates'] = $class_dates;

      $class_data['register_link'] = \AndaluWooCourses\utilities\get_register_link( $product->get_id(), $class_id );

      $class_data['times'] = get_post_meta( $class_id, '_time', true );

      $class_data['css_classes'] = '';
      if( $class->confirmed )
        $class_data['css_classes'].= ' confirmed';
      $term_location = get_term( $class->location, 'class_location' );
      if( $term_location ){
        $class_data['location'] = [
          'description' => apply_filters( 'the_content', $term_location->description ),
          'name' => $term_location->name,
          'id' => $term_location->term_id,
        ];
        $class_data['virtual'] = ( 'Live Virtual' == $term_location->name )? true : false ;
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
  $location_descriptions_js = '<script type="text/javascript">' . file_get_contents(plugin_dir_path( __FILE__ ). '../js/location-descriptions.js' ) . '</script>';
  $html.= $location_descriptions_js;
  return $html;
}
add_shortcode( 'elementor_public_classes', __NAMESPACE__ . '\\elementor_public_classes' );

/**
 * Lists courses from the WooCommerce Product catalog
 *
 * @param      <type>  $atts   The atts
 */
function courselist( $atts ){
  $args = shortcode_atts([
    'tag'       => null,
    'category'  => null,
    'relation'  => 'AND',
  ], $atts );

  $query_args = [
    'numberposts' => -1,
    'orderby'     => 'name',
    'order'       => 'ASC',
    'status'      => 'publish',
    'post_type'   => 'product',
    'tax_query'   => [],
  ];

  if( $args['tag'] ){
    $query_args['tax_query'][] = [
      'taxonomy'  => 'product_tag',
      'field'      => 'name',
      'terms'     => $args['tag'],
    ];
  }
  if( $args['category'] ){
    $query_args['tax_query'][] = [
      'taxonomy'  => 'product_cat',
      'field'      => 'name',
      'terms'     => $args['category'],
    ];
  }
  if( $args['tag'] && $args['category'] )
    $query_args['tax_query']['relation'] = $args['relation'];

  $products = get_posts( $query_args );
  $items = [];
  foreach( $products as $product ){
    $items[] = '<a href="' . get_permalink( $product->ID ) . '">' . $product->post_title . '</a>';
  }

  return '<ul><li>' . implode( '</li><li>', $items ) . '</li></ul>';
}
add_shortcode( 'courselist', __NAMESPACE__ . '\\courselist' );

/**
 * Displays a calendar of Public Classes
 *
 * @param      array  $atts   The atts
 *
 * @return     string  HTML for Public Classes view.
 */
function public_class_calendar( $atts ){
  $args = shortcode_atts([
    'foo' => 'bar'
  ], $atts );

  wp_enqueue_style( 'class-calendar' );

  $html = '<code>Public class calendar goes here.</code>';

  $query_args = [
    'post_type' => 'course_class',
    'posts_per_page' => -1,
    'post_status' => 'inherit',
    'order' => 'ASC',
    'orderby' => 'meta_value_num',
    'meta_key' => '_start_date',
    'meta_query' => [
      [
        'key' => '_start_date',
        'type' => 'NUMBER',
        'compare' => '>=',
        'value' => \date('Ymd'),
      ],
    ]
  ];

  $data = [];
  $data['plugin_dir'] = plugin_dir_url( __FILE__ ) . '../../';

  $classes = get_posts( $query_args );
  if( is_array( $classes ) && 0 < count( $classes ) ){
    $x = 0;
    foreach( $classes as $class ){
      if( ! $class->post_parent )
        continue;

      $private_course = get_post_meta( $class->post_parent, '_private_course', true );
      if( 'yes' == $private_course )
        continue;

      $class_data = [];
      $class_data['course_title'] = get_the_title( $class->post_parent );
      $class_data['course_url'] = get_the_permalink( $class->post_parent );
      $class_data['ID'] = $class->ID;

      $class_data['css_classes'] = 'cal';
      if( $x % 2 )
        $class_data['css_classes'].= ' alt';
      $x++;

      $start_date = get_post_meta( $class->ID, '_start_date', true );
      $start_date_obj = date_create( $start_date );
      $start_month = $start_date_obj->format( 'm' );
      $start_year = $start_date_obj->format( 'Y' );

      $end_date = get_post_meta( $class->ID, '_end_date', true );
      $end_date_obj = date_create( $end_date );
      $end_month = $end_date_obj->format( 'm' );
      $end_year = $end_date_obj->format( 'Y' );

      if( empty( $end_date ) ){
        $days = $start_date_obj->format( 'M j' );
      } else if( $start_month != $end_month ){
        $days = $start_date_obj->format( 'M j' ) . ' &ndash; ' . $end_date_obj->format( 'M j' );
      } else {
        $days = $start_date_obj->format( 'M j' ) . ' &ndash; ' . $end_date_obj->format( 'j' );
      }

      $class_data['times'] = get_post_meta( $class->ID, '_time', true );
      $lang = get_post_meta( $class->ID, '_lang', true );
      $languages = ['en' => __( 'English', 'andalu_woo_courses' ), 'es' => __( 'Spanish', 'andalu_woo_courses' ) ];
      $class_data['lang'] = $languages[$lang];
      $class_data['cal'] = get_post_meta( $class->ID, '_cal', true );
      if( is_array( $class_data['cal'] ) ){
        foreach( $class_data['cal'] as $lang_code ){
          $class_data['css_classes'].= ' cal_' . $lang_code;
        }
      }

      $class_data['days'] = $days;
      $class_data['year'] = $start_year;
      $class_data['register_url'] = \AndaluWooCourses\utilities\get_register_link( $class->post_parent, $class->ID );

      $parent_course_product = wc_get_product( $class->post_parent );
      $class_data['price'] = get_woocommerce_currency_symbol() . $parent_course_product->get_price();
      $class_data['regular_price'] = get_woocommerce_currency_symbol() . $parent_course_product->get_regular_price();
      $class_data['on_sale'] = false;
      if( $class_data['price'] != $class_data['regular_price'] ){
        $class_data['price'] =  '<s>' . $class_data['regular_price'] . '</s> ' . $class_data['price'];
        $class_data['on_sale'] = true;
      }
      $class_obj = wc_get_product( $class->ID );

      $class_data['location'] = [
        'name'        => $class_obj->location_term->name,
        'id'          => $class_obj->location_term->term_id,
        'description' => apply_filters( 'the_content', $class_obj->location_term->description ),
      ];
      $class_data['virtual'] = ( 'Live Virtual' == $class_obj->location_term->name )? true : false ;

      $class_data['duration'] = get_post_meta( $class->post_parent, '_course_duration', true );

      $classes_data[] = $class_data;
    }
    $data['classes'] = $classes_data;
  }
  //$html = '<pre>$classes_data = ' . print_r($classes_data, true ) . '</pre>';

  $data['labels'] = [
    'dates'     => __( 'Dates', 'andalu_woo_courses' ),
    'course'    => __( 'Course', 'andalu_woo_courses' ),
    'location'  => __( 'Location', 'andalu_woo_courses' ),
    'time'      => __( 'Time', 'andalu_woo_courses' ),
    'duration'  => __( 'Duration', 'andalu_woo_courses' ),
    'price'     => __( 'Price', 'andalu_woo_courses' ),
    'language'  => __( 'Language', 'andalu_woo_courses' ),
    'register'  => __( 'Register', 'andalu_woo_courses' ),
  ];

  $html = \AndaluWooCourses\handlebars\render_template('public_class_calendar',$data);
  $location_descriptions_js = '<script type="text/javascript">' . file_get_contents(plugin_dir_path( __FILE__ ). '../js/location-descriptions.js' ) . '</script>';
  $html.= $location_descriptions_js;
  return $html;
}
add_shortcode('public_class_calendar', __NAMESPACE__ . '\\public_class_calendar' );