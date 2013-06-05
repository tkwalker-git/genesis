<?php

	if ( $_GET['delete'] == 'y' && $_GET['event_id'] > 0  ) {
		$sq = "delete from event_wall where userid=$member_id and event_id=" . $_GET['event_id'];
		mysql_query($sq);
		echo "<script>window.location.href='myeventwall.php';</script>";	
	}

?>
							<div class="head_new"><?php echo $name;?> Events</div>
							
							<div class="recBox">
								<div class="yellow_bar">
									Your Events This Week: 
									<?php 
										$week = getCurrentWeek();
										echo date("F jS",$week['start']) . ' - ' . date("F jS",$week['end']);
									?>
								
								</div>
							    
								 <div class="title-row">
								 	<div class="event-date-title">Event Date</div>
									<div class="event-name-title">Event Name</div>
									<div class="location-title">Location</div>
									<div class="confirmation-title">Are You Going?</div>
									<div class="actions-title">Actions</div>								 
								 </div>
								 <?php
								 	
									$sqle = "select * from events where event_status='1' AND id IN (select event_id from event_wall where userid=". $member_id ." ) AND id IN (select event_id from event_dates where event_date BETWEEN '". date("Y-m-d",$week['start']) ."' AND '". date("Y-m-d",$week['end']) ."')";
									$rese = mysql_query($sqle);
									$i=0;
									while ($evet = mysql_fetch_assoc($rese) ) {
										$i++;
										$event_id = $evet['id'];
										if ( ($i%2) == 0 )
											$cls = 'evenRow';
										else
											$cls = 'oddRow';	
										
										$event_sdate 	= getEventStartDates($event_id);
										$event_url		= getEventURL($event_id);
										$event_name		= DBout($evet['event_name']);
										$venue_attrib	= getEventLocations($event_id);
										
										$going			= attribValue('event_wall', 'going', "where event_id='$event_id' and userid='$member_id'");
										
										if ( $going == '' )
											$going = -1;
											
											
								 ?>
							     <div class="<?php echo $cls;?>">
								 	<div class="event-date"><?php echo $event_sdate;?></div>
									<div class="event-name"><a href="<?php echo $event_url; ?>"><?php echo $event_name; ?></a></div>
									<div class="location"><a href="<?php echo $event_url; ?>?type=location"><?php echo $venue_attrib[1]['venue_name'];?></a></div>
									<div class="confirmation">
										<!--<form name="confirm" id="confirm">
										<input type="radio" name="confirm" value="yes"/>
										<input type="radio" name="confirm" value="no"/> </form>	-->
										<?php if ( $going == 0 ) { ?>
											<div class="yesbtn"><a href="?going=yes&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>yes-button.png" /></a></div>
											<div class="nobtn"><a href="?d=1&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>no-button_sel.png" /></a></div>
										<?php } else if ( $going == 1 ) { ?>
											<div class="yesbtn"><a href="?d=1&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>yes-button_sel.png" /></a></div>
											<div class="nobtn"><a href="?going=no&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>no-button.png" /></a></div>
										<?php } else { ?>
											<div class="yesbtn"><a href="?going=yes&id=<?php echo $event_id;?>&n=1"><img src="<?php echo IMAGE_PATH;?>yes-button.png" /></a></div>
											<div class="nobtn"><a href="?going=no&id=<?php echo $event_id;?>&n=1"><img src="<?php echo IMAGE_PATH;?>no-button.png" /></a></div>
										<?php } ?>
											
									</div>
									<div class="actions">
										<div class="view-details"><a href="<?php echo $event_url; ?>">View Deals</a></div>
										<div class="share-event">
										 <script type="text/javascript">var addthis_pub = "UnitedDatabase";</script>
										<a  href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '<?php echo $event_url; ?>', '<?php echo $event_name; ?>')" onmouseout="addthis_close()" onclick="return addthis_sendto()" >Share Event</a>
										<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
										</div>
										<div class="remove-event">
											<a onclick="deleteEventFromWall('<?php echo ABSOLUTE_PATH;?>myeventwall.php?delete=y&event_id=<?php echo $event_id;?>')" href="javascript:void(0)">Remove</a>
										</div>
									</div>
								
								 </div><!-- row --> 
								 <?php } ?>
							
								 <br class="clr" />
								<div class="yellow_bar">Your Events this Month: <?php echo date("F Y");?></div>
							 
								 <div class="title-row">
								 	<div class="event-date-title">Event Date</div>
									<div class="event-name-title">Event Name</div>
									<div class="location-title">Location</div>
									<div class="confirmation-title">Are You Going?</div>
									<div class="actions-title">Actions</div>								 
								 </div>
							     
								  <?php
								 	
									$sqle = "select * from events where event_status='1' and id IN (select event_id from event_wall where userid=". $member_id ." ) AND id IN (select event_id from event_dates where event_date > '". date("Y-m") ."-01' AND event_date < '". date("Y-m") ."-31')";
									$rese = mysql_query($sqle);
									$i=0;
									$going = '';
									while ($evet = mysql_fetch_assoc($rese) ) {
										$i++;
										$event_id = $evet['id'];
										if ( ($i%2) == 0 )
											$cls = 'evenRow';
										else
											$cls = 'oddRow';	
										
										$event_sdate 	= getEventStartDates($event_id);
										$event_url		= getEventURL($event_id);
										$event_name		= DBout($evet['event_name']);
										$venue_attrib	= getEventLocations($event_id);
										$going			= attribValue('event_wall', 'going', "where event_id='$event_id' and userid='$member_id'");
										
										if ( $going == '' )
											$going = -1;
											
								 ?>
							     <div class="<?php echo $cls;?>">
								 	<div class="event-date"><?php echo $event_sdate;?></div>
									<div class="event-name"><a href="<?php echo $event_url; ?>"><?php echo $event_name; ?></a></div>
									<div class="location"><a href="<?php echo $event_url; ?>?type=location"><?php echo $venue_attrib[1]['venue_name'];?></a></div>
									<div class="confirmation">
										<!--<form name="confirm" id="confirm">
										<input type="radio" name="confirm" value="yes"/>
										<input type="radio" name="confirm" value="no"/> </form>	-->
										<?php if ( $going == 0 ) { ?>
											<div class="yesbtn"><a href="?going=yes&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>yes-button.png" /></a></div>
											<div class="nobtn"><a href="?d=1&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>no-button_sel.png" /></a></div>
										<?php } else if ( $going == 1 ) { ?>
											<div class="yesbtn"><a href="?d=1&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>yes-button_sel.png" /></a></div>
											<div class="nobtn"><a href="?going=no&id=<?php echo $event_id;?>"><img src="<?php echo IMAGE_PATH;?>no-button.png" /></a></div>
										<?php } else { ?>
											<div class="yesbtn"><a href="?going=yes&id=<?php echo $event_id;?>&n=1"><img src="<?php echo IMAGE_PATH;?>yes-button.png" /></a></div>
											<div class="nobtn"><a href="?going=no&id=<?php echo $event_id;?>&n=1"><img src="<?php echo IMAGE_PATH;?>no-button.png" /></a></div>
										<?php } ?>
									</div>
									<div class="actions">
										<div class="view-details"><a href="<?php echo $event_url; ?>">View Deals</a></div>
										<div class="share-event">
										<script type="text/javascript">var addthis_pub = "UnitedDatabase";</script>
										<a  href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '<?php echo $event_url; ?>', '<?php echo $event_name; ?>')" onmouseout="addthis_close()" onclick="return addthis_sendto()" >Share Event</a>
										<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
										</div>
										<div class="remove-event">
											<a onclick="deleteEventFromWall('<?php echo ABSOLUTE_PATH;?>myeventwall.php?delete=y&event_id=<?php echo $event_id;?>')" href="javascript:void(0)">Remove</a>
										</div>
									</div>
								
								 </div><!-- row --> 
								 <?php } ?>
									
								 <br class="clr" />
								 </div>