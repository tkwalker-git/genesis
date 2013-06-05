<?php 

require_once('../admin/database.php');
require_once('../site_functions.php');
$suc = false;

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

if ( $_GET['review_id'] > 0 && $_GET['status'] != '' ) {
	if ( mysql_query("INSERT INTO review_helpfull (review_id,status,userid) VALUES (". $_GET['review_id'] .",". $_GET['status'] .",'". $member_id ."')") ) {
		$suc = true;
		$msg = 'Review Status is updated successfully.';
	} else {
		$suc = false;
		$msg = 'There was some error. Please try later.';
	}	
}	

?>

<table width="400" cellpadding="0" cellspacing="0" align="center" >
<tr>
<td colspan="3" align="right" height="30">
	<a href="javascript:void(0)" style="color:#FFFFFF; font-weight:bold" onClick="hideOverlayer(1)">Close</a></td>
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

	<td align="right" width="45"><img src="<?php echo ABSOLUTE_PATH;?>images/icon_error_leftBdr.png"></td>
	<td style="background-image:url(<?php echo ABSOLUTE_PATH;?>images/icon_confirmation_centerBg.png); background-repeat:repeat-x; color:#004182; font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold" height="59" width="339" align="center"><?php echo $msg;?></td>
	<td align="left" width="16"><img src="<?php echo ABSOLUTE_PATH;?>images/icon_confirmation_rhtBdr.png"></td>

<?php } ?>
</tr>
</table>
