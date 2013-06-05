<?php

require_once("admin/database.php");

$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select DISTINCT test_name,id,description,suppliers,cost from `tests` where `test_name` LIKE '$q%'";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$vname = str_replace("|"," ",$rs['test_name']);
	echo $rs['id'] . '|' . "$vname" . "|" . $rs['description'] . "|" . $rs['suppliers'] . "|" . $rs['cost'] . "" . "\n";
	
}

?>