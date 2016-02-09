<?php
/**
 * The template for displaying the course registration form
 *
 * Override this template by copying it to yourtheme/content-single-product-registration.php
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
do_action( 'woocommerce_before_single_course_register' );

?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'woocommerce_course_before_registration_form' ); ?>
	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="course_registration clearfix" method="post" enctype="multipart/form-data">
		<?php wc_print_notices(); ?>

	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
			if ( 'virtual' == get_query_var( 'course_register' ) && $product->has_child() ) {
				do_action( 'woocommerce_course_virtual_registration_form' );
			} else {
				echo '<p>' . __( 'To register for this class complete the form below:', 'andalu_woo_courses' ) . '</p>';
			}
		?>

		<div class="course_registration_form_fields clearfix">
			<?php if ( Andalu_Woo_Courses_Class::can_register() ) : ?>

				<?php do_action( 'woocommerce_course_registration_form' ); ?>

		 		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

				<div class="pull-right">
					<a href="<?php the_permalink(); ?>" class="button button-default"><?php _e( 'Back', 'andalu_woo_courses' ); ?></a>
				 	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
				</div>

			<?php else : ?>

				<p><?php _e( 'This class is not available for registration', 'andalu_woo_courses' ); ?>

			<?php endif; ?>
		</div>

		<?php wc_get_template( 'single-product/course-info.php' ); ?>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>


	<?php do_action( 'woocommerce_course_after_registration_form' ); ?>
	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

</div><!-- #product-<?php the_ID(); ?> -->