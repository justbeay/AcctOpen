<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

require_once('config.php');

function table($name){
	return 'tb_'.$name;
}

function get_input($name, $isPost=false){
	$ret = null;
	$arr = func_get_args();
	if(count($arr) == 1){
		$ret = isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : '');
	}else{
		$isPost = $arr[1];
		if($isPost){
			$ret = isset($_POST[$name]) ? $_POST[$name] : '';
		}else{
			$ret = isset($_GET[$name]) ? $_GET[$name] : '';
		}
	}
	return trim($ret);
}

function get_ip_address(){
	$realip = '';
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv(“HTTP_X_FORWARDED_FOR”)){
            $realip = getenv(“HTTP_X_FORWARDED_FOR”);
        } else if (getenv(“HTTP_CLIENT_IP”)) {
            $realip = getenv(“HTTP_CLIENT_IP”);
        } else {
            $realip = getenv(“REMOTE_ADDR”);
        }
    }
	preg_match("/[\d\.]{7,15}/", $realip, $cips);
	$realip = isset($cips[0]) ? $cips[0] : 'unknown';
	unset($cips);
    return $realip;
	//return $_SERVER['REMOTE_ADDR'];
}

function get_session($key){
	if(!isset($_SESSION)) session_start();
	global $SESSION_PREFIX;
	global $COOKIE_PREFIX;
	$key = $SESSION_PREFIX.$key;
	return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
}

function set_session($key, $value){
	if(!isset($_SESSION)) session_start();
	global $SESSION_PREFIX;
	global $COOKIE_PREFIX;
	$key = $SESSION_PREFIX.$key;
	$_SESSION[$key] = $value;
}

function del_session($key){
	if(!isset($_SESSION)) session_start();
	global $SESSION_PREFIX;
	global $COOKIE_PREFIX;
	$key = $SESSION_PREFIX.$key;
	if(isset($_SESSION[$key])) unset($_SESSION[$key]);
}

function get_cookie($key){
	global $SESSION_PREFIX;
	global $COOKIE_PREFIX;
	$key = $COOKIE_PREFIX.$key;
	return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
}

/*设置cookie（默认保存15天）*/
function set_cookie($key, $value, $expires_in=0){
	global $SESSION_PREFIX;
	global $COOKIE_PREFIX;
	$key = $COOKIE_PREFIX.$key;
	$expires_in = !empty($expires_in) ? $expires_in : get_config('cookie_expires_in');
	setcookie($key, $value, time()+$expires_in);
}

function del_cookie($key){
	global $SESSION_PREFIX;
	global $COOKIE_PREFIX;
	$key = $COOKIE_PREFIX.$key;
	setcookie($key,'',time()-3600);
}

function randomkeys($length){
	$pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
	$key = '';
	for($i=0;$i<$length;$i++){
		$key .= $pattern{mt_rand(0,35)};    //生成php随机数
	}
	return $key;
}

function include_func($name){
	if(is_string($name)){
		include_once('/funcs/'.$name.'.php');
	}
}

function format_date($date){
	if(is_string($date)){
		$pattern = "/(\d{4})[-\/,\.]?(\d{1,2})[-\/,\.]?(\d{1,2})/";
		$replace = "\$1-\$2-\$3";
		$str = preg_replace($pattern, $replace, $date);
		$arr = explode("-", $str);
		return sprintf("%04s-%02s-%02s", $arr[0], $arr[1], $arr[2]);
	}elseif(is_int($date)){
		return date("Y-m-d", $date);
	}
}

function format_liststr($str){
	$arr_separator = array(',', ';', '，', '；', '、', ' ');
	$arr_ret = array($str);
	foreach($arr_separator as $separator){
		$arr_tmp = array();
		foreach($arr_ret as $value){
			$arr_tmp = array_merge($arr_tmp, explode($separator, $value));
		}
		$arr_ret = $arr_tmp;
	}
	return implode(',', $arr_ret);
}

function compare_date($date1, $date2){
	if(is_string($date1)){
		$time1 = format_date($date1);
		$time1 = mktime(0, 0,  0, intval(substr($time1, 5, 2)), intval(substr($time1, 8, 2)), intval(substr($time1, 0, 4)));
	}else{
		$time1 = intval($date1);
	}
	if(is_string($date2)){
		$time2 = format_date($date2);
		$time2 = mktime(0, 0,  0, intval(substr($time2, 5, 2)), intval(substr($time2, 8, 2)), intval(substr($time2, 0, 4)));
	}else{
		$time2 = intval($date2);
	}
	return $time1-$time2;
}

function validate_date($date){
	if(strpos($date, '-') !== FALSE){
		$arr = explode('-', $date);
	}elseif(strpos($date, '/') !== FALSE){
		$arr = explode('/', $date);
	}else{
		$arr = array(
				substr($date, 0, 4),
				substr($date, 4, 2),
				substr($date, 6)
			);
	}
	if(count($arr) == 3){
		$year = intval($arr[0]);
		$month = intval($arr[1]);
		$day = intval($arr[2]);
		if($year >= 2000 && $year <= 2500){
			$arr_month1 = array(1, 3, 5, 7, 8, 10, 12);
			$arr_month2 = array(4, 6, 9, 11);
			if(array_search($month, $arr_month1)!==false && $day>0 && $day<=31){
				return true;
			}elseif(array_search($month, $arr_month2)!==false && $day>0 && $day<=30){
				return true;
			}elseif($month==2 && $day>0){
				if($year%4==0 && $year%100!=0){
					return $day<=29;
				}else{
					return $day<=28;
				}
			}
		}
	}
	return false;
}

function validate_username($str){
	$minlen = 2;
	$maxlen = 16;
	$pattern_cn = '/^[\x{4e00}-\x{9fa5}]{' . $minlen/2 . ',' . $maxlen/2 . '}$/u';
	$pattern_en = '/^[-_a-zA-Z0-9]{' . "$minlen,$maxlen}$/";
	return preg_match($pattern_cn, $str)>0 || preg_match($pattern_en, $str)>0;
}

function validate_email($email){
	$pattern = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';
	return preg_match($pattern, $email) > 0;
}

function validate_number($amount){
	$pattern = '/^((0|([1-9]\d*)*)(.\d+)?)$/';
	return preg_match($pattern, $amount) > 0;
}

function validate_str_en($str, $minlen=0, $maxlen=0){
	if($maxlen == 0) $maxlen = strlen($str);
	$pattern = '/^[0-9a-zA-Z_-]{'."$minlen,$maxlen".'}$/';
	return preg_match($pattern, $str) > 0;	
}

function validate_limited_str($str, $minlen=0, $maxlen=0){
	if($maxlen == 0) $maxlen = strlen($str);
	$pattern = '/^[0-9a-zA-Z\x{4e00}-\x{9fa5}_-]{'."$minlen,$maxlen".'}$/u';
	return preg_match($pattern, $str) > 0;
}

function pop_message($message, $page='', $timeout=3){
	header("Content-Type:text/html; charset=utf-8");
	echo $message."<br><br>本页面将在{$timeout}秒后开始跳转。。。没有反应？单击<a href='$page'>此处<a>";
	redirect($page, $timeout);
	exit();
}

function redirect($page='/', $timeout=0){
	header("refresh:$timeout;url=$page");
	close_db_connection();
	exit();
}

function is_empty(){
	$arr = func_get_args();
	foreach($arr as $value){
		if(empty($value)){
			return true;
		}
	}
	return false;
}

function my_substr($str, $from, $len, $encoding='utf-8'){
	if($encoding == 'utf-8'){
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
					'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
					'$1',$str);
	}else{
		$tmpstr = "";
		$strlen = $from + $len;
		for($i = 0; $i < $strlen; $i++) {
			if(ord(substr($str, $i, 1)) > 0xa0) {
				$tmpstr .= substr($str, $i, 2);
				$i++;
			} else
				$tmpstr .= substr($str, $i, 1);
		}
		return $tmpstr;
	}
}

function br2nl($text){
	return preg_replace('/<br\\s*?\/??>/i', '', $text);
}

function simplify_str($str, $maxlen, $encoding='utf-8'){
	$len = $maxlen>3 ? $maxlen-3 : $maxlen;
	$ret = my_substr($str, 0, $len, $encoding);
	return strlen($ret) < strlen($str) ? $ret.'...' : $ret;
}

function encryptEmail($email){
	return preg_replace("/^([^@]{3})[^@]*@(.+)$/", "\${1}***@\$2", $email);
}

function addslashes_array($a){
	if(is_array($a)){
		foreach($a as $n=>$v){
			$b[$n]=addslashes_array($v);
		}
		return $b;
	}else{
		if(is_string($a)) return addslashes($a);
		else return $a;
	}
}

function get_db_connection(){
	global $g_dblink;
	global $database;
	if(empty($g_dblink)){
		$g_dblink = mysql_connect($database['host'].':'.$database['port'], $database['username'], $database['password']);
		mysql_select_db($database['dbname'], $g_dblink) or die('Could not select database.');
	}
	return $g_dblink;
}

function close_db_connection(){
	global $g_dblink;
	if($g_dblink != false){
		mysql_close($g_dblink);
	}
	$g_dblink = false;
}

function update_sql($table, $update, $where){
	if(is_array($update)){
		$update = addslashes_array($update);
	}
	$sql = 'update '.table($table).' set ';
	if(is_array($update)){
		foreach($update as $key=>$value){
			$sql .= "`$key`=";
			if(is_string($value)){
				$sql .= "'$value',";
			}else{
				$sql .= $value.',';
			}
		}
		if(substr($sql, strlen($sql)-1) == ','){
			$sql = substr($sql, 0, strlen($sql)-1);
		}
	}else{
		$sql .= $update;
	}
	$sql .= ' where '.$where;
	return $sql;
}

function insert_sql($arr, $table){
	$arr = addslashes_array($arr);
	$sql = '';
	if(is_array($arr)){
		$sql .= 'insert into '.table($table).'(';
		$sql_suffix = ') values(';
		foreach($arr as $key=>$value){
			$sql .= "`$key`, ";
			if(is_string($value)){
				$sql_suffix .= "'$value', ";
			}else{
				$sql_suffix .= $value.', ';
			}
		}
		if(substr($sql, strlen($sql)-1) != '('){
			$sql = substr($sql, 0, strlen($sql)-2);
		}
		if(substr($sql_suffix, strlen($sql_suffix)-1) != '('){
			$sql_suffix = substr($sql_suffix, 0, strlen($sql_suffix)-2);
		}
		$sql_suffix .= ')';
		$sql .= $sql_suffix;
	}elseif(is_string($arr)){
		$sql = $arr;
	}
	return $sql;
}

function select_sql($table, $select, $where='1'){
	$sql = 'select ';
	if(is_string($select)){
		$sql .= $select;
	}elseif(is_array($select)){
		foreach($select as $value){
			$sql .= "`$value`,";
		}
		if(strrpos($sql, ',') == strlen($sql)-1){
			$sql = substr($sql, 0, strlen($sql)-1);
		}
	}else{
		pop_message("非法参数$select！");
	}
	$sql .= ' from '.table($table).' where '.$where;
	return $sql;
}

function pop_mysql_error(){
	pop_message("抱歉，数据库查询失败！".mysql_error(), 'index.php', 10);
}

function get_first_row($result){
	if(mysql_num_rows($result) < 1){
		return null;
	}else{
		return mysql_fetch_array($result);
	}
}

function get_time_apm($h){
	if($h<0 || $h>23){
		die("param illegal!");
	}elseif($h<=4 || $h>=18){  //晚上
		return 5;
	}elseif($h <= 8){  // 早上
		return 1;
	}elseif($h <= 10){  // 上午
		return 2;
	}elseif($h <= 13){  // 中午
		return 3;
	}elseif($h <= 17){  // 下午
		return 4;
	}
}

function trans_time_apm($num){
	$arr_map = array(1 => '早上',
				2 => '上午',
				3 => '中午',
				4 => '下午',
				5 => '晚上');
	if(array_key_exists($num, $arr_map)){
		return $arr_map[$num];
	}
}

function get_login_id(){
	return auth_login() ? get_session('user_id') : -1;
}

function get_login_role(){
	return auth_login() ? get_session('user_role') : -1;
}

function get_login_name(){
	return auth_login() ? get_session('user_name') : '游客';
}

function get_role_name($id){
	switch($id){
	case -1:
		return '游客';
	case 0:
		return '全部';
	case 1:
		return '管理员';
	case 2:
		return '普通用户';
	case 3:
		return '受限用户';
	default:
		return '未知';
	}
}