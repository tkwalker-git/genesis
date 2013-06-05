<?php
if($_GET['p'] == 'patient' && isset($_GET['del'])){
	if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'patients',$_GET['del']) =='true'){
		mysql_query("delete from `patients` where id='". $_GET['del'] ."'");
	}
	else{
		echo "<script>window.location.href='clinic_manager.php';</script>";
	}
}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="left">
										<tr>
											<td width="1%"></td>
											<td width="42%">Patient Profile</td>
											<td width="39%">Current Plan</td>								
											<td width="18%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
			if(isset($_GET['search'])){
			$search=$_GET['search'];
			 $sql = "select * from `patients` where (`username` like '%$search%' || `firstname` like '%$search%' || `lastname` like '%$search%' ) AND `clinicid`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
			}else {
			$sql = "select * from `patients` where  `clinicid`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
			}

$res = mysql_query($sql);
$i=0;
$bg = "ffffff";
while($row = mysql_fetch_array($res)){
	if($bg=='ffffff')
		$bg='f6f6f6';
	else
		$bg = "ffffff";
?>
								<div class="ev_eventBox" style="background:#<?php if($row['id']==$_GET['id']){echo "c9e8ca";}else {echo $bg; } ?>">
									<table cellpadding="0" cellspacing="0" width="99%" align="center">
										<tr>
											<td width="1%" valign="top" class="event_name">&nbsp;</td>

										  <td width="42%" valign="top" align="left" class="event_name">
												<table width="100%" border="0" cellspacing="7" cellpadding="0">
  <tr>
    <td width="29%">NAME :</td>
    <td width="71%"><a href="patient.php?id=<?php echo $row['id'];?>"><?php echo $row['firstname']; ?> <?php echo " "; ?> <?php echo $row['lastname']; ?></a>	</td>
  </tr>
  <tr>
    <td>AGE :</td>
    <td><span> <?php  $birthDate = explode("/", $row['dob']);
		         //get age from date or birthdate
		        echo  $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0])); ?></span></td>
  </tr>
  <tr>
    <td>GENDER</td>
    <td><span><?php echo $row['sex']; ?></span></td>
  </tr>
  <tr>
    <td>PHONE :</td>
    <td><span><?php echo $row['phone']; ?></span></td>
  </tr>
</table>
												</td>

										  <td width="39%" valign="top" class="event_name">
	 <?php  $pla_id = getSingleColumn("id","select * from `plan` where `patient_id`='".$row['id']."' && clinic_id='".$member_id."' order by id desc limit 1");
										  
			$prot_id = getSingleColumn("protocol_id","select * from `plan_protocol` where `patient_id`='".$row['id']."' && plan_id='$pla_id' limit 1 ");
			
									 $pln_name = getSingleColumn("plan_name","select * from `plan` where `patient_id`='".$row['id']."' && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."' order by id DESC limit 1");
							 ?>
												<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="2%">&nbsp;</td><br />
    <td width="98%"><?php  if($pla_id){ ?><a href="view_plan_report.php?id=<?php echo $pla_id; ?>"><?php echo  $pln_name; ?></a><?php  } ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
										  
										  </td>

										   <td width="18%" valign="top" class="event_name">
										   <?php
	$check=mysql_query("select * from  schedule_patient where patient_id='".$row['id']."' && clinic_id='$member_id'");
	$have=mysql_num_rows($check); ?>
							<a href="<?php if($have){echo "schedule_patients.php?id=".$row['id'];}else{echo "schedule_patients.php?pn=".$row['id'];}?>">Schedule Appt</a>	<br />	<br />
							<a href="create_patient.php?id=<?php echo $row['id']; ?>">Edit Patient Info</a>	<br />	<br />
							
							<?php  if($pla_id){ ?><a href="view_plan_report.php?id=<?php echo $pla_id; ?>">Print Plan</a>	<br />	<br /><?php  } ?>
							
							<?php  if($prot_id){ ?><a href="view_protocol_report.php?id=<?php echo $prot_id; ?>">Print Protocol</a>	<?php  } ?>						  
												</td>

									</tr>
									<tr style="padding-top:5px;">
									<td>&nbsp;</td> 
								<?php 	$dat=date("Y-m-d");
								 $cft="select * from `schedule_dates` where `patient_id`='".$row['id']."' && `clinic_id`='$member_id' && `cons_date` < '$dat' order by cons_date ASC limit 1";
								$lst_dt = mysql_query($cft);
								$nhex = mysql_num_rows($lst_dt);
									 
								 ?>
								 <?php 	$dat=date("Y-m-d");
				 $vgw = "select * from `schedule_dates` where `patient_id`='".$row['id']."' && `clinic_id`='$member_id'&& `cons_date` >= '$dat' order by cons_date ASC limit 1";
									$nxt_dt = mysql_query($vgw);
									 $rect = mysql_num_rows($nxt_dt);
									 
								 ?>
								 
									<td><br /><span><strong>LAST VISIT :</strong></span>&nbsp;&nbsp;<?php if($nhex >= 1){
									while($s_dt=mysql_fetch_array($lst_dt)){
									   echo  $last_date = $s_dt['cons_date'];
									 
									 }}else {echo "No Prior Visits";}?></td>
									<td><br /><span><strong>NEXT VISIT :</strong></span>&nbsp;&nbsp;<?php if($rect >= 1){
									 while($sat_dta=mysql_fetch_array($nxt_dt)){
									  echo  $nexta_date = $sat_dta['cons_date'];
									 
									 }
									 }else { echo "No Visits Scheduled";} ?></td>
									 
									 
									<td colspan="2">&nbsp;</td>
									</tr>

								
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_patient.php">Create Patient</a></strong>
								</div>

                               </div>