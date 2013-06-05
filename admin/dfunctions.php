<?php
include("database.php");
include_once("xmlparser.php");

function generatePaging($sql,$link,$pageNum,$max_records_per_page) {

	
	//if ($pageNum  == 1 ) {

			$tmpRes = mysql_query($sql);

			$totalRecs = mysql_num_rows($tmpRes);

			$_SESSION['TOTAL_RECORDS'] = $totalRecs;

	//}

		

		$recStart = ( (int) ($pageNum-1) )* ((int) $max_records_per_page );

		$totalRecs = $_SESSION['TOTAL_RECORDS'];

		

		$totalPages = ceil( ( (int) $totalRecs ) / ( (int) $max_records_per_page ) );



		$pagingString = '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="right" ><tr>';
		
		if ($totalRecs > 0 )
			$pagingString .= '<td align="left" class="bc_label" style="padding:5px">Showing page '. $pageNum.' of '. $totalPages .'</td>';
		else
			$pagingString .= '<td align="left" class="bc_label" style="padding:5px">The list is currently empty at this time.</td>';	

		$pagingString .= '<td align="right" class="bc_label" valign="middle" style="padding:5px">';

		

		

		$pagingStartPage = 1;

		$pagingEndPage = $totalPages ;

		

		if ($pageNum > 6 ) 

			$pagingStartPage = $pageNum - 5;

		

		if ($pageNum < ($totalPages - 5) ) 

			$pagingEndPage = $pageNum + 5;

		

		if ($pageNum > 1 ) {

			$prPage = $pageNum -1;

			$pagingString .= '<a href="'. getBreadcrumb($link) .'&page=1" ><img src="images/doublebackarrow.gif" width="18" height="18" border="0" align="absmiddle"/></a> ';

			$pagingString .= '<a href="'. getBreadcrumb($link) .'&page='. $prPage .'" ><img src="images/backarrow.gif" width="18" height="18" border="0" align="absmiddle"/></a>';

		}



		for($i=$pagingStartPage;$i<=$pagingEndPage;$i++) {

		

			if ($pageNum == $i) {

				$pagingString .= '<span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; color: #D90000">' . $i . '</span>';

			} else {

				$pagingString .= '<a href="'. getBreadcrumb($link) .'&page='.$i.'" class="bc_label">'.$i.'</a>';

			}

			

			if ($i != $pagingEndPage)

				$pagingString .= ' | ';

		}

		if ($pageNum < $totalPages ) {

			$nePage = $pageNum + 1;

			$pagingString .= ' <a href="'. getBreadcrumb($link) .'&page='. $nePage .'" ><img src="images/nextarrow.gif" width="18" height="18" border="0" align="absmiddle"/></a> ';

			$pagingString .= '<a href="'. getBreadcrumb($link) .'&page='. $totalPages .'" ><img src="images/doublenextarrow.gif" width="18" height="18" border="0" align="absmiddle"/></a>';

		}

		

		$pagingString .= '</td></tr></table>';

		

		$sqlLIMIT = " LIMIT ". $recStart . " , " . $max_records_per_page;

		

		if ($totalPages == 1)

		{

			$a['pagingString'] = '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" ><tr><td align="right" class="bc_label" style="padding:10px">Showing page 1 of 1</td></tr></table>';

			$a['limit'] = '';

		}

		else

		{

			$a['pagingString'] = $pagingString;

			$a['limit'] =  $sqlLIMIT;

		}

		

		return $a;

}



function getBreadcrumb($str) {

	//$str = $_SERVER['REQUEST_URI'];

	return $str;

}

function DBin($string) {
	return  trim(htmlspecialchars($string,ENT_QUOTES));
}

function DBout($string) {
	$string = trim($string);
	return html_entity_decode($string);
}

function makeThumbnail($imgName, $srcDir, $thDir, $maxWidth, $maxHeight, $th='re_') {
	
    if ($thDir != "") {

        copy($srcDir.$imgName, $thDir.$imgName);

        $srcFile = $thDir.$imgName;

    }

    else {
        copy($srcDir.$imgName, $srcDir.$th.$imgName);

        $srcFile = $srcDir.$th.$imgName;

    }

 chmod($srcFile,0777);

    

    $ext = strtolower(substr($srcFile,-3));

    $width  = $maxWidth;



    if (file_exists($srcFile) ) {

        $size        = getimagesize($srcFile);

        $IW             = $size[0];

        $IH             = $size[1];

        

        if ($IW < $maxWidth && $IH  < $maxHeight ) {

            $w = $IW;

            $h = $IH;

        }    

        else {

            if ($IW >= $IH) {

                $w = number_format($width, 0, ',', '');

                $h = number_format(($IH / $IW) * $width, 0, ',', '');

            }

            else {

                $ARW         = (float) ($size[0]/($IH-$IW));

                $ARH         = (float) ($size[1]/($IH-$IW));

                $h           = number_format($maxHeight, 0, ',', '');

                $tw             = (float)(($h * $ARW) / $ARH);

                $w           = number_format($tw, 0, ',', '');

                

                if ($w > $maxWidth) {

                    $howMuch   = $w - $maxWidth;

                    $reducePro = $howMuch/$ARW;

                    

                    $h  = $h - ( $ARH * $reducePro );

                    $w  = $w - ( $ARW * $reducePro );

                    $h  = number_format($h, 0, ',', '');

                    $w  = number_format($w, 0, ',', '');

                }

            }

            if ($h > $maxHeight ) {

                $w    = number_format(($w / $h) * $maxHeight, 0, ',', '');

                $h    = number_format($maxHeight, 0, ',', '');

            }

        }

    }

    

    $new_width = $w;

    $new_height = $h;

     

    $image_p = imagecreatetruecolor($new_width, $new_height);    

    if ($ext == 'jpg' || $ext == 'peg' || $ext == 'jpeg') {

        $image = imagecreatefromjpeg($srcFile);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $IW, $IH);

        imagejpeg($image_p, $srcFile, 500);

    }

    else if ($ext == 'png') {

        imagealphablending($image_p, false);
		imagesavealpha($image_p,true);
		$transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
		imagefilledrectangle($image_p, 0, 0, $new_width, $new_height, $transparent);
		$image = imagecreatefrompng($srcFile);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $IW, $IH);
        imagepng($image_p, $srcFile, 9);
    }
    else if ($ext == 'gif') {
        imagealphablending($image_p, false);
		imagesavealpha($image_p,true);
		$transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
		imagefilledrectangle($image_p, 0, 0, $new_width, $new_height, $transparent);
        $image = imagecreatefromgif($srcFile);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $IW, $IH);
        imagegif($image_p, $srcFile, 100);

    }

}


function makeThumbnailFixWidthHeight($imgName, $srcDir, $thDir, $setWidth, $setHeight){
$save = $thDir."".$imgName;
$file = $srcDir."".$imgName;
list($width, $height, $type) = getimagesize($file) ;
if($type==1){
$tn = imagecreatetruecolor($setWidth, $setHeight) ;
$image = imagecreatefromgif($file) ;
imagecopyresampled($tn, $image, 0, 0, 0, 0, $setWidth, $setHeight, $width, $height);
imagegif($tn, $save, 100);
}elseif($type==2){
$tn = imagecreatetruecolor($setWidth, $setHeight) ;
$image = imagecreatefromjpeg($file) ;
imagecopyresampled($tn, $image, 0, 0, 0, 0, $setWidth, $setHeight, $width, $height);
imagejpeg($tn, $save, 100);
}elseif($type==3){
$tn = imagecreatetruecolor($setWidth, $setHeight) ;
$image = imagecreatefrompng($file) ;
imagecopyresampled($tn, $image, 0, 0, 0, 0, $setWidth, $setHeight, $width, $height);
imagepng($tn, $save, 100);
}}


function validateEmail($email) {

	if(preg_match("/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i",$email)){
		return true;
		}
	else{
		return false;
		}

}

function emailAlreadyExists($email,$table,$column) {

	$res = mysql_query("select * from ".$table." where ".$column."='". $email ."'");

	if ( mysql_num_rows($res) > 0 )

		return true;

	else

		return false;

}

function emailAlreadyExistsEdit($email,$table,$column,$id) {

	$res = mysql_query("select * from ".$table." where ".$column."='". $email ."' and id !='".$id."'");

	if ( mysql_num_rows($res) > 0 )

		return true;

	else

		return false;

}

function adminAuthentication($username, $password) {

		$sql = "select * from bc_admin where name='". $username ."' and password='". $password ."'";

		$res = mysql_query($sql);

		if (mysql_num_rows($res) > 0)

		 return true;

		else

		 return false;

}

function sendMail($toMail, $toFrom, $toSubject, $toBody ) {

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";			
	$headers .= 'From: ' . $toFrom . "\r\n";	
	mail( $toMail, $toSubject, $toBody, $headers );

}


function dropDownMenu($tp) {

	$return = '';

	$q = mysql_query("select * from pages where page_type='$tp' order by id asc");

	while( $r = mysql_fetch_assoc($q) ) {

		$return .= '<a href="'.ABSOLUTE_PATH.DBout($r['page_href']).'.html">'.DBout($r['page_title']).'</a>';

	}

	return $return;

}





function resizeImage($originalImage,$toWidth,$toHeight){

     list($width, $height) = getimagesize($originalImage);

    $xscale=$width/$toWidth;

    $yscale=$height/$toHeight;

    

    // Recalculate new size with default ratio

    if ($yscale>$xscale){

        $new_width = round($width * (1/$yscale));

        $new_height = round($height * (1/$yscale));

    }

    else {

        $new_width = round($width * (1/$xscale));

        $new_height = round($height * (1/$xscale));

    }



    // Resize the original image

    $imageResized = imagecreatetruecolor($new_width, $new_height);

    $imageTmp     = imagecreatefromjpeg ($originalImage);

    imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);



    return $imageResized;

}




function make_seo_names($string,$table,$column,$id) {
		
		/*
		$string=strtr($string,"äåéöúûü•µ¿¡¬√ƒ≈∆«»… ÀÃÕŒœ–—“”‘’÷ÿŸ⁄€‹›ﬂ‡·‚„‰ÂÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ¯˘˙˚¸˝ˇ",
    	 				  	  "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
		$string = preg_replace('/[^a-z0-9]/i', '-', $string);
		$string = preg_replace('/[-]+/', '-', $string);
		*/
		
		$string = preg_replace("/[^A-Za-z0-9 -]/", "", $string);
		$string = str_replace(" ","-",$string);
		$string = strtolower($string);
		$string = trim($string,"-");
	    $string = explode("-", $string);
	    $string = array_slice($string, 0, 30);
		$string = array_filter($string, 'strlen');
	    $string = join("-", $string);
		$string = trim($string, "-");


	$qry = "select * from $table where `$column` = '$string'";
		$res = mysql_query($qry);
				if(mysql_num_rows($res)){

					while($row = mysql_fetch_array($res)){
						$duplicate	=	$row[$column];

						$fmk = preg_match_all("#([0-9]+)#is", $duplicate, $matches);
 						$matches = $matches[1];
						if(is_numeric($matches[$fmk - 1])){

						 $addOne = $matches[$fmk-1] + 1;

					 // $string = str_replace("-".$matches[$fmk-1],"", $string);
					//  $string .= "-".$addOne;
						$string .= "-".$addOne;

						}
						else{
							$string .= "-1";
						}

						$string = make_seo_names($string,$table,$column,'');
						}
					}

		return $string;
	}	




function getExtension($str) {

         $i = strrpos($str,".");

         if (!$i) { return ""; }

         $l = strlen($str) - $i;

         $ext = substr($str,$i+1,$l);

         return $ext;

 }



 function deleteImage($id,$tbl,$field){

	$sql = "select `$field` from `$tbl` where `id`='$id'";
	$res = mysql_query($sql);

	if ($res)

		if ($row = mysql_fetch_assoc($res) )
		$del_img = $row[$field];
	
	@unlink("../images/".$del_img);
	@unlink("../images/th_".$del_img);
	@unlink("../images/category/".$del_img);
	@unlink("../images/products/".$del_img);
	@unlink("../images/demo/".$del_img);

	mysql_query("update `$tbl` set `$field`='' where `id`='$id'");

}


function getDropDown($table, $selected="",$col="name")
{
	
	$sql = "select * from $table where $col!='' order by id asc";
	$r = mysql_query($sql);
	
	$dropdown = '';
	
	while($row = mysql_fetch_assoc($r))
	{
		if ($row['id'] == $selected)
			$dropdown .= "<option selected='selected' value=".$row['id'].">".$row[$col]."</option>";
		else
			$dropdown .= "<option  value=".$row['id'].">".$row[$col]."</option>";
	}

	return $dropdown;
}


function removeSpecialChar($string)
{
	$string=strtr($string,"äåéöúûü•µ¿¡¬√ƒ≈∆«»… ÀÃÕŒœ–—“”‘’÷ÿŸ⁄€‹›ﬂ‡·‚„‰ÂÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ¯˘˙˚¸˝ˇ",
    	 				  	  "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
	$string = preg_replace('/[^a-z0-9]/i', '-', $string);
	$string = preg_replace('/[-]+/', '-', $string);
	$string = strtolower($string);
	$string = trim($string,"-");
	$string = explode("-", $string);
	$string = array_slice($string, 0, 30);
	$string = join("-", $string);
	$string = trim($string, "-");
	
	return $string;
}

function getCountries($selected="")
{
	
	$sql = "select * from countries order by cName asc";
	$r = mysql_query($sql);
	
	while($row = mysql_fetch_assoc($r))
	{
		if ($row['cID'] == $selected)
			$dropdown .= '<option selected="selected" value="'.$row['cID'].'">'.$row['cName'].'</option>' . "\n";
		else
			$dropdown .= '<option value="'.$row['cID'].'">'.$row['cName'].'</option>' . "\n";
	}

	return $dropdown;
}

function getStates($selected="")
{
	
	$sql = "select * from usstates order by state asc";
	$r = mysql_query($sql);
	
	while($row = mysql_fetch_assoc($r))
	{
		if ($row['abv'] == $selected)
			$dropdown .= '<option selected="selected" value="'.$row['abv'].'">'. ucwords(strtolower($row['state'])) .'</option>' . "\n";
		else
			$dropdown .= '<option value="'.$row['abv'].'">'. ucwords(strtolower($row['state'])) .'</option>' . "\n";
	}

	return $dropdown;
}

function getCountryName($id)
{
	
	$sql = "select * from countries where cID='". $id ."'";
	$r = mysql_query($sql);
	
	if($row = mysql_fetch_assoc($r))
		$dropdown = $row['cName'];

	return $dropdown;
}

function getStateName($abv)
{
	
	$sql = "select * from usstates where abv='". $abv ."'";
	$r = mysql_query($sql);
	
	if($row = mysql_fetch_assoc($r))
		$dropdown = $row['state'];

	return $dropdown;
}

function getCardTypes()
{
	return array("Visa", "Master","Discover", "Amex");
}

function getMonths()
{
	$monthsArray = array(
					"01" => "Jan",
					"02" => "Feb",
					"03" => "Mar",
					"04" => "Apr",
					"05" => "May",
					"06" => "Jun",
					"07" => "Jul",
					"08" => "Aug",
					"09" => "Sep",
					"10" => "Oct",
					"11" => "Nov",
					"12" => "Dec"
				);
		return $monthsArray;
}

function getYears()
{
	$years = array();
	for ($i=2010;$i<=2025;$i++) {
		$k = substr($i,2);
		$years[$k] = $i;
	}
	
	return $years;
}


function attribValue($table, $return, $where) {
	$q = mysql_query("SELECT $return FROM $table $where") or die(mysql_error());
	
	if ( mysql_num_rows($q) > 0 ) {
		$r = mysql_fetch_assoc($q);
		return DBout($r[$return]);
	}
	
	return '';	
}




function select_sname($selname,$init="select category type",$def=NULL,$width=150)
 	{
	echo"<select name='$selname' style='width:".$width."px;'/>";
		//echo ($init != '')?'<option value="">'.$init.'</option><br>':'<br>';
					$sql = "select * from categories order by id";
					$res = mysql_query($sql) or die("Unable to fetch records beacuse : ".mysql_error());
					while($row = mysql_fetch_array($res))
					{
					$id = $row['id'];
					$name = $row['name'];
					$selected = false;
					if($id==$def)
					$selected = true;											
                  echo '<option value='.$id.' '.(($selected)?"selected":'').'>'.$name.'</option>';
					}
					echo '</select>';
   
	} 
	
	function parent_name($id){
		$sql = "select * from categories where id = $id";
		$res=mysql_query($sql);
			while($row=mysql_fetch_array($res))
			{
				return $row['name'];
			}
	}
	

	function getGeoLocation($address){
		// Address format jaranwala+road,+Faisalabad,+Punjab
		$url 	= "http://maps.googleapis.com/maps/api/geocode/xml?address=". urldecode($address) ."&sensor=true" ;
		$xml 	= simplexml_load_file($url);		
		$latlng = array();
		$latlng['lat'] = $xml->result->geometry->location->lat;
		$latlng['lng'] = $xml->result->geometry->location->lng;
		return $latlng;
	}

	function getLocationImage($lat,$lng){
		$h = 200;
		$w = 300;
	
		$url 	= "http://cbk0.google.com/cbk?output=xml&ll=".$lat."," . $lng ;
		
		$nom 	= get_url_contents($url);
			
		$xmlparser	= new xmlparser();
		$data		= $xmlparser->GetXMLTree($nom);
		
		$panoid = $data["PANORAMA"][0]["DATA_PROPERTIES"][0]["ATTRIBUTES"]["PANO_ID"];
		
		if ( $panoid != '' )
			$src = 'http://cbk0.google.com/cbk?output=thumbnail&zoom=3&x=5&y=2&w='. $w .'&h='. $h .'&panoid=' . $panoid ;
		else
			$src = ABSOLUTE_PATH .'images/no_venue_image1.png';
		
		return $src;	
	}

	function getExistingVenueId($name){
		$sql  = "SELECT id FROM venues WHERE venue_name = '". $name ."'";
		$res  = mysql_query($sql);
		if ( mysql_num_rows($res) > 0 ) {
			if ( $row = mysql_fetch_assoc($res) ) {
				return $row['id'];
			}	
		}		
		return 0;
	}

	function getSingleColumn($column,$sql){
		$res = mysql_query($sql);
		if ( mysql_num_rows($res) > 0 ) {
			if ( $row = mysql_fetch_assoc($res) )
				return $row[$column];
		} else
			return '';		
	}

	function getRewriteString($str) {
		$string = strtolower(htmlentities($str));
		$string = preg_replace("/&(.)(uml);/", "$1e", $string);
		$string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
		$string = preg_replace("/([^a-z0-9]+)/", "-", html_entity_decode($string));
		$string = trim($string, "-");
		return $string;
	}

	function matchVenueLatLng($lat,$lng)
	{
		$lat  = substr($lat,0,7);
		$lng  = substr($lng,0,8);
		
		$sql1 = "select * from venues where substring(venue_lat,1,7)='". $lat ."' AND substring(venue_lng,1,8)='". $lng ."'
					ORDER BY CASE 
					WHEN venue_type != '' AND venue_address != '' AND venue_city != '' AND venue_zip != '' AND venue_state != '' THEN 0 
					WHEN venue_address != '' AND venue_city != '' AND venue_zip != '' AND venue_state != '' THEN 1 
					WHEN venue_address != '' AND venue_city != '' AND venue_state != '' AND venue_zip = '' THEN 2 
					WHEN venue_address != '' AND venue_city != '' AND venue_state = '' AND venue_zip = '' THEN 3 
					WHEN venue_address != '' AND venue_city = '' AND venue_state = '' AND venue_zip = '' THEN 4 
					WHEN venue_address = ''  THEN 5 ELSE 6 END";
		$res1 = mysql_query($sql1);
		if ( $row = mysql_fetch_assoc($res1) ) 
			return $row['id'];
			
		return 0;
	}

	function isValidURL($url){
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
		//return preg_match("#^http://www\.[a-z0-9-_.]+\.[a-z]{2,4}$#i",$url);
	}


	function getUserName($user_id){
		if($user_id=='-1'){
			return "Admin";
		}
		else{
			$res = mysql_query("select * from `users` where `id`='$user_id'");
			while($row = mysql_fetch_array($res)){
				return $row['firstname'];
			}
		}
	}

	function getProductName($product_id){
		$qry = "select * from `products` where `id`='$product_id'";
		$res = mysql_query($qry);
		while($row = mysql_fetch_array($res)){
			return $row['name'];
		}
	}

	function getTicketTitle($ticket_id){
	$qry = "select * from `event_ticket_price` where `id`='$ticket_id'";
	$res = mysql_query($qry);
		while($row = mysql_fetch_array($res)){
			return $row['title'];
		}
	}

	function getEventName($event_id){
	$qry = "select * from `events` where `id`='$event_id'";
	$res = mysql_query($qry);
		while($row = mysql_fetch_array($res)){
			return $row['event_name'];
		}
	}

	function getDatesRange($date1, $date2){
	  if ($date1<$date2){ 
		$dates_range[]=$date1; 
		$date1=strtotime($date1); 
		$date2=strtotime($date2); 
		while ($date1!=$date2){ 
		  $date1=mktime(0, 0, 0, date("m", $date1), date("d", $date1)+1, date("Y", $date1)); 
		  $dates_range[]=date('Y-m-d', $date1); 
		}
		return $dates_range;
		}
	}


	function getMaxValue($table,$colum){
		$maxRes = mysql_query("SELECT MAX($colum) as $colum from `$table`");
		while($maxRow = mysql_fetch_array($maxRes)){
			return $maxRow[$colum];
		}
	}


?>

