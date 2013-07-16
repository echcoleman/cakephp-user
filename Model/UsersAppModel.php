<?php
App::uses('Model', 'Model');

/**
 * Users App Model
 *
 * @package User
 * @subpackage User.UserAppModel
 */
class UsersAppModel extends AppModel {

/**
 * Plugin name
 *
 * @var string $plugin
 */
	public $plugin = 'Users';

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array('Containable');
}
