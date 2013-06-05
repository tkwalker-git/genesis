<?php
	if($_GET['p'] == 'subcategories' && isset($_GET['del'])){
		
		mysql_query("delete from `disease_subcategory` where id='". $_GET['del'] ."'");
		
	}
?>
                            	<div class="yellow_bar">
									<table cellpadding="4px"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="5%" align="center"></td>
											<td width="40%">Sub-Category</td>
											<td width="35%">Category</td>											
											<!-- <td width="30%">Description</td>	 -->								
											<td width="20%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								require_once('admin/database.php');
								require_once('site_functions.php');
								
								
								if(isset($_GET['search'])){
									$search=$_GET['search'];
									$sql = "select * from `disease_subcategory` where `sub_cat_name` like '%$search%'";
									}else {								
									$sql = "select * from `disease_subcategory` ORDER BY sub_cat_name ASC";
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
											<td width="5%" valign="top" align="center" class="event_name">
												<!-- <?php echo $row['id']; ?> -->
										  </td>
										  
										   <td width="40%" valign="top" class="event_name">
												<?php echo $row['sub_cat_name']; ?>										
										  </td>
										  
										   <td width="35%" valign="top" class="event_name">
												<?php $ctd = $row['cat_id']; 
												echo getSingleColumn("cat_name","select * from `disease_category` where `id`='$ctd'");
												?>											
										  </td>
										  
										  
									<!--
	  <td width="30%" valign="top" class="event_name">
												<?php
												$subcategory_description	= DBout($row["subcategory_description"]);
													$subcategory_description_s = strip_tags($subcategory_description);
													if(strlen($subcategory_description_s) > 200){
														echo substr($subcategory_description_s,0,200).'...';
														}
														else{
															echo $subcategory_description_s;
															}
															?>
										  </td>	
-->									  											
											
											<td width="20%" valign="top" align="center" class="sales">
											<a href="<?php echo ABSOLUTE_PATH; ?>add_subcategory.php">Add New</a> | 
											<a href="add_subcategory.php?id=<?php echo $row['id']; ?>">Edit</a> | 
											<a onclick="return confirm('Are you sure you want delete this Sub-Category?');" href="?p=subcategories&del=<?php echo $row['id']; ?>">Delete</a>
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>add_subcategory.php">Add Sub-Category</a></strong>
								</div>
								
                               </div>