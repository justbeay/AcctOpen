<?php
define('PAGENAME', 'annDetail');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(!get_announcement_permission(get_input('id'), 'view')){
	pop_message('抱歉，公告不存在或无相应权限浏览该公告！', 'annList.php');
}
include('./include/annDetail.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-公告详情</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script language="javascript" type="text/javascript">
		function doDelete(){
			if(!confirm("你确定要删除该公告吗（不可恢复）？")) return false;
			document.forms[0].action = 'annDelete.php';
			return true;
		}
		function doReturn(){
			location.href = 'annList.php';
		}
	</script>
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>公告详情</h3>
			<form action="annAlter.php" method="get">
				<input type="hidden" name="id" value="<?=$announce['id'] ?>"/>
				<div style="padding:5px 5px; margin:10px auto; border:dashed 1px #842E21">
					<h3 style="text-align:center"><?=$announce['title'] ?></h3><br/>
					<p ><?=$announce['content'] ?></p>
				</div>
				<label>浏览次数：</label><?=$announce['count'] ?><br/>
				<label>最后编辑时间：</label><?=$announce['time_update'] ?>
				<div>
				<?php if(get_announcement_permission(get_input('id'), 'alter')){ ?>
					<input type="submit" value="修改" style="width:80;height:26px;background-color:#EAAC6E;"/>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if(get_announcement_permission(get_input('id'), 'delete')){ ?>
					<input type="submit" value="删除" style="width:80;height:26px;background-color:#EAAC6E;" onclick="return doDelete()"/>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
					<input type="button" value="返回" style="width:80;height:26px;background-color:#EAAC6E;" onclick="doReturn()"/>
				</div>
			</form>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
</body>
</html>