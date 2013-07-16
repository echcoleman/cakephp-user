<div class="users form">
<?php echo $this->Form->create($model);?>
	<fieldset>
		<legend><?php echo __('Update your profile'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('username');
		echo $this->Form->input('email');
		echo $this->Form->input('password', array('value' => '', 'after' => __('Only edit password if you wish to update it!')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
