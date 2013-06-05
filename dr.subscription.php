<?php
include_once('admin/database.php');
include_once('site_functions.php');
include_once('admin/xmlparser.php');

$products = getChargifyProducts();

if ( $_POST["clinic_product"] != ""){
$prodi =$_POST["clinic_product"];
$bc_sub_title	=	$products[$prodi][0];
$bc_sub_des		=	$products[$prodi][2];
$bc_sub_price	=	$products[$prodi][1];
$bc_sub_int		=	$products[$prodi][3];
$bc_int_unt		=	$products[$prodi][4];
}


function genRandomString() {
    $length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = "";

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}
$bc_card_expire_date		=	DBin($_POST["card_expire_date"]);
$bc_year					=	DBin($_POST["year"]);
$bc_expiry_date				=	$bc_card_expire_date .'/'. $bc_year;
$bc_card_first_name			=	DBin($_POST["card_first_name"]);
$bc_card_last_name			=	DBin($_POST["card_last_name"]);
$bc_card_billing_address1	=	DBin($_POST["card_billing_address1"]);
$bc_card_billing_address2	=	DBin($_POST["card_billing_address2"]);
$bc_billing_city			=	DBin($_POST["billing_city"]);
$bc_billing_state			=	DBin($_POST["billing_state"]);
$bc_billing_zip				=	DBin($_POST["billing_zip"]);
$bc_billing_country			=	DBin($_POST["billing_country"]);
$bc_card_w					=	DBin($_POST["card_w"]);
$bc_clinic_product			=	DBin($_POST["clinic_product"]);

$bc_card_number				=	DBin($_POST["card_number"]);
$bc_card_type				=	DBin($_POST["card_type"]);
$bc_doctor_username			=	DBin($_POST["doctor_username"]);
$bc_doctor_password			=	DBin($_POST["doctor_password"]);
$bc_GenensysUserID			=	DBin($_POST["GenensysUserID"]);
$bc_doctor_last_name		=	DBin($_POST["doctor_last_name"]);
$bc_doctor_first_name		=	DBin($_POST["doctor_first_name"]);
$bc_dob					    =	$_POST["dob"];
$bc_subcription_type		=	DBin($_POST["subcription_type"]);
$bc_dob					    =   date("Y-m-d",strtotime(str_replace("/","-",$bc_dob)));
$bc_doctor_gender			=	DBin($_POST["doctor_gender"]);
$bc_arr_doctor_gender		=	array("Male" => "Male", "FeMale" => "FeMale");
$bc_clinic_address1			=	DBin($_POST["clinic_address1"]);
$bc_clinic_city				=	DBin($_POST["clinic_city"]);
$bc_clinic_state			=	DBin($_POST["clinic_state"]);
$bc_clinic_zip				=	DBin($_POST["clinic_zip"]);
$bc_clinic_phone1			=	DBin($_POST["clinic_phone1"]);
$bc_CreatedBy				=	'';
$bc_CreatedDate				=	date("Y-m-d");
$bc_doctor_email			=	DBin($_POST["doctor_email"]);
$bc_Enabled					=   1;
$bc_AffiliateMarketingCode	=	genRandomString();
$bc_coupon					=	DBin($_POST["coupon"]);
$usertype					=	1;
$get_mon =explode('-',$bc_card_expire_date);
$bc_month = trim($get_mon[0]);

$register_date				=	date("Y-m-d");

if($bc_subcription_type == 1)
	$expiry_date	=	date("Y-m-d" , strtotime(date("Y-m-d", strtotime($register_date)) . " +1 year"));
else
	$expiry_date	=	date("Y-m-d" , strtotime(date("Y-m-d", strtotime($register_date)) . " +1 months"));


if($bc_coupon){
$affiliate_id=getSingleColumn("id","select * from `users` where `affiliatemarketingcode`='$bc_coupon'");
}


$bc_clinic_name			=	DBin($_POST["clinic_name"]);
$bc_clinicid		    =	DBin($_POST["clinic_id"]);
$bc_clinic_address1		=	DBin($_POST["clinic_address1"]);
$bc_clinic_address2		=	DBin($_POST["clinic_address2"]);
$bc_clinic_city			=	DBin($_POST["clinic_city"]);
$bc_clinic_state		=	DBin($_POST["clinic_state"]);

$bc_arr_clinic_state	=	array();
$arrRES = mysql_query("select id,state from usstates");
while ($bc_row = mysql_fetch_assoc($arrRES) )
	$bc_arr_clinic_state[$bc_row["id"]] = $bc_row["state"];

$bc_arr_clinic	=	array();
$arrRES = mysql_query("select id,clinicname from clinic");
while ($bc_row = mysql_fetch_assoc($arrRES) )
	$bc_arr_clinic[$bc_row["id"]] = $bc_row["clinicname"];

$bc_clinic_zip		=	DBin($_POST["clinic_zip"]);
$bc_clinic_phone1	=	DBin($_POST["clinic_phone1"]);
$bc_clinic_phone2	=	DBin($_POST["clinic_phone2"]);
$bc_clinic_fax1		=	DBin($_POST["clinic_fax1"]);
$bc_clinic_fax2		=	DBin($_POST["clinic_fax2"]);
$bc_clinic_web		=	DBin($_POST["clinic_web"]);

$sucMessage = "";

$errors = array();
/*if ( $_POST["clinic_product"] == "")
	$errors[] = "Package: can not be empty";*/
if ( $_POST["clinic_name"] == "")
	$errors[] = "ClinicName: can not be empty";
if(!filter_var($_POST["doctor_email"], FILTER_VALIDATE_EMAIL))
	$errors[] = "Email: Email not valid";
if(strlen($_POST["doctor_password"]) < 8 )
	$errors[] = "Password: Password must be at least 8 characters long";
if($_POST["doctor_password"] != $_POST["doctor_password1"])
	$errors[] = "Password: Both password not Match";
if ($_POST["clinic_address1"] == "")
	$errors[] = "Address1: can not be empty";
if ($_POST["clinic_city"] == "")
	$errors[] = "City: can not be empty";
if ($_POST["clinic_state"] == "")
	$errors[] = "State: can not be empty";
if ($_POST["clinic_zip"] == "")
	$errors[] = "Zip: can not be empty";
if ($_POST["clinic_phone1"] == "")
	$errors[] = "Phone1: can not be empty";

if ($_POST["doctor_username"] == "")
	$errors[] = "Username: can not be empty";
if ($_POST["doctor_password"] == "")
	$errors[] = "Password: can not be empty";
if ($_POST["doctor_last_name"] == "")
	$errors[] = "LastName: can not be empty";
if ($_POST["doctor_first_name"] == "")
	$errors[] = "FirstName: can not be empty";
if ($_POST["doctor_gender"] == "")
	$errors[] = "Gender: can not be empty";
if ($_POST["doctor_email"] == "")
	$errors[] = "Email: can not be empty";



$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';




if (isset($_POST["submit"]) )
	{

	if (!count($errors))
		{
			if($_POST["subcription_type"] == 1){
				require('includes/AuthnetARB.class.php');
				
				try
				{
				
					$subscription = new AuthnetARB('48zC2wJr2S8p', '69Cx7SKRf4bJ826V', AuthnetARB::USE_DEVELOPMENT_SERVER);
					$subscription->setParameter('amount', "14.95");
					$subscription->setParameter('cardNumber', '4111111111111111');
					$subscription->setParameter('expirationDate', '2016-12');
					$subscription->setParameter('firstName', $bc_doctor_first_name);
					$subscription->setParameter('lastName', $bc_doctor_last_name);
					$subscription->setParameter('address', $bc_clinic_address1);
					$subscription->setParameter('city', $bc_clinic_city);
					$subscription->setParameter('state', $bc_clinic_state);
					$subscription->setParameter('zip', $bc_clinic_zip);
					$subscription->setParameter('email', $bc_doctor_email);
					$subscription->setParameter('subscrName', "". $bc_doctor_first_name . $bc_doctor_last_name ."");
	
					// Create the subscription
					$subscription->createAccount();
					
					if ($subscription->isSuccessful())
					{
						$subscription_id = $subscription->getSubscriberID();
						$sql = "INSERT INTO `doctors` (`first_name` , `last_name` , `email` , `username` , `password` , `sex` , `dob` , `subscription` , `auth_subscription_id` , `register_date` , `expiry_date`) VALUES ('". $bc_doctor_first_name ."' , '". $bc_doctor_last_name ."' , '". $bc_doctor_email ."' , '". $bc_doctor_username ."' , '". $bc_doctor_password ."' , '". $bc_doctor_gender ."' , '". $bc_dob ."' , '". $bc_subcription_type ."' , '". $subscription_id ."' , '". $register_date ."' , '". $expiry_date ."')";
						$res = mysql_query($sql);
						
						if($res){
							
							$insert_id = mysql_insert_id();
	
							$sql_clinic = "INSERT INTO `clinics` (`user_id` , `clinicname` , `address1` , `address2` , `city` , `state` , `zip` , `phone1` , `phone2` , `fax1` , `fax2` , `website`) VALUES ('". $insert_id ."' , '". $bc_clinic_name ."' , '". $bc_clinic_address1 ."' , '". $bc_clinic_address2 ."' , '". $bc_clinic_city ."' , '". $bc_clinic_state ."' , '". $bc_clinic_zip ."' ,  '". $bc_clinic_phone1 ."' , '". $bc_clinic_phone2 ."' , '". $bc_clinic_fax1 ."' , '". $bc_clinic_fax2 ."' , '". $bc_clinic_web ."')";
	
							$res_clinic = mysql_query($sql_clinic);
	
							if($res_clinic){
							
								
								$_SESSION['logedin'] = '1';
								$_SESSION['LOGGEDIN_MEMBER_ID'] = $insert_id;
								$_SESSION['usertype'] = 'doctor';
								
								
								$to = $bc_doctor_email;
								$name = $bc_doctor_first_name .' '. $bc_doctor_last_name;
								$subject = "You have subscribed successfully";
								$message = 'Dear '. $name .' ,<br><br>';
								$message .= 'You are subscribed successfully with 12 Months Subscription .<br>';
								$message .= 'Your login informations are below:<br><br>';
								$message .= 'Email:'. $bc_doctor_email .'<br>';
								$message .= 'Password:'. $bc_doctor_password .'<br><br>';
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
				
				
								$ok = @mail($to, $subject, $message, implode("\r\n", $headers));
								
		
								echo "<script>window.location.href='". ABSOLUTE_PATH ."dashboard.php';</script>";	
								
								
							}else{
								
							
								$sucMessage = "Error: Client Information does not inserted!";	
								
							} //end if $res_clinic
									
									
						}else{
							
							$sucMessage = "Error: Doctor Information does not inserted!";
							
						} //end if $res
						
						
						
					}else{
						
						$sucMessage = "Error: Subscription does not successfull please try again later!";
						
					} //end if $subscription->isSuccessful()
						
				}
				catch(AuthnetARBException $e)
				{
				
				}
				
			}else{


				$sql = "INSERT INTO `doctors` (`first_name` , `last_name` , `email` , `username` , `password` , `sex` , `dob` , `subscription` , `register_date` , `expiry_date`) VALUES ('". $bc_doctor_first_name ."' , '". $bc_doctor_last_name ."' , '". $bc_doctor_email ."' , '". $bc_doctor_username ."' , '". $bc_doctor_password ."' , '". $bc_doctor_gender ."' , '". $bc_dob ."' , '". $bc_subcription_type ."' , '". $register_date ."' , '". $expiry_date ."')";
				$res = mysql_query($sql);
	
				if($res){
	
					$insert_id = mysql_insert_id();
	
					$sql_clinic = "INSERT INTO `clinics` (`user_id` , `clinicname` , `address1` , `address2` , `city` , `state` , `zip` , `phone1` , `phone2` , `fax1` , `fax2` , `website`) VALUES ('". $insert_id ."' , '". $bc_clinic_name ."' , '". $bc_clinic_address1 ."' , '". $bc_clinic_address2 ."' , '". $bc_clinic_city ."' , '". $bc_clinic_state ."' , '". $bc_clinic_zip ."' ,  '". $bc_clinic_phone1 ."' , '". $bc_clinic_phone2 ."' , '". $bc_clinic_fax1 ."' , '". $bc_clinic_fax2 ."' , '". $bc_clinic_web ."')";
	
					$res_clinic = mysql_query($sql_clinic);
	
					if($res_clinic){
	
						if($bc_subcription_type == 1){
	
							//$sql_billing = "INSERT INTO `doctors_billing_informations` (`doc_id` , `card_no` , `cvv` , `expiry_date` , `card_type` , `first_name` , `last_name` , `address_1` , `address_2` , `city` , `state` , `zip` , `country`) VALUES ('". $insert_id ."' , '". $bc_card_number ."' , '". $bc_card_w ."' , '". $bc_expiry_date ."' , '". $bc_card_type ."' , '". $bc_card_first_name ."' , '". $bc_card_last_name ."' , '". $bc_card_billing_address1 ."' , '". $bc_card_billing_address2 ."' , '". $bc_billing_city ."' , '". $bc_billing_state ."' , '". $bc_billing_zip ."' , '". $bc_billing_country ."')";
							//$res_billing = mysql_query($sql_billing);
							
							
	
							$_SESSION['logedin'] = '1';
							$_SESSION['LOGGEDIN_MEMBER_ID'] = $insert_id;
							$_SESSION['usertype'] = 'doctor';
	
							echo "<script>window.location.href='". ABSOLUTE_PATH ."dashboard.php';</script>";
	
	
	
						}else{
	
	
							$sucMessage = "Error: Please try again later!";
	
						}
	
						echo "<script>window.location.href='". ABSOLUTE_PATH ."dashboard.php';</script>";
	
						$sucMessage = "Record Inserted!";
	
					}
	
				}
			
			} //end else free

	}
	else
		{
			$sucMessage = $err;
		}
}

include_once('includes/header.php');

?>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.accordion.js"></script>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.maskedinput.js"></script>
<script type="text/javascript">
jQuery(function($){
   $("#date").mask("99/99/9999");
   $("#clinic_phone1").mask("(999) 999-9999");
   $("#clinic_phone2").mask("(999) 999-9999");
   $("#clinic_fax1").mask("(999) 999-9999");
   $("#clinic_fax2").mask("(999) 999-9999");
});

</script>

<script>

function getclinic(val){

if(val >= 1){
 $.post("loadclinic.php", {centerid:val},function(data) {

     var da = data.split('|');

     if(da){
     $("#clinic_name").val(da[0]).attr("readonly", "readonly");
     }
	  if(da){
     $("#clinic_address1").val(da[1]).attr("readonly", "readonly");
     }
     if(da){
     $("#clinic_address2").val(da[2]).attr("readonly", "readonly");
     }
     if(da){
     $("#clinic_city").val(da[3]).attr("readonly", "readonly");
     }
     if(da){
     $("#clinic_state").val(da[4]).attr("readonly", "readonly");
     }
	 if(da){
     $("#clinic_zip").val(da[5]).attr("readonly", "readonly");
     }
	 if(da){
     $("#clinic_phone1").val(da[6]).attr("readonly", "readonly");
     }
	 if(da){
     $("#clinic_phone2").val(da[7]).attr("readonly", "readonly");
     }
	 if(da){
     $("#clinic_fax1").val(da[8]).attr("readonly", "readonly");
     }
	 if(da){
     $("#clinic_fax2").val(da[9]).attr("readonly", "readonly");
     }
	  if(da){
     $("#clinic_web").val(da[10]).attr("readonly", "readonly");
     }
    }
   )
   }else {
   $("#clinic_name ,#clinic_address1 ,#clinic_address2,#clinic_city ,#clinic_state,#clinic_zip,#clinic_phone1,#clinic_phone2,#clinic_fax1,#clinic_fax2,#clinic_web").removeAttr("readonly");
   $("#clinic_name").css("display", "block");
    $("#clinic_name ,#clinic_address1 ,#clinic_address2,#clinic_city ,#clinic_state,#clinic_zip,#clinic_phone1,#clinic_phone2,#clinic_fax1,#clinic_fax2,#clinic_web").val("");
   }
};
</script>


   <script>
	$(function() {
		$( "#dob" ).datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			yearRange: '1940:2013'
			});
	});

</script>
<style>

.ev_title input{
	color: #808080;
	font-weight:normal;
	}

.new_ticket_right td{
	height:48px;
	padding:0 16px;
	}

.ev_new_box_center{
	margin:auto;
	width:936px;
	}

.ev_new_box_center .basic_box, .ev_new_box_center .featured_box, .ev_new_box_center .premium_box, .ev_new_box_center .custom_box{
	width:234px;
	height:528px;
	float:left;
	position:absolute
	}


.ev_new_box_center .basic_box ul, .ev_new_box_center .featured_box ul, .ev_new_box_center .premium_box ul, .ev_new_box_center .custom_box ul{
	padding:10px 0 0 18px;
	margin:0
}

.ev_new_box_center .basic_box ul li, .ev_new_box_center .featured_box ul li, .ev_new_box_center .premium_box ul li, .ev_new_box_center .custom_box ul li{
	list-style:circle;
	font-size:12px
}

.ev_new_box_center .basic_box{
	background:url(images/basic_box.gif) no-repeat;
	}

.ev_new_box_center .featured_box{
	background:url(images/featured_box.gif) no-repeat;
	left:234px;
	}

.ev_new_box_center .premium_box{
	background:url(images/premium_box.gif) no-repeat;
	left:468px;
	}

.ev_new_box_center .custom_box{
	background:url(images/custom_box.gif) no-repeat;
	left:702px;
	}


.ev_new_box_center .basic_box .black, .ev_new_box_center .featured_box .black, .ev_new_box_center .premium_box .black, .ev_new_box_center .custom_box .black{
	filter:alpha(opacity=15);
	-ms-filter:alpha(opacity=15);
	-moz-opacity:0.15;
	opacity:0.15;
	background:#000000;
	width:234px;
	height:528px;
	position:absolute;
	}


.ev_new_box_center .black:hover{
	display:none;
	}

.ev_new_box_center .basic_box:hover > .black, .ev_new_box_center .featured_box:hover > .black, .ev_new_box_center .premium_box:hover > .black, .ev_new_box_center .custom_box:hover > .black{
	display:none;
	}


.ev_new_box_center .basic_box:hover, .ev_new_box_center .featured_box:hover, .ev_new_box_center .premium_box:hover, .ev_new_box_center .custom_box:hover{
	z-index:9999;
	-moz-box-shadow:0px 0px 7px 2px #464646;
	-webkit-box-shadow:0px 0px 7px 2px #464646;
	-khtml-box-shadow:0px 0px 7px 2px #464646;
	box-shadow:0px 0px 7px 2px #464646;
	filter: progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=0),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=90),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=180),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=270);
	}
	.ev_new_box_center .detail{
	padding:132px 10px 0;
	height:280px;
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	line-height:18px;
}

#showimg1,#showimg3{
	padding: 5px 0 5px 20px;
	width: 45%
	}

#showimg2,#showimg4{
	padding: 5px 0 5px 27px;
	width:45%;
	}


#accordion h3 {
    border-bottom: 1px solid #89C76F;
    border-radius: 5px 5px 5px 5px;
    color: #FFFFFF;
    cursor: pointer;
	background: none repeat scroll 0 0 #43BB9A;
    font-size: 18px;
    font-weight: bold;
    margin: 0;
    padding: 10px;
}

</style>
<div class="topContainer">
<div class="welcomeBox"></div>




  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Genesis Doctor Subscription <br />
	<!-- <span style="font-size:26px;">Annual Subscription $107.00 / Month</span> --></div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">



        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">
				<form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">

								<!--contact information end-->
    <h3>Step 1 : <span>Contact Information :</span></h3>
    	<div id="box" class="box">
    		<table width="92%" border="0" cellspacing="15" cellpadding="0" align="center">
                <tr>
                    <td width="46%">
                    <strong>* Doctor First Name</strong>:<br /><input type="text" name="doctor_first_name" class="new_input1" value="<?php echo $bc_doctor_first_name; ?>" />	</td>
                    <td width="8%">&nbsp;</td>
                    <td width="46%">
                    <strong>* Doctor Last Name</strong>:<br /><input type="text" name="doctor_last_name" class="new_input1" value="<?php echo $bc_doctor_last_name; ?>" />	</td>
                </tr>
                <tr>
                	<td><strong>* Email Address</strong>:<br /><input type="text" name="doctor_email" class="new_input1" value="<?php echo $bc_doctor_email; ?>" /></td>
                	<td>&nbsp;</td>
                <td>
                    <strong>* Gender</strong>:<br />
                    <select style="width:200px" class="new_input" name="doctor_gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    </select>
                    </td>
                </tr>
                <tr>
                	<td><strong>* Date of Birth</strong>:<br /><input type="text" name="dob" id="dob" class="new_input1" value="" /></td>
                	<td>&nbsp;</td>
                	<td><strong>* Doctor User Name</strong>:<br /><input type="text" name="doctor_username" class="new_input1" value="<?php echo $bc_doctor_username; ?>" /></td>
                </tr>
                <tr>
                    <td><strong>* Password</strong>:<br />
                      <input type="password" name="doctor_password" class="new_input1" value="<?php echo $bc_doctor_password; ?>" /></td>
                    <td>&nbsp;</td>
                    <td><strong>* Confirm Password</strong>:<br />
                      <input type="password" name="doctor_password1" class="new_input1" value="<?php echo $bc_doctor_password; ?>" />
                    </td>
                </tr>
                <tr>
                	<td colspan="3">Password must be minimum of 6 characters in length. Must contain at least 1 special character like (? , !, * etc).</td>
                </tr>
                <tr>
                    <td><strong>* Clinic Name</strong>:<br />

                    <input type="text" name="clinic_name" class="new_input1" id="clinic_name" value="<?php echo $bc_clinic_name; ?>" /></td>
                    <td>&nbsp;</td>
                    <td>
                    <strong> Clinic Website</strong>:<br />
                    <input type="text" name="clinic_web" id="clinic_web"class="new_input1" value="<?php echo $bc_clinic_web; ?>" />
                    </td>
                </tr>
                <tr>
                	<td><strong>* Clinic Address 1</strong>:<br /><input type="text" name="clinic_address1" id="clinic_address1" class="new_input1" value="<?php echo $bc_clinic_address1; ?>" /></td>
                	<td>&nbsp;</td>
                	<td><strong>Clinic Address 2</strong>:<br /><input type="text" name="clinic_address2" id="clinic_address2" class="new_input1" value="<?php echo $bc_clinic_address2; ?>" /></td>
                </tr>

                <tr>
                	<td><strong>* City</strong>:<br />
                	<input type="text" name="clinic_city" id="clinic_city" class="new_input1" value="<?php echo $bc_clinic_city; ?>" /></td>
                	<td>&nbsp;</td>
                	<td>
                    	<table width="100%" border="0" cellspacing="5" cellpadding="0">
                			<tr>
                				<td><strong>* State</strong>:<br />
                                    <select name="clinic_state" style="width:150px" class="new_input" id="clinic_state">
                                    <?php
                                    foreach($bc_arr_clinic_state as $key => $val)
                                    {
                                    if ($key == $bc_clinic_state)
                                    $sel = "selected";
                                    else
                                    $sel = "";
                                    ?>
                                    <option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo ucwords(strtolower($val)); ?> </option>
                                    <?php } ?>
                                    </select>
                                 </td>
                				<td><strong>* Zip Code</strong>:<br /><input type="text" id="clinic_zip" name="clinic_zip" class="new_input" value="<?php echo $bc_clinic_zip; ?>" /></td>
                			</tr>
						</table>

					</td>
  				</tr>

                <tr>
                	<td><strong>* Phone 1</strong>:<br /><input type="text" name="clinic_phone1" id="clinic_phone1" class="new_input1" value="<?php echo $bc_clinic_phone1; ?>" /></td>
                	<td>&nbsp;</td>
                	<td><strong>Phone 2</strong>:<br /><input type="text" name="clinic_phone2" id="clinic_phone2" class="new_input1" value="<?php echo $bc_clinic_phone2; ?>" /></td>
                </tr>

                <tr>
                	<td><strong>Fax 1</strong>:<br /><input type="text" name="clinic_fax1" id="clinic_fax1" class="new_input1" value="<?php echo $bc_clinic_fax1; ?>" /></td>
                	<td>&nbsp;</td>
                	<td><strong>Fax 2</strong>:<br /><input type="text" name="clinic_fax2" id="clinic_fax2" class="new_input1" value="<?php echo $bc_clinic_fax2; ?>" /></td>
                </tr>
			</table>

            <div style="padding: 20px 45px;">
        	<div style="line-height:45px;"><input style="margin-right:5px;" type="radio" name="subcription_type" value="0"  checked="checked" /><strong>Try 30 Days trail</strong></div>
            <div><input style="margin-right:5px;" type="radio" name="subcription_type" value="1" /><strong>12 Month Subscription</strong></div>
        </div>

	</div>

    <!--Billing information box-->
    <h3 style="display:none;">Step 2: <span>Billing Information :</span></h3>

	<div id="box" class="box billing_block">


            <table width="92%" border="0" cellspacing="15" cellpadding="0" align="center">

                <tr>
                    <td width="46%">
                    <strong>* Card Number</strong>:<br /><input type="text" name="card_number" class="new_input1" value="<?php echo $bc_card_number; ?>" />	</td>
                    <td width="8%">&nbsp;</td>
                    <td width="46%">
                    <strong>* Credit Card Type</strong>:<br />
                    <select style="width:200px" class="new_input" name="card_type">
                    <option  value=""></option>
                    <option <?php if($bc_card_type=='MC'){?> selected="selected" <?php } ?> value="MC">Master Card</option>
                    <option <?php if($bc_card_type=='VISA'){?> selected="selected" <?php } ?> value="VISA">Visa</option>
                    <option <?php if($bc_card_type=='AMEX'){?> selected="selected" <?php } ?> value="AMEX">American Express</option>
                    <option <?php if($bc_card_type=='DISC'){?> selected="selected" <?php } ?> value="DISC">Discover</option>
                    </select>
                    </td>
                </tr>

                <tr>
                <td valign="top">
                    <table width="100%" border="0" cellspacing="5" cellpadding="0">
                        <tr>
                            <td>
                                <strong>* CW</strong>:<br /><input type="text" name="card_w" style="width:70px;" class="new_input" value="<?php echo $bc_card_w; ?>" />
                            </td>
                                <td><strong>* Expiration Date</strong>:<br />
                                    <select style="width:100px" class="new_input" name="card_expire_date">
                                    <option value=""></option>
                                    <option <?php if($bc_card_expire_date==1){?> selected="selected" <?php } ?>value="1">1 - Jan</option>
                                    <option <?php if($bc_card_expire_date==2){?> selected="selected" <?php } ?>value="2">2 - Feb</option>
                                    <option <?php if($bc_card_expire_date==3){?> selected="selected" <?php } ?>value="3">3 - Mar</option>
                                    <option <?php if($bc_card_expire_date==4){?> selected="selected" <?php } ?>value="4">4 - Apr</option>
                                    <option <?php if($bc_card_expire_date==5){?> selected="selected" <?php } ?>value="5">5 - May</option>
                                    <option <?php if($bc_card_expire_date==6){?> selected="selected" <?php } ?>value="6">6 - Jun</option>
                                    <option <?php if($bc_card_expire_date==7){?> selected="selected" <?php } ?>value="7">7 - Jul</option>
                                    <option <?php if($bc_card_expire_date==8){?> selected="selected" <?php } ?>value="8">8 - Aug</option>
                                    <option <?php if($bc_card_expire_date==9){?> selected="selected" <?php } ?>value="9">9 - Sep</option>
                                    <option <?php if($bc_card_expire_date==10){?> selected="selected" <?php } ?>value="10">10 - Oct</option>
                                    <option <?php if($bc_card_expire_date==11){?> selected="selected" <?php } ?>value="11">11 - Nov</option>
                                    <option <?php if($bc_card_expire_date==12){?> selected="selected" <?php } ?>value="12">12 - Dec</option>
                                    </select>
                                </td>
                                <td><strong>* Year</strong>:<br />
                                    <select style="width:100px" class="new_input" name="year">
                                    <option value=""></option>
                                    <option <?php if($bc_year==2013){?> selected="selected" <?php } ?>value="2013">2013</option>
                                    <option <?php if($bc_year==2014){?> selected="selected" <?php } ?>value="2014">2014</option>
                                    <option <?php if($bc_year==2015){?> selected="selected" <?php } ?>value="2015">2015</option>
                                    <option <?php if($bc_year==2016){?> selected="selected" <?php } ?>value="2016">2016</option>
                                    <option <?php if($bc_year==2017){?> selected="selected" <?php } ?>value="2017">2017</option>
                                    <option <?php if($bc_year==2018){?> selected="selected" <?php } ?>value="2018">2018</option>
                                    <option <?php if($bc_year==2019){?> selected="selected" <?php } ?>value="2019">2019</option>
                                    <option <?php if($bc_year==2020){?> selected="selected" <?php } ?>value="2020">2020</option>
                                    <option <?php if($bc_year==2021){?> selected="selected" <?php } ?>value="2021">2021</option>
                                    <option <?php if($bc_year==2022){?> selected="selected" <?php } ?>value="2022">2022</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>&nbsp;</td>
                    <td><img src="<?php echo ABSOLUTE_PATH; ?>images/CreditCardLogos.jpg" align="" alt="" width="350" height=""  /></td>
                </tr>
                <tr>
                    <td>
                        <strong>* First Name on Card</strong>:<br /><input type="text" name="card_first_name" class="new_input1" value="<?php echo $bc_card_first_name; ?>" />
                    </td>
                    <td>&nbsp;</td>
                    <td><strong>* Last Name on Card</strong>:<br /><input type="text" name="card_last_name" class="new_input1" value="<?php echo $bc_card_last_name; ?>" /></td>
                </tr>
                <tr>
                    <td><strong>* Billing Address 1</strong>:<br /><input type="text" name="card_billing_address1" class="new_input1" value="<?php echo $bc_card_billing_address1; ?>" /></td>
                    <td>&nbsp;</td>
                    <td><strong>Billing Address 2</strong>:<br /><input type="text" name="card_billing_address2" class="new_input1" value="<?php echo $bc_card_billing_address2; ?>" /></td>
                </tr>
                <tr>
                    <td><strong>* Billing City</strong>:<br /><input type="text" name="billing_city" class="new_input1" value="<?php echo $bc_billing_city; ?>" /></td>
                    <td>&nbsp;</td>
                <td>
                    <strong>* Billing State</strong>:<br />
                    <select style="width:200px" class="new_input" name="billing_state">
                    <option value=""></option>
                    <?php
                    foreach($bc_arr_clinic_state as $key => $val)
                    {
                    if ($key == $bc_clinic_state)
                    $sel = "selected";
                    else
                    $sel = "";
                    ?>
                    <option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo ucwords(strtolower($val)); ?> </option>
                    <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>* Billing ZIP Code</strong>:<br />
                    <input type="text" name="billing_zip" class="new_input" value="<?php echo $bc_billing_zip; ?>" /></td>
                <td>&nbsp;</td>
                <td><strong>Billing Country</strong>:<br /><input type="text" name="billing_country" class="new_input1" value="<?php echo $bc_billing_country; ?>" /></td>
            </tr>
        </table>


    </div>
    <!--Billing information end-->





				<!--subscription type  start-->
				<!--coupon information start-->
				<br />

				<div align="center"><input type="submit" name="submit" value="Submit" style="padding:8px 30px; font-size:16px; font-weight:bold;"  /></div>


				<div>This is a 12 month renewing contract with no early cancellation. The renewal can be cancelled by a written notice atleast 90 days prior to contract ending period. By clicking the button below you agree and accept the <a href="#">Terms and Conditions</a>.</div>
				<!--coupon information end-->



                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
            <!--<input type="image" src="<?php //echo IMAGE_PATH; ?>publish_new.png" name="submit" value="Create Supplement" align="right" />
            <input type="hidden" name="submit" value="Create Supplement" />   -->
        </div>

    </div>
  </div>
</div>
<?php include_once('includes/footer.php');?>
<script>
	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "description",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen,|,print,|,ltr,rtl,|,styleprops,hr,removeformat,|,preview,help,code",
		theme_advanced_buttons3 : "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "style.css",
	});

	$(document).ready(function(){

		$("input[name=subcription_type]").click( function(){

			var val = $(this).val();


			/*if (typeof elem.onclick == "function") {
				elem.onclick.apply(elem);
			}*/

			if(val == 1){
				//$("#billing_block").css("display" , "block");
				$("#2").css("display" , "block");

				$("#box2").slideDown(500);
				$("#box1").slideUp(500);
			}else
				$("#billing_block").slideUp(500);
				//$("#billing_block").css("display" , "none");
		});

	});


</script>


<style>
.pro_duct {
text-align:left;
font-family:Verdana;
font-size:13px;
margin-bottom:20px;
padding-left:30px;
}
.pro_duct p {
text-align:left;
font-family:Verdana;
font-size:13px;

padding:3px 0 3px 25px;
margin:0;
}
</style>