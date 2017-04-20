<?php
$atts = [
	'position',
	'id',
	'time',
	'seats',
	'id',
	'end_date',
	'start_date',
	'location',
	'confirmed'
];

foreach( $atts as $att ){
	if( isset( $class->$att ) ){
		switch( $att ){
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

?>
<div class="course_class wc-metabox closed" rel="<?php echo $class->position; ?>">
	<h3>
		<a href="#" class="remove_row delete"><?php _e( 'Remove', 'andalu_woo_courses' ); ?></a>
		<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'andalu_woo_courses' ); ?>"></div>
		<strong class="class_date"><?php echo esc_html( $start_date . ( empty( $end_date ) ? '' : ' &ndash; ' . $end_date ) ); ?></strong>
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
						<input type="checkbox" class="class_confirmed" name="classes[<?php echo $i; ?>][confirmed]" <?php checked( $confirmed ); ?> />
						<span><?php _e( 'Has this class been confirmed?', 'andalu_woo_courses' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>