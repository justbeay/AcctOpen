<?php

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/session.php');
require_once('./funcs/log.php');

$username = get_input('name', true);
$login = auth_login() ? get_login_name() : addslashes(get_input('login', true));
$opassword = get_input('opassword', true);
$password = get_input('npassword', true);
$passwordagain = get_input('npasswordagain', true);
$code = get_input('code', true);
if($username){
	$login = $username;
}
$uid = get_user_id($login, get_db_connection());

if(is_empty($login, $password, $passwordagain, $code)){
	pop_message('存在未输入的必输项');
}elseif(md5(strtolower($code)) != get_session('code')){
	pop_message('验证码输入错误！');
}elseif($password != $passwordagain){
	pop_message('两次密码输入不一致！');
}elseif(!validate_str_en($password, 6, 16)){
	pop_message("密码格式不正确（必须由6至16位大小写英文字符、数字及'_'、'-'组成）");
}else{
	$log_param = array('ip' => get_ip_address(), 
						'content' => 'failed to reset the password',
						'time' => time()-600);
	$failed_cnt = query_log_cnt($log_param);
	if($failed_cnt > 3){
		insert_log("try to reset the password of '$login' frequently", 2);
		pop_message('尝试过于频繁，请10分钟后再试！');
	}
	if((get_login_role()!=1 || get_login_id()==$uid) && check_user_login($login, $opassword) <= 0){
		insert_log("failed to reset the password of '$login' ".($failed_cnt+1)." times", 2);
		pop_message((auth_login() ? '' : '用户名或').'原密码错误！');
	}
}

$salt = randomkeys(6);
$arr_update = array('password' => md5(md5($password).$salt),
					'salt' => $salt
		);
$sql = update_sql('user', $arr_update, "id=$uid");
mysql_query($sql) or pop_mysql_error();
insert_log("reset the password of '$login'", 4);
pop_message('Congratulations, your password has beeen resetted  successful right now!', 'login.php');
