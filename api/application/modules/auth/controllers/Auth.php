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
						'regexp' => "/^[\S]{8,64}+$/"
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
		$this->response(array('user' => $this->request()));
		// $this->response_data = array('agent' => $this->agent->browser().' '.$this->agent->version(), 'all' => $this->agent->agent_string());
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
						'subject' => 'Kích hoạt tài khoản vienvong.com',
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
					'subject' => 'Đổi mật khẩu tài khoản vienvong.com',
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
							'subject' => 'Đổi mật khẩu tài khoản vienvong.com',
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

	// public function test() {
	// 	$this->load->helper('captcha64');
	// 	$this->load->helper('url');
	// 	$values = array(
	// 		'word' => 'ABCD1234',
	// 		'img_path' => './captcha/',
	// 		'img_url' => 'http://beta.vienvong.com/api/captcha/',
	// 		'font_path' => base_url() . 'system/fonts/texb.ttf',
	// 		'font_size' => 50,
	// 		'img_width' => '150',
	// 		'img_height' => 50,
	// 		'expiration' => 14400
	// 	);
	// 	$data = create_captcha64($values);

	// 	var_dump($data);
	// 	echo '<img src="'.$data['image'].'">';
	// }
}