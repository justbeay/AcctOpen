<?php
define('PAGENAME', 'login');

require_once('./funcs/common.php');
require_once('./funcs/database.php');

if(auth_login()) redirect('index.php');

$submit = get_input('submit', true);
if($submit){
	include('./include/login.inc.php');
}else{
	$username = get_cookie('login');
	$remember_me = get_cookie('remember_me');
?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>User Login</title>
  <link rel="stylesheet" href="css/style_login.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
  <div class="container">
    <div class="login">
      <h1>User Login</h1>
      <form method="post" action="login.php">
        <p><input type="text" name="login" value="<?=$username ?>" placeholder="Username"></p>
        <p><input type="password" name="password" value="" placeholder="Password"></p>
        <p class="remember_me">
          <label>
            <input type="checkbox" name="remember_me" id="remember_me"<?php if($remember_me) echo 'checked'; ?>>
            Remember me on this computer
          </label>
        </p>
        <p class="submit"><input type="submit" name="submit" value="Login"></p>
      </form>
    </div>

    <div class="login-help">
      <p>Want to change your password? <a href="resetPwd.php">Click here to reset it</a>.</p>
    </div>
  </div>
</body>
</html>
<?php
}
?>