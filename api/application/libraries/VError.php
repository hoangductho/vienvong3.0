<?php
/**
 * ================================================
 * Send Mail Class
 * ================================================
 * 
 * Using CI_Email to send mail
 *
 * Using config defined in email.php config file
 */

if(!class_exists('VError')) {
	class VError {
		// controller pointer
		private $controller;
		private $errors;

		// ------------------------------------------------------------
		/**
		 * ----------------------------------------
		 * Constructor
		 * ----------------------------------------
		 */
		public function __construct() {
			$this->initialize();
		}

		// ------------------------------------------------------------
		/**
		 * ----------------------------------------
		 * Initialize
		 * ----------------------------------------
		 */
		private function initialize() {
			// get controller pointer
			$this->controller = &get_instance();
			// set email config
			$this->errors = $this->controller->config->item('errors');
		}

		// ------------------------------------------------------------
		/**
		 * ----------------------------------------
		 * Error
		 * ----------------------------------------
		 *
		 * @param $code int error code
		 * @param $message string error message 
		 */
		public function error($code, $message = '') {
			$error = $this->errors[$code];

			$lang = $this->controller->config->item('language');

			$response = array(
				'ok' => 0,
				'code' => $error['code'],
				'err' => $code,
				'errmsg' => strlen($message) == 0 ? $error['errmsg'][$lang] : $message;
			);

			// Response data
			echo json_encode($response);

			// Kill proccess
			die();
		}
	}
}