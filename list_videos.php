<?php
	if($_GET['p'] == 'videos' && isset($_GET['del'])){
		if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'learning_library',$_GET['del']) =='true'){
			mysql_query("delete from `learning_library` where id='". $_GET['del'] ."'");
		}else {
			echo "<script>window.location.href='clinic_manager.php?p=videos';</script>";
		}
		
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="80%">Video Title</td>
											<td width="20%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								
								$sql = "select * from `learning_library` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";

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
											<td width="80%" valign="top" class="event_name">
												<?php echo $row['title']; ?>
										  </td>
										  
											<td width="20%" valign="top" align="right" class="sales">
											<a href="clinic_manager.php?id=<?php echo $row['id']; ?>&p=create-video">Edit</a> | <a onclick="return confirm('Are you sure you want delete this patient?');" href="?p=videos&del=<?php echo $row['id']; ?>">Delete</a>
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>clinic_manager.php?p=create-video">Create Video</a></strong>
								</div>
								
                               </div>