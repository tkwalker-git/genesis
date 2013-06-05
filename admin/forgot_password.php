<?php 
require_once("database.php");

if( isset($_POST['txtEmail']) && isset($_POST['txtCode']) ) {
	$txtEmail = DBin($_POST['txtEmail']);
	if( $_SESSION['captchaValue'] == md5($_POST['txtCode']) ) {
		
		$q = mysql_query("select email from bc_admin where email='$txtEmail' limit 1");
		if( mysql_num_rows($q) == 1 ) {
			
			$mkNewPass = rand(9999, 999999);
			mysql_query("update bc_admin set password='$mkNewPass' where email='$txtEmail' limit 1");
			
			$To 	 = $txtEmail;
			$From 	 = ADMIN_EMAIL;
			$Subject = 'Forgot Password';
			$Body 	 = 'Hi,';
			$Body	.= '<br /><br />Your new password is: '.$mkNewPass;
			$Body	.= '<br /><br />Please chane your password after login. ';
			$Body	.= '<br /><br />Thanks';
			
			sendMail($To, $From, $Subject, $Body);
			$msg = 'Password has been sent...';
		}
		else
			$msg = 'Email Address Not Found';
	}
	else
		$msg = 'Invalid Security Code';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Admin Panel | Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="css/login.css" />
</head>
<body>

<table cellpadding="0" cellspacing="0" align="center" id="tbl">
	<tr>
    	<td>
        	<div id="logo"><img src="<?=ADMIN_LOGO?>" align="absmiddle" /></div>
            <div id="login">Forgot Password</div>
            <div id="box">
            	<div class="error"><?=$msg?></div>
            	<form action="<?=ABSOLUTE_PATH?>admin/forgot_password.php" method="post">
            	<p>
                    <label>Email Address</label>
                    <input type="text" name="txtEmail" value="" class="input" />
                </p>
                <p>
                    <label>Security Code</label>
                    <input type="text" name="txtCode" value="" class="input" style="width:200px;" />
                    <img src="<?=ABSOLUTE_PATH?>admin/captcha.php" />
                </p>
                <div class="hr">&nbsp;</div>
                <div class="btn">
                    <div class="btn_l"><a href="<?=ABSOLUTE_PATH?>admin/login.php">Login</a></div>
                    <div class="btn_r"><input type="image" src="images/forgot_password.png" border="0" align="absmiddle" /></div>
                </div>
            	</form>
            </div>
            <!--div id="info">
            	<div class="info_img"><img src="images/info.png" /></div>
            	<div class="info_txt">If you are an admin of this site then please input the valid credentials to login to the dashboard of the site to manage content,
                otherwise <a href="#">contact system administrator</a> for further information.</div>
            </div-->
        </td>
    </tr>
    <tr><td height="85"></td></tr>
</table>

<div id="footer">
	System Powered by <a href="http://www.pethersolutions.com" target="_blank" class="powered">Pether Solutions</a><br />
	<a href="#">Home</a> |
    <a href="#">Contact Us</a> |
    <a href="#">Privacy Policy</a> |
    <a href="#">Legal</a>
</div>

</body>
</html>
