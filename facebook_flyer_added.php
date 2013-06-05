<?php

	require_once('admin/database.php');
	require_once('site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
	
	require_once('includes/header_fb.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	$sql = "select * from users where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 		= DBout($row['firstname']);
		$lname 		= DBout($row['lastname']);
		$email  	= DBout($row['email']);
		$zip 		= DBout($row['zip']);
		$gender 	= DBout($row['sex']);
		$usern  	= DBout($row['username']);
		$dob  		= DBout($row['dob']);
		$password	= DBout($row['password']);
		
		if ( $dob == '0000-00-00' )
			$dob = '';
		
		if ( $dob != '' )
			$dob = date("m/d/Y",strtotime($dob));
		
		$image	= DBout($row['image_name']);
		$bc_image  = $image;
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img align="center" '. $img .' />';	
		} else
			$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';	
		
		$total_events 	= getSingleColumn('tot',"select count(*) as tot from events where userid=" . $member_id);
		
		$total_events_grabbed = 0;
	}
	
?>



<div class="topContainer">
		<div>
			<div class="profileBox">
				<div class="fl"><?php echo $img;?></div>
				<div class="profileDetail">
				<strong class="lightBlueClr">&nbsp;</strong><br />
				<strong class="lightBlueClr"><u><?php echo $logged_in_member_name;?></u></strong><br />
				Events Grabbed: <strong class="lightBlueClr"><?php echo $total_events_grabbed;?></strong><br />
				Events Posted: <strong class="lightBlueClr"><?php echo $total_events;?></strong><br />
				Reviews: <strong class="lightBlueClr"><?php //echo showuserreviewevents();?></strong><br />
               <a href="profile_setting.php" class="lbLink">Profile Setting</a>
				</div>
				<div class="clr"></div>
			</div>
         
			<div class="friendsCon">

			</div>

			<!-- code added ends-->
			<div class="clr"></div>
		</div>
	</div>
	<!--End Banner Part -->
	<!--Start Middle Part -->
	<div class="middleConOu">
		<div class="middleContainer">
			<div class="tacConBot">
				<div class="ProfileSettingTab">
					<?php userSubMenu("facebook_settings");?>
				</div>
				<div class="fr"><!--<a href="add_event.php"><img src="images/add_event_btn.gif" alt="" border="0" /></a>--></div>
				<div class="clr"></div>
			</div>
		
		<div class="grayRoundBox">
				<div class="grayLBC">
					<div class="grayRTC">
						<div class="grayLTC">
							<table width="100%" align="center" cellpadding="5" cellspacing="0" >
								<tr>
									<td width="50%" valign="top">
										
										<?php

										$my_url 	= ABSOLUTE_PATH . "facebook_flyer_added.php";
										
										if ( $_GET['page_id'] != ''  ) {
											$_SESSION['page_id'] = $_GET['page_id'];
											$_SESSION['fbur_id'] = $_GET['uid1'];
											$_SESSION['event_id'] = $_GET['eid'];
										}	
										
										$page_id = $_SESSION['page_id'];
										
										$tab_id = $page_id . '/tabs/app_' . FACEBOOK_APP_ID;
										
										$code = $_REQUEST["code"];
										
										if(empty($code)) {
										
										  $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=". FACEBOOK_APP_ID . "&redirect_uri=" . urlencode($my_url). "&scope=manage_pages";
										  echo('<script>top.location.href="' . $dialog_url . '";</script>');
										} else {
										
										  $token_url = "https://graph.facebook.com/oauth/access_token?client_id=" . FACEBOOK_APP_ID . "&redirect_uri=" . urlencode($my_url) . "&client_secret=" . FACEBOOK_SECRET . "&code=" . $code;
										  $access_token = file_get_contents($token_url);
										
										  $page_token_url 	= "https://graph.facebook.com/" . $page_id . "?fields=access_token&" . $access_token;
										  $response 		= file_get_contents($page_token_url);
										
										  $resp_obj = json_decode($response,true);
										  
										  $page_access_token = $resp_obj['access_token'];
										  
										  $page_settings_url = "https://graph.facebook.com/" . $page_id . "/tabs?app_id=". FACEBOOK_APP_ID ."&method=POST&access_token=" . $page_access_token;
										  $response 		= file_get_contents($page_settings_url);
										  $resp_obj 		= json_decode($response,true);
										  
											if ($response == 'true') {
												mysql_query("insert into fb_info (member_id, fb_page_id, fb_user_id, eg_event_id) VALUES ('". $_SESSION['LOGGEDIN_MEMBER_ID'] ."','". $_SESSION['page_id'] ."','". $_SESSION['fbur_id'] ."','". $_SESSION['event_id'] ."')");
												echo '<div class="evField" style="width:100%; text-align:left; font-size:25px!important; color:#086B00; padding:30px">Tab is added to your fan page.</div>';
												$page_settings_url = "https://graph.facebook.com/" . $tab_id ."?access_token=" . $page_access_token . "&method=POST&is_non_connection_landing_tab=true";
												$response 		= file_get_contents($page_settings_url);
											} else
												echo '<div class="evField" style="width:100%; text-align:left; font-size:25px!important; color:#FF0000; padding:30px">Operation Failed.</div>';	
											
										 
										}
										
										?>
									</td>
									<td width="50%" valign="top">
										<div class="evField" style="width:90%; text-align:center; font-size:20px!important">Already Connected Events</div><br>
										<table width="100%" cellpadding="5" cellspacing="0" align="left" style="border:#000000 solid 1px" rules="all">
										<tr>
											<td align="left" height="30" width="50%" style="background-color:#666666; color:#FFFFFF"><strong>Event Name</strong></td>
											<td align="left" height="30" width="50%" style="background-color:#666666; color:#FFFFFF"><strong>Facebook Page</strong></td>
										</tr>
										
										<?php 
											$sql2 = "select *,(select fb_page_name from fb_user_pages where fb_page_id=fb_info.fb_page_id) as fb_pg_name,(select event_name from events where id=fb_info.	eg_event_id ) as event_name from fb_info where member_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
											$rs2  = mysql_query($sql2);
											while ( $r2 = mysql_fetch_assoc($rs2) ) {	
										?>
										
										<tr>
											<td align="left" height="20" valign="top" ><?php echo $r2['event_name'];?></td>
											<td align="left" height="20" valign="top" ><?php echo $r2['fb_pg_name'];?></td>
										</tr>
										<?php } ?>
										</table>
									</td>
										
								</tr>
							</table>
							
							
						</div>
					</div>
				</div>
			</div>
		
		</div>
	</div>

<div class="clr"></div>
<?php require_once('includes/footer.php');?>