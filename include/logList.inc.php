<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$pageno = intval(get_input('pageno'));
$pageno = $pageno>0 ? $pageno : 1;
$role = get_input('role');
$username = get_input('username');
$content = addslashes(get_input('content'));
$time_begin = get_input('time_begin');
$time_end = get_input('time_end');
// 数据合法性校验
if(($time_begin && !validate_date($time_begin)) || ($time_end && !validate_date($time_end))){
	var_dump(validate_date($time_begin));
	pop_message("时间格式错误！", 'list.php');
}
// 格式化数据
$time_begin = $time_begin ? format_date($time_begin) : '';
$time_end = $time_end ? format_date($time_end) : '';
$role = intval($role);
// 保存搜索条件到cookie
$searchtxt = '';
if($pageno) $searchtxt .= '&pageno='.$pageno;
if($role) $searchtxt .= '&role='.$role;
if($username) $searchtxt .= '&username='.$username;
if($time_begin) $searchtxt .= '&time_begin='.$time_begin;
if($time_end) $searchtxt .= '&time_end='.$time_end;
if($content) $searchtxt .= '&content='.$content;
if($searchtxt) $searchtxt = '?'.substr($searchtxt, 1);
set_session('searchtxt', $searchtxt);

// 开始搜索日志信息
$where = '1';
if($role == -1){
	$where .= ' and uid is null';
}else if($role > 0){
	$where .= ' and uid in ( select id from '.table('user').' where role='.$role.' )';
}
if($username){
	$where .= ' and uid in (';
	$index = 0;
	foreach(explode(',', format_liststr($username)) as $per_username){
		$tmp_uid = get_user_id($per_username);
		if($tmp_uid > 0){
			if($index++ == 0) $where .= $tmp_uid;
			else  $where .= ', '.$tmp_uid;
		}
	}
	if(!$index) $where .= "-1";  // 不存在该用户
	$where .= ')';
}
if($time_begin){
	$where .= " and `time`>='$time_begin'";
}
if($time_end){
	$where .= " and `time`<='$time_end'";
}
if($content){
	$where .= " and content like '%$content%'";
}

$conn = get_db_connection();
$sql_select = select_sql('log', 'count(id)', $where);
$totalrows = 0;
$result = mysql_query($sql_select) or pop_mysql_error();
while($row = mysql_fetch_array($result)){
	$totalrows = $row[0];
}
$page_size = get_config('page_size');
$totalpage = (int) (($totalrows-1)/$page_size) + 1;
$row_start = ($pageno-1)*$page_size;

$sql_select = select_sql('log', 
			array('uid', 'ip', 'content', 'time'),
			"$where order by time desc limit $row_start, $page_size");
$result = mysql_query($sql_select) or pop_mysql_error();
$log_arr = array();
$user_list = array();
while($row = mysql_fetch_array($result)){
	$log = array('uid' => $row[0],
				'index' => ++$row_start,
				'ip' => $row[1], 
				'content' => $row[2] ? simplify_str(str_replace('<br />', ' ', $row[2]), 65) : '无内容',
				'time' => $row[3]);
	array_push($log_arr, $log);
	if($row[0]) array_push($user_list, $row[0]);
}
$user_list = get_user_name($user_list);
$log_list = array();
foreach($log_arr as $log){
	$log['username'] = $log['uid'] ? $user_list[$log['uid']] : '游客';
	array_push($log_list, $log);
}
insert_log('view log list with '.$searchtxt, 2);
close_db_connection();
