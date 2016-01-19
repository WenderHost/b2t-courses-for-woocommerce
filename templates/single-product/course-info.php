<?php
global $product;

$class_slug = get_query_var( 'course_register' );
$class_id = 'virtual' == $class_slug ? 0 : Andalu_Woo_Courses_Class::get_class_id( $class_slug );
$class = $class_id ? wc_get_product( $class_id ) : false;
$virtual = ( 'virtual' == $class_slug || ( $class && $class->virtual ) );

?>
<div class="product-info clearfix">

	<h2><?php the_title(); ?><h2>
	
	<?php if ( $virtual ) : ?>

	<p><?php printf( '<strong>%s</strong>: %s/%s', __( 'Price', 'andalu_woo_courses' ), $product->get_price_html(), __( 'student', 'andalu_woo_courses' ) ); ?></p>

	<?php
		$term = get_term_by( 'slug', 'virtual', 'class_location' );
		if ( ! empty( $term->term_id ) ) {
			$address = get_term_meta( $term->term_id, 'address', true );
		}
		
		if ( ! empty( $address ) ) {
			echo '<br/>';
			echo wpautop( $address );
		}
	
	?>

	<?php elseif ( $class_id ) : ?>

	<p><?php echo get_the_title( $class_id ); ?></p>
	<p><?php printf( '<strong>%s</strong>: %s', __( 'Metro Area', 'andalu_woo_courses' ), $class->get_location() ); ?></p>
	<p><?php printf( '<strong>%d</strong> %s', $class->seats, _n( 'seat available', 'seats available', $class->seats, 'andalu_woo_courses' ) ); ?></p>
	<p><?php printf( '<strong>%s</strong>: %s/%s', __( 'Price', 'andalu_woo_courses' ), $product->get_price_html(), __( 'student', 'andalu_woo_courses' ) ); ?></p>
	
	<?php
		$address = get_term_meta( $class->location, 'address', true );
		
		if ( ! empty( $address ) ) {
			echo '<br/><h4>' . __( 'Location:', 'andalu_woo_courses' ) . '</h4>';
			echo wpautop( $address );
		}
	
	?>
	
	<?php endif; ?>

</div>