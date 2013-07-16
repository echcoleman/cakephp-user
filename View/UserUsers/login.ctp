<div class="users login">
	<?php
	echo $this->Form->create($model);
	echo $this->Form->inputs(array(
		'legend' => __('Login'),
		'username',
		'password'
	));
	echo $this->Form->end('Login');
	?>
</div>