<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">My Assessments and Reports</div> </td>
      </tr>
    </table>
	<br/>
	
	<div class="yellow_bar">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>		
		<td width="25%" class="topleft"><strong>Assessment Name</strong></td>
		<td width="20%" class="topleft"><strong>Start Date</strong></td>
		<td width="25%" class="topleftright"><strong>View Report</strong></td>
		<!-- <td width="30%" class="topleftright"><strong>View My Assessment</strong></td> -->
		</tr>
		<?php
		$sql="SELECT * FROM `mnoviforms_response` AS t1 INNER JOIN `mnoviforms` AS t2 ON t1.`mnoviforms_id` = t2.ID WHERE `response_id` IS NOT NULL AND `patient_id`=".$_GET['id'];
		$res = mysql_query($sql);
		if($res)
		{
			if(mysql_num_rows($res)>0)
			while($row = mysql_fetch_array($res))
			{
					
		?>
			<tr>
			<td class="botleft"><a href="survey_view.php?id=<?php echo $row['mnoviforms_id']; ?>&frmID=<?php echo $row['mnoviforms_id'];?>"style="color:#0066FF;"><?php echo  $row['FormName']; ?></td>
			<td class="botleft"><?php echo date("M d, Y \-\-\-\t\ g:ia",strtotime($row['date_started'])); ?></td>
			<!-- <td class="botleft"><?php echo $row['date_completed']; ?></td>	 -->		
			<td class="botleftright">&nbsp;<a href="survey_view.php?id=<?php echo $row['mnoviforms_id']; ?>&frmID=<?php echo $row['mnoviforms_id'];?>"style="color:#0066FF;">View Report</a></td>
			<!-- <td class="botleftright"><a href="survey_report.php?resid=<?php echo $row['response_id']; ?>&frmID=<?php echo $row['mnoviforms_id'];?>">Report</a></td> -->
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