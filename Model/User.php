<?php
App::uses('UsersAppModel', 'User.Model');
App::uses('Security', 'Utility');

/**
 * User Model
 *
 * @property Group $Group
 */
class User extends UsersAppModel {

/**
 * Table to use with model
 */
	public $useTable = 'users';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'username';
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'group_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select group',
				'required' => true,
				'on' => 'create'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select valid group',
				'required' => true,
				'on' => 'create'
			),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter username',
				'required' => true,
			),
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Username must only contain letters and numbers',
				'required' => true,
			),
			'between' => array(
				'rule' => array('between', 6, 20),
				'message' => 'Username length must be minimum of 6 and maximum of 20 characters',
				'required' => true,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Your username has already been used. Please enter a different one',
				'required' => true,
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter email address',
				'required' => true,
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter valid email address',
				'required' => true,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Your email has already been used. Please enter a different one',
				'required' => true,
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter password',
				'required' => true,
				'on' => 'create'
			),
			'between' => array(
				'rule' => array('between', 6, 30),
				'message' => 'Password length must be minimum of 6 and maximum of 30 characters',
				'required' => true,
				'on' => 'create',
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Group' => array(
			'className' => 'User.Group',
			'foreignKey' => 'group_id',
		)
	);

/**
 * Constructor
 *
 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		$this->_setupAdditionalValidation();
		parent::__construct($id, $table, $ds);
	}

/**
 * Setup additional validation rules for users
 */
	protected function _setupAdditionalValidation() {
		// can be overwritten in child class
	}

/**
 * Disable per user permissions
 */

/**
 * Behaviors
 * 
 * @var array 
 */
//	public $actsAs = array('Acl' => array('type' => 'requester'));
	
/**
 * Return parent Group information for User
 * 
 * @return mixed Return array with Group info, otherwise return NULL
 */
//	public function parentNode() {
//		if (!$this->id && empty($this->data)) {
//			return null;
//		}
//		if (isset($this->data[$this->alias]['group_id'])) {
//			$groupId = $this->data[$this->alias]['group_id'];
//		} else {
//			$groupId = $this->field('group_id');
//		}
//		if (!$groupId) {
//			return null;
//		} else {
//			return array('Group' => array('id' => $groupId));
//		}
//	}
	
/**
 * Simplify ACL to use per-group only permissions
 * 
 * @param User $user User object
 * @return array Bind node array
 */
	public function bindNode($user) {
		// get user data
		$key = key($user);
		if (is_array($user[$key])) {
			$user = $user[$key];
		}
		return array('model' => 'Group', 'foreign_key' => $user['group_id']);
	}

/**
 * Ensure password is saved in hashed format
 * 
 * @param array $options Options array passed to beforeSave function
 * @return boolean True
 */
	public function beforeSave($options = array()) {
		parent::beforeSave($options);
		if (isset($this->data[$this->alias]['password'])) {
			
			// if password not modified, do not update
			if (strlen(trim($this->data[$this->alias]['password'])) == 0) {
				unset($this->data[$this->alias]['password']);
				
				// no need to continue
				return true;
			}
			
			// set salt
			$salt = Security::generateAuthKey();
			$this->data[$this->alias]['salt'] = $salt;
			
			// do not use AuthComponent::password to prevent adding system salt
			$password_salted = $this->data[$this->alias]['password'] . $salt;
			$this->data[$this->alias]['password'] = Security::hash($password_salted);
		}
		return true;
	}
	
/**
 * Send forgotten password confrimation to a user
 * 
 * @param array $user User to send confrimation for
 * @return boolean Return true on successful send, otherwise return false
 */
	public function sendForgottenPassword($user) {
		App::uses('CakeEmail', 'Network/Email');
		$alias = $this->alias;
		try {
			// get encrypted url
			$key = Security::hash($user[$alias]['salt'] . $user[$alias]['id']);
			$link = Router::url(array(
				'controller' => 'users',
				'action' => 'forgotten_confirm',
				'?' => array(
					'uid' => $user[$alias]['id'],
					'key' => $key
				)
			), true);
			
			$email = new CakeEmail('default');
			$email->to($user[$alias]['email'])
				->subject(__('Forgotten Password'))
				->template('User.forgotten_password')
				->emailFormat('text')
				->viewVars(compact('user', 'alias', 'link'))
				->send();
			
			return true;
		}
		catch (SocketException $e) {
			$this->log("Unable to send forgotten password to user id ({$user[$this->alias]['id']}): {$e->getMessage()})");
		}
		return false;
	}
	
/**
 * Update user password and email them the new password
 * 
 * @param array $user User array
 * @param string $key Encrypted key to validate request
 * @return boolean Return true on success, otherwise return false
 */
	public function sendForgottenNewPassword($user, $key) {
		$alias = $this->alias;
		// verify key
		$check_key = Security::hash($user[$alias]['salt'] . $user[$alias]['id']);
		if ($key == $check_key) {
			$password = $this->_generatePassword();
			$user[$alias]['password'] = $password;
			if ($this->save($user, false, array('password', 'salt'))) {
				
				App::uses('CakeEmail', 'Network/Email');
				try {
					$email = new CakeEmail('default');
					$email->to($user[$alias]['email'])
						->subject(__('Password Updated'))
						->template('User.password_updated')
						->emailFormat('text')
						->viewVars(compact('user', 'alias', 'password'))
						->send();

					return true;
				}
				catch (SocketException $e) {
					$this->log("Unable to send updated password to user id ({$user[$this->alias]['id']}): {$e->getMessage()})");
				}
			}
		}
		
		return false;
	}
	
/**
 * Generate a random password
 * 
 * @param int $length Length of the password. Default 8
 * @return string Random password
 */
	protected function _generatePassword($length = 8) {
		
		$length = (int) $length;
		$password = '';

		// define possible characters
		$possible = '2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ';

		// check for length overflow and truncate if necessary
		$maxlength = strlen($possible);
		if ($length > $maxlength) {
			$length = $maxlength;
		}

		// add random characters to $password until $length is reached
		$i = 0;
		while ($i < $length) {

			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, $maxlength - 1), 1);

			// have we already used this character in $password?
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		
		return $password;
	}
}
