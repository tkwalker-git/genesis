							<?php
								$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
							?>
							
								<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="20%">RSVPs</td>
											<td width="33%">Event Name</td>
											<td width="29%">Event Date</td>
											<td width="18%">Action</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->
								
								<?php
								include("page_form.php");
								
								
								$qry2 = "select  e.id, e.event_name from events e, events_rsvp er where e.userid=$member_id && e.id=er.event_id";
							//	$qry2	= "select r.event_id, r.name, r.email, r.how_did_hear, r.created_date from events_rsvp r, events e where e.id=r.event_id && e.userid=$member_id";
								$rest123 = mysql_query($qry2);
								$totl_records=mysql_num_rows($rest123);
								
								$lim_record=20;		// records per page
								
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
								
			
							// $sql = "select r.event_id, r.name, e.event_name, r.email, r.how_did_hear, r.created_date from events_rsvp r, events e where e.id=r.event_id && e.userid=$member_id ORDER BY r.created_date DESC LIMIT $start_record,$lim_record";
							
							$sql = "select  e.id, e.event_name from events e, events_rsvp er where e.userid=$member_id && e.id=er.event_id GROUP BY er.event_id LIMIT $start_record,$lim_record";
								$res = mysql_query($sql);
								$i=0;
								$bg = "ffffff";
								if(mysql_num_rows($res)){
									while($row = mysql_fetch_array($res)){
									
									$event_id	= $row['id'];
									
									if($bg=='ffffff')
										$bg='f6f6f6';
									else
										$bg = "ffffff";
										
									$RSVPs = getSingleColumn("tot","select count(*) as tot from events_rsvp where event_id=" . $event_id);
									$event_dateT		= getEventStartDateFB($event_id);
									
									?>
									<div class="ev_eventBox" style="background:#<?php echo $bg; ?>">
										<table cellpadding="0" cellspacing="0" width="99%" align="center">
											<tr>
												<td width="20%" valign="top" style="font-size:18px"><?php echo $RSVPs; ?></td>
												<td width="33%" valign="top"><?php echo $row['event_name']; ?></td>
												<td width="29%" valign="top"><?php echo date('d M Y', strtotime($event_dateT[0])); ?></td>
												<td width="18%" valign="top">
													<a href="view.php?type=rsvp&event_id=<?php echo $event_id; ?>" target="_blank">View</a> &nbsp; &nbsp; 
													<a href="load_xls.php?type=rsvp&event_id=<?php echo $event_id;?>">Export</a></td>
											</tr>
										</table>
									</div>
									<?php }
									}
									else{
										echo "<div style='padding:40px; text-align:center;color:red'><h2>No Record Found</h2></div>";
									}?>
								<div align="center">
									<br />
									<?php include("pagination_interface.php"); ?>
								</div>