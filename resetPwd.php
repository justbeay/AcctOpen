<?php
define('PAGENAME', 'resetPwd');

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/session.php');

$submit = get_input('submit', true);
$username = get_input('name');
if($submit){
	include('./include/resetPwd.inc.php');
}else{
	if($username){
		include('./include/userDetail.inc.php');
	}
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-密码重置</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script language="javascript" type="text/javascript">
		function genCode(){
			document.form1.code_img.src='code.php?tm='+Math.random();
		}
	</script>
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>密码重置：</h3>
			<form name="form1" action="resetPwd.php" method="post" >
				<?php if($username){ ?>
				<input type="hidden" name="name" value="<?=$user['name'] ?>" />
				<?php } ?>
				<ul>
					<?php if(!$username){ ?>
					<li><label for="login">用户名：</label>
						<input type="text" name="login" id="login" />
					</li>
					<?php }else{ ?>
					<li><label for="login">用户名：</label>
						<?=$user['name'] ?>
					</li>
					<?php } ?>
					<?php if(get_login_role()!=1 || !$username || get_login_id()==$user['id']){ ?>
					<li><label for="opassword">请输入原密码：</label>
						<input type="password" name="opassword" id="opassword" />
					</li>
					<?php } ?>
					<li><label for="npassword">请输入新密码：</label>
						<input type="password" name="npassword" id="npassword" />
					</li>
					<li><label for="npasswordagain">请再次输入密码：</label>
						<input type="password" name="npasswordagain" id="npasswordagain" />
					</li>
					<li><label for="code">验证码：&nbsp;&nbsp;</label>
						<input type="text" name="code" id="code"/>
						<img src="code.php" id="code_img" onClick="genCode()"/>
						<a id="check_img" href="javascript:genCode()">看不清?</a>
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
</body>
</html>
<?php 
}
?>