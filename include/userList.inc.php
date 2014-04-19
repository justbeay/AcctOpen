<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$conn = get_db_connection();
$sql_select = select_sql('user', array('name', 'email', 'role'));
$result = mysql_query($sql_select) or pop_mysql_error();
$index = 1;
$user_list = array();
while($row = mysql_fetch_array($result)){
	$user = array('index' => $index++,
				'name' => $row[0],
				'email' => $row[1] ? $row[1] : '未知',
				'role' => $row[2],
				'rolename' => get_role_name($row[2]));
	array_push($user_list, $user);
}
insert_log('view user list', 2);
close_db_connection();
