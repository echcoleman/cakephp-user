<?php

/**
 * User Base Authentication class with common methods and properties.
 */
abstract class UserBaseAuthenticate {

/**
 * Settings for this object.
 *
 * - `fields` The fields to use to identify a user by.
 * - `userModel` The model name of the User, defaults to User.
 * - `scope` Additional conditions to use when looking up and authenticating users,
 *    i.e. `array('User.is_active' => 1).`
 * - `recursive` The value of the recursive key passed to find(). Defaults to 0.
 * - `contain` Extra models to contain and store in session.
 *
 * @var array
 */
	public $settings = array(
		'fields' => array(
			'username' => array('username', 'email'),
			'password' => 'password'
		),
		'userModel' => 'User.User',
		'scope' => array(),
		'recursive' => 0,
		'contain' => null,
	);
	
/**
 * A Component collection, used to get more components.
 *
 * @var ComponentCollection
 */
	protected $_Collection;

/**
 * Constructor
 *
 * @param ComponentCollection $collection The Component collection used on this request.
 * @param array $settings Array of settings to use.
 */
	public function __construct(ComponentCollection $collection, $settings) {
		$this->_Collection = $collection;
		$this->settings = Hash::merge($this->settings, $settings);
	}

/**
 * Find a user record using the standard options.
 *
 * @param mixed $username The username/identifier string/array.
 * @param string $password The unhashed password.
 * @return Mixed Either false on failure, or an array of user data.
 */
	protected function _findUser($username, $password) {
		$userModel = $this->settings['userModel'];
		list($plugin, $model) = pluginSplit($userModel);
		$fields = $this->settings['fields'];
		
		// get conditions
		$conditions = array();
		if (!is_array($fields['username'])) {
			$conditions[ $model . '.' . $fields['username'] ] = $username;
		}
		else {
			$or_conditions = array();
			foreach ($fields['username'] as $username_field) {
				$or_conditions[  $model . '.' . $username_field ] = $username;
			}
			$conditions['OR'] = $or_conditions;
		}
		
		// get user scope
		if (!empty($this->settings['scope'])) {
			$conditions = array_merge($conditions, $this->settings['scope']);
		}
		
		// get users that match
		$result = ClassRegistry::init($userModel)->find('first', array(
			'conditions' => $conditions,
			'recursive' => (int)$this->settings['recursive'],
			'contain' => $this->settings['contain'],
		));
		if (empty($result) || empty($result[$model])) {
			return false;
		}
		
		// authenticate salt hashed password of user
		$password_salted = $password . $result[$model]['salt'];
		if ($result[$model][$fields['password']] != Security::hash($password_salted)) {
			return false;
		}
		
		// matched successfully, set user and return
		$user = $result[$model];
		unset($user[$fields['password']]);
		unset($result[$model]);
		return array_merge($user, $result);
	}
	
/**
 * Hash the plain text password so that it matches the hashed/encrypted password
 * in the datasource.
 *
 * @param string $password The plain text password.
 * @return string The hashed form of the password.
 */
	protected function _password($password) {
		return Security::hash($password, null, true);
	}

/**
 * Authenticate a user based on the request information.
 *
 * @param CakeRequest $request Request to get authentication information from.
 * @param CakeResponse $response A response object that can have headers added.
 * @return mixed Either false on failure, or an array of user data on success.
 */
	abstract public function authenticate(CakeRequest $request, CakeResponse $response);

/**
 * Allows you to hook into AuthComponent::logout(),
 * and implement specialized logout behavior.
 *
 * All attached authentication objects will have this method
 * called when a user logs out.
 *
 * @param array $user The user about to be logged out.
 * @return void
 */
	public function logout($user) {
	}

/**
 * Get a user based on information in the request.  Primarily used by stateless authentication
 * systems like basic and digest auth.
 *
 * @param CakeRequest $request Request object.
 * @return mixed Either false or an array of user information
 */
	public function getUser($request) {
		return false;
	}
}
