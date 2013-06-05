<?php	
include_once('geomap.php');
include_once('includes/header.php'); 
include_once('site_functions.php'); 

if ($_SESSION['LOGGEDIN_MEMBER_ID']!='')
		echo "<script>window.location.href='dashboard.php';</script>";
		
		
		
if ( isset ($_GET['ref']) ) {
		$ref_member = base64_decode($_GET['ref']);
}
function assignDayTime($member_id)
{
		// FOR NEW MEMBERS	
		$sql = "SELECT week_day, day_time,count(*) as cu FROM `users` group by week_day, day_time order by cu ASC LIMIT 1";
		$res = mysql_query($sql);
		if ( $row = mysql_query($res) ) {
			$update = "update users set week_day='". $row['week_day'] ."', day_time='". $row['day_time'] ."' where id='". $member_id ."' ";
			mysql_query($update);
		}
}	
function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

if ($_REQUEST['signed_request']) {
  $response = parse_signed_request($_REQUEST['signed_request'],FACEBOOK_SECRET2);
  	
	if ( isset($response['registration'])) {
			$fname 			= DBin($response['registration']['first_name']);
			$lname 			= DBin($response['registration']['last_name']);
			$email 			= DBin($response['registration']['email']);
			
           
			$password 		= DBin($response['registration']['password']);
            $zip 		    = DBin($response['registration']['zip']);
            $gender  	    = DBin($response['registration']['gender']);
            
            $facebookid     = DBin($response['user_id']);
            $dob            = DBin(date("Y-m-d",strtotime($response['registration']['birthday'])));
			$city           = DBin($response['registration']['location']['name']);
            $usertype 		= DBin($response['registration']['usertype']);
			$agree	 		= DBin($_POST['agree']);
			$memberdate 	= date("Y-m-d");
			$ref_member		= $response['registration']['ref_member'];
			
			//$cpassword 	    = DBin($_POST['cpassword']);

	
		
		$Chk_email_q = "select email from users where email = '$email'";
		
		
		$user_res2 = mysql_query($Chk_email_q);
		if($user_r2 = mysql_fetch_assoc($user_res2)){
				$emailDb = trim($user_r2['email']);
		}
		
		
		if ( !validateEmail($email) ) {
			$errors[] = 'Email Address is invalid.';
		} elseif($email == $emailDb) {
			$errors[] = 'Email Address Already Exist.';
		}
			
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			$varifactionCode = session_id();
			
			if($usertype == "1"){
				$user_type = '2';
			}else{
				$user_type = '1';
			}
			
			$name = $fname;
			$user_insert = "insert into users (firstname,lastname, email, password,zip,city,sex,dob,facebookid, varifaction_code,createddate, usertype,email_verify,status) 
							values ('$name','$lname', '$email', '$password',$zip,$city,$gender,$dob,$facebookid, '$varifactionCode', '$memberdate', '$user_type','1','1') ";
			//d($user_insert,1);
			if ( mysql_query($user_insert) ) {
			
				/*$varification_url = ABSOLUTE_PATH."varify.php?code=/".$varifactionCode;
				$msg = "Welcome <br> Your account has been created on Eventgrabber <br> Click the following url to activate your account <br>".$varification_url; */
				
				$_SESSION['insertid'] = mysql_insert_id();
				assignDayTime($_SESSION['insertid']);
				// keep track of referral member
				if ($_SESSION['insertid'] > 0 && $ref_member > 0) {
				
//	$sql_ref = "insert into member_referals (ref_member_id,member_id) VALUES ('". $ref_member ."','". $_SESSION['insertid'] ."')";
				$sql_ref = "insert into member_referals (ref_member_id,member_id,datetime) VALUES ('". $ref_member ."','". $_SESSION['insertid'] ."','". date('Y-m-d H:i:s') ."')";

					mysql_query($sql_ref);
				}
				
				$_SESSION['page_ref'] = 'signup.php';
				
				$_SESSION['logedin'] = '1';
				$_SESSION['LOGGEDIN_MEMBER_ID'] = $_SESSION['insertid'];
				$_SESSION['usertype'] = $user_type;
				
				$responder = "Hi ". $name .",<br />
								<br />
								First, thanks for trying our site!  We certainly appreciate your interest.<br />
								<br />
								We are a very young site (just released in May 2011).  Right now, our site is just focused on Orlando events but we'll open it up nationally in the near future (early 2012).  We are still in Beta release and we are making tweaks every day to give the best value to our users. We REALLY want to offer a great product to our users.  Our desire is to create a site that helps people find new and diverse events. <br />
								<br />
								Please 'like' us on Facebook, follow us on Twitter, and share us with your social circles.  We hope to be in your city (and nationwide) soon!!  Also, please let us know what you think about the site (we value your feedback!!)<br />
								<br />
								Thanks,<br />
								<br />
								Tk Walker<br />
								Founder/CEO
								<br />
								<br />
								Antoine Bell<br />
								Co-Founder/CFO
								<br />
								<br />
								Website:http://www.eventgrabber.com
								<br />
								Facebook: http://www.facebook.com/eventgrabber
								<br />
								Twitter: http://www.twitter.com/eventgrabber";
				$headers1  = 'MIME-Version: 1.0' . "\r\n";
				$headers1 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers1 .= 'From: EventGrabber.com<info@eventgrabber.com>' . "\r\n";			
				mail($email,"EventGrabber - Thanks for signing up with us.",$responder,$headers1);
				
				echo "<script>window.location.href='dashboard.php';</script>";
				
			} else {
				$err = 'There is some internal error. Please try later.' ;
			}		
		}	
	}
}

	
	if ( isset($_POST['continue1']) || isset($_POST['continue1_x']) ) {
		
			$usertype 		= DBin($_POST['usertype']);
			$fname 			= DBin($_POST['fname']);
			$lname 			= DBin($_POST['lname']);
			$email 			= DBin($_POST['email']);
			$password 		= DBin($_POST['password']);
			$usertype 		= DBin($_POST['usertype']);
			$agree	 		= DBin($_POST['agree']);
			$memberdate 	= date("Y-m-d");
			$ref_member		= $_POST['ref_member'];
			
			$cpassword 	= DBin($_POST['cpassword']);

	
		
		$Chk_email_q = "select email from users where email = '$email'";
		
		
		$user_res2 = mysql_query($Chk_email_q);
		if($user_r2 = mysql_fetch_assoc($user_res2)){
				$emailDb = trim($user_r2['email']);
		}
		
		if ( trim($fname) == '' )
			$errors[] = 'Please enter First Name';
		if ( trim($lname) == '' )
			$errors[] = 'Please enter Last Name';
		if ( trim($password) == '' )
			$errors[] = 'Please enter password';
		
		if ( trim($password) != trim($cpassword))
			$errors[] = 'Confirm Password does not match.';			
		
		if ( !validateEmail($email) ) {
			$errors[] = 'Email Address is invalid.';
		} elseif($email == $emailDb) {
			$errors[] = 'Email Address Already Exist.';
		}
				
	 	if ( trim($agree) == '' )
			$errors[] = 'Please Accept Terms and Policy.';
			
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			$varifactionCode = session_id();
			
			if($usertype == "p"){
				$user_type = '2';
			}else{
				$user_type = '1';
			}
			
			$name = $fname;
			$user_insert = "insert into members (firstname,lastname, email, password, varifaction_code,createddate, usertype,email_verify,enabled) 
							values ('$name','$lname', '$email', '$password', '$varifactionCode', '$memberdate', '$user_type','1','1') ";
			
			if ( mysql_query($user_insert) ) {
			
				/*$varification_url = ABSOLUTE_PATH."varify.php?code=/".$varifactionCode;
				$msg = "Welcome <br> Your account has been created on Eventgrabber <br> Click the following url to activate your account <br>".$varification_url; */
				
				$_SESSION['insertid'] = mysql_insert_id();
				assignDayTime($_SESSION['insertid']);
				// keep track of referral member
				if ($_SESSION['insertid'] > 0 && $ref_member > 0) {
				
//	$sql_ref = "insert into member_referals (ref_member_id,member_id) VALUES ('". $ref_member ."','". $_SESSION['insertid'] ."')";
				$sql_ref = "insert into member_referals (ref_member_id,member_id,datetime) VALUES ('". $ref_member ."','". $_SESSION['insertid'] ."','". date('Y-m-d H:i:s') ."')";

					mysql_query($sql_ref);
				}
				
				$_SESSION['page_ref'] = 'signup.php';
				
				$_SESSION['logedin'] = '1';
				$_SESSION['LOGGEDIN_MEMBER_ID'] = $_SESSION['insertid'];
				$_SESSION['usertype'] = $user_type;
				
				$responder = "Hi ". $name .",<br />
								<br />
								First, thanks for trying our site!  We certainly appreciate your interest.<br />
								<br />
								We are a very young site (just released in May 2011).  Right now, our site is just focused on Orlando events but we'll open it up nationally in the near future (early 2012).  We are still in Beta release and we are making tweaks every day to give the best value to our users. We REALLY want to offer a great product to our users.  Our desire is to create a site that helps people find new and diverse events. <br />
								<br />
								Please 'like' us on Facebook, follow us on Twitter, and share us with your social circles.  We hope to be in your city (and nationwide) soon!!  Also, please let us know what you think about the site (we value your feedback!!)<br />
								<br />
								Thanks,<br />
								<br />
								Tk Walker<br />
								Founder/CEO
								<br />
								<br />
								Antoine Bell<br />
								Co-Founder/CFO
								<br />
								<br />
								Website:http://www.eventgrabber.com
								<br />
								Facebook: http://www.facebook.com/eventgrabber
								<br />
								Twitter: http://www.twitter.com/eventgrabber";
				$headers1  = 'MIME-Version: 1.0' . "\r\n";
				$headers1 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers1 .= 'From: EventGrabber.com<info@eventgrabber.com>' . "\r\n";			
				mail($email,"EventGrabber - Thanks for signing up with us.",$responder,$headers1);
				
					echo "<script>window.location.href='dashboard.php';</script>";
					
					
			} else {
				$err = 'There is some internal error. Please try later.' ;
			}		
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
	}
</style>
<div id="login-main">
  <div id="login-main-top"></div>
  <div id="login-main-middle">
    <div id="login-head">
      <div id="heading">Joining Eventgrabber is Simple!</div>
      <div id="already-member"> Already a Member? <span><a href="<?php echo ABSOLUTE_PATH;?>login.php"> Sign In </a></span> </div>
    </div>
    <!--login-head -->
    <div id="shadow"></div>
    <div class="error"><?php echo $err; ?></div>
   
    <!-- action="signupsubmit.php" -->
    <p style="text-align:center;"><a href="fblogin.php"><img src="<?php echo IMAGE_PATH;?>facebook-login-button.png" /></a> &nbsp; &nbsp; &nbsp; <a href="twitter/connect.php"><img src="<?php echo IMAGE_PATH;?>twitter_login_button.png" /></a> </p>
    <p style="text-align:center;">
      <!--<iframe src="https://www.facebook.com/plugins/registration.php?
             client_id=167535613301542&
             redirect_uri=<?=urlencode("http://www.eventgrabber.com/signup4.php")?>&
             fields=[
                     {'name':'name'},
                     {'name':'email'},
                     {'name':'gender'},
                     {'name':'birthday'},
                     {'name':'password'},
                     {'name':'zip',      'description':'Zip Code',             'type':'text'},
                     {'name':'ref_member',       'description':'', 'type':'hidden',  'default':'<?php echo $ref_member; ?>'}
                    ]
				  "
        scrolling="auto"
        frameborder="no"
        style="border:none"
        allowTransparency="true"
        width="504"
        height="600"> </iframe>
       -->
       <form action="" method="post" name="signupform" id="signupform" enctype="multipart/form-data">
								<input type="hidden" name="ref_member" value="<?php echo $ref_member; ?>" />
								<div id="form">
								<div class="input-row">
									<div class="input-col-l" style="width:100%;">
										<div class="input-field" style="width:25px;" id="ut">
											<input type="checkbox" <?php if($_POST['usertype']){ echo'checked="checked"'; } ?>  name="usertype" value="p" / style="width:20px; height:20px;" >
											</div>
										<div class="input-label" style="width:90%;">Click here if you are an Event Host or Promoter</div>
									</div>
									
									
									
								</div>
									<div class="input-row">
										<div class="input-col-l">
											<div class="input-label">First Name</div>
											<div class="input-field">
												<input type="text" class="required" name="fname" id="fname" value="<?php if(isset($_POST['fname']) && $_POST['fname'] != '') echo html_entity_decode($_POST['fname'],ENT_QUOTES);?>" onblur="this.value=removeSpaces(this.value);" maxlength="40" />
											
											</div>
										</div><!-- input-col-l -->
										<div class="input-col-r">
											<div class="input-label">Last Name</div>
											<div class="input-field">
												<input type="text" class="required" name="lname" id="lname" onblur="this.value=removeSpaces(this.value);" value="<?php if(isset($_POST['lname']) && $_POST['lname'] != '') echo html_entity_decode($_POST['lname'],ENT_QUOTES);?>" maxlength="40"  />
											</div>
										</div><!-- input-col-r -->
									</div><!-- input-row -->
									<div class="input-row">
										<div class="input-label">Email</div>
										<div class="input-field">
											<input type="text" class="required email" name="email" id="email" onblur="this.value=removeSpaces(this.value);" value="<?php if(isset($_POST['email']) && $_POST['email'] != '') echo html_entity_decode($_POST['email'],ENT_QUOTES);?>" maxlength="60" />
										</div>
									</div><!-- input-row -->
									
									
									<div class="input-row">
										<div class="input-label">Password</div>
										<div class="input-field">
											<input type="password" class="required" name="password" id="password" onblur="this.value=removeSpaces(this.value);" maxlength="20"  />
										</div>
									</div><!-- input-row -->
									
									<div class="input-row">
										<div class="input-label">Confirm Password</div>
										<div class="input-field">
											<input type="password" class="required" name="cpassword" id="cpassword" onblur="this.value=removeSpaces(this.value);" maxlength="20"  />
										</div>
									</div><!-- input-row -->
									
									<div class="input-row">
										<div class="terms"><input name="agree" id="agree" type="checkbox" class="required" value="agree" />I agree to the <span><a href="#A" class="lbLink" onclick='window.open("terms-of-use.php","Window1",
"menubar=no,width=730,height=460,toolbar=no,scrollbars=yes");' >Terms of Service</a></span> and <span><a  href="#A" class="lbLink" onclick='window.open("privacy-policy.php","Window1",
"menubar=no,width=730,height=460,toolbar=no,scrollbars=yes");'>Privacy Policy</a></span></div>
									</div><!-- input-row -->
									<div class="form-submit">
										<!--<input type="hidden" name="refpage" value="signup.php" />-->
										
										<div class="singup-next-btn"><input name="continue1" id="continue1" type="image" src="<?php echo IMAGE_PATH; ?>singup-next.png"   vspace="5" onclick="document.forms["signupform"].submit();"/></div>
									</div>	
								</div><!-- form -->
							</form>
    </p>
  </div>
  <!-- login-main-middle-->
  <div id="login-main-bottom"></div>
  <div class="clr"></div>
</div>
<!-- login-main -->

<div class="clr"></div>
<!--Start Footer -->
<?php
include_once('includes/footer.php');
?>