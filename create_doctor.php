<?php 
include_once('admin/database.php'); 
include_once('site_functions.php');
//include_once('admin/xmlparser.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
if($_GET["id"]){	

if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'users',$_GET['id']) =='false'){
	echo "<script>window.location.href='clinic_manager.php?p=doctors';</script>";
	}
}
		

$bc_doctor_username			=	DBin($_POST["doctor_username"]);
$bc_doctor_password			=	DBin($_POST["doctor_password"]);
$bc_GenensysUserID			=	DBin($_POST["GenensysUserID"]);
$bc_doctor_last_name		=	DBin($_POST["doctor_last_name"]);
$bc_doctor_first_name		=	DBin($_POST["doctor_first_name"]);
$bc_doctor_birth			=	date('Y-m-d',strtotime($_POST["doctor_birth"]));
$bc_doctor_gender			=	DBin($_POST["doctor_gender"]);
$bc_arr_doctor_gender		=	array("Male" => "Male", "FeMale" => "FeMale");
$bc_doctor_address	    	=	DBin($_POST["doctor_address"]);
$bc_doctor_city				=	DBin($_POST["doctor_city"]);
$bc_doctor_state			=	DBin($_POST["doctor_state"]);
$bc_doctor_zip				=	DBin($_POST["doctor_zip"]);
$bc_doctor_phone			=	DBin($_POST["doctor_phone"]);
$bc_CreatedBy				=	'';
$bc_CreatedDate				=	date("Y-m-d");
$bc_doctor_email			=	DBin($_POST["doctor_email"]);
$bc_Enabled					=   1;
$bc_usertype				=   2;
$bc_ClinicID				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$cid = getSingleColumn("clinicid","select * from `users` where `clinicid`='$bc_ClinicID'");

$sql	=	"select * from `clinic` where id=$cid";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res)){
	
$clinicname				=	$row["clinicname"];
$address1				=	$row["address1"];
$address2 				=	$row["address2 "];
$clinic_city 			=	$row["city "];
$clinic_state			=	$row["state"];
$clinic_zip	    		=	$row["zip"];
$clinic_phone1			=	$row["phone1 "];
$clinic_phone2			=	$row["phone2"];
$clinic_fax1			=	$row["fax1 "];
$clinic_fax2			=	$row["fax2"];
$clinic_web				=	$row["website"];	
		
		
	} // end if row
}



$bc_arr_clinic_state	=	array();
$arrRES = mysql_query("select id,state from usstates");
while ($bc_row = mysql_fetch_assoc($arrRES) )
	$bc_arr_clinic_state[$bc_row["id"]] = $bc_row["state"];
	
$frmID		=	$_GET["id"];
$action1	= isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();

if(!filter_var($_POST["doctor_email"], FILTER_VALIDATE_EMAIL))
	$errors[] = "Email: Email not valid";
if(strlen($_POST["doctor_password"]) <=5 )
	$errors[] = "Password: Password minimum 6 checcter long";
if($_POST["doctor_password"] != $_POST["doctor_password1"])
	$errors[] = "Password: Both password not Match";
if ($_POST["doctor_address"] == "")
	$errors[] = "Address: can not be empty";
if ($_POST["doctor_city"] == "")
	$errors[] = "City: can not be empty";
if ($_POST["doctor_state"] == "")
	$errors[] = "State: can not be empty";
if ($_POST["doctor_zip"] == "")
	$errors[] = "Zip: can not be empty";
if ($_POST["doctor_phone"] == "")
	$errors[] = "Phone: can not be empty";

if ($_POST["doctor_username"] == "")
	$errors[] = "Username: can not be empty";
if ($_POST["doctor_password"] == "")
	$errors[] = "Password: can not be empty";
if ($_POST["doctor_last_name"] == "")
	$errors[] = "LastName: can not be empty";
if ($_POST["doctor_first_name"] == "")
	$errors[] = "FirstName: can not be empty";
if ($_POST["doctor_gender"] == "")
	$errors[] = "Sex: can not be empty";
if ($_POST["doctor_email"] == "")
	$errors[] = "Email: can not be empty";


$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

//$genSysCustomerID = CreateSubscription($bc_doctor_first_name,$bc_doctor_last_name,$bc_doctor_email) ;
if ($action1 == "save") {
$genSysCustomerID =	addclinicdoctor('pangeafinal2',$clinicname,$address1,$address2,$clinic_city,$clinic_state,$clinic_zip,$clinic_phone1,$clinic_fax1,$clinic_fax2,$clinic_web,$bc_doctor_first_name,$bc_doctor_last_name,$bc_doctor_email,$bc_doctor_gender,$bc_doctor_birth,$bc_doctor_username,$bc_doctor_password);
}

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {	
		if ($action1 == "save") {
		
			if($genSysCustomerID >= 1){
		
			$sql	=	"insert into  `users` (`username`,`password`,`genensysuserid`,`clinicid`,`lastname`,`firstname`,`dob`,`sex`,`address`,`city`,`state`,`zip`,`phone`,`createdby`,`createddate`,`email`,`enabled`,`comments`,`primary`,`affiliatemarketingcode`,`lockedout`,`freesubscription`,`usertype`) values ('" . $bc_doctor_username . "','" . $bc_doctor_password . "','" . $genSysCustomerID . "','" . $bc_ClinicID . "','" . $bc_doctor_last_name . "','" . $bc_doctor_first_name . "','" . $bc_doctor_birth . "','" . $bc_doctor_gender . "','" . $bc_doctor_address . "','" . $bc_doctor_city . "','" . $bc_doctor_state . "','" . $bc_doctor_zip . "','" . $bc_doctor_phone . "','" . $bc_CreatedBy . "','" . $bc_CreatedDate . "','" . $bc_doctor_email . "','" . $bc_Enabled . "','" . $bc_Comments . "','" . $bc_Primary . "','" . $bc_AffiliateMarketingCode . "','" . $bc_LockedOut . "','" . $bc_FreeSubscription . "','" . $bc_usertype . "')";
			
			$res	=	mysql_query($sql);
			
		if ($res) {
			$sucMessage = "Record Entered Successfully";
		} else {
			$sucMessage = "Error: Please try Later";
		}
		}else {
		$sucMessage = "Error: Please try Later";
		}
		}
		
		
		
		if ($action1 == "edit") {
		
		 	$sql	=	"update `users` set `username` ='".$bc_doctor_username."',`password`='" . $bc_doctor_password . "',`lastname`='". $bc_doctor_last_name."',`firstname`='".$bc_doctor_first_name."',`dob`='".$bc_doctor_birth."',`sex`='".$bc_doctor_gender."',`address`='".$bc_doctor_address."',`city`='".$bc_doctor_city."',`state`='".$bc_doctor_state."',`zip`='".$bc_doctor_zip."',`phone`='".$bc_doctor_phone."',`email`='".$bc_doctor_email."' where `id`='$frmID'";
	
			$res	=	mysql_query($sql);
			
		if ($res) {
			$sucMessage = "Record updated Successfully";
		} else {
			$sucMessage = "Error: Please try Later";
		}
		}		
		
		
		
	}
	else {
		$sucMessage = $err;
	}
} 

$sql	=	"select * from `users` where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res)){
	
$doctor_username		=	$row["username"];
$doctor_password		=	$row["password"];
$doctor_last_name		=	$row["lastname"];
$doctor_first_name		=	$row["firstname"];
$doctor_birth			=	date('m/d/Y',strtotime($row["dob"]));
$doctor_gender			=	$row["sex"];
$doctor_address	    	=	$row["address"];
$doctor_city			=	$row["city"];
$bc_clinic_state		=	$row["state"];
$doctor_zip				=	$row["zip"];
$doctor_phone			=	$row["phone"];
$doctor_email			=	$row["email"];	
		
		
	} // end if row
	$action = "edit";
} // end if


$meta_title	= "Create Doctor";

include_once('includes/header.php');
?>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>

<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />



    <script>
    $(function() {
        $( "#datepicker" ).datepicker();	
          
		  $( ".selector" ).datepicker({ dateFormat: "yy-mm-dd" });
      
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



<script language="javascript">
	$(document).ready(function() {
		$(".fancybox2").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'width'			: 540,
			'height'		: 700,
			'type'			: 'iframe'
		});
	});
</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Create Doctor </div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
       <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
        
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">

              
				
				<h3><span>Doctor Profile :</span></h3>
				<div id="box" class="box">
				<table width="92%" border="0" cellspacing="15" cellpadding="0" align="center">
  <tr>
    <td width="46%">
	<strong>* Doctor First Name</strong>:<br />
	<input type="text" name="doctor_first_name" class="new_input1" value="<?php echo $doctor_first_name; ?>" /></td>
    <td width="8%">&nbsp;</td>
    <td width="46%">
	<strong>* Doctor Last Name</strong>:<br /><input type="text" name="doctor_last_name" class="new_input1" value="<?php echo $doctor_last_name; ?>" />	</td>
  </tr>
  <tr>
    <td><strong>* Email Address</strong>:<br /><input type="text" name="doctor_email" class="new_input1" value="<?php echo $doctor_email; ?>" /></td>
    <td>&nbsp;</td>
    <td>
	<strong>* Gender</strong>:<br />
	<select style="width:200px" class="new_input" name="doctor_gender">
	<option value="Male">Male</option>
	<option value="Female">Female</option>
	</select>	</td>
  </tr>
  <tr>
    <td><strong> Date of Birth</strong>:<br /><input type="text" name="doctor_birth" id="datepicker" class="new_input1" value="<?php echo $doctor_birth; ?>" /></td>
    <td>&nbsp;</td>
    <td><strong>* Doctor User Name</strong>:<br /><input type="text" name="doctor_username" class="new_input1" value="<?php echo $doctor_username; ?>" /></td>
  </tr>
  <tr>
    <td><strong>* Password</strong>:<br /><input type="text" name="doctor_password" class="new_input1" value="<?php echo $doctor_password; ?>" /></td>
    <td>&nbsp;</td>
    <td><strong>* Confirm Password</strong>:<br /><input type="text" name="doctor_password1" class="new_input1" value="<?php echo $doctor_password; ?>" /></td>
  </tr>
  <tr>
  <td colspan="3">Password must be minimum of 6 characters in length.	</td>
  </tr>
  
  <tr>
    <td><strong> * Doctor Address </strong>:<br />
      <input type="text" name="doctor_address" id="doctor_address" class="new_input1" value="<?php echo $doctor_address; ?>" /></td>
    <td>&nbsp;</td>
    <td><strong>Doctor Phone </strong>:<br /><input type="text" name="doctor_phone" id="new_input1" class="new_input1" value="<?php echo $doctor_phone; ?>" /></td>
  </tr>
  
  <tr>
    <td><strong>* City</strong>:<br />
      <input type="text" name="doctor_city" id="doctor_city" class="new_input1" value="<?php echo $doctor_city; ?>" /></td>
    <td>&nbsp;</td>
    <td><table width="100%" border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td><strong>* State</strong>:<br />
	<select name="doctor_state" style="width:150px" class="new_input" id="doctor_state">
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
	</select></td>
    <td><strong>* Zip Code</strong>:<br /><input type="text" id="clinic_zip" name="doctor_zip" class="new_input" value="<?php echo $doctor_zip; ?>" /></td>
  </tr>
</table></td>
  </tr>	  
</table>


				</div>
				
				
				
				
				
				
				
				
				
				
               
              </div>
            </div>
          </div>
		 <input type="image" src="<?php echo IMAGE_PATH; ?>publish_new.png" name="submit" value="Create doctor" align="right" />
            <input type="hidden" name="submit" value="Create doctor" />  
        </div>
		 </form>
        
      
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
	

</script>