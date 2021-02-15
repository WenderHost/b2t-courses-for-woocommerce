<?php
$atts = [
	'position',
	'id',
	'time',
	'duration',
	'seats',
	'id',
	'end_date',
	'start_date',
	'location',
	'confirmed',
	'lang',
	'cal',
];

foreach( $atts as $att ){
	if( isset( $class->$att ) ){
		switch( $att ){
			case 'cal':
			case 'lang':
				$$att = get_post_meta( $class->get_id(), '_' . $att, true );
				break;

			case 'location':
				$class_location = $class->location;
				break;

			case 'id':
				$$att = $class->get_id();
				break;

			default:
				$$att = $class->$att;
				break;
		}
	} else {
		$$att = '';
	}
}

$startDateObj = new DateTime( $start_date );
if( ! empty( $end_date ) )
	$endDateObj = new DateTime( $end_date );
$startDateFormat = ( empty( $end_date ) || $startDateObj->format('Y') != $endDateObj->format('Y') )? 'D, M j, Y' : 'D, M j';
$classDateStr = $startDateObj->format( $startDateFormat );
if( ! empty( $end_date ) )
	$classDateStr.= ' &ndash; ' . $endDateObj->format('D, M j, Y');

?>
<div class="course_class wc-metabox closed" rel="<?php echo $class->position; ?>">
	<h3>
		<a href="#" class="remove_row delete"><?php _e( 'Remove', 'andalu_woo_courses' ); ?></a>
		<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'andalu_woo_courses' ); ?>"></div>
		<strong class="class_date"><?= $classDateStr ?></strong>
	</h3>
	<div class="course_class_data wc-metabox-content">
		<table cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td class="class_start_date">
						<label><?php _e( 'Date', 'andalu_woo_courses' ); ?>:</label>
						<input type="text" class="class_start_date course_date" name="classes[<?php echo $i; ?>][start_date]" value="<?php echo esc_attr( $start_date ); ?>" placeholder="YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
						<span class="seperator">&ndash;</span>
						<input type="text" class="class_end_date course_date" name="classes[<?php echo $i; ?>][end_date]" value="<?php echo esc_attr( $end_date ); ?>" placeholder="YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
						<input type="hidden" name="classes[<?php echo $i; ?>][position]" class="class_position" value="<?php echo esc_attr( $position ); ?>" />
						<input type="hidden" name="classes[<?php echo $i; ?>][id]" value="<?php echo esc_attr( $id ); ?>" />
					</td>
				</tr>
				<tr>
					<td class="class_time">
						<label><?php _e( 'Time', 'andalu_woo_courses' ); ?>:</label>
						<input type="text" class="class_time" name="classes[<?php echo $i; ?>][time]" value="<?php echo esc_attr( $time ); ?>" />
					</td>
				</tr>
				<tr>
					<td class="class_duration">
						<label><?php _e( 'Class Duration', 'andalu_woo_courses' ); ?>:</label>
						<input type="text" class="class_duration" name="classes[<?php echo $i; ?>][duration]" value="<?php echo esc_attr( $duration ); ?>" />
					</td>
				</tr>
				<tr>
					<td class="class_location">
						<label><?php _e( 'Location', 'andalu_woo_courses' ); ?>:</label>
						<select class="class_location" name="classes[<?php echo $i; ?>][location]">
						<?php foreach( $locations as $location_id => $location ) : ?>
							<option value="<?php echo $location_id; ?>" <?php selected( $location_id, $class_location ); ?>><?php echo $location; ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="class_seats">
						<label><?php _e( 'Seats available', 'andalu_woo_courses' ); ?>:</label>
						<input type="number" class="class_seats" name="classes[<?php echo $i; ?>][seats]" value="<?php echo esc_attr( $seats ); ?>" />
					</td>
				</tr>
				<tr>
					<td class="class_confirmed">
						<input type="checkbox" class="class_confirmed" name="classes[<?php echo $i; ?>][confirmed]" value="yes" <?php checked( $confirmed ); ?> />
						<span><?php _e( 'Has this class been confirmed?', 'andalu_woo_courses' ); ?></span>
					</td>
				</tr>
				<tr>
					<td class="class_lang">
						<label for=""><?php _e( 'Class language', 'andalu_woo_courses' ); ?>:</label>
						<div class="flex-row">
							<input type="radio" class="class_lang" name="classes[<?php echo $i; ?>][lang]" id="lang_en_<?= $i ?>" value="en" <?php checked( $lang, 'en' ); ?> />
							<label for="lang_en_<?= $i ?>"><?php _e( 'English', 'andalu_woo_courses' ); ?></label>
							<input type="radio" class="class_lang" name="classes[<?php echo $i; ?>][lang]" id="lang_es_<?= $i ?>" value="es" <?php checked( $lang, 'es' ); ?> />
							<label for="lang_es_<?= $i ?>"><?php _e( 'Spanish', 'andalu_woo_courses' ); ?></label>
						</div>
					</td>
				</tr>
				<tr>
					<td class="class_lang">
						<label for=""><?php _e( 'Calendar Display', 'andalu_woo_courses' ); ?>:</label>
						<div class="flex-row">
							<input type="checkbox" class="class_cal" name="classes[<?= $i; ?>][cal][]" id="cal_en_<?= $i ?>" value="en" <?php if( is_array( $cal ) && in_array( 'en', $cal ) ){ echo ' checked="checked"';} ?> />
							<label for="cal_en_<?= $i ?>"><?php _e( 'US', 'andalu_woo_courses' ); ?></label>
							<input type="checkbox" class="class_cal" name="classes[<?= $i; ?>][cal][]" id="cal_es_<?= $i ?>" value="es" <?php if( is_array( $cal ) && in_array( 'es', $cal ) ){ echo ' checked="checked"';} ?> />
							<label for="cal_es_<?= $i ?>"><?php _e( 'Spain', 'andalu_woo_courses' ); ?></label>
						</div>
					</td>
				</tr>
				<?php /**/ ?>
			</tbody>
		</table>
	</div>
</div>