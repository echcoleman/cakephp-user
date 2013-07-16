<?php
$action = $this->params['origAction'];
?>
<div class="groups form">
<?php echo $this->Form->create('Group');?>
	<fieldset>
		<legend><?php echo __('%s Group', Inflector::humanize($action)); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('key');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
	<?php if (strpos($action, 'add') === false) { ?>
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Group.id')), null, __('Are you sure you want to delete group: %s?', $this->Form->value('Group.name'))); ?></li>
	<?php } ?>
		<li><?php echo $this->Html->link(__('List Groups'), array('action' => 'index')); ?></li>
	</ul>
</div>