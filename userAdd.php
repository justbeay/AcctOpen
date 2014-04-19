<?php
define('PAGENAME', 'userAdd');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(!auth_login()) redirect('login.php');
$login_role = get_login_role();
if($login_role!=1) pop_message("抱歉，您无权限进行该操作！", 'list.php');

$submit = get_input('submit', true);
if($submit){
	include('./include/userAdd.inc.php');
}else{
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-新增用户</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>添加用户：</h3>
			<form name="form1" action="userAdd.php" method="post" autocomplete="off" >
				<ul>
					<li><label for="username">用户名：&nbsp;&nbsp;</label><input type="text" name="username" id="username" />&nbsp;&nbsp;*</li>
					<li><label for="email">邮箱：&nbsp;&nbsp;</label><input type="text" name="email" id="email" /></li>
					<li><label for="password">密码：&nbsp;&nbsp;</label><input type="password" name="password" id="password" />&nbsp;&nbsp;*</li>
					<li><label for="password_again">重复密码：&nbsp;&nbsp;</label><input type="password" name="password_again" id="password_again" />&nbsp;&nbsp;*</li>
					<li><label for="description">简介：</label>
						<textarea name="text" id="description" rows="4" cols="30"></textarea>
					</li>
					<li><label for="role">角色：&nbsp;&nbsp;</label>
						<select name="role" id="role" onchange="roleChanges(this)">
							<option value="1">------- 管理员 -------</option>
							<option value="2" selected>----- 一般用户 -----</option>
							<option value="3"> ----- 受限用户 -----</option>
						</select>&nbsp;&nbsp;*
					</li>
					<li style="display:none;"><label for="authPasswd">授权密码：</label>
						<input type="password" name="authPasswd" id="authPasswd" />&nbsp;&nbsp;*
					</li>
					<li>
						<input type="submit" class="button" name="submit" value="提交"/>
						<input type="reset" class="button" name="reset" value="重填"/>
					</li>
				</ul>
			</form>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
	<script language="javascript">
		function roleChanges(ele){
			if(ele.value == 1){
				document.form1.authPasswd.parentNode.style.display = "";
			}else{
				document.form1.authPasswd.parentNode.style.display = "none";
			}
		}
	</script>
</body>
</html>
<?php 
}
?>