<div class="users forgotten_password">
<?php echo $this->Form->create($model);?>
	<fieldset>
		<legend><?php echo __('Forgotten Password'); ?></legend>
		<h3><?php echo __('Enter your username or email address'); ?></h3>
	<?php echo $this->Form->input('username', array('label' => __('Username/Email address'))); ?>
<?php echo $this->Form->end(__('Submit')); ?>
</div>