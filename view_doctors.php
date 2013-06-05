<?php
	if($_GET['p'] == 'doctors' && isset($_GET['del'])){
		if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'users',$_GET['del']) =='true'){
		mysql_query("delete from `users` where id='". $_GET['del'] ."'");
		}
		else {
		echo "<script>window.location.href='clinic_manager.php?p=doctors';</script>";
		}
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="21%">Doctor Name</td>
											<td width="20%">Email</td>
											<td width="24%">City</td>
											<td width="21%">State</td>										
											<td width="14%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								
								$sql = "select * from `users` where  `clinicid`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' && usertype='2'";

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
											<td width="20%" valign="top" class="event_name">
												<?php echo $row['firstname'].' '.$row['lastname']; ?>
										  </td>
										  
										  <td width="20%" valign="top" class="event_name">
												<?php echo $row['email']; ?>											
										  </td>
										  
										  <td width="23%" valign="top" class="event_name">
												<?php echo $row['city']; ?>											
										  </td>
										  
										   <td width="20%" valign="top" class="event_name">
												<?php $abc= $row['state']; 
												echo getSingleColumn("state","select * from `usstates` where `id`='$abc'");
												?>											
										  </td>
											
											
											<td width="14%" valign="top" align="right" class="sales">
											<a href="create_doctor.php?id=<?php echo $row['id']; ?>">Edit</a> | <a onclick="return confirm('Are you sure you want delete this doctor?');" href="?p=doctors&del=<?php echo $row['id']; ?>">Delete</a>
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_doctor.php">Create Doctor</a></strong>
								</div>
								
                               </div>