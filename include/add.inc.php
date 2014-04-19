<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$payer = get_input('payer', true);
$amount = get_input('amount', true);
$time = get_input('time', true);
$time_apm = get_input('time_apm', true);
$place = get_input('place', true);
$content = htmlspecialchars(get_input('detail', true));
$beneficiary = get_input('beneficiary', true);
$note = htmlspecialchars(get_input('note', true));
$content = nl2br($content);
$note = nl2br($note);
if(is_empty($time, $payer, $amount, $beneficiary)){
	pop_message("存在未输入的必须项！");
}
if(!validate_date($time)){
	pop_message("输入时间: $time 格式错误！");
}
if(!validate_number($amount)){
	pop_message("输入金额：$amount 格式有误！");
}
$time_compare = compare_date($time, date('Y-m-d'));
$days_acct_add = get_config('days_acct_add');
if($time_compare < -($days_acct_add-1)*86400 || $time_compare > 0){
	pop_message("为保证录入账目的准确性，本系统只可录入{$days_acct_add}天内的账目！");
}
$beneficiary = format_liststr($beneficiary);
$time = format_date($time);
if($time_apm == 'morning'){
	$time_apm = 1;
}elseif($time_apm == 'am'){
	$time_apm = 2;
}elseif($time_apm == 'noon'){
	$time_apm = 3;
}elseif($time_apm == 'pm'){
	$time_apm = 4;
}elseif($time_apm == 'night'){
	$time_apm = 5;
}

$conn = get_db_connection();
$payer = insert_user($payer, $conn);
// 更新用户通讯录
$payer_arr = get_user_name($payer);
append_addressbook(get_login_id(), $payer_arr[$payer]);
$arr_beneficiary = explode(',', $beneficiary);
foreach($arr_beneficiary as $val){
	append_addressbook(get_login_id(), $val);
}
$beneficiary = implode(',', simplify_userlist($beneficiary, $conn));

// 开始执行插入账务
$insert_arr = array(
			'payer' => $payer,
			'amount' => $amount,
			'time' => $time,
			'time_apm' => $time_apm,
			'place' => $place,
			'content' => $content,
			'beneficiary' => '{'.$beneficiary.'}',
			'note' => $note,
			'poster' => get_session('user_id')
		);
$sql = insert_sql($insert_arr, 'account');
mysql_query($sql) or pop_mysql_error();
$insert_id = mysql_insert_id($conn);
// 更新日志
insert_log('add account '.$insert_id.' success', 4);
close_db_connection();
if($insert_id <= 0){
	pop_message("数据库插入失败！".mysql_error());
}else{
	pop_message("录入成功！<a href='detail.php?id=$insert_id'>点击</a>查看详情", 'list.php');
}