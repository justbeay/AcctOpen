<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$id = get_input('id', true);
$amount = get_input('amount', true);
$beneficiary = get_input('beneficiary', true);
$content = htmlspecialchars(get_input('detail', true));
$note = htmlspecialchars(get_input('note', true));
$content = nl2br($content);
$note = nl2br($note);

/*用户输入数据校验*/
if(is_empty($id, $amount, $beneficiary)){
	pop_message("存在未输入的必须项！");
}
if(!validate_number($amount)){
	pop_message("输入金额：$amount 格式有误！");
}
$sql_select = select_sql('account', 'time', 'id='.$id);
$conn = get_db_connection();
$result = mysql_query($sql_select, $conn) or pop_mysql_error();
$row_data = get_first_row($result);
if($row_data == false){
	pop_message("数据不存在或已删除！");
}
$time_compare = compare_date($row_data[0], date('Y-m-d'));
$days_acct_edit = get_config('days_acct_edit');
if($time_compare < -($days_acct_edit-1)*86400 || $time_compare > 0){
	pop_message("抱歉，本系统只能对{$days_acct_edit}天内的账目进行修改！");
}
$beneficiary = format_liststr($beneficiary);

// 更新用户通讯录
$arr_beneficiary = explode(',', $beneficiary);
foreach($arr_beneficiary as $val){
	append_addressbook(get_login_id(), $val);
}

/*开始修改账目操作*/
$conn = get_db_connection();
$beneficiary = implode(',', simplify_userlist($beneficiary, $conn));
$update_arr = array(
			'amount' => $amount,
			'content' => $content,
			'beneficiary' => '{'.$beneficiary.'}',
			'note' => $note
		);
$sql = update_sql('account', $update_arr, 'id='.$id);
insert_log('alter account '.$id.' success', 3);
mysql_query($sql) or pop_mysql_error();
close_db_connection();
pop_message("修改成功！<a href='detail.php?id=$id'>点击</a>查看详情", "list.php");
