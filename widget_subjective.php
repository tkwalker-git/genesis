 <div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Subjective</div> </td>
      </tr>
    </table>
	<br/>
	
	<div class="yellow_bar">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="topleft"><strong>Patient Health Concerns</strong></td>
			<td class="topleftright"><strong>Concern Date</strong></td>
		</tr>
 <?php
			$loginEmail	= getSingleColumn("email","select * from `users` where `id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
			$qry2 = "select * from `request_appt` where clinic_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
			$rest123 = mysql_query($qry2);
			$totl_records=mysql_num_rows($rest123);
			$sql = "select * from `request_appt` where patient_id='$id' && clinic_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' ORDER BY date_requested DESC";
			$res=mysql_query($sql);
			if($res)
			{
				while($row=mysql_fetch_array($res))
				{ ?>
					<tr>
					<td class="botleft"><?php echo $row['reason']; ?></td>
					<td class="botleftright"><?php echo  date("M d, Y",strtotime($row['date_requested'])); ?></td>
					
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
