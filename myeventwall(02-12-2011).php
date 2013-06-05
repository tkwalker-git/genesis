<?php 

	require_once('admin/database.php');
	require_once('site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
	require_once('includes/header.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
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

		echo "<script>window.location.href='myeventwall.php';</script>";
	}
	
	$sql = "select * from members where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 	= DBout($row['name']);
		$email  = DBout($row['email']);
		
		$image	= DBout($row['image_name']);
		
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img class="userImage" align="center" '. $img .' />';	
		} else
			$img = '<img class="userImage" src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';	
		
		$total_events 	= getSingleColumn('tot',"select count(*) as tot from events where userid=" . $member_id);
		$total_rated 	= getSingleColumn('tot',"select count(*) as tot from comment where c_type='event' AND by_user=" . $member_id);
		$total_rated_v 	= getSingleColumn('tot',"select count(*) as tot from comment where c_type='venue' AND by_user=" . $member_id);
		$total_onwall 	= getSingleColumn('tot',"select count(*) as tot from event_wall where userid=" . $member_id);
		
		$total_comments = getSingleColumn('tot',"select count(*) as tot from comment where by_user=" . $member_id);
		$helpfull_rev	= getSingleColumn('tot',"SELECT count(*) FROM review_helpfull WHERE status=1 and review_id IN (select id from comment where userid='" . $member_id. "')");
		
		$total_friends_invited = getSingleColumn('tot',"select count(*) as tot from member_referals where ref_member_id=" . $member_id);
		
		$helpfull_percent = 0 ;
		if ( $total_comments > 0 )  
			$helpfull_percent = ceil( ($helpfull_rev/$total_comments) * 100);
	}
	
?>

<div id="main">
	<div id="main-inner">
		
		<div id="contents">
			<div id="contents-top"></div>
				<div id="contents-middle">
					
					<div class="performance_block">
	
						<div class="performance_left">
						
							
							<?php echo $img;?>
							
							<div class="awatar_right">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
								  <tr>
									<td colspan="3" class="heading_dark">Your Performance</td>
								  </tr>
								  
								  <tr><td colspan="3">&nbsp;</td></tr>
								  
								  <tr>
									<td colspan="3" class="heading_colored">Events Status</td>
								  </tr>
								  <tr>
									<td class="heading_dark_14">Events on Wall: <?php echo $total_onwall;?></td>
									<td width="25%">&nbsp;</td>
									<td class="heading_dark_14">Events Posted: <?php echo $total_events;?></td>
								  </tr>
								  
								  <tr>
									<td class="heading_dark_14">Events Rated: <?php echo $total_rated;?></td>
									<td width="25%">&nbsp;</td>
									<td class="heading_dark_14">User Links: 96%</td>
								  </tr>
								  
								  <tr><td colspan="3">&nbsp;</td></tr>
								  <tr><td colspan="3">&nbsp;</td></tr>
								  <tr><td colspan="3">&nbsp;</td></tr>
								  
								  <tr>
									<td class="heading_colored">Venue Status</td>
									<td width="25%">&nbsp;</td>
									<td class="heading_colored">Social Status</td>
								  </tr>
								  
								   <tr>
									<td class="heading_dark_14">Total Reviews: <?php echo $total_rated_v;?></td>
									<td width="25%">&nbsp;</td>
									<td class="heading_dark_14">Friends Invited: <?php echo $total_friends_invited;?></td>
								  </tr>
								  
								  <tr>
									<td class="heading_dark_14">Helpful Rank: <?php echo $helpfull_percent;?>%</td>
									<td width="25%">&nbsp;</td>
									<td class="heading_dark_14"><!--Hangout Groups: 4-->&nbsp;</td>
								  </tr>
								  
								  <tr><td colspan="4" align="left" style="padding-top:20px">
								  <a href="javascript:void(0)" onclick="windowOpener(525,625,'Terms and Conditions','cimport/invite_friends.php')" style="display:block; clear:both;float:none; ">
										<img src="<?php echo IMAGE_PATH;?>invite_friends.png"  />
									</a>
								  </td></tr>
								</table>
				
							
							</div><!--end awatar_right-->
							<div class="clr"></div>
						
						</div><!--end performance_left-->
						
						
						<div class="performance_right">
						<?php 
							
							$total_subcats 	= getSingleColumn('tot',"select count(*) as tot from sub_categories");
							$total_pref 	= getSingleColumn('tot',"select count(*) as tot from member_prefrences where member_id=" . $member_id);
							
							if($total_pref < $total_subcats){
						?>
						
							<form name="signfrm" id="signfrm" method="post" action="event_preference_setting.php" enctype="multipart/form-data">
							<table width="100%" border="0" cellspacing="5" cellpadding="0">
								  <tr>
									<td colspan="4" class="heading_dark">How often do you prefer...</td>
								  </tr>
								  
								  <tr><td colspan="4">&nbsp;</td></tr>
								  
								  <tr>
									<td>&nbsp;</td>
									<td class="d_style">Never</td>
									<td class="d_style">Sometimes</td>
									<td class="d_style">Often</td>
								  </tr>
								  <?php
								  	
									$rsc = mysql_query("select * from sub_categories where id NOT IN (select prefrence_type from member_prefrences where member_id='" . $member_id."') LIMIT 6 ");
									while ( $rowc = mysql_fetch_assoc($rsc) ) {
										$cid		= $rowc['id'];
										$attr_name 	= DBout($rowc['name']);
										$attr_name	= ucwords($attr_name);
										
										
								  ?>
								  <tr>
									<td class="heading_dark_14"><?php echo $attr_name?> event</td>
									<td align="center"><input type="radio" name="nl1_<?php echo $cid;?>" value="N"  /></td>
									<td align="center"><input type="radio" name="nl1_<?php echo $cid;?>" value="S" /></td>
									<td align="center"><input type="radio" name="nl1_<?php echo $cid;?>" value="O" /></td>
								  </tr>
								  
								  <tr><td colspan="4" style="font-size:5px">&nbsp;</td></tr>
								  <?php } ?>
								  <tr><td colspan="4" style="font-size:5px; text-align:right" align="right">
								    <input type="hidden" name="evwall" value="1"/>
								  	<input name="continue1" id="continue1" type="image" src="images/save_setting_btn_new.gif" class="vAlign" vspace="10" hspace="10"/>
								  </td></tr>
								  
							</table>
							</form>
						<?php } ?>
						</div><!--end performance_right-->
						<div class="clr"></div>
					</div><!--end performance_block-->
					
					<div class="navigation_bar">
 
					  <div class="nav">
					   
						   <div class="menu_heading heading_colored_14"><a href="<?php echo ABSOLUTE_PATH; ?>profile_setting.php">Edit Preferences</a></div><!--end menu_heading-->
						   
						   <div class="menuBlock">
						   
							<ul>
							<?php if ( $_GET['type'] == 'reviews' ) { ?>
								<li class="libutton"><a href="myeventwall.php?type=eventwall"><span>My Eventwall</span></a></li>
								<li class="libutton"><a href="" class="a_active"><span class="span_active">Reviews and Ratings</span></a></li>
								<li class="libutton"><a href="myeventwall.php?type=recomemded"><span>Recommended Events</span></a></li>
							<?php } else if ( $_GET['type'] == 'eventwall' ) { ?>
								<li class="libutton"><a href="" class="a_active"><span class="span_active">My Eventwall</span></a></li>
								<li class="libutton"><a href="myeventwall.php?type=reviews"><span>Reviews and Ratings</span></a></li>
								<li class="libutton"><a href="myeventwall.php?type=recomemded"><span>Recommended Events</span></a></li>
							<?php	} else { ?>
							   <li class="libutton"><a href="myeventwall.php?type=eventwall"><span>My Eventwall</span></a></li>
								<li class="libutton"><a href="myeventwall.php?type=reviews"><span>Reviews and Ratings</span></a></li>
								<li class="libutton"><a href="" class="a_active"><span class="span_active">Recommended Events</span></a></li>
								
							<?php } ?>	
							</ul>
						   
						   </div><!--end menu-->
						   
						  </div><!--end nav-->
					  
					 </div><!--end navigation_bar-->
					<?php 
						
						if ( $_GET['type'] == 'reviews' )
							include_once("widget_reviews.php");
						else if ( $_GET['type'] == 'eventwall' )
							include_once("widget_wall.php");	
						else
							include_once("widget_recomemded.php");
							 
					?>
					<!-- events -->
				</div><!-- contents-middle-->
			<div id="contents-bottom"></div>	
		</div><!-- contents -->
	</div><!-- main-inner -->
</div><!-- main -->
<div class="clr"></div>
<?php require_once('includes/footer.php');?>