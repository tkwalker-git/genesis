<?php

require_once('admin/database.php');
	
$sql = "update events set source_id = SUBSTRING( source_id,1,18) WHERE SUBSTRING( source_id, 19, 1 ) = '@'";
mysql_query($sql);

$sql1 = "SELECT source_id FROM `events` GROUP BY source_id having count( * ) > 1";
$res1 = mysql_query($sql1);

while ($r1 = mysql_fetch_assoc($res1) ) {

	$sql2 = "select id from events where source_id='". $r1['source_id'] ."' order by id" ;
	$res2 = mysql_query($sql2);
	$i = 1;
	while ( $r2 = mysql_fetch_assoc($res2) ) {
		
		if ( $i == 1 ) {
			$first_event = $r2['id'];
		} else {
			if ( $first_event > 0 ) {
				mysql_query("update event_dates set event_id='". $first_event ."' where event_id='". $r2['id'] ."'");
				mysql_query("delete from venue_events where event_id='". $r2['id'] ."'");
				mysql_query("delete from events where id='". $r2['id'] ."'");
			}
		}
		$i++;
	}
}

// disable past events
$sql = "update events set event_status='0' where id NOT IN (SELECT event_id FROM event_dates WHERE event_date > DATE_SUB(CURDATE(),INTERVAL 1 DAY))"


?>