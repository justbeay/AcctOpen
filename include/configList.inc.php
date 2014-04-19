<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$conn = get_db_connection();
$sql_select = select_sql('config', array('name', 'value', 'role', 'note'));
$result = mysql_query($sql_select) or pop_mysql_error();
$config_list = array();
while($row = mysql_fetch_array($result)){
	$config = array('name' => $row[0],
				'value' => $row[1], 
				'role' => $row[2],
				'rolename' => get_role_name($row[2]),
				'note' => $row[3]);
	array_push($config_list, $config);
}
insert_log('view config list', 2);
close_db_connection();
