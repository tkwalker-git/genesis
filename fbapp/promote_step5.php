<?php

	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='". ABSOLUTE_PATH ."login.php';</script>";
	
	require_once('../includes/header_fb.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	if ( $_GET['app_db_id'] != ''  ) 
		$_SESSION['app_db_id'] = $_GET['app_db_id'];
	
	$app_db_id 	= $_SESSION['app_db_id'];
	
	$fb_app_id  = attribValue('fb_apps', 'app_id', "where id='". $app_db_id ."'");
	$fb_app_sec = attribValue('fb_apps', 'app_secret', "where id='". $app_db_id ."'");
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
						<div class="head_new">Integrate Your Event Into Your Facebook Fan Page </div>
						<div class="rBox">
							
							<table width="100%" align="center" cellpadding="5" cellspacing="0" >
								<tr>
									<td width="100%" valign="top">
										
										<?php

										$my_url 	= ABSOLUTE_PATH . "fbapp/promote_step5.php";
										
										if ( $_GET['page_id'] != ''  ) 
											$_SESSION['page_id'] = $_GET['page_id'];
										if ( $_GET['uid1'] != '' )
											$_SESSION['fbur_id'] = $_GET['uid1'];
										if ( $_GET['eid'] != '' )	
											$_SESSION['event_id'] = $_GET['eid'];
											
										
										$event_name	= attribValue('events', 'event_name', "where id='". $_SESSION['event_id'] ."'");
										if ( $event_name != '' )
											$custom_name = '&custom_name=' . urlencode($event_name);
										
										$page_id = $_SESSION['page_id'];
										
										$tab_id = $page_id . '/tabs/app_' . $fb_app_id;
										
										$code = $_REQUEST["code"];
										
										if(empty($code)) {
										
										  $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=". $fb_app_id . "&redirect_uri=" . urlencode($my_url). "&scope=manage_pages";
										  echo('<script>top.location.href="' . $dialog_url . '";</script>');
										} else {
										
										  $token_url = "https://graph.facebook.com/oauth/access_token?client_id=" . $fb_app_id . "&redirect_uri=" . urlencode($my_url) . "&client_secret=" . $fb_app_sec . "&code=" . $code;
										  $access_token = file_get_contents($token_url);
										
										  $page_token_url 	= "https://graph.facebook.com/" . $page_id . "?fields=access_token&" . $access_token;
										  $response 		= file_get_contents($page_token_url);
										
										  $resp_obj = json_decode($response,true);
										  
										  $page_access_token = $resp_obj['access_token'];
										  
										  $page_settings_url = "https://graph.facebook.com/" . $page_id . "/tabs?app_id=". $fb_app_id ."&method=POST&access_token=" . $page_access_token;
										  $response 		= file_get_contents($page_settings_url);
										  $resp_obj 		= json_decode($response,true);
										  
											if ($response == 'true') {
												//mysql_query("delete from fb_info where member_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' AND fb_page_id='". $_SESSION['page_id'] ."'");
												mysql_query("insert into fanpages (member_id, fb_page_id, fb_user_id, eg_event_id,fb_app_id) VALUES ('". $_SESSION['LOGGEDIN_MEMBER_ID'] ."','". $_SESSION['page_id'] ."','". $_SESSION['fbur_id'] ."','". $_SESSION['event_id'] ."','". $fb_app_id ."')");
												echo '<div class="evField" style="width:100%; text-align:left; font-size:25px!important; color:#086B00; padding:30px">Tab is added to your fan page. <br><br><a href="promote_step2.php">Add More</a></div>';
												//$page_settings_url = "https://graph.facebook.com/" . $tab_id ."?access_token=" . $page_access_token . $custom_name . "&method=POST&is_non_connection_landing_tab=true";
												$page_settings_url = "https://graph.facebook.com/" . $tab_id ."?access_token=" . $page_access_token . $custom_name . "&method=POST";
												$response 	= file_get_contents($page_settings_url);
												?>
												<script>
													window.location.href='<?php echo ABSOLUTE_PATH;?>fbapp/remove_tab.php';
												</script>
												<?php
											} else
												echo '<div class="evField" style="width:100%; text-align:left; font-size:25px!important; color:#FF0000; padding:30px">Operation Failed. <a href="promote_step2.php">Try Again</a></div>';	
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