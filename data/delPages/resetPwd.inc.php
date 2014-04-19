<?php

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/session.php');

$login = addslashes(get_input('login', true));
$password = get_input('npassword', true);
$passwordagain = get_input('npasswordagain', true);
$code = get_input('code', true);
if(is_empty($login, $password, $passwordagain, $code)){
	pop_message('存在未输入的必输项');
}elseif(md5(strtolower($code)) != get_session('code')){
	pop_message('验证码输入错误！');
}elseif($password != $passwordagain){
	pop_message('两次密码输入不一致！');
}elseif($uid != get_user_id($login, get_db_connection())){
	pop_message('用户名输入有误！');
}elseif(!validate_str_en($password, 6, 16)){
	pop_message("密码格式不正确（必须由6至16位大小写英文字符、数字及'_'、'-'组成）");
}

$salt = randomkeys(6);
$arr_update = array('password' => md5(md5($password).$salt),
					'salt' => $salt
		);
$sql = update_sql('user', $arr_update, "id=$uid");
mysql_query($sql) or pop_mysql_error();
delete_db_session($uid, 2);
pop_message('Congratulations, your password has beeen resetted  successful right now!', 'login.php');