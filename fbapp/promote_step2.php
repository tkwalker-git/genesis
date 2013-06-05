<?php
	
	error_reporting(E_ALL);

	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='". ABSOLUTE_PATH ."login.php';</script>";
	
	require_once('../includes/header.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	if ( $_POST['page_id'] != "" ) {
		$occupiedAppsArr = array();
		$fb_page_id = $_POST['page_id'];
		$sql = "select * from fanpages where member_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' AND fb_page_id='". $fb_page_id ."'";
		$res = mysql_query($sql);
		while ( $pages  = mysql_fetch_assoc($res ) ) 
			//echo $pages['fb_app_id'];
			$occupiedAppsArr[] = attribValue('fb_apps', 'id', "where app_id = '". $pages['fb_app_id'] ."'"); ;

		if ( count( $occupiedAppsArr) > 0 ) 
			$occupiedApps = implode("-",$occupiedAppsArr);
		
		?>
		<script>
			window.location.href='promote_step3.php?already=<?php echo $occupiedApps;?>&pageid=<?php echo $fb_page_id;?>';
		</script>
		<link rel="stylesheet" href="../markeeting.css" type="text/css"  />
		<?php
			
	}
	
?>

<style>
.whiteMiddle .evField {
    color: #00ABDF;
    float: left;
    font-size: 12px;
    font-weight: bold;
    padding: 11px 7px 11px 0;
	}
.grayRoundBox {
    width: 909px;
	}
.integrate{
	background:#c0c0c0;
	color:#FFFFFF;
	padding:10px 30px;
	font-size:20px;
}
.dash_menu{
	position:relative;
	height:67px;
	}
	
.dash_menu table{
	}
	
.dash_menu td{
	padding:0 12px;
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
	background:url(../images/dashB_bar.gif) repeat-x;
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
	
.recBox .rBox{
	padding:17px 10px 10px 26px
}

.eventManger_left{
	background:url(../images/eventManger_leftBg.gif) repeat-x #1c6722;
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
	background:url(../images/icon_myEvents.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}
	
.eventManger_left ul .icon_venues{
	background: url("../images/icon_venues.png") no-repeat scroll 9px 2px transparent;
	line-height: 30px;
	}
	
.eventManger_left ul .icon_manageteam{
	background:url(../images/icon_manageTeam.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}
	
.eventManger_left ul .icon_contact{
	background:url(../images/icon_contact.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}
	
.eventManger_left ul .icon_reports{
	background:url(../images/icon_reports.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}
	
.eventManger_left ul .icon_promotions{
	background:url(../images/icon_promotions.png) no-repeat scroll 9px 2px transparent;
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
	
	.eventManger_right{
	width:719px;
	float:left;
	border-left:#CBCBCB solid 1px;
	min-height:637px;
	}
	.eventManger_right table a{
	color:#006695;
	text-decoration:underline
	}

.eventManger_right table a:hover{
	text-decoration:none;
	}
</style>


<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="clr"></div>
    <div class="gredBox">
	<?php include('../dashboard_menu_tk.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
			<div class="whiteMiddle" style="padding-top:1px;">
				
					<div class="recBox">
					
					<?php require_once('../eventManger_left.php'); ?>
							 <!-- /eventManger_left -->
						<div class="eventManger_right">
						<div class="head_new">You are almost there!</div>
						
							<div class="rBox">
							<table width="100%" align="center" cellpadding="5" cellspacing="0" >
								<tr>
									<td width="50%" valign="top">
										<div class="evField" style="width:100%; text-align:center; font-size:20px!important">Select Fan Page</div><br />
										Below dropdown will show your available Fan pages on Facebook. Please select the page where you want to add E-Flyer and Press next button. You are almost there...
										<br /><br />
										<a href="promote_step1.php" style="color:#0066FF"><strong>Click Here</strong></a> if the Fan Page list is not updated OR you do not see your fan page in the list below.</a>
										<br /><br />
										<div class="evField" style="width:100%; text-align:left; font-size:15px!important"></div><br />
										
										<?php
											
										$suc = false;
										
										$sql1 = "select * from fb_user_pages where member_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
										$rs1 = mysql_query($sql1);
										if ( mysql_num_rows ($rs1 ) > 0 ) {
											
										?>
											<form method="post" enctype="multipart/form-data" name="paeSelector" >
											<strong>Available Fan Pages: </strong><br /><br />
											<select name="page_id" id="page_id" style="width:300px" >
											<option selected="selected" value="-1">--Select--</option>
											<?php
											while ($r1 = mysql_fetch_assoc($rs1) ) {
											?>
												<option value="<?php echo $r1['fb_page_id'] ;?>"><?php echo $r1['fb_page_name'] ;?></option>
											<?php
											}
											?>
											</select>
											<br><br />
											<input type="image" src="../images/singup-next.png" />

											</form>
										<?php	
										} else {
										?>		
										<div class="evField" style="width:100%; text-align:center; font-size:25px!important; color:#FF0000; padding:30px">OOPS. You don't have any Fan Page. <br />Click <a href="promote_step1.php" style="color:#0066FF"><strong>here</strong></a> to fetch your Fan Pages</div><br />
										<?php
										}
										?>
									</td>
								</tr>
							</table>
							</div>
							</div>
							
							<br class="clear" />
							
						</div>
            </div> <!-- end whiteMiddle -->
        </div>
      </div>
      <div class="create_event_submited"> </div>
    </div>
  </div>
</div>

<div class="clr"></div>
<?php require_once('../includes/footer.php');?>