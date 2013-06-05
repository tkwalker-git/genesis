<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$event_id	=	$_POST['event_id'];
	$active='photovideo';

$sql = "select * from events where id='". $event_id ."'";
$res = mysql_query($sql);

if ( $row = mysql_fetch_assoc($res) ) {
	$event_description	= DBout($row["event_description"]);
	$event_image		= $row["event_image"];
	$bc_alter			= $row['alter'];
	$bc_alter_url		= $row['alter_url'];
	$event_description_s = strip_tags($event_description);
//	$event_description_s = breakStringIntoMaxChar($event_description_s,200);
		
	if (trim($event_image) != '') {

		if ( substr($event_image,0,7) != 'http://' && substr($event_image,0,8) != 'https://' ) {
			$image = ABSOLUTE_PATH .'event_images/th_' . $event_image;
			$imageE = ABSOLUTE_PATH .'event_images/' . $event_image;
			
		} else {
			if ( $source == "EventFull") {
				if ( strtolower(substr($event_image,-4,4)) != '.gif')
					$image = str_replace("/medium/","/large/",$event_image);	
			}else {
				$image = $event_image;
			}	
		}
		$img_params = returnImage($image,272,375); 
		
	} else {
		$img_params = ' src="'. ABSOLUTE_PATH .'images/bigAvatar_photo.png" height="376" width="272" border="0" ';
		$kk = 1;
	}	
	
	if ( $imageE != '' ) {
		$image_display = '<a id="eventImage" href="'. $imageE .'" ><img align="center" '. $img_params .' /></a>';	
	} else {
		if ( $kk == 1 )	
			$image_display = '<img align="center" '. $img_params .' />';	
		else
			$image_display = '<a id="eventImage" href="'. $image .'" ><img align="center" '. $img_params .' /></a>';	
	}		
	
	$page_url = ABSOLUTE_PATH . 'category/' . $category_seo . '/' . $sub_cat_seo . '/' . $event_seo_name . '.html';
	
}


 $gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$event_id'");
		 $res = mysql_query("select * from `event_gallery_images` where `gallery_id`='$gallery_id'");
		 if(mysql_num_rows($res)){
		 $i = 0;
		 while($row = mysql_fetch_array($res)){
		 $i++;
		 if($i==1){
		 $first_image = EVENT_IMAGE_PATH.'gallery/'.$row['image'];
		 }
		 }}

$res = mysql_query("select * from `event_videos` where `event_id`='$event_id'");
	while($row = mysql_fetch_array($res)){
	$video_embed = $row['video_embed'];
	$video_name = $row['video_name'];
	}
	
	 include("../flayerMenu.php"); ?>
		
		<div class="inrDiv">
      <div class="event-name-big2">Event Guide and Experience</div>
		<div class="new_flayer_title"><br><?php $gallery_name = getSingleColumn('name',"select * from `event_gallery` where `event_id`='$event_id'");
		if($gallery_name){ echo $gallery_name;}else{
		echo "Event Photo:";} ?></div>
        <div class="eventImagesGallery">
         <ul class="images">
		 <?php
		 $gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$event_id'");
		 $res = mysql_query("select * from `event_gallery_images` where `gallery_id`='$gallery_id'");
		 $numRows = mysql_num_rows($res);
		 if($numRows){
		 $i = 0;
		 while($row = mysql_fetch_array($res)){
		 $i++;
		 if($i==1){
		 $first_image = EVENT_IMAGE_PATH.'gallery/'.$row['image'];
		 }
		list($viw, $vih) = getimagesize(EVENT_IMAGE_PATH.'gallery/'.$row['image']);
		list($viw, $vih) = getPropSize($viw, $vih, 107,1000);
		if($numRows==$i){
		$style = "style='margin:0'";
		}else{
		$style = "";
		}
		?>
		<li <?php echo $style; ?> ><a style="max-height:71px; overflow:hidden; display:block; cursor:pointer" onclick="showimage('<?php echo ABSOLUTE_PATH; ?>','<?php echo ABSOLUTE_PATH.'event_images/gallery/'.$row['image']; ?>')"><img src="<?php echo EVENT_IMAGE_PATH.'gallery/th_'.$row['image']; ?>" height="<?php echo $vih; ?>" width="<?php echo $viw; ?>" alt="" title=""></a></li>
		<?php }}
		else{
		echo "<li style='text-align:center'><img src='".IMAGE_PATH."small_noimage.gif'></li>";
		}
		?>
		<div class="clr"></div>
	      </ul><br />
		   <div class="clr"></div>
        </div>
        <div class="clr"></div>
		<?php
		
		if($video_embed){
		?>
		<div class="new_flayer_title"><?php if($video_name){ echo $video_name; }else { echo "Event Video:";}?><br />
<br />
</div>
		<div class="videoBox">
		<?php
	echo '<div style="width:458px; overflow:hidden">'.$video_embed.'</div>';
		}else{?>
		 <div class="locationBox">
		<div class="img">
		<?php
		if($first_image){
		list($viw, $vih, $type33, $attr33) = getimagesize($first_image);
		list($viw, $vih) = getPropSize($viw, $vih, 458,2000);
		?>
		<img src="<?php echo $first_image; ?>" width="<?php echo $viw; ?>" height="<?php echo $vih; ?>" />
		<?php } ?>
		</div></div>
		<?php } ?></div>
		<div class="clr"></div>
		
		</div>
		