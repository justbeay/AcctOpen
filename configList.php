<?php
define('PAGENAME', 'configList');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(get_login_role() != 1) redirect('login.php');

include('./include/configList.inc.php');
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
			<h3>应用配置信息：</h3>
			<div style="width:90%">
				<table class="diytable" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<th width="20%">名称</th>
						<th width="15%">值</th>
						<th width="10%">作用角色</th>
						<th width="35%">备注</th>
						<th width="20%">操作</th>
					</tr>
					<?php foreach($config_list as $config){ ?>
					<tr>
						<td><?=$config['name'] ?></td>
						<td><?=$config['value'] ?></td>
						<td><?=$config['rolename'] ?></td>
						<td><?=$config['note'] ?></td>
						<td>
							<a href="javascript:doAdd('<?=$config['name'] ?>')">添加</a>&nbsp;|&nbsp;<a href="javascript:doModify('<?=$config['name'] ?>', <?=$config['role'] ?>)">修改</a>&nbsp;|&nbsp;<a href="javascript:doDel('<?=$config['name'] ?>', <?=$config['role'] ?>)">删除</a>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
	<script language="javascript">
		function openNewWindow(url){
			//window.open (url, 'newwindow', 'height=300, width=500, top=0,left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no');
			var ret = window.showModalDialog(url,null,"dialogWidth:350px;dialogHeight:350px;help:no;status:no");
			location.reload();
		}
		function doAdd(name){
			openNewWindow("configAlter.php?name="+name);
		}
		function doModify(name, role){
			openNewWindow("configAlter.php?name="+name+"&role="+role);
		}
		function doDel(name, role){
			if(confirm("你确定要删除该配置信息吗（不可恢复）？")){
				location.href = "configDelete.php?name="+name+"&role="+role;
			}
		}
	</script>
</body>
</html>