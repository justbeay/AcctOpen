<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$title = htmlspecialchars(get_input('title', true));
$content = htmlspecialchars(get_input('content', true));
$priority = intval(get_input('priority', true));
$public = get_input('priority', true);
$title = nl2br($title);
$content = nl2br($content);
$public = empty($public) ? 0 : 1;

if(is_empty($content)){
	pop_message("内容不能为空！");
}

$arr_insert = array('title' => $title,
					'content' => $content,
					'priority' => $priority,
					'public' => $public);
$insert_id = insert_announcement($arr_insert);
insert_log('add announcement '.$insert_id.' success', 4);
close_db_connection();
if($insert_id <= 0){
	pop_message("数据库插入失败！");
}else{
	pop_message("录入成功！<a href='annDetail.php?id=$insert_id'>点击</a>查看详情", 'annList.php');
}