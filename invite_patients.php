<?php

	if ( isset($_POST['continue']) )
	{
		
		$email 			=	DBin($_POST['email']);
		

		
		if ( trim($email) == '' )
			$errors[] = 'Please enter Patient Email.';
		
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			$err = "Invitation sent...";
			/*$to = $email;
			$name = $firstname .' '. $lastname;
			$subject = "You have subscribed successfully";
			$message = 'Dear '. $name .' ,<br><br>';
			$message .= 'You are subscribed successfully with pachage '. $product_name .'<br>';
			$message .= 'Your login informations are below:<br><br>';
			$message .= 'Email:'. $email .'<br>';
			$message .= 'Password:'. $password .'<br><br>';
			$message .= 'Now you can brows , invite to any caretaker for your better health<br>';
			$message .= 'Please login to your account by click the url below. <br><br> <a href="'. ABSOLUTE_PATH  .'">'. ABSOLUTE_PATH  .'</a><br><br>';
			$message .= 'Message footer';
			
			
			$semi_rand = md5(time()); 
			$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
			 
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=iso-8859-1";
			$headers[] = "From: ". $name ." <". $to .">";
			$headers[] = "Reply-To: Recipient Name <no-reply@example.com>";
			$headers[] = "Subject: {$subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();


			$ok = @mail($to, $subject, $message, implode("\r\n", $headers));*/
			
			
			
			
	
		}	
			
	}
	
	

?>
<style>
.whiteMiddle .evField {
	
	}

.whiteMiddle .evField {
	text-align:left;
	font-size:15px;
	width:134px;
	}
	
.evLabal{
	font-size:15px;
	}
	
.evInput{
	font-size:14px;
	}
	
</style>

<style type="text/css">
.ew-heading{
	color: #49BA8D;
    font-size: 24px;}
	
.ew-heading a{
	color: #FF7A57;
    float: right;
    font-size: 14px;
	text-decoration:underline;}

.ew-heading-behind{
	color: #6EB432;
    font-size: 24px;}

.ew-heading-behind span{}

.ew-heading-a{
	color: #212121;
    font-size: 20px;}

</style>
    
    
<div class="yellow_bar"> &nbsp; INVITATION TO PATIENTS</div>
<!-- /yellow_bar -->
<div style="padding:0 10px;"><br />
<div class="error" style="color:red; text-align:center; font-weight:bold;"><?php echo $err; ?></div>
	

	<div class="ew-heading">Enter the email and click Submit.</div>

	<form action="" method="post" name='profrm' enctype="multipart/form-data">

      <div class="clr"></div>
    <div class="editProox">
      <div class="evField">Patient Email</div>
      <div class="evLabal">
        <input type="text" name="email" class="evInput" style="width:300px; height:20px" value="" placeholder="Enter email here..." />
      </div>
      <div class="clr"></div>
      
      
    
	
    </div>
 
	<div align="right"><br /><br />
		
		<input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" align="right" name="continue" value="Save & Continue" style="padding:10px 0 0 10px;" />
		<!-- <a href="?p=patint-notifcations"><img src="<?php echo IMAGE_PATH; ?>skip.png" align="right"  style="padding:10px 0 0 10px;" /></a> -->
		<input type="hidden" name="continue" value="Save & Continue" />&nbsp; 
		  <br class="clr" />&nbsp;
	</div>
	
	</form>
</div>