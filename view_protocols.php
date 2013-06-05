<?php
	if($_GET['p'] == 'protocols' && isset($_GET['del'])){
		if(validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'protocols',$_GET['del']) =='true'){
		mysql_query("delete from `protocols` where id='". $_GET['del'] ."'");
		}
		else {
		echo "<script>window.location.href='clinic_manager.php?p=protocols';</script>";
		}
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="30%">Protocol Name</td>
											<td width="30%">Category</td>
											<td width="20%">Sub-category</td>									
											<td width="20%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								if(isset($_GET['search'])){
									$search=$_GET['search'];
									$sql = "select * from `protocols` where `protocol_title` like '%$search%' AND `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
									}else {		
									if(get_affiliate() && get_affiliate() != ""){	
											 $af_id	=get_affiliate();
									$sql = "select * from `protocols` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' || `clinic_id`='".$af_id."'  ORDER BY protocol_title ASC";
											 }else {						
									$sql = "select * from `protocols` where  `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' ORDER BY protocol_title ASC";
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
									<table cellpadding="2px" cellspacing="0" width="99%" align="center">
										<tr>
											<td width="30%" valign="top" class="event_name">
												<?php echo $row['protocol_title']; ?>
										  </td>
										  
										  <td width="30%" valign="top" class="event_name">
												<?php $ctd = $row['disease_category_id']; 
												echo getSingleColumn("cat_name","select * from `disease_category` where `id`='$ctd'");
												?>											
										  </td>
										  
										  <td width="20%" valign="top" class="event_name">
												<?php $cts =$row['disease_subcategory_id']; 
												echo getSingleColumn("sub_cat_name","select * from `disease_subcategory` where `id`='$cts'");
												?>											
										  </td>										  											
											
											<td width="20%" valign="top" align="right" class="sales">
											<?php $cl_id_test =$row['clinic_id'];
											if($cl_id_test == $_SESSION['LOGGEDIN_MEMBER_ID']){?>
											
											<a href="<?php echo ABSOLUTE_PATH; ?>create_protocol.php">New Protocol</a> | 
											<a href="create_protocol.php?id=<?php echo $row['id']; ?>">Edit</a>
											<a href="<?php echo ABSOLUTE_PATH; ?>view_protocol_report.php?id=<?php echo $row['id']; ?>">Print View</a> |											 
											<a onclick="return confirm('Are you sure you want delete this protocols?');" href="?p=protocols&del=<?php echo $row['id']; ?>">Delete</a>
											<?php } ?>
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_protocol.php">Create Protocol</a></strong>
								</div>
								
                               </div>