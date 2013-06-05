<?php

require_once("admin/database.php");

$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select DISTINCT id,event_name from `events` where `event_name` LIKE '$q%' && `pending_approval`=0";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {

	$vname = str_replace("|"," ",$rs['event_name']);
	echo $rs['id'] . '|' . "$vname" . "|" . "\n";
	
}

?>