<?php

include_once('admin/database.php');
include_once('site_functions.php');

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

$res = mysql_query("select * from events where event_source='Facebook' and ( subcategory_id = 0 OR subcategory_id = '') ");
$tot =0;
$count = array();
while ($rows= mysql_fetch_assoc($res) ) {
	
	$eid 		= $rows['id'];
	$ename		= strtolower($rows['event_name']);
	$edesc		= strtolower($rows['event_description']);
	
	$sql3 = "select * from sub_categories where tag!=''";
	$res3 = mysql_query($sql3);
	while ( $row3 = mysql_fetch_assoc($res3) ) {
	
		$sbid = $row3['id'];
		$tag = strtolower($row3['tag']) ;
		
		$tags = explode(",",$tag);
		
		if ( count($tags) > 0 ) 
			foreach ( $tags as $tag ) 
				if ( trim($tag) != '' )
					if ( substr_count($edesc," ".$tag." ") > 0 )
						$count[$eid][$sbid]++;
	}
}

if ( count($count) > 0 ) {
	foreach ($count as $e => $v) {
		foreach($v as $sc => $v1) {
			$temp = 0;
			if ( $v1 > $temp )
				$temp = $v1;
			$subc = $sc;	
		}
		
		$cat_id 	= attribValue("sub_categories","categoryid","where id=" . $subc);
		
		$sql5 = "update events set category_id='". $cat_id ."', subcategory_id='". $subc ."' where id='". $e ."'";
		mysql_query($sql5);
		//echo  '<br>';
	}
}	

// 2nd Iteration... 

$res = mysql_query("select * from events where event_source='Facebook' and ( subcategory_id = 0 OR subcategory_id = '') ");
while ($rows= mysql_fetch_assoc($res) ) {
	
	$eid 		= $rows['id'];
	$ename		= strtolower($rows['event_name']);
	$edesc		= strtolower($rows['event_description']);
	
	if ( substr_count($edesc,' adult ') > 0 || substr_count($edesc,' 18+ ') > 0 )
		$age = 5;
		
	if ( separateTimes($rows['event_end_time']) == 'Night' ) {
		$category = 19;
		$sql5 = "update events set category_id='". $category ."', subcategory_id='80' where id='". $eid ."'";
		mysql_query($sql5);
	}
}

?>