<?php
define('PAGENAME', 'delete');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

$id = get_input('id');
if(!get_account_permission($id, 'delete')){
	insert_log('try to delete account '.$id.' failed', 3);
	pop_message('抱歉，您无权限进行该操作！');
}
del_account($id);
insert_log('delete account '.$id.' success', 5);
pop_message("删除成功！", 'list.php');
