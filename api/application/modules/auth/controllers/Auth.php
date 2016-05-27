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
						'regexp' => "/^[\S]{64,128}+$/"
					)
				)
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
			if($user['status'] !== 1) {
				return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email chưa được xác thực'));
			}
			// Password incorrect
			if($user['password'] !== $this->request('password')) {
				return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email hoặc mật khẩu không đúng'));
			}
			
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
				'user_id' => $user['_id'],
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
		}else {
			return $this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email đã được sử dụng'));
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ======================================================
	 * Sign In By Google Plus
	 * ======================================================
	 */
	public function google() {
		
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
		  		'status' => '0',
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
					$this->response(array('ok' => 1, 'err' => null));

					$dataMail = array(
						'email' => $this->request('email'),
						'active_link' => PUBLICDOMAIN . 'auth/active/' . $signup['_id'] . '/' . $signup['active_code']['code'],
					);

					$email = array(
						'to' => $this->request('email'),
						'subject' => 'Kích hoạt tài khoản '.PUBLICDOMAIN,
						'message' => $this->load->renderSector('template/activemail', $dataMail, 'authmail', true),
					);

					$vmail = new Vmail();

					$vmail->send($email);
				}else {
					$this->response(array('ok' => 0, 'err' => $insert->err, 'errmsg' => 'Tạo tài khoản thất bại!'));
				}
			} catch (Exception $e) {
				$this->response(array('ok' => 0, 'err' => $insert->err, 'errmsg' => 'Tạo tài khoản thất bại!'));
				log_message('error', 'Create account failure! \n'.json_encode($e));
			}
			
		}else {
			$this->response(array('ok' => 0, 'err' => 11000, 'errmsg' => 'Email đã được sử dụng'));
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
		if($user = $this->MUser->exists(array('_id' => $this->request('uid')), '_id, active_code')) {
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
