<?php

include_once('admin/database.php');
include_once('site_functions.php');

$sql3 = "select * from sub_categories";
$res3 = mysql_query($sql3);
while ( $row3 = mysql_fetch_assoc($res3) ) {
		
	$count = 0;
	$sbid 	= $row3['id'];
	$tag 	= $row3['tag'];
	$tags = str_replace(","," ",$tag);

	$sql3 = "select * from events where event_source='Facebook' AND ( subcategory_id = 0 OR subcategory_id = '') AND MATCH (event_description) against ('". $tags ."') ";
	
	//$res3 = mysql_query($sql3);			
}


function separateTimes($str)
{
	$str 			= strtoupper($str);
	$tmp 			= explode(":",$str);
	$check_start	= $tmp[0];
	$ampm			= trim(substr($tmp[1],-2,2));
	$actual_end		= $check_start . $ampm;
	
	$checkingArray = array("11PM","12AM","1AM","2AM","3AM","4AM","5AM");
	
	if ( in_array($actual_end,$checkingArray) )
		return 'Night';
	
	return 'Day';	
}


?>