<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

$id = intval(get_input('id'));
if($id <= 0){
	pop_message("非法参数: $id", 'list.php');
}else{
	$conn = get_db_connection();
	$sql_select = select_sql('account', 
						'id, payer, amount, time, time_apm, place, content, beneficiary, note, time_add',
						'id='.$id);
	$result = mysql_query($sql_select, $conn) or pop_mysql_error();
	$row_data = get_first_row($result);
	if($row_data == false){
		pop_message("数据不存在或已删除！");
	}else{
		// 获取付款人姓名
		$payer = get_user_name($row_data[1]);
		// 获取受益人姓名
		$beneficiary = $row_data[7];
		$arr = preg_split('/[\{\},]/', $beneficiary);
		$arr = array_filter($arr);
		$arr = array_values($arr);
		for($i=0; $i<count($arr); $i++){
			if(substr($arr[$i], 0, 1) == '#'){
				$id = substr($arr[$i], 1);
				$user = get_user_name($id);
				$arr[$i] = $user[$id];
			}
		}
		$beneficiary = implode(',', $arr);

		$acct = array();
		$acct['id'] = $row_data[0];
		$acct['payer'] = $payer[$row_data[1]];
		$acct['amount'] = $row_data[2];
		$acct['time'] = $row_data[3];
		$acct['time_apm'] = $row_data[4];
		$acct['place'] = $row_data[5];
		$acct['content'] = $row_data[6];
		$acct['beneficiary'] = $beneficiary;
		$acct['note'] = $row_data[8];
		$acct['time_add'] = $row_data[9];
	}
}
insert_log("view account detail of $id", 2);
close_db_connection();
