<?php

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/session.php');
require_once('./funcs/log.php');

$login = get_input('login', true);
$password = get_input('password', true);
$remember_me = get_input('remember_me', true);
if(is_empty($login, $password)){
	pop_message("存在未输入的必输项！");
}
if((!validate_username($login) && !validate_email($login)) || !validate_limited_str($password, 4, 16)){
	pop_message("登录失败！");
}
$log_param = array('ip' => get_ip_address(), 
					'content' => 'failed to login as',
					'time' => time()-600);
$failed_cnt = query_log_cnt($log_param);
if($failed_cnt > 3){
	insert_log("try to login as '$login' frequently", 2);
	pop_message('登录过于频繁，请10分钟后再试！');
}
$id = check_user_login($login, $password);
if($id < 0){
	$failed_cnt++;
	insert_log("failed to login as '$login' $failed_cnt times", 2);
	pop_message("登录失败！");
}
$user = get_user_info($id);
// 验证密码成功，设置SESSION及COOKIE
set_session('user_id', $id);
set_session('user_name', $user['name']);
set_session('user_role', $user['role']);
set_cookie('login', $user['name']);
set_cookie('remember_me', $remember_me);
$authstr = get_db_session($id) ? update_db_session($id) : insert_db_session($id);
if($remember_me){
	set_cookie('authstr', $authstr);
}
insert_log('login successful!', 2);
close_db_connection();
pop_message("登录成功", 'index.php');