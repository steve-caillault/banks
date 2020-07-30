<?php use Root\Arr; ?>

<form <?php echo $attributes; ?>>
	<?php if($withTitle): ?>
	<h2><?php echo $title; ?></h2>
	<?php endif; ?>
	<fieldset>
		<?php foreach($inputs['fields'] as $key => $input): ?>
		<div class="form-input">
			<?php echo Arr::get($labels, $key); ?>
			<?php echo $input; ?>
			<?php if(($error = Arr::get($errors, $key))): ?>
			<p class="error"><?php echo $error; ?></p>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
		<?php foreach($inputs['hidden'] as $input): ?>
		<?php echo $input; ?>
		<?php endforeach; ?>
		<?php echo $inputs['name']; ?>
		<?php echo $inputs['submit']; ?>
	</fieldset>
</form>