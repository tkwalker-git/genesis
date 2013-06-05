								<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="24%">EVENT NAME</td>
											<td width="29%">EVENT INFORMATION</td>
											<td width="15%" align="center">SALES</td>
											<td width="14%">REACH</td>
											<td width="18%">ACTIONS</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->
								
								<?php
								include("page_form.php");
								
								$qry2 = "select e.id,ed.event_date, e.event_type, e.userid, e.event_name, e.event_status, e.is_expiring, e.type, e.event_cost, e.view from events e, event_dates ed where ed.event_id=e.id && e.is_expiring='1' && e.userid=".$member_id." GROUP BY ed.event_id UNION select e.id,ed.event_date, e.event_type, e.userid, e.event_name, e.event_status, e.is_expiring, e.type, e.event_cost, e.view from events e, event_dates ed where ed.event_id=e.id && e.is_expiring='0' && e.userid=".$member_id." GROUP BY ed.event_id ORDER BY is_expiring DESC, event_status DESC, event_date ASC";
								
								$rest123 = mysql_query($qry2);
								$totl_records=mysql_num_rows($rest123);
								
								$lim_record=10;		// records per page
								
								$total_pages=ceil($totl_records/$lim_record);     // ceil rounds to ceil number(4.2 to 5)
								$page_num=0; 
								if(isset($_REQUEST['page']))
								{
								$page_num=$_REQUEST['page']; // from pagination_interface.php file..........
								}
								else
								{
								$page_num=1;
								}
								if($page_num==1)
								{
								$start_record=0;  // As we know In mysql database records index starts from 0 
								}
								else
								{
								$start_record= $page_num*$lim_record - $lim_record;
								
								}
								
			
								$sql = "select e.id,ed.event_date, e.event_type, e.userid, e.event_name, e.event_status, e.is_expiring, e.type, e.event_cost, e.view from events e, event_dates ed where ed.event_id=e.id && e.is_expiring='1' && e.userid=".$member_id." GROUP BY ed.event_id UNION select e.id,ed.event_date, e.event_type, e.userid, e.event_name, e.event_status, e.is_expiring, e.type, e.event_cost, e.view from events e, event_dates ed where ed.event_id=e.id && e.is_expiring='0' && e.userid=".$member_id." GROUP BY ed.event_id ORDER BY is_expiring DESC, event_status DESC, event_date ASC LIMIT $start_record,$lim_record";
								$res = mysql_query($sql);
								$i=0;
								$bg = "ffffff";
								while($row = mysql_fetch_array($res)){
									$event_id		= $row['id'];
									$event_name 	= DBout($row['event_name']);
									$RSVPs			= getSingleColumn('tot',"select count(*) as tot from events_rsvp where `event_id`='$event_id'");
									$venue_attrib	= getEventLocations($event_id);
									$event_locations= $venue_attrib[0];
									$event_url		= getEventURL($event_id);
									$event_type		= $row['event_type'];
									
									$startDate		= getSingleColumn("event_date","select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC LIMIT 0,1");
									$entDate		= getSingleColumn("event_date","select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` DESC LIMIT 0,1");
									$startDateId	= getSingleColumn("id","select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC LIMIT 0,1");
									$endDateId		= getSingleColumn("id","select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` DESC LIMIT 0,1");
									$startTime		= getSingleColumn("start_time","select * from `event_times` where `date_id`='$startDateId'");
									$endTime		= getSingleColumn("end_time","select * from `event_times` where `date_id`='$endDateId'");
									
									
								if($bg=='ffffff')
									$bg='f6f6f6';
								else
									$bg = "ffffff";
								
								?>
								<div class="ev_eventBox" style="background:#<?php echo $bg; ?>">
									<table cellpadding="0" cellspacing="0" width="99%" align="center">
										<tr>
											<td width="24%" valign="top" class="event_name">
											<a href="<?php echo $event_url; ?>"><?php echo $event_name; ?></a>
											<br />
											<br /><br /><br /><br /><br /><br />
											<div>STATUS:  
											<?php if ($row['event_status'] == 1){?><span style="color:#289701">Active</span><?php }
											else{?> <span style="color:#a80233">Not Active</span><?php }
											
											if($row['type']=='draft'){?>
												<font color="red"> (Draft)</font>
											<?php } ?></div> 
											
										  </td>
											<td width="29%" class="event_info" valign="top">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td width="27%" valign="top"><strong>WHERE:</strong></td>
														<td width="73%"><span><?php echo $venue_attrib[1]['venue_name']; ?></span></td>
													</tr>
													<tr>
														<td><strong>STARTS:</strong></td>
														<td><span>
																<!-- Fri Mar 2, 8pm -->
																<?php echo date('D M d', strtotime($startDate));
																echo date(', g A', strtotime($startTime));?>
															</span>
														</td>
													</tr>
													<tr>
														<td><strong>ENDS:</strong></td>
														<td><span>
																<!-- Sat Mar 3, 2am -->
																<?php echo date('D M d', strtotime($entDate));
																echo date(', g A', strtotime($endTime));?>
															</span></td>
													</tr>
													<tr>
														<td><strong>COST:</strong></td>
														<td><span><?php echo $row['event_cost']; ?></span></td>
													</tr>
											  </table>
										  </td>
											
											<td width="15%" valign="top" align="center" class="sales">	
											<?php
											if($event_type!=0){
												echo "$".number_format(getTicketGross($event_id,'event'),2);
											}
											else
												echo '---';
												
											$prices = 0;
											?>
											</td>
											<td width="14%" valign="top">
												<strong>RSVPs:</strong>  <span><?php echo $RSVPs; ?></span><br />
												<strong>VIEWS:</strong> <span><?php echo $row['view']; ?></span>
										  </td>
											<td width="18%" valign="top" class="dele">
												<a href="create_event.php?id=<?php echo $event_id;?>">Edit Event</a><br />
												<?php
													if($row['event_type']!='0'){
												?>
														<a href="load_xls.php?type=rsvp&event_id=<?php echo $event_id;?>">Download RSVPs</a><br />
														<a href="load_xls.php?type=ticket&event_id=<?php echo $event_id;?>">Download Sales</a><br />
														<a href="close_rsvp.php?id=<?php echo $event_id;?>">Turn-Off RSVPs</a><br />
														<a href="create_coupon.php">Create Coupon</a><br />
														
												<?php
												}
												?>
												<!-- <a href="#">Turn-Off Tickets</a><br /> -->
												<a href="javascript:void(0)" onclick="removeAlert('event_manager.php?delete=<?php echo $event_id;?>')">Delete</a>
										  </td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="center">
								<br />
									<?php include("pagination_interface.php"); ?>
								</div>