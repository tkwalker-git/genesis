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
		
		$id = $_GET['delete'];
		mysql_query("delete from `products` where `id`='$id' && `user_id`='". $member_id ."'");
		mysql_query("delete from `products_images` where `product_id`='$id'");
		$sucMessage = 'Deal is deleted successfully.';
		
		}
	
?>
<script>
function removeAlert(url) {

	var con = confirm("Are you sure to delete this deal?")

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
					<?php userSubMenu("manage_deals");?>
					
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
								  	<td width="3%">&nbsp;</td>
									<td width="35%">Deal Name</td>
									<td width="19%">Category</td>
									<td width="13%">Sale Price</td>
									<td width="16%">Discount</td>
									
									<td width="14%">Action</td>
								  </tr>
								</table>

								
							</div>
							
							<?php
							$res = mysql_query("select * from `products` where `user_id`='$user_id'");
							$i=0;
							if(mysql_num_rows($res)){?>
							<div class="mngDlsMinHeight">
							
							<?php
							while($row = mysql_fetch_array($res)){
							$i++;
							 if( ($i%2) == 0)
										   $class='class="preferenceBlueBox"';
									 else
										  $class='class="preferenceWhtBox"';
								?>
								<div <?=$class?>>
									<table width="100%" border="0" cellspacing="0" cellpadding="5">
									  <tr>
									  <td width="3%">&nbsp;</td>
										<td width="35%"><a href="<?php echo $event_url; ?>" ><?php echo $row['name'];?></a></td>
										<td width="19%"><?php echo getMarketCategoryName($row['category_id']);?></td>
										<td width="13%"><?php echo $row['sale_price'];?></td>
										<td width="16%"><?php echo $row['discount'];?>%</td>
										
										<td width="14%">
											<a style="color:#0066FF" href="create_deal.php?id=<?php echo $row['id'];?>">Edit</a> - <a style="color:#0066FF" onclick="removeAlert('manage_deals.php?delete=<?php echo $row['id'];?>')" href="javascript:void(0)">Delete</a>										</td>
									  </tr>
									</table>
									</div>
									
									<?php } ?>
									</div>
									<?php } else { ?>
								
								<div class="preferenceWhtBox">
									
									<table width="100%" border="0" cellspacing="0" cellpadding="5">
									  <tr>
										<td width="80%" align="center" style="font-size:20px; font-weight:bold; padding:60px 0px">
											You have not created any deal yet. You can add new deals <a style="color:#0066FF; text-decoration:underline" href="create_deal.php">here</a>.
										</td>
									  </tr>
									</table>
	
									
								</div>
								
								<?php } ?>
							
						</div>
					</div>
				</div>				
			</div>	
		</div>
		</form>
	</div>
		</div>


<div class="clr"></div>

<?php require_once('includes/footer.php');?>