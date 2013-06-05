<?php
	require_once('admin/database.php');
	require_once('site_functions.php');

function genRandomString() {

        $string ="pangea";


    return $string;
}


$errors = array();
if ($_POST["email"] == "")
	$errors[] = "email can not be empty";

	if($_POST['submit']){
	if (!count($errors)){
		$clinic_id		= $_SESSION['LOGGEDIN_MEMBER_ID'];
		$to				= $_REQUEST['email'];
		$chkemail		=mysql_query("select * from `patient_invitations` where email='$to' && clinic_id='$clinic_id'");
		$havemail		=mysql_num_rows($chkemail);
		if($havemail){
		$vcode			= getSingleColumn('verification_code',"select * from `patient_invitations` where `email`='$to' && clinic_id='$clinic_id'");
		}
		else {
		$vcode			= genRandomString();
		}
		 $vc			= base64_encode($vcode);
		 $ci			= base64_encode($clinic_id);
		 $bc_namef		= getSingleColumn('firstname',"select * from `users` where `id`='".$clinic_id."'");
		 $bc_namel		= getSingleColumn('lastname',"select * from `users` where `id`='$clinic_id'");
		$bc_cl_id		= getSingleColumn('clinicid',"select * from `users` where `id`='$clinic_id'");
		$bc_cl_email	= getSingleColumn('email',"select * from `users` where `id`='$clinic_id'");
		$bc_cl_name		= getSingleColumn('clinicname',"select * from `clinic` where `id`='$bc_cl_id'");




	$message= '<div style="text-align:center; margin:0px; background-color:#FFFFFF;" class="background">
			<table style="text-align:center; font-family:Georgia; color:#404040; line-height:160%; font-size:16px;" border="0" cellspacing="0" cellpadding="0" width="600" class="layout_background">
				<tr>
					<td style="margin:0px; padding:10px; color:#666; font-size:11px; font-family:Arial;	font-weight:normal; text-align:center; text-transform:lowercase;
			border:none 0px #FFF;" colspan="3">

						<span>Email not displaying correctly? <a style="color:#666; text-decoration:underline; font-weight:normal;" href="*|ARCHIVE|*" target="_blank">View it in your browser.</a></span>

					</td>
				</tr>
				<tr>
					<td id="lead_image" colspan="3">
						<img src="'.ABSOLUTE_PATH.'images/stethoscope.gif">
					</td>
				</tr>
				<tr>
				  <td id="lead_content" mc:edit="main" colspan="3">
				    <h1 style="font-size:54px; color:#000;	font-weight:normal;	font-family:Georgia; line-height:120%;	margin:10px 0;">Time for a Check-Up!</h1>
				    <p>Hello!</p>
<p>
Thanks for taking the time to complete your personalized health care record with our office. We are looking forward to working with you and want to make sure you have everything you need to be successful as you pursue your healthcare goals. By establishing your protected healthcare record, you will have the ability to access your information at anytime. Please feel free to contact our office if you have any questions. .</p>

				  </td>
				</tr>
				<tr>
					<td valign="top" style="color:#666; font-size:18px;	font-weight:normal;	font-family:Georgia;text-align:center; padding:0px 0px 40px 0px;" >
					<h2 style="color:#000; font-size:24px;font-weight:normal;font-style:normal;font-family:Georgia;	margin:30px 0 10px 0;">
	Your personalized assessment link is below:</h2>
<p>
	Dr. '.$bc_namef.'&nbsp;'.$bc_namel.'<br>
	'.$bc_cl_name.'<br>
	<a href="'.ABSOLUTE_PATH.'create_patient.php?ci='.$ci.'&vc='.$vc.'" target="_blank">Click here to start your health assessment</a></p><br>

					<p>Best of luck to a healthier you!<br>
				   Sincerely,<br>
				   Dr.&nbsp;'.$bc_namel.'</p>



</td>

				</tr>


			  <tr>
					<td style="background-color:#FFFFFF;border-top:1px solid #CCC;padding:20px;	font-size:10px;	color:#666;	line-height:100%;			font-family:Arial; 	text-align:center;" colspan="3" class="background">

					    <p>Copyright (C) 2013 Pangea All rights reserved.</p>

					</td>
				</tr>
			</table>
		</div>';


				//$sender		= ''.$bc_cl_email.'';
				$sender		=	'info@yourhealthsupport.com';
				$subject	= ''.$bc_cl_name.' Invitation';

				$headers	= 'MIME-Version: 1.0' . "\r\n";
				$headers	.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers	.= 'From: '.$bc_name.'<'. $sender .'>' . "\r\n";

				$sent	= 	mail($to,$subject,$message,$headers);

				if(!$havemail){
				$today=date("Y-m-d");
				$sql="INSERT INTO `patient_invitations` (`id` ,`clinic_id` ,`verification_code` ,`email` ,`date`)VALUES (NULL , '$clinic_id', '$vcode', '$to', '$today');";
				$succ=mysql_query($sql);
				}

				if ($sent) {
					$msg = "Invitation sent successfully!";
				} else {
					$msg="Please try again later";
				} // end if res

			}else {
			$msg = "Email can not be Empty";
			}
	}




?>

<style>
		/* Overlay */

	#simplemodal-overlay {

		background-color:#000;

		cursor:wait;

	}
	/* Container */

	#simplemo {

		color:#000;

		box-shadow:0 2px 3px #777;

		padding:12px;

		behavior: url(https://www.eventgrabber.com/css/PIE.htc);

	}



	#simplemo .simplemodal-data {
		padding:8px;

	}

	#simplemo code {
		background:#141414;

		border-left:3px solid #65B43D;

		color:#bbb;

		display:block;

		font-size:12px;

		margin-bottom:12px;

		padding:4px 6px 6px;

	}

	#simplemo a {

		color:#ddd;

	}

	#close {

		width:25px;

		height:29px;

		display:inline;

		z-index:3200;

		position:absolute;

		top:-15px;

		right:-16px;

		cursor:pointer;

	}

	#simplemo h3 {

		background: none repeat scroll 0 0 #F2F2F2;

		border-left: 1px solid #CCCCCC;

		border-right: 1px solid #CCCCCC;

		border-top: 1px solid #CCCCCC;

		color: #84B8D9;

		margin: 0;

		padding: 10px;

		text-align:left

	}

	.formfield{

		overflow:hidden;

		padding-top: 8px;

		}

	.formfield label{

		float: left;

		color: #666666;

		font-weight: bold;

		padding-right: 10px;

		text-align: right;

		width:115px;

		}

	.formfield input,.formfield select{

		width:232px;

		border: 1px solid #BDC7D8;

		height:18px;

		font-size: 11px;

		}



	.formfield select{

		height: 22px;

		padding: 2px;

		width: 234px;

		}

	.rsvpBox{

		border: 1px solid #C1C1C1;

		background:#FFFFFF;

		min-height:225px;

		padding-top:10px;

		}

</style>

<div id="ds">

  <div id="simplemo" class="">

    <div id="basic-modal-content" >


<form name="invite" method="post" action="" onsubmit="return checkRsvp();">
      <div id="rsvp">

        <h3>Invite Patient</h3>

        <!-- SET THE ACTION URL -->

        <div class="rsvpBox" align="center">


			<div style="height:30px; padding-top:15px;"> <?php echo '<span style="color:#ff0000">'.$msg.'</span>';  ?></div>

          <div class="formfield">

            <label for="firstname" style="width:15%; float:left;">Email:</label>

            <input type="text" name="email" value="<?php echo $email; ?>" id="email" style="height:30px; width:300px;" />

          </div>



          <div align="center" style="padding-top:30px" id="rsvp_submit">

            <input type="submit" name="submit" value="Send Invitiation" align="left" style="padding:7px 15px;" />

          </div>
			<div style="text-align:left; line-height:20px; font-size:14px; font-family:Arial; padding:15px 15px 0"> <strong>Invite Link : </strong><br /><span style="font-size:12px;"><?php echo ABSOLUTE_PATH; ?>/create_patient.php?ci=<?php echo base64_encode($_SESSION['LOGGEDIN_MEMBER_ID']); ?>&vc=<?php echo base64_encode('pangea') ?></span></div>

        </div>

      </div>
</form>
    </div>

  </div>

</div>