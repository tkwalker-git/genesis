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
			<td class="topleft"><strong>Plan Description</strong></td>
			<td class="topleftright"><strong>Plan Date</strong></td>
		</tr>
		<?php  	
		
				$sql = "select * from `plan` where `patient_id`='".$row['id']."' && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."' order by id DESC";
				$res = mysql_query($sql);							
		
			if($res)
			{
				while($row=mysql_fetch_array($res))
				{ ?>
					<tr>
					<td class="botleft"><a target="_blank" href="view_plan_report.php?id=<?php echo $row['id']; ?>"style="color:#0066FF;"><?php echo  $row['plan_name'];?></a></td>
					<td class="botleft"><a target="_blank" href="view_plan_report.php?id=<?php echo $row['id']; ?>"style="color:#0066FF;"><?php echo $row['plan_detail']; ?></td>
					<td class="botleftright"><a target="_blank" href="view_plan_report.php?id=<?php echo $row['id']; ?>"style="color:#0066FF;"><?php echo date("M d, Y",strtotime($row['plan_date'])); ?></td>
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