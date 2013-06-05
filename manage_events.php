<?php

	require_once('admin/database.php');
	require_once('site_functions.php');
	
	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
	
	require_once('includes/header.php');
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	$sql = "select * from users where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 	= DBout($row['firstname']);
		$email  = DBout($row['email']);
		
		$image	= DBout($row['image_name']);
		
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img align="center" '. $img .' />';	
		} else
			$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';	
		
		$total_events 	= getSingleColumn('tot',"select count(*) as tot from events where userid=" . $member_id);
		
		$total_events_grabbed = 0;
	}
	
	if ( $_GET['delete'] > 0 ) {
		
		$dEvent_id = $_GET['delete'];
		if ( mysql_query("delete from events where id='$dEvent_id' AND userid='". $member_id ."'") ) {
			mysql_query("delete from event_dates where event_id='$dEvent_id'");
			mysql_query("delete from event_music where event_id='$dEvent_id'");
			mysql_query("delete from event_wall where event_id='$dEvent_id'");
			mysql_query("delete from venue_events where event_id='$dEvent_id'");
			
			$sucMessage = 'Event is deleted successfully.';
			
		}
		
	}
	
?>
<script>
function removeAlert(url) {

	var con = confirm("Are you sure to delete this event? Your event will also be deleted from Event's Wall of all other members.")

	if (con) 

		window.location.href = url;

}
</script>
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
         
			<!-- code added by kshitiz dixit -->
			<div class="friendsCon">
			</div>

			
			<div class="clr"></div>
		</div>
	</div>
	<!--End Banner Part -->
	<!--Start Middle Part -->
	<div class="middleConOu">
		<div class="middleContainer">
			<div class="tacConBot">
				<div class="ProfileSettingTab">
					<?php userSubMenu("manage_events");?>
					
				</div>
				<div class="fr"><!--<a href="add_event.php"><img src="images/add_event_btn.gif" alt="" border="0" /></a>--></div>
				<div class="clr"></div>
			</div>
	<!--	<div class="topContainer"  style="padding-top:20px;">-->
	<form action="" method="post" name="manage_events" id="manage_events">
	<!-- Start Middle-->
	<div id="middleContainer">
		<div class="eventMdlBg">
			<div class="eventMdlMain">				
				<div class="preferenceCon">
					<div class="preferenceBotBg">
						<div class="preferenceTopBg">
							<font color='green'><?php echo $sucMessage;?> </font>
							<br>
							<div style="background-color:#EEEEEE; border-bottom:#CCCCCC solid 2px; border-top:#CCCCCC solid 2px; font-size:14px; font-weight:bold">
								
								<table width="100%" border="0" cellspacing="0" cellpadding="5">
								  <tr>
									<td width="50%">Event Name</td>
									<td width="15%">Event Type</td>
									<td width="15%">Category</td>
									<td width="10%">End Date</td>
									<td width="10%">Action</td>
								  </tr>
								</table>

								
							</div>
							<?php  
								
								$sql = "select *,(select event_date from event_dates where event_id=events.id order by event_date DESC LIMIT 1) as dt from events where userid=" . $member_id;
								$res = mysql_query($sql);
								$i=0;
								if ( mysql_num_rows($res) > 0 ) {
									while ( $row = mysql_fetch_assoc($res) ) {
										
										$event_id		= $row['id'];
										$event_name 	= DBout($row['event_name']);
										$category 		= attribValue('categories', 'name', "where id='". $row['category_id'] ."'");
										$scategory	 	= attribValue('sub_categories', 'name', "where id='". $row['subcategory_id'] ."'");
										$date			= date("m/d/Y",strtotime($row['dt']));
										$event_url		= getEventURL($event_id);
	
									 if( ($i%2) == 0)
										   $class='class="preferenceWhtBox"';
									 else
										  $class='class="preferenceBlueBox"';
								?>
								<div <?=$class?>>
									
									<table width="100%" border="0" cellspacing="0" cellpadding="5">
									  <tr>
										<td width="50%"><a href="<?php echo $event_url; ?>" ><?php echo $event_name;?></a></td>
										<td width="15%"><?php echo $category;?></td>
										<td width="15%"><?php echo $scategory;?></td>
										<td width="10%"><?php echo $date;?></td>
										<td width="10%">
											<a style="color:#0066FF" href="edit_event.php?id=<?php echo $event_id;?>">Edit</a> - <a style="color:#0066FF" onclick="removeAlert('manage_events.php?delete=<?php echo $event_id;?>')" href="javascript:void(0)">Delete</a>
										</td>
									  </tr>
									</table>
	
									
								</div>
	
								<?php $i++; } } else { ?>
								
								<div class="preferenceWhtBox">
									
									<table width="100%" border="0" cellspacing="0" cellpadding="5">
									  <tr>
										<td width="80%" align="center" style="font-size:20px; font-weight:bold; padding:60px 0px">
											You have not created any event yet. You can add new events <a style="color:#0066FF; text-decoration:underline" href="add_event.php">here</a>.
										</td>
									  </tr>
									</table>
	
									
								</div>
								
								<?php } ?>
							<div align="right">
								<a href="myeventwall.php"><img src="images/back_event_well_btn.gif" class="vAlign" vspace="10" hspace="10" border="0"/></a> 
							</div>
						</div>
					</div>
				</div>				
			</div>	
		</div>
		</form>
	</div>
		</div>
	</div>

<div class="clr"></div>

<?php require_once('includes/footer.php');?>