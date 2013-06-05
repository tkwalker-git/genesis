<?php 

require_once('admin/database.php');
require_once('site_functions.php');

$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
$type		= $_GET['type'];
$event_id	= $_GET['event_id'];



if($type == 'rsvp' && $event_id){

	$result=mysql_query("select * from `events_rsvp` where `event_id`='$event_id'");
	
	$userid	= getSingleColumn("userid","select * from `events` where `id`='$event_id'");

	if($userid == $_SESSION['LOGGEDIN_MEMBER_ID']){

		if(mysql_num_rows($result)){
			$userid 	= getSingleColumn('userid',"select * from events where id=" . $event_id);
			if($userid==$_SESSION['LOGGEDIN_MEMBER_ID'] || $_SESSION['admin_user']){
				?>
				<div style="width:54%;font-family:Arial, Helvetica, sans-serif; font-size:13px; margin:5px auto"><strong><a href="load_xls.php?type=rsvp&event_id=<?php echo $event_id;?>">Export</a></strong></div>
				<table cellpadding="5" cellspacing="0" width="54%" align="center" border="1" style="font-family:Arial, Helvetica, sans-serif; font-size:13px;">
					<tr>
						<td width="7%" align="center"><strong>No</strong></td>
						<td width="29%"><strong>Name</strong></td>
						<td width="33%"><strong>Email</strong></td>
						<td width="31%"><strong>How did hear about us</strong></td>
					</tr>
				<?php
				$i=0;
				$data = '';
				$line = '';
				while($row=mysql_fetch_array($result)){
					$i++;
					?>
					<tr>
						<td align="center"><?php echo $i; ?></td>
						<td><?php echo $row['name']; ?></td>
						<td><?php echo $row['email']; ?></td>
						<td><?php echo $row['how_did_hear']; ?></td>
					</tr>
				<?php
				}
			?>
</table>
			<?php
			}
		}
		else{
			echo "<b>No RSVP found for this event</b>";
		}
	}
	else
		echo "<strong>Error:</strong> Try again later";
}
?>