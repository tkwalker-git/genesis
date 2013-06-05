<?php
	
	include_once("../admin/database.php");
	
	// Collect LivingSocial Deals
	
	$string = "http://deals.livingsocial.com/cities.atom?offer_id=4&aff_id=3481";
	
	$deals_xml = simplexml_load_file("$string");	
	
	mysql_query("truncate live");
	
	foreach ($deals_xml->entry as $ent) 
	{ 
		$liveid  		= DBin($ent->id);
		$linktype 		= DBin($ent->link->attributes()->href);
		$subtitle 		= DBin($ent->subtitle);
		$price	 		= utf8_decode($ent->price);
		$value 			= utf8_decode($ent->value);
		
		$savings 		= DBin($ent->savings);	
		$image   		= DBin($ent->image_url);
		
		$description1 	= DBin($ent->description);
		$description	= utf8_decode($description1);   
		//$description 	= str_replace('?','&#128;',$description2); 
		
		$sql = "INSERT INTO live (liveid,linktype,subtitle,price,value,saving,imageurl,description) VALUES ('$liveid','$linktype','$subtitle','$price','$value','$savings','$image','$description')";
		$res = mysql_query($sql);
	}	
	
	// Collect Groupon
	
	mysql_query("truncate deal");
	
	$string = "http://api.groupon.com/v2/deals.xml?client_id=be655eda6ef50e249b931e529fa8fd19cb7ce9c2&division_id=orlando";
	$deals_xml = simplexml_load_file("$string");	
	
	
	
	foreach($deals_xml->deals->deal as $deal)
	{
		$dealid = DBin($deal->id);
		$title1 	= DBin($deal->title);	
		$title2 = utf8_decode($title1);   
		$title= str_replace('?',' ',$title2); 
		$division_timezone = DBin($deal->division->timezone);
		$division_lang 	= DBin($deal->division->lat);
		$division_lang 	= DBin($deal->division->lng);
		$division_name 	= DBin($deal->division->name);
		$division_id 	= DBin($deal->division->id);
		$division_timezoneOff_S = DBin($deal->division->timezoneOffsetInSeconds);
		$areas  = DBin($deal->areas);
		$placementPriority 	= DBin($deal->placementPriority);
		$sidebarImageUrl = DBin($deal->sidebarImageUrl);
		$smallImageUrl = DBin($deal->smallImageUrl);
		$mediumImageUrl	= DBin($deal->mediumImageUrl);
		$largeImageUrl = DBin($deal->largeImageUrl);
		$says_emailConte = DBin($deal->says->emailContent);	
		$says_emailConten= utf8_decode($says_emailConte);   
		$says_emailContent= str_replace('?',' ',$says_emailConten); 
		$says_websiteContentH 	= DBin($deal->says->websiteContentHtml);
		$says_websiteContentHt= utf8_decode($says_websiteContentH);   
		$says_websiteContentHtm= str_replace('\n','',$says_websiteContentHt);
		$says_websiteContentHtml= str_replace('?','-',$says_websiteContentHtm); 	
		$says_emailContentHt = DBin($deal->says->emailContentHtml);
		$says_emailContentHtm= utf8_decode(	$says_emailContentHt);   
		$says_emailContentHtml= str_replace('?',' ',$says_emailContentHtm); 
		$says_title = DBin($deal->says->title);
		$says_id 	= DBin($deal->says->id); 
		$says_websiteCont = DBin($deal->says->websiteContent);
		$says_websiteConte = utf8_decode($says_websiteCont);   
		$says_websiteConten= str_replace('\n','',$says_websiteConte); 
		$says_websiteContent1= str_replace('*','',$says_websiteConten); 
		$says_websiteContent= str_replace('?','',$says_websiteContent1); 
		$announcementTitle 	= DBin($deal->announcementTitle);
		$tags_tag1 	= DBin(@$deal->tags->tag[0]->name);
		$tags_tag2 	= DBin(@$deal->tags->tag[1]->name);
		$dealUrl = DBin($deal->dealUrl);
		$status  = DBin($deal->status);
		$isTipped  = DBin($deal->isTipped);
		$tippingPoint = DBin($deal->tippingPoint);
		$isSoldOut 	= DBin($deal->isSoldOut);
		$soldQuantity  = DBin($deal->soldQuantity);
		$shipping_Add_Req	= DBin($deal->shippingAddressRequired);
		$option_id 	= DBin($deal->options->option->id);
		$option_title = DBin($deal->options->option->title);
		$option_soldQty  = DBin($deal->options->option->soldQuantity);
		$option_isSoldOut  = DBin($deal->options->option->isSoldOut);
		$option_price_amount 	= DBin($deal ->options->option->price->amount);
		$option_price_currencyCode 	= DBin($deal->options->option->price->currencyCode);
		$option_price_formatted = DBin($deal->options->option->price->formattedAmount);
		$option_value_amount = DBin($deal->options->option->value->amount);
		$option_value_currencyCode  = DBin($deal->options->option->value->currencyCode);
		$option_value_formatted = DBin($deal->options->option->value->formattedAmount);
		$option_disc_amount = DBin($deal->options->option->discount->amount);
		$option_disc_currencyCode = $deal->options->option->discount->currencyCode;
		$option_disc_formatted 	= $deal->options->option->discount->formattedAmount;
		$option_discPercent 	= DBin($deal->options->option->discountPercent);
		$option_isLimitedQty	= DBin($deal->options->option->isLimitedQuantity);
		$option_initialQty 	= DBin($deal->options->option->initialQuantity);
		$option_remainingQty  = DBin($deal->options->option->remainingQuantity); 
		$option_minPurchaseQty 	= DBin($deal->options->option->minimumPurchaseQuantity);
		$option_maxPurchaseQty 	= DBin($deal->options->option->maximumPurchaseQuantity);
		$option_detail_desc	 = DBin($deal->options->option->details->detail->description);
		$redempLoc_lng 	= DBin($deal->options->option->redemptionLocations->redemptionLocation->lng);
		 $redempLoc_city = DBin($deal->options->option->redemptionLocations->redemptionLocation->city);
		$redempLoc_streetAddress1  = DBin($deal->options->option->redemptionLocations->redemptionLocation->streetAddress1);
		$redempLoc_state  = DBin($deal->options->option->redemptionLocations->redemptionLocation->state);
		$redempLoc_postalCode  = DBin($deal->options->option->redemptionLocations->redemptionLocation->postalCode);
		$redempLoc_streetAddress2 	= DBin($deal->options->option->redemptionLocations->redemptionLocation->streetAddress2);
		$redempLoc_name  = DBin($deal->options->option->redemptionLocations->redemptionLocation->name);
		$redempLoc_lat 	= DBin($deal->options->option->redemptionLocations->redemptionLocation->lat);
		$option_externalUrl = DBin($deal->options->option->externalUrl);
		$option_customFields  = DBin($deal->options->option->customFields);
		$option_buyUrl 	= DBin($deal->options->option->buyUrl);
		$merchant_websiteUrl 	= DBin($deal->merchant->websiteUrl);
		$merchant_name 	= DBin($deal->merchant->name);
		$merchant_id 	= DBin($deal->merchant->id);	
		
		$highlightsHt = DBin($deal->highlightsHtml);
		$highlightsHtm= utf8_decode($highlightsHt);   
		$highlightsHtml= str_replace('\n','',$highlightsHtm);
			
		$pitchH = DBin($deal->pitchHtml);
		$pitchHt = utf8_decode($pitchH);   
		$pitchHtm = str_replace('\n','',$pitchHt);
		$pitchHtml = str_replace('?','',$pitchHtm);
		
		$textAd_headline 	= DBin($deal->textAd->headline);
		$textAd_line1  = DBin($deal->textAd->line1);
		$textAd_line2  = DBin($deal->textAd->headline2);
		$type = DBin($deal->type);
		$startAt = DBin($deal->startAt);
		$endAt 	= DBin($deal->endAt);
		
		 $query1 = "insert into deal (dealid,title,division_lang,division_timezone,division_name,division_id,division_lat,division_timezoneOff_S,areas,placementPriority,sidebarImageUrl,smallImageUrl,mediumImageUrl,largeImageUrl,says_emailContent,says_websiteContentHtml,says_emailContentHtml,says_title,says_id,says_websiteContent,announcementTitle,tags_tag1,tags_tag2,dealUrl,status,isTipped,tippingPoint,isSoldOut,soldQuantity,shippingAdd_Req,option_id,option_title,option_soldQty,option_isSoldOut,option_price_amount,option_price_currencyCode,option_price_formatted,option_value_amount,option_value_currencyCode,option_value_formatted,option_disc_amount,option_disc_currencyCode,option_disc_formatted,option_discPercent,option_isLimitedQty,option_initialQty,option_remainingQty,option_minimumPurchaseQty,option_maximumPurchaseQty,option_detail_desc,redempLoc_lng,redempLoc_city,redempLoc_streetAddress1,redempLoc_state,redempLoc_postalCode,redempLoc_streetAddress2,redempLoc_name,redempLoc_lat,option_externalUrl,option_customFields,option_buyUrl,merchant_websiteUrl,merchant_name,merchant_id,highlightsHtml,pitchHtml,textAd_headline,textAd_line1,textAd_line2,type,startAt,endAt)

values('$dealid','$title','$division_lang','$division_timezone','$division_name','$division_id','$division_lat','$division_timezoneOff_S','$areas','$placementPriority','$sidebarImageUrl','$smallImageUrl','$mediumImageUrl','$largeImageUrl','$says_emailContent','$says_websiteContentHtml','$says_emailContentHtml','$says_title','$says_id','$says_websiteContent','$announcementTitle','$tags_tag1','$tags_tag2','$dealUrl','$status','$isTipped','$tippingPoint','$isSoldOut','$soldQuantity','$shipping_Add_Req','$option_id','$option_title','$option_soldQty','$option_isSoldOut','$option_price_amount','$option_price_currencyCode','$option_price_formatted','$option_value_amount','$option_value_currencyCode','$option_value_formatted','$option_disc_amount','$option_disc_currencyCode','$option_disc_formatted','$option_discPercent','$option_isLimitedQty','$option_initialQty','$option_remainingQty','$option_minPurchaseQty','$option_maxPurchaseQty','$option_detail_desc','$redempLoc_lng','$redempLoc_city','$redempLoc_streetAddress1','$redempLoc_state','$redempLoc_postalCode','$redempLoc_streetAddress2','$redempLoc_name','$redempLoc_lat','$option_externalUrl','$option_customFields','$option_buyUrl','$merchant_websiteUrl','$merchant_name','$merchant_id','$highlightsHtml','$pitchHtml','$textAd_headline','$textAd_line1','$textAd_line2','$type','$startAt','$endAt')"; 
		$res = mysql_query($query1) ;
	}
	
	

?>