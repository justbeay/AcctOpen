<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$id = intval(get_input('id'));
if($id <= 0){
	pop_message("非法参数: $id");
}else{
	$sql_select = select_sql('announcement', 
						'id, title, content, time_create, time_update, count, priority, public',
						'id='.$id);
	$conn = get_db_connection();
	$result = mysql_query($sql_select, $conn) or pop_mysql_error();
	$row_data = get_first_row($result);
	if($row_data == false){
		pop_message("数据不存在或已删除！");
	}else{
		$announce = array();
		$announce['id'] = $row_data[0];
		$announce['title'] = $row_data[1];
		$announce['content'] = $row_data[2];
		$announce['time_create'] = $row_data[3];
		$announce['time_update'] = $row_data[4];
		$announce['count'] = intval($row_data[5])+1;
		$announce['priority'] = $row_data[6];
		$announce['public'] = $row_data[7];
	}
}
// 更新浏览次数
$sql = update_sql('announcement', 'count='.$announce['count'], 'id='.$id);
mysql_query($sql, $conn) or pop_mysql_error();
insert_log('view announcement detail of '.$id, 1);
close_db_connection();
