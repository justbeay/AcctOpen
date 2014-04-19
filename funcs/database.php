<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');
require_once('common.php');
require_once('session.php');
require_once('log.php');

function check_user_login($login, $password){
	$sql = 'select id,password,salt from '.table('user')." where name='$login'";
	$conn = get_db_connection();
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	$id = -1;
	while($row = mysql_fetch_array($result)){
		$id = $row['id'];
		$authpassword = $row['password'];
		$salt = $row['salt'];
	}
	if($id < 0) return -1;
	else{
		return $authpassword==md5(md5($password).$salt) ? $id : -1;
	}
}

function auth_login(){
	if(get_session('user_id')){
		return true;
	}else{
		$login = addslashes(get_cookie('login'));
		$authstr = addslashes(get_cookie('authstr'));
		if(!$login || !$authstr) return false;
		$conn = get_db_connection();
		$uid = get_user_id($login, $conn);
		if(validate_db_session($uid, $authstr)){
			$user = get_user_info($uid);
			set_session('user_id', $uid);
			set_session('user_name', $user['name']);
			set_session('user_role', $user['role']);
			insert_log('login successful!', 2);
			return true;
		}else{
			del_cookie('authstr');
			insert_log("failed to login as '$login'", 2);
			return false;
		}
	}
}

function insert_user($user, $conn=null){
	$arr_insert = array();
	if(is_string($user)){
		$arr_insert['name'] = $user;
	}elseif(is_array($user)){
		$arr_insert = $user;
	}else{
		pop_message("非法参数!");
	}
	$arr_insert['salt'] = randomkeys(6);
	if(!isset($arr_insert['password'])){
		$arr_insert['password'] = '123456';
	}
	$arr_insert['password'] = md5(md5($arr_insert['password']).$arr_insert['salt']);
	if(!validate_username($arr_insert['name'])){
		pop_message("姓名 {$arr_insert['name']} 过长或包含非法字符！");
	}
	if(!$conn){
		$conn = get_db_connection();
	}
	$id = get_user_id($arr_insert['name'], $conn);
	if($id == -1){
		$sql = insert_sql($arr_insert, 'user');
		mysql_query($sql, $conn) or pop_mysql_error();
		$id = mysql_insert_id($conn);
		insert_log('add one user '.$id.' success', 4);
	}
	return $id;
}

function get_user_id($name, $conn=null){
	$id = -1;
	if(!$conn){
		$conn = get_db_connection();
	}
	$sql = select_sql('user', 'id', "name='$name' limit 0,1");
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	while($row = mysql_fetch_array($result)){
		$id = $row['id'];
	}
	return $id;
}

function simplify_userlist($userlist, $conn){
	$arr_ret = array();
	$arr_user = array();
	if(is_string($userlist)){
		$arr_user = explode(',', $userlist);
	}elseif(is_array($userlist)){
		$arr_user = $userlist;
	}
	foreach($arr_user as $user){
		if(!trim($user)){
			continue;
		}elseif(strpos($user, '#') !== FALSE){
			// 是用户ID
			array_push($arr_ret, $user);
		}else{
			// 为用户姓名，查询其ID
			$id = get_user_id($user, $conn);
			array_push($arr_ret, '#'.($id>0 ? $id : insert_user($user, $conn)));
		}
	}
	//var_dump($arr_ret);
	//die();
	return $arr_ret;
}

// 返回数组形式，键名为id，键值为name
function get_user_name($id){
	$ret_arr = array();
	if($id){
		$where = '';
		if(is_array($id)){
			$id = array_unique($id);
			$where = 'id in (' . implode(',', $id) . ')';
		}else{
			$id = intval($id);
			$where = 'id='.$id;
		}
		$sql_select = select_sql('user', 'id, name', $where);
		$conn = get_db_connection();
		$result = mysql_query($sql_select, $conn) or pop_mysql_error();
		while($row = mysql_fetch_array($result)){
			$ret_arr[$row[0]] = $row[1];
		}
	}
	return $ret_arr;
}

function get_user_info($id){
	$arr_select = array('id', 'name', 'email', 'description', 'role');
	$sql_select = select_sql('user', $arr_select, "id=$id");
	$conn = get_db_connection();
	$result = mysql_query($sql_select, $conn) or pop_mysql_error();
	while($row = mysql_fetch_array($result)){
		return array('id' => $row[0],
					'name' => $row[1],
					'email' => $row[2],
					'description' => $row[3],
					'role' => $row[4]);
	}
}

/* 获取指定用户通讯录（没有通讯录则返回FALSE）
	format: 0-原始 1-全名称 2-尽量ID
*/
function get_addressbook($uid, $format=1){
	$sql_select  = select_sql('addressbook', 'uid,addressbook', "uid=$uid");
	$conn = get_db_connection();
	$result = mysql_query($sql_select, $conn) or pop_mysql_error();
	$row = get_first_row($result);
	if($row){
		if($format == 0){
			return $row[1];
		}
		$arr_ret = array();
		$arr = explode(',', $row[1]);
		foreach($arr as $val){
			if($val){
				if(substr($val, 0, 1) == '#'){
					if($format == 1){
						$tmp = intval(substr($val, 1));
						$user = get_user_name($tmp);
						if($user) array_push($arr_ret, $user[$tmp]);
					}else{
						array_push($arr_ret, $val);
					}
				}else{
					if($format == 2){
						$uid_tmp = get_user_id($val);
						if($uid != -1) array_push($arr_ret, '#'.$uid_tmp);
					}else{
						array_push($arr_ret, $val);
					}
				}
			}
		}
		return $arr_ret;
	}
	return false;
}

function append_addressbook($uid, $username){
	if($username){
		$conn = get_db_connection();
		$uid_append = get_user_id($username, $conn);
		$addressbook = get_addressbook($uid);
		if($addressbook !== false){
			// 检查通讯录中是否已存在该名称
			foreach($addressbook as $val){
				if($val == $username) return;
			}
			// 不存在该姓名，更新通讯录
			if($uid_append != -1){
				$sql_update = 'update '.table('addressbook')." set addressbook=concat(addressbook, ',', '#$uid_append') where uid=$uid";;
			}else{
				$sql_update = 'update '.table('addressbook')." set addressbook=concat(addressbook, ',', '$username') where uid=$uid";;
			}
			mysql_query($sql_update, $conn) or pop_mysql_error();
		}else{
			// 该用户还没有通讯录，新建
			$addressbook = $uid_append==-1 ? $username : '#'.$uid_append;
			$sql_insert = insert_sql(array('uid'=>$uid, 'addressbook'=>$addressbook),
								'addressbook');
			mysql_query($sql_insert, $conn) or pop_mysql_error();
		}
	}
}

function del_from_addressbook($uid, $username){
	if($username){
		$conn = get_db_connection();
		$arr_user = get_addressbook($uid, 2);
		$uid_del = get_user_id($username, $conn);
		$arr_user = array_diff($arr_user, array($username, '#'.$uid_del));
		$addressbook = implode(',', $arr_user);
		$sql_update = update_sql('addressbook', "addressbook='$addressbook'", "uid=$uid");
		mysql_query($sql_update, $conn) or pop_mysql_error();
	}
}

function insert_announcement($announce){
	$title_default = '未命名';
	$conn = get_db_connection();
	$arr_insert = array();
	if(!is_array($announce)){
		$arr_insert['title'] = $title_default;
		$arr_insert['content'] = $announce;
	}else{
		$arr_insert['title'] = isset($announce['title']) ? $announce['title'] : $title_default;
		$arr_insert['content'] = isset($announce['content']) ? $announce['content'] : '';
		if(isset($announce['priority'])) $arr_insert['priority'] = $announce['priority'];
		if(isset($announce['public'])) $arr_insert['public'] = $announce['public'];
	}
	$arr_insert['time_update'] = date('Y-m-d H:i:s', time());
	$sql = insert_sql($arr_insert, 'announcement');
	mysql_query($sql, $conn) or die("数据库查询失败".mysql_error());
	$id = mysql_insert_id($conn);
	return $id;
}

function get_announcement($id, $conn=null){
	$id = intval($id);
	if(!$conn) $conn = get_db_connection();
	$sql = select_sql('announcement', 'id, title, content, time_create, time_update, count, public', 'id='.$id);
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	$row = get_first_row($result);
	if($row){
		$announce = array();
		$announce['id'] = $row[0];
		$announce['title'] = $row[1];
		$announce['content'] = $row[2];
		$announce['time_create'] = $row[3];
		$announce['time_update'] = $row[4];
		$announce['count'] = $row[5];
		$announce['public'] = $row[6];
		return $announce;
	}else{
		return null;
	}
}

function get_announcement_permission($id, $operator){
	if(get_login_role() == 1) return true;
	$announcement = get_announcement($id);
	if($operator == 'view'){
		return $announcement['public']==1 || (auth_login() && get_login_role()!=3);
	}
	return false;
}

function del_announcement($id){
	$conn = get_db_connection();
	$sql = 'delete from '.table('announcement')." where id=$id";
	mysql_query($sql, $conn) or pop_mysql_error();
}

function get_account_payer($acct_id){
	$conn = get_db_connection();
	$sql = 'select payer from '.table('account')." where id=$acct_id";
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	while($row = mysql_fetch_array($result)){
		return $row['payer'];
	}
	return -1;
}

function get_account($acct_id, $conn=null){
	$acct_id = intval($acct_id);
	if(!$conn) $conn = get_db_connection();
	$sql = select_sql('account', 'id, payer, amount, place, beneficiary, content, note, `time`, time_apm, time_add, poster', 'id='.$acct_id);
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	$row = get_first_row($result);
	if($row){
		$account = array();
		$account['id'] = $row[0];
		$account['payer'] = $row[1];
		$account['amount'] = $row[2];
		$account['place'] = $row[3];
		$account['beneficiary'] = $row[4];
		$account['content'] = $row[5];
		$account['note'] = $row[6];
		$account['time'] = $row[7];
		$account['time_apm'] = $row[8];
		$account['time_add'] = $row[9];
		$account['poster'] = $row[10];
		return $account;
	}else{
		return null;
	}
}

function get_account_poster($acct_id){
	$conn = get_db_connection();
	$sql = 'select poster from '.table('account')." where id=$acct_id";
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	while($row = mysql_fetch_array($result)){
		return $row['poster'];
	}
	return -1;
}

function get_account_permission($id, $operator){
	if(auth_login()){
		if(get_login_role() == 1) return true;
		$login_id = get_login_id();
		$account = get_account($id);
		if($operator == 'view'){
			return get_login_role()==2 || $login_id==$account['payer'] || $login_id==$account['poster']
					|| preg_match('/#'.$login_id.'[},]/', $account['beneficiary']);
		}
		return $login_id==get_account_payer($id) || $login_id==get_account_poster($id);
	}
	return false;
}

function del_account($id){
	$conn = get_db_connection();
	$sql = 'delete from '.table('account')." where id=$id";
	mysql_query($sql, $conn) or pop_mysql_error();
}

// 获取配置信息（role为null时获取当前登录用户的角色）
function get_config($name, $role=null){
	$name = strtoupper($name);
	global ${$name};
	$value_ret = ${$name};
	if($value_ret === null){
		if($role === null) $role = get_login_role();
		global ${$name.$role};
		if(${$name.$role} !== null){
			$value_ret = ${$name.$role};
		}else{
			$conn = get_db_connection();
			$sql = select_sql('config', 'value,role', 'name=\''.strtolower($name).'\'');
			$result = mysql_query($sql, $conn) or pop_mysql_error();
			$value_default = null;
			while($row = mysql_fetch_array($result)){
				if($row['role'] == $role){ $value_ret = $row['value']; break; }
				else if($row['role'] == 0) $value_default = $row['value']; 
			}
			if($value_ret === null) $value_ret = $value_default;
			${$name.$role} = $value_ret;
		}
	}
	return $value_ret;
}

function alter_config($name, $value, $role){
	$name = strtoupper($name);
	$conn = get_db_connection();
	$sql_update = update_sql('config', "value='$value'", 'name=\''.strtolower($name).'\' and role='.$role);
	mysql_query($sql_update, $conn) or pop_mysql_error();
	$name = strtoupper($name);
	global ${$name.$role};
	${$name.$role} = $value;
	return $value;
}

function insert_config($name, $value, $role, $note){
	$name = strtoupper($name);
	$conn = get_db_connection();
	$sql_insert = insert_sql(
					array('name' => strtolower($name),
						'value' => $value,
						'role' => $role,
						'note' => $note
					), 'config');
	mysql_query($sql_insert, $conn) or pop_mysql_error();
	$name = strtoupper($name);
	global ${$name.$role};
	${$name.$role} = $value;
	return $value;
}

// 获取数据库配置表表中某用户的配置星系
function get_config_info($name, $role){
	$conn = get_db_connection();
	$sql = select_sql('config', 'name,value,role,note', 'name=\''.strtolower($name).'\' and role='.$role);
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	if($row = mysql_fetch_array($result)){
		$config = array(
				'name' => $row[0],
				'value' => $row[1],
				'role' => $row[2],
				'note' => $row[3]
			);
		return $config;
	}else{
		return null;
	}
}

function del_config($name, $role){
	if($role){
		$conn = get_db_connection();
		$sql = 'delete from '.table('config')." where name='$name' and role='$role'";
		mysql_query($sql, $conn) or pop_mysql_error();
	}
}