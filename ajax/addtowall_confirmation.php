<?php 

require_once('../admin/database.php');
require_once('../site_functions.php');
$suc = false;

if ( $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ) {

	if ( isset( $_GET['event_id'] ) ) {
		
		$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
		
		$already = attribValue('event_wall', 'id', "where event_id='". $_GET['event_id'] ."' and userid='$member_id'");
		
		if ( $already > 0 ) {
			$suc = true;
			$msg = 'Event is already on your calendar.';
		} else {
			$sq = "insert into event_wall VALUES (NULL,'". $_GET['event_id'] ."','". $member_id ."','". date("Y-m-d") ."','-1',1,1)";
			if ( mysql_query($sq) ) {
				$suc = true;
				$msg = 'Event is added to your calendar successfully.';
			} else {
				$suc = false;
				$msg = 'Error: ' . $sq;
			}	
		}		
	}
} else {
	$suc = false;
	$msg = '<font color="red">Please <a href="'.ABSOLUTE_PATH.'login.php">login</a> OR <a href="'.ABSOLUTE_PATH.'signup.php">Signup</a> before adding an Event to calendar.</font>';
	}
?>
<table width="400" cellpadding="0" cellspacing="0" align="center" >
	<tr>
		<td colspan="3" align="right" height="30"><a href="javascript:void(0)" style="color:#FFFFFF; font-weight:bold" onClick="hideOverlayer(1)">Close</a></td>
	</tr>
	<tr>
	<?php
	if ( $suc) {
	?>
		<td align="right" width="45"><img src="<?php echo ABSOLUTE_PATH;?>images/icon_confirmation_leftBdr.png"></td>
		<td style="background-image:url(<?php echo ABSOLUTE_PATH;?>images/icon_confirmation_centerBg.png); background-repeat:repeat-x; color:#004182; font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold" height="59" width="339" align="center"><?php echo $msg;?></td>
		<td align="left" width="16"><img src="<?php echo ABSOLUTE_PATH;?>images/icon_confirmation_rhtBdr.png"></td>
	</tr>
</table>
	<?php
	} else {
	?>
		<td align="right" width="45">
			<img src="<?php echo ABSOLUTE_PATH;?>images/icon_error_leftBdr.png">
		</td>
		<td style="background-image:url(<?php echo ABSOLUTE_PATH;?>images/icon_confirmation_centerBg.png); background-repeat:repeat-x; color:#004182; font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold" height="59" width="339" align="center">
			<?php echo $msg;?>
		</td>
		<td align="left" width="16">
			<img src="<?php echo ABSOLUTE_PATH;?>images/icon_confirmation_rhtBdr.png">
		</td>
	<?php } ?>
	</tr>
</table>