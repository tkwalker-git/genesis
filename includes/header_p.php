<?php
	
	
	include_once('admin/database.php');
	include_once('site_functions.php');
	
	$file_name = basename($_SERVER['SCRIPT_FILENAME']);
	
	$meta_query 	= "select * from default_settings";
	$meta_res 		= mysql_query($meta_query);	
	if ( $meta_row 		= mysql_fetch_assoc($meta_res) ) {
		$dmeta_title 		= DBout($meta_row['meta_title']);
		$dmeta_desc 		= DBout($meta_row['meta_desc']);
		$dmeta_keywords 	= DBout($meta_row['meta_keywords']);
	}
		
	$meta_descrp 	= ($meta_descrp != '') ? $meta_descrp : $dmeta_desc ;
	$meta_kwords 	= ($meta_kwords != '') ? $meta_kwords : $dmeta_keywords ;
	$meta_title 	= ($meta_title != '') ? $meta_title : $dmeta_title ; 
	
	if ( $localSite != 1 ) {
	 
		include_once('facebook.php');
	    $facebook2 = new Facebook(array(
							'appId'      => FACEBOOK_APP_ID2,
							'secret'     => FACEBOOK_SECRET2,
							'cookie'     => true
					));
			$facebookloginurl  = $facebook2->getLoginUrl(array('next'=>"http://www.eventgrabber.com","scope"=>"email,user_birthday"));
			$facebooklogouturl = $facebook2->getLogoutUrl(array('next'=>"http://www.eventgrabber.com"));
			//d($facebook2,1);
		if ( $_SESSION['LOGGEDIN_MEMBER_ID'] == '' || $_SESSION['logedin'] == ''  ) {
			
			$cookie 	= get_facebook_cookie(FACEBOOK_APP_ID2, FACEBOOK_SECRET2);
			$url		= "https://graph.facebook.com/me?access_token=" .$cookie['access_token']."";
			
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			$xml_resp 	= curl_exec($ch);
			curl_close($ch);
			$userfacebook = json_decode($xml_resp);
	        
			if($userfacebook->id != '')
			{
				$gender = $userfacebook->gender;
				$email	= $userfacebook->email;
				$fb_id	= $userfacebook->id;
				$name 	= $userfacebook->name;
				$fname	= $userfacebook->first_name;
				$lname	= $userfacebook->last_name;
				
				$fsql = "select * from members where email='". $email ."'";
				$fres = mysql_query($fsql);
				
				if ( mysql_num_rows($fres) ) {	
					if ( $frow = mysql_fetch_assoc($fres) ) {
						$_SESSION['logedin'] 			= '1';
						$_SESSION['LOGGEDIN_MEMBER_ID'] = $frow['id'];
						$_SESSION['usertype'] 			= $frow['usertype'];
					}
				} else {
				
					
                    $fql = "SELECT pic_small,pic_big,pic FROM profile WHERE id = " . $fb_id; 
				 
					$response = $facebook2->api(	
												array(
													'method' => 'fql.query',
													'query' =>$fql,
												)
											);
					
					foreach ($response as $value) {
					
						$pic_s	= $value['pic_small'];
						$ipath	= $value['pic_big'];
						$pic	= $value['pic'];
							
					}
					
					if ($ipath == '') {
						$ipath = $pic;
						if ($ipath == '') {
							$ipath = $pic_s;
						}
					}
					
					$tmp 	= explode("/",$pic);
					$iname 	= $tmp[count($tmp)-1];
					
					copy($pic,'images/members/' . $iname);
					
					$password 	= substr(md5(rand(0,1000)),1,8);
					$username 	= $email;
					$memberdate	= date("Y-m-d");
					$user_insert = "insert into members (name,lname, email, username, password, memberdate, usertype, email_verify,status,facebookid,image_name) 
									values ('$name','$lname', '$email', '$username', '$password', '$memberdate', '1','1','1','$fb_id','$iname') ";
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: EventGrabber.com<info@eventgrabber.com>' . "\r\n";
					$welcome = 'Hello ' . $fname . ',<br>';
					$welcome .= 'Welcome to EventGrabber.com. You will find this site very exciting.<br><br>Thanks,<br>EventGrabber.com';
	
					if ( mysql_query($user_insert) ) {
						
						$_SESSION['logedin'] 			= '1';
						$_SESSION['LOGGEDIN_MEMBER_ID'] = mysql_insert_id();
						$_SESSION['usertype'] 			= 1;
						
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= 'From: EventGrabber.com<info@eventgrabber.com>' . "\r\n";
						$welcome = 'Hello ' . $fname . ',<br>';
						$welcome .= 'Welcome to EventGrabber.com. You will find this site very exciting.<br><br>Thanks,<br>EventGrabber.com';
						mail($email,"Welcome to EventGrabber.com",$welcome,$headers);
					}
				}	
					
			}
				
			$facebook = new Facebook( array(
										  'appId'  => FACEBOOK_APP_ID2,
										  'secret' => FACEBOOK_SECRET2,
										  'cookie' => true, // enable optional cookie support
									));
			
			if ($session) {
				try {
					$uid = $facebook->getUser();
					$me = $facebook->api('/me');
				} catch (FacebookApiException $e) {
					error_log($e);
				}
			}
			
		}
	}
		
	if ( $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 )
		$logged_in_member_name = attribValue("members","name","where id=" . $_SESSION['LOGGEDIN_MEMBER_ID']);
	
	$caturl = getViewEventURL();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="description" content="<?php echo $meta_descrp; ?>" />
<meta name="keywords" content="<?php echo $meta_kwords; ?>" />
<title><?php echo $meta_title; ?></title>
<?php 
if(isset($_GET['type'])){
		$user_type = $_GET['type'];
		if($user_type == "p"){
			$_SESSION['usertype'] = '2';
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
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/popup.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/validate.decimal.js"></script>
<script type="text/javascript">
	var	a	=	$('body').height();
</script>
 <script language="javascript">
                function locationPicker()
				{
                   if($('#location_picker').is(':hidden'))
 				     $('#get_location_picker2').addClass("activ");
				  else
				    $('#get_location_picker2').removeClass("activ");
				  
				   $('#location_picker').toggle('slow');
				}
</script>
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
      <ul>
        <li><a href="<?php echo ABSOLUTE_PATH; ?>index.php" <?php if($file_name == 'index.php' || $file_name == '') echo 'class="sel"';?>><span>HOME</span></a></li>
        <li><a href="<?php echo ABSOLUTE_PATH;?>about-us.php" <?php if($file_name == 'about-us.php') echo 'class="sel"';?>><span>ABOUT</span></a></li>
        <li><a href="<?php echo ABSOLUTE_PATH;?>features.php" <?php if($file_name == 'features.php') echo 'class="sel"';?>><span>FEATURES</span></a></li>
        <li><a href="<?php echo $caturl;?>" <?php if($file_name == 'category.php') echo 'class="sel"';?>><span>VIEW EVENTS</span></a></li>
        <li><a href="<?php echo ABSOLUTE_PATH;?>myeventwall.php" <?php if(strstr($file_name, 'myeventwall') /*== 'myeventwall-a.php'*/) echo 'class="sel"';?>><span>MY EVENTWALL</span></a></li>
        <!--
					<?php if($_SESSION['LOGGEDIN_MEMBER_ID'] > 0 )
					{ ?>
					<li><a href="<?php echo ABSOLUTE_PATH;?>manage_events.php" <?php if($file_name == 'manage_events.php' || $file_name == 'citypulse.php' || $file_name == 'statgrabber.php') echo 'class="sel"';?>><span>EVENT MANAGER</span></a></li>
					<?php }?>-->
        <li><a href="<?php echo ABSOLUTE_PATH;?>contact-us.php" <?php if($file_name == 'contact-us.php') echo 'class="sel"';?>><span>CONTACT US</span></a></li>
	<!--	<li><a href="<?php echo ABSOLUTE_PATH;?>cart.php" <?php if($file_name == 'cart.php') echo 'class="sel"';?>><span>View Cart</span></a></li> -->
      </ul>
    </div>
    <div class="tabRight">
      <div id="fb-root"></div>
      	<script src="http://connect.facebook.net/en_US/all.js"></script>
      <script>
				FB.init({appId: '<?= FACEBOOK_APP_ID2 ?>', status: true, cookie: true, xfbml: true});
				FB.Event.subscribe('auth.login', function(response) {
					login();
				});
				
				FB.Event.subscribe('auth.logout', function(response) {
					logout();
				});
			
				function logout(){
					document.location.href = "<?php echo ABSOLUTE_PATH;?>logout.php";
				}
			
				function login(){
					document.location.href = '<?php echo ABSOLUTE_PATH;?>myeventwall.php';
				}
				  
			</script>
      
      <?php if(!isset($_SESSION['logedin']) || $_SESSION['logedin']!=1 || $_SESSION['LOGGEDIN_MEMBER_ID'] == 0 ){ ?>
      <? }else{ ?>
      <a href="<?=$facebookloginurl?>"><img src="<?php echo IMAGE_PATH;?>facebook_login_button.png" /></a>       <a href="<?=$facebooklogouturl?>"><img src="<?php echo IMAGE_PATH;?>facebookLogOutButton.png" /></a>
      <? } ?>
      <?php if(isset($_SESSION['logedin']) && $_SESSION['logedin']==1 && $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ){ ?>
      <select onchange="window.location.href=this.value">
        <option value="#" selected="selected"><?php echo $logged_in_member_name;?></option>
        <option value="<?php echo ABSOLUTE_PATH;?>profile_setting.php" >My Profile</option>
        <option value="<?php echo ABSOLUTE_PATH;?>logout.php" >Logout</option>
      </select>
      <!--<a href="<?php echo ABSOLUTE_PATH;?>logout.php"><img src="<?php echo IMAGE_PATH;?>signup_icon.gif" alt="" /> Logout</a> -->
      <?php } else { ?>
      <a href="<?php echo ABSOLUTE_PATH;?>login.php"><img src="<?php echo IMAGE_PATH;?>login_icon.gif" alt="" /> Login</a> | <a href="<?php echo ABSOLUTE_PATH;?>signup.php"><img src="<?php echo IMAGE_PATH;?>signup_icon.gif" alt="" /> Sign Up</a>
      <?php }?>
    </div>
    <div class="clr"></div>
  </div>
  <div class="header">
    <div class="logo"><a href="<?php echo ABSOLUTE_PATH;?>"><img src="<?php echo IMAGE_PATH;?>logo.jpg" alt="" border="0" /></a></div>
    <div class="logoTag">
      <!--<a href="#"><img src="images_site/change_btn.gif" alt="" border="0" class="vAlign" /></a>-->
    </div>
    <div class="headerRight">
      <div class="topBtn">
        <?php
				
			if($_SESSION['LOGINflag']!=''){	?>
        <!--<a href="<?php echo ABSOLUTE_PATH;?>add_event.php"><img src="<?php echo IMAGE_PATH;?>add_event_btn.gif" alt="" border="0" /></a>-->
        <?php } ?>
      </div>
 <div class="header_rightnew">
 <div class="location">
 
 <div>
 	
	<div style="float:left; margin:0px 10px 0px 0px; font-size:18px; width:250px; text-align:right;" id="eventlocation" >USA Events</div>
	
	<div style="float:left; margin:5px 0px 0px 0px; width:150px; text-align:left;">
		<a href="javascript:void(0);" style="color:#1A6AA0;" id="get_location_picker2" onclick="locationPicker()">Change Location <img src="../images/arr.png" width="10" height="4" /></a>
	</div>
	
 </div>
 
 
<!-- 
 <table width="300" border="0" cellpadding="0" cellspacing="0" class="tab_text" style="margin:0px 0px 0px 0px;">
  <tr>
    <td align="left" style="font-size:18px;"></td>
    <td align="left"></td>
  </tr>
</table>-->
<div class="clear"></div>
<div id="location_picker" style="display:none">
<form name="frm" method="post">
<fieldset class="fieldset">
<input autocomplete="off" id="zipcode" name="zipcode" value="" type="text" style="width:275px;">

<input type="hidden" value="1" id="showHide" />
  <input type="hidden" name="text1" id="text1" value="<?php if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode'] != "")
	  { 
	  	echo $val['Latitude'];
	  }
	  ?>">
      <input type="hidden" name="text2" id="text2" value="<?php if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode'] != "")
	  { 
	  	echo $val['Longitude'];
	  }
	  ?>">
      <input class="button button_bordered" value="OK" type="submit">
</fieldset>
</form>
</div>

</div>
</div>

    </div></div>
      </div>
    </div>
    <div class="clr"></div>
  </div>
  <!--End Header -->
</div>
<!--End Tab -->