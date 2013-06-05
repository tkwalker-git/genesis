<?php
	include_once('admin/database.php');
	include_once('site_functions.php');
	
	$file_name = basename($_SERVER['SCRIPT_FILENAME']);
	
	
	
	if($_SESSION['LOGGEDIN_MEMBER_ID'] > 0 && basename($_SERVER['PHP_SELF']) == 'index.php' &&  basename($_SERVER['PHP_SELF'])!='login.php'){
		if(basename($_SERVER['PHP_SELF'])!='dashboard.php')
			echo "<script>window.location.href='dashboard.php'</script>";
		}
	else{
		if(basename($_SERVER['PHP_SELF'])!='login.php' && basename($_SERVER['PHP_SELF'])!='user_login.php' && basename($_SERVER['PHP_SELF'])!='signup.php' && basename($_SERVER['PHP_SELF'])!='forgot_password.php' && $_SESSION['LOGGEDIN_MEMBER_ID']=='' &&  basename($_SERVER['PHP_SELF'])!='subscription.php' &&  basename($_SERVER['PHP_SELF'])!='create_patient.php' )
			echo "<script>window.location.href='".ABSOLUTE_PATH."login.php'</script>";
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
		$logged_in_member_name = attribValue("users","firstname","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
		$logged_in_member_lname = attribValue("users","lastname","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
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
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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
<link rel="shortcut icon" href="<?php echo ABSOLUTE_PATH; ?>images/favicon.ico" type="image/x-icon" />
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
		<?php if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){	?>
     <ul>
        <li><a href="<?php echo ABSOLUTE_PATH; ?>dashboard.php" <?php if($file_name == 'dashboard.php' || $file_name == '') echo 'class="sel"';?>><span>DOCTOR DASHBOARD</span></a></li>
        <li><a href="<?php echo ABSOLUTE_PATH; ?>dashboard.php" <?php if($file_name == 'dashboard.php') echo 'class="sel"';?>><span>AFFILIATE MANAGER</span></a></li>
        <li><a href="<?php echo ABSOLUTE_PATH; ?>dashboard.php" <?php if($file_name == 'dashboard.php') echo 'class="sel"';?>><span>SETTINGS</span></a></li>
        <!--
<li><a href="<?php echo $caturl;?>" <?php if($file_name == 'category.php') echo 'class="sel"';?>><span>VIEW EVENTS</span></a></li>
        <li><a href="<?php echo ABSOLUTE_PATH_WITHOUT_SSL;?>myeventwall.php" <?php if(strstr($file_name, 'myeventwall') /*== 'myeventwall-a.php'*/) echo 'class="sel"';?>><span>MY EVENTWALL</span></a></li>
        <li><a href="<?php echo ABSOLUTE_PATH_WITHOUT_SSL;?>contact-us.php" <?php if($file_name == 'contact-us.php') echo 'class="sel"';?>><span>CONTACT US</span></a></li>
-->
      </ul>
	  <?php } ?>
    </div>
    <div class="tabRight">
      <!--<?php if(!isset($_SESSION['logedin']) || $_SESSION['logedin']!=1 || $_SESSION['LOGGEDIN_MEMBER_ID'] == 0 ){ ?>
      <a href="<?=ABSOLUTE_PATH_WITHOUT_SSL."fblogin.php"?>"><img src="<?php echo IMAGE_PATH;?>facebook_login_button.png" /></a>
      <? }?>-->
      <?php if(isset($_SESSION['logedin']) && $_SESSION['logedin']==1 && $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ){ ?>
     <!-- <select onchange="window.location.href=this.value">
        <option value="#" selected="selected"><?php echo $logged_in_member_fullname;?></option>
        <option value="<?php echo ABSOLUTE_PATH;?>dashboard.php" >My Profile</option>
		<option value="<?=ABSOLUTE_PATH."settings.php"?>" >Settings</option>
        <option value="<?=ABSOLUTE_PATH."logout.php"?>" >Logout</option>
      </select>-->
      <!--<a href="<?php echo ABSOLUTE_PATH;?>logout.php"><img src="<?php echo IMAGE_PATH;?>signup_icon.gif" alt="" /> Logout</a> -->
      <?php } else { ?>
   <!--   <a href="<?php echo ABSOLUTE_PATH_WITHOUT_SSL;?>login.php"><img src="<?php echo IMAGE_PATH;?>login_icon.gif" alt="" /> Login</a> | <a href="<?php echo ABSOLUTE_PATH_WITHOUT_SSL;?>signup.php"><img src="<?php echo IMAGE_PATH;?>signup_icon.gif" alt="" /> Sign Up</a>-->
      <?php }?>
    </div>
    <div class="clr"></div>
  </div>
  <div class="header">
   <!--
 <div class="logo"><a href="<?php echo ABSOLUTE_PATH_WITHOUT_SSL;?>">

<img src="<?php echo ABSOLUTE_PATH.logo(); ?>" alt="" border="0"  />
</a></div>
    <div class="logoTag"> 
-->
      <!--<a href="#"><img src="images_site/change_btn.gif" alt="" border="0" class="vAlign" /></a>--> 
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
        <option value="<?php echo ABSOLUTE_PATH;?>dashboard.php" >My Profile</option>
		<option value="<?=ABSOLUTE_PATH."settings.php"?>" >Settings</option>
        <option value="<?=ABSOLUTE_PATH."logout.php"?>" >Logout</option>
      </select>
      <!--<a href="<?php echo ABSOLUTE_PATH;?>logout.php"><img src="<?php echo IMAGE_PATH;?>signup_icon.gif" alt="" /> Logout</a> -->
      <?php } ?>
      <!--  <form name='searchfrm' id='searchfrm' method='get' action='<?php echo ABSOLUTE_PATH_WITHOUT_SSL; ?>search.php'>
          <?php

				if($_POST['searchtext']=='' || $_POST['searchtext']=='Search events with event name'){
					$val='Search events with event name';
				}
				else{
					$val = $_GET['term'];
				}
			
			?>
          <input type="text" name='term'  id='term' class="searchInput" onblur="if(this.value==''){this.value='<?php echo $val;?>';}" 
		  value="<?php if ($_REQUEST['search']){ echo $_REQUEST['term'];} else{ echo $val; }?>" onfocus="if(this.value=='<?php echo $val;?>'){this.value='';}" />
          <input type="image" name='search' src="<?php echo IMAGE_PATH;?>search_btn.gif" />
        </form>-->
        <?php if ( $_SESSION['usertype'] == '2' ) { ?>
			<div class="ProfileSettingTab" style="padding-top:10px">
		<?php // userSubMenu2("manage_events");?>
        </div>
        <?php } ?>
      </div><br />
	  <?php
	  if($_SESSION['selectedZip']){
	  	$selectedZip = $_SESSION['selectedZip'];
		$city	= getSingleColumn("city","select * from `zipcodes` where `zipcode`='".$selectedZip."' limit 1");
		$state	= getSingleColumn("state","select * from `zipcodes` where `zipcode`='".$selectedZip."' limit 1");
		$city	= strtolower($city);
		$city	= ucwords($city);
		?>
		<!--<div style="font-size:20px; color:#333333"><?php echo $city.", ".$state."&nbsp; &nbsp; &nbsp; "; ?> <a href="javascript:void(0)" onclick="showPages('<?php echo ABSOLUTE_PATH; ?>','ajax/selectZip.php','');" style="font-size:12px;color:#0066FF">Change ZipCode</a></div>-->
	<?php
	  }
	  else{?>
	 <!-- <a href="javascript:void(0)" onclick="showPages('<?php echo ABSOLUTE_PATH; ?>','ajax/selectZip.php','');" style="font-size:12px; color:#0066FF">Change ZipCode</a>-->
	  <?php
	  }
	  ?>

    </div>
    <div class="clr"></div>
  </div>
  <!--End Header --> 
</div>
<!--End Tab -->
<?php if (($_SESSION['zipEnter']!='yes' || $zrpError) && $page!='login' && ($page!='locations' || $zrpError)){ ?>
	<script>
/*		showPages('<?php echo ABSOLUTE_PATH; ?>','ajax/selectZip.php','<?php echo $zrpError; ?>'); */
	</script>
<?php } ?>