<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ================================================
 * Log_sent_mail Table Queries
 * ================================================
 *
 * @Description 
 *
 * Log sent mail status and errors
 *
 * --------------------------------------
 * 
 * @Properties
 *
 * array(
 * 		'_id' => 'ID',
 *		'action' => 'Action controller method name',
 *		'address' => 'Email address',
 *		'status' => 'Status sent mail',
 *		'origin' => 'Origin of erro',
 *		'message' => 'Error message data',
 *		'data' => 'Email sent data',
 *		'config' => 'Mailer config',
 *		'created_at' => 'Created at time',
 * )
 */
class MAccess_token extends MBase {
	/**
	 * Table name
	 */
	public $table = 'access_token';
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Constructor
	 * --------------------------------------------
	 */
	function __construct() {
		parent::__construct();
	}
}