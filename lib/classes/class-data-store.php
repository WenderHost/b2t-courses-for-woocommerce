<?php
// WC_Product_Data_Store_Interface,
class WC_Product_Course_Class_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

    public function __construct(){

    }

    public function create( &$product ){

    }

    public function update( &$product ){

    }

    /**
     * Method to read a course_class from the database.
     * @param WC_Product
     */
    public function read( &$product ) {
        $product->set_defaults();

        if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || 'course_class' !== $post_object->post_type ) {
            throw new Exception( __( basename(__FILE__). ' line ' . __LINE__ . ' Invalid course_class.', 'woocommerce' ) );
        }

        $id = $product->get_id();

        $product->set_props( array(
            'name'              => $post_object->post_title,
            'slug'              => $post_object->post_name,
            'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
            'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
            'status'            => $post_object->post_status,
            'description'       => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id'         => $post_object->post_parent,
            'menu_order'        => $post_object->menu_order,
            'reviews_allowed'   => 'open' === $post_object->comment_status,
        ) );

        $product->set_object_read( true );
    }

    public function delete( &$product, $args = array() ){

    }

}