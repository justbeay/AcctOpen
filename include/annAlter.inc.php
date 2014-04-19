<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$id = intval(get_input('id'));
$title = htmlspecialchars(get_input('title', true));
$content = htmlspecialchars(get_input('content', true));
$priority = intval(get_input('priority'));
$public = get_input('public');
$title = nl2br($title);
$content = nl2br($content);
$public = empty($public) ? 0 : 1;

if(is_empty($content)){
	pop_message("内容不能为空！");
}

$conn = get_db_connection();
$arr_update = array('title' => $title,
					'content' => $content,
					'time_update' => date('Y-m-d H:i:s', time()),
					'priority' => $priority,
					'public' => $public);
$sql = update_sql('announcement', $arr_update, 'id='.$id);
mysql_query($sql, $conn) or die("数据库查询失败".mysql_error());
insert_log('alter announcement '.$id.' success', 3);
close_db_connection();
pop_message("修改成功！<a href='annDetail.php?id=$id'>点击</a>查看详情", "annList.php");
