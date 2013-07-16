<?php
App::uses('UserAppController', 'User.Controller');

/**
 * Permissions Controller
 */
class PermissionsController extends UserAppController {
	
/**
 * Models
 * 
 * @var array 
 */
	public $uses = array();

/**
 * Run before any other functions in controller
 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		// allow following actions
		// NB: MUST ONLY BE ENABLED IN ORIGINAL SETUP!!!!!!!!!!!!!!!
		//$this->Auth->allow('admin_setupPermissions');
	}

/**
 * Setup permissions
 *
 * @return void
 */
	public function admin_setupPermissions() {
		$this->loadModel('Group');
		
		$group = $this->Group;
		
		// hardcoded permissions
		// @todo: make customizable and move to the database
		
		// allow admins to do everything
		$group->id = 1;
		$this->Acl->allow($group, 'controllers');
		
		$this->Session->setFlash(__('Permissions successfully setup'));
		$this->redirect(array('controller' => 'groups', 'action' => 'index'));
	}

}
