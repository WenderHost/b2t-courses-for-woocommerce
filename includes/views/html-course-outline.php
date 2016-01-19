<div class="course_outline wc-metabox closed" rel="<?php echo $outline['position']; ?>">
	<h3>
		<a href="#" class="remove_row delete"><?php _e( 'Remove', 'andalu_woo_courses' ); ?></a>
		<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'andalu_woo_courses' ); ?>"></div>
		<strong class="outline_name"><?php echo esc_html( $outline['name'] ); ?></strong>
	</h3>
	<div class="course_outline_data wc-metabox-content">
		<table cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td class="outline_name">
						<label><?php _e( 'Name', 'andalu_woo_courses' ); ?>:</label>
						<input type="text" class="outline_name" name="outlines[<?php echo $i; ?>][name]" value="<?php echo esc_attr( $outline['name'] ); ?>" />
						<input type="hidden" name="outlines[<?php echo $i; ?>][position]" class="outline_position" value="<?php echo esc_attr( $outline['position'] ); ?>" />
					</td>
				</tr>
				<tr>
					<td class="outline_duration">
						<label><?php _e( 'Duration', 'andalu_woo_courses' ); ?>:</label>
						<input type="text" class="outline_duration" name="outlines[<?php echo $i; ?>][duration]" value="<?php echo esc_attr( $outline['duration'] ); ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<label><?php _e( 'Content', 'andalu_woo_courses' ); ?>:</label>
						<?php if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) : // TODO show textarea in an ajax call until we fix wp_editor loading in ajax ?>
							<textarea name="outlines[<?php echo $i; ?>][content]" cols="5" rows="5"><?php echo esc_textarea( $outline['content'] ); ?></textarea>
						<?php else : ?>
							<?php wp_editor( $outline['content'], 'course_outline_content_' . $i, array( 'textarea_name' => 'outlines[' . $i . '][content]' ) ); ?>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>