<?php
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
	echo "<script>window.location.href='login.php';</script>";

$meta_title = 'Settings';
$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor')
	$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
else
	$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");

include_once('includes/header.php');
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
$current_page = curPageURL();
?>

<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery.ui.datepicker.js"></script>
<link rel="stylesheet" media="all" type="text/css" href="<?php echo ABSOLUTE_PATH;?>js/jquery-ui-timepicker.css" />
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-ui-timepicker.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/new_modules.js"></script>
<script>
	$(function() {
		 var date = new Date();
         var currentMonth = date.getMonth();
         var currentDate = date.getDate();
         var currentYear = date.getFullYear();
		$( "#date" ).datepicker({
			maxDate: new Date(currentYear, currentMonth, currentDate),
			dateFormat: "mm/dd/yy",
			changeMonth: true,
			changeYear: true
			
		});
	  $('#datepickerImage').click(function() {
      		$('#date').datepicker('show');
		});
		$('#time').timepicker({
			hourGrid: 4,
			minuteGrid: 10,
			timeFormat: 'hh:mm TT'
		});
		$('#timePickerImage').click(function() {
      		$('#time').timepicker('show');
		});
	});
</script>
<script>
	$(document).ready(function(){
		var height = $('.eventManger_right').height();
		$('.eventManger_left').css('height',height-40);
		
	});
</script>
<script type="text/javascript">
	function printElem(){
		var DocumentContainer = document.getElementById('toPrint');
		var WindowObject = window.open('', "PrintWindow", "width=1024,height=800,top=10,left=10,toolbars=no,scrollbars=yes,status=no,resizable=yes");
		WindowObject.document.writeln(DocumentContainer.innerHTML);
		WindowObject.document.close();
		WindowObject.focus();
		WindowObject.print();
		WindowObject.close();
	}
	function editType(){
		showOverlayer("blood_gluco_type.php?page=<?php echo $current_page;?>");
		}
</script>


		
<style type="text/css">
.font{
	font-family: Arial,Verdana,Helvetica,sans-serif;
	}
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
.alert{
  padding: 8px 35px 8px 14px;
  margin-bottom: 20px;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
  background-color: #fcf8e3;
  border: 1px solid #fbeed5;
  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;
}
.alert-success{
  color: #468847;
  background-color: #dff0d8;
  border-color: #d6e9c6;
}
.alert-error {
  color: #b94a48;
  background-color: #f2dede;
  border-color: #eed3d7;
}
.print_edit a{
	font-size:12px;
	color:#C6DDEB;
	text-decoration:none !important;}
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
						<div class="head_new">Blood Glucose Tracker</div>
						<div class="recBox">
							<div class="eventManger_left">
								<ul>
									<li class="icon_myEvents"><a href="javascript:void(0)" style="cursor:default">Blood Glucose</a>
										<ul>
											<li><a href="blood_gluco.php" <?php if($_GET['p']==''){ echo "class='active'";} ?>>Add Reading</a></li>
											<li><a href="?p=log" <?php if($_GET['p']=='log'){ echo "class='active'";} ?>>View Log</a></li>
											<li><a href="?p=report" <?php if($_GET['p']=='report'){ echo "class='active'";} ?>>Reporting</a></li>
										</ul>


									</li>


								</ul>
							</div> <!-- /eventManger_left -->

							<div class="eventManger_right">
							<?php 

	if($_GET['p']=='add')
		include('blood_gluco_add.php');
	elseif($_GET['p']=='log')
		include('blood_gluco_log.php');
	elseif($_GET['p']=='report')
		include('blood_gluco_report.php');
	else
		include('blood_gluco_add.php');
	/* include('patient-sync-calendars.php'); */
?>
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