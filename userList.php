<?php
define('PAGENAME', 'userList');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(get_login_role() != 1) redirect('login.php');

include('./include/userList.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-后台管理</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link type="text/css" rel="stylesheet" href="css/table.css" />
	<link href="css/calendar.css" type="text/css" rel="stylesheet"/>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>用户信息列表：</h3>
			<div style="width:72%">
				<table class="diytable" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<th width="10%">序号</th>
						<th width="20%">用户名</th>
						<th width="30%">邮箱</th>
						<th width="15%">角色</th>
						<th width="25%">操作</th>
					</tr>
					<?php foreach($user_list as $user){ ?>
					<tr>
						<td><?=$user['index'] ?></td>
						<td><a href="userCenter.php?name=<?=$user['name'] ?>"><?=$user['name'] ?></a></td>
						<td><?=$user['email'] ?></td>
						<td><?=$user['rolename'] ?></td>
						<td>
							<a href="javascript:doAdd()">添加</a>&nbsp;|&nbsp;<a href="javascript:doModify('<?=$user['name'] ?>')">修改</a>&nbsp;|&nbsp;<a href="javascript:doDel('<?=$user['name'] ?>')">删除</a>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
	<script language="javascript">
		function doAdd(){
			location.href = "userAdd.php";
		}
		function doModify(name){
			location.href = "userAlter.php?name="+name;
		}
		function doDel(name){
			if(confirm("你确定要删除该用户吗（不可恢复）？")){
				location.href = "userDelete.php?name="+name;
			}
		}
	</script>
</body>
</html>