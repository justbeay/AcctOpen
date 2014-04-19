<?php
define('PAGENAME', 'add');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(!auth_login()) redirect('login.php');
$login_role = get_login_role();
if($login_role<1 || $login_role>2) pop_message("抱歉，您无权限进行该操作！", 'list.php');

$submit = get_input('submit', true);
if($submit){
	include('./include/add.inc.php');
}else{
	$time = date('Y-m-d');
	$time_apm = get_time_apm(date('G'));
	$payer = get_login_name();
	$beneficiary = get_login_name();
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-添加</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link href="css/calendar.css" type="text/css" rel="stylesheet"/>
	<script src="js/CalendarPopup.js"></script>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script language="javascript">
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
		var cal = new CalendarPopup("calendarDiv");
		cal.showNavigationDropdowns();
	</script>
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<h3>账务录入</h3>
			<form name="form1" action="add.php" method="post" >
				<ul>
					<li>
						<label for="time">时间：&nbsp;&nbsp;</label>
						<input type="text" name="time" id="time" value="<?=$time?>" />
						<a href="#" onclick="cal.select(document.form1.time,'anchor','yyyy-MM-dd'); return false;">
							<img style="border-width:0" src="images/datepicker.png" width="20" height="20" name="anchor" id="anchor" />
						</a>
						<select name="time_apm" id="time_apm">
							<option value="morning" <?php if($time_apm==1) echo 'selected'?>>早晨</option>
							<option value="am" <?php if($time_apm==2) echo 'selected'?>>上午</option>
							<option value="noon" <?php if($time_apm==3) echo 'selected'?>>中午</option>
							<option value="pm" <?php if($time_apm==4) echo 'selected'?>>下午</option>
							<option value="night" <?php if($time_apm==5) echo 'selected'?>>晚上</option>
						</select>&nbsp;&nbsp;*
					</li>
					<li><label for="place">地点：&nbsp;&nbsp;</label><input type="text" name="place" id="place" /></li>
					<li><label for="amount">金额：&nbsp;&nbsp;</label><input type="text" name="amount" id="amount" />&nbsp;&nbsp;*</li>
					<li><label for="payer">付款人：</label><input type="text" name="payer" id="payer" value="<?=$payer ?>" />&nbsp;&nbsp;*</li>
					<li><label for="beneficiary">受益方：</label>
						<input type="text" name="beneficiary" id="beneficiary" value="<?=$beneficiary ?>" />&nbsp;&nbsp;*（姓名之间以 , 隔开）
						<a href="javascript:openNewWindow('commonlist.php')">添加常用联系人</a>
					</li>
					<li><label for="detail">详情：&nbsp;&nbsp;</label><textarea type="text" name="detail" id="detail" cols="30" rows="3"></textarea></li>
					<li><label for="note">备注：&nbsp;&nbsp;</label><textarea type="text" name="note" id="note" cols="30" rows="3"></textarea></li>
					<li>
						<input type="submit" class="button" name="submit" value="提交" onclick="return check();"/>
						<input type="reset" class="button" name="reset" value="重填"/>
					</li>
				</ul>
			</form>
			<div id="calendarDiv" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
	<script language="javascript">
		function checkDate(){
			var time = document.form1.time.value;
			var pattern = /^(\d{4})[-\/,\.]?(\d{1,2})[-\/,\.]?(\d{1,2})$/;
			if(time=='' || !pattern.test(time)){
				alert('请输入正确的日期');
				return false;
			}
			return true;
		}
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
			return !(!checkDate() || !checkAmount() || document.form1.payer.value==''
				|| document.form1.beneficiary.value=='');
		}
	</script>
</body>
</html>
<?php 
}
?>