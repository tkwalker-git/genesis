<?php
	if($_GET['p'] == 'supplement' && isset($_GET['del'])){
		if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'supplement',$_GET['del']) =='true'){
		mysql_query("delete from `supplement` where id='". $_GET['del'] ."'");
		}else {
				echo "<script>window.location.href='clinic_manager.php?p=supplement';</script>";
		}
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="30%">Supplement Name</td>
											
											<td width="30%">Supplier</td>
											
											<td width="15%">Cost</td>
											
											<td width="25%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								if(isset($_GET['search'])){
									$search=$_GET['search'];
									$sql = "select * from `supplement` where (`supplement_name` like '%$search%' || `description` like '%$search%') AND `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
									}else {		
										if(get_affiliate() && get_affiliate() != ""){	
											 $af_id	=get_affiliate();	
											 $sql = "select * from `supplement` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' || `clinic_id`='".$af_id."'  ORDER BY supplement_name ASC";
										}else {						
											$sql = "select * from `supplement` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' ORDER BY supplement_name ASC";
											}
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
												<?php echo $row['supplement_name']; ?>
										  </td>
										  
										  <td width="30%" valign="top" class="event_name">
												<?php echo $row['suppliers']; ?>
										  </td>
											
											<td width="15%" valign="top" class="event_name">
												
												<?php echo $row['cost']; ?>
										  </td>
											
											<td width="25%" valign="top" align="center" class="sales">
											<?php $cl_id_test =$row['clinic_id'];
											if($cl_id_test == $_SESSION['LOGGEDIN_MEMBER_ID']){?>
											
											<a href="<?php echo ABSOLUTE_PATH; ?>create_supplement.php">Add New</a> | 
											<a href="create_supplement.php?id=<?php echo $row['id']; ?>">Edit</a> | <a onclick="return confirm('Are you sure you want delete this supplement?');" href="?p=supplement&del=<?php echo $row['id']; ?>">Delete</a>
											<?PHP } ?>
											<td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_supplement.php">Create Supplement</a></strong>
								</div>
								
                               </div>