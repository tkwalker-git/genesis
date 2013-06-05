<?php
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$day = $_POST['day'];
	$specials_id = 2;
	
						$res = mysql_query("select * from `special_event` where `specials_id`='$specials_id'");
						while($row	= mysql_fetch_array($res)){
							$t_event_id		= $row['event_id'];
							$event_name		= getSingleColumn('event_name',"select * from `events` where `id`='$t_event_id'");
							$event_type		= getSingleColumn('event_type',"select * from `events` where `id`='$t_event_id'");
							$event_dateT	= getEventStartDateFB($t_event_id);
							$event_date		= $event_dateT[0];
							$event_time		= getEventTime($event_dateT[1]);
							$event_start_day	= strtoupper(date('l', strtotime($event_dateT[0])));
								
								if($event_type=='0' && $event_start_day==$day){
									if(strlen($event_name) > 38)
										$event_name = substr($event_name,0,38)."...";
							?>
										<li>
										
											<span class="title"><a href="<?php  echo getEventUrl($t_event_id); ?>" target="_blank"><?php echo $event_name; ?></a></span>
											<span class="date"><?php echo date('D. M d',strtotime($event_date))." &nbsp;".date('h:m A',strtotime($event_time['start_time'])); ?></span>
										</li>
								<?php
									} // end if $event_type==0
									
							} // end 
							?>