<?php

require_once('admin/database.php');


function makeRecommendedQuery($sele) {
	
	$ages 	= determineMemberAgeWeights($sele);
	$music	= determineMemberMusicWeights($sele);
	$events	= determineMemberEventWeights($sele);

	
	$category = 17;
	
	$events = $events[$category];
	foreach($events as $key => $value) {
		if ( $value > 0 )
			$tsubids[] = $key;
	}
	
	if ( count($tsubids) > 0 ) {
		$tmp = implode(",",$tsubids);
		$q1 = "  subcategory_id AND (". $tmp .")";
	}
	
	foreach($music as $key => $value) {
		if ( $value > 0 )
			$tmids[] = $key;
	}
	
	if ( count($tmids) > 0 ) {
		$tmp = implode(",",$tmids);
		$q2 = "  musicgenere_id AND (". $tmp .")";
	}
	
	foreach($ages as $key => $value) {
		if ( $value > 0 )
			$taids[] = $key;
	}
	
	if ( count($taids) > 0 ) {
		$tmp = implode(",",$taids);
		$q3 = "  event_age_suitab IN (". $tmp .")";
	}
	
	$sql = "select id from events where category_id=$category and ( " . $q1 . " OR ". $q2 ." OR ". $q3 ." ) ";
}

function determineMemberMusicWeights($sele)
{
	$member_id = 1;
	$mscore = array();
	$s3 	= "select * from member_music_pref where member_id='". $member_id ."' and selection='". $sele ."'";
	$rs3	= mysql_query($s3);
	while ( $r3 = mysql_fetch_assoc($rs3) ) {
		$selection = $r3['selection'];

		if ( $selection == 'O')
			$mscore[$r3['music_genre']] = 5;
		else if ( $selection == 'S')
			$mscore[$r3['music_genre']] = 3;
		else
			$mscore[$r3['music_genre']] = 0;
	}
	
	return $mscore;
	
}

function determineMemberAgeWeights($sele)
{
	$member_id = 1;
	$mscore = array();
	$s3 	= "select * from member_age_pref where member_id='". $member_id ."'";
	$rs3	= mysql_query($s3);
	while ( $r3 = mysql_fetch_assoc($rs3) ) {
		$selection = $r3['selection'];

		if ( $selection == 'O')
			$mscore[$r3['age_id']] = 4;
		else if ( $selection == 'S')
			$mscore[$r3['age_id']] = 2;
		else
			$mscore[$r3['age_id']] = 0;
	}
	
	return $mscore;
	
}

function determineMemberEventWeights($sele)
{
	$member_id = 1;
	$rs	= mysql_query("select id from categories");
	while ( $r = mysql_fetch_assoc($rs) ) {

		$rs2	= mysql_query("select id from sub_categories where categoryid=" . $r['id']);
		while ( $r2 = mysql_fetch_assoc($rs2) ) {
			$score[$r['id']][$r2['id']] = 0;
			$s3 = "select selection from member_prefrences where member_id='". $member_id ."' AND prefrence_type=" . $r2['id'];
			$rs3 = mysql_query($s3);
			if ( $r3 = mysql_fetch_assoc($rs3) ) {
				$selection = $r3['selection'];
				//echo $r['id'] . ' = ' . $r2['id'] . ' = ' . $selection . '<br>';
				if ( $selection == 'O')
					$score[$r['id']][$r2['id']] += 10;
				if ( $selection == 'S')
					$score[$r['id']][$r2['id']] += 5;
				else
					$sc = 0;
			}	
			
		}
	}
	
	return $score;
}

function returnSubCatEventList($cat) 
{
	$ids = array();
	$score = determineMemberEventWeights();
	$scores = $score[$cat];
	$sql1 = '';
	$sql2 = '';
	$pscr = -1;
	arsort($scores);
	foreach ($scores as $subcat => $score) {
		if ( $score > 0 ) {
			$sql[] = "select id from events where subcategory_id='". $subcat ."' and event_status=1 ";
		}	
	}
	
	if ( count($sql) > 0 ) {
		$rsql = implode(" UNION ",$sql);
		
		$res = mysql_query($rsql);
		while ($r = mysql_fetch_assoc($res) )
			$ids[] = $r['id'];
	}
	
	return $ids;	
}

?>
