<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

/*
| -------------------------------------------------------------------------
| Presystem Setup
| -------------------------------------------------------------------------
| 
| Default setup for system befor running proccess
*/
$hook['pre_system'][] = array(
        'class'    => 'Presytem',
        'function' => 'init',
        'filename' => 'Presytem.php',
        'filepath' => 'hooks',
);

/*
| -------------------------------------------------------------------------
| Validate Pre Controller's Function
| -------------------------------------------------------------------------
|
| Validate pre controller conditions
*/
$hook['post_controller_constructor'][] = array(
        'class'    => 'Rules',
        'function' => 'defined',
        'filename' => 'Rules.php',
        'filepath' => 'hooks',
);

/*
| -------------------------------------------------------------------------
| Request Data Decrypt
| -------------------------------------------------------------------------
| 
| In security mod, data sent from client will be encrypt by AES/RSA method
|
| If controller want to use them, this need defined security mod and using  
| Datasec class to get data request
|
*/
$hook['post_controller_constructor'][] = array(
        'class'    => 'Datasec',
        'function' => 'requestData',
        'filename' => 'Datasec.php',
        'filepath' => 'hooks',
);

/*
| -------------------------------------------------------------------------
| Authenticate User Requested
| -------------------------------------------------------------------------
| 
| Check authenticate of user when he sent request to server
|
*/
$hook['post_controller_constructor'][] = array(
        'class'    => 'Rules',
        'function' => 'authenticate',
        'filename' => 'Rules.php',
        'filepath' => 'hooks',
);

/*
| -------------------------------------------------------------------------
| Request Data Valid
| -------------------------------------------------------------------------
| 
| Check data sent from client is validate with Rules defined
|
*/
$hook['post_controller_constructor'][] = array(
        'class'    => 'Rules',
        'function' => 'validate',
        'filename' => 'Rules.php',
        'filepath' => 'hooks',
);

/*
| -------------------------------------------------------------------------
| Response Data Encrypt
| -------------------------------------------------------------------------
| 
| With controller using security mod, response data needed encrypt by AES
| method before send to server.
|
| After finish proccessing, response data will be transfer to $response_data
| variable of controller class, and then, Datasec class will be encrypt them
| and sent to client.
|
*/
$hook['post_controller'][] = array(
        'class'    => 'Datasec',
        'function' => 'responseData',
        'filename' => 'Datasec.php',
        'filepath' => 'hooks',
);
