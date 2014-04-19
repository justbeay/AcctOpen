<?php

require_once('./funcs/common.php');
require_once('./funcs/database.php');
require_once('./funcs/session.php');

require_once('./classes/class.phpmailer.php');
require_once('./funcs/config.php');

try {
	$code = get_input('code');
	$login = addslashes(get_input('login'));
	if(md5(strtolower($code)) != get_session('code')){
		pop_message('验证码输入有误！', 'findPwd.php');
	}
	$uid = get_user_id($login, get_db_connection());
	if($uid <= 0) pop_message('该用户不存在！');	
	$user = get_user_info($uid);
	if(empty($user['email'])){
		pop_message('您暂未设置邮箱，请联系管理员修改邮箱信息！');
	}
	$session = get_db_session($uid, 2);
	/*if($session && time()-$session['update_time'] < 1800){
		pop_message('您发送邮件过于频繁，请稍后再试！');
	}*/
	
	$authstr = $session ? update_db_session($uid, 2) : insert_db_session($uid, 2);
	$authstr = randomkeys(4).$uid.'a'.$authstr;
	$resetUrl = "http://acctopen.duapp.com/resetPwd.php?authstr=$authstr";
	$subject = "Reset your password on acctopen.duapp.com";
	$email_content = "<h3>Reset your password on acctopen.duapp.com</h3>
		<p>
			&nbsp;&nbsp;This email was sent by system automatically, please do not reply!<br/>
			&nbsp;&nbsp;Click <a href='$resetUrl'>here</a> to reset your password.<br/>
			&nbsp;&nbsp;If your couldn't open the link above, just copy the link below to your browser:<br>
			&nbsp;&nbsp;$resetUrl<br/><br/>
			&nbsp;&nbsp;if you receieve it by accident, just regard of it, we do a deeply apologize for bothering you!
		</p><hr/>
		<p>Account Open platform (http://acctopen.duapp.com)</p>";
	global $EMAIL_CONFIG;
	$mail = new PHPMailer(true); //New instance, with exceptions enabled

	$body             = $email_content;
	$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

	$mail->IsSMTP();                           // tell the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Port       = 25;                    // set the SMTP server port
	$mail->Host       = $EMAIL_CONFIG['host']; // SMTP server
	$mail->Username   = $EMAIL_CONFIG['username'];     // SMTP server username
	$mail->Password   = $EMAIL_CONFIG['password'];            // SMTP server password

	//$mail->IsSendmail();  // tell the class to use Sendmail

	$mail->AddReplyTo($EMAIL_CONFIG['username'],"First Last");

	$mail->From       = $EMAIL_CONFIG['username'];
	$mail->FromName   = "acctopen.duapp.com";

	$to = $user['email'];

	$mail->AddAddress($to);

	$mail->Subject  = "=?UTF-8?B?".base64_encode($subject)."?=";

	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	$mail->WordWrap   = 80; // set word wrap

	$mail->MsgHTML($body);

	$mail->IsHTML(true); // send as HTML
	$mail->Charset    = 'UTF-8';

	$mail->Send();
	pop_message('邮件发送成功，请登录邮箱查看。', 'login.php');
} catch (phpmailerException $e) {
	echo $e->errorMessage();
}
?>