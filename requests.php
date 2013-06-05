	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>
	<script language="javascript">
function del_sched(vala){

if(vala >= 1){ 

$.post("set_scedule.php", {centerid:vala},function(data) { 

	 location.reload();   
    
    }
   )   }
}
</script>		
					
								<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="20%">Patient Name</td>
											<td width="20%">Request Date</td>
											<td width="20%">Preferred Time</td>
											<td width="25%">Reason</td>
											<td width="15%">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								include("page_form.php");

								$loginEmail	= getSingleColumn("email","select * from `users` where `id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");

								$qry2 = "select * from `request_appt` where clinic_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";

								$rest123 = mysql_query($qry2);
								$totl_records=mysql_num_rows($rest123);

								$lim_record=10;		// records per page

								$total_pages=ceil($totl_records/$lim_record);     // ceil rounds to ceil number(4.2 to 5)
								$page_num=0; 
								if(isset($_REQUEST['page']))
								{
									$page_num=$_REQUEST['page']; // from pagination_interface.php file..........
								}
								else
								{
									$page_num=1;
								}
								if($page_num==1)
								{
									$start_record=0;  // As we know In mysql database records index starts from 0 
								}
								else
								{
									$start_record= $page_num*$lim_record - $lim_record;
								}
								
								
								$sql = "select * from `request_appt` where clinic_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' LIMIT $start_record,$lim_record";

								$res = mysql_query($sql);
								$i=0;
								$bg = "ffffff";
								while($row = mysql_fetch_array($res)){
									
									$date	    = $row['date_requested'];
									$rid	    = $row['id'];
									$time		= $row['time_requested'];
									$reason		= $row['reason'];	
									$pid		= $row['patient_id'];								
									$name1		= getSingleColumn("firstname","select * from `patients` where `id`='$pid'");
									$name2		= getSingleColumn("lastname","select * from `patients` where `id`='$pid'");
									
									
									
								if($bg=='ffffff')
									$bg='f6f6f6';
								else
									$bg = "ffffff";
								
								?>
								<div class="ev_eventBox" style="background:#<?php echo $bg; ?>">
									<table cellpadding="0" cellspacing="0" width="99%" align="center">
										<tr>
											<td width="20%" valign="top" class="event_name">
												<a href="patient.php?id=<?php echo $pid;?>"><?php echo $name1." ".$name2; ?></a>
											</td>					 											

											<td width="20%" class="event_info" valign="top">
												<?php echo date("M d, Y",strtotime($date)); ?>
											</td>
											
										  <td width="20%" class="event_info" valign="top">
												<?php echo $time; ?>
										  </td>
										  
										  <td width="20%" class="event_info" valign="top">
												<?php echo $reason; ?>										  </td>
										  
										  <td width="20%" class="event_info" align="right" valign="top">
											   <?php
											$check=mysql_query("select * from  schedule_patient where patient_id='".$pid."' && clinic_id='$member_id'");
											$have=mysql_num_rows($check); ?>
											<input type="checkbox" name="schedule_confirm" value="<?php echo $row ['id']; ?>" onclick="del_sched(<?php echo $row ['id']; ?>);" />
					&nbsp;Accept Request<br />	<br />
											<a href="<?php if($have){echo "schedule_patients.php?id=".$pid;}else{echo "schedule_patients.php?pn=".$pid;}?>">Re-Schedule Appt</a>	<br />	
											<br />
											<!-- <a href="create_patient.php?id=<?php echo $pid; ?>">Edit Patient Info</a>	 -->
					
										  </td>	
										  
										  
										  									
										</tr>
										
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									
								</div>
								<div align="center">
								<br />
									<?php include("pagination_interface.php"); ?>
								</div>
								
