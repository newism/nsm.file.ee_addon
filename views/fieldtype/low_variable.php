<?= $file['hidden_fields'] ?>

<?php if($caption) : ?>
	<?= $caption['label'] ?>
	<?= $caption['field'] ?>
<?php endif; ?>

<?php if($credit) : ?>
	<?= $credit['label'] ?>
	<?= $credit['field'] ?>
<?php endif; ?>

<?php if($subject) : ?>
	<?= $subject['label'] ?>
	<?= $subject['field'] ?>
<?php endif; ?>

<?php if($style) : ?>
	<?= $style['label'] ?>
	<?= $style['field'] ?>
<?php endif; ?>

<?php if($size) : ?>
	<?= $size['label'] ?>
	<?= $size['field'] ?>
<?php endif; ?>