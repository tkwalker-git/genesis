<?php
	if($_GET['p'] == 'test' && isset($_GET['del'])){
	if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'tests',$_GET['del']) =='true'){
		mysql_query("delete from `tests` where id='". $_GET['del'] ."'");
		}else {
		echo "<script>window.location.href='clinic_manager.php?p=test';</script>";
		}
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="20%">Test Name</td>
											<td width="60%">Test Summary</td>
											
											<td width="20%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								if(isset($_GET['search'])){
									$search=$_GET['search'];
									$sql = "select * from `tests` where (`test_name` like '%$search%' || `description` like '%$search%') AND `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
									}else {		
										if(get_affiliate() && get_affiliate() != ""){	
											 $af_id	=get_affiliate();				  							
											 $sql = "select * from `tests` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' || `clinic_id`='".$af_id."'";
										}else {
											$sql = "select * from `tests` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
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
									<table cellpadding="0" cellspacing="10%" width="99%" align="center">
										<tr>
											<td width="20%" valign="top" class="event_name">
												<?php echo $row['test_name']; ?>
										  </td>
										  <td width="60%" valign="top" class="event_name">
												<?php
												if(strlen($row['description']) > 250){
													echo substr($row['description'],0,250). '...';
												}
												else{
													echo DBout($row['description']);
												}
												?>
										  </td>
											
											
											<td width="20%" valign="top" align="center" class="sales">
											<?php $cl_id_test =$row['clinic_id'];
											if($cl_id_test == $_SESSION['LOGGEDIN_MEMBER_ID']){
											 ?>
											
											<a href="<?php echo ABSOLUTE_PATH; ?>create_test.php">Add New</a> | 
											<a href="create_test.php?id=<?php echo $row['id']; ?>">Edit</a> | <a onclick="return confirm('Are you sure you want delete this test?');" href="?p=test&del=<?php echo $row['id']; ?>">Delete</a>
											<?php } ?>
											<td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_test.php">Create Test</a></strong>
								</div>
								
                               </div>