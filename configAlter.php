<?php define('PAGENAME', 'configAlter');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(!auth_login()) redirect('login.php');
include('./include/configAlter.inc.php');
?>
<head>
	<meta charset="utf-8" />
	<base target="_self">
	<title>账务开放平台-修改配置信息</title>
</head>
<body>
	<div class="mainbody">
		<form method="post" action="configAlter.php?name=<?=$config['name'] ?>">
			<label>名称:</label><?=$config['name'] ?><br />
			<label for="value">值:</label><input type="text" name="value" id="value" value="<?=$config['value'] ?>" /><br />
			<label>作用角色:</label>
			<select name="role">
				<option value="0" <?php if($config['role']==0) echo 'selected' ?>>全部</option>
				<option value="1"<?php if($config['role']==1) echo 'selected' ?>>管理员</option>
				<option value="2"<?php if($config['role']==2) echo 'selected' ?>>普通用户</option>
				<option value="3"<?php if($config['role']==3) echo 'selected' ?>>受限用户</option>
				<option value="-1"<?php if($config['role']==-1) echo 'selected' ?>>游客</option>
			</select><br />
			<label>备注:</label><?=$config['note'] ?><br />
			<input type="submit" name="submit" value="确定" />
			<input type="button" onclick="window.close()" value="取消" />
		</form>
	</div>
</body>
