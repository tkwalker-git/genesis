

                            	<div class="yellow_bar">

									<table cellpadding="0"  cellspacing="0" width="99%" align="center">

										<tr>

											<td width="2%"></td>	
											<td width="78%">EVENT NAME</td>										
											<td width="20%" align="center">Action</td>

										</tr>

									</table>

								</div> <!-- /yellow_bar -->



								<?php

								include("page_form.php");



								$loginEmail	= getSingleColumn("email","select * from `users` where `id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");



								$qry2 = "select event_name,id from events where userid='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";



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

								$sql = "select event_name,event_status,id from events where userid='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' LIMIT $start_record,$lim_record";



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

									$is_private		= getSingleColumn("is_private","select * from `events` where `id`='". $event_id ."'");



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

											<td width="80%" valign="top" class="event_name">

											<a target="_blank" href="<?php echo ABSOLUTE_PATH;?>fbflayer/index.php?id=<?php echo $row['id']; ?>"><?php echo $event_name; ?></a>
											<!-- <a href="<?php echo $event_url; ?>"><?php echo $event_name; ?></a> -->

											<br />

											<br /><br /><br /><br />

											<div>STATUS:  

											<?php if ($row['event_status'] == 1){?>

												<span style="color:#289701">Active </span>

											<?php }

											else{?>

												<span style="color:#a80233">Not Active</span>

											<?php }



											if($row['type']=='draft'){?>

												<font color="red"> (Draft)</font>

											<?php } ?></div><br />
											
											<div>SHOWCASE LINK:  

												<span style="color:#505050"><?php echo ABSOLUTE_PATH;?>fbflayer/index.php?id=<?php echo $row['id']; ?></span>

											</div> 											

										  </td>
										  						

											<td width="20%" valign="top" align="center" class="sales">
											<a target="_blank" href="<?php echo ABSOLUTE_PATH;?>fbflayer/index.php?id=<?php echo $row['id']; ?>">Promote</a> | 
											<a href="create_showcase.php?id=<?php echo $row['id']; ?>">Edit</a> | 
											<a onclick="return confirm('Are you sure you want delete this showcase?');" href="">Delete</a>

											<td>

										</tr>

									</table>

								</div>

								<?php }?>

								<div align="right" style="padding-right:10px;"><br />

									<strong><a href="<?php echo ABSOLUTE_PATH; ?>create_showcase.php?type=showcase">Create Showcase</a></strong>

								</div>

								<div align="center">

								<br />

									<?php include("pagination_interface.php"); ?>

								</div>

                               </div>