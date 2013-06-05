<?php

	error_reporting(E_ALL);
	require_once('../admin/database.php');
	require_once('../site_functions.php');

	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='". ABSOLUTE_PATH ."login.php';</script>";

	require_once('../includes/header.php');
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

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
							<div class="head_new">Fetch your Facebook Fan Pages <a href="<?php echo ABSOLUTE_PATH; ?>myeventwall.php"><img src="<?php echo IMAGE_PATH; ?>back_to_wall.png" align="right" /></a></div>
						<div class="rBox">

						

							<table width="100%" align="center" cellpadding="5" cellspacing="0" >

								<tr>

									<td width="50%" valign="top">

										<?php

											$suc = false;									

											if ( $_GET['connect'] == '' ) {

											?>

											<div> 

												Click the button below to permit EventGrabber to connect to your facebook and list your fan pages 

												<a href="promote_step1.php?connect=1"><img src="<?php echo ABSOLUTE_PATH;?>images/connect_with_facebook.gif" /></a>

											</div>

											<?php

											} else {

												

												include_once('../facebooksdk/facebook.php');

												$facebook  = new Facebook(array(

													'appId'      => FACEBOOK_APP_ID,

													'secret'     => FACEBOOK_SECRET,

												));

												

												$user = $facebook->getUser();

												if ($user) {

													try {

														$user_profile = $facebook->api('/me');

													} catch (FacebookApiException $e) {

														error_log($e);

														$user = null;

													}

												}

												

												if ($user) {

													$logoutUrl = $facebook->getLogoutUrl(array('next'=>"http://restorationhealth.yourhealthsupport.com"));

												} else {

													$loginUrl = $facebook->getLoginUrl(array(

																	'next'=>"http://restorationhealth.yourhealthsupport.com",

																	'scope' => 'manage_pages,email'

																  ));

												 

													echo  "<script>window.parent.location=\"".html_entity_decode($loginUrl)."\"  </script>";

													exit;

												}

												

												$access_token  	= $facebook->getAccessToken();

												$userfacebook  	= $facebook->api("/me");

												$uid 			= $userfacebook["id"];

												

												$fql = "SELECT page_id,page_url,name,type FROM page WHERE page_id IN (SELECT page_id FROM page_admin WHERE uid = " . $uid . ") and name!='' ";

	

												$response = $facebook->api(	

																			array(

																				'method' => 'fql.query',

																				'query' =>$fql,

																			)

																		);

												?>

												<div class="evField" style="width:100%; text-align:center; font-size:20px!important">Congratulations!</div><br />

													Our application will connect to facebook and fetch your facebook fan page. To accomplish this, our application will ask for some permissions on your facebook login widget. Please select "Allow" to make things easy for you.

													<br /><br />

													

													<?php						

													foreach ($response as $value) {

												

														if ( $value['type'] != 'APPLICATION' ) { 

															echo $value['name'] . '<br>';

															mysql_query("insert ignore into fb_user_pages (member_id, fb_user_id, fb_page_id, fb_page_name) VALUES('".

																		$_SESSION['LOGGEDIN_MEMBER_ID']."','".$uid."','".$value['page_id']."','".$value['name']."')");

														}

													}


												if ( count( $response ) > 0 ) {

												?>

													<br><br><a href="promote_step2.php" style="display:block; float:left; margin-right:10px" ><strong>Start Promoting your events</strong></a>

												<?php

												}

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