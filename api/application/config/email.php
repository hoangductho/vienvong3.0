<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Memcached settings
| -------------------------------------------------------------------------
| Your Memcached servers can be specified below.
|
|	See: http://codeigniter.com/user_guide/libraries/caching.html#memcached
|
*/
$config['email'] = array(
	'useragent' => 'Vienvong.com',
	'protocol' => 'smtp',
    'smtp_host' => 'ssl://smtp.gmail.com',
    'smtp_port' => 465,
    'smtp_user' => 'noreply@vienvong.com',
    'smtp_pass' => 'vienvong@08020388',
    'mailtype'  => 'html', 
    'charset'   => 'utf-8',
    'wordwrap' => TRUE,
    'newline' => "\r\n"
);
