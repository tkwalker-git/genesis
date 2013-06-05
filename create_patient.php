<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

if(isset($_GET['vc']) && isset($_GET['ci']))
	{
		$vc=base64_decode($_GET['vc']);
		$ci=base64_decode($_GET['ci']);
		$sqla=mysql_query("select * from `patient_invitations` where `clinic_id`='$ci' && `verification_code`='$vc' && `status`='0'");
		$in_id_patient =  getSingleColumn("id","select * from `patient_invitations` where `clinic_id`='$ci' && `verification_code`='$vc' && `status`='0'");
		$have=mysql_num_rows($sqla);
		
	if(!$have)
		{
			$sucMessage = "Error: Your Link is Untrusted";
			sleep(4);
			echo "<script>window.location.href='user_login.php';</script>";
		}
	}

if($_GET["id"])
	{
		if (validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'patients',$_GET["id"]) =='false')
		echo "<script>window.location.href='clinic_manager.php';</script>";
	}


function genRandomString() 
	{
	    $length = 6;
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $string = "";    

	    for ($p = 0; $p < $length; $p++) 
	    	{
	        	$string .= $characters[mt_rand(0, strlen($characters))];
	        }
    return $string;
	}

if(isset($_GET['vc']) && isset($_GET['ci']))
	{
		$bc_clinic_id		= 	$ci;
	}
	else 
		{
			$bc_clinic_id		=	$_SESSION['LOGGEDIN_MEMBER_ID'];
		}

$bc_arr_clinic_state	=	array();
$arrRES = mysql_query("select id,state from usstates");
while ($bc_row = mysql_fetch_assoc($arrRES) )
	$bc_arr_clinic_state[$bc_row["id"]] = $bc_row["state"];
	

 $bc_user_name		= $_POST["user_name"];
 $bc_first_name		= $_POST['first_name'];
 $bc_last_name		= $_POST['last_name'];
 $bc_password		= $_POST['password'];
 $bc_dob			= $_POST['dob'];
 if($_REQUEST['dob']){
 $bc_dob1		    =   date("Y-m-d",strtotime($bc_dob));
 }
 $bc_address		= $_POST['address'];
 $bc_city			= $_POST['city'];
 $bc_state			= $_POST['state'];
 $bc_zip			= $_POST['zip'];
 $bc_gender			= $_POST['gender'];
 $bc_phone			= $_POST['phone'];
 $bc_email			= $_POST['email'];
 $bc_comments		= DBin($_POST["comments"]);


 
if(isset($bc_dob))
	//$bc_dob 			= date("Y-m-d", strtotime($dob));
	$bc_refer_by		=	$_POST['refer_by'];
	$bc_status			=	1;

		if($bc_address == 'Address')
			$bc_address	=	'';
		
		if($bc_city == 'City')
			$bc_city	=	'';
		
		if($bc_zip == 'Zip / Postal Code')
			$bc_zip	=	'';
		


$frmID		=	$_GET["id"];
$action1	= isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();

if($frmID)
	$sql_id	= " && `id`!='". $frmID ."'";
	$is_email_already	= getSingleColumn("id","select * from `patients` where `email`='". $_POST['email'] ."' ". $sql_id ."");

if ($bc_user_name == "")
	$errors[] = "Username is blank: Please enter a username";
	
if(strlen($bc_password) < 8)
	$errors[] = 'For security reasons, your password must be at least 8 characters long!';
	
if (strpos($bc_password, " ") !== false)
	$errors[] = 'Password Invalid: Your password cannot include spaces!';
	
if (!preg_match("/[0-9]/", $password))
	$errors[] = 'Password Invalid: Your password must include at least one number!';
	
if (!preg_match("/[a-z]/i", $bc_password))
	$errors[] = 'Password Invalid: Your password must include at least one letter!';

if ($bc_first_name == "")
	$errors[] = "First Name is blank: Please enter your First Name";	
	
if ($bc_last_name == "")
	$errors[] = "Last Name is blank: Please enter your Last Name";

if ($bc_email == "")
	$errors[] = "Email is blank: Please enter your email address";
	
if(!filter_var($bc_email, FILTER_VALIDATE_EMAIL))
	$errors[] = "Invalid Email Address: Please enter a valid email address";

if($is_email_already)
	$errors[] = 'This email address is already exist. Please select another email address';
	
if ($bc_address == "")
	$errors[] = "Address is blank: Please enter your address";

if ($bc_city == "")
	$errors[] = "City is blank: Please enter your city";
	
if ($bc_zip == "")
	$errors[] = "Zipcode is blank: Please enter a valid zipcode";

if ($bc_phone == "")
	$errors[] = "Phone Number is blank: Please enter a valid phone number";


$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
	
	for ($i=0;$i<count($errors); $i++) 
		{
			$err .= '<li>' . $errors[$i] . '</li>';
		}
	$err .= '</ul></td></tr></table>';	

	if (isset($_POST["submit"]) ) 
		{
			if (!count($errors))
				{	
					$bc_genensys_user_id = addpatient('pangeafinal2',$bc_first_name,$bc_last_name,$bc_gender,$bc_dob,$bc_address,$bc_city,$bc_state,$bc_zip,$bc_phone,$bc_email,$bc_user_name,$bc_password);
					$novi_id = get_novi_id('admin','Meghal123',1,$bc_first_name,$bc_last_name,$bc_user_name,$bc_email,$bc_password);

					if($novi_id != '' && $novi_id != 'nologin')
						{
							$bc_novi_id	= $novi_id;
						}
		 
					if ($action1 == "save") 
						{
							/*
if($bc_genensys_user_id != -1)
								{
*/
									if($bc_novi_id)
									{
									$bc_created_date	= 	date("Y-m-d");
									$sql	=	"INSERT INTO `patients` (`id`,`username`, `password`, `genensysuserid`,`novi_id`,`clinicid`, `lastname`, `firstname`, `dob`, `sex`, `address`, `city`, `state`, `zip`, `phone`,`createdby`, `createddate`, `email`, `enabled`, `comments`, `primary`, `affiliatemarketingcode`) VALUES (NULL, '".$bc_user_name."', '". $bc_password ."', '". $bc_genensys_user_id ."', '". $bc_novi_id ."', '". $bc_clinic_id ."', '". $bc_last_name ."', '". $bc_first_name ."', '". $bc_dob1 ."', '". $bc_gender ."', '". $bc_address ."', '". $bc_city ."', '". $bc_state ."', '". $bc_zip ."', '". $bc_phone ."',NULL, '". $bc_created_date ."', '". $bc_email ."', '". $bc_status ."', '". $bc_comments ."', '". $bc_primary ."', '". $bc_affiliate_marketing_code ."');";
			
									$res			= mysql_query($sql);
									$frmID			= mysql_insert_id();
									$bc_patient_id	= $frmID;
			
										if($_GET['ci'] || $_GET['vc'] )
											{						
												echo "<script>window.location.href='user_login.php';</script>";
											}
													
											if ($res) 
												{
													$sucMessage = "Record Successfully inserted.";
													echo "<script>window.location.href='clinic_manager.php?p=patient&id=$frmID';</script>";
												} 
											else 
												{
													$sucMessage = "Error Code 1: Please try Later";
												} // end if res
								}
								else 
									{
										$sucMessage = "Error Code 2: Novi Interface not responding ";
									}
									
							/*
}
							else 
								{
									$sucMessage = "Error Code 3: EMR Interface not responding ";
								}
*/	
			
						} // end if for save function
		
					if ($action1 == "edit") 
						{
							mysql_query("DELETE from `patient_protocols` where `patient_id`='".$frmID."'");
							mysql_query("DELETE from `patient_tests` where `patient_id`='".$frmID."'");
							mysql_query("DELETE from `patient_supplements` where `patient_id`='".$frmID."'");
					
							$sql	=	"UPDATE `patients` SET `password` = '". $bc_password ."', `firstname` = '". $bc_first_name ."', `lastname` = '". $bc_last_name ."', `dob` = '". $bc_dob1 ."', `sex` = '". $bc_gender ."', `address` = '". $bc_address ."', `city` = '". $bc_city ."', `state` = '". $bc_state ."', `zip` = '". $bc_zip ."', `phone` = '". $bc_phone ."', `email` = '". $bc_email ."', `enabled` = '". $bc_status ."', `primary` = '". $bc_primary ."', `comments` = '". $bc_comments ."', `affiliatemarketingcode` = '". $bc_affiliate_marketing_code ."' WHERE `id` = '". $frmID ."'";
		
							$res			=	mysql_query($sql);
							$bc_patient_id	= $frmID;
							
							if ($res) 
								{
									$sucMessage = "Record Successfully updated.";
									echo "<script>window.location.href='clinic_manager.php?p=patient&id=$frmID';</script>";
								} 
							else 
								{
									$sucMessage = "Error Code 3: Please try Later";
								} // end if res
						} // end if for edit function
				} // end if errors

		else 
			{
				$sucMessage = $err;
			}
		} // end if submit


$sql	=	"select * from patients where id=$frmID";
$res	=	mysql_query($sql);
if ($res) 
	{
		if ($row = mysql_fetch_assoc($res))
			{	
				$genensys_user_id	= '';
				$bc_first_name		= $row['firstname'];
				$bc_last_name		= $row['lastname'];
				$bc_dob				= date("m/d/Y",strtotime(str_replace("-","/",$bc_dob)));
				$bc_address			= $row['address'];
				$bc_city			= $row['city'];
				$bc_state			= $row['state'];
				$bc_zip				= $row['zip'];
				$bc_gender			= $row['gender'];
				$bc_phone			= $row['phone'];
				$bc_email			= $row['email'];
				$bc_comments		= $row['comments'];
				$bc_patient_id		= $row['id'];
				
				if ( $bc_dob == '0000-00-00' )
					$bc_dob = '';
		
				if ( $bc_dob != '' )
					$bc_dob = date("m/d/Y",strtotime($bc_dob));
				
				$res2	= mysql_query("select * from `patient_protocols` where `patient_id`='". $bc_patient_id ."'");
					while($row2 = mysql_fetch_array($res2))
						$bc_protocols[]		=	$row2['protocol_id'];
					
				$res3	= mysql_query("select * from `patient_tests` where `patient_id`='". $bc_patient_id ."'");
					while($row3 = mysql_fetch_array($res3))
						$bc_tests[]		=	$row3['test_id'];
								
				$res4	= mysql_query("select * from `patient_supplements` where `patient_id`='". $bc_patient_id ."'");
					while($row4 = mysql_fetch_array($res4))
						$bc_supplements[]		=	$row4['supplement_id'];
		
			} // end if row
		$action = "edit";
	} // end if

	$meta_title	= "Patient Registration";
		
	include_once('includes/header.php');
	
?>


<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.maskedinput.js"></script>
<script type="text/javascript">
		
		
$(document).ready(function()
	{
		//the min chars for username
		var min_chars = 3;
		//result texts
		var characters_error = 'Minimum amount of chars is 3';
		var checking_html = 'Checking...';
		//when button is clicked
		$('#user_name').keyup(function()
		{
			//run the character number check
			if($('#user_name').val().length < min_chars)
				{
					//if it's bellow the minimum show characters_error text '
					$('#username_availability_result').html(characters_error);
				}
			else
				{
					//else show the cheking_text and run the function to check
					$('#username_availability_result').html(checking_html);
					check_availability();
				}
		});
	});

//function to check username availability
function check_availability()
	{
		//get the username
		var username = $('#user_name').val();
		//use ajax to run the check
		$.post("check_username.php", { username: username },
			function(result)
				{
					//if the result is 1
					if(result == 1)
						{
						//show that the username is available
						$('#username_availability_result').html(username + ' is Available');
						$('#username_availability_result').css("color","black");
						}
					else
						{
							//show that the username is NOT available
							$('#username_availability_result').html(username + ' is Not Available');
							$('#username_availability_result').css("color","red");
						}
				});

	}
	
jQuery(function($){
   $("#date").mask("99/99/9999");
   $("#phone").mask("(999) 999-9999");
   $("#tin").mask("99-9999999");
   $("#ssn").mask("999-99-9999");
});

	</script>


<style>


#accordion h3 {
    border-bottom: 1px solid #89C76F;
    border-radius: 5px 5px 5px 5px;
    color: #FFFFFF;
	background: none repeat scroll 0 0 #43BB9A;
    font-size: 18px;
    font-weight: bold;
    margin: 0;
    padding: 10px;
}

.bxs{
	float:left;
	width:25%;
	}

</style>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
<div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Patient Registration</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
	<?php if(($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor') && $_SESSION['LOGGEDIN_MEMBER_ID']>0){?>
	
	<?php } ?>
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
        <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
        <div class="clr"></div>
        <?php include('dashboard_menu_tk.php'); ?>
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">

                <h3 style="cursor:default"><span>Patient Profile</span></h3>
                <div id="box" class="box">
               
                <div class="bxs">
                  <div id="head">Username</div>
                  <div>
                    <input type="text" name="user_name" style="color:#000" id="user_name" class="new_input" value=""/>
					<div id='username_availability_result'></div> 
                  </div>
                </div>
               
                
              
                  
                <div class="bxs">
                  <div id="head">Password </div>
                  <div>
                   
					 <input type="text" name="password" style="color:#000" id="password" class="new_input" value="<?php echo $bc_password; ?>"/>
                  </div>
                </div>
                  <div class="bxs">
                  <div id="head">First Name</div>
                  <div>
                    <input type="text" name="first_name" style="color:#000" id="first_name" class="new_input" value="<?php echo $bc_first_name; ?>"/>
					
                  </div>
                </div>
                <div class="bxs">
                  <div id="head">Last Name</div>
                  <div>
                    <input type="text" name="last_name" style="color:#000" id="last_name" class="new_input" value="<?php echo $bc_last_name; ?>"/>
                  </div>
                </div>
                
                

                <div class="clear"><br /></div>
				
				 <div class="bxs">
                  <div id="head">Email</div>
                  <div>
                    <input type="text" name="email" style="color:#000" id="email" class="new_input" value="<?php echo $bc_email; ?>"/>
					 
                  </div>
                </div>
				
				
				<div class="bxs">
                <div id="head">DOB</div>
                	<div>
                    	<input type="text" class="new_input" name="dob" id="dob" value="<?php if($bc_dob){ echo $bc_dob;} ?>" />
                	</div>
                </div>
				
				 
				 
				  <div class="bxs">
                    <div id="head">Gender</div> 
                    <div>
                        <select style="width:184px;" class="new_input" name="gender">
                            <option value="male" <?php if($bc_gender == 'male'){ echo 'selected="selected"'; } ?>>Male</option>
                            <option value="female" <?php if($bc_gender == 'female'){ echo 'selected="selected"'; } ?>>Female</option>
                        </select>
                     </div>
                 </div>
                 
                 <div class="bxs">
                     <div id="head">Address</div>
                     <div>
                         <input type="text" name="address" id="address" class="new_input" value="<?php if ($bc_address){ echo $bc_address; } ?>">
                     </div>
                 </div>
				 
				 <div class="clear"><br /></div>
				 
                 
                 <div class="bxs">
                     <div id="head">City</div>
                     <div>
                         <input type="text" name="city" id="city" class="new_input" value="<?php if ($bc_city){ echo $bc_city; } ?>">
                     </div>
                 </div>

                 <div class="bxs">
                     <div id="head">State</div>
                     <div>
					 
          <select name="state" style="width:150px" class="new_input" id="state">
	<?php 
	foreach($bc_arr_clinic_state as $key => $val)
	{
		if ($key == $bc_state)
			$sel = "selected";
		else
			$sel = "";	
	?>
	<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo ucwords(strtolower($val)); ?> </option>
	<?php } ?>
	</select>
						 
                     </div>
                 </div>
                 
                 <div class="bxs">
                     <div id="head">Zipcode</div>
                     <div>
                         <input type="number" name="zip" id="zip" class="new_input" maxlength="5" value="<?php if ($bc_zip){ echo $bc_zip; } ?>">
                     </div>
                 </div>
                 
                 
                 
                 
                
                 
                 <div class="bxs">
                    <div id="head">Phone Number</div> 
                    <div>
                    	<input type="text" class="new_input" name="phone" id="phone" value="<?php echo $bc_phone; ?>" />
                     </div>
                 </div>
                 
                
                 
                 <div class="clear"><br /></div>   
				 

               
                </div>
							

 
                 
                 
                  <div class="clear"><br /></div> 
                  <br /><br />


				
				        <div class="create_event_submited">
            <a href="<?php echo ABSOLUTE_PATH; ?>dr_patient.php"><input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" name="submit" value="Create Test" align="right" /></a>
            <input type="hidden" name="submit" value="Create Test" />        
        </div>
				
              </div>
            </div>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php');?>

<script language="javascript" type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
<script>
	$(function() {
		$( "#dob" ).datepicker({
			dateFormat: "mm/dd/yy",
			changeMonth: true,
			changeYear: true,
			yearRange: '1940:2011'
			});
	});
	
</script>