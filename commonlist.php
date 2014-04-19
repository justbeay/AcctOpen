<?php define('PAGENAME', 'add');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(!auth_login()) redirect('login.php');
include('./include/commonlist.inc.php');
?>
<head>
	<meta charset="utf-8" />
	<base target="_self">
	<title>账务开放平台-常用联系人列表</title>
	<script language="javascript">
		function append(){
			var retStr = '';
			var inputs = document.getElementsByTagName("input");
			for(var i=0; i<inputs.length; i++){
				if(inputs[i].type=="checkbox" && inputs[i].checked){
					retStr += inputs[i].value + ',';
				}
			}
			if(retStr!='' && retStr[retStr.length-1]==','){
				retStr = retStr.substr(0, retStr.length-1);
			}
			var newuser = document.getElementById("nusername").value.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
			if(newuser != ''){
				retStr += ',' + newuser;
			}
			window.returnValue = retStr;
			window.close();
		}
		function del(name){
			location.href = "commonlist.php?oper=del&name=" + name;
		}
	</script>
</head>
<body>
	<h4>常用联系人列表：</h4>
	<?php
	if($userlist){
		foreach($userlist as $username){ 
	?>
		<input type="checkbox" name="user1" value="<?=$username ?>" /><?=$username ?>&nbsp;&nbsp;
		<a href="javascript:del('<?=$username ?>')"><img width="16px" height="16px" src="./images/delete.png" alt="remove from address book" style="border:0px"/></a><br/>
	<?php 
		}
	}
	?>
	Can't find? type it here: <input type="text" name="nusername" id="nusername" /><br/><br/>
	<input type="button" onclick="append()" value="确定" />
	<input type="button" onclick="window.close()" value="取消" />
</body>
