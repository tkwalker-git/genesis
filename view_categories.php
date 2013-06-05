

<?php
	if($_GET['p'] == 'categories' && isset($_GET['del'])){		
		mysql_query("delete from `disease_category` where id='". $_GET['del'] ."'");		
	}
?>



                            	<div class="yellow_bar">
									<table cellpadding="4px"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="5%" align="center"></td>
											<td width="30%">Category Name</td>
											<td width="40%">Category Description</td>									
											<td width="25%" align="center">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								
									if(isset($_GET['search'])){
									$search=$_GET['search'];
									$sql = "select * from `disease_category` where `cat_name` like '%$search%'";
									}else {								
									$sql = "select * from `disease_category` ORDER BY cat_name ASC";
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
										  
										  <td width="30%" valign="top" class="event_name">
												<?php echo $row['cat_name']; ?>										
										  </td>
										  
										  
										  <td width="40%" valign="top" class="event_name">
												<?php
												if(strlen($row['category_description']) > 250){
													echo substr($row['category_description'],0,250). '...';
												}
												else{
													echo $row['category_description'];
												}
												?>
										  </td>										  											
											
											<td width="25%" valign="top" align="center" class="sales">
											<a href="<?php echo ABSOLUTE_PATH; ?>add_category.php">Add New</a> | 
											<a href="add_category.php?id=<?php echo $row['id']; ?>">Edit</a> | 
											<a onclick="return confirm('Are you sure you want delete this Category?');" href="?p=categories&del=<?php echo $row['id']; ?>">Delete</a>
											</td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="right" style="padding-right:10px;"><br />
									<strong><a href="<?php echo ABSOLUTE_PATH; ?>add_category.php">Add Category</a></strong>
								</div>
								
                               </div>