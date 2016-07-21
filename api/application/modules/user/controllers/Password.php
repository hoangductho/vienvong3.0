<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password extends MY_Controller {
	/**
	 * Rule to valid function
	 */
	public $rules = array(
		'index' => array(
			'method' => 'POST',
			'authenticate' => true,
			'security' => true,
			'data' => array(
				'current' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\S]{8,64}+$/"
					)
				),
				'change' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\S]{8,64}+$/"
					)
				)
			)
		),
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
	 * Get User's Profile
	 * ======================================================
	 *
	 * @todo Get Profile of current user
	 *
	 * @method GET
	 */
	public function index() {
		if(!empty($this->user)) {
			// data request
			$data = (array)$this->request();

			if(hash('sha256', $data['current']) != $this->user['password']) {
				return $this->response(array('ok' => 0, 'err' => 304, 'errmsg' => 'Mật khẩu hiện tại không đúng!'));
			}

			try{
				if($this->MUser->update(array('password' => hash('sha256', $data['change'])), array('_id' => $this->user['_id']))) {
					$this->response(array('ok' => 1, 'err' => null));
				}else {
					$this->response(array('ok' => 0, 'err' => 304, 'errmsg' => 'Cập nhật thất bại!'));
				}
			}catch(Exception $e) {
				$this->response(array('ok' => 0, 'err' => 304, 'errmsg' => 'Cập nhật thất bại!'));
			}
		}else {
			return $this->response(array('ok' => 0, 'err' => 511, 'errmsg' => 'Phiên làm việc hết hạn'));
		}
	}
}
/* End Class*/
