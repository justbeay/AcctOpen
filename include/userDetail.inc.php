<?php 

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$username = get_input('name');
if($username && validate_username($username) && ($uid=get_user_id($username))!=-1){
	$user = get_user_info($uid);
}else{
	pop_message('该用户不存在', 'userList.php');
}