<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$username = htmlspecialchars(get_input('name'));
$email = htmlspecialchars(get_input('email', true));
$description = get_input('description', true);
$role = intval(get_input('role', true));
$authPasswd = get_input('authPasswd', true);
$description = nl2br($description);

// 地段校验
$uid = get_user_id($username);
if($uid == -1){
	pop_message('不存在该用户！', 'userList.php');
}
$user = get_user_info($uid);
if($role==1 && $user['role']!=1 && check_user_login(get_login_name(), $authPasswd)==-1){
	pop_message('您无权限将用户角色升级为管理员！', 'userList.php');
}

// 插入新的用户
$user_update = array('role'=>$role);
if(!empty($email)){
	$user_update['email'] = $email;
}
if(!empty($description)){
	$user_update['description'] = $description;
}
$conn = get_db_connection();
$sql_update = update_sql('user', $user_update, "name='$username'");
mysql_query($sql_update, $conn) or die("数据库更新失败".mysql_error());
close_db_connection();
pop_message("用户更新成功！<a href='userCenter.php?name=$username'>点击</a>查看详情", 'userList.php');
