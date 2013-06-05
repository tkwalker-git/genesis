<?php

require_once("database.php"); 

$sql = "update events set event_score = IF(musicgenere_id='',0,5) + IF(event_age_suitab='',0,4) + 10";
mysql_query($sql);
/*
$res = mysql_query("select id from age");
while ( $r = mysql_fetch_assoc($res) ) 
	$age[] = $r['id'];

$ac = count($age);

$res = mysql_query("select id from music");
while ( $r = mysql_fetch_assoc($res) ) 
	$music[] = $r['id'];
$mc = count($music);
	

$res = mysql_query("select id from events");
while ( $r2 = mysql_fetch_assoc($res) ) {

	$m = rand(0,$mc-1);
	$a = rand(0,$ac-1);
	
	$m1 = $music[$m];
	$a1 = $age[$a];
	
	$sql = "update events set musicgenere_id = $m1, event_age_suitab=$a1 where id=" . $r2['id'];
	mysql_query($sql);
}

*/

?>