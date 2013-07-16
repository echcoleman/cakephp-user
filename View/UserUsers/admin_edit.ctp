<?php
$action = $this->params['origAction'];
?>
<div class="users form">
<?php echo $this->Form->create($model);?>
	<fieldset>
		<legend><?php echo __('%s User', Inflector::humanize($action)); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('group_id');
		echo $this->Form->input('username');
		echo $this->Form->input('email');
		$password_options = array('value' => '');
		if ($action != 'add') {
			$password_options['after'] = __('Only edit password if you wish to update it!');
		}
		echo $this->Form->input('password', $password_options);
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
	<?php if (strpos($action, 'add') === false) { ?>
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('User.id')), null, __('Are you sure you want to delete user: %s?', $this->Form->value('User.username'))); ?></li>
	<?php } ?>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>