<?php
define('PAGENAME', 'annList');

include('./include/annList.inc.php');
?>
<html>
<head>
	<meta charset="utf-8" />
	<title>账务开放平台-公告列表</title>
	<link type="text/css" rel="stylesheet" href="css/base.css" />
	<link type="text/css" rel="stylesheet" href="css/table.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
</head>
<body>
	<div class="main">
		<?php include('./include/header.inc.php'); ?>
		<div class="mainbody">
			<table class="diytable" width=100% cellspacing="0" cellpadding="0">
				<tr>
					<th width="10%">编号</th>
					<th width="20%">更新时间</th>
					<th width="50%">标题</th>
					<th width="10%">访问次数</th>
				</tr>
				<?php foreach($announce_list as $announce){ ?>
				<tr>
					<td><a href='annDetail.php?id=<?=$announce['id'] ?>'><?=$announce['index'] ?></a></td>
					<td><?=$announce['time_update'] ?></td>
					<td><a href='annDetail.php?id=<?=$announce['id'] ?>'><?=simplify_str($announce['title'], 48) ?></a></td>
					<td><?=$announce['count'] ?></td>
				</tr>
				<?php } ?>
			</table>
			<div class="pagingbar">
				<a href="annList.php?pageno=1">首页</a>
				<a <?php if($pageno>1){ echo 'href="annList.php?pageno=' . ($pageno-1) . '"'; } ?>>上一页</a>
				<a <?php if($pageno<$totalpage){ echo 'href="annList.php?pageno=' . ($pageno+1) . '"'; } ?>>下一页</a>
				<a href="annList.php?pageno=<?=$totalpage ?>">尾页</a>
			</div>
		</div>
		<?php include('./include/footer.inc.php'); ?>
	</div>
</body>
</html>