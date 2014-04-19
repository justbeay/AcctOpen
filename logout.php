<?php
define('PAGENAME', 'add');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

if(auth_login()){
	insert_log('logout');
	del_session('user_id');
	del_session('user_name');
	del_cookie('authstr');
	if(!get_cookie('remember_me')){
		del_cookie('login');
	}
}
pop_message('您已成功注销！', 'login.php');