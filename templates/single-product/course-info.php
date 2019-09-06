<?php
global $product;

$class_slug = get_query_var( 'course_register' );
$class_id = 'virtual' == $class_slug ? 0 : Andalu_Woo_Courses_Class::get_class_id( $class_slug );
$class = $class_id ? wc_get_product( $class_id ) : false;
$virtual = ( 'virtual' == $class_slug || ( $class && $class->virtual ) );

?>
<div class="product-info clearfix">

	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

	<?php if ( $virtual ) : ?>

	<p><?php printf( '<strong>%s</strong>: %s/%s', __( 'Price', 'andalu_woo_courses' ), $product->get_price_html(), __( 'student', 'andalu_woo_courses' ) ); ?></p>

	<?php
		$term = get_term_by( 'slug', 'virtual', 'class_location' );
		if ( ! empty( $term->term_id ) ) {
			$location_desc = term_description( $class->location, 'class_location' );
			if( ! empty( $location_desc ) )
				echo wpautop( $location_desc );
		}
	?>

	<?php elseif ( $class_id ) : ?>

	<p><?php echo get_the_title( $class_id ); ?><br/><?php echo get_post_meta( $class_id, '_time', true ); ?><br />
	<?php printf( '<strong>%s</strong>: %s', __( 'Metro Area', 'andalu_woo_courses' ), $class->get_location() ); ?><br />
	<?php printf( '<strong>%d</strong> %s', $class->seats, _n( 'seat available', 'seats available', $class->seats, 'andalu_woo_courses' ) ); ?><br />
	<?php printf( '<strong>%s</strong>: %s/%s', __( 'Price', 'andalu_woo_courses' ), $product->get_price_html(), __( 'student', 'andalu_woo_courses' ) ); ?></p>

	<?php
		$location = get_term( $class->location );
		$location_name = $location->name;

		$location_desc = term_description( $class->location, 'class_location' );
		if ( ! empty( $location_desc ) ) {
			echo  wpautop( '<strong>' . __( 'Location:', 'andalu_woo_courses' ) . '</strong><br />' . $location_name . '<br />' . $location_desc );
		}


	?>

	<?php endif; ?>

</div>