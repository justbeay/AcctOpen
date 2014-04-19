<?php
define('PAGENAME', 'configDelete');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$id = get_input('id');
if(!get_announcement_permission($id, 'delete')){
	insert_log('try to delete announcement '.$id.' failed', 3);
	pop_message('抱歉，您无权限进行该操作！');
}
del_announcement($id);
insert_log('delete announcement '.$id.' success', 5);
close_db_connection();

pop_message("删除成功！", 'annList.php');
