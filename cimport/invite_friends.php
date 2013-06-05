<?php  

	

	require_once('../admin/database.php'); 

	require_once('invitation_message.php'); 

	

	$arrSites = array("aol"=>"AOL","gmail"=>"Gmail","hotmail"=>"Hotmail/Live","yahoo"=>"Yahoo!","facebook"=>"Facebook","twitter"=>"Twitter","linkedin"=>"LinkedIn");

	

	if ( isset ( $_GET['site'] ) ) {

		if ( array_key_exists($_GET['site'],$arrSites) ) {

			$provider_box = $_GET['site'];

			$step = 'get_contacts';

		} else

			$provider_box = 'Provider not in List';	

	}

	

	

	include('openinviter.php');

	$inviter=new OpenInviter();

	

	$oi_services=$inviter->getPlugins();

	if (isset($_POST['provider_box'])) 

	{

		if (isset($oi_services['email'][$_POST['provider_box']])) $plugType='email';

		elseif (isset($oi_services['social'][$_POST['provider_box']])) $plugType='social';

		else $plugType='';

	}

	else 

		$plugType = '';

		

	function ers($ers)

	{

	if (!empty($ers))

		{

		$contents="<table cellspacing='0' cellpadding='0' style='border:1px solid red;' align='center'><tr><td valign='middle' style='padding:3px' valign='middle'><img src='images/ers.gif'></td><td valign='middle' style='color:red;padding:5px;'>";

		foreach ($ers as $key=>$error)

			$contents.="{$error}<br >";

		$contents.="</td></tr></table><br >";

		return $contents;

		}

	}

		

	function oks($oks)

	{

	if (!empty($oks))

		{

		$contents="<table border='0' cellspacing='0' cellpadding='10' style='border:1px solid #5897FE;' align='center'><tr><td valign='middle' valign='middle'><img src='images/oks.gif' ></td><td valign='middle' style='color:#5897FE;padding:5px;'>	";

		foreach ($oks as $key=>$msg)

			$contents.="{$msg}<br >";

		$contents.="</td></tr></table><br >";

		return $contents;

		}

	}

	

	if ( !empty($_POST['step'])) 

		$step = $_POST['step'];

	

	$ers=array();

	$oks=array();

	$import_ok=false;

	$done=false;

	

	if ($_SERVER['REQUEST_METHOD']=='POST')

	{

		if ($step=='get_contacts')

		{

			if (empty($_POST['email_box']))

				$ers['email']="Email missing !";

			if (empty($_POST['password_box']))

				$ers['password']="Password missing !";

			if (empty($_POST['provider_box']))

				$ers['provider']="Provider missing !";

			if (count($ers)==0)

			{

			$inviter->startPlugin($_POST['provider_box']);

			$internal=$inviter->getInternalError();

			if ($internal)

				$ers['inviter']=$internal;

			elseif (!$inviter->login($_POST['email_box'],$_POST['password_box']))

				{

				$internal=$inviter->getInternalError();

				$ers['login']=($internal?$internal:"Login failed. Please check the email and password you have provided and try again later !");

				}

			elseif (false===$contacts=$inviter->getMyContacts())

				$ers['contacts']="Unable to get contacts !";

			else

				{

				$import_ok=true;

				$step='send_invites';

				$_POST['oi_session_id']=$inviter->plugin->getSessionID();

				$_POST['message_box']='';

				}

			}

		}

		

		elseif ($step=='send_invites')

		{

			if (empty($_POST['provider_box'])) 

				$ers['provider']='Provider missing !';

			else

			{

				$inviter->startPlugin($_POST['provider_box']);

				$internal=$inviter->getInternalError();

				if ($internal) 

					$ers['internal']=$internal;

				else

				{

					if (empty($_POST['email_box'])) 

						$ers['inviter']='Inviter information missing !';

					if (empty($_POST['oi_session_id'])) 

						$ers['session_id']='No active session !';

					//if (empty($_POST['message_box'])) $ers['message_body']='Message missing !';

					else $_POST['message_box']=strip_tags($_POST['message_box']);

					$selected_contacts=array();$contacts=array();

					$message=array('subject'=>$inviter->settings['message_subject'],'body'=>$inviter->settings['message_body'],'attachment'=>"\n\r  \n\r");

					if ($inviter->showContacts())

					{

						foreach ($_POST as $key=>$val)

							if (strpos($key,'check_')!==false)

								$selected_contacts[$_POST['email_'.$val]]=$_POST['name_'.$val];

							elseif (strpos($key,'email_')!==false)

							{

								$temp=explode('_',$key);$counter=$temp[1];

								if (is_numeric($temp[1])) 

									$contacts[$val]=$_POST['name_'.$temp[1]];

							}

							if (count($selected_contacts)==0) $ers['contacts']="You haven't selected any contacts to invite !";

						}

					}

				}



			if (count($ers)==0)

			{

			

				$sendMessage=$inviter->sendMessage($_POST['oi_session_id'],$message,$selected_contacts);

				$inviter->logout();

				if ($sendMessage === -1)

				{

					

					$logged_in_member_name = attribValue("users","firstname","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);

					$logged_in_member_email = attribValue("users","email","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);

					

					$message_body = "Dear #NAME#, <br><br>Your friend " . $logged_in_member_name . " has invited you to joing EventGrabber. Follow the link below to create your account.<br><br>"; 

					$message_body .= '<a href="http://www.eventgrabber.com/signup.php?ref='. base64_encode($_SESSION['LOGGEDIN_MEMBER_ID']) .'">www.eventgrabber.com</a>';

					$message_body .= '<br><br>Thanks,<br>EventGrabber.com';

					

					$message_subject = 'Your Friend ' . $logged_in_member_name . ' invited you to join EventGrabber.com';

					

					$headers  = 'MIME-Version: 1.0' . "\r\n";

					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					$headers .="From: " . $logged_in_member_name . "<". $logged_in_member_email .">" . "\r\n";



					foreach ($selected_contacts as $email=>$name) {

						$message_body = getInviteEmailContents($logged_in_member_name,$_SESSION['LOGGEDIN_MEMBER_ID']);

						mail($email,$message_subject,$message_body,$headers);

					}	

					$oks['mails'] = "Mails sent successfully";

					$step = 'invites_sent';

				}

				elseif ($sendMessage === false)

				{

					$internal=$inviter->getInternalError();

					$ers['internal']=($internal?$internal:"There were errors while sending your invites.<br>Please try again later!");

				}

				else 

					$oks['internal']="Invites sent successfully!";

				$done=true;

			}

		}

	}

	else

	{

		$_POST['email_box']='';

		$_POST['password_box']='';

		$_POST['provider_box']='';

	}



?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Invite your Friends!!</title>



<link rel="shortcut icon" href="<?php echo IMAGE_PATH; ?>favicon.ico" type="image/x-icon" />

<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>style.css"/>

</head>



<body style="background-image:url(<?php echo IMAGE_PATH; ?>headar_bg.jpg); background-repeat:repeat-x; width:600px!important; min-width:600px!important; height:500px">



<script type='text/javascript'>



	function toggleAll(element) 

	{

	var form = document.forms.openinviter, z = 0;

	for(z=0; z<form.length;z++)

		{

		if(form[z].type == 'checkbox')

			form[z].checked = element.checked;

	   	}

	}

	

</script>





<form action="" method="POST" name="openinviter" >



<table width="550" border="0" cellspacing="0" cellpadding="5" align="center">

  <tr>

    <td height="55">&nbsp;</td>

  </tr>

  <tr>

    <td>

    <img src="<?php echo IMAGE_PATH; ?>logo.jpg" width="359" height="61" /></td>

  </tr>

  <tr>

    <td style="padding-top:20px"><div class="eventDetailhd"><span>Invite Your <strong>Friends</strong></span></div></td>

  </tr>

  <?php if ($step == 'get_contacts') { ?>

  <tr>

    <td style="color:#990000"><strong>Step 2:</strong> Put Email/Password and get your friends list</td>

  </tr>

  

  <tr>

    <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="5">



	  <tr>

        <td colspan="2"><h3><?php echo 'Enter your '.  $arrSites[$provider_box]  . ' Email/Password below.';?></h3></td>

      </tr>

	  <tr>

        <td>Email:</td>

        <td><input type="text" name="email_box" value="<?php echo $_POST['email_box'];?>" ></td>

      </tr>

	  <tr>

        <td>Password:</td>

        <td><input type="password" name="password_box" value="<?php echo $_POST['password_box'];?>"></td>

      </tr>

	  <tr>

        <td>&nbsp;<input type="hidden" name="provider_box" value="<?php echo $provider_box;?>" /></td>

        <td><input type="submit" name="send" value="Get Contacts"  ></td>

      </tr>

	  

    </table></td>

  </tr>

  <?php } elseif ($step == 'invites_sent') { ?>

  <tr>

    <td style="color:#990000"><strong>Congratulations!</strong> Your invites are sent to the following friends.</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td align="left">

		<?php

			foreach ($selected_contacts as $email=>$name) {

				echo $name . ' ('.$email.')<br>';

			}

		?>

	</td>

  </tr>

   <?php } if ($step == 'send_invites') { ?>

  <tr>

    <td style="color:#990000"><strong>Step 3:</strong> Select your friends from the list below and Invite to join EventGrabber.com</td>

  </tr>

 

  <tr>

    <td align="left" style="padding:20px!important;">

		<div style="height:200px; width:550px; overflow:auto">

			<?php 

				if ($inviter->showContacts()) { 

					if ( count($contacts) == 0 ) {

						echo 'No Contacts';

					} else {

					?>

					<table width="100%" cellpadding="3" cellspacing="0" align="left">

					<tr style="background-color:#EEEEEE; border-bottom:#CCCCCC solid 1px">

						<td align="left" width="20"><input type='checkbox' onChange='toggleAll(this)' name='toggle_all' title='Select/Deselect all' checked></td>

						<td align="left">Select/Deselect All</td>

					</tr>

					<?php

						foreach ($contacts as $email=>$name) { 

							$counter++;

						?>

							<tr>

							<td align="left">

								<input name="check_<?php echo $counter;?>" value="<?php echo $counter;?>" type="checkbox" class="thCheckbox" checked>

							</td>

							<td align="left">

								

								<input type="hidden" name="email_<?php echo $counter;?>" value="<?php echo $email;?>">

								<input type="hidden" name="name_<?php echo $counter;?>" value="<?php echo $name;?>">

								<?php echo $name;?> (<?php echo $email;?>)

							</td>

							</tr>

						<?php

						}

						?>

					</table>

					<?php	

					}

				} ?>

		</div>

	</td>

  </tr>

  <tr>

    <td >

		<input type="submit" name="send" value="Invite Your Friends" >

		

		<input type="hidden" name="step" value="send_invites">

		<input type="hidden" name="provider_box" value="<?php echo $_POST["provider_box"];?>">

		<input type="hidden" name="email_box" value="<?php echo $_POST["email_box"];?>">

		<input type="hidden" name="oi_session_id" value="<?php echo $_POST["oi_session_id"];?>">

		

	</td>

  </tr>

  

  <?php } else  if ($step == '') { ?>

  <tr>

    <td style="color:#990000"><strong>Step 1:</strong> Invite your friends from the sites below... </td>

  </tr>

  <tr>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="5">

      <tr>

        <td>

			<a href="?site=gmail"><img src="<?php echo IMAGE_PATH; ?>gmail_logo.jpg" width="134" height="100" /></a>

		</td>

        <td>

			<a href="?site=hotmail"><img src="<?php echo IMAGE_PATH; ?>hotmail_logo.png" width="122" height="100" /></a>

		</td>

        <td>

			<a href="?site=yahoo"><img src="<?php echo IMAGE_PATH; ?>yahoo_logo.jpg" width="128" height="100" /></a>

		</td>

        <td>

			<a href="?site=aol"><img src="<?php echo IMAGE_PATH; ?>aol_logo.jpg" width="134" height="100" /></a></td>

      </tr>

	  

	   <tr>

        <td align="center">

			<a href="?site=twitter"><img src="<?php echo IMAGE_PATH; ?>twitter_logo.png" width="100" height="100" /></a>

		</td>

		<td align="center">

			<!--<a href="facebook_invites.php"><img src="<?php echo IMAGE_PATH; ?>facebook_logo.png" width="100" height="100" /></a>-->

		</td>

        <td align="center">

			<!--<a href="?site=linkedin"><img src="<?php echo IMAGE_PATH; ?>linkedin_logo.png" width="118" height="100" /></a>-->

		</td>

        <td>&nbsp;</td>

      </tr>

    </table></td>

  </tr>

  <?php } ?>

  <tr>

    <td>&nbsp;</td>

  </tr>

</table>

</form>

</body>

</html>

