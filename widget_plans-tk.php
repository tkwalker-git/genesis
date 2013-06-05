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
		<?php  	$pla_id = getSingleColumn("id","select * from `plan` where `patient_id`='".$row['id']."' && clinic_id='".$member_id."' order by id desc");
		
				$pln_name = getSingleColumn("plan_name","select * from `plan` where `patient_id`='".$row['id']."' && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."' order by id DESC");
		
				$plan_details = getSingleColumn("plan_detail","select * from `plan` where `patient_id`='".$row['id']."' && clinic_id='".$member_id."' order by id desc");
				
				$plan_date = getSingleColumn("plan_date","select * from `plan` where `patient_id`='".$row['id']."' && clinic_id='".$member_id."' order by id desc");										  
			
				
		?>
					<tr>
					<td class="botleft"><a target="_blank" href="view_plan_report.php?id=<?php echo $pla_id; ?>"><?php echo  $pln_name; ?></a></td>
					<td class="botleft"><?php echo $plan_details; ?></td>
					<td class="botleftright"><?php echo $plan_date; ?></td>
					</tr>

	</table>
	</div>
  </div>
  <div class="blockerBottom"></div>
</div>


<!--end center_contents-->
<div class="clr"></div>
 <!--end blocker-->