# CakePHP User plugin

NB: This plugin is still in development!

CakePHP User plugin is meant to help create a base for your User and Group management and normal actions, it is not meant as a "plugin and play" plugin

## Requirements

* CakePHP 2.x
* PHP5
* Patience

## Installation

_[Manual]_

* Download this: [https://github.com/echcoleman/cakephp-user/develop](https://github.com/echcoleman/cakephp-user/develop)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `User`

_[GIT Submodule]_

In your app directory type:

	git submodule add -b develop git://github.com/echcoleman/cakephp-user.git Plugin/User
	git submodule init
	git submodule update

_[GIT Clone]_

In your `Plugin` directory type:

	git clone -b master git://github.com/echcoleman/cakephp-user.git User

### Enable plugin

In 2.0 you need to enable the plugin your `app/Config/bootstrap.php` file:

	CakePlugin::load('User');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

### Setup tables in database

In console run the following command:

	cake schema create --plugin User

### Create controllers and models

The plugin is meant as a base for user and group management, in order to use it you need to setup the following controllers and models:

#### User



#### UsersController

<?php
App::uses('UserUsersController', 'User.Controller');

	/**
	 * Users Controller
	 *
	 * @property UserData $User
	 */
	class UsersController extends UserUsersController {

	/**
	 * Run before any other functions in controller
	 */
		public function beforeFilter() {
			parent::beforeFilter();

			// set allowed actions
			$this->Auth->allow('register', 'login', 'logout', 'forgotten_password', 'forgotten_confirm');

			// set user model
			$this->User = ClassRegistry::init('UserData');
			$this->set('model', 'User');

			// set profile fields
			$this->_setProfileEditAllowed(array(
				'username',
				'email',
				'password',
				'country_id',
				'first_name',
				'last_name',
				'company',
				'designation',
				'business_type',
				'address',
				'mobile',
				'telephone',
				'fax',
				'newsletter_subscribe'
			));
		}
	}

### Set routes

