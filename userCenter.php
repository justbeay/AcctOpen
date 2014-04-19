<?php
define('PAGENAME', 'userCenter');

require_once('./funcs/database.php');

if(!auth_login()){
	redirect('login.php');
}

include('./include/userCenter.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-个人中心</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link href="css/calendar.css" type="text/css" rel="stylesheet"/>
	<script src="js/CalendarPopup.js"></script>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script language="javascript" type="text/javascript">
		function doDelete(){
			if(!confirm("你确定要删除该账目吗（不可恢复）？")) return false;
			document.forms[0].action = 'delete.php';
			return true;
		}
		function doReturn(){
			location.href = 'list.php<?=get_session('searchtxt') ?>';
		}
		var cal = new CalendarPopup("calendarDiv");
		cal.showNavigationDropdowns();
	</script>
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>用户资料：</h3>
			<div style="height:120px; margin-left:12px">
				<ul style="list-style:squar	e">
					<li class="left singleline"><div class="left">姓名：</div><div class="left"><?=$user['name'] ?></div></li>
					<li class="left singleline"><div class="left">邮箱：</div><div class="left"><?=$user['email'] ?></div></li>
					<li class="left singleline"><div class="left">密码：</div><div class="left">******&nbsp;&nbsp;<a href="resetPwd.php?name=<?=$user['name'] ?>">修改</a></div></li>
					<li class="left singleline"><div class="left">简介：</div><div class="left"><?=$user['description'] ?></div></li>
					<li class="left singleline"><div class="left">角色：</div><div class="left"><?=$user['role'] ?></div></li>
				</ul>
			</div>
			<h3>我的账务信息：</h3>
			<div style="height:120px; margin-left:12px">
				<form name="form1" name="post">
					<label>起始时间:</label>
					<input type="text" name="time_begin" id="time_begin" size="8" value="<?php if($time_begin) echo $time_begin; ?>" />
					<a style="margin-right:50px" href="#" onclick="cal.select(document.form1.time_begin,'anchor1','yyyy-MM-dd'); return false;">
						<img style="border-width:0" src="images/datepicker.png" width="20" height="20" name="anchor1" id="anchor1" />
					</a>
					<label>结束时间:</label>
					<input type="text" name="time_end" size="8" value="<?php if($time_end) echo $time_end; ?>" />
					<a style="margin-right:20px" href="#" onclick="cal.select(document.form1.time_end,'anchor2','yyyy-MM-dd'); return false;">
						<img style="border-width:0" src="images/datepicker.png" width="20" height="20" name="anchor2" id="anchor2" />
					</a>
					<input style="width:72px" type="submit" name="searh" value="查看" />
				</form><br/>
				<div>
					<?php
						if(!empty($time_begin)) echo $time_begin.(empty($time_end) ? '至今' : '至'.$time_end);
						else if(!empty($time_end)) echo '截止'.$time_end;
						else echo '全部账目中';
					?>，您为&nbsp;<a href="<?=$url_pay ?>"><?=$num_pay ?></a>&nbsp;项公共账目付款，总额为&nbsp;<?=$amount_pay ?>&nbsp;元；<br />
					需要分摊的公共账目为&nbsp;<a href="<?=$url_share ?>"><?=$num_share ?></a>&nbsp;项，总额为&nbsp;<?=$amount_share ?>&nbsp;元；<br />
					最终您实际还需要支付&nbsp;<?=$amount_remain ?>&nbsp;元。
				</div>
				<div id="calendarDiv" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
			</div>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
</body>
</html>