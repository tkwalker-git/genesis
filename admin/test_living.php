<?php

include_once("xmlparser.php");
/*
$xm = file_get_contents("http://deals.livingsocial.com/cities.atom?offer_id=4&aff_id=3481");
$xm = str_replace("ls:","LL",$xm);

$XMLFileName =  'living.xml';

if (!$handle = fopen($XMLFileName, 'w')) {
	 echo "Cannot open file ($XMLFileName)";
	 exit;
}

if (fwrite($handle, $xm) === FALSE) {
	echo "Cannot write to file ($XMLFileName)";
	exit;
}

fclose($handle);
*/

$string = "living.xml";
	
$deals_xml = simplexml_load_file("$string");	
$i=0;
foreach ($deals_xml->entry as $ent) 
{ 
	
	$city = $ent->LLlocation->LLcity;
	if ( strtolower($city) == 'orlando' ) {
		$i++;
		echo $i . ' : ' ;
		echo $ent->id;
		echo ' = ' . $city ;
		echo '<br>';
		
	}	
	//$a =  $ent->location;	
	//print_r($ent);
	
}	

?>