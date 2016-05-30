<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {
	/**
	 * Rule to valid function
	 */
	public $rules = array(
		'signin' => array(
			'method' => 'POST',
			'authenticate' => false,
			'security' => true,
			'data' => array(
				'email' => array(
					'filter' => FILTER_VALIDATE_EMAIL
				),
				'password' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[a-f0-9]{64}+$/"
					)
				)
			)
		),
		'google' => array(
			'method' => 'POST',
			'authenticate' => false,
			'security' => true,
			'data' => array(
				'access_token' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\S]{64,256}+$/"
					)
				),
				'expires_at' => array(
					'filter' => FILTER_VALIDATE_INT,
				),
				'expires_in' => array(
					'filter' => FILTER_VALIDATE_INT,
				),
			)
		),
		'facebook' => array(
			'method' => 'POST',
			'authenticate' => false,
			'security' => true,
			'data' => array(
				'access_token' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\S]{64,256}+$/"
					)
				),
			)
		),
		'active' => array(
			'method' => 'POST',
			'authenticate' => false,
			'security' => true,
			'data' => array(
				'uid' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[0-9a-f]{32}+$/"
					)
				),
				'code' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[0-9a-zA-Z]{32}+$/"
					)
				)
			)
		),
		'signup' => array(
			'method' => 'POST',
			'authenticate' => false,
			'security' => true,
			'data' => array(
				'email' => array(
					'filter' => FILTER_VALIDATE_EMAIL
				),
				'password' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\S]{8,64}+$/"
					)
				)
			)
		),
		'resend' => array(
			'method' => 'POST',
			'authenticate' => false,
			'security' => true,
			'data' => array(
				'email' => array(
					'filter' => FILTER_VALIDATE_EMAIL
				)
			)
		),
		'forgot' => array(
			'method' => 'POST',
			'authenticate' => false,
			'security' => true,
			'data' => array(
				'email' => array(
					'filter' => FILTER_VALIDATE_EMAIL
				)
			)
		),
		// 'test' => array(
		// 	'method' => 'GET',
		// 	'authenticate' => false,
		// 	'security' => false
 	// 	)
	);
	// ----------------------------------------------------------------
	/**
	 * ============================================
	 * Constructor
	 * ============================================
	 * 
	 * @todo construct all initilization value
	 */
	public function __construct() {
		parent::__construct();
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Create Access Session
	 * ======================================================
	 * 
	 * @param string $uid User's ID 
	 *
	 * @return array $token access token
	 */
	private function _access_token($uid) {
		/*
		 * Create Access token for user
		 * 
		 * Using Agent, IP, Randomstring, UserID
		 */
		// create datetime object
		$date = new DateTime(date('Y/m/d'));
		// create extend's functions object
		$extend = new MyExtends();
		// create token data
		$token = [
			'ip' => $this->input->ip_address(),
			'user_id' => $uid,
			'random_string' => $extend->RandomString(32),
			'browser' => $this->agent->browser(),
			'browser_version' => $this->agent->version(),
			'mobile' => $this->agent->mobile(),
			'platform' => $this->agent->platform(),
			'referrer' => $this->agent->referrer(),
			'agent_string' => $this->agent->agent_string(),
			'languages' => $this->agent->languages(),
			'created_at' => date('Y/m/d h:i:s'),
			'live_time' => $date->modify('+7 days')->format('Y/m/d H:i:s'),
		];
		// create access token
		$token['_id'] = hash('sha256', $token['ip'] . $token['random_string'] . $token['user_id']);
		// save access info into database
		try{
			$insert = $this->MAccess_token->insert($token);

			if($insert['ok']) {
				// response data
				$response = [
					'access_token' => $token['_id'],
					'created_at' => $token['created_at'],
					'live_time' => $token['live_time']
				];
				return $this->response(array('ok' => 1, 'err' => null, 'result' => $response));	
			}else {
				return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Đăng nhập xảy ra sự cố, mong bạn vui lòng thử lại sau'));	
			}
		} catch(Exception $e) {
			return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Đăng nhập xảy ra sự cố, mong bạn vui lòng thử lại sau'));	
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Sign in Action
	 * ======================================================
	 *
	 * @todo Process request Sign in of user
	 *
	 * @method Post
	 */
	public function signin() {
		if($user = $this->MUser->exists(array('_id' => md5($this->request('email'))), '_id, email, password, status')) {
			$user = $user[0];
			// filter email didn't actived
			if((int) $user['status'] !== 1) {
				return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email chưa được xác thực'));
			}
			// Password incorrect
			if($user['password'] !== $this->request('password')) {
				return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email hoặc mật khẩu không đúng'));
			}
			
			return $this->_access_token($user['_id']);
		}else {
			return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email chưa được đăng kí'));
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Quick Sign Up By Social
	 * ======================================================
	 */
	private function _quick_sign_up($email) {
		// create user proccess
		if(!$this->MUser->exists(array('_id' => md5($email)))) {
			// current date
			$date = date('Y/m/d H:i:s');
			// date modify
			$modify = new DateTime($date);
			// create extend functions
			$ext = new MyExtends();

			// create sing up data
			$signup = array(
				'_id' => md5($email),
				'email' => $email,
		  		'password' => null,
		  		'status' => 1,
 		  		'status_alias' => 'actived',
				'active_code' => null,
		  		'reset_code' => null,
		  		'fullname' => null,
		  		'nickname' => null,
		  		'birthday' => null,
		  		'avatar' => null,
		  		'avatar_thumb' => null,
		  		'interested' => null,
		  		'created_at' => $date,
			);

			try {
				// create new user
				$insert = (object) $this->MUser->insert($signup);

				return $insert->ok;
			} catch (Exception $e) {
				log_message('error', 'Create account failure! \n'.json_encode($e));
				return false;
			}
			
		}else {
			return true;
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Sign In By Google Plus
	 * ======================================================
	 */
	public function google() {
		/************************************************
		  Make an API request on behalf of a user. In
		  this case we need to have a valid OAuth 2.0
		  token for the user, so we need to send them
		  through a login flow. To do this we need some
		  information from our API console project.
		 ************************************************/
		$client_id = '1082471688155-kao66flq0pn2f1ceo006q1prsrrp2r4r.apps.googleusercontent.com';
		$client_secret = 'b7aGOFUk-5xXBkUE4a6t-RU0';
		$access_token = json_encode($this->request());

		/**
		 * Connect to Google Plus 
		 */
		if ($access_token) {
			// Include Google Plus API
			set_include_path(APPPATH . "libraries/google-api/src/" . PATH_SEPARATOR . get_include_path());
			require_once 'Google/Config.php';
			require_once 'Google/Client.php';
			require_once 'Google/Service.php';
			require_once 'Google/Collection.php';
			require_once 'Google/Model.php';
			require_once 'Google/Service/Resource.php';
			require_once 'Google/Service/Plus.php';
			// Connect Google API
			$client = new Google_Client();
			$client->setClientId($client_id);
			$client->setClientSecret($client_secret);
			$client->addScope("https://www.googleapis.com/auth/plus.login");
			$client->addScope("email");

			$client->setAccessToken($access_token);
		  	
		  	$PlusService = new Google_Service_Plus($client);
		  	$me = new Google_Service_Plus_Person();
		  	$me = $PlusService->people->get('me');
		 	$PlusPersonEMails = new Google_Service_Plus_PersonEmails();
		 	$PlusPersonEMails = $me->getEmails();
		 	foreach($PlusPersonEMails as $em) {
		 		if($em->type == "account") {
		 			$user_email = $em->value;
		 		}
		 	}
		 	if(!empty($user_email)) {
		 		if($this->_quick_sign_up($user_email)) {
		 			$this->_access_token(md5($user_email));	
		 		}else {
		 			$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Quá trình xử lý xảy ra sự cố. Mong bạn vui lòng thử lại sau.'));
		 		}
		 		
		 		return;
		 	}else {
		 		return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Không thể kết nối với Google+'));
		 	}
		} else {
			header("HTTP/1.1 401 Bad token");
		  	exit;
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Sign In By Facebook
	 * ======================================================
	 */
	public function facebook() {
		/************************************************
		  Make an API request on behalf of a user. In
		  this case we need to have a valid OAuth 2.0
		  token for the user, so we need to send them
		  through a login flow. To do this we need some
		  information from our API console project.
		 ************************************************/
		$client_id = '550251971759267';
		$client_secret = 'a38bfb60e1649061029d529915e33c07';
		$access_token = $this->request('access_token');

		if ($access_token) {
			// Include Facebook API
			set_include_path(APPPATH . "libraries/facebook-api/src/" . PATH_SEPARATOR . get_include_path());
			require_once 'Facebook/autoload.php';
			spl_autoload_register();

			// Init Facebook Client
			$fb = new Facebook\Facebook([
			  'app_id' => $client_id,
			  'app_secret' => $client_secret,
			  'default_graph_version' => 'v2.2',
			  ]);
			// Connect Facebook API
			try {
			  // Returns a `Facebook\FacebookResponse` object
			  $response = $fb->get('/me?fields=email', $access_token);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				echo 'Graph returned an error: ' . $e->getMessage();
				exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			  	echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  	exit;
			}
			// get user info
			$user = $response->getGraphUser();
			// create access session token
		 	if(!empty($user['email'])) {
		 		if($this->_quick_sign_up($user['email'])) {
		 			$this->_access_token(md5($user['email']));
		 		}else {
		 			$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Quá trình xử lý xảy ra sự cố. Mong bạn vui lòng thử lại sau.'));
		 		}
		 		return;
		 	}else {
		 		return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Không thể kết nối với Facebook'));
		 	}
		} else {
			header("HTTP/1.1 401 Bad token");
		  	exit;
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Send Active Mail
	 * ======================================================
	 *
	 * @param email $email email registed
	 * @param string $code active code
	 *
	 * @return sent mail result
	 */
	private function _active_mail($email, $code) {
		try {
			$dataMail = array(
				'email' => $this->request('email'),
				'active_link' => PUBLICDOMAIN . 'auth/active/' . md5($email) . '/' . $code,
			);

			$email = array(
				'to' => $this->request('email'),
				'subject' => 'Kích hoạt tài khoản '.PUBLICDOMAIN,
				'message' => $this->load->renderSector('template/activemail', $dataMail, 'authmail', true),
			);

			$vmail = new Vmail();

			$vmail->send($email);
			return $this->response(array('ok' => 1, 'err' => null));
		}catch(Exception $e){
			return $this->response(array('ok' => 0, 'err' => 101, 'errmsg' => 'Do sự cố nên hệ thống chưa thể gửi e-mail cho bạn. Mong bạn vui lòng thử lại sau.'));
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Sign up Action
	 * ======================================================
	 *
	 * @todo Process request sign up of user
	 *
	 * @method Post
	 */
	public function signup() {
		// create user proccess
		if(!$this->MUser->exists(array('_id' => md5($this->request('email'))))) {
			// current date
			$date = date('Y/m/d H:i:s');
			// date modify
			$modify = new DateTime($date);
			// create extend functions
			$ext = new MyExtends();

			// create sing up data
			$signup = array(
				'_id' => md5($this->request('email')),
				'email' => $this->request('email'),
		  		'password' => hash('sha256', $this->request('password')),
		  		'status' => 0,
 		  		'status_alias' => 'pendding',
				'active_code' => array(
					'code' => $ext->RandomString(32),
					'live_time' => $modify->modify('+7 days')->format('Y/m/d H:i:s'),
					'created_at' => date('Y/m/d H'),
				),
		  		'reset_code' => null,
		  		'fullname' => null,
		  		'nickname' => null,
		  		'birthday' => null,
		  		'avatar' => null,
		  		'avatar_thumb' => null,
		  		'interested' => null,
		  		'created_at' => $date,
			);

			try {
				// create new user
				$insert = (object) $this->MUser->insert($signup);

				if($insert->ok) {
					return $this->_active_mail($this->request('email'), $signup['active_code']['code']);
				}else {
					return $this->response(array('ok' => 0, 'err' => $insert->err, 'errmsg' => 'Tạo tài khoản thất bại!'));
				}
			} catch (Exception $e) {
				$this->response(array('ok' => 0, 'err' => $insert->err, 'errmsg' => 'Tạo tài khoản thất bại!'));
				log_message('error', 'Create account failure! \n'.json_encode($e));
				return;
			}
			
		}else {
			return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email đã được sử dụng'));
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Active Account
	 * ======================================================
	 *
	 * @todo Process request active of user
	 *
	 * @method Post
	 */
	public function active() {
		if($user = $this->MUser->exists(array('_id' => md5($this->request('email'))), '_id, active_code')) {
			$user = $user[0];
			// check email actived
			if(!empty($user['status']) && $user['status'] === 1) {
				$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email đã được xác thực'));
				return;
			}
			// check active code exists
			if(empty($user['active_code']) || $user['active_code']['code'] != $this->request('code')) {
				$this->response(array('ok' => 0, 'err' => 11001, 'errmsg' => 'Mã kích hoạt không tồn tại'));
				return;
			}
			// check active code expired
			$time = date('Y/m/d H:i:s');
			if($time > $user['active_code']['live_time']) {
				$this->response(array('ok' => 0, 'err' => 11001, 'errmsg' => 'Mã kích hoạt đã hết hạn'));
				return;
			}
			// active email
			$update = array(
				'status' => 1,
				'status_alias' => 'actived',
				'active_code' => null
			);
			try {
				if($this->MUser->update($update, array('_id' => $this->request('uid')))) {
					$this->response(array('ok' => 1, 'err' => null));
				}else {
					$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Kích hoạt tài khoản xảy ra sự cố. Mong bạn vui lòng thử lại sau'));
				}
			} catch(Exception $e) {
				$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Kích hoạt tài khoản xảy ra sự cố. Mong bạn vui lòng thử lại sau'));
			}
		}else {
			$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email không tồn tại'));
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Resend Active Code
	 * ======================================================
	 *
	 * @method Post
	 */
	public function resend() {
		if($user = $this->MUser->exists(array('_id' => md5($this->request('email'))), '_id, active_code')) {
			$user = $user[0];
			// check email actived
			if(!empty($user['status']) && $user['status'] === 1) {
				return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email đã được xác thực'));
			}
			// check active code expired
			$time = date('Y/m/d H:i:s');
			if(empty($user['active_code']) || $time > $user['active_code']['live_time']) {
				// date modify
				$modify = new DateTime(date('Y/m/d H:i:s'));
				// create extend functions
				$ext = new MyExtends();
				// active email
				$update = array(
					'active_code' => array(
						'code' => $ext->RandomString(32),
						'live_time' => $modify->modify('+7 days')->format('Y/m/d H:i:s'),
						'created_at' => date('Y/m/d H'),
					),
				);
				try {
					if($this->MUser->update($update, array('_id' => $user['_id']))) {
						return $this->_active_mail($this->request('email'), $update['active_code']['code']);
					}else {
						return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Quá trình cập nhật xảy ra sự cố. Mong bạn vui lòng thử lại sau'));
					}
				} catch(Exception $e) {
					return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Quá trình xử lý xảy ra sự cố. Mong bạn vui lòng thử lại sau'));
				}
			}else {
				return $this->_active_mail($this->request('email'), $user['active_code']['code']);
			}
		}else {
			return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email không tồn tại'));
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Forgot Password Action
	 * ======================================================
	 *
	 * @todo Process request forgot password of user
	 *
	 * @method Post
	 */
	public function forgot() {
		// check user existed
		if($user = $this->MUser->exists(array('_id' => md5($this->request('email'))), '_id, reset_code')) {
			$user = $user[0];
			// reset code not existed
			$time = date('Y/m/d H');
			if(!empty($user['reset_code']) && $time === $user['reset_code']['created_at']) {
				// response data
				$this->response(array('ok' => 1, 'err' => null));
				// send reset code to user's email
				$dataMail = array(
					'email' => $this->request('email'),
					'active_link' => PUBLICDOMAIN . 'auth/reset/' . $user['_id'] . '/' . $user['reset_code']['code'],
				);

				$email = array(
					'to' => $this->request('email'),
					'subject' => 'Đổi mật khẩu tài khoản '.PUBLICDOMAIN,
					'message' => $this->load->renderSector('template/activemail', $dataMail, 'authmail', true),
				);

				$vmail = new Vmail();

				$vmail->send($email);
				return ;
			} else {
				// current date
				$date = date('Y/m/d H:i:s');
				// date modify
				$modify = new DateTime($date);
				// create extend functions
				$ext = new MyExtends();
				// reset code
				$update = array(
					'reset_code' => array(
						'code' => $ext->RandomString(32),
						'live_time' => $modify->modify('+7 days')->format('Y/m/d H:i:s'),
						'created_at' => $time,
					),
				);
				// push reset code to database
				try {
					if($this->MUser->update($update, array('_id' => md5($this->request('email'))))) {
						$this->response(array('ok' => 1, 'err' => null));
						// send reset code to user's email
						$dataMail = array(
							'email' => $this->request('email'),
							'active_link' => PUBLICDOMAIN . 'auth/reset/' . md5($this->request('email')) . '/' . $update['reset_code']['code'],
						);

						$email = array(
							'to' => $this->request('email'),
							'subject' => 'Đổi mật khẩu tài khoản '.PUBLICDOMAIN,
							'message' => $this->load->renderSector('template/activemail', $dataMail, 'authmail', true),
						);

						$vmail = new Vmail();

						$vmail->send($email);
						return ;
					}else {
						$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Tạo mã không thành công. Mong bạn vui lòng thử lại sau'));
						return;
					}
				} catch(Exception $e) {
					$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Tạo mã không thành công. Mong bạn vui lòng thử lại sau'));
					return ;
				}
			}
			// reset code expired
			$time = date('Y/m/d H');
			if($time > $user['reset_code']['live_time']) {
				$this->response(array('ok' => 0, 'err' => 11001, 'errmsg' => 'Mã đổi mật khẩu đã hết hạn'));
				return;
			}
			// 
		}else {
			$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email không tồn tại'));
			return;
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Reset Account
	 * ======================================================
	 *
	 * @todo Process request reset of user
	 *
	 * @method Post
	 */
	public function reset() {
		if($user = $this->MUser->exists(array('_id' => $this->request('uid')), '_id, reset_code')) {
			$user = $user[0];
			// check reset code exists
			if(empty($user['reset_code']) || $user['reset_code']['code'] != $this->request('code')) {
				$this->response(array('ok' => 0, 'err' => 11001, 'errmsg' => 'Mã kích hoạt không tồn tại'));
				return;
			}
			// check active code expired
			$time = date('Y/m/d H:i:s');
			if($time > $user['reset_code']['live_time']) {
				$this->response(array('ok' => 0, 'err' => 11001, 'errmsg' => 'Mã kích hoạt đã hết hạn'));
				return;
			}
			// active email
			$update = array(
				'reset_code' => null
			);
			try {
				if($this->MUser->update($update, array('_id' => $this->request('uid')))) {
					$this->response(array('ok' => 1, 'err' => null));
				}else {
					$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Kích hoạt tài khoản xảy ra sự cố. Mong bạn vui lòng thử lại sau'));
				}
			} catch(Exception $e) {
				$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Kích hoạt tài khoản xảy ra sự cố. Mong bạn vui lòng thử lại sau'));
			}
		}else {
			$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email không tồn tại'));
		}
	}
	
}
/* End Class*/
