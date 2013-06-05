<?php
	if($_GET['p'] == 'patient' && isset($_GET['del'])){
		mysql_query("delete from `users` where id='". $_GET['del'] ."'");
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="left">
										<tr>
											<td width="1%"></td>
											<td width="15%">Affiliate Name</td>
											<td width="10%">Gender</td>
											<td width="22%">Email</td>
											<td width="18%">Phone</td>
											<td width="18%">Status</td>											
											<td width="16%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								
								$sql = "select * from `users` where  `clinicid`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";

								$res = mysql_query($sql);
								$i=0;
								$bg = "ffffff";
								while($row = mysql_fetch_array($res)){
									if($bg=='ffffff')
										$bg='f6f6f6';
									else
										$bg = "ffffff";
								?>
								<div class="ev_eventBox" style="background:#<?php echo $bg; ?>">
									<table cellpadding="0" cellspacing="0" width="99%" align="center">
										<tr>
											<td width="15%" valign="top" class="event_name">
												<a href="patient.php?id=<?php echo $row['id'];?>"
												><?php echo $row['firstname']; ?>												
												<?php echo $row['lastname']; ?>
											</a>
										  </td>
										  
										  <td width="10%" valign="top" align="left" class="event_name">
												<?php echo $row['sex']; ?>											
										  </td>
										  
										  <td width="22%" valign="top" class="event_name">
												<?php echo $row['email']; ?>											
										  </td>
										  
										   <td width="18%" valign="top" class="event_name">
												<?php echo $row['phone']; ?>											
										  </td>
										  
										  <td width="18%" valign="top" class="event_name">
												<?php echo $row['status']; ?>											
										  </td>
											
											
											<td width="18%" valign="top" align="right" class="sales">
											<a href="patient.php?id=<?php echo $row['id']; ?>">View</a> | <a href="create_patient.php?id=<?php echo $row['id']; ?>">Edit</a> | <a onclick="return confirm('Are you sure you want delete this patient?');" href="?p=patient&del=<?php echo $row['id']; ?>">Delete</a>
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<!--
<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_patient.php">Create Patient</a></strong>
								</div>
-->
								
                               </div>