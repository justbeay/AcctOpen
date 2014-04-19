<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('common.php');

function insert_db_session($uid, $category=1){
	if($category == 1){ // 用户登录session
		$expires_in = 86400*15;
	}elseif($category == 2){  // 密码重置session
		$expires_in = 86400;
	}
	$conn = get_db_connection();
	$key = '';
	while(1){
		$key = randomkeys(32);
		$sql_select = select_sql('session', 'id', "`key`='$key'");
		$result = mysql_query($sql_select, $conn) or pop_mysql_error();
		if(mysql_num_rows($result) == 0) break;
	}
	$arr_insert = array('uid' => $uid,
						'key' => $key,
						'category' => $category,
						'expires_in' => $expires_in,
						'update_time' => date('Y-m-d H:i:s', time())
			);
	$sql_insert = insert_sql($arr_insert, 'session');
	mysql_query($sql_insert, $conn) or pop_mysql_error();
	return $key;
}

function get_db_session($uid, $category=1){
	$conn = get_db_connection();
	$where = "uid=$uid and category='$category'";
	$sql_select = select_sql('session', array('id', 'uid', 'key', 'category', 'expires_in', 'update_time'), $where);
	$result = mysql_query($sql_select, $conn) or pop_mysql_error();
	while($row = mysql_fetch_array($result)){
		return array('id' => $row[0],
					'uid' => $row[1],
					'key' => $row[2],
					'category' => $row[3],
					'expires_in' => $row[4],
					'update_time' => strtotime($row[5])
			);
	}
	return null;
}

function validate_db_session($uid, $key, $category=1){
	if(!$uid || !$key) return false;
	$key = addslashes($key);
	$conn = get_db_connection();
	$where = "`uid`=$uid and `key`='$key' and `category`=$category";
	$sql_select = select_sql('session', array('id', 'uid', 'key', 'category', 'expires_in', 'update_time'), $where);
	$result = mysql_query($sql_select, $conn) or pop_mysql_error();
	while($row = mysql_fetch_array($result)){
		$session = array('id' => $row[0],
					'uid' => $row[1],
					'key' => $row[2],
					'category' => $row[3],
					'expires_in' => $row[4],
					'update_time' => strtotime($row[5])
			);
		$time_current = time();
		$time_expires = $session['update_time']+$session['expires_in'];
		return $time_current>=$session['update_time'] && $time_current<=$time_expires;
	}
	return false;
}

function delete_db_session($uid, $category=1){
	$conn = get_db_connection();
	$sql_update = update_sql('session', array('expires_in'=>0), "uid=$uid and category=$category");
	//$sql = 'delete from '.table('session')." where uid='$uid' and category='$category'";
	mysql_query($sql_update, $conn) or pop_mysql_error();
}

function update_db_session($uid, $category=1){
	$key = '';
	$conn = get_db_connection();
	while(1){
		$key = randomkeys(32);
		$sql_select = select_sql('session', 'id', "`key`='$key'");
		$result = mysql_query($sql_select, $conn) or pop_mysql_error();
		if(mysql_num_rows($result) == 0) break;
	}
	$arr_update = array('key' => $key,
					'update_time' => date('Y-m-d H:i:s', time())
		);
	$arr_where = "`uid`=$uid and `category`=$category";
	$sql = update_sql('session', $arr_update, $arr_where);
	mysql_query($sql) or pop_mysql_error();
	return $key;
}