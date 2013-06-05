<style>

.whiteRound{
	padding:10px;
	border:#cecece solid 1px;
	background:#fff;
	width:90%;
	margin:0 auto;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	-khtml-border-radius: 10px;
	border-radius: 10px;
	font-size:13px;
	}

.head{
	font-size:18px;
	width:93%;
	margin:0 auto;
	padding-bottom:5px;
	}


</style>
<?php
	$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
?>

	<div class="yellow_bar">
		<table cellpadding="0"  cellspacing="0" width="99%" align="center">
			<tr>
				<td width="20%">VENUE NAME</td>
				<td width="27%">VENUE ADDRESS</td>
				<td width="19%">CITY</td>
				<td width="9%">STATE</td>
				<td width="11%">ZIP</td>
				<td width="14%">ACTIONS</td>
			</tr>
		</table>
	</div> <!-- /yellow_bar -->
	
	<?php
	include("page_form.php");
	$qry2 = "select * from venues where `user_id`=" . $member_id;
//	$qry2 = "select * from venues ORDER BY `id` DESC";
	$rest123 = mysql_query($qry2);
	$totl_records=mysql_num_rows($rest123);
	
	$lim_record=20;		// records per page
	
	$total_pages=ceil($totl_records/$lim_record);     // ceil rounds to ceil number(4.2 to 5)
	$page_num=0; 
	if(isset($_REQUEST['page']))
	{
	$page_num=$_REQUEST['page']; // from pagination_interface.php file..........
	}
	else
	{
	$page_num=1;
	}
	if($page_num==1)
	{
	$start_record=0;  // As we know In mysql database records index starts from 0 
	}
	else
	{
	$start_record= $page_num*$lim_record - $lim_record;
	
	}
	

$sql = "select * from venues where user_id=" . $member_id . " ORDER BY `id` DESC LIMIT $start_record,$lim_record";
//	$sql = "select * from venues ORDER BY `id` DESC LIMIT $start_record,$lim_record";
	$res = mysql_query($sql);
	$i=0;
	$bg = "ffffff";
	if(mysql_num_rows($res)){
		while($row = mysql_fetch_array($res)){
			
		if($bg=='ffffff')
			$bg='f6f6f6';
		else
			$bg = "ffffff";
		?>
		<div class="ev_eventBox" style="background:#<?php echo $bg; ?>">
			<table cellpadding="0" cellspacing="0" width="99%" align="center">
				<tr>
					<td width="20%" valign="top"><?php echo $row['venue_name']; ?></td>
					<td width="27%" valign="top"><?php echo $row['venue_address']; ?></td>
					<td width="19%" valign="top"><?php echo $row['venue_city']; ?></td>
					<td width="9%" valign="top"><?php echo $row['venue_state']; ?></td>
					<td width="11%" valign="top"><?php echo $row['venue_zip']; ?></td>
					<td width="14%" valign="top" style="font-size:11px">
						<a href="#">Edit Venue</a>&nbsp;|&nbsp;
						<a href="#">Delete</a>
					</td>
				</tr>
			</table>
		</div>
		<?php }
		}
		else{
			echo "<div style='padding:40px; text-align:center;color:red'><h2>No Record Found</h2></div>";
		}?>
	<div align="center">
		<br />
		<?php include("pagination_interface.php"); ?>
		<div align="right" style="width:90%; margin:0 auto">
			<a href="javascript:void(0)" style="color:#0066FF; text-decoration:underline" onClick="windowOpener(525,645,'Add New Location','add_venue.php')"><img src="<?php echo IMAGE_PATH; ?>add_venue.png" /></a>
		</div>
	</div>
	<br /><br /><br /><br />
	
	<div class="head">Help Guide</div>
	<div class="whiteRound">
		You can assign team members to single events or all events. Your team is typically the people who help create, manage and promote your event. Below you will find definitions that should help you determine what role and access to give to each team member
	</div>
	<br />
<br />

	<div class="head">Roles:</div>
	<div class="whiteRound">
		Executive: Has full access to your account. This role has the ability to add, edit and delete all functions on your account. Essentially, this person is equal to you.<br><br>
		Manager: Managers have viewing, editing and promotional capabilities on your account. They can edit events, ticket items, promotional campaigns and view reports. They can also add influencers to your account. They cannot delete any function.<br /><br />
		Influencers: Influencers only have the ability to send out promotional campaigns on your behalf. This includes sending out social media promotions, email blasts and text messages. Influencers are rewarded based on their performance. The Executive determines the awards and can track the Influencer's performance.

	</div>