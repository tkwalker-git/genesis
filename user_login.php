<?php 
	require_once('admin/database.php'); 
	$page	= 'login';
	$loginPageUrl	=	"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	if($_SERVER['HTTP_REFERER']!=$loginPageUrl){
	$_SESSION['REFERER'] = $_SERVER['HTTP_REFERER'];
	}
	
	if ( isset ($_GET['rUrl']) )
		$_SESSION['REDIRECT_URL'] = urldecode($_GET['rUrl']);
	
	if(!isset($_SESSION['logedin'])){
		$_SESSION['logedin'] = '';
		$_SESSION['LOGGEDIN_MEMBER_ID'] = '';
		$_SESSION['LOGGEDIN_MEMBER_TYPE'] = '';
	}
	
	if ( $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 )
		echo "<script>window.location.href='dashboard.php';</script>";
	
	include_once('includes/patient-header.php'); 
	
	if (isset($_POST['email'])){
		
		$errors = "";
		$email = $_POST['email'];
		$pass = $_POST['password'];
		
		 $emailChk_q	= "select id, email, password, email from patients where email = '$email' and password = '$pass'";
		$emailChk_q2 = "select id, email, password, email from patients where email = '$email' and password = '$pass'";

		  $email_res = mysql_query($emailChk_q);
		  $email_res2 = mysql_query($emailChk_q2);
		
		if($email_r1 = mysql_fetch_assoc($email_res)){
		
		$thisUserId = $email_r1['id'];
		
		$rs = mysql_query("select * from `patients` where `id`='$thisUserId'");
		while($ro = mysql_fetch_array($rs)){
		 $status = $ro['enabled'];
		}
		
		if($status!=0){	
			$_SESSION['logedin'] = '1';
			$_SESSION['LOGGEDIN_MEMBER_ID'] = $email_r1['id'];
			$_SESSION['usertype']	= 'patient';
		
			if ( $_SESSION['REDIRECT_URL'] != '' )
				echo "<script>window.location.href='". urldecode($_SESSION['REDIRECT_URL']) ."';</script>";
			else
				echo "<script>window.location.href='".$_SESSION['REFERER']."';</script>";
		}
		else{
			$errors = "Your Account is not Active";
		}
				
		}elseif($email_r2 = mysql_fetch_assoc($email_res2)){
			$errors = "Your Account is not Active";
		}else{
			
			$errors = "Invalid email or password  &nbsp; &nbsp;<b>OR </b>&nbsp; &nbsp;  Are you Doctor &nbsp; <b><a href='login.php'>Login Here</a></b>";		
		}
		if ( count( $errors) > 0 ) {
			
				$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
				//for ($i=0;$i<count($errors); $i++) {
					$err .= '<li>' . $errors . '</li>';
				//}
				$err .= '</ul></td></tr></table>';	
		}	
	}	
?>
<style>
	.addEInput {
		width:225px!important;
		height:30px!important;
	}
	
	.required{
		font-size:14px;
		padding-left:3px;
	}		
</style>
<script>
 $(document).ready(function(){
	 $('#email').focus();
 });
</script>
    <div id="main">
      <div id="login-main">
        <div id="login-main-top"></div>
        <div id="login-main-middle">
          <div id="login-head">
            <div id="heading" style="width:350px">Patient Login</div>
            <div id="already-member"><span><a href="login.php"> Click Here to login as a Doctor </a></span></div>
          </div>
          <!--login-head -->
          <div id="shadow"></div>
          <div class="error"><?php echo $err; ?></div>
         <!-- <p style="text-align:center;"> 
            
            <?php if(!isset($_SESSION['logedin']) || $_SESSION['logedin']!=1 || $_SESSION['LOGGEDIN_MEMBER_ID'] == 0 ){ ?>
            <a href="fblogin.php"><img src="<?php echo IMAGE_PATH;?>facebook-login-button.png" /></a>&nbsp; &nbsp; &nbsp; <a href="twitter/connect.php"><img src="<?php echo IMAGE_PATH;?>twitter_login_button.png" /></a> 
            <? }?>

          </p> -->
          <form action="user_login.php" method="post" name="loginform" id="loginform">
            <!-- <input type="hidden" value="<?php //echo $ref;?>" name="ref" id="ref"> -->
            <div id="form" style="width:348px">
              <div class="input-row">
                <div class="input-label">Email Address</div>
                <div class="input-field">
                  <input type="text" name="email" id="email" class="required" maxlength="40"/>
                </div>
              </div>
              <!-- input-row -->
              <div class="input-row">
                <div class="input-label">Password</div>
                <div class="input-field">
                  <input type="password" name="password" id="password" class="required" maxlength="20"/>
                </div>
              </div>
              <!-- input-row -->
              <div class="input-row">
                <div class="forgot-passwd"><span><a href="forgot_password.php">Forgot Password?</a></span></div>
                <div style="font-weight:bold; color:#FF0000;"></div>
              </div>
              <!-- input-row -->
              <div class="form-submit">
                <input type="hidden" name="action_new" value="Login" />
                <div class="submit-btn">
                  <input name="continue1" id="continue1" type="image" src="images/login-button.png" vspace="5" onclick="return getLogin();"/>
                </div>
              </div>
            </div>
            <!-- form -->
			<div style="background: none repeat scroll 0 0 #F5F5F5; border: 1px solid #CCCCCC; color: red; float: left; font-size: 12px; line-height: 17px; margin-top: 42px; padding: 10px; width: 190px;"><strong>Important:</strong> Please use your Email Address for login. Your Password will remain the same.</div>
          </form>
        </div>
        <!-- login-main-middle-->
        <div id="login-main-bottom"></div>
      </div>
      <!-- login-main --> 
    </div>
    <div class="clr"></div>
<?php include_once('includes/footer.php'); ?>
