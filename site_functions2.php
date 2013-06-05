<?php
include("admin/database.php");
require_once('qrcode/qrlib.php');
function showCatList($catId){

	$query = "select * from categories where id = '$catId'"; 
	$res = mysql_query($query);
	if ($r = mysql_fetch_assoc($res)){
			$cat_seo_name	= DBout($r['seo_name']);
			echo '<span><a href="'. ABSOLUTE_PATH . 'category/' . $cat_seo_name .'.html" class="home_cat_title">'.$r['name'].'</a></span>';
			 
			$query2 = "select * from sub_categories where categoryid = '$catId' ORDER BY (select count(*) from events where subcategory_id=sub_categories.id group by subcategory_id) DESC LIMIT 3";		
			$res2 = mysql_query($query2);
				echo '<ul>';
					
				while($r2 = mysql_fetch_assoc($res2)){
						echo '<li><a href="category-all/'.$r['seo_name']."/".$r2[seo_name].'.html'.'">'.$r2['name'].'</a></li>';
				}
				echo '<li><a href="'. ABSOLUTE_PATH . 'category/' . $cat_seo_name .'.html">See All</a></li>';
	}
	
	echo "</ul>";
}

function ShowCatMeta($category, $sub_category){
	
	$pcat_meta_q = "select * from categories where seo_name = '$category'";
		$subcat_meta_q 	= "select * from sub_categories where seo_name = '$sub_category'";
		$pcat_meta_res 	= mysql_query($pcat_meta_q) or die("Error");
		$pcat_meta_row 	= mysql_fetch_assoc($pcat_meta_res);
		$pcat_title 	= DBout($pcat_meta_row['meta_title']);
		$pcat_meta_desc = DBout($pcat_meta_row['meta_desc']);
		$pcat_meta_keywords = DBout($pcat_meta_row['meta_keywords']);
		
		$subcat_meta_row = mysql_fetch_assoc($subcat_meta_res);
		$subcat_meta_res = mysql_query($subcat_meta_q) or die("Error");
		$subcat_meta_row = mysql_fetch_assoc($subcat_meta_res);
		
		$subcat_title 			= DBout($subcat_meta_row['meta_title']);
		$subcat_meta_desc 		= DBout($subcat_meta_row['meta_desc']);
		$subcat_meta_keywords 	= DBout($subcat_meta_row['meta_keywords']); 
		
		$cat_title = $pcat_title." - ".$subcat_title;
		$cat_meta_desc = $subcat_meta_desc." ".$subcat_meta_desc;
		$cat_meta_keywords = $pcat_meta_keywords.", ".$subcat_meta_keywords;
}

function validateUrl($url) {

	if(preg_match("/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/",$url))

		return true;

	else	

		return false;

}

function getEventImage($image,$source="EventFull",$small=0)
{
	if ( $small ) {
		$h = 2000;
		$w = 127;
	} else {
		$h = 2000;
		$w = 163;
	}
	
	if ( substr($image,0,7) != 'http://' && substr($image,0,8) != 'https://' ) {
	
		if ( $image != '' && file_exists(DOC_ROOT . 'event_images/th_' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'event_images/th_' . $image,$w,$h );
			$img = '<img align="left" '. $img .' />';	
		} else
			$img = '<img src="' . IMAGE_PATH . 'imgg.png" align="left" width="'. $w .'"/>';	
	} else {
		if ( $source != "EventFull")
			$img_params 	= returnImage($image,$w,$h);
		else {
			if ( strtolower(substr($image,-4,4)) != '.gif')
				$image = str_replace("/medium/","/large/",$image);
			$img_params		= 'src="'. $image .'" width="'. $w .'" height="' . $h .'"';	
		}	
		$img 			= '<img align="left" '. $img_params .' />';	
	}	
	
	return $img;
}

function returnImage($image,$mxWidth=163,$mxHeight=200)
{
	$image = str_replace("%20"," ",$image);

	if ($mxWidth > 0 ) {
		list($width, $height, $type, $attr) = @getimagesize($image);
		list($width, $height) = getPropSize($width, $height, $mxWidth,$mxHeight );
	} else {
		list($width, $height, $type, $attr) = @getimagesize($image);
	}
		
	$ret = 'src="'. $image .'" width="'. $width .'" height="'. $height .'"';
	
	return $ret;
}	

function getPropSize($actualWidth,$actualHeight,$resultWidthidth,$resultHeighteight)
{
	if ($actualWidth < $resultWidthidth && $actualHeight  <	$resultHeighteight ) {
		$resultWidth = $actualWidth;
		$resultHeight = $actualHeight;
	} else {
		if ($actualWidth >= $actualHeight) {
			$resultWidth        = number_format($resultWidthidth, 0, ',', '');
			$resultHeight       = number_format(($actualHeight / $actualWidth) * $resultWidthidth, 0, ',', '');
		} else {
			$resultHeight       = number_format($resultHeighteight, 0, ',', '');
			$resultWidth        = number_format(($actualWidth / $actualHeight) * $resultHeighteight, 0, ',', '');
		}
		
		if ($actualWidth > $resultWidthidth) {
			$resultWidth        = number_format($resultWidthidth, 0, ',', '');
			$resultHeight       = number_format(($actualHeight / $actualWidth) * $resultWidthidth, 0, ',', '');
		}	
		
	}
	
	return array($resultWidth,$resultHeight);
}

function breakStringIntoMaxChar($string,$max)
{
	if (strlen($string) > $max)	{
		$str 	= substr($string,0, $max);
		$str1	= strrev($str);
		$st		= strpos($str1," ");
		$str1	= substr($str1,$st);
		$str	= strrev($str1);
		$str = $str . '...';
	} else {
		$str = $string;
	}	
	return $str;
}

function getEventDates($event_id)
{
	$sql = "select * from event_dates where event_id='". $event_id ."' Order BY event_date ASC";
	$res = mysql_query($sql);
	$dates = array();
	while ( $row = mysql_fetch_assoc($res) ) 
		$dates[] = $row['event_date'];
	
	$tot = count($dates);
	
	$sdate = $dates[0];
	$edate = $dates[$tot-1];
	
	if ( $sdate != '' )
		$date1 = date("M d, Y",strtotime($sdate));
	
	if ( $edate != $sdate )	{
		if ( $edate != '' )
			$date2 = ' - ' .date("l F d, Y",strtotime($edate));	
	} else {
		$date2 = '';
	}
		
	return $date1 . $date2;	
	
}

function getEventStartDates($event_id)
{

	//$sql = "select * from event_dates where event_id='". $event_id ."' AND event_date > DATE_SUB(CURDATE(),INTERVAL 1 DAY) ORDER by event_date ASC";
	$sql = "select * from event_dates where event_id='". $event_id ."' AND event_date > DATE_SUB(CURDATE(),INTERVAL 1 DAY)  ORDER by event_date ASC";
	$res = mysql_query($sql);
	$dates = array();
	while ( $row = mysql_fetch_assoc($res) ) 
		$dates[] = $row['event_date'];
	
	$tot = count($dates);
	
	$sdate = $dates[0];
	$edate = $dates[$tot-1];
	
	if ( $sdate != '' )
		$date1 = date("M d, Y",strtotime($sdate));
	
	if ( $date1 == '' )
		$date1 = '&nbsp;';
	
	return $date1;	
	
}


function getEventLocations($event_id)
{
	$sql = "select * from venue_events where event_id='". $event_id ."'";
	$res = mysql_query($sql);
	if ( $rows = mysql_fetch_assoc($res) ) {
		$sql1 = "select * from venues where id='". $rows['venue_id'] ."'";
		$res1 = mysql_query($sql1);
		if ( $row = mysql_fetch_assoc($res1) ) {
			
			//$location[] 	= $row['venue_name'];
			$location[]		= $row['venue_name'];
			$location[] 	= $row['venue_address'];
			$location[] 	= $row['venue_city'] . ' ' . $row['venue_state'] . ' , ' . $row['venue_zip'] . '<br>';
				
		}
	}
	
	if ( is_array($location) ) 
		$locations = implode("<br>",$location);
	else 
		$locations = 'Venue not yet decided.';	
	
	$ret[0] = $locations;
	$ret[1] = $row;
	
	return $ret;	
}

function getReTweetBtn($url="")
{
	if ($url == "" ) 
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	
	?>
<script type="text/javascript">
			tweetmeme_url = '<?php echo $url;?>';
			tweetmeme_style = 'compact';
			
			</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
<?php		

}

function getFShareBtn($url="")
{
	if ($url == "" ) 
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	?>
<script>function fbs_click() {u=location.href;t=document.title;window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent('<?php echo $url;?>')+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;}</script>

<a href="http://www.facebook.com/share.php?u=<?php echo $url;?>" onclick="return fbs_click()" target="_blank"><img src="<?php echo ABSOLUTE_PATH;?>images/fshare.png" alt="Share on Facebook" align="top"/></a>
<?php	
}


function getCurrentWeek()
{

	$dayofweek 	= date("N");
	
	$start		= $dayofweek - 1;
	$end		= 7- $dayofweek ;
	
	$start 	= strtotime("now") - $start * 86400;
	$end 	= strtotime("now") + $end * 86400;
	
	$ret['start'] = $start;
	$ret['end']   = $end;
	
	return $ret;
}


function getEventURL($event_id)
{
	$category_id 			= attribValue('events', 'category_id', "where id='$event_id'");
	$scategory_id 			= attribValue('events', 'subcategory_id', "where id='$event_id'");
	
	$category_seo_name 		= attribValue("categories","seo_name","where id=" . $category_id);
	$subcategory_seo_name 	= attribValue("sub_categories","seo_name","where id=" . $scategory_id);
	$event_seo_name 		= attribValue('events', 'seo_name', "where id='$event_id'");
	
	if ( $category_seo_name == "" )
		$category_seo_name = 'uncategorized';
		
	if ( $subcategory_seo_name == "" )
		$subcategory_seo_name = 'uncategorized';
	
	$event_url	= ABSOLUTE_PATH . 'category/' . $category_seo_name . '/' . $subcategory_seo_name . '/' . $event_seo_name . '.html';
	
	return $event_url;
}

function getAddToWallButton($event_id,$small=0)
{
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$already = attribValue('event_wall', 'id', "where event_id='$event_id' and userid='$member_id'");
	if ( $small == 0 ) {
		if ( $already > 0 ) 
			echo  '<img src="'. ABSOLUTE_PATH .'images/added_to_event_btn.png" />';
		else
			echo '<a href="javascript:void(0)" onclick="addToEventWall(\''. ABSOLUTE_PATH .'\','. $event_id .')"><img src="'. ABSOLUTE_PATH .'images/add_event23.png" /></a>';
	} else {
		if ( $already > 0 ) 
			echo  '<img src="'. ABSOLUTE_PATH .'images/added_to_event_btn_small2.png" />';
		else
			echo '<a href="javascript:void(0)" onclick="addToEventWall(\''. ABSOLUTE_PATH .'\','. $event_id .')"><img src="'. ABSOLUTE_PATH .'images/add_to_event_btn_small2.png" /></a>';
	}		
}

function getEventReviewMember($event_id)
{
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$sql = "select comment from comment where key_id=$event_id and c_type='event' and by_user=$member_id";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$comment = $row['comment'];
	echo $comment;
}

function getEventRatingMember($event_id)
{
	/*$sql = "select sum(rating) as agg from comment where key_id=$event_id and c_type='event'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$agg = $row['agg'];
	*/
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$sql = "select rating from comment where key_id=$event_id and c_type='event' and by_user=$member_id";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$rating = $row['rating'];
	
	if ( $rating > 0 ) {
		$rating2 = ceil($rating/2);
		for($i=1; $i<=$rating2;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'on_star.png" width="15" height="14" border="0" />';	
		for($i=$rating2+1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
		
	} else {
		for($i=1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
	}
}

function getEventRatingAggregate($event_id)
{
	$sql = "select sum(rating) as agg from comment where key_id=$event_id and c_type='event'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$agg = (int)$row['agg'];
	
	$sql = "select id from comment where key_id=$event_id and c_type='event' ";
	$res = mysql_query($sql);
	$tot = mysql_num_rows($res);
	
	if ( $tot > 0 )
		$rating = ceil($agg/$tot);
	else
		$rating = 0;	
	
	?>
<a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $event_id;?>,'event')">
<?php
	
	if ( $rating > 0 ) {
		$rating2 = ceil($rating/2);
		for($i=1; $i<=$rating2;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'on_star.png" width="15" height="14" border="0" />';	
		for($i=$rating2+1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
		
	} else {
		for($i=1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
	}
	?>
</a>
<?php
}



function getRatingAggregate($id,$type)
{
	$sql = "select sum(rating) as agg from comment where key_id=$id and c_type='$type'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$agg = (int)$row['agg'];
	
	$sql = "select id from comment where key_id=$id and c_type='$type' ";
	$res = mysql_query($sql);
	$tot = mysql_num_rows($res);
	
	if ( $tot > 0 )
		$rating = ceil($agg/$tot);
	else
		$rating = 0;	
		
		
		
	
	?>
<a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>','<?php echo $id;?>','<?php echo $type; ?>')">
<?php
	
	if ( $rating > 0 ) {
		$rating2 = ceil($rating/2);
		for($i=1; $i<=$rating2;$i++)
			echo '<img style="padding:0 1px" src="'.IMAGE_PATH.'on_star.png" width="15" height="14" border="0" />';	
		for($i=$rating2+1; $i<=5;$i++)
			echo '<img style="padding:0 1px" src="'.IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
		
	} else {
		for($i=1; $i<=5;$i++)
			echo '<img style="padding:0 1px" src="'.IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
	}
	?>
</a>
<?php
}



function getReviewStarRating($venue_id,$memb,$c_type)
{
	$sql = "select rating from comment where key_id=$venue_id and c_type='". $c_type ."' and by_user=$memb";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$rating = $row['rating'];
	?>
<a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $venue_id;?>,'<?php echo $c_type;?>')">
<?php
	if ( $rating > 0 ) {
		$rating2 = ceil($rating/2);
		for($i=1; $i<=$rating2;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'on_star.png" width="15" height="14" border="0" />';	
		for($i=$rating2+1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
		
	} else {
		for($i=1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
	}
	?>
</a>
<?php
}

function getReviewStarsOnRating($rating)
{
	if ( $rating > 0 ) {
		$rating2 = ceil($rating/2);
		for($i=1; $i<=$rating2;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'on_star.png" width="15" height="14" border="0" />';	
		for($i=$rating2+1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
		
	} else {
		for($i=1; $i<=5;$i++)
			echo '<img style="margin:0px 3px;" src="'. IMAGE_PATH.'off_star.png" width="15" height="14" border="0" />';	
	}
}


function returnFormatedEvents($res1,$tot_events,$category_id,$sub_cat_id,$view='',$showNextPrev=1)
{
?>
<span id="sbc<?php echo $sub_cat_id ;?>">
<?php 
	  	
	if ( $tot_events > 0 ) {
		$i=0;
		while ($rows1 = mysql_fetch_assoc($res1) )
	  	{
			$i++;
			if ( $view != 'all' ) {	
				if ( $i > 4 )
					break;
			}	
			$event_name 	= breakStringIntoMaxChar(DBout($rows1['event_name']),25);
			$full_name		= DBout($rows1['event_name']);
			$event_date 	= getEventStartDates($rows1['id']);
			$source			= $rows1['event_source'];
			$event_image	= getEventImage($rows1['event_image'],$source);
			$event_url		= getEventURL($rows1['id']);
			
	  ?>
<table style="width:233px; float:left;">
  <tr>
    <td width="233"><div class="txt2"><a href="<?php echo $event_url;?>" title="<?php echo $full_name;?>"><?php echo $event_name;?></a></div>
      <div class="date"><?php echo $event_date;?></div>
      <div class="imag" >
        <div style="width:163px; height:200px; position:relative;  z-index:999;">
          <?php 
				if ( $rows1['featured'] == 1 ) 
					$corner_image = 'featured_event.png';
				else if ( $rows1['free_event'] == 1 ) 
					$corner_image = 'free_event.png';
				else
					$corner_image = '';
				
				if ( $corner_image != '' ) {		
			?>
          <div style="position:absolute; bottom:-3px; right:-3px; z-index:999; width:80px; height:75px"> <img src="<?php echo ABSOLUTE_PATH;?>images/<?php echo $corner_image;?>" /> </div>
          <?php		
				}
			?>
          <a style="overflow:hidden; width:163px; height:200px; display:block; background-color:#FFFFFF" href="<?php echo $event_url;?>" alt="<?php echo $full_name;?>" title="<?php echo $full_name;?>"><?php echo $event_image;?></a> </div>
      </div>
      <div class="add_event2">
        <!-- <a href="<?php echo $event_url;?>"><img src="<?php echo IMAGE_PATH;?>add_event22.png" /></a> -->
        <?php getAddToWallButton($rows1['id']); ?>
      </div></td>
  </tr>
  <tr>
    <td align="center"><?php getEventRatingAggregate($rows1['id']);?>
    </td>
  </tr>
</table>
<?php 
	  		}
			
		if ( $tot_events > 4 ) {
			if ( $view != 'all' ) {		
		?>
<table width="100%" cellpadding="0" cellspacing="0" style="float:none; clear:both">
  <tr>
    <td width="823"><div class="prev"> <a href="javascript:void(0)"> <img src="<?php echo IMAGE_PATH;?>prev_disabled.png" /> </a></div></td>
    <td width="84"><?php if ( $showNextPrev ) { ?>
      <div class="next"><a href="javascript:loadNextSubCategory('<?php echo ABSOLUTE_PATH;?>','<?php echo $category_id;?>','<?php echo $sub_cat_id;?>','next',1)"><img src="<?php echo IMAGE_PATH;?>next.png" /></a></div>
      <?php } ?>
    </td>
  </tr>
</table>
<?php } } ?>
</span>
<?php } else {	?>
<table width="100%">
  <tr>
    <td height="100" ><div class="txt2" style="color:#990000!important; text-align:center; width:80%!important"> No event found in this category. </div></td>
  </tr>
</table>
<?php
	}
}  // end returnFormatedEvents


function getMemberPrefrences()
{
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$rsc = mysql_query("select * from categories ");
	while ( $rowc = mysql_fetch_assoc($rsc) ) {
		$cid		= $rowc['id'];
		$attr_name 	= DBout($rowc['name']);
		$attr_name	= ucwords($attr_name);
		$val = attribValue("member_prefrences","selection","where prefrence_type=" . $cid . " and member_id=" . $member_id );
		
		if ( $val == 'O' )
			$value = 'Often';
		else if ( $val == 'S' )
			$value = 'Sometimes';	
		else if ( $val == 'N' )
			$value = 'Never';		
		
		$pref[] = array($attr_name,$value);
		
	}
	
	return $pref;	
}


function productImagesGallery($product_id){?>
<?php
		$qry = "select * from `products_images` where `product_id`='$product_id'";
		$res = mysql_query($qry);
		if(mysql_num_rows($res)){
		?>
<div class="recommendedBlock" style="border: 1px solid #C1C1C1; margin-top:10px;">
  <div class="recommended_heading heading_dark_16"> Product Images </div>
  <ul id="mycarousel" class="jcarousel-skin-tango">
    <?php
		while($row = mysql_fetch_array($res)){?>
    <li><a href="<?php echo PRODUCT_IMAGE_PATH.''.$row['image']; ?>" class="fancybox"><img src="<?php echo PRODUCT_IMAGE_PATH.'ico_'.$row['image']; ?>" width="75" height="75" /></a></li>
    <?php
		}
		?>
  </ul>
</div>
<?php
}}


function getReviewsList($key_id,$c_type,$cg_reviews="")
{
	
	if ( is_array($cg_reviews) && count($cg_reviews) > 0 ) 
		$cgr = 1;
	

	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
?>
<div class="recommendedBlock" style="border: 1px solid #C1C1C1; margin-top:10px; width:854px">
  <div class="recommended_heading heading_dark_16"> <?php echo ucwords($c_type);?> Reviews <a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $key_id;?>,'<?php echo $c_type;?>')"> <img src="<?php echo IMAGE_PATH;?>write_review.png" width="141" height="30" align="right" style="float:right; margin-top:-8px; " border="0" /> </a> </div>
  <?php
			
			$sqlv = "select * from comment where key_id='".$key_id . "' AND c_type='". $c_type."'";
				$resv = mysql_query($sqlv);
				$totv = mysql_num_rows($resv);
				
				if ($totv > 0 || $cgr == 1) {
			
		?>
  <ul class="recommend_ul">
    <?php
				
				
					while ($rowv = mysql_fetch_assoc($resv) ) {
						$review_id = $rowv['id'];
						$sqm = "select * from users where id='" . $rowv['by_user'] . "'";
						$resm = mysql_query($sqm);
						if ( $rowm = mysql_fetch_assoc($resm) ) {
							$mid			= $rowm['id'];
							$member_image 	= $rowm['image_name'];
							$member_name 	= $rowm['firstname'];
							
							$member_image 	= returnImage( ABSOLUTE_PATH . 'images/members/' . $member_image,125,150 );
				
						}
						
						$cdate 		= date("M d, Y",strtotime($rowv['date_posted']));
						$comment	= DBout($rowv['comment']);
			?>
    <li style="width:795px; margin-left: 10px;  padding: 20px; border-bottom: 1px solid #d8d8d8;">
      <div class="eventList">
        <div> <img <?php echo $member_image;?>  border="0" /><br />
          <span class="heading_colored_14" style="text-decoration:underline;"><?php echo $member_name;?></span> </div>
      </div>
      <div class="eventList_dt" style="width:640px">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php getReviewStarRating($key_id,$mid,$c_type);?>
              &nbsp;<span class="heading_dark_14_bold" style="margin-left:20px;"><?php echo $cdate;?></span></td>
          </tr>
          <tr>
            <td class="heading_dark_14"  style="padding-top:10px;"><?php echo $comment;?> </td>
          </tr>
          <tr>
            <td class="heading_dark_14" valign="top"  style="padding-top:10px;"><?php

								$already = attribValue("review_helpfull","id","where review_id=" . $review_id . " AND userid='". $member_id ."'");
								$said    = attribValue("review_helpfull","status","where review_id=" . $review_id . " AND userid='". $member_id ."'");
								if ( $said == 0 )
									$sai = 'No';
								if ( $said == 1 )
									$sai = 'Yes';
								if ( $said == 2 )
									$sai = 'Inappropriate';		
								if ( $already > 0 ) {
									echo '<span class="heading_dark_16">Was this review helpfull? You said "'. $sai .'"</span>';
								} else {	
								
							?>
              <span class="heading_dark_16">Was this review helpfull?</span>
              <button onclick="reviewHelpFull('<?php echo ABSOLUTE_PATH;?>',<?php echo $review_id;?>,1)" class="jes_btn"></button>
              <button onclick="reviewHelpFull('<?php echo ABSOLUTE_PATH;?>',<?php echo $review_id;?>,0)" class="no_btn"></button>
              <button onclick="reviewHelpFull('<?php echo ABSOLUTE_PATH;?>',<?php echo $review_id;?>,2)"  class="inap_btn"></button>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td class="heading_dark_14" valign="top"  style="padding-top:10px;"><?php
			$total_reviews1	= getSingleColumn('tot',"select count(*) as tot from review_helpfull where review_id=" . $review_id);
			$total_helpfull	= getSingleColumn('tot',"select count(*) as tot from review_helpfull where status=1 and review_id=" . $review_id);
								
								if ( $total_reviews1 > 0 )
									$total_helpfull_precent = ceil( ($total_helpfull/$total_reviews1) * 100);
								else
									$total_helpfull_precent = 0;	
								
							?>
              <?php echo $total_helpfull;?> out of <?php echo $total_reviews1;?> members found this review helpful </td>
          </tr>
          <tr>
            <td class="heading_dark_14" valign="top"  style="padding-top:15px;"><span class="d_style">Total Reviews:</span> <?php echo $total_reviews1;?> </td>
          </tr>
          <tr>
            <td class="heading_dark_14" valign="top"><span class="d_style">Helpfull Percent:</span> <?php echo $total_helpfull_precent;?>% </td>
          </tr>
        </table>
      </div>
    </li>
    <?php 
				} // end while 
				if ( $cgr == 1 ) { 
					foreach ($cg_reviews as $cg_review) {  
			?>
    <li style="width:795px; margin-left: 10px;  padding: 20px; border-bottom: 1px solid #d8d8d8;">
      <div class="eventList" style="width:100px">
        <div> <img src="<?php echo ABSOLUTE_PATH; ?>images/noimage_review.png" width="78" height="100" border="0" /><br />
          <span class="heading_colored_14" style="text-decoration:underline;"><?php echo $cg_review['author'];?></span> </div>
      </div>
      <div class="eventList_dt" style="width:686px">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php getReviewStarsOnRating($cg_review['rating']); ?>
              &nbsp;<span class="heading_dark_14_bold" style="margin-left:20px;"><?php echo date("M d, Y",strtotime($cg_review['date']));?></span></td>
          </tr>
          <tr>
            <td class="heading_dark_14"  style="padding-top:10px;"><strong><?php echo $cg_review['title'];?></strong><br>
              <?php echo $cg_review['text'];?> </td>
          </tr>
        </table>
      </div>
    </li>
    <?php } } ?>
    <?php } else { ?>
    <div style="padding:30px; text-align:center; color:#990000"> No review found for this <?php echo $c_type;?>. Be the first to write a review. <a href="javascript:void(0)" onclick="showReviewBox('<?php echo ABSOLUTE_PATH;?>',<?php echo $key_id;?>,'<?php echo $c_type;?>')"> Click Here. </a> </div>
    <?php } ?>
    <div class="clr"></div>
  </ul>
</div>
<!--end recommendedBlock-->
<?php
}

function getViewEventURL()
{
//	$sql = "select seo_name from categories order by id LIMIT 1";
	$sql = "select seo_name from categories where `id`='16'";
	$res = mysql_query($sql);
	if ( $r = mysql_fetch_assoc($res) )  {
		$seo_name = $r['seo_name'];
	}
	
	return ABSOLUTE_PATH . 'category/' . $seo_name . '.html';
}

function getTwitterStatus($userid){
	//http://twitter.com/statuses/user_timeline/eventgrabber.xml?count=1
	$url = "http://twitter.com/statuses/user_timeline/$userid.xml?count=1";
	$xml = simplexml_load_file($url) ;

	foreach($xml->status as $status) {
	   $text = $status->text;
	   $date = $status->created_at;
	}
	
	$a['tweet'] = ($text!="") ? $text : 'Huge News! Mashable Features EventGrabber. We need your support Orlando. We are getting flooded with emails from... http://t.co/ON8Id771';
	$a['date']  = ($date!="") ? $date : 'Fri Dec 09 06:16:20 +0000 2011';
	
	return  $a;
}


	

function getRecommendedQueryFinal($category)
{
	$q1 = makeRecommendedQuery($category,'O');
	$q2 = makeRecommendedQuery($category,'S');
	
	$sql = $q1 . ' UNION ' . $q2;
	return $sql;
}

function makeRecommendedQuery($category,$sele) {
	
	$ages 	= determineMemberAgeWeights($sele);
	$music	= determineMemberMusicWeights($sele);
	$events	= determineMemberEventWeights($sele);
	
	$events = $events[$category];
	if ( is_array($events) ) {
		foreach($events as $key => $value) {
			if ( $value > 0 )
				$tsubids[] = $key;
		}
	}
		
	if ( count($tsubids) > 0 ) {
		$tmp = implode(",",$tsubids);
		$q1 = " subcategory_id IN (". $tmp .")";
	} else {
		$q1 = " subcategory_id = '-1' ";
	}
	
	if ( is_array($music) ) {
		foreach($music as $key => $value) {
			if ( $value > 0 )
				$tmids[] = $key;
		} 
	}
	
	if ( count($tmids) > 0 ) {
		$tmp = implode(",",$tmids);
		$q2 = " AND id IN (select event_id from event_music where music_id IN (". $tmp .") ) ";
	} else {
		$q2 = " ";
	}
	
	if ( is_array($ages) ) {
		foreach($ages as $key => $value) {
			if ( $value > 0 )
				$taids[] = $key;
		}
	}
	
	if ( count($taids) > 0 ) {
		$tmp = implode(",",$taids);
		$q3 = " AND event_age_suitab IN (". $tmp .")";
	} else {
		$q2 = " ";
	}
	
	return $sql = "select * from events where event_status='1' AND (select event_date from event_dates where event_id=events.id ORDER by event_date DESC LIMIT 1) > DATE_SUB(CURDATE(),INTERVAL 1 DAY) AND category_id=$category and ( " . $q1 .  $q2 . $q3 ." ) ";
}

function determineMemberMusicWeights($sele)
{
	if ( $sele == 'O' )
		$sc = 5;
	else
		$sc = 3;	
		
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$mscore = array();
	$s3 	= "select * from member_music_pref where member_id='". $member_id ."' and selection='". $sele ."'";
	$rs3	= mysql_query($s3);
	while ( $r3 = mysql_fetch_assoc($rs3) )
		$mscore[$r3['music_genre']] = $sc;
	
	return $mscore;
	
}

function determineMemberAgeWeights($sele)
{
	if ( $sele == 'O' )
		$sc = 5;
	else
		$sc = 3;	
		
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$mscore = array();
	$s3 	= "select * from member_age_pref where member_id='". $member_id ."' and selection='". $sele ."'";
	$rs3	= mysql_query($s3);
	while ( $r3 = mysql_fetch_assoc($rs3) )
		$mscore[$r3['age_id']] = $sc;
	
	return $mscore;
}

function determineMemberEventWeights($sele)
{
	$score = array();
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	$rs	= mysql_query("select id from categories");
	while ( $r = mysql_fetch_assoc($rs) ) {

		$rs2	= mysql_query("select id from sub_categories where categoryid=" . $r['id']);
		while ( $r2 = mysql_fetch_assoc($rs2) ) {
			$score[$r['id']][$r2['id']] = 0;
			$s3 = "select selection from member_prefrences where member_id='". $member_id ."' AND prefrence_type='" . $r2['id'] . "' AND selection='". $sele ."' ";
			$rs3 = mysql_query($s3);
			if ( $r3 = mysql_fetch_assoc($rs3) ) { 
				$sc = 0;
				if ( $r3['selection'] == 'O' )
					$sc = 10;
				else if ( $r3['selection'] == 'S' )
					$sc = 5;	

				if ( $sc > 0 )
					$score[$r['id']][$r2['id']] = $sc;
				
			}	
		}
	}
	
	return $score;
}

function get_facebook_cookie($app_id, $application_secret) {
	$args = array();
	parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
	ksort($args);
	$payload = '';
	
	foreach ($args as $key => $value)
		if ($key != 'sig') 
	  		$payload .= $key . '=' . $value;

	if (md5($payload . $application_secret) != $args['sig']) 
		return null;
	
	return $args;
}

function getCompleteRow($table, $where) {
	$q = mysql_query("SELECT * FROM $table $where") or die(mysql_error());
	
	if ( mysql_num_rows($q) > 0 ) {
		$r = mysql_fetch_assoc($q);
		return $r;
	}
	
	return '';	
}


function userSubMenu($selected){

	$pages = array(
				"profile_setting" => "Profile Setting",
				"event_preference_setting" => "Event Preferences",
				"music_preference_setting" => "Music Preferences",
				"age_preference_setting" => "Age Suitability",
				"manage_event" => "Manage Events"
				
				);
	echo '<ul>';			
	$k=0;
	foreach ($pages as $name => $title) {
		$k++;
		if ( $name == $selected )
			$cls = 'class="active"';
		else	
			$cls = '';	
		
		if 	( $k == count($pages) )
			$stl = 'style="background:none;"';
		else
			$stl = '';	
?>
<li <?php echo $stl;?>><a href="<?php echo ABSOLUTE_PATH.$name;?>.php" <?php echo $cls;?> id="a5" ><strong><?php echo $title;?></strong></a></li>
<?php
	}
	
	echo '<ul>';

}

function userSubMenu2($selected){

	$pages = array(
				ABSOLUTE_PATH. "manage_event" => "Manage Events",
				ABSOLUTE_PATH. "create_event" => "Add Event",
				ABSOLUTE_PATH. "facebook_flyer_settings" => "Facebook",
				
				);
	echo '<ul>';			
	$k=0;
	foreach ($pages as $name => $title) {
		$k++;
		if ( $name == $selected )
			$cls = 'class="active"';
		else	
			$cls = '';	
		
		if 	( $k == count($pages) )
			$stl = 'style="background:none;"';
		else
			$stl = '';	
?>
<li <?php echo $stl;?>><a href="<?php echo ABSOLUTE_PATH.$name;?>.php" <?php echo $cls;?> id="a5" ><strong><?php echo $title;?></strong></a></li>
<?php
	}
	
	echo '<ul>';

}


function makeRecommendedQueryExtended($category="",$sele) {
	
	if ( $category != "" ) 
		$catFilter = " AND category_id='". $category ."'";
	else
		$catFilter = "";	
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	$often_subcat = array();
	$sql = "select prefrence_type from member_prefrences where member_id='". $member_id ."' and selection='". $sele ."'";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) 
		$often_subcat[]  = $row['prefrence_type'];
	
	if ( count($often_subcat) > 0 )
		$subCat = implode(",",$often_subcat);
	else
		$subCat = 0;	
		
	$often_music = array();
	$sql = "select music_genre from member_music_pref where member_id='". $member_id ."' and selection='". $sele ."'";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) 
		$often_music[]  = $row['music_genre'];
	
	if ( count($often_music) > 0 )
		$musGen = implode(",",$often_music);
	else
		$musGen = 0;
		
	$often_age = array();
	$sql = "select age_id from member_age_pref where member_id='". $member_id ."' and selection='". $sele ."'";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) 
		$often_age[]  = $row['age_id'];
	
	if ( count($often_age) > 0 )
		$ages = implode(",",$often_age);
	else
		$ages = 0;
	
	$often_subcat = array();
	$sql = "select prefrence_type from member_prefrences where member_id='". $member_id ."' and selection='N'";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) 
		$often_subcat[]  = $row['prefrence_type'];
	
	if ( count($often_subcat) > 0 )
		$subCatN = implode(",",$often_subcat);
	else
		$subCatN = 0;
			
	$often_music = array();
	$sql = "select music_genre from member_music_pref where member_id='". $member_id ."' and selection='N'";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) 
		$often_music[]  = $row['music_genre'];
	
	if ( count($often_music) > 0 )
		$musGenN = implode(",",$often_music);
	else
		$musGenN = 0;
		
	$often_age = array();
	$sql = "select age_id from member_age_pref where member_id='". $member_id ."' and selection='N'";
	$res = mysql_query($sql);
	while ( $row = mysql_fetch_assoc($res) ) 
		$often_age[]  = $row['age_id'];
	
	if ( count($often_age) > 0 )
		$agesN = implode(",",$often_age);
	else
		$agesN = 0;

	$sql = "select * from events where event_status='1' ". $catFilter ." AND is_expiring=1 AND 
			( 	
				subcategory_id IN (" . $subCat . ")
				AND
				id IN (select event_id from event_music where music_id IN (". $musGen .") )
				AND
				event_age_suitab IN (". $ages .") 
			) AND (
				subcategory_id NOT IN (" . $subCatN . ")
				OR
				id IN (select event_id from event_music where music_id NOT IN (". $musGenN .") )
				OR
				event_age_suitab NOT IN (". $agesN .") 
			)
			
			UNION
			
			select * from events where event_status='1' ". $catFilter ." AND is_expiring=1 AND
			( 	
				subcategory_id IN (" . $subCat . ")
				AND
				id IN (select event_id from event_music where music_id IN (". $musGen .") )
			) AND (
				subcategory_id NOT IN (" . $subCatN . ")
				OR
				id IN (select event_id from event_music where music_id NOT IN (". $musGenN .") )
				OR
				event_age_suitab NOT IN (". $agesN .") 
			)
			
			UNION
			
			select * from events where event_status='1' ". $catFilter ." AND is_expiring=1 AND
			( 	
				subcategory_id IN (" . $subCat . ")
				AND
				event_age_suitab IN (". $ages .") 
			) AND (
				subcategory_id NOT IN (" . $subCatN . ")
				OR
				id IN (select event_id from event_music where music_id NOT IN (". $musGenN .") )
				OR
				event_age_suitab NOT IN (". $agesN .") 
			)
			
			UNION
			
			select * from events where event_status='1' ". $catFilter ." AND is_expiring=1 AND
			( 	
				id IN (select event_id from event_music where music_id IN (". $musGen .") )
				AND
				event_age_suitab IN (". $ages .") 
			) AND (
				subcategory_id NOT IN (" . $subCatN . ")
				OR
				id IN (select event_id from event_music where music_id NOT IN (". $musGenN .") )
				OR
				event_age_suitab NOT IN (". $agesN .") 
			)
			";
	return $sql;
}

function format_phone($phone)
{
	$phone = preg_replace("/[^0-9]/", "", $phone);

	if( strlen($phone) == 11 &&  substr($phone,0,1) == 1 )
		$phone = substr($phone,1,10);
	 
	if(strlen($phone) == 7)
		return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
	elseif(strlen($phone) == 10)
		return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
	else
		return $phone;
}


function getLocationImageFromYelp($lat,$lng)
{
	$h = 200;
	$w = 300;

	require_once ('admin/yelp_lib.php');

	$consumer_key 		= "2rAJbVGZCjq4lkOJeM5lyw";
	$consumer_secret	= "b9RCOfJy0XLZ59r3BBUVzMaCKI8";
	$token 				= "d6MQYMqKCuTfQtvEHUSakg7p2C2jqNTt";
	$token_secret 		= "Z6ZVBvYk-O7UOEbvk15CHLGk69U";
	
	$token 				= new OAuthToken($token, $token_secret);
	$consumer 			= new OAuthConsumer($consumer_key, $consumer_secret);
	$signature_method 	= new OAuthSignatureMethod_HMAC_SHA1();
	
	$unsigned_url2 = "http://api.yelp.com/v2/search?ll=". $lat .",". $lng ."&limit=1";
	
	$unsigned_url3 = $unsigned_url2 ;
	$oauthrequest2 = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url3);
	$oauthrequest2->sign_request($signature_method, $consumer, $token);
	$signed_url2 = $oauthrequest2->to_url();
	
	$ch = curl_init($signed_url2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data2 = curl_exec($ch); 
	curl_close($ch);
		
	$response2 = json_decode($data2);
	$records2 = $response2->businesses;
	
	if ( count($records2) > 0 ) 
		foreach ($records2 as $obj ) 
			$image = $obj->image_url;
	
	if ( $image != '' )	{
		if ( strpos($image, 'ms.') !== FALSE ) {
			$image = str_replace("ms.","l.",$image);
			return $image;
		}
	}
	
	return '';
}


function getMarketCategoryName($cat_id){
$res = mysql_query("select * from `market_category` where `id`='$cat_id'");
while($row = mysql_fetch_array($res)){
return $row['name'];
}}


function getProductImg($id,$width,$height,$align,$thumb){
$qry = "select * from `products` where `id`='$id'";
$res = mysql_query($qry);
if(mysql_num_rows($res)){
while($row = mysql_fetch_array($res)){
if($row['image']!='' && file_exists(DOC_ROOT . 'images/products/ico_' . $row['image'] ) ){
return '<img src="'.PRODUCT_IMAGE_PATH.$thumb.$row['image'].'" width="'.$width.'" height="'.$height.'" align="'.$align.'" />';
}else{
return '<img src="'.ABSOLUTE_PATH.'admin/images/no_image.png" width="'.$width.'" height="'.$height.'" align="'.$align.'" />';
}}}
}


function getProductTitle($product_id){
$qry = "select * from `products` where `id`='$product_id'";
$res = mysql_query($qry);
while($row = mysql_fetch_array($res)){
return $row['name'];
}}

function sponsored(){

$res = mysql_query("select * from `sponsor` ORDER BY RAND() LIMIT 0,6");
?>
<div class="sponsoreBanners">
  <table cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <?php
while($row = mysql_fetch_array($res)){
?>
      <td class="sp" width="114" align="left"><a target="_blank" title="<?=$row['name'];?>" href="<?=$row['url'];?>"><img src="<?=IMAGE_PATH."th_".$row['image']; ?>" alt="" title=""></a></td>
      <td width="10">&nbsp;</td>
      <?php
}
?>
      <td>&nbsp;</td>
    </tr>
  </table>
</div>
<?php
}

function getFeaturedDeals($cat_id,$order,$limit,$crctrLimit,$seo_name){
//return "select * from `products` where `featured`='1' && `category_id`='$cat_id' ORDER BY `id` $order LIMIT 0,$limit";

$res = mysql_query("select * from `products` where `featured`='1' && `category_id`='$cat_id' ORDER BY `id` $order LIMIT 0,$limit");
if(mysql_num_rows($res)){
$a = '<span class="catDesc">';
while($row = mysql_fetch_array($res)){
$a.='<a href="'.ABSOLUTE_PATH.'deal/'.$row['seo_name'].'/'.$row['id'].'.html">'.substr($row["name"],0,$crctrLimit);
if(strlen($row["name"]) > $crctrLimit){
$a.="...";
}
$a.='</a><br>
';
}
return $a.='</span><br><a href="'.ABSOLUTE_PATH.'cat/'.$seo_name.'.html" class="seeAll">See All</a>';
}
}


function getCatNameFromProductId($product_id){
$res = mysql_query("select * from `products` where `id`='$product_id'");
while($row = mysql_fetch_array($res)){
$cat_id = $row['category_id'];
$res = mysql_query("select * from `market_category` where `id`='$cat_id'");
while($row = mysql_fetch_array($res)){
return $row['name'];
}}}




function validEventTicketSaleTime($event_id){
$r = mysql_query("select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC LIMIT 0,1");
while($ro = mysql_fetch_array($r)){
$bc_event_start_date		=	$ro['event_date'];
}


$r = mysql_query("select * from `events` where `id`='$event_id'");
while($ro = mysql_fetch_array($r)){
$bc_event_start_time	=	$ro['event_start_time'];
$bc_event_start_time	=	date('H:i', strtotime($bc_event_start_time));
}

$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
while($row = mysql_fetch_array($res)){
$bc_current_date				=	date('Y-m-d');
$bc_current_time				=	date('H:i:s');
$bc_start_sales_date			=	$row['start_sales_date'];
$bc_start_sales_time			=	$row['start_sales_time'];
$bc_start_sales_before_days		=	$row['start_sales_before_days'];
$bc_start_sales_before_hours	=	$row['start_sales_before_hours'];
$bc_start_sales_before_minutes	=	$row['start_sales_before_minutes'];
$bc_end_sales_date				=	$row['end_sales_date'];
$bc_end_sales_time				=	$row['end_sales_time'];
$bc_end_sales_before_days		=	$row['end_sales_before_days'];
$bc_end_sales_before_hours		=	$row['end_sales_before_hours'];
$bc_end_sales_before_minutes	=	$row['end_sales_before_minutes'];
}

if($bc_start_sales_date!='0000-00-00'){
$start_sales_date	=	$bc_start_sales_date;
$start_sales_time	=	$bc_start_sales_time;
}

elseif($bc_start_sales_before_days!=0 || $bc_start_sales_before_hours!=0 || $bc_start_sales_before_minutes!=0){

if($bc_start_sales_before_days!=0 && $bc_start_sales_before_hours==0 && $bc_start_sales_before_minutes==0){

$bc_start_sales_before_days	=	$bc_start_sales_before_days*24;
$time1						= strtotime($bc_event_start_date) - (60 * $bc_start_sales_before_days * 60);

		$start_sales_date			= date("Y-m-d", $time1);
		$start_sales_time		=	'00:00:00';
}


if($bc_start_sales_before_days==0 && $bc_start_sales_before_hours!=0 && $bc_start_sales_before_minutes==0){

$time1						= strtotime($bc_event_start_time) - (60 * $bc_start_sales_before_hours * 60);

		$start_sales_date			=	$bc_event_start_date;
		$start_sales_time			=	date('H:i', $time1);
}


if($bc_start_sales_before_days==0 && $bc_start_sales_before_hours==0 && $bc_start_sales_before_minutes!=0){

$time1						= strtotime($bc_event_start_time) - (60*$bc_start_sales_before_minutes);

		$start_sales_date			=	$bc_event_start_date;
		$start_sales_time			=	date('H:i', $time1);
}

if($bc_start_sales_before_days!=0 && $bc_start_sales_before_hours!=0 && $bc_start_sales_before_minutes!=0){


$mn2	=	$bc_start_sales_before_hours*60;

$bc_start_sales_before_minutes	=	$bc_start_sales_before_minutes+$mn2;

$time1						= strtotime($bc_event_start_time) - (60*$bc_start_sales_before_minutes);

		$start_sales_time			=	date('H:i', $time1);

$time1						= strtotime($bc_event_start_date) - (60 * $bc_start_sales_before_days * 60);

		$start_sales_date			= date("Y-m-d", $time1);
	
}

}

else{
	$start_sales_date	=	"1990-1-1";
	$start_sales_time	=	"00:00:00";
}


////// END

if($bc_end_sales_date!='0000-00-00'){
	$end_sales_date	=	$bc_end_sales_date;
	$end_sales_time	=	$bc_end_sales_time;
}

elseif($bc_end_sales_before_days!=0 || $bc_end_sales_before_hours!=0 || $bc_end_sales_before_minutes!=0){

if($bc_end_sales_before_days!=0 && $bc_end_sales_before_hours==0 && $bc_end_sales_before_minutes==0){

$bc_end_sales_before_days	=	$bc_end_sales_before_days*24;
$time1						= strtotime($bc_event_start_date) - (60 * $bc_end_sales_before_days * 60);

		$end_sales_date			= date("Y-m-d", $time1);
		$end_sales_time		=	'00:00:00';
}


if($bc_end_sales_before_days==0 && $bc_end_sales_before_hours!=0 && $bc_end_sales_before_minutes==0){

$time1						= strtotime($bc_event_start_time) - (60 * $bc_end_sales_before_hours * 60);

		$end_sales_date			=	$bc_event_start_date;
		$end_sales_time			=	date('H:i', $time1);
}


if($bc_end_sales_before_days==0 && $bc_end_sales_before_hours==0 && $bc_end_sales_before_minutes!=0){

$time1						= strtotime($bc_event_start_time) - (60*$bc_end_sales_before_minutes);

		$end_sales_date			=	$bc_event_start_date;
		$end_sales_time			=	date('H:i', $time1);
}

if($bc_end_sales_before_days!=0 && $bc_end_sales_before_hours!=0 && $bc_end_sales_before_minutes!=0){


$mn2	=	$bc_end_sales_before_hours*60;

$bc_end_sales_before_minutes	=	$bc_end_sales_before_minutes+$mn2;

$time1						= strtotime($bc_event_start_time) - (60*$bc_end_sales_before_minutes);

		$end_sales_time			=	date('H:i', $time1);

$time1						= strtotime($bc_event_start_date) - (60 * $bc_end_sales_before_days * 60);

		$end_sales_date			= date("Y-m-d", $time1);
	
}
}
else{
	$end_sales_date	=	$bc_event_start_date;
	$end_sales_time	=	"00:00:00";
}

if($start_sales_date < $bc_current_date  && $end_sales_date > $bc_current_date){
return $start_sale_valid	=	'yes';
}


elseif($start_sales_date==$bc_current_date){

if($start_sales_time < $bc_current_time){
return $start_sale_valid	=	'yes';
}
else{
return $start_sale_valid	=	'no';
}}
else{
return $start_sale_valid	=	'no';
}


}



function eventImagesGallery($product_id){?>
<?php
		$qry = "select * from `products_images` where `product_id`='$product_id'";
		$res = mysql_query($qry);
		if(mysql_num_rows($res)){
		?>
<ul id="mycarousel" class="jcarousel-skin-tango">
  <?php
		while($row = mysql_fetch_array($res)){?>
  <li><a href="<?php echo PRODUCT_IMAGE_PATH.''.$row['image']; ?>" class="fancybox"><img src="<?php echo PRODUCT_IMAGE_PATH.'ico_'.$row['image']; ?>" width="125" /></a></li>
  <?php
		}
		?>
</ul>
<?php
}}

function getMemberFirstAndLastName($id){
$res = mysql_query("select * from `users` where `id`='$id'");
while($row = mysql_fetch_array($res)){
return $row['firstname']." ".$row['lastname'];
}
}



function getTicketStartSale($event_id){

$r = mysql_query("select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC LIMIT 0,1");
while($ro = mysql_fetch_array($r)){
$bc_event_start_date		=	$ro['event_date'];
}


$r = mysql_query("select * from `events` where `id`='$event_id'");
while($ro = mysql_fetch_array($r)){
$bc_event_start_time	=	$ro['event_start_time'];
$bc_event_start_time	=	date('H:i', strtotime($bc_event_start_time));
}

$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
while($row = mysql_fetch_array($res)){
$bc_current_date				=	date('Y-m-d');
$bc_current_time				=	date('H:i:s');
$bc_start_sales_date			=	$row['start_sales_date'];
$bc_start_sales_time			=	$row['start_sales_time'];
$bc_start_sales_before_days		=	$row['start_sales_before_days'];
$bc_start_sales_before_hours	=	$row['start_sales_before_hours'];
$bc_start_sales_before_minutes	=	$row['start_sales_before_minutes'];
$bc_end_sales_date				=	$row['end_sales_date'];
$bc_end_sales_time				=	$row['end_sales_time'];
$bc_end_sales_before_days		=	$row['end_sales_before_days'];
$bc_end_sales_before_hours		=	$row['end_sales_before_hours'];
$bc_end_sales_before_minutes	=	$row['end_sales_before_minutes'];
}

if($bc_start_sales_date!='0000-00-00'){
	$start_sales_date	=	$bc_start_sales_date;
	$start_sales_time	=	$bc_start_sales_time;
}

elseif($bc_start_sales_before_days!=0 || $bc_start_sales_before_hours!=0 || $bc_start_sales_before_minutes!=0){

if($bc_start_sales_before_days!=0 && $bc_start_sales_before_hours==0 && $bc_start_sales_before_minutes==0){

$bc_start_sales_before_days	=	$bc_start_sales_before_days*24;
$time1						= strtotime($bc_event_start_date) - (60 * $bc_start_sales_before_days * 60);

		$start_sales_date			= date("Y-m-d", $time1);
		$start_sales_time		=	'00:00:00';
}


if($bc_start_sales_before_days==0 && $bc_start_sales_before_hours!=0 && $bc_start_sales_before_minutes==0){

$time1						= strtotime($bc_event_start_time) - (60 * $bc_start_sales_before_hours * 60);

		$start_sales_date			=	$bc_event_start_date;
		$start_sales_time			=	date('H:i', $time1);
}


if($bc_start_sales_before_days==0 && $bc_start_sales_before_hours==0 && $bc_start_sales_before_minutes!=0){

$time1						= strtotime($bc_event_start_time) - (60*$bc_start_sales_before_minutes);

		$start_sales_date			=	$bc_event_start_date;
		$start_sales_time			=	date('H:i', $time1);
}

if($bc_start_sales_before_days!=0 && $bc_start_sales_before_hours!=0 && $bc_start_sales_before_minutes!=0){


$mn2	=	$bc_start_sales_before_hours*60;

$bc_start_sales_before_minutes	=	$bc_start_sales_before_minutes+$mn2;

$time1						= strtotime($bc_event_start_time) - (60*$bc_start_sales_before_minutes);

		$start_sales_time			=	date('H:i', $time1);

$time1						= strtotime($bc_event_start_date) - (60 * $bc_start_sales_before_days * 60);

		$start_sales_date			= date("Y-m-d", $time1);
	
}

}

else{
	$start_sales_date	=	"1990-1-1";
	$start_sales_time	=	"00:00:00";
}

$result[0]	=	$start_sales_date;
$result[1]	=	$start_sales_time;

return $result;

}

function getTicketEndSale($event_id){

$r = mysql_query("select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC LIMIT 0,1");
while($ro = mysql_fetch_array($r)){
$bc_event_start_date		=	$ro['event_date'];
}


$r = mysql_query("select * from `events` where `id`='$event_id'");
while($ro = mysql_fetch_array($r)){
$bc_event_start_time	=	$ro['event_start_time'];
$bc_event_start_time	=	date('H:i', strtotime($bc_event_start_time));
}

$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
while($row = mysql_fetch_array($res)){
$bc_current_date				=	date('Y-m-d');
$bc_current_time				=	date('H:i:s');
$bc_start_sales_date			=	$row['start_sales_date'];
$bc_start_sales_time			=	$row['start_sales_time'];
$bc_start_sales_before_days		=	$row['start_sales_before_days'];
$bc_start_sales_before_hours	=	$row['start_sales_before_hours'];
$bc_start_sales_before_minutes	=	$row['start_sales_before_minutes'];
$bc_end_sales_date				=	$row['end_sales_date'];
$bc_end_sales_time				=	$row['end_sales_time'];
$bc_end_sales_before_days		=	$row['end_sales_before_days'];
$bc_end_sales_before_hours		=	$row['end_sales_before_hours'];
$bc_end_sales_before_minutes	=	$row['end_sales_before_minutes'];
}

if($bc_end_sales_date!='0000-00-00'){
	$end_sales_date	=	$bc_end_sales_date;
	$end_sales_time	=	$bc_end_sales_time;
}

elseif($bc_end_sales_before_days!=0 || $bc_end_sales_before_hours!=0 || $bc_end_sales_before_minutes!=0){

if($bc_end_sales_before_days!=0 && $bc_end_sales_before_hours==0 && $bc_end_sales_before_minutes==0){

$bc_end_sales_before_days	=	$bc_end_sales_before_days*24;
$time1						= strtotime($bc_event_start_date) - (60 * $bc_end_sales_before_days * 60);

		$end_sales_date			= date("Y-m-d", $time1);
		$end_sales_time		=	'00:00:00';
}


if($bc_end_sales_before_days==0 && $bc_end_sales_before_hours!=0 && $bc_end_sales_before_minutes==0){

$time1						= strtotime($bc_event_start_time) - (60 * $bc_end_sales_before_hours * 60);

		$end_sales_date			=	$bc_event_start_date;
		$end_sales_time			=	date('H:i', $time1);
}


if($bc_end_sales_before_days==0 && $bc_end_sales_before_hours==0 && $bc_end_sales_before_minutes!=0){

$time1						= strtotime($bc_event_start_time) - (60*$bc_end_sales_before_minutes);

		$end_sales_date			=	$bc_event_start_date;
		$end_sales_time			=	date('H:i', $time1);
}

if($bc_end_sales_before_days!=0 && $bc_end_sales_before_hours!=0 && $bc_end_sales_before_minutes!=0){


$mn2	=	$bc_end_sales_before_hours*60;

$bc_end_sales_before_minutes	=	$bc_end_sales_before_minutes+$mn2;

$time1						= strtotime($bc_event_start_time) - (60*$bc_end_sales_before_minutes);

		$end_sales_time			=	date('H:i', $time1);

$time1						= strtotime($bc_event_start_date) - (60 * $bc_end_sales_before_days * 60);

		$end_sales_date			= date("Y-m-d", $time1);
	
}
}
else{
	$end_sales_date	=	$bc_event_start_date;
	$end_sales_time	=	"00:00:00";
}

	$result[0]	=	$end_sales_date;
	$result[1]	=	$end_sales_time;
	
	return $result;

}


function getEventDatesInArray($event_id){
$res = mysql_query("select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC");
while($row = mysql_fetch_array($res)){
$dates[]	=	$row['event_date'];
}
return $dates;
}



function getEventDatesInArray2($event_id){
$date = array();
$id = array();
$result = array();

$res = mysql_query("select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC");
while($row = mysql_fetch_array($res)){
$date[]	=	$row['event_date'];
$id[]	=	$row['id'];
}
$result['date']	= $date;
$result['id']	= $id;
return $result;
}



function getFriends($user_id){
$res = mysql_query("select * from `member_referals` where `ref_member_id`='$user_id'");
if(mysql_num_rows($res)){
echo "<ul>";
$i	= 0;
while($row = mysql_fetch_array($res)){
$i++;
$member_id	=	$row['member_id'];
$res2 = mysql_query("select * from `users` where `id`='$member_id'");
while($row2	= mysql_fetch_array($res2)){
if($row2['image_name']!=''){
$img	=	IMAGE_PATH."members/".$row2['image_name'];
}
else{
$img	=	IMAGE_PATH.'man.gif';
}
$maginNot	=	'';
if($i==1){
$maginNot	=	"margin:0;";
}
else{
$maginNot	=	"";
}

?>
<li style=" <?php echo $maginNot ?>"><a href="#"><?php echo $row2["name"].' '.$row2["lname"]; ?></a>
  <div class="list_inner">
    <div style="overflow: hidden; height: 200px;"><a href="#"><img align="left" <?php echo returnImage($img); ?> /></a></div>
  </div>
</li>
<?php
}}
echo "</ul>";
}
else{
echo "<b>No Record Found</b>";
}}


//function getEventWallTodayEvents(){}
//
//$res = mysql_query("select * from `event_wall` where `userid`='$user_id'");
//while($row = mysql_fetch_array($res)){
//$event_id	=	$row['event_id'];
//$res2 = mysql_query("select * from `` where ``=''");
//}
//

function getFnameLname($member_id){
$res = mysql_query("select * from `users` where `id`='$member_id'");
while($row = mysql_fetch_array($res)){
$name['id']	=	$row['id'];
$name['name']	=	$row['firstname'];
$name['lname']	=	$row['lastname'];
}
return $name;
}


function getGroups($user_id,$group_id){
$res = mysql_query("select * from `hangout_group` where `member_id`='$user_id'");
if(mysql_num_rows($res)){
echo "<ul>";
$i=0;
while($row = mysql_fetch_array($res)){
$member_id	=	$row['member_id'];
$group_id	=	$row['id'];
$i++;
if($row['image']!=''){
$img	=	IMAGE_PATH."group/th_".$row['image'];
}
else{
$img	=	IMAGE_PATH.'n_image.gif';
}
if($i==1){
$maginNot	=	"margin:0;";
}
else{
$maginNot	=	'';
}
?>
<li style=" <?php echo $maginNot ?>">
  <div class="list_inner">
    <div style="overflow: hidden; height: 200px;"><a href="<?php echo ABSOLUTE_PATH; ?>my_network.php?groupid=<?php echo $group_id; ?>&#group"><img align="left"  <?php echo returnImage($img); ?> /></a></div>
  </div>
</li>
<?php
}
echo "</ul>";
}
else{
echo "<b>No Record Found</b>";
}}


function getGroupMembers($group_id){
$position = array("left: 135px; top: 4px;","left: 254px; top: 3px;","left: 351px; top: 73px;","left: 388px; top: 189px;","left: 351px; top: 302px;","left: 253px; top: 373px;","left: 135px; top: 374px;","left: 38px; top: 302px;","left: 0; top: 188px;","left: 38px; top: 73px;",);
echo '<div style="position:relative">';
$res = mysql_query("select * from `group_members` where `group_id`='$group_id' ORDER BY `id` ASC LIMIT 0,10");
if(mysql_num_rows($res)){
$i=0;
while($row = mysql_fetch_array($res)){
$bc_member_id	=	$row['member_id'];
$res2 = mysql_query("select * from `users` where `id`='$bc_member_id'");

while($row2 = mysql_fetch_array($res2)){
$bc_image_name	=	$row2['image_name'];

if($bc_image_name==''){
$bc_image_thumb =	IMAGE_PATH."group/icon/noimg.gif";
$bc_image_big	=	IMAGE_PATH."group/icon/big/noimg.gif";
}

else{
if (file_exists(DOC_ROOT . 'images/group/icon/' . $bc_image_name)) {
$bc_image_thumb =	IMAGE_PATH."group/icon/".$bc_image_name;
$bc_image_big	=	IMAGE_PATH."group/icon/big/".$bc_image_name;
}

else{

if (file_exists(DOC_ROOT . 'images/members/' . $bc_image_name ) ) {

makeThumbnailFixWidthHeight($bc_image_name, 'images/members/th_', 'images/group/icon/', 77, 77);
makeThumbnailFixWidthHeight($bc_image_name, 'images/members/th_', 'images/group/icon/big/', 281, 280);
}
else{
$bc_image_thumb =	IMAGE_PATH."group/icon/noimg.gif";
$bc_image_big	=	IMAGE_PATH."group/icon/big/noimg.gif";
}

}
}
?>
<a href="javascript:void(0)" style=" <?php echo $position[$i]; ?>position:absolute;cursor:pointer;display: block;height: 77px;width: 77px; background:url(<?php echo $bc_image_thumb; ?>)" 
				onclick="getGroupMemberProfile('<?php echo ABSOLUTE_PATH ;?>',<?php echo $bc_member_id; ?>,'<?php echo $bc_image_big ;?>');"><img src="<?= IMAGE_PATH; ?><?php if ($i==0){ echo "frameOver.gif";} else{ echo "frame.gif"; }?>" class="roundImg" id="roundImg<?php echo $bc_member_id; ?>" style="position:static"></a>
<?php if ($i==0){ ?>
<div id="bigImgss" style=" width:281px; height:280px; left: 93px; position: absolute; top: 85px; z-index: 0; background:url(<?php echo $bc_image_big; ?>);"> <img src="<?= IMAGE_PATH; ?>big3.gif" style="position:static" /></div>
<?php
			}
			?>
<?php				
		}
$i++;
}}

echo '</div>';}



function getGroupMemberProfile($member_id){
$res = mysql_query("select * from `users` where `id`='$member_id'");
while($row = mysql_fetch_array($res)){
$fname	=	$row['firstname'];
$lname	=	$row['lastname'];
}
?>
<div><span><u> <?php echo $fname." ".$lname; ?> </u> / Profession</span></div>
<br>
"This is the person's favorite slogan or tagline"<br>
<br>
<strong> <?php echo $fname." ".$lname; ?> 's Preferences:</strong><br>
<br>
<div class="color2">Events I love: </div>
Concerts, Happy Hour, Festivals<br>
<br>
<div class="color2">Events I like:</div>
Lounges, Live Music, Performing Arts<br>
<br>
<div class="color2">My type of music:</div>
<?php
			 	$rs = mysql_query("select * from `member_music_pref` where `member_id`='$member_id'");
				while ( $ro = mysql_fetch_assoc($rs) ) {
				$music	=	$ro['music_genre'];
				$music = attribValue("music","name","where id='$music'");
				echo $music.", ";
				}
			?>
<br>
<br>
<div class="color2">My type of crowds:</div>
<?php
			 	$rs = mysql_query("select * from `member_age_pref` where `member_id`='$member_id'");
				while ( $ro = mysql_fetch_assoc($rs) ) {
				$age_id	=	$ro['age_id'];
				$age = attribValue("age","name","where id='$age_id'");
				echo $age.", ";
				}}
				
				
				
function getMyEventwall($value){
?>
<script type="text/javascript" src="<?= ABSOLUTE_PATH; ?>js/jquery.flip.min.js"></script>
<script type="text/javascript" src="<?= ABSOLUTE_PATH; ?>js/script.js"></script>
<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
		 var container			=	$('div.sliderGallery2');
			 var ul					=	$('ul', container);
			 var li					=	$('li', ul);
			 var list_inner			=	$('div.list_inner', li);
			 var list_inner_size	=	list_inner.size();
		//	 alert(list_inner_size);
			 if(list_inner_size > 4){
			 var productWidth		=	li.innerWidth()+46;
			$(ul).css('width',list_inner_size*productWidth-46);
		/*	var showResord	=	ul.innerWidth()/container.innerWidth()+1; */
			 var showResord	=	5;
			var itemsWidth = ul.innerWidth()-(li.innerWidth()*showResord);
            $('.slider2', container).slider({
                min: 0,
                max: itemsWidth,
                handle: '.handle2',
                stop: function (event, ui) {
                    ul.animate({'left' : ui.value * -1}, 500);
                },
                slide: function (event, ui) {
					$('#show').val(ui.value);
                    ul.css('left', ui.value * -1);
                }
            });	
			}
			else{
			$('.slider2').css('display','none');
			}		
        });
		</script>
<?php
$user_id	=	$_SESSION['LOGGEDIN_MEMBER_ID'];
 $qry = "select * from `events` where `event_status`='1' AND id IN (select `event_id` from `event_wall` where `userid`='$user_id' ) ORDER BY `id` ASC";
$res = mysql_query($qry);
?>
<div class="flayerCenter" style="float:left; width:auto">
  <div class="menu">
    <ul>
      <li <?php if ($value=='today'){ echo 'class="firstOver"';} else{ echo 'class="first" onMouseOver="document.getElementById(\'first2\').className=\'firstOver\';" onMouseOut="document.getElementById(\'first2\').className=\'first\';"';}?> id="first2"> <a <?php if ($value=='today'){ echo 'class="flayerMenuActive"';}?> href="javascript:void(0)"  onclick="getMyEventwall('<?php echo ABSOLUTE_PATH; ?>','today');">Today</a> </li>
      <li><a href="javascript:void(0)" <?php if ($value=='week'){ echo 'class="flayerMenuActive"';}?> onclick="getMyEventwall('<?php echo ABSOLUTE_PATH; ?>','week');">This Week</a></li>
      <li><a href="javascript:void(0)" <?php if ($value=='weekend'){ echo 'class="flayerMenuActive"';}?> onclick="getMyEventwall('<?php echo ABSOLUTE_PATH; ?>','weekend');">This Weekend</a></li>
      <li <?php if ($value=='all'){ echo 'class="lastOver"';} else{ echo 'class="last" onMouseOver="document.getElementById(\'last2\').className=\'lastOver\';" onMouseOut="document.getElementById(\'last2\').className=\'last\';"';}?> id="last2"><a <?php if ($value=='all'){ echo 'class="flayerMenuActive"';}?> href="javascript:void(0)" onclick="getMyEventwall('<?php echo ABSOLUTE_PATH; ?>','all');">All</a></li>
    </ul>
  </div>
  <div class="clr" style="height:14px">&nbsp;</div>
</div>
<div class="clr"></div>
<div class="frndBoxTop">
  <div class="frndBoxBottom">
    <div class="frndBoxMiddle">
      <div class="sliderGalleryContainer">
        <div class="sliderGallery2">
          <?php
if (mysql_num_rows($res)){

echo "<ul>";
$i=0;
$r = 0;
while($row = mysql_fetch_array($res)){

$event_id		=	$row['id'];
$event_image	=	$row['event_image'];
$bc_event_type	=	$row['event_type'];

if($value=='today'){
$today	=	date('Y-m-d');
$qry2	=	"select * from `event_dates` where `event_id`='$event_id' && `event_date`='$today'";
}

elseif($value=='week'){
$week	=	getCurrentWeek();
$weekStartDate	=	date('Y-m-d', $week['start']);
$weekEndDate	=	date('Y-m-d', $week['end']);
$today	=	date('Y-m-d');
$qry2	=	"select * from `event_dates` where `event_id`='$event_id' && `event_date` BETWEEN '$weekStartDate' AND '$weekEndDate'";
}

elseif($value=='weekend'){
$saturday = date("Y-m-d", strtotime('Saturday'));
$sunday = date("Y-m-d", strtotime('Sunday'));
$qry2	=	"select * from `event_dates` where `event_id`='$event_id' && (`event_date`='$saturday' ||`event_date`='$sunday')";
}

$res2	=	mysql_query($qry2);

if($value=='all' || mysql_num_rows($res2)){
$r = 1;
$i++;
$maginNot	=	'';
if($i==1){
$maginNot	=	"margin:0;";
}
?>
          <li style=" <?php echo $maginNot; ?>">
            <div class="list_inner">
              <div style="overflow: hidden; height: 200px;">
                <?php 
if($bc_event_type==0){
$event_url = getEventURL($event_id);
?>
                <a href="<?php echo $event_url; ?>" target="_blank">
                <?php }
else{?>
                <a href="javascript:void(0)" onClick="getFlayer('<?php echo ABSOLUTE_PATH; ?>', '<?php echo $event_id; ?>','');">
                <?php } ?>
                <?php 
			$event_image = removeSpaces("events","event_image",$event_image,"event_images/");
				echo getEventImage($event_image,1); ?>
                </a></div>
            </div>
          </li>
          <?php
}
}
echo "</ul><div class='slider2'><div class='handle2'></div></div>";
}
else{
if($r==0){
echo "<strong style='color:#ed6c6c; font-size: 25px;'> &nbsp;  &nbsp;  &nbsp;  &nbsp; No record found</strong>";
}
}
if($r==0){
echo "<strong style='color:#ed6c6c; font-size: 25px;'> &nbsp;  &nbsp;  &nbsp;  &nbsp; No record found</strong>";
}
?>
        </div>
      </div>
      <div class="clr"></div>
    </div>
  </div>
</div>
<?php }


function getEventImage2($image,$fix,$source="EventFull",$small=0)
{
	
	if ( $small ) {
		$h = 157;
		$w = 127;
	} else {
		$h = 187;
		$w = 162;
	}
	
	if ( substr($image,0,7) != 'http://' && substr($image,0,8) != 'https://' ) {
	
		if ( $image != '' && file_exists(DOC_ROOT . 'event_images/th_' . $image ) ) {
		if($fix==1){
		//	$img = returnImage( ABSOLUTE_PATH . 'event_images/th_' . $image,$w,$h );
			$img =  ABSOLUTE_PATH . 'event_images/th_' . $image;
			$img = '<img  src="'. $img .'"  width="'. $w .'" height="'. $h .'" />';	
			}
		else{
			$img = returnImage( ABSOLUTE_PATH . 'event_images/th_' . $image,$w,$h );
			$img = '<img '. $img .' />';	
		}
		} else
			$img = '<img src="' . IMAGE_PATH . 'imgg.png" width="'. $w .'" height="'. $h .'"/>';	
	} else {
		if ( $source != "EventFull")
			$img_params 	= returnImage($image,$w,$h);
		else {
			if ( strtolower(substr($image,-4,4)) != '.gif')
				$image = str_replace("/medium/","/large/",$image);
				if($fix==1){
			$img_params		= 'src="'. $image .'" width="'. $w .'" height="' . $h .'"';	
			}
			else{
			$img_params		= 'src="'. $image .'"';	
			}
		}	
		$img 			= '<img '. $img_params .' />';	
	}	
	$img	=	str_replace('height=""','', $img);
	return str_replace('width=""','', $img);
}



function getFlayerImage($image,$source="EventFull",$small=0,$w)
{

		$h = 2000;
	
	if ( substr($image,0,7) != 'http://' && substr($image,0,8) != 'https://' ) {

		if ( $image != '' && file_exists(DOC_ROOT . 'event_images/' . $image ) ) {

			$img_path	= ABSOLUTE_PATH.'event_images/'.$image;

			list($width, $height, $type, $attr) = @getimagesize($img_path);
			if($width > $w){
			$img = '<img align="center" src="'.ABSOLUTE_PATH.'event_images/'.$image.'" width="'. $w.'" />';	
				}
				else{
			$img = '<img align="center" src="'.ABSOLUTE_PATH.'event_images/'.$image.'" />';
			}
		} else
			$img = '<img src="' . IMAGE_PATH . 'imgg.png" align="center"/>';	
	} else {
		if ( $source != "EventFull")
			$img_params 	= returnImage($image,$w,$h);
		else {
			if ( strtolower(substr($image,-4,4)) != '.gif')
				$image = str_replace("/medium/","/large/",$image);
				
				list($width, $height, $type, $attr) = @getimagesize($image);
				if($width>$h){
				$img_params		= 'src="'. $image .'" width="'. $w .'" height="' . $h .'"';	
				}
				else{
			$img_params		= 'src="'. $image .'"';	
			}
		}	
		$img 			= '<img align="center" '. $img_params .' />';	
	}	
	
	return $img;
}


function generateTicket($values){
		$event_name		=	$values['event_name'];
		$ticket_number	=	$values['ticket_number'];
		$event_date		=	$values['event_date'];
		$time			=	$values['time'];
		$recipi			=	$values['recipi'];
		$state			=	$values['state'];
		$address		=	$values['address'];
		$city			=	$values['city'];
		$zip			=	$values['zip'];
		$expire_date	=	$values['expire_date'];
		$event_image	=	$values['event_image'];
		$ticket_type	=	$values['ticket_type'];
		$ticket_price	=	$values['ticket_price'];
			
		$width			=	800;
		$height			=	436;
	
		$arialbd		=	"arialbd.ttf";
		$arial			=	"arial.ttf";
				
		$im		=	imagecreatefrompng("blankticket.png");
		list($eventImageWidth, $eventImageHeight, $eventImageType, $attr) = getimagesize("event_images/eventTicketImg.gif");
		
		if($eventImageType==1){
		$src	=	imagecreatefromgif("event_images/eventTicketImg.gif");
		}elseif($eventImageType==2){
		$src	=	imagecreatefromjpeg("event_images/eventTicketImg.gif");
		}elseif($eventImageType==3){
		$src	=	imagecreatefrompng("event_images/eventTicketImg.gif");
		}
		
		$black	=	imagecolorallocate($im, 0, 0, 0);
		$yellow	=	imagecolorallocate($im, 238, 175, 12);
		$blue	=	imagecolorallocate($im, 1, 100, 165);
		
		imagettftext($im, 10, 0, 583, 68, $black, $arialbd, "Ticket # ".$ticket_number);
		
		imagettftext($im, 10, 0, 201, 120, $yellow, $arialbd, "Event");
		imagettftext($im, 13, 0, 201, 140, $black, $arialbd, $event_name);
		imagettftext($im, 9, 0, 201, 164, $black, $arialbd, "DATE:");
		imagettftext($im, 9, 0, 240, 164, $blue, $arialbd, date('l, F d, Y', strtotime($event_date)));
		imagettftext($im, 9, 0, 201, 186, $black, $arialbd, "TIME:");
		imagettftext($im, 9, 0, 240, 186, $blue, $arialbd, date('h:i A', strtotime($time)));
		imagettftext($im, 9, 0, 201, 207, $black, $arialbd, "TYPE:");
		imagettftext($im, 9, 0, 240, 207, $blue, $arialbd, $ticket_type);
		imagettftext($im, 9, 0, 201, 228, $black, $arialbd, "PRICE:");
		imagettftext($im, 9, 0, 240, 228, $blue, $arialbd, $ticket_price);
		imagettftext($im, 9, 0, 201, 261, $black, $arialbd, "RECIPIENTS:");
		imagettftext($im, 9, 0, 201, 275, $blue, $arialbd, $recipi);
		imagettftext($im, 9, 0, 302, 261, $black, $arialbd, "REDEEM AT:");
		imagettftext($im, 9, 0, 302, 275, $blue, $arialbd, $state);
		imagettftext($im, 8, 0, 302, 288, $blue, $arial, $address);
		imagettftext($im, 8, 0, 302, 300, $blue, $arial, $city.", ");
		imagettftext($im, 8, 0, 362, 300, $blue, $arial, $zip);
		imagettftext($im, 9, 0, 419, 261, $black, $arialbd, "TICKET EXPIRES:");
		imagettftext($im, 9, 0, 419, 275, $blue, $arialbd, date('l, M. d, Y', strtotime($event_date)));
		imagecopymerge($im, $src, 89, 109, 0, 0, $eventImageWidth, $eventImageHeight, 100);
		
		header ("Content-type: image/png");
		mkdir("tickets/".$folderName."", 0777);
		
		imagepng($im, 'tickets/'.$folderName.'/ticket'.$i.'.png');
		imagedestroy($im);
		
//		imagepng($im);

//		header('Content-Type: image/png');
//		header('Content-Disposition: attachment;filename="ticket'.$i.'.png"');
//		$fp=fopen('temp_ticket.png','r');
//		fpassthru($fp);
//		fclose($fp);
}

function checkForSelected($id,$event_id){
$res = mysql_query("select * from `events` where `id`='$event_id'");
while($row = mysql_fetch_array($res)){
$occupation_target	=	$row['occupation_target'];
}
$explodeOccupationTarget	=	explode(",",$occupation_target);

for ($i=0;$i<count($explodeOccupationTarget);$i++){
if($explodeOccupationTarget[$i]!=''){
if($explodeOccupationTarget[$i]==$id){
return 'checked="checked"';
}}}}



function getPurchased($user_id){
		  $res = mysql_query("select * from `orders` where `user_id`='$user_id' && `type`='product' ORDER BY `id` DESC LIMIT 0,2");
		  if(mysql_num_rows($res)){
		  while($row = mysql_fetch_array($res)){
		  $order_id	=	$row['id'];
		  $product_id	=	getSingleColumn('product_id',"select * from `order_products` where `order_id`='$order_id'");
		  $re = mysql_query("select * from `products` where `id`='$product_id'");
		  while($ro = mysql_fetch_array($re)){
		  $product_image	=	$ro['image'];
		  $product_name		=	$ro['name'];
		  $product_desc		=	DBOut($ro['desc']);
		  if($product_image){
		  if (file_exists(DOC_ROOT . 'images/products/' . $product_image ) ) {
		  $product_imageWithPath = "images/products/".$product_image;
		list($width, $height, $type, $attr) = @getimagesize($product_imageWithPath);
		list($width, $height) = getPropSize($width, $height, "180","180" );
		  $show_products_image	=	"<img src='".IMAGE_PATH."products/".$product_image."' width='".$width."' height='".$height."'/>";
		  }
		  else{
		  $show_products_image	=	"<img src='".IMAGE_PATH."small_noimage.gif' width='180' height='180' align='left' />";
		  }}
		  else{
		  $show_products_image	=	"<img src='".IMAGE_PATH."small_noimage.gif' width='180' height='180' align='left' />";
		  }
		  }
		  ?>
<div class="bx">
  <div class="ev_fltlft" style=" background: none repeat scroll 0 0 #FFFFFF; margin: 0 10px 0 0;  padding: 10px;  text-align: center;   width: 180px;height:200px;">
    <?php  echo $show_products_image; ?>
  </div>
  <div class="ev_fltlft" style="width:180px;"> <span class="dealTitle"> <?php echo $product_name; ?></span> <br />
    <span class="dealDesc"><?php echo $product_desc; ?></span>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
</div>
<?php
		  }
		  ?>
<div class="clr"></div>
<?php
		$rt = mysql_query("select * from `orders` where `user_id`='$user_id' && `type`='product'");
			if(mysql_num_rows($rt)>2){?>
<div class="ev_fltlft" style="padding:10px 0 0 25px;"><img src="<?= IMAGE_PATH; ?>prevdisable.png"></div>
<div class="ev_fltrght" style="padding:10px 25px 0 0;"><img src="<?= IMAGE_PATH; ?>nxt.png" style="cursor:pointer" onclick="loadPurchasedDeals('<?php echo ABSOLUTE_PATH;?>','next',1)"></div>
<?php } 
		  }
		  else{
		  echo "<strong style='color:#ed6c6c; font-size: 25px;'> &nbsp;  &nbsp;  &nbsp;  &nbsp; No record found</strong>";
		  }

}

function getEventStartDateFB($event_id)
{
	$dt = array();
	$sql = "select * from event_dates where event_id='". $event_id ."' AND event_date > DATE_SUB(CURDATE(),INTERVAL 1 DAY)  ORDER by event_date ASC LIMIT 1";
	$res = mysql_query($sql);
	$dates = array();
	if ( $row = mysql_fetch_assoc($res) )  {
		$dt[0] = $row['event_date'];
		$dt[1] = $row['id'];
	}
	return $dt;	
}

function getEventTime($date_id)
{

	$sql = "select * from event_times where date_id='". $date_id ."' LIMIT 1";
	$res = mysql_query($sql);
	$dates = array();
	if ( $row = mysql_fetch_assoc($res) ) 
		return $row;
		
}


function removeSpaces($table,$filed,$imageName,$path){

	if ( substr($imageName,0,7) != 'http://' && substr($imageName,0,8) != 'https://' ) {
		$space = strrpos($imageName, " ");
	if ($space == true) {
		$imageNewName = str_replace(' ', '_',$imageName);
		$oldImage = $path . $imageName;
		$newImage = $path . $imageNewName;
		copy($oldImage , $newImage);
		unlink($oldImage);

/// if th_ image found///
$root = str_replace("../","",$path);
	if ( file_exists(DOC_ROOT . $root . 'th_' . $imageName ) ) {
		$oldImageTh = $path . 'th_' . $imageName;
		$newImageTh = $path . 'th_' . $imageNewName;
		copy($oldImageTh, $newImageTh);
		unlink($oldImageTh);
	}
/////////////////////////
mysql_query("UPDATE `$table` SET `$filed` = '$imageNewName' WHERE `$filed` = '$imageName'");
	return $imageNewName;
}
else{
	return $imageName;
}}
else{
	return $imageName;
}}




function getSpecialsEvents($event_id,$type){
if($type=='flyer'){
$event_type	=	1;
}else{
$event_type	=	0;
}
$res = mysql_query("select * from `events` where `id`='$event_id' && `type`!='draft' && `event_type`='$event_type' && `event_status`='1' ORDER BY `id` ASC");
if ( $row = mysql_fetch_assoc($res) ) 
		return $row;
		
}



function getEntry($id){
$res = mysql_query("select * from `team` where `id`='$id'");
$record = array();
while($row = mysql_fetch_array($res)){
return $row;

}}



function getPollTeams($poll_id,$stype){
?>
<script>
	var container	=		$('#margin');
	var box			=		$('.voting_person', container);
	var width		=		box.innerWidth()+20;
	$(container).css('width',width*box.size());
</script>
<?php
	$session_id = session_id();
	$res = mysql_query("select * from `teams` where `poll_id`='$poll_id'");
	$numRows = mysql_num_rows($res);
	if($numRows!=0){?>
<div style="margin:auto" id="margin">
  <?php
		$alreadyVoted = mysql_query("select * from `poll_voting` where `poll_id`='$poll_id' && `session_id`='$session_id'");
		while($row = mysql_fetch_array($res)){
		$teamName	=	$row['name'];
		$teamImage	=	$row['image'];
		$teamId		=	$row['id'];
		$pollId		=	$row['poll_id'];

	if($numRows == 3){
		$imgWidth = 155;
	}
	elseif($numRows > 3){
		$imgWidth = 110;
	}
	else{
	$imgWidth = '171';
	}
	list($width, $height, $type, $attr) = @getimagesize(IMAGE_PATH.$teamImage);
	list($width, $height) = getPropSize($width, $height, $imgWidth,2000);
?>
  <div class="voting_person">
    <ul>
      <li><img src="<?php echo IMAGE_PATH.$teamImage; ?>" border="0" width="<?php echo $width; ?>" /></li>
    </ul>
    <?php
 if(mysql_num_rows($alreadyVoted) || $stype=='showresult'){
 $votes = getTotalVotes($poll_id,$teamId);
 if($votes==''){
 $votes=0;
 }
 if($votes > 1){
 $votes = $votes." Votes";
 }
 else{
 $votes = $votes." Vote";
 }
 echo '<div class="votes">'.$votes.'</div>';
 }
 else{?>
    <span id="<?php echo $teamId; ?>-<?php echo $pollId; ?>"><?php echo $teamName; ?> Win</span>
    <?php } ?>
  </div>
  <?php
}?>
  <div class="clr"></div>
</div>
<?php
  }

else{
echo "<div style='padding:30px'><strong>No record found.</strong></div>";
}}



function getTotalVotes($poll_id,$teamId){
	$res = mysql_query("SELECT SUM(vote) as totalVote FROM `poll_voting` where `poll_id`='$poll_id' && `team_id`='$teamId'");
	while($row = mysql_fetch_array($res)){
	return $row['totalVote'];
	}
}


function getTopRecomemdedFeaturedEvents()
{

 $member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
 $often_subcat = array();
 $sql = "select prefrence_type from member_prefrences where member_id='". $member_id ."' and selection='O' ";
 $res = mysql_query($sql);
 while ( $row = mysql_fetch_assoc($res) ) 
  $often_subcat[]  = $row['prefrence_type'];
 
 if ( count($often_subcat) > 0 )
  $subCat = implode(",",$often_subcat);
 else
  $subCat = 0;
 
 $often_subcat = array();
 $sql = "select prefrence_type from member_prefrences where member_id='". $member_id ."' and selection='N'";
 $res = mysql_query($sql);
 while ( $row = mysql_fetch_assoc($res) ) 
  $often_subcat[]  = $row['prefrence_type'];
 
 if ( count($often_subcat) > 0 )
  $subCatN = implode(",",$often_subcat);
 else
  $subCatN = 0;  
  

$sql = "select * from events where featured='1' AND event_status='1' ". $catFilter ." AND is_expiring=1 AND
   ( subcategory_id IN (" . $subCat . ")  AND  subcategory_id NOT IN (" . $subCatN . ") )";
 return $sql;
}


function checkValidZip($zip){
$res = mysql_query("select '$zip' IN (select zip_code from allowed_zips) as value");
while($row = mysql_fetch_array($res)){
	return $row[value];
	}
}


function getTicketDetail($ticketId){
	$res = mysql_query("select * from `event_ticket_price` where `id`='$ticketId'");
	while($row = mysql_fetch_array($res)){
		return $row;
		}
	}
	
function getDateById($dateId){
	$res = mysql_query("select * from `event_dates` where `id`='$dateId'");
	while($row = mysql_fetch_array($res)){
		return $row['event_date'];
		}
}


function validDiscountCode($code){
	
	$dsCode = '123';
	
	if($code == $dsCode){
	return '10';
	}
	else{
	return 'no';
	
	}
}

function calculateDiscount($ccode,$amount)
{
    $sql ="select * FROM coupons where code = '$ccode' and active='yes'";
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    $now = time();
 $exp_time = (int) $row['expiration_date'];
 if ($row['coupon_type'] == 3) {
  if ( $row['expires'] > 0 ) {
   if ( $now > $row['valid_from'] + ( $row['expires'] * 86400 ) ) // days set but expires
    return 0;
  } else {
   if ($now > $exp_time ) //date is expired
    return 0;
  }  
  if ($row['flat_value'] > 0) //flat value is set
   $discount = $row['flat_value'];
  else // percent value is set
   $discount = ($amount * $row['percent_value']) / 100;
   
 } else if ($row['coupon_type'] == 2) {
 
  if ($row['flat_value'] > 0) //flat value is set
   $discount = $row['flat_value'];
  else // percent value is set
   $discount = ($amount * $row['percent_value']) / 100;
   
 } else {
  $rs = mysql_query("select * from orders where coupon_code='". $ccode ."'");
  if (mysql_num_rows($rs) > 0 )
   return 0;
  else {
   if ($row['flat_value'] > 0) //flat value is set
    $discount = $row['flat_value'];
   else // percent value is set
    $discount = ($amount * $row['percent_value']) / 100;
  }
    
 }
 
 return $discount;
  
}//function



function generateTicketsPDF($order_id) {

	$sql1 = "select t.ticket_id,t.price,t.quantity,t.name as tname,t.email as temail,t.ticket_number,t.date,TIME_FORMAT( t.t_time, '%r' ) as t_time,p.* from order_tickets t, paymeny_info p where p.order_id = '". $order_id ."' AND p.order_id=t.order_id";
	$res1 = mysql_query($sql1);
	
	ob_start();
	
	while ( $rows1 = mysql_fetch_assoc($res1) ) {
		
		$ticket_package_id 	= $rows1['ticket_id'];
		$ticket_price		= $rows1['price'];
		$ticket_quantity 	= $rows1['quantity'];
		$ticket_date	 	= $rows1['date'];
		$ticket_time	 	= $rows1['t_time'];
		
		$tname			 	= $rows1['tname'];
		$temail			 	= $rows1['temail'];
		$tnumber		 	= str_pad($rows1['ticket_number'],8,"0",STR_PAD_LEFT);
		
		$fname			 	= $rows1['f_name'];
		$lname			 	= $rows1['l_name'];
		$address		 	= $rows1['address1'];
		$city			 	= $rows1['city'];
		$zip			 	= $rows1['zip'];
		
		$ticket_id 			= attribValue('event_ticket_price', 'ticket_id', "where id='$ticket_package_id'");
		$ticket_title		= attribValue('event_ticket_price', 'title', "where id='$ticket_package_id'");
		$event_id 			= attribValue('event_ticket', 'event_id', "where id='$ticket_id'");
		$event_name			= attribValue('events', 'event_name', "where id='$event_id'");
		$venue_id 			= attribValue('venue_events', 'venue_id', "where event_id='$event_id'");
		$venue_name			= attribValue('venues', 'venue_name', "where id='$venue_id'");
		$venue_addr			= attribValue('venues', 'venue_address', "where id='$venue_id'");
		$venue_city			= attribValue('venues', 'venue_city', "where id='$venue_id'");
		$image				= attribValue('events', 'event_image', "where id='$event_id'");
		
		$h = 115;
		$w = 92;
		
		$event_dateT		= getEventStartDateFB($event_id);
		
		if ( substr($image,0,7) != 'http://' && substr($image,0,8) != 'https://' ) {
		
			if ( $image != '' && file_exists(DOC_ROOT . 'event_images/th_' . $image ) ) {
				$fle = cropImage($image);
				$img = returnImage( DOC_ROOT . 'pdf/tc_' . $image,$w,$h );
				//$img = ' src="' . ABSOLUTE_PATH . 'pdf/tc_' . $image . '" ';
				$img = '<img align="left" '. $img .' />';	
			} else
				$img = '<img src="' . IMAGE_PATH . 'imgg.png" align="left" width="'. $w .'"/>';	
		} else {
			if ( $source != "EventFull")
				$img_params 	= returnImage($image,$w,$h);
			else {
				if ( strtolower(substr($image,-4,4)) != '.gif')
					$image = str_replace("/medium/","/large/",$image);
				$img_params		= 'src="'. $image .'" width="'. $w .'" height="' . $h .'"';	
			}	
			
			$img 			= '<img align="left" '. $img_params .' />';	
		}
		
		$qrCode = generateQRCode($tnumber);
		$qrCode = DOC_ROOT.'temp_qrcodes' . DIRECTORY_SEPARATOR . $qrCode;
	?>
<table width="840" border="0" cellspacing="0" cellpadding="0" align="center" >
  <tr>
    <td style="padding:10px; border:#ECECEC solid 1px" ><table width="820" border="0" cellspacing="0" cellpadding="0" align="center" >
        <tr>
          <td width="380" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
              <tr>
                <td valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
                    <tr>
                      <td class="imag_ticket" valign="top" height="126"><div class="divInner" > <?php echo $img ;?> </div></td>
                      <td align="left" valign="top" style="padding-left:10px"><span class="event_name"><?php echo $event_name ;?></span> <br>
                        <span class="event_date"><?php echo date("M d, Y",strtotime($event_dateT[0]));?></span> </td>
                    </tr>
                  </table></td>
              </tr>
              <tr>
                <td align="left" class="ticket">e-Ticket</td>
              </tr>
              <tr>
                <td align="left" class="ticket_sub">Admit one person only</td>
              </tr>
              <tr>
                <td align="left" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="87%" class="ticket_sub1">Without ticket there is no guaranteed access.<br>
                        Barcodes are unique. Copying is useless.</td>
                      <td width="46"><img src="<?php echo  $qrCode;?>" width="46" height="46"></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
          <td align="center" valign="top"><img src="<?php echo DOC_ROOT; ?>images/barcode_vertical.jpg" width="34" height="264"></td>
          <td width="390" align="right" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0" align="right">
              <tr>
                <td align="center" ><!-- <img src="ticket_images/barcode.jpg" > -->
                  <barcode code="<?php echo $tnumber;?>" type="C39E" class="barcode" />
                  <br>
                  <span style="font-size:18px;">eTicket # <strong><?php echo $tnumber;?></strong></span> </td>
              </tr>
              <tr>
                <td >&nbsp;</td>
              </tr>
              <tr>
                <td ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="109" class="labels">Buyer:</td>
                      <td width="275" class="labels"><?php echo $tname ;?> &nbsp;  (<?php echo $temail; ?>)</td>
                    </tr>
                    <tr>
                      <td class="labels">Date:</td>
                      <td class="labels"><?php echo $ticket_date ;?></td>
                    </tr>
                    <tr>
                      <td class="labels">Venue:</td>
                      <td class="labels"><?php echo $venue_name ;?></td>
                    </tr>
                    <tr>
                      <td class="labels">Address:</td>
                      <td class="labels"><?php echo $venue_addr . ' ' . $venue_city ;?></td>
                    </tr>
                    <tr>
                      <td class="labels">Time:</td>
                      <td class="labels"><?php echo $ticket_time; ?></td>
                    </tr>
                    <tr>
                      <td class="labels">Price:</td>
                      <td class="labels">$ <?php echo number_format($ticket_price,2) ;?></td>
                    </tr>
                    <tr>
                      <td class="labels">Type:</td>
                      <td class="labels"><?php echo $ticket_title;?></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td style="background-image:url(images/ticket_cutter.jpg); background-repeat:no-repeat; height:33px">&nbsp;</td>
  </tr>
</table>
<?php
	
	}
	
	$html = ob_get_contents();
	ob_clean();
	
	$filename = 'eTicket_'. time() . '.pdf';
	
	include(DOC_ROOT."mpdf/mpdf.php");
	
	$mpdf=new mPDF('c','A4-L');
	$mpdf->list_indent_first_level = 0;
	$stylesheet = file_get_contents(DOC_ROOT.'ticket_css.css');
	$mpdf->WriteHTML($stylesheet,1);
	$mpdf->WriteHTML($html);
	$mpdf->Output(DOC_ROOT.'pdf/'.$filename, 'F');
	
	if ( $fle != '' )
		@unlink($fle);
	@unlink($qrCode);
	return 'pdf/'.$filename;
}

function cropImage($bc_image)
{
	
	makeThumbnail($bc_image, DOC_ROOT.'event_images/', DOC_ROOT.'pdf/', 92, 150,'tic_');

	$filename = DOC_ROOT.'pdf/' . $bc_image;
	$filename1 = DOC_ROOT.'pdf/tc_' . $bc_image;
 
	list($current_width, $current_height, $type) = getimagesize($filename);
	 
	$left = 0;
	$top = 0;
	 
	$crop_width = 92;
	$crop_height = 115;
	 
	$canvas 		= imagecreatetruecolor($crop_width, $crop_height);
	
	if($type == 1){ // 1 = GIF, 2 = JPG, 3 = PNG
		$current_image 	= imagecreatefromgif($filename);
		imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
		imagegif($canvas, $filename1, 100);
	}
	elseif($type == 2){
		$current_image 	= imagecreatefromjpeg($filename);
		imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
		imagejpeg($canvas, $filename1, 100);
	}
	elseif($type == 3){
		$current_image 	= imagecreatefrompng($filename);
		imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
		imagepng($canvas, $filename1, 9);
	}
	
	
	
	
	
	@unlink($filename);
	return $filename1;
}

function generateQRCode($code)
{
	$PNG_TEMP_DIR = DOC_ROOT.'temp_qrcodes' . DIRECTORY_SEPARATOR;
	$file = time() . '.png';
	$filename = $PNG_TEMP_DIR . $file ;
	$errorCorrectionLevel = 'L'; //  L, M, Q, H
	$matrixPointSize = 5;
	
	//$filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
	QRcode::png($code, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
	
	return $file;
}



function generateVouchersPDF($id){
	$res = mysql_query("select * from `nba_package_order` where `id`='$id'");
	while($row = mysql_fetch_array($res)){
		$package_name	= $row['package_name'];
		$fname			= $row['fname'];
		$lname			= $row['lname'];
		$tname			= $fname." ".$lname;
		$temail 		= $row['email'];
		$address		= $row['address'];
		$amount			= $row['amount'];
		$tnumber		= str_pad($row['id'],8,"0",STR_PAD_LEFT);
	}
	ob_start();
?>
<table width="840" border="0" cellspacing="0" cellpadding="0" align="center" >
  <tr>
    <td style="padding:10px; border:#ECECEC solid 1px" ><table width="820" border="0" cellspacing="0" cellpadding="0" align="center" >
        <tr>
          <td width="380" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
              <tr>
                <td align="left" valign="top" style="padding-left:10px"><span class="event_name"><?php echo $package_name ;?></span> <br>
                        <!--<span class="event_date"><?php echo date("M d, Y",strtotime($event_dateT[0]));?></span>-->
                      </td>
              </tr>
              <tr>
                <td align="left" class="ticket">Voucher</td>
              </tr>
              <tr>
                <td align="left" class="ticket_sub">Admit one person only</td>
              </tr>
              <tr>
                <td align="left" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="87%" class="ticket_sub1">Without voucher there is no guaranteed access.<br>
                        Barcodes are unique. Copying is useless.</td>
                      <td width="46"><!--<img src="<?php echo  $qrCode;?>" width="46" height="46">--></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
          <td align="center" valign="top"><img src="<?php // echo DOC_ROOT; ?>images/barcode_vertical.jpg" width="34" height="160"></td>
          <td width="390" align="right" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0" align="right">
              <tr>
                <td align="center" ><!-- <img src="ticket_images/barcode.jpg" > -->
                  <barcode code="<?php echo $tnumber;?>" type="C39E" class="barcode" />
                  <br>
                  <span style="font-size:18px;">Voucher # <strong><?php echo $tnumber;?></strong></span> </td>
              </tr>
              <tr>
                <td >&nbsp;</td>
              </tr>
              <tr>
                <td ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="109" class="labels">Buyer:</td>
                      <td width="275" class="labels"><?php echo $tname ;?> &nbsp;  (<?php echo $temail; ?>)</td>
                    </tr>
                    <tr>
                      <td class="labels">Date:</td>
                      <td class="labels"><?php echo date('d M Y');?></td>
                    </tr>
                    <tr>
                      <td class="labels">Price:</td>
                      <td class="labels">$<?php echo number_format($amount,2) ;?></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="right"><img src="<?php echo IMAGE_PATH; ?>fb_footer.png" border="0" /> </td>
  </tr>
  <tr>
    <td style="background-image:url(images/ticket_cutter.jpg); background-repeat:no-repeat; height:33px">&nbsp;</td>
  </tr>
</table>
<?php
$html = ob_get_contents();
	ob_clean();	

$filename = 'Voucher_'. time() . '.pdf';
include(DOC_ROOT."mpdf/mpdf.php");
$mpdf=new mPDF('c','A4-L');
	$mpdf->list_indent_first_level = 0;
	$stylesheet = file_get_contents(DOC_ROOT.'ticket_css.css');
	$mpdf->WriteHTML($stylesheet,1);
	$mpdf->WriteHTML($html);
	$mpdf->Output(DOC_ROOT.'pdf/'.$filename, 'F');	
	return  'pdf/'.$filename;
}


function getDays($specials_id){
	$ress = mysql_query("select * from `special_event` where `specials_id`='$specials_id' ");
	$eventDaysName =  '';
	while($rows = mysql_fetch_array($ress)){
		$t_event_id = $rows['event_id'];
		
		$event_type	= getSingleColumn('event_type',"select * from `events` where id=$t_event_id");
		
		//if($event_type=='0'){
//			$days = getEventDatesInArray($t_event_id);
//			if(is_array($days)){
//				foreach($days as $dates){
//					 $eventDaysName.=date('l', strtotime($dates)).",";	
//				}
//			}
//		}
	if($event_type==0){
			$event_dateT	= getEventStartDateFB($t_event_id);
			$event_date		.= date('l', strtotime($event_dateT[0])).",";	
			
			}
	}
	$a = explode(",",$event_date);
	$d = array();
	foreach($a as $days){
		if($days!=''){
			if($days == 'Monday')
				$index=0;
			elseif($days == 'Tuesday')
				$index=1;
			elseif($days == 'Wednesday')
				$index=2;
			elseif($days == 'Thursday')
				$index=3;
			elseif($days == 'Friday')
				$index=4;
			elseif($days == 'Saturday')
				$index=5;
			elseif($days == 'Sunday')
				$index=6;
			$d[$index] = $days;
		}
	}
	$ds = array();
	ksort($d);
	foreach($d as $a){
		$ds[] = strtoupper($a);
	}
	
	return $ds;
}




function downloadRSVP($event_id){


echo "";



}
?>
