<?php 
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		

$meta_title	= 'My Calendar';
include_once('includes/header.php');		
?>



	<script src="myCalendar/dhtmlxscheduler.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="myCalendar/dhtmlxscheduler.css" type="text/css" title="no title" charset="utf-8">
	<script src="myCalendar/dhtmlxscheduler_readonly.js" type="text/javascript" charset="utf-8"></script>
	
<style type="text/css" media="screen">
	html, body{
		height:100%;
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
</style>

<script type="text/javascript" charset="utf-8">
	function init() {
		
		scheduler.config.xml_date="%Y-%m-%d %H:%i";
		scheduler.init('scheduler_here',new Date(),"month");
		scheduler.config.readonly_form = true;

		scheduler.attachEvent("onBeforeDrag",function(){return false;})
		scheduler.attachEvent("onClick",function(){return false;})
		scheduler.config.details_on_dblclick = true;
		scheduler.config.dblclick_create = false;
		
		scheduler.load("calendar_events_patient.php");
		
	}
	
$(document).ready(function(){
	init();
});

</script>


<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="clr"></div>
    <div class="gredBox">
      <?php include('dashboard_menu_tk.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
			<div class="whiteMiddle" style="padding-top:7px;">

<!---- ------------------------------------------------------------------------------------------------------------------ --------->
				<div class="head_new">MY CALENDAR</div> <!-- /yellow_bar -->
				<div class="recBox">
					<div style="height:600px; width:100%; margin:auto">
						<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
							<div class="dhx_cal_navline">
								<div class="dhx_cal_prev_button">&nbsp;</div>
								<div class="dhx_cal_next_button">&nbsp;</div>
								<div class="dhx_cal_today_button"></div>
								<div class="dhx_cal_date"></div>
								<div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
								<div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
								<div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
							</div>
							<div class="dhx_cal_header"></div>
							<div class="dhx_cal_data"></div>
						</div>
					</div>
				</div>
<!---- ----------------------------------------------------------------------------------------------------------------- ---------->

				
			</div>
		</div>
	</div>
</div>
</div>
</div>
				
<?php include_once('includes/footer.php'); ?>

<!-- </body> -->