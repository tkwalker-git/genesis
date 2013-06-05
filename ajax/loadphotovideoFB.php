<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$event_id	=	$_POST['event_id'];
	$active		=	'photovideo';

$sql = "select * from events where id='". $event_id ."'";
$res = mysql_query($sql);

if ( $row = mysql_fetch_assoc($res) ) {
	
	$video_embed		= $row['video_embed'];
	$event_description	= DBout($row["event_description"]);
	$event_image		= $row["event_image"];
	$bc_alter			= $row['alter'];
	$bc_alter_url		= $row['alter_url'];
	$event_description_s = strip_tags($event_description);
	
//	$event_description_s = breakStringIntoMaxChar($event_description_s,200);
		
	if (trim($event_image) != '') {

		if ( substr($event_image,0,7) != 'http://' && substr($event_image,0,8) != 'https://' ) {
			$image = ABSOLUTE_PATH .'event_images/th_' . $event_image;
			$imageE = ABSOLUTE_PATH_SECURE .'event_images/' . $event_image;
			
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
		 

	$res = mysql_query("select * from `event_videos` where `event_id`='$event_id' ORDER BY `id` ASC LIMIT 0,1");
	while($row = mysql_fetch_array($res)){
		$video_embed	= $row['video_embed'];
		$video_name		= $row['video_name'];
		$video_id		= $row['id'];

		if (preg_match('/src="([^"]*)"/i', $video_embed , $regs))
		$src = $regs[1];
		$src.='?wmode=transparent';
		$linka	= explode($regs[0],$video_embed);
		$video_embed	= $linka[0] .' src="'. $src .'" '. $linka[1];
	}

 include("../flayerMenuFB.php");
?>
	<script>
        $(document).ready(function(){
        // FOR IMAGES START
			var ul				= $(".images",".eventImagesGallery");
			var li				= $("li",ul);
			var count_images	= li.size();
			var liWidth			= li.innerWidth();
			var pading			= 3;
			var current			= 0;
			var valid			= 0;
			$('.images').css("width",(liWidth+pading) * count_images);
			$('#nxtIMg').click(function(){
				if((count_images-4)!=valid){
					valid++;
					current--;
					var left = liWidth+pading;
					left = left*current;
					$(ul).animate({'margin-left' : left}, 500);
				}
			});
			$('#prvIMg').click(function(){
				if(current!=0){
					valid--;
					current++;
					var left = liWidth+pading;
					left = left*current;
					$(ul).animate({'margin-left' : left}, 500);
				}
			});
        // FOR IMAGES END
        
		// FOR VIDEO START
			$('.nxtPrvVideo').click(function(){
				var attr	= $(this).attr('rel').split('-');
				var video_id	= attr[0];
				var event_id	= attr[1];
				var type		= attr[2];

				$.ajax({  
					type: "POST",  
					url: "/ajax/loadvideo.php",  
					data:"video_id="+video_id+"&event_id="+event_id+"&type="+type,
					beforeSend: function()
					{
					showOverlayer('/ajax/loader.php');
					},
					success: function(resp)
					{  
					$("#vidArea").html(resp);
					}, 
					
					complete: function()
					{
					hideOverlayer();
					},
					
					error: function(e)
					{  
					//alert('Error: ' + e);  
					}  
				});
			});
		// FOR VIDEO END
        });
    </script>

<style>
	#nxtIMg{
		cursor:pointer;
		position: absolute;
		right: 0;
		top: 35px;
		z-index: 50;
		}
	#prvIMg{
		cursor:pointer;
		position: absolute;
		left: 0;
		top: 35px;
		z-index: 50;
		}
</style>

     	<div class="inrDiv">
            <div class="event-name-big2">Photo & Video Gallery</div>
            <div class="new_flayer_title"><br><?php $gallery_name = getSingleColumn('name',"select * from `event_gallery` where `event_id`='$event_id'");
            if($gallery_name){ echo $gallery_name;}else{
            echo "Event Photo:";} ?></div>
            <div class="eventImagesGallery">
            <?php 
            $gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$event_id'");
             $res = mysql_query("select * from `event_gallery_images` where `gallery_id`='$gallery_id'");
             $numRows = mysql_num_rows($res);
             if($numRows > 4){ ?>
                <span id="nxtIMg"><img src="<?php echo IMAGE_PATH; ?>imgnxt.png" /></span>
                <span id="prvIMg"><img src="<?php echo IMAGE_PATH; ?>imgprv.png" /></span>
            <?php
             }
             ?>
             <ul class="images">
             <?php
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
            }
            else{
                $style = "";
            }
            ?>
            <li <?php echo $style; ?> ><a style="max-height:71px; overflow:hidden; display:block; cursor:pointer" onclick="showimage('/','<?php echo '/event_images/gallery/sub_'.$row['image']; ?>')"><img src="<?php echo EVENT_IMAGE_PATH.'gallery/th_'.$row['image']; ?>" height="<?php echo $vih; ?>" width="<?php echo $viw; ?>" alt="" title=""></a></li>
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
                <div id="vidArea">
                    <div class="new_flayer_title"><?php if($video_name){ echo $video_name; }else { echo "Event Video:";}?><br />
                    <br />
                    </div>
                    <div class="videoBox" style="padding:0">
                        <div style="padding:12px; position:relative; width:460px">
                        <?php $num_videos	= getSingleColumn("tot","select COUNT(*) as tot from `event_videos` where `event_id`='". $event_id ."'"); ?>
                            <div style="width:460px; overflow:hidden;">
                                <?php
                                if($num_videos > 1){ ?>
                                <span class="nxtPrvVideo" rel="<?php echo $video_id; ?>-<?php echo $event_id ?>-nxt" style="cursor:pointer;position:absolute;right:0;top:153px;z-index:50;"><img src="<?php echo IMAGE_PATH; ?>imgnxt.png" /></span>
                                    <span style="position:absolute;left:0;top:153px;z-index:50;"><img src="<?php echo IMAGE_PATH; ?>imgprv.png" /></span>
                                <?php } echo $video_embed; ?>
                            </div>
                        </div>
                </div> <!-- /vidArea -->
            <?php
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