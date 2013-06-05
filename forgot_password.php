<?php

require_once('includes/header.php');

if ($_SESSION['LOGGEDIN_MEMBER_ID'] > 0)
		echo "<script>window.location.href='dashboard.php';</script>";

function genRandomString() {
    $length = 6;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = "";    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}


if (isset($_POST['email'])){
	//$errors = "";
	$email = DBin($_POST['email']);
	if(!strstr($email,"@"))
		$username = $email;
	else
		$username='';
		
	if($username == ''){
		if($email == ''){
				$errors[] = 'Please Enter Username or Email to Reset Password.';
		}elseif ( !validateEmail($email)  ){
				$errors[] = 'Please Enter a Valid Email.';
		}
	}
	
	
	if(count($errors) == 0){
		if($username != ''){
			$emailChk_q = "select id, username, email from users where username = '$username' and enabled = '1'";
		}else{
			$emailChk_q = "select id, username, email from users where email = '$email' and enabled = '1'";
		}	
		
			if($email_res = mysql_query($emailChk_q))
				$tot_rec = mysql_num_rows($email_res) ;
			if($tot_rec > 0){
				$user_r1 = mysql_fetch_assoc($email_res);
				$emailDb = $user_r1['email'];	
				
				$newpassword = genRandomString();
				
				$set_passwd = "update users set password = '$newpassword' where email = '$emailDb'";		
				mysql_query($set_passwd);
				
				$sender_name = "Pangea";
				$sender_email = "info@eventgrabber.com";
				$subject	= "Your New Pangea Password";
				$html		= "<p>Your password has been successfully reset.  Your new password is:</p><p>".$newpassword."</p><p>Please login to your account and change your password by clicking on Settings and then Profile.<p>Thanks<br>". ABSOLUTE_PATH ."</p>";
				$headers  = 'MIME-Version: 1.0' . "\r\n";
 				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 				$headers .= 'From: '.$sender_name.'<'. $sender_email .'>' . "\r\n";
				
				if(mail($emailDb,$subject,$html,$headers)){
					$message = "Success! Your password has been reset. Please check your email";
				}else{
					$errors[] = "Your request can not be completed right now, Please try Later";
				}
									
					
			}else{
				$errors[] = "Wrong Username or Email Id ";		
			}
	}	
	if ( count($errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
	}
	
}


?>
<style>

	.addEInput
	{
		width:225px!important;
		height:30px!important;
	}
	
	.error
	{
		text-align:left;
		float:left;
		width:100%;
	}
	
	.error ul
	{
		border:#CC8968 solid 1px;
		background-color:#FFFFCC;
		padding:10px;
		background-image:url(images/error.png);
		background-repeat:no-repeat;
		background-position:5px 5px;
		padding-top:40px;
		padding-left:10px;
	}
	
	.error ul li
	{
		margin-left:40px;
		color:#990000;
		font-family:Arial, Helvetica, sans-serif;
		font-size:12px;
	}
.success{
	font-size:14px;
	font-weight:bold;
	color:#CC9900;
	float:left;
	margin-left:20px;
}
</style>

<div id="main">
<div id="login-main">
				
					<div id="login-main-top"></div>
						<div id="login-main-middle">
							<div id="login-head">
								<div id="heading">Reset Password</div>
								
							</div><!--login-head -->
							<div id="shadow"></div>
							<!--<div id="fb-root"></div>-->
							<div class="success"><?php echo $message; ?></div>
							<div class="error"><?php echo $err; ?></div>
							
														
							<form action="" method="post" name="loginform" id="loginform">
								<!-- <input type="hidden" value="<?php //echo $ref;?>" name="ref" id="ref"> -->
								<div id="form">
									<div class="input-row">
										<div class="input-label">Please Enter Your Email Address</div>
										<div class="input-field"><input type="text" name="email" id="email" class="required" style="width:500px;" /></div>					
									</div><!-- input-row -->
									
									
									
									<div class="form-submit">
										<input type="hidden" name="action_new" value="Login" />
										<div class="submit-btn">
											<input name="continue1" id="continue1" type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" vspace="5"/>
										</div>
									</div>
										
								</div><!-- form -->
								</form>
							</div><!-- login-main-middle-->
					<div id="login-main-bottom"></div>	
				
			</div><!-- login-main -->
			<div class="clr"></div>
</div>	



<?php require_once('includes/footer.php'); ?>
