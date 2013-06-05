<?php 
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

	$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
	$meta_title	= 'Event Manager';
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];


if ( $_GET['delete'] > 0 ) {		
	$dEvent_id = $_GET['delete'];
	if ( mysql_query("delete from events where id='$dEvent_id' AND userid='". $member_id ."'") ) {
		
		$sucMessage = 'Event is deleted successfully.';	
	}
}
	include_once('includes/header.php');
?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var height = $('.eventManger_right').height();
		$('.eventManger_left').css('height',height-40);
	});
	
function removeAlert(url) {
	var con = confirm("Are you sure to delete this event? Your event will also be deleted from Event's Wall of all other members.");
	if (con)
		window.location.href = url;
}

</script>

<style>
.dash_menu{
	position:relative;
	height:67px;
	}
	
.dash_menu table{
	}
	
.dash_menu td{
	padding:00 25px;
	height:64px;
	text-align:center;
	vertical-align:text-bottom;
	}
	
.dash_menu .bordr{
	border-right:#c1c1c1 solid 1px;
	}
	
.dash_menu td a{
	float:left;
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
	min-height:850px;
	padding-top:22px;
	}
	
.eventManger_right{
	width:719px;
	float:left;
	border-left:#CBCBCB solid 1px;
	min-height:637px;
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
	font-size:12px;
	padding:4px 0 4px 47px;
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
	padding:0 0 0 58px;
	}
	
.ev_eventBox{
	border-bottom:#c1c1c1 solid 1px;
	padding:13px 5px;
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
						<div class="head_new">PATIENT MANAGER</div>
						<div class="recBox">
							<div class="eventManger_left">
								<ul>
									<!--<li class="icon_myEvents"><a href="?p=my-events">MY EVENTS</a></li>
									<li class="border"></li>-->
									
									<li class="icon_myEvents"><a href="javascript:void(0)">MY HEALTH</a>
										<ul>
                                            <li><a style="font-size:12px;"  href="?p=plans" <?php if($_GET['p'] == 'plans'){ echo "class='active'";} ?>>My Plans</a></li>
                                            <li><a style="font-size:12px;"  href="?p=patient" <?php if($_GET['p'] == 'patient'){ echo "class='active'";} ?>>Health Concerns</a></li>
                                            <li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>create_patient.php">Assessments</a></li>
                                            <li><a style="font-size:12px;"   href="?p=invite-patient" <?php if($_GET['p'] == 'invite-patient'){ echo "class='active'";} ?>>Reports</a></li>
                                            <li><a style="font-size:12px;"   href="?p=invite-patient" <?php if($_GET['p'] == 'invite-patient'){ echo "class='active'";} ?>>Recommended Tests</a></li>
										</ul>
									</li>
									
									<li class="border"></li>
									
									<?php if($_SESSION['usertype']=='clinic'){?>
									<li class="icon_myEvents"><a href="javascript:void(0)">FORMS</a>
										<ul>
                                            <li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>create_supplement.php">Add Supplement</a></li>
                                            <li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>create_protocol.php">Add Protocol</a></li>
                                            <li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>create_plan.php">Add Plan</a></li>
                                            <li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>create_test.php">Add Test</a></li>
                                            <!-- <li><a style="font-size:12px;"  href="?p=doctors" <?php if($_GET['p'] == 'doctors'){ echo "class='active'";} ?>>View Doctors</a></li> -->
                                            <!-- <li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>create_doctor.php">Add Doctor</a></li> -->
										</ul>
									</li>
									<li class="border"></li>
									<?php } ?>
									
									<li class="icon_myEvents"><a href="javascript:void(0)">VISITS</a>
										<ul>
                                        	<li><a style="font-size:12px;"  href="?p=supplement" <?php if($_GET['p'] == 'supplement'){ echo "class='active'";} ?>>View Supplements</a></li>
                                        	<li><a style="font-size:12px;"  href="?p=protocols" <?php if($_GET['p'] == 'protocols'){ echo "class='active'";} ?>>View Protocols</a></li>
                                        	<li><a style="font-size:12px;"  href="?p=plans" <?php if($_GET['p'] == 'plans'){ echo "class='active'";} ?>>View Plans</a></li>
                                            <li><a style="font-size:12px;"  href="?p=test" <?php if($_GET['p'] == 'test'){ echo "class='active'";} ?>>View Tests</a></li>
										   
                                            
                                            
                                            
                                            
                                            
										</ul>
									</li>
									<li class="border"></li>
									<li class="icon_myEvents"><a href="javascript:void(0)">MANAGE FORMS</a>
										<ul>
                                        	<li><a style="font-size:12px;"  href="?p=forms" <?php if($_GET['p'] == 'forms'){ echo "class='active'";} ?>>View Forms</a></li>
                                            <!-- <li><a style="font-size:12px;"  href="?p=add-forms" <?php if($_GET['p'] == 'add-forms'){ echo "class='active'";} ?>>Add Forms</a></li> -->
										</ul>
									</li>
									<li class="border"></li>
									<li class="icon_myEvents"><a href="?p=videos" <?php if($_GET['p'] == 'videos'){ echo "class='active'";} ?>>MANAGE VIDEOS</a>
										<ul>
                                        	<li><a style="font-size:12px;"  href="?p=videos" <?php if($_GET['p'] == 'videos'){ echo "class='active'";} ?>>View Patient Videos</a></li>
                                           <li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>clinic_manager.php?p=create-video">Add Video</a></li> 
										</ul>
									</li>
									<li class="border"></li>
									<li class="icon_myEvents"><a href="javascript:void(0)">HELP LINKS</a>
										<ul>
                                        	<li><a style="font-size:12px;"  href="<?php echo ABSOLUTE_PATH; ?>help_support.php">Pangea Help</a></li>
										</ul>
									</li>
									<li class="border"></li>
								</ul>
							</div> <!-- /eventManger_left -->
							<div class="eventManger_right">
								<?php 
									if($_GET['p'] == 'patient')
										include("dr_patient.php");
									elseif($_GET['p'] == 'invite-patient')
										include("patient_invitation.php");																						
									elseif($_GET['p'] == 'protocols')
										include("view_protocols.php");
									elseif($_GET['p'] == 'plans')
										include("view_plans.php");
									elseif($_GET['p'] == 'doctors')
										include("view_doctors.php");
									elseif($_GET['p'] == 'test')
										include("dr_test.php");
									elseif($_GET['p'] == 'supplement')
										include("dr_supplement.php");
									elseif($_GET['p'] == 'forms')
										include("novi_forms.php");
									elseif($_GET['p'] == 'add-forms')
										include("add_novi_forms.php");	
									elseif($_GET['p'] == 'videos')
										include("list_videos.php");
									elseif($_GET['p'] == 'create-video')
										include("create_video.php");	
									else								
										include("dr_patient.php");
									
									
									?>
                            </div><!-- /eventManger_right -->
							<br class="clear" />
						</div>
						<br class="clear" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once('includes/footer.php'); ?>