<table class='nsm_file-options'>
	<thead>
		<tr>
			<th style='width:50px;' scope='col'>&nbsp;</th>
			<th style='width:50px;' scope='col'>Display</th>
			<th scope='col'>Options</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($text_fields as $field) : $field_settings = $settings['fields'][$field]; ?>
		<tr>
			<th scope='row'><?= lang(ucfirst($field)); ?></th>
			<td>
				<?= form_checkbox(array(
						'name' => "{$input_name}[fields][{$field}][display]",
						'value' => 1,
						'checked' => $field_settings['display']
				)); ?>
			<td>
				<label>
					<span>Height: </span>
					<?= 
						form_input(array(
							'name' => "{$input_name}[fields][{$field}][height]",
							'value' => $field_settings['height'],
							'placeholder' => 'px',
						))
					?>
				</label>
				<label>
					<span>Width: </span>
					<?= 
						form_input(array(
							'name' => "{$input_name}[fields][{$field}][width]",
							'value' => $field_settings['width'],
							'placeholder' => 'px',
						))
					?>
					
				</label>
			</td>
		</tr>
		<?php endforeach; ?>

		<?php foreach($select_fields as $field) : $field_settings = $settings['fields'][$field]; ?>
		<tr>
			<th scope='row'><?= lang(ucfirst($field)); ?></th>
			<td>
				<?= form_checkbox(array(
						'name' => "{$input_name}[fields][{$field}][display]",
						'value' => 1,
						'checked' => $field_settings['display']
				)); ?>
			</td>
			<td>
				<?= 
					form_textarea(array(
						'name' => "{$input_name}[fields][{$field}][options]",
						'value' => $field_settings['options'],
						'rows' => 4
					))
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>