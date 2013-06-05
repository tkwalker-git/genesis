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
										<div class="evField" style="width:100%; text-align:center; font-size:20px!important">Connect to Facebook</div><br />
										Our application will connect to facebook and add your E-Flyer to your facebook fan page. To accomplish this, our application will ask for some permissions on your facebook login widget. Please select "Allow" to make things easy for you.
										
										<div class="evField" style="width:100%; text-align:left; font-size:15px!important">STEP 1: Select your E-Flyer</div><br />
										
										<?php
											
										$suc = false;
										
										$sql1 = "select * from events where userid='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' AND event_type='1' AND event_status='1'";
										$rs1 = mysql_query($sql1);
										if ( mysql_num_rows ($rs1 ) > 0 ) {
											?>
											
											<strong>Please Select your E-Flyer: </strong><br />
											<select name="event_id" id="event_id" style="width:300px" >
											<option selected="selected" value="-1">Select</option>
											<?php
											while ($r1 = mysql_fetch_assoc($rs1) ) {
											?>
												<option value="<?php echo $r1['id'] ;?>"><?php echo $r1['event_name'] ;?></option>
											<?php
											}
											?>
											</select>
											<br><br />
											
											<?php
											include_once('facebook.php');
											
											$cookie 	= get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);
											$url		= "https://graph.facebook.com/me?access_token=" .$cookie['access_token']."&scope=email,manage_pages";
											
											$ch = curl_init($url);
											curl_setopt($ch, CURLOPT_HEADER, 0);
											curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
											curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
											
											$xml_resp 	= curl_exec($ch);
											curl_close($ch);
											$userfacebook = json_decode($xml_resp);
											
											//var_dump($userfacebook);
											if($userfacebook->id != '')
											{
												$facebook2 = new Facebook(array(
														'appId'      => FACEBOOK_APP_ID,
														'secret'     => FACEBOOK_SECRET,
														'cookie'     => true
												));
												
												$uid 	= $userfacebook->id;
												
												$fql = "SELECT page_id,page_url,name,type FROM page WHERE page_id IN (SELECT page_id FROM page_admin WHERE uid = " . $uid . ") and name!='' ";
											 
												$response = $facebook2->api(	
																			array(
																				'method' => 'fql.query',
																				'query' =>$fql,
																			)
																		);
												?>
												<strong style="color:#333333; font-family:Arial, Helvetica, sans-serif; font-size:12px">Please Select Page where you want to add E-Flyer: </strong><br>
												<select name="page_id" id="page_id" style="width:300px" >
												<option selected="selected" value="-1">Select</option>
												<?php
												foreach ($response as $value) {
													if ( $value['type'] != 'APPLICATION' ) { 
														
														mysql_query("insert ignore into fb_user_pages (member_id, fb_user_id, fb_page_id, fb_page_name) VALUES('".$_SESSION['LOGGEDIN_MEMBER_ID']."','".$uid."','".$value['page_id']."','".$value['name']."')");
														
													?>
														<option value="<?php echo $value['page_id'] ;?>"><?php echo $value['name'] ;?></option>
												<?php	}
												}
												?>
												</select>
												
												<script>
												function redirectPage()
												{
													id = document.getElementById("page_id").value;
													eid = document.getElementById("event_id").value;
													if ( id != -1 && eid != -1 )
														window.location.href = 'fbapp/facebook_flyer_added.php?page_id='+id + '&uid1=<?php echo $uid;?>&eid='+eid ;
													else
														alert('Please select Event and Fan Page.');	
												}
												</script>
												
												<?php
											}
												
											$facebook = new Facebook( array(
																		  'appId'  => FACEBOOK_APP_ID,
																		  'secret' => FACEBOOK_SECRET,
																		  'cookie' => true, // enable optional cookie support
																	));
											
											if ($session) {
												try {
													$uid = $facebook->getUser();
													$me = $facebook->api('/me');
												} catch (FacebookApiException $e) {
													error_log($e);
												}
											}
											?>
											<br><a href="javascript:void(0)" style="display:block; float:left; margin-right:10px" onclick="redirectPage()"><img src="connect.jpg" width="131" height="22" /></a>
											<?php
											if ( count( $response ) == 0 ) {
										?>
										
										
										
										<div id="fb-root"></div>
										<script src="http://connect.facebook.net/en_US/all.js"></script>
										<script>
												FB.init({appId: '<?= FACEBOOK_APP_ID ?>', status: true, cookie: true, xfbml: true});
												FB.Event.subscribe('auth.login', function(response) {
													login();
												});
												
												FB.Event.subscribe('auth.logout', function(response) {
													logout();
												});
											
												function logout(){
													document.location.href = "";
												}
											
												function login(){
													document.location.href = 'facebook_flyer_settings.php';
												}
												  
											</script>
											<fb:login-button autologoutlink="true"  width="100" background="white" length="short" label="Logout" perms="email,manage_pages"></fb:login-button>
											
										<?php	
											}	
										} else {
										
										?>
										<div class="evField" style="width:100%; text-align:left; font-size:25px!important; color:#FF0000; padding:30px">OOPS. You don't have any Digital Flyer yet.</div><br />
										<?php	
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