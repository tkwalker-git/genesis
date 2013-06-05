<?php

include_once('database.php');
include_once('header.php'); 
?>

<div class="bc_heading">
	<div>Event Categorization</div>
</div>

<?php if ( $_GET['start'] == '' ) { ?>

<div style="padding:20px">
	<a href="?start=1"><strong>Start Database Clean Up</strong></a>
	<br>
	This may take some time. The process is, 
	<br>1. Move events data to <strong>eventgra_expired_event</strong>.
	<br>2. Delete all events which are expired 1 week before to manage the database load.
</div>

<?php } else { ?>

<div style="padding:20px">


<?php



// Condition for getting expired events for more than a week
mysql_query("TRUNCATE TABLE active_events");
mysql_query("insert ignore into active_events select DISTINCT event_id from event_dates where event_date > DATE_SUB(CURDATE(),INTERVAL 2 MONTH)");

// Copy Data to Expired Events Database
mysql_query("insert ignore into eventgra_expired_event.events select * from eventgra_events.events WHERE id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("insert ignore into eventgra_expired_event.event_dates select * from eventgra_events.event_dates WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("insert ignore into eventgra_expired_event.event_music select * from eventgra_events.event_music WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("insert ignore into eventgra_expired_event.event_hosts select * from eventgra_events.event_hosts WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("insert ignore into eventgra_expired_event.venue_events select * from eventgra_events.venue_events WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");


// delete from Current Database

mysql_query("delete from eventgra_events.events WHERE id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
$total = mysql_affected_rows();

mysql_query("delete from eventgra_events.event_dates WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("delete from eventgra_events.event_music WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("delete from eventgra_events.event_hosts WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("delete from eventgra_events.venue_events WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("delete from eventgra_events.venue_events WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");
mysql_query("delete from eventgra_events.event_wall WHERE event_id NOT IN ( SELECT event_id FROM eventgra_events.active_events )");

if ( $total > 0 ) 
	echo '<h1>'. $total .' events Deleted.</h1>';
else
	echo '<h1>No expired event found.</h1>';
	
?>

</div>

<?php 
} 
include_once('footer.php');
?>