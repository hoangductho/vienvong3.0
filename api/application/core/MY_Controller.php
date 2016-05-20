<?php
(defined('BASEPATH')) or exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	/**
	 * Setup request data
	 */
	public $request_data = null;
	/**
	 * Setup response data
	 */
	public $response_data = null;
	/**
	 * Setup AES keypair
	 */
	public $aes = array();
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Constructor
	 * --------------------------------------------
	 */
	public function __construct() {
		parent::__construct();
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Get Request Data
	 * --------------------------------------------
	 *
	 * @access protected
	 * @param string $name data's key
	 * @return 
	 */
	protected function request($name = null) {
		if(is_string($name)) {
			try {
				return isset($this->request_data->{$name})?$this->request_data->{$name}:null;
			} catch (Exception $e) {
				return null;
			}
		}else {
			return $this->request_data;
		}
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Response Data
	 * --------------------------------------------
	 *
	 * @access protected
	 * @param array $data data needed response
	 * @return point
	 */
	protected function response($data) {
		if(is_array($data)) {
			$this->response_data = $data;
		}

		return $this;
	}
}