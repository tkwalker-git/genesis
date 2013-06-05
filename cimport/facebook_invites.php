<?php

	

require_once('../admin/database.php'); 

require_once('invitation_message.php');

include_once('../admin/api/facebook.php');

	

	

$step = $_POST['step'];

if ( $step == 'send_invites') {

	

	foreach ($_POST as $key => $val) {

		if (strpos($key,'check_')!== false)

			$selected_contacts[$_POST['fb_id_'.$val]] = $_POST['name_'.$val];

		elseif (strpos($key,'fb_id_') !== false)

		{

			$temp=explode('_',$key);

			$counter=$temp[1];

			if (is_numeric($temp[1])) 

				$contacts[$val]=$_POST['name_'.$temp[1]];

		}

		

		if (count($selected_contacts)==0) 

			$error = "You haven't selected any contacts to invite !";

	}



	if ( $error == '' ) {

		

		$attachment = array('message' => 'this is my message',

                'name' => 'This is my demo Facebook application!',

                'caption' => "Caption of the Post",

                'link' => 'http://mylink.com',

                'description' => 'this is a description',

                'picture' => 'http://mysite.com/pic.gif',

                'actions' => array(array('name' => 'Get Search',

                                  'link' => 'http://www.google.com'))

                );





    	$result = $facebook->api('/me/feed/','post',$attachment);



							

		/*

		$logged_in_member_name = attribValue("users","firstname","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);

		$logged_in_member_email = attribValue("users","email","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);

		

		$message_body = "Dear #NAME#, <br><br>Your friend " . $logged_in_member_name . " has invited you to joing EventGrabber. Follow the link below to create your account.<br><br>"; 

		$message_body .= '<a href="http://www.eventgrabber.com/signup.php?ref='. base64_encode($_SESSION['LOGGEDIN_MEMBER_ID']) .'">www.eventgrabber.com</a>';

		$message_body .= '<br><br>Thanks,<br>EventGrabber.com';

		

		$message_subject = 'Your Friend ' . $logged_in_member_name . ' invited you to join EventGrabber.com';

		

		$headers  = 'MIME-Version: 1.0' . "\r\n";

		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		$headers .="From: " . $logged_in_member_name . "<". $logged_in_member_email .">" . "\r\n";

	

		foreach ($selected_contacts as $email => $name) {

			$message_body = getInviteEmailContents($logged_in_member_name,$_SESSION['LOGGEDIN_MEMBER_ID']);

			echo 'Sending Email to : ' . $email . '<br>';

			//mail($email,$message_subject,$message_body,$headers);

		}	

		$oks['mails'] = "Mails sent successfully";

		*/

		$step = 'invites_sent';



	}	

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

	var form = document.forms.facebookinviter, z = 0;

	for(z=0; z<form.length;z++)

	{

		if(form[z].type == 'checkbox')

			form[z].checked = element.checked;

	}

}

	

</script>





<form action="" method="POST" name="facebookinviter" >



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

 

  <?php if ($step == 'invites_sent') { ?>

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

   <?php } else { ?>

  <tr>

    <td style="color:#990000"><strong>Step 3:</strong> Select your friends from the list below and Invite to join EventGrabber.com</td>

  </tr>

 

  <tr>

    <td align="left" style="padding:20px!important;">

		<div style="height:200px; width:550px; overflow:auto">

			<?php

			

			function get_facebook_cookie($app_id, $application_secret) {

			  $args = array();

			  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);

			  ksort($args);

			  $payload = '';

			  foreach ($args as $key => $value) {

				if ($key != 'sig') {

				  $payload .= $key . '=' . $value;

				}

			  }

			  if (md5($payload . $application_secret) != $args['sig']) {

				return null;

			  }

			  return $args;

			}

			

			$cookie = get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);

			

			if ($cookie) { 

				

				$friends = json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token=' . $cookie['access_token']), true);

				$friends = $friends['data'];

			

				echo '<table width="100%" cellpadding="3" cellspacing="0" align="left">

						<tr style="background-color:#EEEEEE; border-bottom:#CCCCCC solid 1px">

							<td align="left" width="20"><input type="checkbox" onChange="toggleAll(this)" name="toggle_all" title="Select/Deselect all" checked></td>

							<td align="left">Select/Deselect All</td>

						</tr>';

				$counter = 1;		

				foreach ($friends as $friend) {

					$counter++;

					echo '<tr><td align="left">

							<input name="check_'. $counter .'" value="'. $counter .'" type="checkbox" checked="checked" class="thCheckbox">

							<input type="hidden" name="fb_id_'. $counter .'" value="'. $friend['id'] .'">

							<input type="hidden" name="name_'. $counter .'" value="'. $friend['name'] .'">

						</td>';

					echo '<td align="left">'. $friend['name'] . '</td>';

					echo '</tr>';

				}

				echo '</table>';

					

					

			} else { ?>

				  <fb:login-button></fb:login-button>

			<?php } ?>

			

				<div id="fb-root"></div>

				<script src="http://connect.facebook.net/en_US/all.js"></script>

				<script>

				  FB.init({appId: '<?= FACEBOOK_APP_ID ?>', status: true,

						   cookie: true, xfbml: true});

				  FB.Event.subscribe('auth.login', function(response) {

					window.location.reload();

				  });

				</script>



		</div>

	</td>

  </tr>

  <tr>

    <td >

		<input type="submit" name="send" value="Invite Your Friends" >

		<input type="hidden" name="step" value="send_invites">

	</td>

  </tr>

 	<?php }  ?>

  <tr>

    <td>&nbsp;</td>

  </tr>

</table>

</form>

</body>

</html>

