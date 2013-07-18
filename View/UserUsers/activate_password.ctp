<?php
/*
	This file is part of UserMgmt.

	Author: Chetan Varshney (http://ektasoftwares.com)

	UserMgmt is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UserMgmt is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<div class="users activate_password">
	<?php echo $this->Form->create('User', array('action' => 'activatePassword')); ?>
	<fieldset>
	<?php
		echo $this->Form->input('password');
		echo $this->Form->input('cpassword', array('type' => 'password', 'label' => __('Confirm Password')));
		
		if (!isset($ident)) {
			$ident='';
		}
		if (!isset($activate)) {
			$activate='';
		}
		echo $this->Form->hidden('ident',array('value'=>$ident));
		echo $this->Form->hidden('activate',array('value'=>$activate));
	?>
	</fieldset>
	<?php echo $this->Form->end(__('Reset')); ?>
</div>