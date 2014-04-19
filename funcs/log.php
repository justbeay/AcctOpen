<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');
require_once('common.php');

function insert_log($content, $priority=null, $uid=null){
	$conn = get_db_connection();
	$arr = array();
	$arr['content'] = simplify_str($content, 100);
	if($priority){
		$arr['priority'] = intval($priority);
	}
	if($uid){
		$arr['uid'] = $uid;
	}elseif(auth_login()){
		$arr['uid'] = get_login_id();
	}
	$arr['ip'] = get_ip_address();
	$sql = insert_sql($arr, 'log');
	mysql_query($sql, $conn) or pop_mysql_error();
	$id = mysql_insert_id($conn);
	if($id%get_config('log_clean_interval') == 0){
		clean_log();
	}
	return $id;
}

function clean_log(){
	$num_save = array( 1 => get_config('log_save', 1), 
					2 => get_config('log_save', 2),
					3 => get_config('log_save', 3));  // 每个用户保留的日志数
	$num_save_nouser = get_config('log_save', -1);  // 游客所保留的日志数
	$conn = get_db_connection();
	// 删除每个用户的多余日志
	foreach($num_save as $role=>$num_save_peruser){
		//获取某一角色下所有需清理日志的用户ID
		$arr_cnt = array();
		$sql_peruser = 'select uid,count(id) as cnt from '.table('log').
						' where uid in( select id from '.table('user').' where role='.$role.' )'.
						' group by uid having count(id)>'.$num_save_peruser;
		$result = mysql_query($sql_peruser, $conn) or pop_mysql_error();
		while($row = mysql_fetch_array($result)){
			$arr_cnt[$row[0]] = $row[1];
		}
		// 清理用户的多余日志
		foreach($arr_cnt as $uid=>$num){
			$sql = 'delete from '.table('log').' where uid='.$uid.' order by `time` asc limit '.($num - $num_save_peruser);
			//echo $sql.'<br/>';
			mysql_query($sql, $conn) or pop_mysql_error();
		}
	}
	// 删除游客的多余日志
	$sql_nouser = 'select count(id) from '.table('log').' where uid is null or uid<1';
	$result = mysql_query($sql_nouser, $conn) or pop_mysql_error();
	$row = mysql_fetch_row($result);
	if($row[0] > $num_save_nouser){
		$sql = 'delete from '.table('log').' where uid is null or uid<1'.
						' order by `time` asc limit '.($row[0]-$num_save_nouser);
		//echo $sql.'<br/>';
		mysql_query($sql, $conn) or pop_mysql_error();
	}
}

function query_log_cnt($param=null){
	$where = 1;
	if($param){
		if(is_array($param)){
			if(isset($param['uid'])){
				$where .= ' and uid='.$param['uid'];
			}
			if(isset($param['ip'])){
				$where .= ' and ip=\''.$param['ip'].'\'';
			}
			if(isset($param['content'])){
				$where .= ' and content like \'%'.$param['content'].'%\'';
			}if(isset($param['priority'])){
				$where .= ' and priority='.$param['priority'];
			}
			if(isset($param['time'])){
				$where .= ' and UNIX_TIMESTAMP(`time`)>='.$param['time'];
			}
		}else{
			$where .= ' and content like \'%'.addslashes($param).'%\'';
		}
	}
	$sql = select_sql('log', 'count(id)', $where);
	$conn = get_db_connection();
	$result = mysql_query($sql, $conn) or pop_mysql_error();
	$row = mysql_fetch_row($result);
	return $row[0];
}
