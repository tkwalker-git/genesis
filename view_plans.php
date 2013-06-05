<?php
	if($_GET['p'] == 'plans' && isset($_GET['del'])){	
		if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'plan',$_GET['del']) =='true'){
		mysql_query("delete from `plan` where id='". $_GET['del'] ."'");
		mysql_query("delete from `plan_protocol` where `plan_id`='". $_GET['del'] ."'");
		mysql_query("delete from `plan_supplement` where `plan_id`='". $_GET['del'] ."'");
		mysql_query("delete from `plan_test` where `plan_id`='". $_GET['del'] ."'");
		}
		else {
		echo "<script>window.location.href='clinic_manager.php?p=plans';</script>";
		}
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="30%">Patient Name</td>
											<td width="35%">Plan Name</td>
											<td width="15%">Plan Date</td>																					
											<td width="20%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								if(isset($_GET['search'])){
									$search=$_GET['search'];
									$sql = "select * from `plan` where (`plan_name` like '%$search%' || `plan_detail` like '%$search%') AND `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
									}else {								
									$sql = "select * from `plan` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
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
											<td width="30%" valign="top" class="event_name">
												<?php $dfg = $row['patient_id']; ?> 
												<a href="patient.php?id=<?php echo $row['patient_id'];?>"
												>
												<?php echo getSingleColumn("firstname","select * from `patients` where `id`='$dfg'"); ?>
												<?php echo " "; ?>
												<?php echo getSingleColumn("lastname","select * from `patients` where `id`='$dfg'"); ?>
												</a>																							
										  </td>
										  
										  <td width="35%" valign="top" class="event_name">
												<a href="<?php echo ABSOLUTE_PATH; ?>view_plan_report.php?id=<?php echo $row['id']; ?>">
												<?php echo  $row['plan_name'];?></a> 											
										  </td>
										  
										  <td width="15%" valign="top" class="event_name">
												<?php echo $row['plan_date'];?> 										
										  </td>
										  
										  
										  
										  
										   
										   
										   
											
											
											<td width="20%" valign="top" align="center" class="sales">
											<a href="<?php echo ABSOLUTE_PATH; ?>create_plan.php?pn=<?php echo $row['patient_id']; ?>">New Plan</a> | 
											<a href="create_plan.php?id=<?php echo $row['id']; ?>">Edit Plan</a> <a href="<?php echo ABSOLUTE_PATH; ?>view_plan_report.php?id=<?php echo $row['id']; ?>">Print Plan</a> | <a onclick="return confirm('Are you sure you want delete this Plan?');" href="?p=plans&del=<?php echo $row['id']; ?>">Delete</a> 
											
											
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_plan.php">Create Plan</a></strong>
								</div>
								
                               </div>