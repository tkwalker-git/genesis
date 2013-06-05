<?php
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
	echo "<script>window.location.href='index.php';</script>";

$meta_title = 'Settings';
$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){
	$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}else {
	$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");

}


if(isset($_GET["deactivate"])){
	
	$sql = "update patients set status='0' where id='". $member_id ."'";
		if ( mysql_query($sql) ){
			unset($_SESSION['logedin']);
			unset($_SESSION['LOGGEDIN_MEMBER_ID']);
		}
}

if(isset($_GET["cancel_subscription"])){
	
	$subscrId	=	attribValue("patients" , "auth_subscription_id" , "where id = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
	require('includes/AuthnetARB.class.php');
	try
	{

		$subscription = new AuthnetARB('48zC2wJr2S8p', '69Cx7SKRf4bJ826V', AuthnetARB::USE_DEVELOPMENT_SERVER);
		
		$subscription->setParameter('subscrId', $subscrId);

		// update the subscription
		$subscription->deleteAccount();
		
		//echo $subscription->getResponse();
		
		// Check the results of our API call
		
		if ($subscription->isSuccessful())
		{
			$sql = "update patients set subscription_type='0' where id='". $member_id ."'";
			$res = mysql_query($sql);
			if($res)
				$err = "Subscruption Canceled successfully";
			
			}
		else
		{
			// The subscription was not created!
			$err = "Subscription does not Canceled";
			
			$excptn = new AuthnetARBException();
			echo $excptn;
		}
	}
	catch (AuthnetARBException $e)
	{
		$err = $e;
		$err .= $subscription;
	} // catch
	
	
}

if(isset($_GET["cancel_dr_subscription"])){
	
	$subscrId	=	attribValue("doctors" , "auth_subscription_id" , "where id = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
	require('includes/AuthnetARB.class.php');
	try
	{

		$subscription = new AuthnetARB('48zC2wJr2S8p', '69Cx7SKRf4bJ826V', AuthnetARB::USE_DEVELOPMENT_SERVER);
		
		$subscription->setParameter('subscrId', $subscrId);

		// update the subscription
		$subscription->deleteAccount();
		
		$subscription->getResponse();
		
		// Check the results of our API call
		
		if ($subscription->isSuccessful())
		{
			$sql = "update doctors set subscription='0' where id='". $member_id ."'";
			$res = mysql_query($sql);
			if($res)
				$err = "Subscruption Canceled successfully";
			
			}
		else
		{	// Error Deffinition
			// The subscription was not created!
			$err = "Subscription does not Canceled";
			
			$excptn = new AuthnetARBException();
			echo $excptn;
		}
	}
	catch (AuthnetARBException $e)
	{
		$err = $e;
		$err .= $subscription;
	} // catch
	
	
}

if($_SESSION['usertype'] == 'doctor')
	$subscription_type	=	attribValue("doctors" , "subscription" , "where id = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
else
	$subscription_type	=	attribValue("patients" , "subscription_type" , "where id = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");

include_once('includes/header.php');

?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script>
	$(document).ready(function(){
		var height = $('.eventManger_right').height();
		$('.eventManger_left').css('height',height-40);
	});
</script>

<style>
.dash_menu{
	position:relative;
	height:67px;
	}

.dash_menu table{
	}

.dash_menu td{
	padding:0 25px;
	height:64px;
	text-align:center;
	vertical-align:text-bottom;
	}

.dash_menu .bordr{
	border-right:#c1c1c1 solid 1px;
	}

.dash_menu td a{
	/*float:left;*/
	font-size:11px;
	font-weight:bold
	}

.dash_menu td a:hover{
	text-decoration:none;
	color:#0598fa
	}

.dash_menu td a img{
	margin-bottom:3px;
	}

.head_new{
	font-size:18px;
	background:url(images/dashB_bar.gif) repeat-x;
	padding:8px 12px;
	color:#ffffff;
	border-left:solid 1px #cbcbcb;
	border-right:solid 1px #cbcbcb;
	}

.recBox{
	border:#cecece solid 1px;
	border-top:none;
	background:#f6f6f6;
	}


.recBox .yellow_bar{
	background: url("images/yellow_bar.gif") repeat-x scroll 0 0 transparent;
    border-bottom: 1px solid #CBCBCB;
    color: #231F20;
    font-size: 14px;
    font-weight: bold;
    height: 25px;
    padding:12px 0 0;
	}

.eventManger_left{
	background:url(images/eventManger_leftBg.gif) repeat-x #1c6722;
	width:186px;
	float:left;
	min-height:597px;
	padding-top:40px;
	}

.eventManger_right{
	width:719px;
	float:left;
	border-left:#CBCBCB solid 1px;
	min-height:636px
	}

.eventManger_left ul{
	margin:0;
	padding:0;
	}

.eventManger_left ul li{
	list-style:none;
	}

.eventManger_left ul .icon_myEvents{
	background:url(images/icon_myEvents.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_venues{
	background: url("images/icon_venues.png") no-repeat scroll 9px 2px transparent;
	line-height: 30px;
	}

.eventManger_left ul .icon_manageteam{
	background:url(images/icon_manageTeam.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_contact{
	background:url(images/icon_contact.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_reports{
	background:url(images/icon_reports.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_promotions{
	background:url(images/icon_promotions.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul li a{
	color:#231f20;
	font-size:14px;
	padding:4px 0 4px 38px;
	display:block;
	font-weight:bold;
	}

.eventManger_left ul li a:hover{
	text-decoration:none;
	}

.eventManger_left ul .border{
	border-bottom: 1px solid #AEDAB0;
    border-top: 1px solid #38893A;
    margin:6px 0 12px;
	height:0px;
	line-height:0;
	padding:0;
	}

.eventManger_left ul ul{
/*	padding-left:10px; */
	}

.eventManger_left ul ul a:hover, .eventManger_left ul ul .active{
	background:#558c58;
	display:block;
	}

.eventManger_left ul ul li a{
	padding:8px 0 8px 45px;
	font-size:12px;
	line-height:normal
	}

.ev_eventBox{
	border-bottom:#c1c1c1 solid 1px;
	padding:13px 10px;
	}

.event_name{
	font-size:12px;
	font-weight:bold
	}

.event_name a{
	color:#006695;
	text-decoration:none;
	}

.event_name a:hover{
	text-decoration:underline;
	}

.event_info{
	line-height:19px
	}

.ev_eventBox span{
	color:#6d6e71
	}

.ev_eventBox ul{
	margin:0;
	padding:4px 0 0 13px;
	line-height:16px;
	color:#6d6e71
	}

.sales{
	color:#289701;
	font-weight:bold
	}

.eventManger_right table a{
	color:#006695;
	text-decoration:underline
	}

.eventManger_right table a:hover{
	text-decoration:none;
	}

table .dele{
	line-height:16px;
	}

.event_name div{
	color:#000000}
</style>
<div class="topContainer">
	<div class="welcomeBox"></div>
	<!-- Start Middle-->
	<div id="middleContainer">
		<div class="clr"></div>
		<div class="gredBox">
			<?php include('dashboard_menu_tk.php'); ?>
			<div class="whiteTop">
				<div class="whiteBottom">
					<div class="whiteMiddle" style="padding-top:7px;">
						<div class="head_new">ACCOUNT SETTINGS</div>
						<div class="recBox">
							<div class="eventManger_left">
								<ul>
									<li class="icon_myEvents"><a href="javascript:void(0)" style="cursor:default">UPDATE ACCOUNT</a>
										<?php if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){ ?>
										<ul>
											<!-- <li><a href="settings.php" <?php if($_GET['p']==''){ echo "class='active'";} ?>>1. Update Your Calendars</a></li> -->
											<li><a href="?p=my-profile" <?php if($_GET['p']=='my-profile'){ echo "class='active'";} ?>>My Profile</a></li>
											<!-- <li><a href="?p=notifcations" <?php if($_GET['p']=='notifcations'){ echo "class='active'";} ?>>2. Setup Notifications</a></li> -->
											<!-- <li><a href="?p=recommend" <?php if($_GET['p']=='recommend'){ echo "class='active'";} ?>>3. Invite</a></li> -->
                                            <li><a href="?p=dr_subscription" <?php if($_GET['p']=='dr_subscription'){ echo "class='active'";} ?>>Update Subscription</a></li>
											<li><a href="?p=dr_invitations" <?php if($_GET['p']=='dr_invitations'){ echo "class='active'";} ?>>Invitations</a></li>
                                            <li><a href="?p=invite_patients" <?php if($_GET['p']=='invite_patients'){ echo "class='active'";} ?>>Invite Patient</a></li>
                                            <li><a href="?p=set_logo" <?php if($_GET['p']=='set_logo'){ echo "class='active'";} ?>>Update Your Logo</a></li>

										</ul>
										<?php  } else {?>
										<ul>
											<!-- <li><a href="settings.php" <?php if($_GET['p']==''){ echo "class='active'";} ?>>1. Update Your Calendars</a></li> -->
											<li><a href="?p=patient-profile" <?php if($_GET['p']=='patient-profile'){ echo "class='active'";} ?>>My Profile</a></li>
                                            <?php if($subscription_type != 0){ ?>
                                            <li><a href="?p=pat_subscriptions" <?php if($_GET['p']=='pat_subscriptions'){ echo "class='active'";} ?>>My Subscriptions</a></li>
											<?php }else{ ?>
                                            <li><a href="?p=pat_subscriptions" <?php if($_GET['p']=='pat_subscriptions'){ echo "class='active'";} ?>>Create Subscriptions</a></li>
                                            <?php } ?>
                                            
                                            <li><a href="?p=my_doctors" <?php if($_GET['p']=='my_doctors'){ echo "class='active'";} ?>>My Doctors</a></li>
                                            <!--
<li><a href="?p=patient-notifcations" <?php if($_GET['p']=='patient-notifcations'){ echo "class='active'";} ?>>3. Setup Notifications</a></li>
											<li><a href="?p=patient-recommend" <?php if($_GET['p']=='patient-recommend'){ echo "class='active'";} ?>>4. Invite</a></li>
-->
										</ul>

										<?php } ?>

									</li>


								</ul>
							</div> <!-- /eventManger_left -->

							<div class="eventManger_right">
							<?php if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){

							if($_GET['p']=='my-profile')
								include('dr_profile.php');
								
							elseif($_GET["p"] == 'dr_subscription')
								include_once("dr_subscription.php");
								
							elseif($_GET["p"] == 'invite_patients')	
								include_once("invite_patients.php");
								
							elseif($_GET["p"] == 'dr_invitations')	
								include_once("dr_invitations.php");
								
							elseif($_GET['p']=='set_logo')
								include_once("set_logo.php");
							else
								include('dr_profile.php');
					}

else if($_SESSION['usertype']=='patient'){
   
	if($_GET['p']=='patient-profile')
		include('patient_profile.php');

	elseif($_GET['p']=='patient-notifcations')
		include('patient_notifcations.php');
		
	elseif($_GET['p']=='pat_subscriptions')
		include('pat_subscriptions.php');
		
	elseif($_GET['p']=='my_doctors')
		include('my_doctors.php');
	
	
	elseif($_GET['p']=='patient-recommend')
		include('patient_recommend.php');

	else
		include('patient_profile.php');
	/* include('patient-sync-calendars.php'); */


}?>
							</div> <!-- /eventManger_right -->

							<br class="clear" />
						</div>
						<br class="clear" />
					</div>
				</div>
			</div>
		</div>
	</div> <!-- /middleContainer -->
</div>
<?php include_once('includes/footer.php'); ?>