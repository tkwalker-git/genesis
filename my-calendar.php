<?php 
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
$meta_title	= 'Reviews & Ratings';
include_once('includes/header.php');

	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$name = getSingleColumn('firstname','select * from users where `id`='.$member_id);
	
	if ( ( isset ($_GET['going']) || isset ($_GET['d']) ) && isset($_GET['id']) ) {
		
		if ( $_GET['going'] == 'yes')
			$go = 1;
		else if ( $_GET['going'] == 'no')
			$go = 0;
		else
			$go = -1;
		
		if ( isset($_GET['d']) && !isset($_GET['going'])  ) 
			mysql_query("update event_wall set going='-1' where event_id=" . $_GET['id']);
		else
			mysql_query("update event_wall set going='". $go ."' where event_id=" . $_GET['id']);
			
		if ( isset($_GET['n']) ) 
			mysql_query("insert into event_wall VALUES (NULL,'". $_GET['id'] ."','". $member_id ."','". date("Y-m-d") ."','". $go ."',1,1)");

		echo "<script>window.location.href='my-calendar.php';</script>";
	}
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-1.4.min.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<style>
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
    height: 28px;
    padding: 9px 12px 0;
	}
	
.oddRow {
    border-top: 1px solid #D1D1D1;
    color: #333333;
    float: left;
    width: 906px;
}

.location-title, .location{
	width:206px;
	}
	
.title-row {
    float: left;
    padding: 10px 0;
}

</style>
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="clr"></div>
    <div class="gredBox">
      <?php include('dashboard_menu.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:7px;">
            <!--	<div class="head_new">REVIEWS & RATINGS</div>-->
            <?php include("widget_wall.php"); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>