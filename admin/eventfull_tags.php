<?php
	
include_once('database.php');
include_once('header.php');
include_once("xmlparser.php");

?>

<div class="bc_heading">
<div>Collect EventFull Events Tags</div>
</div>
<div id="outer"><div class="label"><strong> This may take some time...</strong></div></div>
<div style="padding:20px">

<?php

ob_start();

$total_added = 0;

$sql = "SELECT * FROM `events` WHERE `event_source` = 'EventFull' AND tags=''  AND ( 
		( SELECT event_date FROM event_dates WHERE event_id = events.id ORDER BY event_date DESC LIMIT 1 ) > DATE_SUB( CURDATE( ) , INTERVAL 1 DAY ))";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res) ) {
	
	$id			= $row['id'];
	$event_id	= $row['source_id'];
	
	$url3 = 'http://api.evdb.com/rest/events/tags/list?app_key=CSxRkR9vtmFKQTsX&id=' . $event_id ;
	$xml3 = simplexml_load_file($url3);
	
	$tags 	= $xml3->tags->tag;
	$tgs	= '';	
	foreach ($tags as $tag) {
		$tg = $tag->title;
		if ( $tg != '' ) 
			$tgs .= ',' . $tg;
	}
	
	if ( $tgs != '' ) {
		$event_query = "update events set tags='" . $tgs . "' where id='" . $id . "'";
		mysql_query($event_query) ;
		$total_added++;
	}	
}
			
?>

</div>


<script>
document.getElementById("loading").innerHTML = '<h1><?php echo $total_added;?>' + ' Records added.</h1>';
</script>
<?php  include_once('footer.php')?>