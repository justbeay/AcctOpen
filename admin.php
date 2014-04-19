<?php
define('PAGENAME', 'admin');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(get_login_role() != 1) redirect('login.php');
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
			<ul>
				<li style="margin:5 20"><a href="configList.php">应用配置管理</a></li>
				<li style="margin:5 20"><a href="userList.php">用户管理</a></li>
				<li style="margin:5 20"><a href="logList.php">应用日志查看</a></li>
			</ul>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
	<script language="javascript">
	</script>
</body>
</html>