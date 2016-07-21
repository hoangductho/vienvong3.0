<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {
	/**
	 * Rule to valid function
	 */
	public $rules = array(
		'index' => array(
			'method' => 'GET',
			'authenticate' => true,
			'security' => true,
			'data' => array()
		),
		'update' => array(
			'method' => 'POST',
			'authenticate' => true,
			'security' => true,
			'data' => array(
				'fullname' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\\s\\w]{6,64}$/u"
					)
				),
				'birthday' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => '/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]|(?:Jan|Mar|May|Jul|Aug|Oct|Dec)))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2]|(?:Jan|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)(?:0?2|(?:Feb))\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9]|(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep))|(?:1[0-2]|(?:Oct|Nov|Dec)))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/'
					)
				),
				'address' => array(
					'allow_null' => true,
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\\s\\w]{6,128}$/u"
					)
				),
				'introduce' => array(
					'allow_null' => true,
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => "/^[\\s\\w]{6,512}$/u"
					)
				),
				'website' => array(
					'allow_null' => true,
					'filter' => FILTER_VALIDATE_URL,
				),
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
			// fields will be get
			$fields = '_id, email, fullname, nickname, birthday, avatar, avatar_thumb, interested, address, introduce, website';
			// convert string fields to array
			$fields = explode(',', $fields);
			// data response
			$data = array();
			// get data response
			foreach ($fields as $field) {
				$field = trim($field);
				$data[$field] = $this->user[$field];
			}
			// return data
			return $this->response(array('ok' => 1, 'err' => null, 'errmsg' => null, 'data' => $data));
		}else {
			return $this->response(array('ok' => 1, 'err' => null, 'errmsg' => null, 'data' => null));
		}
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
	public function update() {
		if(!empty($this->user)) {
			// data request
			$data = (array)$this->request();
			try{
				if($this->MUser->update($data, array('_id' => $this->user['_id']))) {
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
