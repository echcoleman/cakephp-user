<?php
App::uses('UserAppController', 'User.Controller');

/**
 * UserUsers Controller
 * 
 * Cannot be called directly, URL accessible controller should extend it
 *
 * @package User
 * @subpackage User.UserUsersController
 * 
 * @property AuthComponent $Auth
 * @property PaginatorComponent $Paginator
 * @property SecurityComponent $Security
 * @property SessionComponent $Session
 * @property RememberMeComponent $RememberMe
 * @property User $User
 */
class UserUsersController extends UserAppController {

/**
 * Controller name
 * 
 * Has been set for rendering purposes
 *
 * @var string
 */
	public $name = 'UserUsers';

/**
 * Model controller uses
 * 
 * @var array
 */
	public $uses = array('User.User');

/**
 * Constructor.
 *
 * @param CakeRequest $request Request object for this controller.
 * @param CakeResponse $response Response object for this controller.
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		
		// should not be URL accessible since it should
		// be extended in the application
		// @todo: see if there's a better method to implement this
		if (get_class($this) == __CLASS__) {
			throw new MissingControllerException(array(
				'class' => Inflector::camelize($this->params['controller']) . 'Controller',
				'plugin' => empty($this->params['plugin']) ? null : Inflector::camelize($this->params['plugin'])
			));
		}
		
		$this->_setupHelpers();
		$this->_setupComponents();
	}

/**
 * Setup additional helpers
 */
	protected function _setupHelpers() {
		$helpers = array(
			'Time',
			'Text',
		);
		$this->helpers = array_merge($this->helpers, $helpers);
	}

/**
 * Setup additional components
 */
	protected function _setupComponents() {
		// does nothing for now
	}

/**
 * Run before any other functions in controller
 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		// set model to use in views
		$this->set('model', $this->modelClass);
		
		// always allow following actions
		$this->Auth->allow('login', 'logout', 'register', 'forgotten_password', 'forgotten_confirm');
	}

/**
 * User login
 * 
 * @return void
 */
	public function login() {
		$this->set('title_for_layout', __('Login'));
		
		// if user already logged in, redirect
		if ($this->Auth->loggedIn()) {
			$this->redirect($this->Auth->loginRedirect);
		}
		
		// process login request
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect($this->Auth->redirect());
			} else {
				// check if user could not login because they aren't active
				$this->Auth->authenticate['UserForm']['scope'] = false;
				if ($this->Auth->identify($this->request, $this->response)) {
					$this->Session->setFlash('Your account is not active. Please confirm your account via email or contact our administrators.', null, null, 'error');
				}
				else {
					$this->Session->setFlash('Your username or password was incorrect. Please try again.', null, null, 'error');
				}
			}
		}
	}

/**
 * User logout
 * 
 * @return void
 */
	public function logout() {
		$this->Session->setFlash('Logged out');
		$this->redirect($this->Auth->logout());
	}
	
/**
 * Forgotten password
 * 
 * @return void
 */
	public function forgotten_password() {
		$this->set('title_for_layout', __('Forgotten Password'));
		
		// process forgotten password
		if ($this->request->is('post')) {
			if (isset($this->request->data['User']['username'])) {
				
				// @todo: Found bug in CakePHP when try to use below statement,
				// had to supply username twice (for username and email values check)
				$username = $this->request->data['User']['username'];
				$user = $this->{$this->modelClass}->findByUsernameOrEmail($username, $username);
				
				// ensure user is valid and active
				if (!empty($user)) {
					if ($user['User']['active'] == 1) {
						if ($this->{$this->modelClass}->sendForgottenPassword($user)) {
							$this->Session->setFlash(__('Please check your mail to reset your password'), null, null, 'error');
							unset($this->request->data['User']);
						}
						else {
							$this->Session->setFlash(__('Unable process your request. Please try again.'), null, null, 'error');
						}
					}
					else {
						$this->Session->setFlash('Your account is not active. Please confirm your account via email or contact our administrators.', null, null, 'error');
					}
				}
				else {
					$this->Session->setFlash(__('Unable to find user with submitted username or email address. Please try again.'), null, null, 'error');
				}
			}
		}
	}
	
/**
 * Confirm forgotten password
 * 
 * @return void
 */
	public function forgotten_confirm() {
		$this->set('title_for_layout', __('Forgotten Password Confrimation'));
		
		// ensure user id and key is available
		$invalidConfirm = true;
		if (isset($this->request->query['uid']) && isset($this->request->query['key'])) {
			$user = $this->{$this->modelClass}->findById($this->request->query['uid']);
			if (!empty($user)) {
				if ($this->{$this->modelClass}->sendForgottenNewPassword($user, $this->request->query['key'])) {
					$invalidConfirm = false;
					$this->Session->setFlash(__('Your password has been updated and emailed to you'));
				}
				else {
					$this->Session->setFlash(__('Unable to update your password. Please try again.'), null, null, 'error');
				}
			}
		}
		if ($invalidConfirm) {
			$this->Session->setFlash(__('Invalid forgotten password confrimation received. Please try again.'), null, null, 'error');
		}
		$this->redirect(array('action' => 'login'));
	}

/**
 * view method
 *
 * @return void
 */
	public function view_profile() {
		$this->set('title_for_layout', __('Profile'));
		
		$id = $this->Auth->user('id');
		$this->{$this->modelClass}->id = $id;
		if (!$this->{$this->modelClass}->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->{$this->modelClass}->read(null, $id));
	}

/**
 * add/edit method
 *
 * @return void
 */
	public function edit_profile() {
		$this->set('title_for_layout', __('Update Profile'));
		
		$id = $this->Auth->user('id');
		$this->{$this->modelClass}->id = $id;
		if (!$this->{$this->modelClass}->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			// remove/set any data users are not allowed to edit
			$this->request->data['id'] = $id;
			unset($this->request->data['group_id']);
			unset($this->request->data['active']);
			if ($this->{$this->modelClass}->save($this->request->data)) {
				$this->Session->setFlash(__('Your profile has been updated'));
				$this->redirect(array('action' => 'view_profile'));
			} else {
				$this->Session->setFlash(__('Unable to update your profile. Please, try again.'), null, null, 'error');
			}
		}
		if (empty($this->data)) {
			$this->request->data = $this->{$this->modelClass}->read(null, $id);
		}
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->{$this->modelClass}->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->{$this->modelClass}->id = $id;
		if (!$this->{$this->modelClass}->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->{$this->modelClass}->read(null, $id));
	}

/**
 * admin_add method (uses admin_edit method)
 * @return void
 */
	public function admin_add() {
		$this->setAction('admin_edit');
	}

/**
 * admin_add/edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if ($id) {
			$this->{$this->modelClass}->id = $id;
			if (!$this->{$this->modelClass}->exists()) {
				throw new NotFoundException(__('Invalid user'));
			}
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->{$this->modelClass}->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), null, null, 'error');
			}
		}
		if ($id && empty($this->data)) {
			$this->request->data = $this->{$this->modelClass}->read(null, $id);
		}
		$groups = $this->{$this->modelClass}->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * admin_delete method
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->{$this->modelClass}->id = $id;
		if (!$this->{$this->modelClass}->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->{$this->modelClass}->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'), null, null, 'error');
		$this->redirect(array('action' => 'index'));
	}
}
