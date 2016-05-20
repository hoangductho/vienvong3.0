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
			$this->controller->output->set_status_header($code);
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
				if(!isset($this->controller->request_data[$key])) {
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

					if(!$filter === false) {
						$validate->{$key} = $inputed;
					}else {
						$this->error('Rule: '. $key .' is invalid', 401);
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
}