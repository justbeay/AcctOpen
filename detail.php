<?php
define('PAGENAME', 'detail');

require_once('./funcs/database.php');

if(!auth_login()){
	redirect('login.php');
}elseif(get_input('id') && !get_account_permission(get_input('id'), 'view')){
	pop_message('抱歉，账目不存在或无相应权限浏览该账目！', 'list.php');
}

include('./include/detail.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-详情</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
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
	</script>
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>账务详情</h3>
			<form action="alter.php" method="get">
				<input type="hidden" name="id" value="<?=$acct['id'] ?>"/>
				<ul style="list-style:squar	e">
					<li class="left singleline"><div class="left">时间：</div><div class="left"><?=$acct['time'].' '.trans_time_apm($acct['time_apm']) ?></div></li>
					<li class="left singleline"><div class="left">地点：</div><div class="left"><?=$acct['place'] ?></div></li>
					<li class="left singleline"><div class="left">金额：</div><div class="left"><?=$acct['amount'] ?></div></li>
					<li class="left singleline"><div class="left">付款人：</div><div class="left"><?=$acct['payer'] ?></div></li>
					<li class="left singleline"><div class="left">受益方：</div><div class="left"><?=$acct['beneficiary'] ?></div></li>
					<li class="left singleline"><div class="left">详情：</div><div class="left"><?=$acct['content'] ?></div></li>
					<li class="left singleline"><div class="left">备注：</div><div class="left"><?=$acct['note'] ?></div></li>
				</ul>
				<div class="left singleline">
				<?php if(get_account_permission(get_input('id'), 'alter')){ ?>
					<input type="submit" value="修改" style="width:80;height:26px;background-color:#EAAC6E;"/>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } ?>
				<?php if(get_account_permission(get_input('id'), 'delete')){ ?>
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