<div class="users forgotten_password">
<?php echo $this->Form->create($model);?>
	<fieldset>
		<legend><?php echo __('Forgotten Password'); ?></legend>
	<?php echo $this->Form->input('username', array('label' => __('Enter your username or email address'))); ?>
<?php echo $this->Form->end(__('Submit')); ?>
</div>