<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends MY_Controller {
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
		'edit' => array(
			'method' => 'POST',
			'authenticate' => true,
			'security' => true,
			'data' => array(
				'id' => array(
					'filter' => FILTER_VALIDATE_INT,
					'options' => array(
						'min_range'=>0,
					)
				),
				'name' => array(
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => '/^[\\s\\w]{4,64}$/u'
					)
				),
				'status' => array(
					'filter' => FILTER_VALIDATE_INT,
					'options' => array(
						'min_range'=>0, 
						'max_range'=>5
					)
				),
				'introduce' => array(
					'allow_null' => true,
					'filter' => FILTER_VALIDATE_REGEXP,
					'options' => array(
						'regexp' => '/^[\\s\\w]{6,512}$/u'
					)
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
	 * Get List Groups
	 * ======================================================
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
	 * Create Group
	 * ======================================================
	 *
	 * @method POST
	 */
	public function edit() {
		$data = (array)$this->request();

		
		// $data['ok'] = 1;
		// $this->response($data);
		try {

			if($data['id'] == 0) {
				$max_id = $this->MGroups->select_max('_id');
				$data['_id'] = count($max_id['result']) ? $max_id['result'][0]['_id_MAX'] + 1 : 1;
				unset($data['id']);

				// insert data
				$insert = $this->MGroups->insert($data);

				return $this->response($insert);
			}else {
				$id = $data['id'];
				if($this->MGroups->exists(array('_id' => $id))) {
					unset($data['id']);

					// update groups
					$update = $this->MGroups->update($data, array('_id' => $id), true);
					return $this->response($update);
				}else {
					return $this->response(array('ok' => 0, 'err' => null, 'errmsg' => 'ID is not exists'));
				}
			}
		} 
		catch (Exception $e) {
			return $this->response(array('ok' => 0, 'err' => null, 'errmsg' => 'Server Error'));
		}
		
	}
}
/* End Class*/
