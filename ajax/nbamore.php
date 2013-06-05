<?php
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$id = $_POST['id'];
?>
<ul>
	<?php
	$res = mysql_query("select * from `special_event` where `specials_id`='$id' ORDER BY `id` ASC");
	$z = 0;
	$events_id = array();
	while($row = mysql_fetch_array($res)){
		$ev_id = $row['event_id'];
		$res2 = mysql_query("select * from `event_dates` where `event_id`='$ev_id' ORDER BY `event_date` ASC LIMIT 0, 1");
		while($row2 = mysql_fetch_array($res2)){
			$events_id[$ev_id] = $row2['event_date'];
			}
		}		
		asort($events_id);
		foreach($events_id as $events_id => $index){
			$specials_array = getSpecialsEvents($events_id,'simple');  // flyer or simple
			
			if($specials_array['id']!=''){
				$n++;
					$eventUrl = getEventURL($specials_array['id']);
					?>
            		<li class="as_more_box">
						<span>
							<a href="<?php echo $eventUrl; ?>"><?php 
					 		if(strlen($specials_array['event_name']) > 33)
						  	echo substr($specials_array['event_name'],0,33)."...";
					  		else
						  	echo $specials_array['event_name'];
							?>
							</a>
						</span>
						<br>
						<?php
						$event_dateT = getEventStartDateFB($specials_array['id']);
						echo date('D, F d, Y', strtotime($event_dateT[0]));
						?>
						at
						<?php
						$event_time = getEventTime($event_dateT[1]);
						echo date('h:i A', strtotime($event_time['start_time']));
						?>
						<br>
						<a href="#">more info</a>
					</li>
					<?php
				}
			}
		?>
</ul>