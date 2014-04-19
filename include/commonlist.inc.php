<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$oper = get_input('oper');
$name = get_input('name');
if($oper && $name){
	if(!validate_username($name)){
		die('invalid username!');
	}
	if($oper == 'del'){
		del_from_addressbook(get_login_id(), $name);
	}
}

$userlist = get_addressbook(get_login_id());

?>