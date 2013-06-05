<?php

require_once('admin/database.php');

mysql_query("update events set is_expiring=0");
mysql_query("update events set is_expiring=1 where 1=1 AND (select event_date from event_dates where event_id=events.id ORDER by event_date DESC LIMIT 1) > DATE_SUB(CURDATE(),INTERVAL 1 DAY)");

mysql_query("DELETE FROM event_dates WHERE event_id NOT IN (select id from events) ");
mysql_query("UPDATE event_dates set expired=1 WHERE event_date < date_sub(now(), interval 1 day)");

mysql_query("DELETE FROM event_times WHERE date_id NOT IN (select id from event_dates) ");

mysql_query("DELETE FROM venue_events WHERE event_id NOT IN (select id from events) ");


mysql_query("OPTIMIZE TABLE events");
mysql_query("OPTIMIZE TABLE event_dates");
mysql_query("OPTIMIZE TABLE event_times");
mysql_query("OPTIMIZE TABLE venue_events");

?>