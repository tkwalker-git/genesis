<?php 
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

	$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
	$meta_title	= 'Event Manager';
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];


if ( $_GET['delete'] > 0 ) {		
	if(validateEID($_SESSION['LOGGEDIN_MEMBER_ID'],'events',$_GET['delete'],'') =='true'){
	$dEvent_id = $_GET['delete'];
	if ( mysql_query("delete from events where id='$dEvent_id' AND userid='". $member_id ."'") ) {
		$dateID	= getSingleColumn("id","select * from `` where `event_id`='". $dEvent_id ."'");
		if($dateID){
			mysql_query("delete from event_dates where event_id='". $dEvent_id ."'");
			mysql_query("delete from event_times where date_id='". $dateID ."'");
		}
		$sucMessage = 'Event is deleted successfully.';	
	}
	}
	else {
	echo "<script>window.location.href='activity_manager.php?p=".$_GET['p']."';</script>";
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
	padding:0 25px;
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
	min-height:597px;
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
						<div class="head_new">SHOWCASE CREATOR&#0153; MANAGER</div>
						<div class="recBox">
							<?php require_once('eventManger_left.php'); ?>
							 <!-- /eventManger_left -->
							 
							<div class="eventManger_right">
								<?php								
									
								if($_GET['p'] == 'community-events')
									include("community-events.php");									
																	
								elseif($_GET['p'] == 'clinic-events')
									include("private-events.php");
								
								elseif($_GET['p'] == 'showcase')
									include("manageShowcase.php");									
									
								elseif($_GET['p'] == 'promote')
									include("fbapp/promote_step2.php");
									
								elseif($_GET['p'] == 'promote_step1')
									include("fbapp/promote_step1.php");
									
								elseif($_GET['p'] == 'promote_step3')
									include("fbapp/promote_step3.php");
									
								elseif($_GET['p'] == 'promote_step4')
									include("fbapp/promote_step4.php");
									
								elseif($_GET['p'] == 'promote_step5')
									include("fbapp/promote_step5.php");
									
								elseif($_GET['p'] == 'community-events')
									include("community-events.php");
									
								else
									include("manageShowcase.php");
								?>
							</div> <!-- /eventManger_right -->
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