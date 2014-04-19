<?php
$arr1 = array('add');
$arr2 = array('list', 'alter', 'detail');
$arr3 = array('annAdd', 'annAlter', 'annList', 'annDetail');
$arr4 = array('userCenter', 'resetPwd');
$arr5 = array('admin', 'logList', 'configList', 'userList', 'userAdd', 'userAlter');
?>
<div class="header">
	<div class="welcome">您好，<?=get_login_name() ?>！
	<?php if(get_session('user_id')){ ?>
		<a href="logout.php">注销</a>
	<?php }else{ ?>
		<a href="login.php">登录</a>
	<?php } ?>
	</div>
	<h1>账务开放平台</h1>
</div>
<div class="navbar">
	<ul>
	<?php if(get_login_role()>0 && get_login_role()<3){ ?>
		<li <?php if(in_array(PAGENAME, $arr1)) echo 'class="cur"'; ?>><a href="add.php">账务录入</a></li>
	<?php } ?>
	<?php if(auth_login()){ ?>
		<li <?php if(in_array(PAGENAME, $arr2)) echo 'class="cur"'; ?>><a href="list.php">账务查看</a></li>
	<?php } ?>
		<li <?php if(in_array(PAGENAME, $arr3)) echo 'class="cur"'; ?>><a href="annList.php">公告</a></li>
	<?php if(auth_login()){ ?>
		<li <?php if(in_array(PAGENAME, $arr4)) echo 'class="cur"'; ?>><a href="userCenter.php">个人中心</a></li>
	<?php } ?>
	<?php if(get_login_role()==1){ ?>
		<li <?php if(in_array(PAGENAME, $arr5)) echo 'class="cur"'; ?>><a href="admin.php">后台管理</a></li>
	<?php } ?>
	</ul>
</div>