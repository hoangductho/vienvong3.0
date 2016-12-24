<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ================================================
 * RSA Table Queries
 * ================================================
 * @Description 
 *
 * Storage user info in database
 *
 * --------------------------------------
 *
 * @Properties
 * 
 * array(
 * 		'_id' => 'ID',
 *		'email' => 'Email of usr',
 *		'password' => 'Password hashed by Sha256 method',
 *		'status' => 'Status code of account',
 *		'status_alias' => 'Status name',
 *		'active_code' => 'Active account code',
 *		'reset_code' => 'Reset password code',
 *		'fullname' => 'Fullname of user',
 *		'nickname' => 'Nickname of user',
 *		'birthday' => 'Birthday of user',
 *		'avatar' => 'Avatar of user',
 *		'avatar_thumb' => 'Avatar thumb of user',
 *		'interested' => 'Interested objects of user',
 *		'created_at' => 'Crated at time',
 * )
 */
class MGroups extends MBase {
	/**
	 * Table name
	 */
	public $table = 'groups';
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