<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$username = htmlspecialchars(get_input('username', true));
$email = htmlspecialchars(get_input('email', true));
$password = get_input('password', true);
$password_again = get_input('password_again', true);
$description = get_input('description', true);
$role = intval(get_input('role', true));
$authPasswd = get_input('authPasswd', true);
$description = nl2br($description);

// 地段校验
if(is_empty($username, $password, $password_again, $role)){
	pop_message('有必输字段没有输入！');
}
if($password != $password_again){
	pop_message('两次密码输入不一致！');
}
if(get_user_id($username) != -1){
	pop_message('用户名已存在！');
}
if($role==1 && check_user_login(get_login_name(), $authPasswd)==-1){
	pop_message('您无权限添加管理员权限用户！', 'userList.php');
}

// 插入新的用户
$user_insert = array('name' => $username,
					'password' => $password,
					'role' => $role);
if(!empty($email)){
	$user_insert['email'] = $email;
}
if(!empty($description)){
	$user_insert['description'] = $description;
}
$insert_id = insert_user($user_insert);
close_db_connection();
if($insert_id <= 0){
	pop_message("数据库插入失败！");
}else{
	pop_message("录入成功！<a href='userCenter.php?name=$username'>点击</a>查看详情", 'userList.php');
}