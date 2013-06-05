<?php
	if($_GET['p'] == 'supplement' && isset($_GET['del'])){
		mysql_query("delete from `supplement` where id='". $_GET['del'] ."'");
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="35%">Form ID</td>
											
											<td width="45%">Form Name</td>
											
											<td width="20%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								
								$sql = "select * from `supplement` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";

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
											<td width="35%" valign="top" class="event_name">
												<!-- <?php echo $row['supplement_name']; ?> -->
										  </td>
										  <td width="50%" valign="top" class="event_name">
												<!-- <?php echo $row['suppliers']; ?> -->
										  </td>
											
											
											<td width="15%" valign="top" align="center" class="sales">
											<!-- <a href="create_supplement.php?id=<?php echo $row['id']; ?>">Edit</a> | <a onclick="return confirm('Are you sure you want delete this supplement?');" href="?p=supplement&del=<?php echo $row['id']; ?>">Delete</a> -->
											<td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<!-- <strong><a href="<?php echo ABSOLUTE_PATH; ?>create_supplement.php">Create Supplement</a></strong> -->
								</div>
								
                               </div>