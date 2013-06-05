<?php

//require_once("database.php");
//
//$q = strtolower($_GET["q"]);
//if (!$q) return;
//
//$sql = "select DISTINCT venue_name,id,venue_address from venues where venue_name LIKE '$q%'";
//$rsd = mysql_query($sql);
//while($rs = mysql_fetch_array($rsd)) {
//	$vname = str_replace("|"," ",$rs['venue_name']);
//	echo $rs['id'] . '|' . "$vname" . ", ". $rs['venue_address'] ."" . "\n";
//}







require_once("database.php");

$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select DISTINCT venue_name,id,venue_city,venue_zip,venue_address from `venues` where `venue_name` LIKE '$q%'";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$vname = str_replace("|"," ",$rs['venue_name']);
	echo $rs['id'] . '|' . "$vname" . "|" . $rs['venue_address'] . "|" . $rs['venue_city'] . "|" . $rs['venue_zip'] . "" . "\n";
	
}




/*
if($_GET['venue_name']){
		$v_n =	$_GET['venue_name'];
	
		$sql = "select DISTINCT id from venues where venue_name = '$v_n'";
		$rsd = mysql_query($sql);
		while($rs = mysql_fetch_array($rsd)) {
		echo  $rs['id'];
	}	

}

$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select DISTINCT venue_name,id from venues where venue_name LIKE '$q%'";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$vname = str_replace("|"," ",$rs['venue_name']);
	echo "$vname" . "\n";
}
*/
?>