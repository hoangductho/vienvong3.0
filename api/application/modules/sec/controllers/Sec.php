<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sec extends MY_Controller {
	/**
	 * Rule to valid function
	 */
	public $rules = array(
		'rsakey' => array(
			'method' => 'Get',
			'authenticate' => false,
			'security' => false
		)
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
	 * Get RSA Public Key
	 * ======================================================
	 *
	 * @todo response rsa public key and hexa key for client 
	 */
	public function rsakey($clientRandom) {
		// create id for key
		$key['_id'] = date('Ymd');
		// fields needed get
		$select = ['_id', 'publicHex', 'public'];
		// get key from database
		$existed = $this->MRsakey->get($key, $select);

		if($existed['ok'] && count($existed['result'])) {
			$result = array('ok' => 1, 'result' => $existed['result'][0]);
		}elseif(!count($existed['result'])) {
			// init phpseclib
			$phpseclib = new Phpseclib();
			// create rsa keys
			$create = $phpseclib->rsaKeyInit();
			// setup create at time 
			$create['_id'] = $key['_id'];
			$create['created_at'] = date('Y/m/d h:i:s');
			try {
				// insert into database
				$insert = $this->MRsakey->insert($create);
				if($insert['ok']){
					// get rsa public key
					$key['public'] = $create['public'];
					$key['publicHex'] = $create['publicHex'];
					$result = array('ok' => 1, 'result' => $key);
				}else {
					$result = array('ok' => 0, 'err' => (ENVIRONMENT !== 'production')?$insert['errmsg']:'Data Error');	
				}	
			} catch (Exception $ex) {
				$result = array('ok' => 0, 'err' => (ENVIRONMENT !== 'production')?$ex:'Data Error');	
			}
		}else {
			$result = array('ok' => 0);
		}

		// log user connect
		$this->load->library('Visitor');
		$visitor = new Visitor();
		$visitor->log($clientRandom);

		// sent to client rsa key result
		echo json_encode($result, true);
	}
}