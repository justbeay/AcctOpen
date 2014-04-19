<?php
require_once('./funcs/common.php');
require_once('./funcs/database.php');

$uid = get_login_id();
$username = get_input('name');
$time_begin = get_input('time_begin');
$time_end =get_input('time_end');
// 用户权限检查
if($username){
	$id_tmp = get_user_id($username);
	if(!$id_tmp){
		pop_message("不存在该用户！", "userList.php");
	}
	if(get_login_role() == 1){
		$uid = $id_tmp;
	}
}else{
	$username = get_login_name();
}
// 数据合法性校验
if(($time_begin && !validate_date($time_begin)) || ($time_end && !validate_date($time_end))){
	var_dump(validate_date($time_begin));
	pop_message("时间格式错误！", 'list.php');
}
// 格式化数据
$time_begin = $time_begin ? format_date($time_begin) : '';
$time_end = $time_end ? format_date($time_end) : '';

$user = get_user_info($uid);
if($user['email']){
	$user['email'] = encryptEmail($user['email']);
}else{
	$user['email'] = '未设置';
}
$user['role'] = get_role_name($user['role']);

$conn = get_db_connection();
$where_pay = 'payer='.$uid;
$where_share = 'beneficiary REGEXP \'#'.$uid.'[},]\'';
if($time_begin){
	$where_pay .= " and `time`>='$time_begin'";
	$where_share .= " and `time`>='$time_begin'";
}
if($time_end){
	$where_pay .= " and `time`<='$time_end'";
	$where_share .= " and `time`<='$time_end'";
}
$sql_pay = select_sql('account', 'amount', $where_pay);
$sql_share = select_sql('account', 'amount,beneficiary', $where_share);

$result_pay = mysql_query($sql_pay, $conn);
$num_pay = mysql_num_rows($result_pay);
$amount_pay = 0;
while($row = mysql_fetch_array($result_pay)){
	$amount_pay += $row[0];
}

$result_share = mysql_query($sql_share, $conn);
$num_share = mysql_num_rows($result_share);
$amount_share = 0;
while($row = mysql_fetch_array($result_share)){
	$tmp = substr_count($row[1], ',') + 1;
	$amount_share += $row[0]/$tmp;
}
$amount_share = sprintf('%.2f', $amount_share);
$amount_remain = $amount_share - $amount_pay;

$url_pay = 'list.php?payer='.$username;
$url_share = 'list.php?beneficiary='.$username;
if($time_begin){
	$url_pay .= '&time_begin='.$time_begin;
	$url_share .= '&time_begin='.$time_begin;
}
if($time_end){
	$url_pay .= '&time_end='.$time_end;
	$url_share .= '&time_end='.$time_end;
}

// 插入日志
$log_content = 'view userCenter';
if($username != get_login_name()){
	$log_content .= ' of uid: '.$uid;
}
if($time_begin){
	$log_content .= ' with ?time_begin='.$time_begin;
	if($time_end){
		$log_content .= '&time_end'.$time_end;
	}
}elseif($time_end){
	$log_content .= ' with ?time_end='.$time_end;
}
insert_log($log_content, 1);

close_db_connection();
