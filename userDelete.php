<?php
define('PAGENAME', 'annDelete');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/log.php');

if(get_login_role() != 1) redirect('login.php');

$username = get_input('name');
$uid = get_user_id($username);

if($uid == -1){
	pop_message('不存在该用户！');
}
if($uid == get_login_id()){
	pop_message('无法删除自己！');
}

$conn = get_db_connection();
$where_acct = 'payer='.$uid.' and beneficiary REGEXP \'#'.$uid.'[},]\'';
$sql_acct = select_sql('account', 'id', $where_acct);
$result_acct = mysql_query($sql_acct, $conn);
if(mysql_num_rows($result_acct) > 0){
	pop_message('请先删除或修改包含该用户的所有账户信息！');
}

$sql_delete = 'delete from '.table('user').' where id='.$uid;
mysql_query($sql_delete) or die("数据库更新失败".mysql_error());

insert_log('delete user '.$username.' success', 5);
close_db_connection();

pop_message("删除成功！", 'userList.php');
