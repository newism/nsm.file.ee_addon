<?php if($file['error']) : ?>
<div class='nsm_file_ft-error'><?= $file['error'] ?></div>
<?php endif; ?>

<div class='nsm_file_ft <?= $layout ?>' data-filename='<?= $file['filename'] ?>'>

	<div class='nsm_file-add' <?php if($file['filename']) : ?>style='display:none'<?php endif; ?>>
		<button class='add-file submit submit_alt'>Add file</button>
	</div>

	<div class='nsm_file-preview' <?php if(!$file['filename']) : ?>style='display:none'<?php endif; ?>>
		<?php if($file['filename']) : ?>
			<a href="<?= $file['path'] . $file['filename'] ?>" target="_blank">
				<img src="<?= $file['thumb']?>" />
				<?= $file['filename'] ?>
			</a>
		<?php endif; ?>
		<button class='remove-file submit submit_alt'>Remove file</button>
	</div>

	<?php if($caption) : ?>
		<div class='nsm_file-caption'>
			<?= $caption['label'] ?>
			<?= $caption['field'] ?>
		</div>
	<?php endif; ?>
	
	<?php if($credit) : ?>
		<div class='nsm_file-credit'>
			<?= $credit['label'] ?>
			<?= $credit['field'] ?>
		</div>
	<?php endif; ?>
	
	<?php if($subject) : ?>
		<div class='nsm_file-subject'>
			<?= $subject['label'] ?>
			<?= $subject['field'] ?>
		</div>
	<?php endif; ?>
	
	<?php if($style) : ?>
		<div class='nsm_file-style'>
			<?= $style['label'] ?>
			<?= $style['field'] ?>
		</div>
	<?php endif; ?>
	
	<?php if($size) : ?>
		<div class='nsm_file-size'>
			<?= $size['label'] ?>
			<?= $size['field'] ?>
		</div>
	<?php endif; ?>

	<?= form_hidden($input_name.'[filedir]', $file['filedir']) ?>
	<?= form_hidden($input_name.'[filename]', $file['filename']) ?>
	<?= form_hidden($input_name.'[is_image]', $file['is_image']) ?>

</div>