<div class="users login">
	<?php
	echo $this->Form->create($model);
	echo $this->Form->inputs(array(
		'legend' => __('Login'),
		'username',
		'password'
	));
	?>
	<p>
		<?php echo $this->Html->link(__('I forgot my password'), array('action' => 'forgotten_password')); ?>
	</p>
	<?php
	echo $this->Form->end(__('Login'));
	?>
</div>