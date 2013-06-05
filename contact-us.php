<?php require_once('admin/database.php');?>

<?php require_once('includes/header.php');?>

<?php
error_reporting(1);
if(isset($_POST['fname'])){

	$fname 			= DBin($_POST['fname']);
	$lname 			= DBin($_POST['lname']);
	$email 			= DBin($_POST['email']);
	$subject		= DBin($_POST['subject']);
	$message		= stripslashes(nl2br($_POST['message']));
	
	
	if ( trim($fname) == '' )
		$errors[] = 'Please enter First Name';
	if ( trim($lname) == '' )
		$errors[] = 'Please enter Last Name';
	if ( !validateEmail($email)  ){
		$errors[] = 'Email Address is invalid.';
	}elseif($email == ''){
		$errors[] = 'Please Enter Email Address.';
	}
	
	if($message == ''){
			$errors[] = 'Please Write a Message';
	}
	if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			
				$sender_name = $fname." ".$lname;
				$sender_email = $email;
				$to_email = 'info@eventgrabber.com';
				//$subject	= "Your New EventGrabber Password";
				$html	  = $message;
				$headers  = 'MIME-Version: 1.0' . "\r\n";
 				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 				$headers .= 'From: '.$sender_name.'<'. $sender_email .'>' . "\r\n";
				//$eventtype
				if(mail($to_email,$subject,$html,$headers)){
					$smessage = "Your Message has been sent";
					
					$responder = "Hi ". $fname .",<br />
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
					mail($sender_email,"EventGrabber - We received your contact request",$responder,$headers1);
				}else{
					$errors[] = "Your Request can not be completed right now, Please try Later";
				}
			
			
		}
	
		
}
?>
<script type="text/javascript" src="js/jquery.validate.js"></script>
<script>
$(document).ready(function(){
   $("#contactus").validate({
		rules: {
    	message: {
      		required: true,
     		minlength: 50
    	}
  	 }
	});
});
</script>
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
	margin-left:116px;
}

.evInput{
	font-size:12px;
	font-family: "Century Gothic";
	}
	
label.error {
	color:#FF0000}
</style>
<div class="topContainer">

	<div class="welcomeBox"></div>

	<div class="eventDetailhd"><span>Contact<strong>Us</strong></span></div>			

	<div class="clr"></div>

	<div class="contactDetail"></div>

</div>

<div class="">

		<div class="middleContainer" style="padding-top:16px;">

		<div style="float: left;"></div>

                  <!--submit-contact-us.php -->
<span style="font-size:22px;">By Phone:  <span style="color:#0066FF">(407) 505-5758</span><br />
					or Send Us a Message</span><br />&nbsp;
			<form id="contactus" name="contactus" method="post" action="">			

			<div class="eventLft">

				<div><img alt="" src="<?php echo IMAGE_PATH; ?>event_tpcone.gif"></div>

				
				<div class="eventMdlData">
				
					<div class="success"><?php echo $smessage; ?></div>
					<div class="error"><?php echo $err; ?></div>
					<div class="evField" style="width:107px">First Name</div>

					<div class="evLabal"><input type="text" maxlength="100" style="width: 300px; height:20px" class="evInput" id="fname" name="fname" value="<?php echo $fname; ?>"></div>

					<div class="clr"></div>

					<div class="evField" style="width:107px">Last Name</div>

					<div class="evLabal"><input type="text" maxlength="100" style="width: 300px; height:20px" class="evInput" id="lname" name="lname" value="<?php echo $lname; ?>"></div>

					<div class="clr"></div>

					<div class="evField" style="width:107px">Your Email</div>

					<div class="evLabal"><input type="text" maxlength="100" style="width: 300px; height:20px" class="evInput" id="email" name="email" value="<?php echo $email; ?>"></div>

					<div class="clr"></div>

					<div class="evField" style="width:107px">Subject</div>

					<div class="evLabal">

					<select style="width: 306px; padding:3px;" id="subject" name="subject" class="evSel">

						<option selected="selected" value="General questions, comments or feedback" <?php if($subject == 'General questions, comments or feedback'){echo "selected";}?>>General questions, comments or feedback</option>

						<option value="Event listing and advertising" <?php if($subject == 'Event listing and advertising'){echo "selected";}?>>Event listing and advertising</option>

						<option value="Technical Support" <?php if($subject == 'Technical Support'){echo "selected";}?>>Technical Support</option>

						<option value="Interested in Partnerships or Investing" <?php if($subject == 'Interested in Partnerships or Investing'){echo "selected";}?>>Interested in Partnerships or Investing</option>

						<option value="Other" <?php if($subject == 'Other'){echo "selected";}?>>Other</option>

					</select>

					</div>

					<div class="clr"></div>

					<div class="evField" style="width:107px">Message</div>

					<div class="evLabal"><textarea class="evInput" style="height: 100px;" rows="2" cols="2" id="message" name="message"><?php echo $message; ?></textarea></div>

					<div class="clr"></div>

					<div class="evField" style="width:107px"></div>

					<div class="evLabal"><input type="image"  id="submit" src="<?php echo IMAGE_PATH; ?>submit_contact.jpg" name=""></div>

					<div class="clr"></div>		

				</div>

				<div style="display: none;" id="errmsg" class="errorBox">

					<div id="myspan" class="erroeMessage"><img class="vAlign" alt="" src="<?php echo IMAGE_PATH; ?>error.jpg"> Error Message will gose here</div>

					

					<div class="clr"></div>

					<input type="hidden" value="contactus" name="action_new">

					<input type="hidden" value="" id="submit_form" name="submit_form">

				</div>

				<div><img alt="" src="<?php echo IMAGE_PATH; ?>event_btcone.gif"></div>

			</div>

			<div class="clr"></div>	

			</form>

		</div>

</div>	



<?php require_once('includes/footer.php');?>