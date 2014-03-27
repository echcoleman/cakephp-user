<?php
App::uses('UserBaseAuthenticate', 'User.Controller/Component/Auth');

/**
 * Basic Authentication
 *
 * Provides Basic HTTP authentication support for AuthComponent.  Basic Auth will authenticate users
 * against the configured userModel and verify the username and passwords match.  Clients using Basic Authentication
 * must support cookies.  Since AuthComponent identifies users based on Session contents, clients using Basic
 * Auth must support cookies.
 * 
 * It will authenticate a user with their own salt
 *
 * In your controller's components array, add auth + the required settings.
 * {{{
 *	$this->Auth->authenticate = array(
 *		'UserBasic' => array(
 *			'scope' => array('User.active' => 1)
 *		)
 *	)
 * }}}
 */
class UserBasicAuthenticate extends UserBaseAuthenticate {

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
	public function __construct(ComponentCollection $collection, $settings) {
		
		parent::__construct($collection, $settings);
		if (empty($this->settings['realm'])) {
			$this->settings['realm'] = env('SERVER_NAME');
		}
	}

/**
 * Authenticate a user using basic HTTP auth.  Will use the configured User model and attempt a
 * login using basic HTTP auth.
 *
 * @param CakeRequest $request The request to authenticate with.
 * @param CakeResponse $response The response to add headers to.
 * @return mixed Either false on failure, or an array of user data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$result = $this->getUser($request);
		
		if (empty($result)) {
			$response->header($this->loginHeaders());
			$response->statusCode(401);
			$response->send();
			return false;
		}
		return $result;
	}

/**
 * Get a user based on information in the request.  Used by cookie-less auth for stateless clients.
 *
 * @param CakeRequest $request Request object.
 * @return mixed Either false or an array of user information
 */
	public function getUser($request) {
		
		// BUG FIX FOR HETZNER SERVERS
		// it seems the Hetzner server uses Fast-CGI to pass the auth headers,
		// so we need to catch those variables to emulate the normal auth variables
		if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && 
			preg_match('/Basic+(.*)$/i', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $matches)) {
			$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			list(
				$_SERVER['PHP_AUTH_USER'], 
				$_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
		}
		
		$username = env('PHP_AUTH_USER');
		$pass = env('PHP_AUTH_PW');

		if (empty($username) || empty($pass)) {
			return false;
		}
		return $this->_findUser($username, $pass);
	}

/**
 * Generate the login headers
 *
 * @return string Headers for logging in.
 */
	public function loginHeaders() {
		return sprintf('WWW-Authenticate: Basic realm="%s"', $this->settings['realm']);
	}

/**
 * Handles an unauthenticated access attempt by sending appropriate login headers
 *
 * @param CakeRequest $request A request object.
 * @param CakeResponse $response A response object.
 * @return void
 * @throws UnauthorizedException
 */
	public function unauthenticated(CakeRequest $request, CakeResponse $response) {
		$Exception = new UnauthorizedException();
		$Exception->responseHeader(array($this->loginHeaders()));
		throw $Exception;
	}

}
