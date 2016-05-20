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

if(!class_exists('Visitor')) {
	class Visitor {
		// controller pointer
		private $controller;

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
			// load user_agent library
			$this->controller->load->library('user_agent');
		}

		// ------------------------------------------------------------
		/**
		 * ----------------------------------------
		 * Send Email
		 * ----------------------------------------
		 * 
		 * @todo Logging user agent into log visitor table
		 *
		 * @param array $log 
		 */
		public function log($client) {
			if($this->controller->agent->is_browser() || $this->controller->agent->is_mobile()) {
				// init log data
				$log = array(
					'ip' => $this->controller->input->ip_address(),
					'client_string' => $client,
					'browser' => $this->controller->agent->browser(),
					'browser_version' => $this->controller->agent->version(),
					'mobile' => $this->controller->agent->mobile(),
					'platform' => $this->controller->agent->platform(),
					'referrer' => $this->controller->agent->referrer(),
					'agent_string' => $this->controller->agent->agent_string(),
					'languages' => $this->controller->agent->languages(),
					'created_at' => date('Y/m/d h:i:s'),
				);

				// init log id
				$log['_id'] = hash('sha256', $log['ip'].$log['agent_string'].$client);

				// load log Model
				$this->controller->load->model('MLog_visitor');
				$existed = $this->controller->MLog_visitor->get(array('_id' => $log['_id']));
				if($existed['ok'] && !count($existed['result'])) {
					$insert = $this->controller->MLog_visitor->insert($log);
					return $insert['ok'];
				}
				return $existed['ok'];
			}
		}
	}
}