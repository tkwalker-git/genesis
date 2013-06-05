<?php

require_once("database.php"); 
error_reporting(0);
$qry	=	"select * from events order by id ";
$res = mysql_query($qry);
while($row = mysql_fetch_array($res)){
	$event_id		=	$row['id'];
	$bc_event_name	= html_entity_decode(trim($row["event_name"]),ENT_QUOTES);
	$bc_seo_name	= make_seo_names($bc_event_name,"events","seo_name","");
	//echo $bc_seo_name . '<br>';
	mysql_query("UPDATE events SET seo_name = '$bc_seo_name' WHERE id = '" . $event_id ."'");
}


function seonm($string)
{
	$string = preg_replace("/[^A-Za-z0-9 -]/", "", $string);
	$string = str_replace(" ","-",$string);
	$string = strtolower($string);
	$string = trim($string);
	
	$string = explode("-", $string);
	$string = array_slice($string, 0, 30);
	$string = array_filter($string, 'strlen');
	$string = join("-", $string);
	$string = trim($string, "-");

	return $string;
}

?>