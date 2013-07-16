<?php

/**
 * User Authentication Helper to get current logged in user status and details
 */
class UserAuthHelper extends AppHelper {

	/**
	 * This helper uses following helpers
	 *
	 * @var array
	 */
	public $helpers = array('Session');

	/**
	 * Used to check whether user is logged in or not
	 *
	 * @access public
	 * @return boolean
	 */
	public function loggedIn() {
		return $this->user() != array();
	}

/**
 * Get the current user or user value by passed key
 * 
 * @param string $key field to retrieve.  Leave null to get entire User record
 * @return mixed User record. or null if no user is logged in.
 */
	public static function user($key = null) {
		if ($key == 'group') {
			$key = 'Group.key';
		}
		
		$user = CakeSession::read(AuthComponent::$sessionKey);
		if ($key === null) {
			return $user;
		}
		return Hash::get($user, $key);
	}
}