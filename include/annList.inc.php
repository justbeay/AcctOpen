<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

$pageno = intval(get_input('pageno'));
$pageno = $pageno>0 ? $pageno : 1;
$conn = get_db_connection();

$sql_select = select_sql('announcement', 'count(id)');
$totalrows = 0;
$result = mysql_query($sql_select) or pop_mysql_error();
while($row = mysql_fetch_array($result)){
	$totalrows = $row[0];
}
$page_size = get_config('page_size');
$totalpage = (int) (($totalrows-1)/$page_size+1);
$row_start = ($pageno-1)*$page_size;

$where = get_login_role()>=1 && get_login_role()<=2 ? '1' : 'public=1';
$sql_select = select_sql('announcement', 
			array('id', 'title', 'time_update', 'count'),
			"$where order by priority,time_update desc limit $row_start, $page_size");
$result = mysql_query($sql_select) or die("数据库查询失败！");
$announce_list = array();
while($row = mysql_fetch_array($result)){
	$announce = array('id' => $row[0], 
				'index' => ++$row_start,
				'title' => $row[1], 
				'time_update' => $row[2], 
				'count' => $row[3]);
	array_push($announce_list, $announce);
}
insert_log('view announcement list', 1);
close_db_connection();