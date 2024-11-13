<?php
use function AndaluWooCourses\shortcodes\class_dates_from_url;

function b2t_course_class_merge_tag( $merge_tags ){
  $merge_tags[] = [
    'label' => 'Class Dates',
    'tag'   => '{class_dates}',
  ];
  return $merge_tags;
}
add_filter( 'gform_custom_merge_tags', 'b2t_course_class_merge_tag', 10, 4 );


function replace_download_link( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
    $custom_merge_tag = '{class_dates}';

    if ( strpos( $text, $custom_merge_tag ) === false ) {
        return $text;
    }

    $class_dates = class_dates_from_url();

    return str_replace( $custom_merge_tag, $class_dates, $text );
}
add_filter( 'gform_replace_merge_tags', 'replace_download_link', 10, 7 );