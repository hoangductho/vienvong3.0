<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Errors code setting
| -------------------------------------------------------------------------
| Errors code config and errors system messages.
|
|	
|
*/
# Errors data
$config['errors'] = array(
	# Rules Erros
	# Type: 10xxx
	#
	# Function Undefined Errors
	# Type: 100xx
	10000 => array(
		'err' => 10000,
		'code' => '404',
		'message' => array(
			'english' => 'Init: Rules is undefined!',
			'vietnam' => 'Khởi tạo: Quy tắc chưa được khai báo!'
		)
	),
	10001 => array(
		'err' => 10001,
		'code' => '404',
		'message' => array(
			'english' => 'Init: Function is undefined!',
			'vietnam' => 'Khởi tạo: Hàm chưa được khai báo!'
		)
	),
	10002 => array(
		'err' => 10002,
		'code' => '404',
		'message' => array(
			'english' => 'Init: Function is not existed!',
			'vietnam' => 'Khởi tạo: Hàm không tồn tại!'
		)
	),
	# Http method incorrect
	# Type: 101xx
	10101 => array(
		'err' => 10101,
		'code' => '401',
		'message' => array(
			'english' => 'Init: Http method is incorrect!',
			'vietnam' => 'Khởi tạo: Giao thức http không đúng!'
		)
	),
	# Data security mode incorrect
	# Type: 102xx
	10201 => array(
		'err' => 10201,
		'code' => '401',
		'message' => array(
			'english' => 'Init: Data security info is incorrect!',
			'vietnam' => 'Khởi tạo: Thông tin bảo mật không đúng!'
		)
	),
	# Authenglishticate missing
	# Type: 103xx
	10301 => array(
		'err' => 10301,
		'code' => '401',
		'message' => array(
			'english' => 'Authenticate: info is incorrect!',
			'vietnam' => 'Thông tin xác thực không đúng!'
		)
	),
	10302 => array(
		'err' => 10302,
		'code' => '401',
		'message' => array(
			'english' => 'Authenticate: token is expired!',
			'vietnam' => 'Xác thực: token đã hết hạn!'
		)
	),
	10303 => array(
		'err' => 10303,
		'code' => '401',
		'message' => array(
			'english' => 'Authenticate: token is invalid!',
			'vietnam' => 'Xác thực: token không hợp lệ!'
		)
	),
	10304 => array(
		'err' => 10304,
		'code' => '401',
		'message' => array(
			'english' => 'Authenticate: user is not actived!',
			'vietnam' => 'Xác thực: tài khoản chưa được kích hoạt!'
		)
	),
	10305 => array(
		'err' => 10305,
		'code' => '401',
		'message' => array(
			'english' => 'Authenticate: user is not existed!',
			'vietnam' => 'Xác thực: tài khoản không tồn tại!'
		)
	),

	# Validate Errors
	# Type: 104xx
	10400 => array(
		'err' => 10401,
		'code' => '401',
		'message' => array(
			'english' => 'Validate: Function require parameters!',
			'vietnam' => 'Xác nhận: hàm không có tham sô!'
		)
	),
	10401 => array(
		'err' => 10401,
		'code' => '401',
		'message' => array(
			'english' => 'Validate: Function do not have parameters!',
			'vietnam' => 'Xác nhận: hàm không có tham sô!'
		)
	),
	10402 => array(
		'err' => 10402,
		'code' => '401',
		'message' => array(
			'english' => 'Validate: Parameters is not null!',
			'vietnam' => 'Xác nhận: Bắt buộc nhập tham số đầu vào!'
		)
	),
	10403 => array(
		'err' => 10403,
		'code' => '401',
		'message' => array(
			'english' => 'Validate: Parameters is invalid!',
			'vietnam' => 'Xác nhận: Tham số đầu vào không hợp lệ!'
		)
	),
);
