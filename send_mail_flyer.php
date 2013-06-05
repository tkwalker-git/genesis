<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Showcase Creator</title>
</head>
<body style="background-image:url(images/headar_bg.jpg); background-repeat:repeat-x; width:500px!important; min-width:500px!important; height:400px">
<?php
include_once("admin/functions.php");
include_once("site_functions.php");
								
									
	$event_id = $_GET['id'];

	if( $_GET['pangea'] == 1 )
		$u	= 'https://pangeaifa.com/IFA/Account/Register.aspx?ClinicID=14';
		
	else
		 /* $u = getEventURL($event_id); */
		 $u = 'http://restorationhealth.yourhealthsupport.com/fbflayer/index.php?id='.$event_id; 


if(isset($_POST["name"]) && $_POST["name"] != ""){
	$bc_name				= DBin($_POST['name']);
	$bc_to_emails			= DBin($_POST['emails']);
	$bc_subject				= DBin($_POST['subject']);
	$bc_message				= DBin($_POST['message']);
	
	
	
	if ($bc_name == ''){ 
			$errors[] = 'Please enter Name*';
	}
	if ($bc_to_emails == ''){ 
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
	if ($bc_subject == ''){ 
			$errors[] = 'Please enter Subject*';
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
		
		 
		$to = $bc_to_emails;
		$subject = $bc_subject;
		
		$sender_name	= $bc_name;
		$sender_email	=	"info@pangea.com";
		$subject		=	$bc_subject;
		$html			=	$bc_message."<br>".$u;
	//	$html			=	$u;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
 		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 		$headers .= 'From: '.$sender_name.'<'. $sender_email .'>' . "\r\n";
		
		
			if(mail($to,$subject,$html,$headers))
			{
				 echo '<script type="text/javascript">alert("Emails Has been Sent Successfully");var popup_window = window.open("", "_self");popup_window.close ();</script>';
			}else{
			
				 echo '<script type="text/javascript">alert("Please Try Later");</script>';
			
			}
	}
}
?>
<style>
	body
	{
		font-family:Arial, Helvetica, sans-serif;
		font-size:12px;
		color:#333333;	
	}
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
		padding-top:20px;
		padding-left:10px;
	}
	
	.error ul li
	{
		margin-left:40px;
		color:#990000;
		font-family:Arial, Helvetica, sans-serif;
		font-size:12px;
	}
#emailMsg{
	width:400px; 
	padding-left:50px;
}

.eventDetailhd {background:url(images/event_detail_hd_bg.gif) no-repeat left top; color:#FFFFFF; font-size:22px !important; display:block; float:left; padding:0 0 0 10px; margin:0 10px 0 0; height:50px; width:400px; padding-top:5px;}

</style>
<div id="emailMsg">
	<form action="" method="post">
	<table cellpadding="5" cellspacing="0" border="0" style="width:100%;">
		<tr><td colspan="2" height="55">&nbsp;</td></tr>
  		<!-- <tr><td colspan="2"><img src="images/logo_transparent.png" width="359" height="61" /></td> </tr> -->
  		<tr><td colspan="2" style="padding-top:20px"><div class="eventDetailhd"><span>Invite Your <strong>Friends</strong></span></div></td> </tr>
		<tr><td colspan="2" valign="top" > <div class="error"><?php if(isset($err)){echo $err;} ?></div></td></tr>
		<tr><td align="right" valign="top" >Your Name: </td><td align="left" valign="top" ><input type="text" name="name" size="30"/></td></tr>
		<tr><td align="right" valign="top">To Email(s): </td><td align="left" valign="top"><input type="text" name="emails" size="30"/><br /><span style="font-size:11px;">&nbsp;Enter Email(s) Comma separated</span></td></tr>
		<tr><td align="right" valign="top">Subject: </td><td align="left" valign="top"><input type="text" name="subject" size="30"/></td></tr>
		<tr><td align="right" valign="top">Message:</td><td align="left" valign="top"><textarea name="message" cols="30" rows="6"></textarea></td></tr>
		<tr><td align="right" valign="top">&nbsp;</td><td align="left" valign="top"><input type="submit" value="Send"  name="submit" /></td></tr>
	</table>
	</form>
</div>
</body>
</html>
<script type="text/javascript">