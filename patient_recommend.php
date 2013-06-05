<?php

if(isset($_POST["name"]) && $_POST["name"] != ""){
	$bc_name				= DBin($_POST['name']);
	$bc_to_emails			= DBin($_POST['emails']);
	$bc_message				= $_POST['message'];
	
	 $member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	if ($bc_name == '' || $bc_name == 'Your Name'){ 
			$errors[] = 'Please enter Name*';
	}
	if ($bc_to_emails == '' || $bc_to_emails == 'example@recipient.com'){ 
			$errors[] = 'Please enter Email(s)*';
	}else{
		
		$flag = "";
		$email_arr = explode(",", $bc_to_emails);
		for($e = 0; $e < count($email_arr); $e++){
			if(!validateEmail(trim($email_arr[$e]))){
				$flag = "invalid";
			}
			if($flag == "invalid"){
				$errors[] = 'Enter Valid Email(s)*';
			}
		}
	
	
	}
	
	if ($bc_message	== ''){ 
			$errors[] = 'Please write Message*';
	}
	
	if (isset($errors)) {
		$err = '<table border="0" width="90%"><tr><td  ><ul class="error">';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	
    }else{
	
		
	$invite	= getSingleColumn("id","select * from `completeness` where `user_id`='$member_id'");
	if($invite)
		mysql_query("UPDATE `completeness` SET `invite` = '1' WHERE `user_id` = '$member_id'");
	else
		mysql_query("INSERT INTO `completeness` (`id`, `invite`, `user_id`) VALUES (NULL, '1', '$member_id')");
		
		
		$emails		= 	explode(",",$bc_to_emails);
		if(is_array($emails)){
			foreach ($emails as $to){
				$sender		= 'info@pangea.com';
				$subject	= 'Pangea Invitation';
				$headers	= 'MIME-Version: 1.0' . "\r\n";
				$headers	.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers	.= 'From: '.$bc_name.'<'. $sender .'>' . "\r\n";
				mail($to,$subject,$bc_message,$headers);
			}
		}
		else{
			$sender		=	'info@pangea.com';
			$subject	=	'Pangea Invitation';
			$headers	= 'MIME-Version: 1.0' . "\r\n";
			$headers	.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers	.= 'From: '.$bc_name.'<'. $sender .'>' . "\r\n";
			mail($to,$subject,$bc_message,$headers);
		}
		$err	= '<span style="color:#ff0000"><strong>Email Sent Successfully</strong></span><br /><br />';
		
			
	
	}
	
}

?>
<style>
.stp1Fb{
	border-right: 1px solid #CFCCCF;
    float: left;
    font-size: 13px;
    padding: 10px;
    width: 41%;
	}
	
.YellowHead{
	color:#cb9836;
	padding-bottom:26px;
	font-weight:bold;
	}
	
.sendMail {
    float: left;
    padding: 0 10px;
    width: 52%;
	color:#7a7979
}

.sendMail b{
	color:#000000;
	}

.sendMail input[type=text]{
	border: 1px solid #C0CAE0;
    color: #B6B6B6;
    height: 25px;
    padding: 0 5px;
    width: 337px;
	}
	
.sendMail textarea{
	border:#cdd5e6 solid 1px;
	padding: 0 5px;
    width: 337px;
	color: #B6B6B6;
	}
</style>
 <div id="fb-root"></div>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
	    <script>
	$(document).ready(function(){
		  FB.init({
			appId  : '417488184934744',
		  });
	});
	
      function sendRequestToRecipients() {
        var user_ids = document.getElementsByName("user_ids")[0].value;
        FB.ui({method: 'apprequests',
          message: 'My Great Request',
          to: user_ids, 
        }, requestCallback);
      }

      function sendRequestViaMultiFriendSelector() {
	  	
		$.ajax({  
			type: "POST",
			url: "ajax/updateInvite.php",
			data:""
			});
		
        FB.ui({method: 'apprequests',
          message: 'My Great Request'
        }, requestCallback);
      }
      
      function requestCallback(response) {
        if (response.request && response.to && response.to.length > 0) {
          var ids = response.to.join();
		 // alert(ids);
		 } 
      }
    </script>
	
	
	
<div class="yellow_bar"> &nbsp; STEP 4 - INVITE</div>
<!-- /yellow_bar -->
<div style="padding:0 10px;"><br />
<div class="error"><?php if(isset($err)){echo $err;} ?></div><br class="clr" />
	<div style="border:#dae0e8 solid 12px">
		<div style="border:#a4b5ca solid 1px; background:#FFFFFF">
			<div class="stp1Fb">
				<div class="YellowHead">Step 1: Log in with Facebook and Recommend your Friends to Pangea</div>
				<div align="center">
					<img src="<?php echo IMAGE_PATH; ?>facebook-login-button.png" onClick="sendRequestViaMultiFriendSelector(); return false;" style="cursor:pointer" />
					<img src="<?php echo IMAGE_PATH; ?>members2.gif" style="padding-top: 35px;" />
				</div>
			</div> <!-- /stp1Fb -->
			<div class="sendMail">
				<form action="" method="post">
					<table cellpadding="5" cellspacing="0" border="0" style="width:100%;">
						<tr>
							<td width="84%" align="left" valign="top" >
								<b>From:</b> Your Name<br />
								<input type="text" name="name" value="Your Name" onfocus="if(this.value=='Your Name'){ this.value=''; this.style.color='#373737';}" onblur="if(this.value==''){ this.value='Your Name';this.style.color='#B6B6B6';}" size="30"/>
								
							</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<b>To:</b> Enter your friends' emails (separate with commas)<br />
								<input type="text" name="emails" onfocus="if(this.value=='example@recipient.com'){ this.value=''; this.style.color='#373737';}" onblur="if(this.value==''){ this.value='example@recipient.com';this.style.color='#B6B6B6';}" value="example@recipient.com"size="30"/>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<b>Your Message</b><br />
								<textarea name="message" cols="30" rows="6" readonly="readonly">Hey,
								
I just came across this really neat site called Pangea.   You should check it out. <?php echo ABSOLUTE_PATH; ?></textarea><br />
								We don't spam you or the people your invite. View Pangeas's <a href="http://www.pangeaexperience.com" target="_blank" style="text-decoration:none">Website.</a>
							</td>
						</tr>
						<tr>
							<td align="right" valign="top">
								<input type="image" src="<?php echo IMAGE_PATH; ?>send_invitation.gif" name="send_invitation" value="Send Invitation" />
								<input type="hidden" name="send_invitation" value="Send Invitation" /> or <a href="?p=event-preferences" style="text-decoration:none">Skip</a>
							</td>
						</tr>
					</table>
				</form>
			</div> <!-- /sendMail -->
			
			<br class="clr" />
		</div>
	</div>
	<br class="clr" />
	<table width="100%">
		<tr>
			<!--
<td width="39%" class="YellowHead">Step 2: "Like" Us on Facebook
				<div id="fb-root"></div>
				<script>
					(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
					fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				</script>
				<div class="fb-like-box" data-border-width="0" data-href="https://www.facebook.com/Pangea"  data-height="64px" data-width="287px" data-show-faces="false" data-stream="false" data-header="false"></div>
		  </td>
-->
			<td width="61%" valign="top"><br /><br />
				
				
				<a href="?p=notifcations"><img src="<?php echo IMAGE_PATH; ?>back_gray.png" align="right" /></a>
				<input type="hidden" name="continue" value="Save & Continue" />&nbsp; 
				<br class="clr" />&nbsp;
		  </td>
		</tr>
	</table>

</div>