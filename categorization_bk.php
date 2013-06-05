<?php

include_once('admin/database.php');
include_once('site_functions.php');

$i=1;

$res2 = mysql_query("select id,tags from events ");
while ($rows= mysql_fetch_assoc($res2) ) {
	
	$eid 	= $rows['id'];
	$tg		= $rows['tags'];
	
	$cat	= 0;
	$subcat = 0;
	
	if ( trim($tg) != '' ) {
		$tags	= substr($tg,1) ;
		$tags  	= str_replace(","," ",$tags) ;
		$tags  	= str_replace("_"," ",$tags) ;
		$q = "select * from sub_categories where MATCH (name) AGAINST  ('". strtolower($tags) ."') ORDER BY rand() LIMIT 1";
		$res = mysql_query($q);
		$c=0;
		if ( mysql_num_rows($res) == 0 ) {
			$q = "select * from categories where MATCH (name) AGAINST  ('". strtolower($tags) ."') ORDER BY rand() LIMIT 1";
			$res = mysql_query($q);
			$c=1;
		}
		
		if( $r=mysql_fetch_assoc($res) ) {
			
			if ( $c == 1 ) {
				$cat	= $r['id'];
				$subcat = attribValue("sub_categories","id","where categoryid=".$r['id']);
			} else {
				$cat	= $r['categoryid'];
				$subcat = $r['id'];
			}	
			
			if ( mysql_query("update events set category_id='$cat', subcategory_id='$subcat',event_status='1' where id=$eid") ) {
				$i++;
				echo $i . ': Event Id = ' . $eid . ' Updated<br>'; 
			}
			//echo $i . ' = ' . $tags . ' = ' . $subcat . ' = ' . $cat . '<br>';
			
		}
	} 
}

$manual_categories = array(
							array("art",17,32),
							array("family",22,55),
							array("fundraiser",18,56),
							array("galleries",17,32),
							array("gallery",17,32),
							array("performing_arts",16,46),
							array("bar",19,24),
							array("pub",19,24),
							array("film",17,33),
							array("game",21,38),
							array("conference",18,34),
							array("food",19,24),
							array("community",16,66),
							
						);

foreach ( $manual_categories as $value) {
	$val = $value[0];
	$res2 = mysql_query("select id,tags from events where tags LIKE '%" . $val . "%' and category_id=0 ");
	while ($rows= mysql_fetch_assoc($res2) ) {
		
		$eid 	= $rows['id'];
		$tg		= $rows['tags'];
		
		$cat	= $value[1];
		$subcat = $value[2];
		
		mysql_query("update events set category_id='$cat', subcategory_id='$subcat',event_status='1' where id=$eid") ;
		$i++;
		echo $i . ': Event Id = ' . $eid . ' Updated<br>'; 
			
	}	
}

?>