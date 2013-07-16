<?php
App::uses('UserBaseAuthenticate', 'User.Controller/Component/Auth');

/**
 * An authentication adapter for AuthComponent.  Provides the ability to authenticate using POST
 * data.  Can be used by configuring AuthComponent to use it via the AuthComponent::$authenticate setting
 * 
 * It will authenticate a user with their own salt and support to check multiple fields
 * for username ie. username input can be checked against a username and email address
 * 
 * In your controller's components array, add auth + the required settings.
 * {{{
 *	$this->Auth->authenticate = array(
 *		'UserForm' => array(
 *			'scope' => array('User.active' => 1)
 *		)
 *	)
 * }}}
 *
 */
class UserFormAuthenticate extends UserBaseAuthenticate {

/**
 * Authenticates the identity contained in a request.  Will use the `settings.userModel`, and `settings.fields`
 * to find POST data that is used to find a matching record in the `settings.userModel`.  Will return false if
 * there is no post data, either username or password is missing, of if the scope conditions have not been met.
 *
 * @param CakeRequest $request The request that contains login information.
 * @param CakeResponse $response Unused response object.
 * @return mixed.  False on login failure.  An array of User data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$userModel = $this->settings['userModel'];
		list($plugin, $model) = pluginSplit($userModel);

		// check if fields are set in request
		$fields = $this->settings['fields'];
		if (empty($request->data[$model])) {
			return false;
		}
		if (!is_array($fields['username'])) {
			if (empty($request->data[$model][$fields['username']])) {
				return false;
			}
			$username_value = $request->data[$model][$fields['username']];
		}
		else {
			$found = false;
			foreach ($fields['username'] as $username_field) {
				if (array_key_exists($username_field, $request->data[$model])) {
					$found = true;
					$username_value = $request->data[$model][$username_field];
					break;
				}
			}
			if (!$found) {
				return false;
			}
		}
		if (empty($request->data[$model][$fields['password']])) {
			return false;
		}
		return $this->_findUser(
			$username_value,
			$request->data[$model][$fields['password']]
		);
	}

}
