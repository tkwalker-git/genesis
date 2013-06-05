<?php
	require_once('../admin/database.php');
	$video_id	= $_POST['video_id'];
	$event_id	= $_POST['event_id'];
	$type		= $_POST['type'];
	
	if($type == 'nxt'){
		$cand	= ">";
		$sort	= " ASC ";
	}
	else{
		$cand	= "<";
		$sort	= " DESC ";
	}
		
	$sql = "select * from `event_videos` where `id` ". $cand ." ". $video_id ." && `event_id` = '". $event_id ."' ORDER BY `id` ". $sort ." LIMIT 0,1";
	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res)){
    	$video_embed	= $row['video_embed'];
		$video_name		= $row['video_name'];
		$video_id		= $row['id'];

		if (preg_match('/src="([^"]*)"/i', $video_embed , $regs))
		$src = $regs[1];
		$src.='?wmode=transparent';
		$linka	= explode($regs[0],$video_embed);
		$video_embed	= $linka[0] .' src="'. $src .'" '. $linka[1];
    ?>
        <div class="new_flayer_title">
			<?php if($video_name){ echo $video_name; }else { echo "Event Video:";}?><br />
            <br />
            </div>
            <div class="videoBox" style="padding:0">
            <div style="padding:12px; position:relative; width:460px">
            <?php $num_videos	= getSingleColumn("tot","select COUNT(*) as tot from `event_videos` where `event_id`='". $event_id ."'"); ?>
            <div style="width:460px; overflow:hidden;">
            <?php
				$nextVid	= getSingleColumn("id","select * from `event_videos` where `id` > ". $video_id ." && `event_id` = '". $event_id ."' ORDER BY `id` ASC LIMIT 0,1");
				$prvVid		= getSingleColumn("id","select * from `event_videos` where `id` < ". $video_id ." && `event_id` = '". $event_id ."' ORDER BY `id` DESC LIMIT 0,1");
		    if($nextVid){ ?>
	            <span class="nxtPrvVideo" rel="<?php echo $video_id; ?>-<?php echo $event_id ?>-nxt" style="cursor:pointer;position:absolute;right:0;top:153px;z-index:50;"><img src="<?php echo IMAGE_PATH; ?>imgnxt.png" /></span>
            <?php }
			else{ ?>
            	<span style="position:absolute;right:0;top:153px;z-index:50;"><img src="<?php echo IMAGE_PATH; ?>imgnxt.png" /></span>
			<?php	}
			if($prvVid){ ?>
            	<span class="nxtPrvVideo" rel="<?php echo $video_id; ?>-<?php echo $event_id ?>-prv" style="cursor:pointer;position:absolute;left:0;top:153px;z-index:50;"><img src="<?php echo IMAGE_PATH; ?>imgprv.png" /></span>
            <?php }
			else{ ?>
            	<span style="position:absolute;left:0;top:153px;z-index:50;"><img src="<?php echo IMAGE_PATH; ?>imgprv.png" /></span>
            <?php } echo $video_embed; ?>
            </div>
        </div>
    <?php  } ?>
	
    <script>
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
	</script>