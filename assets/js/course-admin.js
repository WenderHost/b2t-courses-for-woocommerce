jQuery(document).ready(function($) {
	
	if ( typeof Andalu_Woo_Courses == 'undefined' || ! Andalu_Woo_Courses ) return;

	$.extend({
		showHideCourseMeta: function(){
			if ($('select#product-type').val() == Andalu_Woo_Courses.product_type) {
				$( 'input#_virtual' ).prop( 'checked', true ).change();
				$('.show_if_simple').show();
				$('.show_if_course').show();
				$('.hide_if_course').hide();
			} else {
				$('.show_if_course').hide();
				$( 'input#_virtual' ).prop( 'checked', false ).change();
			}
		},
	});

	$('body').bind('woocommerce-product-type-change',function(){
		$.showHideCourseMeta();
	});

	// Initial hide / show
	$.showHideCourseMeta();



	// Datepicker fields
	$( document.body ).on( 'wc-datepicker-init', function() {
		$( '.course_date' ).each( function() {
			var dates = $( this ).datepicker({
				defaultDate: '',
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
			});
		});
	}).trigger( 'wc-datepicker-init' );


	// Course Outline Tables

	// Initial order
	var course_outline_items = $('.course_outlines').find('.course_outline').get();

	course_outline_items.sort(function(a, b) {
	   var compA = parseInt( $( a ).attr( 'rel' ), 10 );
	   var compB = parseInt( $( b ).attr( 'rel' ), 10 );
	   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
	});
	$( course_outline_items ).each( function( idx, itm ) {
		$( '.course_outlines' ).append(itm);
	});

	function course_outline_row_indexes() {
		$( '.course_outlines .course_outline' ).each( function( index, el ) {
			$( '.outline_position', el ).val( parseInt( $( el ).index( '.course_outlines .course_outline' ), 10 ) );
		});
	}

	// Add rows
	$( 'button.add_course_outline' ).on( 'click', function() {
		var size         = $( '.course_outlines .course_outline' ).size();
		var $wrapper     = $( this ).closest( '#course_outline_data' ).find( '.course_outlines' );
		var data         = {
			action:   'woocommerce_add_course_outline',
			i:        size,
			security: Andalu_Woo_Courses.add_course_outline_nonce
		};

		$wrapper.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 }	});

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
			var editor_id = 'course_outline_content_' + size;
			$wrapper.append( response );
						
			course_outline_row_indexes();
			$wrapper.unblock();

			$( document.body ).trigger( 'woocommerce_added_course_outline' );
		});

		return false;
	});

	$( '.course_outlines' ).on( 'blur', 'input.outline_name', function() {
		$( this ).closest( '.course_outline' ).find( 'strong.outline_name' ).text( $( this ).val() );
	});

	$( '.course_outlines' ).on( 'click', '.remove_row', function() {
		if ( window.confirm( Andalu_Woo_Courses.remove_outline ) ) {
			var $parent = $( this ).parent().parent();
			$parent.find( 'select, input[type=text], textarea' ).val('');
			$parent.hide();
			course_outline_row_indexes();
		}
		return false;
	});

	// Outline ordering
	$( '.course_outlines' ).sortable({
		items: '.course_outline',
		cursor: 'move',
		axis: 'y',
		handle: 'h3',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
			course_outline_row_indexes();
		}
	});

	// Save outlines
	$( '.save_course_outlines' ).on( 'click', function() {

		var $wrapper = $( '#woocommerce-product-data' );
		$wrapper.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 }	});

		var data = {
			post_id:  woocommerce_admin_meta_boxes.post_id,
			data:     $( '.course_outlines' ).find( 'input, select, textarea' ).serialize(),
			action:   'woocommerce_save_course_outlines',
			security: Andalu_Woo_Courses.save_course_outlines_nonce
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function() {
			$wrapper.unblock();
		});
	});



	// Course Classes Tables

	// Initial order
	var course_classes_items = $('.course_classes').find('.course_class').get();

	course_classes_items.sort(function(a, b) {
	   var compA = parseInt( $( a ).attr( 'rel' ), 10 );
	   var compB = parseInt( $( b ).attr( 'rel' ), 10 );
	   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
	});

	$( course_classes_items ).each( function( idx, itm ) {
		$( '.course_classes' ).append(itm);
	});

	function course_classes_row_indexes() {
		$( '.course_classes .course_class' ).each( function( index, el ) {
			$( '.class_position', el ).val( parseInt( $( el ).index( '.course_classes .course_class' ), 10 ) );
		});
	}

	// Add rows
	$( 'button.add_course_class' ).on( 'click', function() {
		var size         = $( '.course_classes .course_class' ).size();
		var $wrapper     = $( this ).closest( '#course_classes_data' ).find( '.course_classes' );
		var data         = {
			action:   'woocommerce_add_course_class',
			i:        size,
			security: Andalu_Woo_Courses.add_course_class_nonce
		};

		$wrapper.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 }	});

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
			$wrapper.append( response );

			$( document.body ).trigger( 'wc-datepicker-init' );
			course_classes_row_indexes();
			$wrapper.unblock();

			$( document.body ).trigger( 'woocommerce_added_course_class' );
		});

		return false;
	});

	// Set class name
	$( '.course_classes' ).on( 'change', 'input.class_start_date, input.class_end_date', function() {
		var wrapper = $( this ).closest( '.course_class' );
		var start = wrapper.find( 'input.class_start_date' ).val(), end = wrapper.find( 'input.class_end_date' ).val() ;
		wrapper.find( 'strong.class_date' ).text( start + ( end ? ' - ' + end : '' ) );
	});

	// Toggle seats when virtual is selected 
	$( '.course_classes' ).on( 'change', 'select.class_location', function() {
		var t = $(this), label = t.find('option:selected').text(), seats = t.parents('table').find('td.class_seats').parent();
		
		if ( 'Virtual' == label ) {
			seats.slideUp();
		} else {
			seats.slideDown();
		}
	}).find( 'select.class_location' ).change();

	// Remove class
	$( '.course_classes' ).on( 'click', '.remove_row', function() {
		if ( window.confirm( Andalu_Woo_Courses.remove_class ) ) {
			var $parent = $( this ).parent().parent();
			var class_id = $parent.find('[name*="[id]"]').val(), class_ids = [], data = { action: 'woocommerce_remove_course_class' };
			console.debug( $parent, class_id );

			$parent.block();

			if ( 0 < class_id ) {
				class_ids.push( class_id );

				data.class_ids = class_ids;
				data.security  = Andalu_Woo_Courses.remove_course_class_nonce;

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function() {
					$( '#woocommerce-product-data' ).trigger( 'woocommerce_course_class_removed' );
				});

			} else {
				$parent.unblock();
			}

			$parent.find( 'select, input[type=text], textarea' ).val('');
			$parent.hide();
			course_classes_row_indexes();

		}
		return false;
	});

	// Class ordering
	$( '.course_classes' ).sortable({
		items: '.course_class',
		cursor: 'move',
		axis: 'y',
		handle: 'h3',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
			course_classes_row_indexes();
		}
	});

	// Save classes
	$( '.save_course_classes' ).on( 'click', function() {

		var $wrapper = $( '#woocommerce-product-data' );
		$wrapper.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 }	});

		var data = {
			post_id:  woocommerce_admin_meta_boxes.post_id,
			data:     $( '.course_classes' ).find( 'input, select, textarea' ).serialize(),
			action:   'woocommerce_save_course_classes',
			security: Andalu_Woo_Courses.save_course_classes_nonce
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function() {
			$wrapper.unblock();
			
			$( '#woocommerce-product-data' ).trigger( 'load_classes' );
		});
	});
	

	// Load classes	
	$( '#woocommerce-product-data' ).on( 'load_classes', function () {
		var wrapper = $( '#course_classes_data .course_classes' );

		wrapper.block();

		$.ajax({
			url: woocommerce_admin_meta_boxes_variations.ajax_url,
			data: {
				action:     'woocommerce_load_course_classes',
				security:   Andalu_Woo_Courses.load_course_classes_nonce,
				product_id: woocommerce_admin_meta_boxes.post_id,
			},
			type: 'POST',
			success: function( response ) {
				wrapper.empty().append( response );
				wrapper.find( '.wc-metabox.closed .wc-metabox-content' ).hide();
				course_classes_row_indexes();
				$( document.body ).trigger( 'wc-datepicker-init' );

				$( '#woocommerce-product-data' ).trigger( 'woocommerce_course_classes_loaded' );

				wrapper.unblock();
			}
		});
	});


});
