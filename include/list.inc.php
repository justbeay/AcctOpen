<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$pageno = intval(get_input('pageno'));
$pageno = $pageno>0 ? $pageno : 1;
$operFlag = get_input('oper');
$amount = get_input('amount');
$payer = addslashes(get_input('payer'));
$beneficiary = addslashes(get_input('beneficiary'));
$time_begin = get_input('time_begin');
$time_end = get_input('time_end');
$content = addslashes(get_input('content'));
$note = addslashes(get_input('note'));
// 数据合法性校验
if($amount && !validate_number($amount)){
	pop_message("金额格式有误！", 'list.php');
}elseif(($time_begin && !validate_date($time_begin)) || ($time_end && !validate_date($time_end))){
	var_dump(validate_date($time_begin));
	pop_message("时间格式错误！", 'list.php');
}
// 格式化数据
$time_begin = $time_begin ? format_date($time_begin) : '';
$time_end = $time_end ? format_date($time_end) : '';
if($operFlag == 1){
	$oper = '>';
}elseif($operFlag == 3){
	$oper = '<';
}else{
	$oper = '=';
}
// 保存搜索条件到cookie
$searchtxt = '';
if($pageno) $searchtxt .= '&pageno='.$pageno;
if($amount) $searchtxt .= '&oper='.$operFlag.'&amount='.$amount;
if($payer) $searchtxt .= '&payer='.$payer;
if($beneficiary) $searchtxt .= '&beneficiary='.$beneficiary;
if($time_begin) $searchtxt .= '&time_begin='.$time_begin;
if($time_end) $searchtxt .= '&time_end='.$time_end;
if($content) $searchtxt .= '&content='.$content;
if($note) $searchtxt .= '&note='.$note;
if($searchtxt) $searchtxt = '?'.substr($searchtxt, 1);
set_session('searchtxt', $searchtxt);

// 开始搜索账务信息
$where = '1';
if($amount){
	$where .= ' and amount'.$oper.$amount;
}
if($payer){
	$where .= ' and payer in (';
	$index = 0;
	foreach(explode(',', format_liststr($payer)) as $per_payer){
		$tmp_uid = get_user_id($per_payer);
		if($tmp_uid > 0){
			if($index++ == 0) $where .= $tmp_uid;
			else  $where .= ', '.$tmp_uid;
		}
	}
	if(!$index) $where .= "-1";  // 不存在该用户
	$where .= ')';
}
if($beneficiary){
	$where .= ' and ';
	$index = 0;
	foreach(explode(',', format_liststr($beneficiary)) as $per_beneficiary){
		$tmp_uid = get_user_id($per_beneficiary);
		if($tmp_uid > 0){
			if(!$index++) $where .='beneficiary REGEXP \'';
			else $where .= '|';
			$where .= '(#'.$tmp_uid.'[},])';
		}
	}
	if($index) $where .= '\'';
	else $where .= 'beneficiary=\'{-1}\''; // 不存在该用户
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
if($note){
	$where .= " and note like '%$note%'";
}
if(get_login_role()==3){  // 角色为3的用户做特殊账目显示控制
	$where .= ' and (payer='.get_login_id().' or beneficiary REGEXP \'#'.get_login_id().'[},]\')';
}

$conn = get_db_connection();

$sql_select = select_sql('account', 'count(id)', $where);
$totalrows = 0;
$result = mysql_query($sql_select) or pop_mysql_error();
while($row = mysql_fetch_array($result)){
	$totalrows = $row[0];
}
$page_size = get_config('page_size');
$totalpage = (int) (($totalrows-1)/$page_size) + 1;
$row_start = ($pageno-1)*$page_size;

$sql_select = select_sql('account', 
			array('id', 'payer', 'amount', 'content', 'time', 'time_apm'),
			"$where order by time desc,time_apm,id desc limit $row_start, $page_size");
$result = mysql_query($sql_select) or pop_mysql_error();
$acct_arr = array();
$payer_list = array();
while($row = mysql_fetch_array($result)){
	$acct = array('id' => $row[0], 
				'index' => ++$row_start,
				'payer' => $row[1], 
				'amount' => $row[2], 
				'content' => $row[3] ? simplify_str(str_replace('<br />', ' ', $row[3]), 50) : '无内容',
				'time_display' => $row[4].' '.trans_time_apm($row[5]));
	array_push($acct_arr, $acct);
	array_push($payer_list, $row[1]);
}
$payer_list = get_user_name($payer_list);
$acct_list = array();
foreach($acct_arr as $acct){
	$acct['payer'] = $payer_list[$acct['payer']];
	array_push($acct_list, $acct);
}
insert_log('view account list with '.$searchtxt, 2);
close_db_connection();
