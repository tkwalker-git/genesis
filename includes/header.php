<?php
	include_once('admin/database.php');
	include_once('site_functions.php');

	$file_name = basename($_SERVER['SCRIPT_FILENAME']);




	if($_SESSION['LOGGEDIN_MEMBER_ID'] > 0 && basename($_SERVER['PHP_SELF']) == 'index.php' &&  basename($_SERVER['PHP_SELF'])!='login.php'){
		if(basename($_SERVER['PHP_SELF'])!='dashboard.php')
			echo "<script>window.location.href='dashboard.php'</script>";
		}
	else{
		if(basename($_SERVER['PHP_SELF'])!='index.php' && basename($_SERVER['PHP_SELF'])!='login.php' && basename($_SERVER['PHP_SELF'])!='user_login.php' && basename($_SERVER['PHP_SELF'])!='signup.php' && basename($_SERVER['PHP_SELF'])!='forgot_password.php' && $_SESSION['LOGGEDIN_MEMBER_ID']=='' &&  basename($_SERVER['PHP_SELF'])!='subscription.php' &&  basename($_SERVER['PHP_SELF'])!='create_patient.php' && basename($_SERVER['PHP_SELF'])!='dr.subscription.php' && basename($_SERVER['PHP_SELF'])!='patient.subscription.php' )
			echo "<script>window.location.href='".ABSOLUTE_PATH."index.php'</script>";
	}

	$meta_query 	= "select * from `default_settings`";
	$meta_res 		= mysql_query($meta_query);
	if ($meta_row 	= mysql_fetch_assoc($meta_res)){
		$dmeta_title 		= DBout($meta_row['meta_title']);
		$dmeta_desc 		= DBout($meta_row['meta_desc']);
		$dmeta_keywords 	= DBout($meta_row['meta_keywords']);
	}

	$meta_descrp 	= ($meta_descrp != '') ? $meta_descrp : $dmeta_desc ;
	$meta_kwords 	= ($meta_kwords != '') ? $meta_kwords : $dmeta_keywords ;
	$meta_title 	= ($meta_title != '') ? $meta_title : $dmeta_title ;

	if ( $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ){
		if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){
			$logged_in_member_name = attribValue("doctors","first_name","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
			$logged_in_member_lname = attribValue("doctors","last_name","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
			$logged_in_member_fullname = $logged_in_member_name." ".$logged_in_member_lname;
		}else {
			$logged_in_member_name = attribValue("patients","firstname","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
			$logged_in_member_lname = attribValue("patients","lastname","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
			$logged_in_member_fullname = $logged_in_member_name." ".$logged_in_member_lname;
		}
	}

	$caturl = getViewEventURL();

	$secureLogoPages = array("/buy-slots.php","/create_flyer_step2.php","/buy_tickets_step2.php","/reserve-table.php","/buy_tickets.php");

	//	$_SESSION['userZip'] = ''; $_SESSION['zipEnter'] = '';

	if(isset($_POST['addzip']) || isset($_POST['zipReq'])=='all' || $_GET['zipSel']){
		if(isset($_POST['zipReq']) =='all'){
			$_SESSION['userZip']		= '';
			$_SESSION['selectedZip']	= '';
			$_SESSION['zipEnter']		= 'yes';
			if($page == 'locations')
				$redrkt = ABSOLUTE_PATH."category/live-entertainment.html";
		}
		else{
			$zip	= DBin($_POST['zip']);
			$redrkt = '';
			if($zip=='')
				$zip	= DBin($_GET['zipSel']);

			if($page == 'locations')
				$redrkt = ABSOLUTE_PATH."category/live-entertainment.html";

			$zipEvent = checkZipEvent($zip);

			if($zipEvent > 0){
				$_SESSION['selectedZip'] = $zip;
				$lat = getSingleColumn("latitude","select * from `zipcodes` where `zipcode`='$zip' limit 1");
				$lng = getSingleColumn("longitude","select * from `zipcodes` where `zipcode`='$zip' limit 1");

				$qryZip	= "select DISTINCT zipcode from zipcodes where (( ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS(( ".$lng." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1) <= 50 and ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS(( ".$lng." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1) >= 0 and  latitude!=''";

				$resZip	= mysql_query($qryZip);
				$i=0;
				while($rowZip	= mysql_fetch_array($resZip)){
					$i++;
					if($i!=mysql_num_rows($resZip))
						$coma	= ',';
					else
						$coma	= '';

					$showZip	.= $rowZip['zipcode'].$coma;
				} // end while
				$_SESSION['userZip']	= $showZip;
				$_SESSION['zipEnter']	= 'yes';
			} // end if $zipId

			else
				$zrpError = 1;
		} // end else

		if($zrpError==''){
			if ($_SERVER["SERVER_PORT"] != "80")
				$pageURL = "https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			else
				$pageURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				if($redrkt)
					header("location: ".$redrkt);
				else
					header("location: ".$pageURL);
			}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta id="FirstCtrlID" http-equiv="X-UA-Compatible" content="IE=8" />
<meta property="og:title" content="<?php echo $meta_title; ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="<?=$page_url?>" />
<meta property="og:image" content="<?=$image?>" />
<meta property="og:site_name" content="eventgrabber" />
<meta property="fb:admins" content="<?=1433252254?>" />

<meta name="description" content="<?php echo $meta_descrp;?>" />
<link rel="image_src" href="<?=$image;?>" / >
<meta name="keywords" content="<?php echo $meta_kwords; ?>" />
<title><?php echo $meta_title; ?></title>
<?php

	if(isset($_GET['type'])){
		$user_type = $_GET['type'];
		if($user_type == "p"){
			//$_SESSION['usertype'] = '2';
		}
		//echo $user_type;
	}

$user_id	=	$_SESSION['LOGGEDIN_MEMBER_ID'];

?>
<!-- <link rel="shortcut icon" href="<?php echo ABSOLUTE_PATH; ?>images/favicon.ico" type="image/x-icon" /> -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>style.css"/>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/common_bc.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/ev_functions.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/popup.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/validate.decimal.js"></script>
<script type="text/javascript">
	var	a	=	$('body').height();
</script>

<?php

if ( in_array($_SERVER['PHP_SELF'],$secureLogoPages) ) {
?>

<script language="javascript" type="text/javascript">
//<![CDATA[
var cot_loc0=(window.location.protocol == "https:")? "https://secure.comodo.com/trustlogo/javascript/cot.js" :
"http://www.trustlogo.com/trustlogo/javascript/cot.js";
document.writeln('<scr' + 'ipt language="JavaScript" src="'+cot_loc0+'" type="text\/javascript">' + '<\/scr' + 'ipt>');
//]]>
</script>

<?php
}
?>

</head>
<body>

<div id="page-bg" class="translucent"></div>
<div class="subscribe-overlayer" id="overlayer" align="center"></div>
<div>
<!--Start Top Part -->
<div class="headerOut">
  <!--Start Tab -->
  <div class="tabCon">
    <div class="tabLeft">

    </div>
    <div class="tabRight">&nbsp;

    </div>
    <div class="clr"></div>
  </div>
  <div class="header">
    <div class="logo">
	<?php if(isset($_GET['vc']) && isset($_GET['ci'])){
 $ci=base64_decode($_GET['ci']); ?>
<img src="<?php echo ABSOLUTE_PATH.invitep_logo($ci); ?>" alt="" border="0"  />
<?php }?>
	<?php if($_SESSION['LOGGEDIN_MEMBER_ID']){?>
	<a href="<?php echo ABSOLUTE_PATH_WITHOUT_SSL;?>"><img src="<?php echo ABSOLUTE_PATH.logo(); ?>" alt="" border="0"  /></a>
<?php } ?>
</div>
    <div class="logoTag">

    </div>
    <div class="headerRight">
      <div class="topBtn">
        <?php

			if($_SESSION['LOGINflag']!=''){	?>
        <!--<a href="<?php echo ABSOLUTE_PATH;?>add_event.php"><img src="<?php echo IMAGE_PATH;?>add_event_btn.gif" alt="" border="0" /></a>-->
        <?php } ?>
      </div>
      <div class="search">
       <?php if(isset($_SESSION['logedin']) && $_SESSION['logedin']==1 && $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ){ ?>
      <select onchange="window.location.href=this.value">
        <option value="#" selected="selected"><?php echo $logged_in_member_fullname;?></option>
        <!-- <option value="<?php echo ABSOLUTE_PATH;?>dashboard.php" >My Profile</option> -->
		<option value="<?=ABSOLUTE_PATH."settings.php"?>" >Settings</option>
        <option value="<?=ABSOLUTE_PATH."logout.php"?>" >Logout</option>
      </select>

      <?php } ?>


      </div><br /><br />

      <button onclick="history.go(-1);">Back </button>



    </div>
    <div class="clr"></div>
  </div>
  <!--End Header -->
</div>
<!--End Tab -->
