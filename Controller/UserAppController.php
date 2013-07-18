<?php
App::uses('AppController', 'Controller');

/**
 * Plugin App Controller
 */
class UserAppController extends AppController {

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array(
		'Html',
		'Form',
		'Session',
		'Auth' => array('className' => 'User.UserAuth'),
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Session',
		'Paginator',
	);

/**
 * Constructor.
 *
 * @param CakeRequest $request Request object for this controller.
 * @param CakeResponse $response Response object for this controller.
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		
		// set original action if not set
		if (!isset($this->params['origAction'])) {
			
			if (isset($this->params['prefix'])) {
				// set original action name called
				// replace prefix since we only want to action
				if (strpos($this->params['action'], $this->params['prefix'] . '_') === 0) {
					$this->request->params['origAction'] = substr($this->params['action'], strlen($this->params['prefix']) + 1);
				}
			}
			else {
				$this->request->params['origAction'] = $this->request['action'];
			}
		}
		
		// should not be URL accessible since it should
		// be extended in the application
		// @todo: see if there's a better method to implement this
		if (get_class($this) == __CLASS__) {
			throw new MissingControllerException(array(
				'class' => Inflector::camelize($this->params['controller']) . 'Controller',
				'plugin' => empty($this->params['plugin']) ? null : Inflector::camelize($this->params['plugin'])
			));
		}
	}

/**
 * Instantiates the correct view class, hands it its data, and uses it to render the view output.
 * 
 * Since this parent class won't be called directly the render function is used
 * as an fallback, if a view can't be found it will fallback on the plugin view
 *
 * @param string $view View to use for rendering
 * @param string $layout Layout to use
 * @return CakeResponse A response object containing the rendered view.
 */
	public function render($view = null, $layout = null) {
		if (is_null($view)) {
			$view = $this->action;
		}
		$viewPath = substr(get_class($this), 0, strlen(get_class($this)) - 10);
		if (!file_exists(APP . 'View' . DS . $viewPath . DS . $view . '.ctp')) {
			$this->plugin = 'User';
		}
		else {
			$this->viewPath = $viewPath;
		}
		return parent::render($view, $layout);
	}
}
