<?php
define('PAGENAME', 'list');

require_once('./funcs/database.php');

if(!auth_login()) redirect('login.php');

include('./include/list.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-账务列表</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link type="text/css" rel="stylesheet" href="css/table.css" />
	<link href="css/calendar.css" type="text/css" rel="stylesheet"/>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script src="js/CalendarPopup.js"></script>
	<script language="javascript">
		function search(){
			alert('s');
		}
		var cal = new CalendarPopup("calendarDiv");
		cal.showNavigationDropdowns();
	</script>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<div class="searchdiv">
				<h4>条件检索：</h4>
				<form name="form1" action="list.php" method="get">
					<label for="amount">金额:</label>
					<select name="oper">
						<option value="1" <?php if($operFlag==1) echo 'selected'; ?>>大于</option>
						<option value="2" <?php if(!$operFlag || $operFlag==2) echo 'selected'; ?>>等于</option>
						<option value="3" <?php if($operFlag==3) echo 'selected'; ?>>小于</option>
					</select>
					<input type="text" name="amount" id="amount" size="12" value="<?php if($amount) echo $amount; ?>" />
					<label for="payer">付款人:</label>
					<input type="text" name="payer" id="payer" value="<?php if($payer) echo $payer; ?>" />
					<label for="beneficiary">受益人:</label>
					<input type="text" name="beneficiary" id="beneficiary" value="<?php if($beneficiary) echo $beneficiary; ?>" />
					<label for="time_begin">时间:</label>
					<input type="text" name="time_begin" id="time_begin" size="8" value="<?php if($time_begin) echo $time_begin; ?>" />
					<a href="#" onclick="cal.select(document.form1.time_begin,'anchor1','yyyy-MM-dd'); return false;">
						<img style="border-width:0" src="images/datepicker.png" width="20" height="20" name="anchor1" id="anchor1" />
					</a>至
					<input type="text" name="time_end" size="8" value="<?php if($time_end) echo $time_end; ?>" />
					<a href="#" onclick="cal.select(document.form1.time_end,'anchor2','yyyy-MM-dd'); return false;">
						<img style="border-width:0" src="images/datepicker.png" width="20" height="20" name="anchor2" id="anchor2" />
					</a><br/>
					<label for="content">内容:</label>
					<input type="text" name="content" id="content" size="30" value="<?php if($content) echo $content; ?>" />
					<label for="note">备注:</label>
					<input type="text" name="note" id="note" size="30" value="<?php if($note) echo $note; ?>" />
					<input type="submit" value="搜索" style="width:72px;margin-left:30px;" />
				</form>
				<div id="calendarDiv" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
			</div>
			<table class="diytable" width=100% cellspacing="0" cellpadding="0">
				<tr>
					<th width="8%">编号</th>
					<th width="15%">时间</th>
					<th width="50%">内容</th>
					<th width="12%">金额</th>
					<th width="15%">付款人</th>
				</tr>
				<?php foreach($acct_list as $acct){ ?>
				<tr>
					<td><a href='detail.php?id=<?=$acct['id'] ?>'><?=$acct['index'] ?></a></td>
					<td><?=$acct['time_display'] ?></td>
					<td><?php if($acct['content']){ ?><a href='detail.php?id=<?=$acct['id'] ?>'><?=$acct['content'] ?></a><?php } ?></td>
					<td><?=$acct['amount'] ?></td>
					<td><?=$acct['payer'] ?></td>
				</tr>
				<?php } ?>
			</table>
			<?php
				$requestUrl = get_session('searchtxt');
				$requestUrl = preg_replace("/([?&])pageno=\d+[?&]?/", "", $requestUrl);
				if($requestUrl) $requestUrl = 'list.php?'.$requestUrl.'&pageno=';
				else  $requestUrl = 'list.php?pageno=';
			?>
			<div class="pagingbar">
				<a href="<?=$requestUrl ?>1">首页</a>
				<a <?php if($pageno>1){ echo 'href="'.$requestUrl.($pageno-1).'"'; } ?>>上一页</a>
				<a <?php if($pageno<$totalpage){ echo 'href="'.$requestUrl.($pageno+1).'"'; } ?>>下一页</a>
				<a href="<?=$requestUrl.$totalpage ?>">尾页</a>
			</div>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
</body>
</html>