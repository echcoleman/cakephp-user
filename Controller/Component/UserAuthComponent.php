<?php
App::uses('AuthComponent', 'Controller/Component');

/**
 * UserAuthComponent
 * 
 * Adds additional information to return about current logged in user
 */
class UserAuthComponent extends AuthComponent {

/**
 * Settings for this object.
 *
 * - `fields` The fields to use to identify a user by.
 * - `userModel` The model name of the User, defaults to User.
 * - `scope` Additional conditions to use when looking up and authenticating users,
 *    i.e. `array('User.is_active' => 1).`
 * - `recursive` The value of the recursive key passed to find(). Defaults to 0.
 * - `contain` Extra models to contain and store in session.
 * - `realm` The realm authentication is for.  Defaults the server name.
 *
 * @var array
 */
	public $settings = array(
		'fields' => array(
			'username' => 'username',
			'password' => 'password'
		),
		'userModel' => 'User.User',
		'scope' => array(),
		'recursive' => 0,
		'contain' => null,
		'realm' => '',
	);

/**
 * Constructor, completes configuration for basic authentication.
 *
 * @param ComponentCollection $collection The Component collection used on this request.
 * @param array $settings An array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (!empty($settings)) {
			$settings = Hash::merge($this->settings, $settings);
		}
		parent::__construct($collection, $settings);
	}

/**
 * Get the current user.
 * Works exactly like parent AuthComponent function except it can return the group key.
 * To retrieve group key pass the key as 'group'
 * 
 * @param string $key field to retrieve.  Leave null to get entire User record
 * @return mixed User record. or null if no user is logged in.
 */
	public static function user($key = null) {
		if ($key == 'group') {
			$key = 'Group.key';
		}
		return parent::user($key);
	}
	
}