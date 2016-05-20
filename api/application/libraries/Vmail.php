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

if(!class_exists('Vmail')) {
	class Vmail {
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
			// set email config
			$this->controller->email->initialize($this->controller->config->item('email'));
		}

		// ------------------------------------------------------------
		/**
		 * ----------------------------------------
		 * Send Email
		 * ----------------------------------------
		 * 
		 * @todo Send email to address and cc or bcc defined in $email
		 *
		 * @param array $email 
		 */
		public function send($email) {
			// get mail data
			$email =(object) $email;

			// set from address email
			$this->controller->email->from($this->controller->email->smtp_user, $this->controller->email->useragent);

			// set received address email
			if(empty($email->to)) {
				show_error('Email: Receiver address did not set.');
			}else {
				$this->controller->email->to($email->to);
			}

			// set subject of email
			if(empty($email->subject)) {
				show_error('Email: Subject did not set');	
			}else {
				$this->controller->email->subject($email->subject);
			}

			// set 
			if(empty($email->message)) {
				show_error('Email: Message did not set');	
			}else {
				$this->controller->email->message($email->message);
			}

			if(!empty($email->cc)) {
				$this->controller->email->cc($email->cc);
			}

			if(!empty($email->bcc)) {
				$this->controller->email->bcc($email->bcc);
			}

			// init log data
			$log = (object) array(
				'action' => $this->controller->router->method,
				'address' => $email->to,
				'config' => json_encode($this->controller->email, true),
				'data' => json_encode($email, true),
			);

			// send mail
			try {
				$log->status = $this->controller->email->send();

				// log error send mail
				if(!$log->status) {
					echo $this->controller->email->print_debugger();
					$log->origin = 'localhost';
					$log->message = json_encode($this->controller->email->print_debugger(), true);
				}
			} catch (Exception $e) {
				// log exception sent mail
				$log->origin = 'smtp';
				$log->message = json_encode($e, true);
			}

			// set log time
			$log->created_at = date('Y/m/d h:i:s');

			// log sent mail
			$this->controller->MLog_sent_mail->insert((array) $log);

			$this->controller->email->clear();

			return $log->status;
		}
	}
}