<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Patient Health Reports</div> </td>
      </tr>
    </table>
	<br/>
	
	<?php 
                       
                       $clinic_id	= getSingleColumn('clinicid',"select * from `patients` where `id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");

                       if( $clinic_id == 21 ){	?>
	
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
		<td>Name</td><td>Date</td><td>Report Link</td>
		</tr>
		<?php
		$sql="SELECT * FROM `mnoviforms_response` AS t1 INNER JOIN `mnoviforms` AS t2 ON t1.`mnoviforms  ID` = t2.ID WHERE `Response id` IS NOT NULL AND `Patient Id`=".$_GET['id'];
		$res = mysql_query($sql);
		if($res)
		{
			if(mysql_num_rows($res)>0)
			while($row = mysql_fetch_array($res))
			{
				echo '<tr><td>'.$row['FormName'].'</td><td>'.$row['Completed on'].'</td><td><a href="survey_report-r1.php?resid='.$row['Response id'].'">Report</a></td></tr>';
				echo '<tr><td>'.$row['FormName'].'</td><td>'.$row['Completed on'].'</td><td><a href="survey_report-r2.php?resid='.$row['Response id'].'">Report</a></td></tr>';
			}
		}
		?>
	</table>
	
<?php  }else {?>

		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
		<td>Name</td><td>Date</td><td>Report Link</td>
		</tr>
		<?php
		$sql="SELECT * FROM `mnoviforms_response` AS t1 INNER JOIN `mnoviforms` AS t2 ON t1.`mnoviforms  ID` = t2.ID WHERE `Response id` IS NOT NULL AND `Patient Id`=".$_GET['id'];
		$res = mysql_query($sql);
		if($res)
		{
			if(mysql_num_rows($res)>0)
			while($row = mysql_fetch_array($res))
			{
				echo '<tr><td>'.$row['FormName'].'</td><td>'.$row['Completed on'].'</td><td><a href="survey_report.php?resid='.$row['Response id'].'">Report</a></td></tr>';
			}
		}
		?>
	</table>
	
	<?php } ?> 
	
	
	
	
  </div>
  <div class="blockerBottom"></div>
</div>


<!--end center_contents-->
<div class="clr"></div>
 <!--end blocker-->