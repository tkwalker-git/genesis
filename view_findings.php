<?php
	if($_GET['p'] == 'findings' && isset($_GET['del'])){
	
		mysql_query("delete from `findings_category` where id='". $_GET['del'] ."'");
	
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="1%"> </td>
											<td width="31%">Findings Name</td>
											<td width="24%">Category</td>
											<td width="25%">Sub-category</td>
											<!-- <td width="23%">Supplier</td> -->										
											<td width="20%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								if(isset($_GET['search'])){
									$search=$_GET['search'];
									$sql = "select * from `findings_category` where `finding_name` like '%$search%' || `finding_description` like '%$search%'";
									}else {								
									$sql = "select * from `findings_category` ORDER BY finding_name ASC";
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
											<td width="31%" valign="top" class="event_name">
												<?php echo $row['finding_name']; ?>
										  </td>
										  
										  <td width="24%" valign="top" class="event_name">
												<?php $ctd = $row['disease_category_id']; 
												echo getSingleColumn("cat_name","select * from `disease_category` where `id`='$ctd'");
												?>											
										  </td>
										  
										  <td width="25%" valign="top" class="event_name">
												<?php $cts =$row['disease_subcategory_id']; 
												echo getSingleColumn("sub_cat_name","select * from `disease_subcategory` where `id`='$cts'");
												?>											
										  </td>										  											
											
											<td width="20%" valign="top" align="center" class="sales">
											<!-- <a href="<?php echo ABSOLUTE_PATH; ?>view_protocol_report.php?id=<?php echo $row['id']; ?>">Print Protocol</a> | -->
											<a href="<?php echo ABSOLUTE_PATH; ?>create_finding.php">Add New</a> | 
											<a href="create_finding.php?id=<?php echo $row['id']; ?>">Edit</a> | 
											<a onclick="return confirm('Are you sure you want delete this finding?');" href="?p=findings&del=<?php echo $row['id']; ?>">Delete</a>
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_finding.php">Create Findings</a></strong>
								</div>
								
                               </div>