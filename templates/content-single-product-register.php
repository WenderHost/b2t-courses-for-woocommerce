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

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'woocommerce_course_before_registration_form' ); ?>
	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<?php if ( Andalu_Woo_Courses_Class::can_register() ) : ?>
	<form class="course_registration clearfix" method="post" enctype="multipart/form-data">
		<?php wc_print_notices(); ?>

	 	

		<?php
			if ( 'virtual' == get_query_var( 'course_register' ) && $product->has_child() ) {
				do_action( 'woocommerce_course_virtual_registration_form' );
			} else {
				echo sprintf( __( 'To register for this class complete the form below, or you may return to the <a href="%s">Public Class Schedule</a>.', 'andalu_woo_courses' ), site_url( '/services/public-class-schedule/' ) );
			}
		?>

		<div class="course_registration_form_fields clearfix">

			<div class="class-details">
				<?php wc_get_template( 'single-product/course-info.php' ); ?>
			</div>


				<?php do_action( 'woocommerce_course_registration_form' ); ?>

                <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
            
		 		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

				<div class="pull-right">
					<a href="<?php the_permalink(); ?>" class="button button-default"><?php _e( 'Back', 'andalu_woo_courses' ); ?></a>
				 	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
				</div>

		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
	<?php else : ?>
		<?php
		// Check if there's an Elementor template named "Class Full Message"
		$template_name = 'Class Full Message';
		$template_id = '';

		$elementor_templates = get_posts([
		    'post_type' => 'elementor_library',
		    'name' => sanitize_title($template_name),
		    'posts_per_page' => 1
		]);

		// If the template is found, get its ID
		if ($elementor_templates) {
		    $template_id = $elementor_templates[0]->ID;
		}

		if ($template_id) {
		    // Display the Elementor template if found
		    echo \Elementor\Plugin::instance()->frontend->get_builder_content($template_id);

		    // Provide an "Edit" link for admins
		    if (current_user_can('administrator')) {
		        $edit_link = get_edit_post_link($template_id);
		        echo '<div style="background-color: #eee; padding: 1em; border-radius: 5px;"><a href="' . esc_url($edit_link) . '" target="_blank">Edit "Class Full Message" Template &rarr;</a><br>This message visible only to logged in administrators.</div>';
		    }
		} else {
		    // Default content to display when the template is not found
		    ?>
		<div class="alert alert-info">
			<h3>Class No Longer Available &ndash; <a href="<?php the_permalink() ?>">Please choose another session &rarr;</a></h3>
			<p><?php _e( 'The <em>' . get_the_title() . '</em> class you\'re trying to register for is no longer available. Please check the <a href="' . get_permalink() . '">' . get_the_title() . ' page</a> for an updated list of available classes for this course.', 'andalu_woo_courses' ); ?>
		</div>
		    <?php

		    // Message for logged-in admins with instructions on creating the custom template
		    if (current_user_can('administrator')) {
		    	?>
		    	<div style="padding: 1em; background-color: #eee; border-radius: 5px;">
		        <p><strong>Admin Notice:</strong> No custom "Class Full Message" template found. To create a custom message:</p>
		        <ol>
			        <li>Go to the Elementor Library in the WordPress dashboard.</li>
			        <li>Create a new template and title it "Class Full Message".</li>
			        <li>Customize the template with your message for full classes.</li>
		        </ol>
		      </div>
		    	<?php

		    }
		}
		?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_course_after_registration_form' ); ?>
	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

</div><!-- #product-<?php the_ID(); ?> -->