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
$config['user_status'] = (object)array(
	'pendding' => 0,
    'actived' => 1,
    'deactive' => 2,
    'banned' => 3,
    'lock' => 4
);
