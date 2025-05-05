<?php

namespace AndaluWooCourses\shortcodes;
use function AndaluWooCourses\multilingual\{get_class_pricing,months_to_spanish};


/**
 * Adds the Course Details widget.
 *
 * @param      array  $atts   Not used.
 *
 * @return     string  HTML for the Course Details widget.
 */
function course_details( $atts ){
  global $product;
  if( ! is_object( $product ) )
    $product = wc_get_product( $product );

  if( empty( $product ) )
    return;

  $product_id = $product->get_id();

  $data = [];
  $data['duration'] = get_post_meta( $product_id, '_course_duration', true );
  $data['reference'] = get_post_meta( $product_id, '_course_reference', true );
  $data['delivery_mode'] = get_post_meta( $product_id, '_course_delivery_mode', true );
  if( is_array( $data['delivery_mode'] ) )
    $data['delivery_mode'] = implode(', ', $data['delivery_mode'] );

  // Get Print Version
  $print_version = get_post_meta( $product_id, 'print_version', true );
  if( is_numeric( $print_version ) ){
    $file_url = wp_get_attachment_url( $print_version );
    if( ! empty( $file_url ) )
      $data['print_version'] = $file_url;
  }

  // Get Certification Links
  if( function_exists( 'have_rows' ) ){
    if( have_rows( 'certification_links', $product_id ) ){
      $certification_links = [];
      $x = 0;
      while( have_rows( 'certification_links', $product_id ) ): the_row();
        $text = get_sub_field( 'text' );
        $link = get_sub_field( 'link' );
        $certification_links[$x] = [
          'text'  => $text,
          'link'  => $link,
        ];
        if( 'iiba' == strtolower( $text ) ){
          $data['iiba_endorsed'] = true;
          $data['iiba_img'] = plugin_dir_url( __FILE__ ) . '../img/b2t-iiba-endorsed-course_deep-teal_510x120.png';
        }

        $x++;
      endwhile;
      $data['certification_links'] = $certification_links;
    }
  }

  $data['labels'] = [
    'course_details' => __( 'Course Details', 'andalu_woo_courses' ),
    'reference' => __( 'Reference', 'andalu_woo_courses' ),
    'duration' => __( 'Duration', 'andalu_woo_courses' ),
    'delivery_mode' => __( 'Delivery Mode', 'andalu_woo_courses' ),
    'certification' => __( 'Certification', 'andalu_woo_courses' ),
    'request_info' => __( 'Request Info', 'andalu_woo_courses' ),
  ];
  $data['locale'] = ANDALU_LANG;

  $html = \AndaluWooCourses\handlebars\render_template( 'course_details', $data );
  return $html;
}
add_shortcode( 'course_details', __NAMESPACE__ . '\\course_details' );

/**
 * Shortcode to display the post title of a `course_class` CPT by matching URL segment.
 *
 * Usage: [class_dates_from_url]
 *
 * @return string The post title of the `course_class` CPT if found, or an error message.
 */
function class_dates_from_url() {
  // Get the current URL.
  $current_url = (is_ssl() ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

  // Parse the URL to get the last segment.
  $parsed_url = parse_url($current_url);
  $path = rtrim($parsed_url['path'], '/');
  $segments = explode('/', $path);
  $post_name = end($segments);

  // Query the `course_class` CPT by `post_name`.
  $args = [
    'name'           => $post_name,
    'post_type'      => 'course_class',
    'post_status'    => 'inherit',
    'posts_per_page' => 1,
  ];
  $query = new \WP_Query($args);

  // Check if the post is found and return the post title.
  if ($query->have_posts()) {
    $query->the_post();
    $post_title = get_the_title();
    wp_reset_postdata();
    return esc_html($post_title);
  } else {
    return "Class not found for {$post_name}";
  }
}
add_shortcode('class_dates_from_url', __NAMESPACE__ . '\\class_dates_from_url');


/**
 * Displays "Public Classes" widget in the sidebar of Course pages.
 *
 * @param      array  $atts {
 *   @type  int  $id   The ID of the Course whose public classes you wish to display, defaults to current post.
 * }
 *
 * @return     string  HTML for the "Public Classes" widet.
 */
function elementor_public_classes( $atts ){
  $args = shortcode_atts([
    'id' => null
  ],$atts);

  wp_enqueue_style( 'woo-courses' );
  wp_enqueue_script( 'class-calendar' );

  // Initialize the data we'll be passing to our Handlebars template:
  $data = [
    'labels' => [
      'widget_title' => __( 'Public Classes', 'andalu_woo_courses' ),
      'button_label' => __( 'Register', 'andalu_woo_courses' ),
      'no_public_classes_button_label' => __( 'See Public Class Schedule', 'andalu_woo_courses' ),
    ],
  ];

  if( is_null( $args['id'] ) ){
    //if( ! $product )
      global $product;

    if( ! is_object( $product ) )
      $product = wc_get_product($product);

    if( empty( $product ) )
      return;

    if( ! method_exists( $product, 'has_classes' ) )
      return;

    $has_classes = $product->has_classes();

    if( ! $has_classes ){
      $data['title'] = ( isset( $course_id ) )? esc_attr( get_the_title( $course_id ) ) : 'No Public Sessions' ;
      $data['labels']['no_public_classes_message'] = __( 'Currently, we don\'t have any public sessions of this course scheduled. Please let us know if you are interested in adding a session.', 'andalu_woo_courses' );

      $html = \AndaluWooCourses\handlebars\render_template( 'no_classes_scheduled', $data );
      return $html;
    }
  }

  $date_format = ('es_ES' == ANDALU_LANG )? 'j M, Y' : 'M j, Y';
  $locations = \Andalu_Woo_Courses_Class::get_locations();

  $course_id = $product->get_id();

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

      // Check for class price
      $price = get_post_meta( $class_id, '_price', true );
      if( $price ){
        $class_price_array = get_class_pricing( null, $class_id );
        $class_data['class_price'] = $class_price_array['formatted']['current_price'];
      }

      $class_dates = date( $date_format, $class->start_timestamp );
      if ( ! empty( $class->end_timestamp ) ) { $class_dates .= ' - ' . date( $date_format, $class->end_timestamp ); }
      $class_dates = apply_filters( 'andalu_woo_courses_class_dates', $class_dates, $class->start_timestamp, $class->end_timestamp, $date_format );
      if( 'es_ES' == ANDALU_LANG )
        $class_dates = months_to_spanish( $class_dates );
      $class_data['class_dates'] = $class_dates;

      $class_data['register_link'] = \AndaluWooCourses\utilities\get_register_link( $product->get_id(), $class_id );

      $class_data['times'] = get_post_meta( $class_id, '_time', true );
      $duration = get_post_meta( $class_id, '_duration', true );
      $class_data['duration'] = ( ! empty( $duration ) )? $duration : false ;

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
        $class_data['virtual'] = ( in_array( $term_location->name, ['Live Virtual','OpenClass®'] ) )? true : false ;
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

  return \AndaluWooCourses\handlebars\render_template('public_classes',$data);
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
 * Utilizes `lib/templates/public_class_calendar.hbs`.
 *
 * @param      array  $atts   {
 *             No options currently defined.
 * }
 *
 * @return     string  HTML for Public Classes view.
 */
function public_class_calendar( $atts ){
  $args = shortcode_atts([
    'foo' => 'bar'
  ], $atts );

  wp_enqueue_style( 'woo-courses' );

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

  if( false === ( $classes = get_transient( 'public_class_calendar' ) ) && ! WP_DEBUG ){
    $classes = get_posts( $query_args );
    set_transient( 'public_class_calendar', $classes, HOUR_IN_SECONDS );
  } else {
    $classes = get_posts( $query_args );
  }

  if( is_array( $classes ) && 0 < count( $classes ) ){
    $x = 0;
    foreach( $classes as $class ){
      if( ! $class->post_parent || 'publish' != get_post_status( $class->post_parent ) )
        continue;

      $private_course = get_post_meta( $class->post_parent, '_private_course', true );
      if( 'yes' == $private_course )
        continue;

      $class_data = [];
      $class_data['course_title'] = get_the_title( $class->post_parent );
      $class_data['course_url'] = get_the_permalink( $class->post_parent );
      $class_data['ID'] = $class->ID;
      $confirmed = get_post_meta( $class->ID, '_confirmed', true );
      $class_data['confirmed'] = ( 'yes' == $confirmed )? true : false ;

      $class_data['css_classes'] = 'cal';
      if( $x % 2 )
        $class_data['css_classes'].= ' alt';
      $x++;
      if( 'yes' == $confirmed )
        $class_data['css_classes'].= ' confirmed';

      $start_date = get_post_meta( $class->ID, '_start_date', true );
      $start_date_obj = date_create( $start_date );
      $start_month = $start_date_obj->format( 'm' );
      $start_year = $start_date_obj->format( 'Y' );

      $end_date = get_post_meta( $class->ID, '_end_date', true );
      $end_date_obj = date_create( $end_date );
      $end_month = $end_date_obj->format( 'm' );
      $end_year = $end_date_obj->format( 'Y' );

      $date_format = ( 'es_ES' == ANDALU_LANG )? 'j M' : 'M j' ;

      if( empty( $end_date ) ){
        $days = $start_date_obj->format( $date_format );
      } else if( $start_month != $end_month ){
        $days = $start_date_obj->format( $date_format ) . ' &ndash; ' . $end_date_obj->format( $date_format );
      } else {
        switch( ANDALU_LANG ){
          case 'es_ES':
            $days = $start_date_obj->format( 'j' ) . ' &ndash; ' . $end_date_obj->format( $date_format );
            break;
          default:
            $days = $start_date_obj->format( $date_format ) . ' &ndash; ' . $end_date_obj->format( 'j' );
        }

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

      if( 'es_ES' == ANDALU_LANG )
        $days = months_to_spanish( $days );
      $class_data['days'] = $days;
      $class_data['year'] = $start_year;
      $class_data['register_url'] = \AndaluWooCourses\utilities\get_register_link( $class->post_parent, $class->ID );

      // Setup Pricing
      $class_data['pricing'] = get_class_pricing( $class->post_parent, $class->ID );

      $class_obj = wc_get_product( $class->ID );
      $class_data['location'] = [
        'name'        => $class_obj->location_term->name,
        'slug'        => $class_obj->location_term->slug,
        'id'          => $class_obj->location_term->term_id,
        'description' => $class_obj->location_term->description,
      ];
      $class_data['virtual'] = ( in_array( $class_obj->location_term->name, ['Live Virtual','OpenClass®'] ) )? true : false ;

      //$class_data['duration'] = get_post_meta( $class->post_parent, '_course_duration', true );
      $class_data['duration'] = get_post_meta( $class->ID, '_duration', true );

      $classes_data[] = $class_data;
    }
    $data['classes'] = $classes_data;
  }
  //$html = '<pre>$classes_data = ' . print_r($classes_data, true ) . '</pre>';

  $data['labels'] = [
    'dates'           => __( 'Dates', 'andalu_woo_courses' ),
    'course'          => __( 'Course', 'andalu_woo_courses' ),
    'location'        => __( 'Location', 'andalu_woo_courses' ),
    'time'            => __( 'Time', 'andalu_woo_courses' ),
    'duration'        => __( 'Duration', 'andalu_woo_courses' ),
    'course_length'   => __( 'Course Length', 'andalu_woo_courses' ),
    'price'           => __( 'Price', 'andalu_woo_courses' ),
    'language'        => __( 'Language', 'andalu_woo_courses' ),
    'register'        => __( 'Register', 'andalu_woo_courses' ),
    'legend' => [
      'confirmed' => __( 'Confirmed Class', 'andalu_woo_courses' ),
    ],
  ];

  wp_enqueue_script( 'class-calendar' );
  return \AndaluWooCourses\handlebars\render_template('public_class_calendar',$data);
}
add_shortcode('public_class_calendar', __NAMESPACE__ . '\\public_class_calendar' );