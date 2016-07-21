<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ================================================
 * Check Rule Of Function
 * ================================================
 * 
 * @description 
 *		- check rule of function to valid inputes.
 *		- if function not define in rule, it can't accessed
 *		- this class execute after controller constructed
 */

class Rules {
	/**
	 * Current controller pointer
	 */
	private $controller;
	/**
	 * Class name of controller
	 */ 
	private $class;
	/**
	 * Controller's function requested
	 */
	private $function;
	/**
	 * Extend funtion
	 */
	private $Extend;
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Constructor
	 * --------------------------------------------
	 */
	function __construct() {
		$this->controller = &get_instance();
		
		$router = $this->controller->router;

		$this->function = $router->method;

		$this->class = $router->class;

		$this->Extend = new MyExtends();
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Error Show
	 * --------------------------------------------
	 *
	 * @todo Response error state of request for client
	 *
	 * @param string $message error message need set
	 * @param int $code error code need set
	 */
	private function error($message, $code = false) {
		// default error code
		$code = $code?$code:500;
		// response data
		$error = array(
			'ok' => 0,
			'err' => $code,
			'errmsg' => $message
		);
		// response error message
		echo json_encode($error);
		// set error code for request
		if($code) {
			$this->controller->output->set_status_header(500);
		}
		// kill proccess
		die();
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Get Rules
	 * --------------------------------------------
	 * 
	 * Get $rules variable of controller class.
	 *
	 * $rules using to define action in controller
	 */
	private function _getrule() {
		if(empty($this->controller->rules)){
			$this->error('Rule: Controller '. $this->class .' did not set rules', 401);
		}

		if(empty($this->controller->rules[$this->function])) {
			$this->error('Rule: Function '. $this->function .' did not define in rules', 401);
		}

		return (object) $this->controller->rules[$this->function];
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Check Function Defined
	 * --------------------------------------------
	 *
	 * Check controller action as defined befor processing started
	 *
	 * Using $rule of controller class to define action
	 */
	public function defined() {
		$rules = $this->_getrule();

		// check agent device
		if(!$this->controller->agent->is_browser() && !$this->controller->agent->is_mobile()) {
			die();
		}

		// deteced mothod checking
		if($this->controller->input->method(true) === 'OPTIONS'){
			die();
		}elseif(strtoupper($rules->method) != $this->controller->input->method(true)) {
			$this->error('Rule: Function '. $this->function .' required other method', 401);
		}

		// check encrypt data required
		if($rules->security && empty($this->controller->input->get_request_header('Content-Header'))) {
			$this->error('Rule: Function '. $this->function .' required security mode', 401);
		}

		// check authenticate required
		if($rules->authenticate && empty($this->controller->input->get_request_header('Authenticate'))) {
			$this->error('Rule: Function '. $this->function .' required authenticate mode', 401);
		}
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Validate Posted Data
	 * --------------------------------------------
	 *
	 * Validate posted data using standard defined by rules 
	 *
	 * If data don't defined in rules will be unload in controller
	 */
	public function validate() {
		// get rules of function
		$rules = $this->_getrule();

		// if data inputed
		if(!empty($this->controller->request_data)) {
			// if function don't allow input data
			if(empty($rules->data)) {
				$this->error('Rule: Function '. $this->function .' not allowed input data', 401);
			}

			// data return
			$validate = (object) array();

			foreach ($rules->data as $key => $value) {
				if(!isset($this->controller->request_data[$key]) && (empty($value['allow_null']) || !$value['allow_null'])) {
					$this->error('Rule: '. $key .' is required', 401);
				}

				// data need check
				$inputed = $this->controller->request_data[$key];

				// if data filter type defined
				if(!empty($value['filter'])) {
					// options filter
					$options = (!empty($value['options']))?$value['options']:array();

					// filter data 
					$filter = filter_var($inputed, $value['filter'], array('options' => $options));

					if(!$filter === false || ((!empty($value['allow_null']) && $value['allow_null']) && empty($inputed))) {
						$validate->{$key} = $inputed;
					}else {
						$this->error('Rule: '. $key . ' = ' . $inputed .' is invalid', 401);
					}
				}else {
					$validate->{$key} = $inputed;
				}
			}

			$this->controller->request_data = $validate;
		}elseif(!empty($rules->data)) {
			$this->error('Rule: Function '. $this->function .' required data input', 401);
		}
	}
	// ----------------------------------------------------------------
	/**
	 * ============================================
	 * Check Authenticate
	 * ============================================
	 *
	 * Check authenticate info on header of client request
	 * 
	 */
	public function authenticate() {
		// get rules of function
		$rules = $this->_getrule();
		// init phpseclib
		$phpseclib = new Phpseclib();

		// check authenticate required
		if($rules->authenticate && !empty($authEncrypt = $this->controller->input->get_request_header('Authenticate'))) {
			// decrypt authenticate data
			$authInfo = json_decode($phpseclib->aesDecryptCryptoJS($authEncrypt, $this->controller->aes['key'], $this->controller->aes['iv']), true);
			// get current time
			$datetime = date('yyyy/mm/dd H:i:s');
			// check expired 
			if($authInfo['live_time'] < $datetime) {
				$this->error('Authenticate: token is expired', 498);
			}
			// create check conditions
			$where = array(
				'_id' => (!empty($authInfo['access_token'])) ? $authInfo['access_token'] : 0,
				'user_id' => (!empty($authInfo['email'])) ? $this->Extend->CreateID($authInfo['email']) : 0,
				'created_at' => $authInfo['created_at'],
				'live_time' => $authInfo['live_time'],
				'browser' => $this->controller->agent->browser(),
				'mobile' => $this->controller->agent->mobile(),
				'platform' => $this->controller->agent->platform(),
			);
			// get access_token
			$access_token = $this->controller->MAccess_token->exists($where, '_id, ip, password');
			// check authenticate
			if(!$access_token) {
				$this->error('Authenticate: token is invalid', 498);
			}
			// get user info
			if($user = $this->controller->MUser->exists(array('_id' => $this->Extend->CreateID($authInfo['email'])), '*')) {
				$user = $user[0];
				// check password changed
				if(!empty($access_token[0]['password']) && $access_token[0]['password'] != $user['password']) {
					$this->error('Authenticate: token is invalid', 498);
				}
				// get status code
				$actived_status = $this->controller->config->item('user_status')->actived;
				// continue proccessing
				if(!empty($user['status']) && $user['status'] === $actived_status)
					$this->controller->user = $user;
				else {
					$this->error('Authenticate: user is not actived', 401);	
				}
			}else {
				$this->error('Authenticate: user is not existed', 498);
			}
		}
	}
}