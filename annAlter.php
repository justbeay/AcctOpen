<?php
define('PAGENAME', 'annAlter');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(!auth_login()){
	redirect('login.php');
}elseif(!get_account_permission(get_input('id'), 'alter')){
	pop_message('抱歉，您无权限进行该操作！', 'annList.php');
}
$submit = get_input('submit', true);
if($submit){
	include('./include/annAlter.inc.php');
}else{
	include('./include/annDetail.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-新增公告</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>添加公告</h3>
			<form name="form1" action="annAlter.php" method="post" autocomplete="off" >
				<input type="hidden" name="id" value="<?=$announce['id'] ?>" />
				<ul>
					<li><label for="title">标题：&nbsp;&nbsp;</label><input type="text" name="title" id="title" value="<?=$announce['title'] ?>" /></li>
					<li><label for="content">内容：&nbsp;&nbsp;</label><textarea type="text" name="content" id="content" cols="50" rows="5"><?=br2nl($announce['content']) ?></textarea></li>
					<li><label for="priority">级别：&nbsp;&nbsp;</label>
						<select name="priority" id="priority">
							<option value="1" <?php if($announce['priority']==1) echo 'selected' ?>>-- 非常重要 --</option>
							<option value="2" <?php if($announce['priority']==2) echo 'selected' ?>>----- 重要 -----</option>
							<option value="3" <?php if($announce['priority']==3) echo 'selected' ?>> ----- 一般 -----</option>
							<option value="4" <?php if($announce['priority']==4) echo 'selected' ?>>---- 不重要 ----</option>
							<option value="5" <?php if($announce['priority']==5) echo 'selected' ?>>----- 垃圾 -----</option>
						</select>
					</li>
					<li><label for="public">是否公开：&nbsp;&nbsp;</label>
						<select name="public" id="public">
							<option value="1" <?php if($announce['public']) echo 'selected' ?>>---- 是 ----</option>
							<option value="0" <?php if(!$announce['public']) echo 'selected' ?>>---- 否 ----</option>
						</select>
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