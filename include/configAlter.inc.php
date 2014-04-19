<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$name = get_input('name');
$role = intval(get_input('role'));
$submit = get_input('submit');
if(!$role) $role = 0;
$config = get_config_info($name, $role);
if(!$submit){
	if(!$config){
		pop_message('there is no config message named '.$name, 'configList.php');
	}
}else{
	$value = get_input('value');
	if($config){ //修改配置信息
		alter_config($name, $value, $role);
	}else{ //添加配置信息
		$config = get_config_info($name, 0);
		var_dump($name);
		if(!$config) pop_message('there is no config message named '.$name, 'configList.php');
		insert_config($name, $value, $role, $config['note']);
	}
	insert_log('update config '.$name.' with value '.$value.' under role '.$role.' success', 5);
	close_db_connection();
	pop_message('update config message '.$name.' success', 'configList.php');
}
close_db_connection();
