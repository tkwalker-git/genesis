<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Plans</div> </td>
      </tr>
    </table>
	<br/>
	
	<div class="yellow_bar">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="topleft"><strong>Plan Name</strong></td>
			<td class="topleftright"><strong>View Plan</strong></td>
		</tr>
		<?php
			$sql="select * from plan where patient_id=".$_GET['id']." && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."' ORDER BY plan_date DESC";
			$res=mysql_query($sql);
			if($res)
			{
				while($row=mysql_fetch_array($res))
				{ ?>
					<tr>
					<td class="botleft"><?php echo $row['plan_name']; ?></td>
					<td class="botleftright"><a href="view_plan_report.php?id='<?php echo  $row['id']; ?>'" target="_blank">View Plan</a></td>
					</tr>
				<?php }
			}
		?>
	</table>
	</div>
  </div>
  <div class="blockerBottom"></div>
</div>


<!--end center_contents-->
<div class="clr"></div>
 <!--end blocker-->