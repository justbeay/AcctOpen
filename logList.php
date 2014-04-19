<?php
define('PAGENAME', 'admin');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(get_login_role() != 1) redirect('login.php');

include('./include/logList.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-后台管理</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link type="text/css" rel="stylesheet" href="css/table.css" />
	<link href="css/calendar.css" type="text/css" rel="stylesheet"/>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script src="js/CalendarPopup.js"></script>
	<script language="javascript">
		var cal = new CalendarPopup("calendarDiv");
		cal.showNavigationDropdowns();
	</script>
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>应用日志：</h3>
			<div  class="searchdiv">
				<form name="form1" action="logList.php" method="get">
					<label for="role">角色:</label>
					<select name="role">
						<option value="0" <?php if($role==0) echo 'selected' ?>>全部</option>
						<option value="1" <?php if($role==1) echo 'selected' ?>>管理员</option>
						<option value="2" <?php if($role==2) echo 'selected' ?>>普通用户</option>
						<option value="3" <?php if($role==3) echo 'selected' ?>>受限用户</option>
						<option value="4" <?php if($role==4) echo 'selected' ?>>游客</option>
					</select>
					<label for="username">用户名:</label>
					<input type="text" name="username" id="username" value="<?php if($username) echo $username; ?>" size="8" />
					<label for="content">内容:</label>
					<input type="text" name="content" id="content" size="20" value="<?php if($content) echo $content; ?>" />
					<label for="time_begin">时间:</label>
					<input type="text" name="time_begin" id="time_begin" size="8" value="<?php if($time_begin) echo $time_begin; ?>" />
					<a href="#" onclick="cal.select(document.form1.time_begin,'anchor1','yyyy-MM-dd'); return false;">
						<img style="border-width:0" src="images/datepicker.png" width="20" height="20" name="anchor1" id="anchor1" />
					</a>至
					<input type="text" name="time_end" size="8" value="<?php if($time_end) echo $time_end; ?>" />
					<a href="#" onclick="cal.select(document.form1.time_end,'anchor2','yyyy-MM-dd'); return false;">
						<img style="border-width:0" src="images/datepicker.png" width="20" height="20" name="anchor2" id="anchor2" />
					</a>
					<input type="submit" value="搜索" style="width:72px;margin-left:30px;" />
				</form>
				<div id="calendarDiv" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
			</div>
			<table class="diytable" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<th width="7%">序号</th>
					<th width="12%">用户名</th>
					<th width="51%">内容</th>
					<th width="12%">IP地址</th>
					<th width="18%">时间</th>
				</tr>
				<?php foreach($log_list as $log){ ?>
				<tr>
					<td><?=$log['index'] ?></td>
					<td><?=$log['username'] ?></td>
					<td><?=$log['content'] ?></td>
					<td><?=$log['ip'] ?></td>
					<td><?=$log['time'] ?></td>
				</tr>
				<?php } ?>
			</table>
			<?php
				$requestUrl = get_session('searchtxt');
				$requestUrl = preg_replace("/([?&])pageno=\d+[?&]?/", "", $requestUrl);
				if($requestUrl) $requestUrl = 'logList.php?'.$requestUrl.'&pageno=';
				else  $requestUrl = 'logList.php?pageno=';
			?>
			<div class="pagingbar">
				<a href="<?=$requestUrl ?>1">首页</a>
				<a <?php if($pageno>1){ echo 'href="'.$requestUrl.($pageno-1).'"'; } ?>>上一页</a>
				<a <?php if($pageno<$totalpage){ echo 'href="'.$requestUrl.($pageno+1).'"'; } ?>>下一页</a>
				<a href="<?=$requestUrl.$totalpage ?>">尾页</a>
			</div>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
	<script language="javascript">
	</script>
</body>
</html>