			
<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Patient Recommended Tests</div> </td>
      </tr>
    </table><br />
	
	<div class="yellow_bar">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="topleftright"><strong>Tests Name</strong></td>
			
		</tr>
		<?php
		$reqw = mysql_query("select * from `patients` where id='".$_SESSION['LOGGEDIN_MEMBER_ID']."'");
        if(mysql_num_rows($reqw)){
            while($roer=mysql_fetch_array($reqw)){
            $member_id = $roer['clinicid'];              
        
            }
    
        }
		 	
			
			 $sqlt="select * from plan where patient_id=".$_SESSION['LOGGEDIN_MEMBER_ID']." && clinic_id='".$member_id."'";
			$dfre=mysql_query($sqlt);
			while($get_test_id=mysql_fetch_array($dfre)){
			$pl_id=$get_test_id['id'];
			
			 $sqlt="select * from `plan_test` where `patient_id`='".$_GET['id']."' && `plan_id`='".$pl_id."'";
			$res=mysql_query($sqlt);
			$res12=mysql_num_rows($res);
			if($res12)
			{
				while($row=mysql_fetch_array($res))
				{
					 $tstid=$row['test_id'];
					$tcd=mysql_query("select * from tests where id='$tstid'");
					while($asdf=mysql_fetch_array($tcd)){ ?>
					<tr><td class="botleftright"> <?php echo $asdf['test_name']; ?></td></tr>
					<?php }
				}
			}
			
			
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