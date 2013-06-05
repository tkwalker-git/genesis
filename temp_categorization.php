<?php

include_once('admin/database.php');
include_once('site_functions.php');
/*
$res = mysql_query("select * from events");
while ($rows= mysql_fetch_assoc($res) ) {
	
	$eid = $rows['id'];
	
	$pair = rand(1,$t);
	
	$cid = $categories[$pair][0];
	$sid = $categories[$pair][1];
	
	if ( mysql_query("update events set category_id=$cid, subcategory_id=$sid where id=$eid") )
		echo 'Updated: '. $eid . '<br>';
	
}
*/

$cat_q = "select * from categories order by id ASC";
$cat_res = mysql_query($cat_q);
while($cat_r=mysql_fetch_assoc($cat_res)) { 
	
	$cid = $cat_r['id'];
	$res = mysql_query("select * from sub_categories where categoryid=". $cid);
	while($r=mysql_fetch_assoc($res)) 
		$categories[] = array($cid,$r['id']);
}

$t = count($categories);

$res = mysql_query("select * from events");
while ($rows= mysql_fetch_assoc($res) ) {
	
	$eid = $rows['id'];
	
	$pair = rand(1,$t);
	
	$cid = $categories[$pair][0];
	$sid = $categories[$pair][1];
	
	if ( mysql_query("update events set category_id=$cid, subcategory_id=$sid where id=$eid") )
		echo 'Updated: '. $eid . '<br>';
	
}


?>