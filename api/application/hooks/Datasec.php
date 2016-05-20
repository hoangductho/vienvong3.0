<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datasec {
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Constructor
	 * --------------------------------------------
	 */
	function __construct() {
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Get Request Data
	 * --------------------------------------------
	 */
	public function requestData() {

		// get controller pointer
		$controller = &get_instance();
		// get aes keypair
		$dataSecKey = $controller->input->get_request_header('Content-Header');
		if(!empty($dataSecKey)) {
			// create id for key
			$key['_id'] = date('Ymd');
			// fields needed get
			$select = ['_id', 'private'];
			// get key from database
			$rsaKeypair = $controller->MRsakey->get($key, $select);

			if($rsaKeypair['ok'] && count($rsaKeypair['result'])) {
				// init phpseclib
				$phpseclib = new Phpseclib();

				try {
					// RSA descrypt to read AES keypair
					$aesKeypair = $phpseclib->rsaDecryptCryptoJS($dataSecKey, $rsaKeypair['result'][0]);
					// Regexp of AES Keypair
					$aesKeyRegexp = array('options' => array('regexp' => '/^[a-f0-9]{64}+(\/)+[a-f0-9]{32}$/'));
					// get AES Keypair
					if(!filter_var($aesKeypair, FILTER_VALIDATE_REGEXP, $aesKeyRegexp) === false) {
						// explode aes key and init vector
						list($aesKey, $aesIV) = explode('/', $aesKeypair);
						// set aes keypair for controller
						$controller->aes = array('key' => $aesKey, 'iv' => $aesIV);

						if($controller->input->method(true) === 'POST') {
							// get data posted
							$dataPosted = json_decode(file_get_contents('php://input'), true);
							// AED decrypt to read data posted
							if(!empty($dataPosted['encrypted'])) {
								// data decrypted
								$dataDecrypted = $phpseclib->aesDecryptCryptoJS($dataPosted['encrypted'], $aesKey, $aesIV);
								// set request data for controller
								$controller->request_data = json_decode($dataDecrypted, true);
							}else {
								$this->error('AES encrypted data not found');	
							}
						}
					}else {
						$this->error('AES Keypair invalid');	
					}
				} catch (Exception $e) {
					$this->error($e);
				}
			}
		}else if($controller->input->method(true) === 'POST') {
			$controller->request_data = json_decode(file_get_contents('php://input'), true);
		}
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Response Protected Data
	 * --------------------------------------------
	 */
	public function responseData() {
		$controller = &get_instance();
		if($controller->input->method(true) === 'POST' && !empty($controller->aes)) {
			// init PHP seclib
			$Phpseclib = new Phpseclib();
			// AES Encrypt data
			$response = $Phpseclib->aesEncryptCryptoJS(json_encode($controller->response_data, true), $controller->aes['key'], $controller->aes['iv']);
			// response data
			echo json_encode(array('response' => $response), true);
		}
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Error Message Response
	 * --------------------------------------------
	 * 
	 * @param string $message error message
	 *
	 * @return void
	 */
	Protected function error($message = null) {
		echo json_encode(array('ok' => 0, 'err' => ($message && (ENVIRONMENT !== 'production'))?$message:'Data posted invalid!'));
		die();
	}
}
