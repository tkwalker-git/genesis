<script language="javascript">
function fn_submit()
{
document.status.submit();
}
</script>	

<script language="javascript">
function chng_com_status(vala){
if(vala != ""){ 
$.post("set_com_status.php", {centerid:vala},function(data) {   
location.reload();   
    }
   )   }
}
</script>	

			
<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
  <?php $stats_pat	=	 getSingleColumn("status","select * from `patients` where `id`='".$_GET['id']."'"); 
  if($stats_pat==3 || $stats_pat==4){?>
  
    <br />
<div>
	<table cellspacing="0" cellpadding="0" border="0" width="100%"> 
      <tr>
        <td width="23%" height="" align="left" valign="top" style="font-size:13px" ><div class="ew-heading">Visit Status</div> </td>
		<td width="77%" height="" align="left" valign="top" style="font-size:13px" >
		
		<form name="status" method="post" action="">
		<div style="padding-top:7px;">
		<input type="radio" name="release" value="3" <?php if($stats_pat==3){?> checked="checked" <?php } ?> onclick="fn_submit();" />&nbsp;Review Required&nbsp;&nbsp;
		<input type="radio" name="release" value="4" <?php if($stats_pat==4){?> checked="checked" <?php } ?> onclick="fn_submit();" />&nbsp;
		Release Plan&nbsp;&nbsp;
		<input type="hidden" name="final" value="1" />
	<input type="radio" name="release" value="2" <?php if($stats_pat==2){?> checked="checked" <?php } ?> onclick="fn_submit();" />&nbsp;	None 
		</div></form></td>
      </tr>
  </table>
</div>

<br /><br />
   <?php } ?>
   
   	<div style="margin-top:30px;">
		<table cellspacing="0" cellpadding="0" border="0" width="100%"> 
			<tr>
				<td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Add New Note</div></td>
			</tr>
		</table>
	
		<br />
		<div>
			<form name="comment" method="post" action="">
				<textarea name="comment" id="comment" class="bc_input" style="width:855px;height:250px;"></textarea>
				<div class="clr";></div>
				<div align="right";>
					<input style="padding:4px 10px;" type="submit" name="comm" value="submit" />
				</div>
			</form>
		</div>
	</div>
		<br /><br />
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%"> 
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Past Notes</div> </td>
      </tr>
	  </table>
	<br />
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="topleft" width="20%"><strong>Comment Date</strong></td>
			<td width="50%" class="topleftright"><strong>Comment </strong></td>
			<td width="30%" class="topleftright"><strong>&nbsp; </strong></td>
		<!-- 	<td class="topleftright"><strong>Notes</strong></td> -->
		</tr>
		<?php
			$sqlt="select * from patient_comments where patient_id=".$_GET['id']." && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."'";
			$dfre=mysql_query($sqlt);
			$no=1;
			while($get_co_id=mysql_fetch_array($dfre)){
			
					$gstst = $get_co_id['status']; ?>
					
					
					<tr>
					<td class="botleft"><?php echo date("M d, Y",strtotime($get_co_id['comment_date'])); ?></td>
					<td class="botleftright"><?php echo $get_co_id['comment']; ?></td>
					<td class="botleftright">
					<input type="radio" name="pri<?php echo $no; ?>" <?php if($gstst==1){?> checked="checked" <?php } ?> value="<?php echo $patient_id."_3";  ?>" onClick="chng_com_status('<?php echo $get_co_id['id']."_1";  ?>');">&nbsp;Keep Private&nbsp;&nbsp;&nbsp;
					<input type="radio" name="pri<?php echo $no; ?>" <?php if($gstst==2){?> checked="checked" <?php } ?> value="<?php echo $patient_id."_4"; ?>" onClick="chng_com_status('<?php echo $get_co_id['id']."_2";  ?>');">&nbsp; Available to Patient&nbsp;&nbsp;&nbsp;
	
				</td>
					</tr>					
					<!-- <tr><td class="botleftright"><?php echo $get_co_id['comment']; ?></td></tr> -->
					<?php $no++;} 	?>
	</table>



<br /><br />
      
     
	
	


<table cellspacing="0" cellpadding="0" border="0" width="100%">
   
    
  <!--   <table cellspacing="0" cellpadding="0" border="0" width="100%"> -->
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Patient Recommended Tests</div> </td>
	
      </tr>
 </table>
      
<br />
   <!--  </table><br /> -->
   <!-- <br /> -->
	
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="38%" class="topleft"><strong>Tests Name</strong></td>
			<td width="62%" class="topleftright"><strong>Comments</strong></td>
		<!-- 	<td class="topleftright"><strong>Notes</strong></td> -->
		</tr>
		<?php
			$sqlt="select * from plan where patient_id=".$_GET['id']." && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."'";
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
					while($asdf=mysql_fetch_array($tcd)){?>
					<tr>
					<td class="botleft"><?php echo $asdf['test_name']; ?></td>
					<td class="botleftright"><?php echo $asdf['description']; ?></td>
					</tr>
					<!-- <tr><td class="botleftright"><?php echo $asdf['test_name']; ?></td></tr> -->
					<?php }
				}
			}
			
			
			}
			
			
			
			
			
		?>
	</table>

	</div>	

  <div class="blockerBottom"></div>
</div>


<!--end center_contents-->
<div class="clr"></div>
 <!--end blocker-->
