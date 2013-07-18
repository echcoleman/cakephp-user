<?php
App::uses('UserAppController', 'User.Controller');

/**
 * Groups Controller
 *
 * @package User
 * @subpackage User.UserGroupsController
 * 
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 * @property PaginatorComponent $Paginator
 * @property Group $Group
 */
class UserGroupsController extends UserAppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'UserGroups';

/**
 * Model controller uses
 * 
 * @var array
 */
	public $uses = array('User.Group');

/**
 * Called before the controller action
 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		// set controller default title
		$this->set('title_for_layout', __('Groups'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Group->recursive = 0;
		$this->set('groups', $this->paginate());
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->Group->id = $id;
		if (!$this->Group->exists()) {
			throw new NotFoundException(__('Invalid group'));
		}
		$this->set('group', $this->Group->read(null, $id));
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
			$this->Group->id = $id;
			if (!$this->Group->exists()) {
				throw new NotFoundException(__('Invalid group'));
			}
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Group->save($this->request->data)) {
				$this->Session->setFlash(__('The group has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The group could not be saved. Please, try again.'), null, null, 'error');
			}
		}
		if ($id && empty($this->data)) {
			$this->request->data = $this->Group->read(null, $id);
		}
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
		$this->Group->id = $id;
		if (!$this->Group->exists()) {
			throw new NotFoundException(__('Invalid group'));
		}
		if ($this->Group->delete()) {
			$this->Session->setFlash(__('Group deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Group was not deleted'), null, null, 'error');
		$this->redirect(array('action' => 'index'));
	}
}
