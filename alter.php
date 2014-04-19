<?php
define('PAGENAME', 'alter');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(!auth_login()){
	redirect('login.php');
}elseif(!get_account_permission(get_input('id'), 'alter')){
	pop_message('禁止修改非本人提交或付款账目！', 'list.php');
}
$submit = get_input('submit', true);
if($submit){
	include('./include/alter.inc.php');
}else{
	include('./include/detail.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-修改</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script language="javascript" type="text/javascript">
		function openNewWindow(url){
			//window.open (url, 'newwindow', 'height=300, width=500, top=0,left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no');
			var ret = window.showModalDialog(url,null,"dialogWidth:350px;dialogHeight:350px;help:no;status:no");
			if(ret.length > 0){
				var ele = document.form1.beneficiary;
				if(ele.value.length > 0){
					var ch = ele.value[ele.value.length-1];
					if(ch!=',' && ch!=';' && ch!='，' && ch!='；' && ch!='、'){
						ele.value += ',';
					}
				}
				ele.value += ret;
			}
		}
		function doReturn(){
			location.href = 'detail.php?id=<?=$acct['id'] ?>';
		}
	</script>
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>账务详情</h3>
			<form name="form1" action="" method="post">
				<input type="hidden" name="id" value="<?=$acct['id'] ?>"/>
				<ul style="list-style:squar	e">
					<li class="left singleline"><div class="left">时间：</div><div class="left"><?=$acct['time'].' '.trans_time_apm($acct['time_apm']) ?></div></li>
					<li class="left singleline"><div class="left">地点：</div><div class="left"><?=$acct['place'] ?></div></li>
					<li class="left singleline"><div class="left">金额：</div>
						<div class="left"><input type="text" name="amount" id="amount" value="<?=$acct['amount'] ?>" /></div>
					</li>
					<li class="left singleline"><div class="left">付款人：</div><div class="left"><?=$acct['payer'] ?></div></li>
					<li class="left singleline"><div class="left">受益方：</div>
						<div class="left"><input type="text" name="beneficiary" id="beneficiary" value="<?=$acct['beneficiary'] ?>" /></div>
						<a href="javascript:openNewWindow('commonlist.php')">添加常用联系人</a>
					</li>
					<li class="left singleline"><div class="left">详情：</div>
						<div class="left"><textarea type="text" name="detail" id="detail" cols="30" rows="3"><?=br2nl($acct['content']) ?></textarea></div>
					</li>
					<li class="left singleline"><div class="left">备注：</div>
						<div class="left"><textarea type="text" name="note" id="note" cols="30" rows="3"><?=br2nl($acct['note']) ?></textarea></div>
					</li>
				</ul>
				<div class="left singleline">
					<input type="submit" name="submit" value="确定" onclick="return check();" style="width:80;height:26px;background-color:#EAAC6E;"/>&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="cancel" value="取消" style="width:80;height:26px;background-color:#EAAC6E;" onclick="doReturn()"/>
				</div>
			</form>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
	<script language="javascript">
		function checkAmount(){
			var amount = document.form1.amount.value;
			var pattern = /^((0|([1-9]\d*)*)(.\d+)?)$/;
			if(amount=='' || !pattern.test(amount)){
				alert('请输入正确的金额');
				return false;
			}
			return true;
		}
		function check(){
			return !(!checkAmount() || document.form1.beneficiary.value=='');
		}
	</script>
</body>
</html>
<?php 
}
?>