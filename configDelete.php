<?php
define('PAGENAME', 'annDelete');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

if(get_login_role() != 1) redirect('login.php');

$name = get_input('name');
$role = intval(get_input('role'));
if(!$role) $role = 0;
$config = get_config_info($name, $role);
if(!$config){
	pop_message('there is no config message named '.$name, 'configList.php');
}
if($role == 0){
	pop_message('could not delete the basic value of config message: '.$name, 'configList.php');
}

del_config($name, $role);
insert_log('delete config '.$name.' under role '.$role.' success', 5);
close_db_connection();

pop_message("删除成功！", 'configList.php');
