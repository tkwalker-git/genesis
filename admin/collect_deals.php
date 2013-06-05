<?php
	
	include_once("database.php");
	
	// Collect LivingSocial Deals
	$url = "http://deals.livingsocial.com/cities.atom?offer_id=4&aff_id=3481";
	
	$xm = file_get_contents($url);
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
	$string = "living.xml";
	$deals_xml = simplexml_load_file($string);	
	
	mysql_query("truncate deals");
	
	foreach ($deals_xml->entry as $ent) 
	{ 
		$liveid  		= DBin($ent->id);
		$dealurl 		= DBin($ent->link->attributes()->href);
		$subtitle 		= DBin($ent->subtitle);
		$price	 		= utf8_decode($ent->price);
		$value 			= utf8_decode($ent->value);
		
		$savings 		= DBin($ent->savings);	
		$image   		= DBin($ent->image_url);
		
		$description1 	= DBin($ent->description);
		$description	= utf8_decode($description1);   
		//$description 	= str_replace('?','&#128;',$description2); 
		$city = $ent->LLlocation->LLcity;
		if ( strtolower($city) == 'orlando' ) {
			$sql = "INSERT INTO deals (source,dealid,linktype,subtitle,price,value,saving,imageurl,description) VALUES ('living','$liveid','$dealurl','$subtitle','$price','$value','$savings','$image','$description')";
			$res = mysql_query($sql);
		}	
	}	
	
	// Collect Groupon
	
	$string = "http://api.groupon.com/v2/deals.xml?client_id=be655eda6ef50e249b931e529fa8fd19cb7ce9c2&division_id=orlando";
	
	$deals_xml = simplexml_load_file("$string");	
	
	foreach($deals_xml->deals->deal as $deal)
	{

		$dealid 	= $deal->id;
		$title1 	= DBin($deal->title);	
		$title2 	= utf8_decode($title1);   
		$title		= str_replace('?',' ',$title2); 
		
		$sidebarImageUrl = DBin($deal->sidebarImageUrl);
		
		/*
		$says_websiteContentH 	= DBin($deal->says->websiteContentHtml);
		$says_websiteContentHt	= utf8_decode($says_websiteContentH);   
		$says_websiteContentHtm	= str_replace('\n','',$says_websiteContentHt);
		$says_websiteContentHtml= str_replace('?','-',$says_websiteContentHtm); 	
		*/
		
		$announcementTitle 		= DBin($deal->announcementTitle);
		$announcementTitle1 	= utf8_decode($announcementTitle);   
		$announcementTitle2		= str_replace('?',' ',$announcementTitle1); 

		$dealUrl 	= DBin($deal->dealUrl);
		$status  	= DBin($deal->status);
		
		$price 		= $deal->options->option->price->amount;
		$value 		= $deal->options->option->value->amount;
		$discount 	= $deal->options->option->discount->amount;
		
		$sql = "INSERT INTO deals (source,dealid,linktype,subtitle,price,value,saving,imageurl,description) VALUES ('groupon','$dealid','$dealUrl','$announcementTitle2','$price','$value','$discount','$sidebarImageUrl','$title');";
		mysql_query($sql) or die(mysql_error());
	}
	
	$says_websiteContentH 	= '';
	$says_websiteContentHt	= '';
	$says_websiteContentHtm	= '';
	$says_websiteContentHtml= '';
	
	//mail("sales@bluecomp.net","Deals EventGrabber","Done");

?>