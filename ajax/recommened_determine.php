<?php 

require_once('../admin/database.php');
require_once('../site_functions.php');

?>

<table width="601" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td colspan="3" width="601" height="64" valign="middle"  style="background-image:url(<?php echo ABSOLUTE_PATH;?>ajax/images/lb_top.png); background-repeat:no-repeat; " >
		<table width="525" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:25px">
      	<tr>
			<td style=" font-size:14px!important; color:#FCFBF9; padding-left:10px!important">How did we determine these events?</td>
	        <td width="16" nowrap="nowrap"  align="right" >
				<a href="javascript:void(0)" onClick="hideOverlayer(0)" style="color:#D7FD02; font-size:12px;">
					<img src="<?php echo ABSOLUTE_PATH;?>ajax/images/close.gif" width="16" height="16" border="0" align="left" /></a>
				</td>
    	  </tr>
   	  </table>
	</td>
  </tr>
  <tr>
    <td width="32" style="background-image:url(<?php echo ABSOLUTE_PATH;?>ajax/images/lb_left.png); background-repeat:repeat-y">&nbsp;</td>
    <td width="538" align="center" style="background-color:#FFFFFF" >

			<table width="100%" border="0" cellspacing="2" cellpadding="2" bgcolor="#FFFFFF">
			<tr><td align="left" height="250" valign="top" style="padding:30px">
				These events are the results of a combination of your personal preference profile.  View <a style="color:#52BDE9; text-decoration:underline" href="<?php echo ABSOLUTE_PATH;?>settings.php?p=event-preferences">Edit Preferences</a> to modify your settings

			</td></tr>
			</table>
			
	</td>
    <td width="31" style="background-image:url(<?php echo  ABSOLUTE_PATH;?>ajax/images/lb_right.png); background-repeat:repeat-y">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><img src="<?php echo  ABSOLUTE_PATH;?>ajax/images/lb_bottom.png" width="601" height="56" /></td>
  </tr>
</table>