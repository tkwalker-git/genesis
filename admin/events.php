<?php

require_once("database.php"); 
require_once("header.php"); 

//$bc_source_id	=	"Admin-".rand();
$bc_event_source = 'Admin';

if(isset($_GET["id"]))
	$frmID	=	$_GET["id"];

if($_GET["type"]=='flyer')
	$bc_event_type	=	'1';
elseif($_GET["type"]=='premium')
	$bc_event_type	=	'2';
elseif($_GET["type"]=='showcase')
	$bc_event_type	=	'3';
else
	$bc_event_type	=	'0';

$action = "save";
$sucMessage = "";

$repeat_arr = array("day" => "Event Happens Daily","week"=>"Event Happens Weekly","month"=>"Event Happens Monthly");

$errors = array();
$bc_event_music 		=	array();
$bc_occupation_array	=	array();

if (isset($_POST["create"]) || isset($_POST["create"]) ) {
	$bc_add_venue			=	DBin($_POST['addvenue']);
	$bc_event_name			=	DBin($_POST["event_name"]);
	$bc_event_description	=	DBin($_POST["event_description"]);
	$bc_category_id			=	DBin($_POST["category_id"]);
	$bc_subcategory_id		=	DBin($_POST["subcategory_id"]);
	$bc_event_age_suitab	=	DBin($_POST['min_age_allow']);
	$bc_event_status		=	($bc_add_venue == "clicked" ? $_POST["event_status"] = "0" : DBin($_POST["event_status"]));

	if($bc_event_status == '')
		$bc_event_status	=	0;

	$bc_men_preferred_age	=	DBin($_POST['men_preferred_age']);
	$bc_women_preferred_age	=	DBin($_POST['women_preferred_age']);
	$bc_event_music			=	$_POST['event_music'];
	$bc_occupation_target	=	$_POST['occupation_target'];
	$bc_occupation_array	=	$_POST['occupation_target'];
	$bc_gallery				=	DBin($_POST['gallery']);
	$bc_video_name			=	$_POST['video_name'];
	$bc_video_embed			=	$_POST['video_embed'];
	$bc_venue_id				=	$_POST['venue_id'];

	$zipcode	= getSingleColumn("venue_zip","select * from `venues` where `id`='$bc_venue_id'");

	$bc_modify_date			=	date("Y-m-d");
	$bc_event_host			=	DBin($_POST['event_host']);
	$bc_host_description	=	DBin($_POST['host_description']);
	$bc_averagerating		=	DBin($_POST["averagerating"]);
	$bc_added_by			=	DBin($_POST["added_by"]);
	$occurrences			=	$_POST['occurrences'];
	$bc_featured			=	$_POST['featured'];
	$bc_free_event			=	$_POST['free_event'];
	$bc_specials			=	$_POST['specials'];
	$bc_event_type			=	$_POST['event_type'];
	$bc_alter				=	$_POST['alter'];
	if($bc_alter)
		 $bc_alter_url  =	$_POST['alter_url'];



	$bc_event_end_time		=	$_POST['event_end_time'];

	$bc_seo_name			=	make_seo_names($_POST["event_name"],"events","seo_name","");

	$frmID					=	$_POST['frmID'];
	if($bc_gallery=='Create a name for your image gallery (i.e. Dress Code)'){
		$bc_gallery='';
	}

	$bc_event_cost			=	$_POST['event_cost'];
	
	if($bc_event_status==1){
	$bc_is_expiring = 1;
	}
	
	$sucMessage = "";
	
	$errors = array();
	if ( trim($bc_category_id) == '' )
		$errors[] = 'Please select Primary Category ';
	if ( trim($bc_subcategory_id) == '' )
		$errors[] = 'Please select Secondary Category';
	if ( trim($bc_event_name) == '' )
		$errors[] = 'Please enter Eevent Name';
	if ( trim($bc_event_description) == '' )
		$errors[] = 'Please enter Eevent Details';
	if ( count($occurrences) < 1)
		$errors[] = 'Select single date for repeat event';
	if($_POST['noTicket']=='no' || $bc_free_event=='0'){
		if ( trim($bc_event_cost) == '' )
			$errors[] = 'Please enter Event Cost';
	}
	else{
		if ( trim($bc_event_cost) == '' && $bc_free_event!=1 )
			$errors[] = 'Please enter Event Cost';
		}
	
	if ( trim($bc_venue_id) == '' )
		$errors[] = 'Please Select Venue.';
	
	if($bc_alter && $bc_alter_url=='')
		$errors[] = 'Please write Alternative Buy Ticke URL.';
			
		
	if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';
	}
	
	if (!count($errors)) {
	
		$bc_image = '';
		if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
			$bc_image  = time() . "_" . $_FILES["event_image"]["name"] ;
			$bc_image	=	str_replace(" ","_", $bc_image);
			if ($action1 == "edit") {
				deleteImage($frmID,"events","event_image");
			}
			move_uploaded_file($_FILES["event_image"]["tmp_name"], '../event_images/' .$bc_image);
			makeThumbnail($bc_image, '../event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
		
if($bc_alter_url){
	if ( substr($bc_alter_url,0,7) == 'http://' || substr($bc_alter_url,0,8) == 'https://' )
		$bc_alter_url = $bc_alter_url;
	else
		$bc_alter_url = 'http://'.$bc_alter_url;
}
			

if($bc_occupation_target){
	$bc_occupation_targets	=	'';
	for($d=0;$d<count($bc_occupation_target);$d++){
	
	$bc_occupation_targets.=	$bc_occupation_target[$d].",";
	
	}}
	
	if($frmID){
	$bs	=	getSingleColumn('id',"select * from `events` where `event_name`='$bc_event_name' && `id`='$frmID'");
		if($bs){
		$bc_seo_name	=	'';
		}
		else{
		$bc_seo_name	=	"`seo_name` = '$bc_seo_name', ";
		}
		
	$is_private	= getSingleColumn('is_private',"select * from `events` where `id`='$frmID'");
	
	if($is_private)
		$bc_event_status	= 0;

	$sql = "UPDATE `events` SET `category_id` = '$bc_category_id', `subcategory_id` = '$bc_subcategory_id', `event_name` = '$bc_event_name',  $bc_seo_name `event_description` = '$bc_event_description', `event_cost` = '$bc_event_cost', $sql_img `event_age_suitab` = '$bc_event_age_suitab', `event_status` = '$bc_event_status', `modify_date` = '$bc_modify_date', `men_preferred_age` = '$bc_men_preferred_age', `women_preferred_age` = '$bc_women_preferred_age', `occupation_target` = '$bc_occupation_targets', `video_name` = '$bc_video_name', `video_embed` = '$bc_video_embed', `event_end_time` = '$bc_event_end_time', `type` = '', `featured`='$bc_featured', `free_event`='$bc_free_event', `is_expiring`='$bc_is_expiring',`added_by`='". $bc_added_by ."', `alter`='$bc_alter', `alter_url`='$bc_alter_url', `zipcode`='$zipcode'  WHERE `id` = '$frmID'";
 	$res = mysql_query($sql);
if($res){

mysql_query("DELETE from `event_videos` where `event_id`='$frmID'");

		$event_id	=	$frmID;
		
		$re = mysql_query("select * from `event_dates` where `event_id`='$event_id'");
		while($ro = mysql_fetch_array($re)){
		$date_id = $ro['id'];
		mysql_query("DELETE FROM `event_times` WHERE `date_id`='$date_id'");
		}
		mysql_query("DELETE from `event_dates` where `event_id`='$event_id'");
		mysql_query("DELETE from `event_music` where `event_id`='$event_id'");
		mysql_query("DELETE from `venue_events` where `event_id`='$event_id'");		
		
		
	$gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$frmID'");
	
	if($gallery_id){
		mysql_query("UPDATE `event_gallery` SET `name` = '$bc_gallery' WHERE `event_id` = '$event_id'");
	}
	else{
			mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$bc_gallery', '$event_id')");
			$gallery_id = mysql_insert_id();
	}
	
	if ( is_array($_FILES['images']) && $gallery_id!='' ) {
			for($i=0;$i< count($_FILES['images']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, '../event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, '../event_images/gallery/', '', 107, 200,'th_');
					makeThumbnail($ei_image, '../event_images/gallery/', '', 199, 140,'sub_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}
		
		
		$sucMessage = "Event Successfully updated";
		
		
		}else{
			$sucMessage = "Error: Please try Later";
		}
	
	}
	
	else{
	
		$bc_source_id		=	"Admin-".rand(); 
		$bc_publishdate	=	 date("Y-m-d");
		
		
$sql	=	"insert into events (event_source,source_id,userid,category_id,subcategory_id,event_name,seo_name,musicgenere_id,event_description,event_cost,event_image,event_sell_ticket,event_age_suitab,event_status,publishdate,averagerating,modify_date,del_status,added_by,men_preferred_age,women_preferred_age,occupation_target,video_name,video_embed,repeat_event,repeat_freq,tags,privacy,pending_approval,event_end_time,type,featured,free_event,event_type,`alter`,`alter_url`, `zipcode`) values ('" .$bc_event_source . "','" .$bc_source_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_seo_name . "','" . $bc_musicgenere_id . "','" . $bc_event_description . "','" . $bc_event_cost . "','" . $bc_image . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "','".$bc_men_preferred_age. "','".$bc_women_preferred_age. "','" . $bc_occupation_targets . "','".$bc_video_name. "','".$bc_video_embed. "','".$repeat."','".$frequency."','','". $privacy ."','0','$bc_event_end_time','','$bc_featured','$bc_free_event','$bc_event_type','$bc_alter','$bc_alter_url','$zipcode')";
		
		$res	=	mysql_query($sql);
		$frmID	=	mysql_insert_id();
		if ($res) {
		$event_id	=	$frmID;
		mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$bc_gallery', '$event_id')");
	$gallery_id	=	mysql_insert_id();

	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, '../event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, '../event_images/gallery/', '', 107, 200,'th_');
					makeThumbnail($ei_image, '../event_images/gallery/', '', 199, 140,'sub_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}
		
		$sucMessage = "Record Successfully inserted.";
		
		} // end if $res
		else {
			$sucMessage = "Error: Please try Later";
		}	
			}
	if($res){
	
	///// ADD VIDEO START /////
			if ( is_array($bc_video_embed) ) {
	
				for($f=0;$f <count($bc_video_embed);$f++) {
					$video_embed =	$bc_video_embed[$f];
					$video_name	=	$bc_video_name[$f];
					if($video_name == 'Enter the name of your video')
						$video_name = '';
						if($video_embed!=''){
						mysql_query("INSERT INTO `event_videos` (`id`, `video_name`, `video_embed`, `event_id`) VALUES (NULL, '$video_name', '$video_embed', '$frmID')");
						}
					}
				}
	///// ADD VIDEO END /////
	
	if($bc_specials!=''){
	$bc_specials_id	=	getSingleColumn('id',"select * from `special_event` where `event_id`='$frmID'");
	if($bc_specials_id){
	mysql_query("UPDATE `special_event` SET `specials_id` = '$bc_specials' WHERE `id` = '$bc_specials_id'");
	}
	else{
	mysql_query("INSERT INTO `special_event` (`id`, `event_id`, `specials_id`) VALUES (NULL, '$frmID', '$bc_specials')");
	}
	}
	
	
	if (isset($_POST['occurrences']) && $_POST['occurrences'] != ''){
	$date			=	array();
	$start_time		=	array();
	$start_am_pm	=	array();
	$end_time		=	array();
	$end_am_pm		=	array();
	
	foreach($occurrences as $v){
	
	$date[]			=	$v['date'];
	$start_time[]	=	$v['start_time'];
	$start_am_pm[]	=	$v['start_am_pm'];  // AM = 0, PM = 1
	$end_time[]		=	$v['end_time'];
	$end_am_pm[]	=	$v['end_am_pm'];	// AM = 0, PM = 1
	
	}
	for($i=0;$i<count($date);$i++){
	
	$one_date	=	date('Y-m-d',strtotime($date[$i]));
	

////// FOR START & END Time //////
	$startTime	=	'';
	$endTime	=	'';
	$startTime = $start_time[$i];
	if($start_am_pm[$i]==0)
		$startTime .= " AM";
	else
		$startTime .= " PM";
	$startTime = date("H:i", strtotime($startTime));
	if($end_time[$i]!='' && $end_time[$i]!='00:00:00' && $end_time[$i]!='00:00'){
		$endTime = $end_time[$i];
		if($end_am_pm[$i]==0)
			$endTime .= " AM";
		else
			$endTime .= " PM";
		$endTime = date("H:i", strtotime($endTime));
	}

//////////////////////////////////
	$sql_date	=	mysql_query("insert into event_dates (event_id, event_date) values('" . $event_id . "','" . $one_date . "')");
	$date_id	=	mysql_insert_id();
	mysql_query("INSERT INTO `event_times` (`id`, `start_time`, `end_time`, `date_id`) VALUES (NULL, '$startTime', '$endTime', '$date_id')");
	
	}
	}
////////////////////////// //////
	
	if ($bc_venue_id != ''){
		$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venue_id . "','" . $event_id . "')";
		mysql_query($sql_venue);	
	}
			
	
	if($bc_event_host || $bc_host_description){
	
	mysql_query("INSERT INTO `event_hosts` (`id`, `source_id`, `event_id`, `host_name`, `host_description`) VALUES (NULL, '$bc_source_id', '$event_id', '$bc_event_host', '$bc_host_description');");
	}
	
			if($bc_event_music){
			foreach($bc_event_music as $bc_event_music_value){
				$sql_event_music = "insert ignore into event_music (event_id, music_id) values('" . $event_id . "','" . $bc_event_music_value . "')";			
				mysql_query($sql_event_music);
			} 
			}
			} 
		
}
else{
	$sucMessage	=	$err;
	
}
}


if ($frmID){
	$qry	=	"select * from `events` where `id`='$frmID'";
	$res = mysql_query($qry);
	while($row = mysql_fetch_array($res)){
		$bc_event_source 		=	$row["event_source"]; 
		$bc_source_id			=	$row["source_id"];
		$bc_fb_event_id			=	$row["fb_event_id"];
		$bc_userid				=	$row["userid"];			
		$bc_category_id			=	$row["category_id"];
		$bc_subcategory_id		=	$row["subcategory_id"];
		$bc_event_name			=	$row["event_name"];
		$bc_event_description	=	$row["event_description"];
		$bc_image				=	$row["event_image"];
		$bc_event_sell_ticket	=	$row["event_sell_ticket"];
		$bc_event_age_suitab	=	$row["event_age_suitab"];
		$bc_event_status		=	$row["event_status"];
		$bc_publishdate			=	$row["publishdate"];
		$bc_averagerating		=	$row["averagerating"];
		$bc_modify_date			=	$row["modify_date"];
		$bc_del_status			=	$row["del_status"];
		$bc_added_by			=	$row["added_by"];
		$frequency				=	$row['repeat_freq'];
		$repeat					=	$row['repeat_event'];
		$bc_men_preferred_age	=	$row['men_preferred_age'];
		$bc_women_preferred_age	=	$row['women_preferred_age'];
		$pending_approval		=	$row['pending_approval'];
		$privacy				= 	$row['privacy'];
		$tags					= 	$row['tags'];
		$bc_occupation_target	=	$row['occupation_target'];
		$bc_occupation_array	=	explode(",", $bc_occupation_target);
		$bc_event_cost			=	$row['event_cost'];
		$bc_featured			=	$row['featured'];
		$bc_free_event			=	$row['free_event'];
		$bc_event_type			=	$row['event_type'];
		$bc_event_end_time		=	$row['event_end_time'];
		$bc_alter				=	$row['alter'];
		$bc_alter_url			=	$row['alter_url'];
		$is_private				=	$row['is_private'];
		
		if($is_private)
			$bc_event_status	= 1;
		
		$event_ticket_id	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$frmID'");
		
		
		$specials_res = mysql_query("select * from `special_event` where `event_id`='$frmID'");
		if(mysql_num_rows($specials_res)){
			while($specials_row = mysql_fetch_array ($specials_res)){
				$bc_specials = $specials_row['specials_id'];
			}
		}
		
		if($event_ticket_id){
			$_SESSION['event_ticket_id'] = $event_ticket_id;
		}
		$rt = mysql_query("select * from `event_gallery` where `event_id`='$frmID' && `event_id`!=''");
		while($rq = mysql_fetch_array($rt)){
			$bc_gallery		=	$rq['name'];
			$bc_gallery_id	=	$rq['id'];
		}
		
		
		$get_gallery_images	=	mysql_query("SELECT * FROM  `event_gallery_images` where `gallery_id`='$bc_gallery_id'");
		while($galleryImage=mysql_fetch_array($get_gallery_images)){
			if($galleryImage['image']!=''){
				$bc_gallery_images[]	=	$galleryImage['image'];
				$bc_gallery_images_id[]	=	$galleryImage['id'];
			}
		}



		$bc_musicgenere_id = array();
		$r4 = mysql_query("select * from event_music where event_id='$frmID'");
		while ( $ro4 = mysql_fetch_assoc($r4) )
			$bc_event_music[] =	$ro4["music_id"];
			
		
	$sql	=	"SELECT * FROM `venue_events` where `event_id`='$frmID'";
	$rt		=	mysql_query($sql);
	while($ro = mysql_fetch_array($rt)){
		$bc_venue_id	=	$ro['venue_id'];
	}

	
	$qry = "select * from `event_hosts` where `event_id`='$frmID'";
	$r = mysql_query($qry);
	while($ro = mysql_fetch_array($r)){
		$bc_event_host			=	$ro['host_name'];
		$bc_host_description	=	$ro['host_description'];
	}
	
	
		$event_id = $frmID;
		
		$_SESSION['event_ticket_id_for_ticket']=$event_id;
		$action = "edit";
	
	$dates_q = "select * from event_dates where event_id = '$frmID' ORDER BY event_date ASC";
	$dates_res = mysql_query($dates_q);
	$first_date = "";
	$dates = "";
	$i = 0;
	while($dates_r = mysql_fetch_assoc($dates_res)){
		if(mysql_num_rows($dates_res) > 0){
			$date = date("m/d/Y",strtotime($dates_r['event_date']));
			if($i<1){ $first_date = $date; $i++;}
			$dates = $dates."'".$date."', ";
		}else{
			$date = $dates_r['event_date'];
			$first_date = $date;
			$dates = "'".date("m/d/Y",strtotime($date))."'";
			}
	}

	if($first_date != ''){
		$yr = date("Y",strtotime($first_date));
		$mon = date("m",strtotime($first_date));
		$mon1 = $mon - 1;
		$dy = date("d",strtotime($first_date));
		$first_date = $yr.", ".$mon1.", ".$dy;
	}
	}
}

	$sql = "select * from `venues` where `id`='$bc_venue_id'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
		$bc_venue_address	=	$ro['venue_address'];
		$bc_venue_name		=	$ro['venue_name'];
		$bc_venue_city		=	$ro['venue_city'];
		$bc_venue_zip		=	$ro['venue_zip'];
	}
	
	$cat_q = "select * from categories order by id ASC";
	$cat_res = mysql_query($cat_q);
	
	//$subCat_q = "select * from sub_categories";
	//$subCat_res = mysql_query($subCat_q);
	$age_q = "select * from age order by id ASC";
	$age_res = mysql_query($age_q);
	
	$music_q = "select * from music";
	$music_res = mysql_query($music_q);
	
	$occupation_q = "select * from occupation";
	$occupation_res = mysql_query($occupation_q);



	if ( $_GET['delete'] > 0 ) {
		$r = mysql_query("select * from event_images where id='". $_GET['delete'] ."'");
		if ( $rr = mysql_fetch_assoc($r) ) {
			@unlink('../event_images/'.$rr['image']);
			@unlink('../event_images/th_'.$rr['image']);
		}	
		mysql_query("delete from event_images where id='". $_GET['delete'] ."'");
	?>
<script>window.location.href="events.php?id=<?php echo $frmID;?>";</script>
<?php
}


	$get_event_videos	=	mysql_query("SELECT * FROM  `event_videos` where `event_id`='$frmID' && `event_id`!=''");
	if(mysql_num_rows($get_event_videos)){
		$bc_video_name = array();
		$bc_video_embed = array();
		while($eventVideos=mysql_fetch_array($get_event_videos)){
			$bc_video_name[]			=	$eventVideos['video_name'];
			$bc_video_embed[]			=	$eventVideos['video_embed'];
		}
	}
	

?>
<script src="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_default.js?0" type="text/javascript"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yuiloader/yuiloader-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/event/event-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dom/dom-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/calendar/calendar-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dragdrop/dragdrop-min.js"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_event_edit.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />
<link href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/happy_event_edit.css?0" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>
<script type='text/javascript' src='js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />
<!-- loads mdp -->
<script type="text/javascript" src="../js/jquery-ui.multidatespicker.js"></script>
<!-- loads mdp -->
<style>


table.ui-datepicker-calendar {border-collapse: separate;}
.ui-datepicker-calendar td {border: 1px solid transparent;}

.hasDatepicker .ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
	background: #743620 none;
	color: white;
}

#ui-datepicker-div {display:none;}

</style>
<script type="text/javascript">
/*
$(document).ready(function() {
	$("#venue_name").autocomplete("get_venue_list.php", {
		width: 260,
		matchContains: true,
		//mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
		});
});
*/
$(document).ready(function() {
			$("#venue_name").autocomplete("../get_venue_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
				
			}).result(function(event, data) {
				if (data) {
					if(data[0]){
					$("#venue_id").attr("value", data[0]);
					$('#venue_id').css('color', '#000');
					}
					if(data[2]){
					$("#ev_address1").attr("value", jQuery.trim(data[2]));
					$('#ev_address1').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
					if(data[3]){
					$("#ev_city").attr("value", jQuery.trim(data[3]));
					$('#ev_city').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
					if(data[4]){
					$("#ev_zip").attr("value", jQuery.trim(data[4]));
					$('#ev_zip').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
				}
			}).setOptions({
				max: '100%'
		});
});

function add_newImage(id)
{
	var next_tr = id+1;
	var new_url_feild = '<tr id="image_tr_'+next_tr+'"><td align="right" width="20%"  class="bc_label">Extra Images(s):</td><td width="80%" align="left" class="bc_input_td"><input type="hidden" value="'+next_tr+'" /><input type="file" name="eimage[]" id="eimage'+next_tr+'" class="bc_input" /><span id="add_more_btn_'+next_tr+'"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newImage('+next_tr+');">&nbsp;&nbsp;Add More</span></span></td></tr>';
	$('#add_more_btn_'+id).html('&nbsp;&nbsp;<img src="images/delete.png" onclick="remove_image('+id+')" style="cursor:pointer">');
	$('#add_url_ist').append(new_url_feild);
	
}
function remove_image(id){
if(id==1){
var id2='';}
else{
var id2 = id;
}
var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
document.getElementById('eimage'+id2).value='';
document.getElementById('image_tr_'+id).style.display='none';
}}


function deleteExtraImage(id)
{
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		window.location.href= 'events.php?id=<?php echo $frmID;?>&delete='+id;
	}
}


$(document).ready(function(){
$('input[name=noTicket]').click(function(){
if($(this).val()=='no'){
$('#showCostPrice').css('visibility','visible');
}
else{
$('#showCostPrice').css('visibility','hidden');
}
});
});


function save(){
$err	=	$('#check_errors').val();

if($err == 1){
$errText	=	$('#dErrors').val();
	alert($errText);
return false;
}
else{
$("#z_listing_event_form").submit();
}
}



function add_more_image(id){
	var limitImages	= 15;
	if(id!=limitImages){  
	var next_row 	= id+1;
	var new_url_feild = '<div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg'+next_row+'"><input type="file" name="images[]" /><img style="cursor:pointer" src="images/icon_delete2.gif" align="" onclick="remove_image('+next_row+')"></div>';
	$('#add_more_image_area').append(new_url_feild);	
	$('#add_more_image_btn').html('<img src="../images/add_more.png" style="cursor:pointer" id="" onclick="add_more_image('+next_row+')" />');
	}
	else{
	alert("You can not upload more then "+limitImages+" images");
	}

}

function remove_image(id){
	$('#showimg'+id).remove();
	var a = $('#add_more_image_btn').html();
	var b = a.split('add_more_image(');
	b = b[1].split(')');
	c = b[0]-1;
	$('#add_more_image_btn').html('<img src="../images/add_more.png" style="cursor:pointer" id="" onclick="add_more_image('+c+')" />');
	
}


function add_more_video(id){
	var limitVideos	= 5;
	if(id!=limitVideos){
	var next_row 	= id+1;
	var new_url_feild = '<div id="showvid'+next_row+'"><table width="100%"><tr><td width="22%" align="right" class="bc_label">Video Name</td><td width="78%"><input type="text" name="video_name[]" value="Enter the name of your video" id="video_name'+next_row+'" onFocus="removeText(this.value,\'Enter the name of your video\',\'video_name'+next_row+'\');" onBlur="returnText(\'Enter the name of your video\',\'video_name'+next_row+'\');" class="new_input" style="width:350px;"><img style="cursor: pointer; padding: 3px 0 0;" src="images/icon_delete2.gif" align="" onclick="remove_video('+next_row+')"></td></tr><td align="right" class="bc_label">Video Embed Code:</td><td><textarea class="new_input" name="video_embed[]" style="width:466px; height:80px;"></textarea></td></tr></table>';
	$('#add_more_video_area').append(new_url_feild);	
	$('#add_more_video_btn').html('<img src="../images/add_more.png" style="cursor:pointer" id="" onclick="add_more_video('+next_row+')" />');
	}
	else{
	alert("You can not upload more then "+limitVideos+" videos");
	}
}

function remove_video(id){
	$('#showvid'+id).remove();
	var a = $('#add_more_video_btn').html();
	var b = a.split('add_more_video(');
	b = b[1].split(')');
	c = b[0]-1;
	$('#add_more_video_btn').html('<img src="../images/add_more.png" style="cursor:pointer" id="" onclick="add_more_video('+c+')" />');
}


</script>
<form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onsubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
  <input type="hidden" name="frmID" value="<?php echo $frmID; ?>" />
  <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
  <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
  <?php if ($event_id != "" && $dates != "" )  { ?>
  <input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="<?php echo substr(str_replace("'","",trim($dates)),0,-1); ?>" />
  <?php } else {?>
  <input type="hidden" name="selected_dates" id="selected_dates" class="bc_input" value="" />
  <?php } ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
    <tr class="bc_heading">
      <td colspan="2" align="left">Add/Edit Event</td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="success" ><?php echo $sucMessage;?></td>
    </tr>
    <tr>
      <td width="22%" align="right" class="bc_label">Category:</td>
      <td width="78%" align="left" class="bc_input_td"><select name="category_id" class="bc_input"  onchange="dynamic_Select('subcategory.php', this.value, 0 )">
          <option value="">-- Category --</option>
          <?php while($cat_r=mysql_fetch_assoc($cat_res)){ ?>
          <option value="<?php echo $cat_r['id']; ?>" <?php if($cat_r['id'] == $bc_category_id){?> selected="selected" <?php }?>><?php echo $cat_r['name']; ?></option>
          <!--<input type="text" name="category_id" id="category_id" class="bc_input" value="<?php //echo $bc_category_id; ?>"/>-->
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Sub Category:</td>
      <td align="left" class="bc_input_td"><div id="subcategory_id">
          <?php
		  if($bc_category_id!=''){?>
          <select name="subcategory_id" class="selectBig">
            <option value="">-- Select Secondary Category --</option>
            <?php
	 $subcat_q = "SELECT * FROM sub_categories WHERE categoryid = '$bc_category_id' ORDER BY id ASC";
		$res = mysql_query($subcat_q);
	  	while( $r = mysql_fetch_assoc($res) ){
	  ?>
            <option value="<?php echo $r['id']; ?>" <?php if ($bc_subcategory_id==$r['id']){ echo 'selected="selected"'; }?>><?php echo $r['name']; ?></option>
            <?php
   }
   ?>
          </select>
          <?php
			}
		  else{
		  ?>
          <select name="subcategory_id" class="bc_input">
            <option selected="selected" value=""></option>
          </select>
          <?php } ?>
        </div>
        <!--<input type="text" name="subcategory_id" id="subcategory_id" class="bc_input" value="<?php //echo $bc_subcategory_id; ?>"/>--></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Name:</td>
      <td align="left" class="bc_input_td"><input type="text" name="event_name" id="event_name" class="bc_input" style="width:350px" value="<?php echo $bc_event_name; ?>"/></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Music Genere:</td>
      <td align="left" class="bc_input_td"><select name="event_music[]" class="bc_input" style="width:250px; height:100px" multiple="multiple">
          <!--          <option value="">-- Music Genre --</option>-->
          <?php while($music_r=mysql_fetch_assoc($music_res)){
		  if(is_array($bc_event_music)){
		if ( in_array($music_r['id'],$bc_event_music) )
			$che = 'selected="selected"';
		else
			$che = '';		
			}
			?>
          <option value="<?php echo $music_r['id']; ?>" <?php echo $che; ?>><?php echo $music_r['name']; ?></option>
          <?php } ?>
        </select>
        <!--<input type="text" name="musicgenere_id" id="musicgenere_id" class="bc_input" value="<?php //echo $bc_musicgenere_id; ?>"/>--></td>
    </tr>
    <?php if ($bc_event_type!=0){?>
    <tr>
      <td align="right" class="bc_label">Occupation Target:</td>
      <td align="left" class="bc_input_td"><select name="occupation_target[]" class="bc_input" style="width:250px; height:100px" multiple="multiple">
          <?php while($occupation_r=mysql_fetch_assoc($occupation_res)){
		  if(is_array($bc_occupation_array)){
		  if( in_array($occupation_r['id'],$bc_occupation_array) ){
		  $sel = 'selected="selected"';
		  }
		  else{
		  $sel = '';
		  }}
		  ?>
          <option value="<?php echo $occupation_r['id']; ?>" <?php echo $sel; ?>><?php echo $occupation_r['occupation']; ?></option>
          <?php } ?>
        </select>
        <!--<input type="text" name="musicgenere_id" id="musicgenere_id" class="bc_input" value="<?php //echo $bc_musicgenere_id; ?>"/>--></td>
    </tr>
    <?php } ?>
    <tr>
      <td align="right" class="bc_label">Event Description:</td>
      <td align="left" class="bc_input_td"><textarea name="event_description" id="event_description" class="bc_input" style="width:400px; height:200px"><?php echo $bc_event_description; ?></textarea>
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Event Image:</td>
      <td align="left" class="bc_input_td"><?php 
if( $bc_image != ''  ) {
	if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
		$bc_image1 = ABSOLUTE_PATH . 'event_images/th_'.$bc_image;
	else
		$bc_image1 = $bc_image;	
		
	echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_event_image" width="75" height="76" />';
	$image_del = '<img src="images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="events|event_image|'.$bc_image.'|../event_images/" />';
}
else
	echo '<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />';
							
 ?>
        <input type="file" name="event_image" id="event_image" class="bc_input" value="<?php echo $bc_image; ?>"/>
        <br />
        <?php echo $image_del;  ?> </td>
    </tr>
    <?php if ($bc_event_type!=0){?>
    <tr>
      <td align="right" class="bc_label">Gallery Name:</td>
      <td align="left" class="bc_input_td"><input type="text" name="gallery" value="<?php echo $bc_gallery; ?>" id="gname"  class="bc_input" style="width:350px" /><br />
	<span style="color:#FF0000">*if you want upload images must write gallery name</span></td>
    </tr>
    <tr>
      <td align="right" class="bc_label" valign="top"><br />
        Gallery Images:</td>
      <td align="left" class="bc_input_td"><?php
					  $i=0;
					 if(is_array($bc_gallery_images)){
					 $count_images = count($bc_gallery_images);
					 for ($z=0;$z < $count_images;$z++){
//						foreach($bc_gallery_images as $gallery_images){
						
						$gallery_images		= $bc_gallery_images[$z];
						$gallery_images_id	= $bc_gallery_images_id[$z];
						
							if($gallery_images!=''){
							$i++;
							if($bc_event_type==1){
							if( $i <=4 ){
							?>
        <div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg<?php echo $i; ?>">
          <?php
								echo "<img src='".ABSOLUTE_PATH."event_images/gallery/th_".$gallery_images."' id='".$i."'>";
								echo $image_del = '<br><img src="images/remove_img.png" class="delImg" id="'.$gallery_images_id.'" style="cursor:pointer" rel="event_gallery_images|image|'.$gallery_images.'|event_images/gallery/|delImg_image|showfile|'.$i.'" />';
								?>
        </div>
        <?php
								if($i%2==0){
								echo '<div class="clr"></div>';
								}
								} // END if( $i <=4 )
								} // END if( $bc_event_type == 1)
								else{?>
        <div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg<?php echo $i; ?>">
          <?php
								echo "<img src='".ABSOLUTE_PATH."event_images/gallery/th_".$gallery_images."' id='".$i."'>";
								echo $image_del = '<br><img src="images/remove_img.png" class="delImg" id="'.$gallery_images_id.'" style="cursor:pointer" rel="event_gallery_images|image|'.$gallery_images.'|event_images/gallery/|delImg_image|showfile|'.$i.'" />';
								?>
        </div>
        <?php
								if($i%2==0){
									echo '<div class="clr"></div>';
								}
								
								}
							} 
							}
							
							if(count($bc_gallery_images) < 4){
								$lp =	4 - count($bc_gallery_images);
								for($s=0;$s < $lp;$s++){
									$i++;
							?>
        <div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg<?php echo $i; ?>">
          <input type="file" name="images[]" />
        </div>
        <?php
							if($i%2==0){
								echo '<div class="clr"></div>';
								}
							}
							}
							
							}
							else{?>
        <div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg1">
          <input type="file" name="images[]" />
        </div>
        <div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg2">
          <input type="file" name="images[]" />
        </div>
        <div class="clr"></div>
        <div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg3">
          <input type="file" name="images[]" />
        </div>
        <div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg4">
          <input type="file" name="images[]" />
        </div>
        <?php
							}
							
				   if($bc_event_type==2 || $bc_event_type==3){?>
        <div id="add_more_image_area"></div>
        <div class="clr"></div>
        <div align="right" style="padding-right:100px"><br />
          <br />
          <span id="add_more_image_btn"><img src="<?php echo IMAGE_PATH; ?>add_more.png" style="cursor:pointer" id="" onClick="add_more_image(<?php if ($count_images>4){echo $count_images;} else{ echo "4"; } ?>)" /></span> </div>
        <?php } ?>
        </div>
      </td>
    </tr>
    <tr>
      <!-- <td align="right" class="bc_label">Gallery Images:</td>
	 <td align="left" class="bc_input_td">
	 <div class="fltlft" style="width:50%; padding:5px 0" id="showimg1">
			<?php
			if($bc_gallery_images[0]!=''){
			echo "<img src='".ABSOLUTE_PATH."event_images/gallery/th_".$bc_gallery_images[0]."' id='1'>";
			echo $image_del = '<br><img src="images/remove_img.png" class="delImg" id="'.$bc_gallery_images_id[0].'" style="cursor:pointer"
	rel="event_gallery_images|image|'.$bc_gallery_images[0].'|../event_images/gallery/|delImg_image|showfile|1" />';
			}
			else{
			?>
              <input type="file" name="images[]" />
			  <?php } ?>
            </div>
            <div class="fltlft" style="width:50%; padding:5px 0" id="showimg2">
           <?php
			if($bc_gallery_images[1]!=''){
			echo "<img src='".ABSOLUTE_PATH."event_images/gallery/th_".$bc_gallery_images[1]."' id='2'>";
			echo $image_del = '<br><img src="images/remove_img.png" class="delImg" id="'.$bc_gallery_images_id[1].'" style="cursor:pointer"
	rel="event_gallery_images|image|'.$bc_gallery_images[1].'|../event_images/gallery/|delImg_image|showfile|2" />';
			}
			else{
			?>
              <input type="file" name="images[]" />
			  <?php } ?>
            </div>
            <div class="clr"></div>
            <div class="fltlft" style="width:50%; padding:5px 0" id="showimg3">
              <?php
			if($bc_gallery_images[2]!=''){
				echo "<img src='".ABSOLUTE_PATH."event_images/gallery/th_".$bc_gallery_images[2]."' id='3'>";
				echo $image_del = '<br><img src="images/remove_img.png" class="delImg" id="'.$bc_gallery_images_id[2].'" style="cursor:pointer" rel="event_gallery_images|image|'.$bc_gallery_images[2].'|../event_images/gallery/|delImg_image|showfile|3" />';
			}
			else{
			?>
              <input type="file" name="images[]" />
			  <?php } ?>
            </div>
            <div class="fltlft" style="width:50%; padding:5px 0" id="showimg4">
              <?php
			if($bc_gallery_images[3]!=''){
				echo "<img src='".ABSOLUTE_PATH."event_images/gallery/th_".$bc_gallery_images[3]."' id='4'>";
				echo $image_del = '<br><img src="images/remove_img.png" class="delImg" id="'.$bc_gallery_images_id[3].'" style="cursor:pointer"
	rel="event_gallery_images|image|'.$bc_gallery_images[3].'|../event_images/gallery/|delImg_image|showfile|4" />';
			}
			else{
			?>
              <input type="file" name="images[]" />
			  <?php } ?>
            </div>
	 </td>
    </tr>-->
      <?php
				  $i=0;
					 if(is_array($bc_video_embed)){
					 $count_videos = count($bc_video_embed);
					 for ($z=0;$z < $count_videos;$z++){
//						foreach($bc_gallery_images as $gallery_images){
						
						$video_embed	= $bc_video_embed[$z];
						$video_name		= $bc_video_name[$z];
						$i++;
							if($bc_event_type==1){
							if( $i <=1 ){?>
    <tr>
      <td align="right" class="bc_label">Video Name</td>
      <td><input type="text" name="video_name[]" value="<?php if ($video_name){ echo $video_name; } else{ echo "Enter the name of your video"; } ?>" id="video_name<?php echo $i; ?>" onFocus="removeText(this.value,'Enter the name of your video','video_name<?php echo $i; ?>');" onBlur="returnText('Enter the name of your video','video_name<?php echo $i; ?>');" class="new_input" style="width:350px;">
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Video Embed Code:</td>
      <td><textarea class="new_input" name="video_embed[]" style="width:466px; height:80px;"><?php if ($video_embed){ echo $video_embed; }?>
</textarea></td>
    </tr>
    <?php
					} // END $i <=1 
							} // END $bc_event_type==1
						else{ ?>
    <tr>
    <td colspan="2">
    
    <div id="showvid<?php echo $i ; ?>">
    
    <table>
      <tr>
        <td align="right" class="bc_label">Video Name:</td>
        <td><input type="text" name="video_name[]" value="<?php if ($video_name){ echo $video_name; } else{ echo "Enter the name of your video"; } ?>" id="video_name<?php echo $i; ?>" onFocus="removeText(this.value,'Enter the name of your video','video_name<?php echo $i; ?>');" onBlur="returnText('Enter the name of your video','video_name<?php echo $i; ?>');" class="new_input" style="width:350px;">
          <?php
					  if($i>1){?>
          <img style="padding: 3px 0 0 0;cursor:pointer" src="images/icon_delete2.gif" align="" onClick="remove_video(<?php echo $i; ?>)">
          <?php
					  } ?>
        </td>
      </tr>
      <tr>
        <td align="right" class="bc_label">Video Embed Code:</td>
        <td><textarea class="new_input" name="video_embed[]" style="width:466px; height:80px;"><?php if ($video_embed){ echo $video_embed; }?>
</textarea></td>
      </tr>
      </div>
      
      </td>
      </tr>
      
    </table>
    <?php				
						
						}
							} // END FOR
							} // END if is_array
						else{
						?>
    <tr>
      <td align="right" class="bc_label">Video Name:</td>
      <td><input type="text" name="video_name[]" value="<?php if ($bc_video_name){ echo $bc_video_name; } else{ echo "Enter the name of your video"; } ?>" id="video_name" onFocus="removeText(this.value,'Enter the name of your video','video_name');" onBlur="returnText('Enter the name of your video','video_name');" class="new_input" style="width:350px;"></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Video Embed Code:</td>
      <td><textarea class="new_input" name="video_embed[]" style="width:466px; height:80px;"><?php if ($bc_video_embed){ echo $bc_video_embed; }?>
</textarea></td>
    </tr>
    <?php
						}
							 if($bc_event_type==2 || $bc_event_type==3){?>
    <tr>
      <td colspan="2"><div id="add_more_video_area"></div>
        <div class="clr"></div>
        <div align="right" style="padding-right:100px"><br />
          <br />
          <span id="add_more_video_btn"><img src="<?php echo IMAGE_PATH; ?>add_more.png" style="cursor:pointer" id="" onClick="add_more_video(<?php if ($count_videos){echo $count_videos;} else{ echo "1"; } ?>)" /></span> </div>
        <?php } ?>
        </div>
      </td>
    </tr>
    <?php
	} ?>
   
    <tr>
      <td align="right" class="bc_label" valign="top">Event date and time:</td>
      <td colspan="2"><div id="z_listing_event_form_occurrences" class="z-group z-panel-occurrences">
          <div>
            <label for="z_event_start_time" class="z-inline" style="width:100px"><sup>*</sup> Start Time</label>
            <input id="z_event_start_time" class="z-input-time" type="text" value="7:00" name="start_time"/>
            <select id="z_event_start_am_pm" name="start_time_am_or_pm">
              <option value="0">AM</option>
              <option selected="selected" value="1">PM</option>
            </select>
			<!-- &nbsp; &nbsp; &nbsp; Event End Time:
            <input type="text" class="new_input"  style="width:150px" value="<?php echo $bc_event_end_time; ?>" name="event_end_time" />-->
			
			<label for="z_event_end_time" class="z-inline" style="float:none; display:inline">End Time (optional)</label>
			<input id="z_event_end_time" class="z-input-time" type="text" name="end_time"/>
			<select id="z_event_end_am_pm" name="end_time_am_or_pm">
				<option value="0">AM</option>
				<option selected="selected" value="1">PM</option>
			</select>
			
          </div>
          <div class="ev_fltlft"><br />
            <br />
            <div id="head"><strong>Select Event Date(s)</strong></div>
            <a name="z_repeat_pattern_list"></a>
            <ul class="z-tabs" style="display:">
              <li class="z-current"><a href="#">Calendar View</a></li>
              <li><a href="#">Advanced View</a></li>
            </ul>
            <div id="z_tab_calender_view" class="z-calendar-view z-tab-content" style="display: block">
              <label><sup>&#42;</sup><small> Click one or more dates for your event or event series on the calendars below.</small></label>
              <div class="yui-skin-sam">
                <div id="z_calendar_container"></div>
                <div class="clear"></div>
              </div>
            </div>
            <!-- Advance View START -->
            <div id="z_tab_advanced_view" class="z-advanced-view z-tab-content" style="border: 1px solid #CCCCCC; margin-top: -1px;">
              <div class="z-date-range-block yui-skin-sam">
                <label class="z-input-date-label"><sup>&#42;&nbsp;</sup>Start Date</label>
                <input class="z-input-date" id="z_start_date_advanced" readonly="" type="text" value="<?php echo date('d/m/Y'); ?>"  />
                <div id="z_popup_start_date_container" class="z-popup-date" style="display: none"></div>
                <div id="z_popup_end_date_container" class="z-popup-date" style="display: none"></div>
                <div class="z-end-date-block" id="z_end_date_block" style="display: none">
                  <label class="z-input-date-label"><sup>&#42;&nbsp;</sup>End Date</label>
                  <input class="z-input-date" id="z_end_date_advanced" readonly="" type="text" value="1/1/2009"  />
                </div>
              </div>
              <div id="z_repeat_pattern">
                <p><strong>Event Recurrence</strong></p>
                <p>From your chosen start date this event:</p>
                <p>
                  <label for="repeat_type">Repeats:</label>
                  <select id="z_occurrence_repeat_type_select" name="z_occurrence_repeat_type_select">
                    <option value="z_repeat_once_layer">Occurs only once</option>
                    <option value="z_repeat_daily_layer">Daily</option>
                    <option value="z_repeat_weekly_layer">Weekly</option>
                    <option value="z_repeat_monthly_layer">Monthly</option>
                  </select>
                </p>
                <div  id="z_repeat_once_layer" ></div>
                <div  id="z_repeat_daily_layer" style="display: none;">
                  <p>
                    <label for="daily_repeat_interval" ><span class="required">&#42;&nbsp;</span>Repeat every:</label>
                    <input type="text" class="date" style="padding:3px; color:#000000; margin:0" id="daily_repeat_interval" name="daily_repeat_interval" />
                    day(s) </p>
                </div>
                <div  id="z_repeat_weekly_layer"  style="display: none;">
                  <p>
                    <select id="z_weekly_repeat_interval">
                      <option value="1" selected="selected">Every</option>
                      <option value="2">Every other</option>
                      <option value="3">Every third</option>
                      <option value="4">Every fourth</option>
                    </select>
                  </p>
                  <p id="z_weekly_repeat_days">
                    <input type="checkbox" name="repeat_day" value="0" />
                    Su
                    <input type="checkbox" name="repeat_day" value="1" />
                    M
                    <input type="checkbox" name="repeat_day" value="2" />
                    T
                    <input type="checkbox" name="repeat_day" value="3" />
                    W
                    <input type="checkbox" name="repeat_day" value="4" />
                    Th
                    <input type="checkbox" name="repeat_day" value="5" />
                    F
                    <input type="checkbox" name="repeat_day" value="6" />
                    Sa </p>
                </div>
                <div  id="z_repeat_monthly_layer" style="display: none;">
                  <table border="0">
                    <!--<tr>
                      <td><input type="radio" id="z_monthly_day" class="z-monthly-repeat-type" name="monthly_repeat_type" value="day" />
                      </td>
                      <td> On Day
                        <input type="text" class="z-date" id="z_monthly_day_of_month" name="Within" />
                        of every month </td>
                    </tr>-->
                    <tr>
                      <td><input type="radio" checked="checked" id="z_monthly_pattern" class="z-monthly-repeat-type" name="monthly_repeat_type" value="pattern" />
                      </td>
                      <td> On the
                        <select name="Every" id="z_monthly_pattern_period">
                          <option value="0" selected="selected">First</option>
                          <option value="1">Second</option>
                          <option value="2">Third</option>
                          <option value="3">Fourth</option>
                        </select>
                        <select name="Every" id="z_monthly_pattern_day">
                          <option value="0" selected="selected">Sunday</option>
                          <option value="1" >Monday</option>
                          <option value="2">Tuesday</option>
                          <option value="3">Wednesday</option>
                          <option value="4">Thursday</option>
                          <option value="5">Friday</option>
                          <option value="6">Saturday</option>
                        </select>
                      </td>
                    </tr>
                  </table>
                </div>
                <br />
                <ul class="clear">
                  <li>
                    <input id="z_add_repeat_date" type="button" value="+ Add to Preview List" />
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="z-clear"></div>
            </div>
            <!-- Advance View END -->
          </div>
          <div class="clr"></div>
          <br />
          <div id="head"><strong>Event Preview</strong></div>
          <a name="z_a_repeat_pattern_list"></a>
          <div id="z_review_errors"></div>
          <input type="hidden" id="check_errors" value=""  />
          <input type="hidden" id="dErrors" value="" />
          <div class="z-simple-box">
            <table width="100%" cellspacing="0" class="z-event-occurrences-heading">
              <tbody>
                <tr>
                  <th width="34%" class="z-col-1">Event Date</th>
                  <th width="26%" class="z-col-2">Type of Date</th>
                  <th width="30%" class="z-col-3">Start Time</th>
                  <th width="10%" class="z-col-4">Remove</th>
                </tr>
              </tbody>
            </table>
            <div class="z-block-event-occurrences" style="height: 188px;  overflow: auto;">
              <table class="z-event-occurrences" cellspacing="0" width="100%">
                <tbody>
                  <?php
				  
if (isset($_POST['occurrences']) && $_POST['occurrences'] != ''){
	$date			=	array();
	$start_time		=	array();
	$start_am_pm	=	array();
	$endTime		=	array();
	$end_am_pm		=	array();
	$unique_id		=	0;
	foreach($occurrences as $v){
	
	$date[]			=	$v['date'];
	$start_time[]	=	$v['start_time'];
	$start_am_pm[]	=	$v['start_am_pm'];  // AM = 0, PM = 1
	$endTime[]		=	$v['end_time'];
	$end_am_pm[]	=	$v['end_am_pm'];	// AM = 0, PM = 1
	
	}
	$totalOccurrences = count($date);
	for($i=0;$i<count($date);$i++){
	$unique_id++;
	
	if($endTime[$i]!='00:00:00' && $endTime[$i]!='')
		$end_time = date("H:i:s", strtotime($endTime[$i]));
	else
		$end_time = '00:00:00';
	?>
                  <tr id="z_occurrence_row_<?php echo $unique_id ?>">
                    <td width="34%" class="z-occurrence-date-cell z-col-1" style="background-color: #d1e5c0;"><input type="hidden" class="z-occurrence-id" name="occurrences[<?php echo $unique_id ?>][occurrence_id]" value="" />
                      <input type="hidden" class="z-occurrence-date" name="occurrences[<?php echo $unique_id ?>][date]" value="<?php echo date('m/d/Y', strtotime($date[$i])); ?>" />
                      <?php echo date('D, d M, Y', strtotime($date[$i])); ?> </td>
                    <td width="26%" class="z-occurrence-type-cell z-col-2" style="background-color: #d1e5c0;"><select name="occurrences[<?php echo $unique_id ?>][date_type]" class="z-occurrence-type">
                        <option value="0" >Normal</option>
                        <option value="1" >Tickets on Sale</option>
                        <option value="2" >Opening Night</option>
                        <option value="3" >Special Event</option>
                      </select>
                    </td>
                    <td width="30%" class="z-time-cell z-col-3" style="background-color: #d1e5c0;"><div class="z-occurrence-start-time-layer" >
                        <input type="text" class="z-occurrence-start-time z-input-time" name="occurrences[<?php echo $unique_id ?>][start_time]" value="<?php echo date('h:i',strtotime($start_time[$i])); ?>"  />
                        <select class="z-occurrence-start-am-pm" name="occurrences[<?php echo $unique_id ?>][start_am_pm]" >
                          <option value="0" <?php if ($start_am_pm[$i] == 0){ echo "selected='selected'";}; ?>>AM</option>
                          <option value="1" <?php if ($start_am_pm[$i] == 1){ echo "selected='selected'";}; ?>>PM</option>
                        </select>
                      </div>
                      <div class="z-occurrence-end-time-layer" <?php if ($end_time=='00:00:00'){ echo 'style="display:none"'; } ?>>
<input type="text" class="z-occurrence-end-time z-input-time" name="occurrences[<?php echo $unique_id ?>][end_time]" value="<?php
if ($end_time!='00:00:00'){ echo date('h:i',strtotime($end_time)); }?>"  />
                          <select class="z-occurrence-end-am-pm" name="occurrences[<?php echo $unique_id ?>][end_am_pm]"  >
                            <option value="0" <?php if ($end_am_pm[$i] == 0){ echo "selected='selected'";}; ?>>AM</option>
                            <option value="1" <?php if ($end_am_pm[$i] == 1){ echo "selected='selected'";}; ?>>PM</option>
                          </select>
                        </div>
                    <a class="z-end-time-toggle">[+] end time</a> </td>
                    <td width="10%" class="z-remove-cell z-col-4" style="background-color: #d1e5c0;"><a class="z-occurrence-remove"><img src="<?php echo ABSOLUTE_PATH; ?>images/icon_remove.gif" alt="remove" title="remove"></a> </td>
                  </tr>
                  <?php
	}}
elseif($frmID){
				 $res = mysql_query("select * from `event_dates` where `event_id`='$frmID' ORDER BY `event_date` ASC");
				 $unique_id	=	0;
				 $totalOccurrences	=	mysql_num_rows($res);
				 while($row = mysql_fetch_array($res)){
				 $unique_id++;
				 $date_id	=	$row['id'];
				 $re = mysql_query("select * from `event_times` where `date_id`='$date_id'");
				 while($ro = mysql_fetch_array($re)){
				 $start_time	=	$ro['start_time'];
				 $end_time		=	$ro['end_time'];
				 }
				 ?>
                  <tr id="z_occurrence_row_<?php echo $unique_id ?>">
                    <td width="34%" class="z-occurrence-date-cell z-col-1" style="background-color: #d1e5c0;"><input type="hidden" class="z-occurrence-id" name="occurrences[<?php echo $unique_id ?>][occurrence_id]" value="" />
                      <input type="hidden" class="z-occurrence-date" name="occurrences[<?php echo $unique_id ?>][date]" value="<?php echo date('m/d/Y', strtotime($row['event_date'])); ?>" />
                      <?php echo date('D, d M, Y', strtotime($row['event_date'])); ?> </td>
                    <td width="26%" class="z-occurrence-type-cell z-col-2" style="background-color: #d1e5c0;"><select name="occurrences[<?php echo $unique_id ?>][date_type]" class="z-occurrence-type">
                        <option value="0" >Normal</option>
                        <option value="1" >Tickets on Sale</option>
                        <option value="2" >Opening Night</option>
                        <option value="3" >Special Event</option>
                      </select>
                    </td>
                    <td width="30%" class="z-time-cell z-col-3" style="background-color: #d1e5c0;"><div class="z-occurrence-start-time-layer" >
                        <input type="text" class="z-occurrence-start-time z-input-time" name="occurrences[<?php echo $unique_id ?>][start_time]" value="<?php if ($start_time!='00:00:00' && $start_time!='') { echo date('h:i',strtotime($start_time));} ?>"  />
                        <select class="z-occurrence-start-am-pm" name="occurrences[<?php echo $unique_id ?>][start_am_pm]" >
                          <option value="0" <?php if (date('A',strtotime($start_time))=='AM'){ echo "selected='selected'";}; ?>>AM</option>
                          <option value="1" <?php if (date('A',strtotime($start_time))=='PM'){ echo "selected='selected'";}; ?>>PM</option>
                        </select>
                      </div>
                      <div class="z-occurrence-end-time-layer" <?php if ($end_time=='00:00:00' || $end_time==''){ echo 'style="display:none"'; } ?>>
<input type="text" class="z-occurrence-end-time z-input-time" name="occurrences[<?php echo $unique_id ?>][end_time]" value="<?php
if ($end_time!='00:00:00' && $end_time!=''){ echo date('h:i',strtotime($end_time)); }?>"  />
                          <select class="z-occurrence-end-am-pm" name="occurrences[<?php echo $unique_id ?>][end_am_pm]"  >
                            <option value="0" <?php if (date('A',strtotime($end_time))=='AM'){ echo "selected='selected'";}; ?>>AM</option>
                            <option value="1" <?php if (date('A',strtotime($end_time))=='PM'){ echo "selected='selected'";}; ?>>PM</option>
                          </select>
                        </div>
                      <a class="z-end-time-toggle">[+] end time</a>
                    </td>
                    <td width="10%" class="z-remove-cell z-col-4" style="background-color:#d1e5c0;"><a class="z-occurrence-remove"><img src="<?php echo ABSOLUTE_PATH; ?>images/icon_remove.gif" alt="remove" title="remove"></a> </td>
                  </tr>
                  <?php
}
}
?>
                </tbody>
              </table>
            </div>
            <div class="z-table-bottom">
              <div id="z_total_occurrences_block"> Total Occurrences: <span id="z_total_occurrences">
                <?php if ($totalOccurrences){ echo $totalOccurrences; } else{ echo 0;} ?>
                </span> </div>
              <div id="z_clear_occurrences_block"> <a href="#" id="z_clear_occurrences">Clear Occurrences</a> </div>
              <div class="z-clear"></div>
            </div>
          </div>
          <script type="text/plain" id="occurrence_template">
<tr id="z_occurrence_row_<@=unique_id@>">
  <td class="z-occurrence-date-cell z-col-1">
    <input type="hidden" class="z-occurrence-id" name="occurrences[<@=unique_id@>][occurrence_id]" value="<@=occurrence_id@>" />
    <input type="hidden" class="z-occurrence-date" name="occurrences[<@=unique_id@>][date]" value="<@=date@>" />
    <@=display_date@>
  </td>
  <td class="z-occurrence-type-cell z-col-2">
    <select name="occurrences[<@=unique_id@>][date_type]" class="z-occurrence-type">
      <option value="0" <@= date_type == "0" ? "selected='SELECTED'" : '' @>>Normal</option>
      <option value="1" <@= date_type == "1" ? "selected='SELECTED'" : '' @>>Tickets on Sale</option>
      <option value="2" <@= date_type == "2" ? "selected='SELECTED'" : '' @>>Opening Night</option>
      <option value="3" <@= date_type == "3" ? "selected='SELECTED'" : '' @>>Special Event</option>
    </select>
  </td>
  <td class="z-time-cell z-col-3">
    <div class="z-occurrence-start-time-layer" >
      <input type="text" class="z-occurrence-start-time z-input-time" name="occurrences[<@=unique_id@>][start_time]" value="<@=start_time@>"  />
      <select class="z-occurrence-start-am-pm" name="occurrences[<@=unique_id@>][start_am_pm]" >
        <option value="0" <@= start_am_pm == "0" ? "selected='SELECTED'" : '' @>>AM</option>
        <option value="1" <@= start_am_pm == "1" ? "selected='SELECTED'" : '' @>>PM</option>
      </select>
    </div>
    <div class="z-occurrence-end-time-layer" <@= end_time == "" ? "style='display:none'" : '' @>>
      <input type="text" class="z-occurrence-end-time z-input-time" name="occurrences[<@=unique_id@>][end_time]" value="<@=end_time@>"  />
      <select class="z-occurrence-end-am-pm" name="occurrences[<@=unique_id@>][end_am_pm]"  >
        <option value="0" <@= end_am_pm == "0" ? "selected='SELECTED'" : '' @>>AM</option>
        <option value="1" <@= end_am_pm == "1" ? "selected='SELECTED'" : '' @>>PM</option>
      </select>
    </div>
    <a class="z-end-time-toggle">[+] end time</a>
  </td>
  <td class="z-remove-cell z-col-4">
    <a class="z-occurrence-remove"><img src="<?php echo ABSOLUTE_PATH; ?>images/icon_remove.gif" alt="remove" title="remove"></a>
  </td>
</tr>
</script>
        </div></td>
    </tr>
    <!--	  <?php if ($bc_event_type!=0){?>
    <tr>
      <td align="right" class="bc_label">Ticketed Event:</td>
      <td align="left" class="bc_input_td"><span class="bc_label"> Yes </span>
        <input type="radio" name="noTicket" id="noTicket" class="bc_input" value="yes"
 <?php if($_POST['noTicket']=='yes' || $bc_event_cost==''){echo 'checked="checked';}?>/>
        &nbsp; <span class="bc_label"> No </span>
        <input type="radio" name="noTicket" id="noTicket" class="bc_input" value="no" 
 <?php if($_POST['noTicket']=='no' || $bc_event_cost!=''){echo 'checked="checked"';}?> />
 <span id="showCostPrice" style=" <?php if ($_POST['noTicket']=='yes' || $bc_event_cost==''){ echo 'visibility:hidden';} ?>">Event Cost:    $
            <input type="text" class="new_input" style="width:150px; font-weight:bold" value="<?php echo $bc_event_cost; ?>" name="event_cost" /></span>
		<?php if ($_GET['id']){
		$r = mysql_query("select * from `event_ticket` where `event_id`");
	$bc_event_ticket_id	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$frmID'");	
		if($bc_event_ticket_id){
		?><br />
		<a target="_blank" href="tickets.php?id=<?php echo $bc_event_ticket_id; ?>" style="padding:5px; background:#eff2dd;  color:#000; text-decoration:none; font-weight:bold">Edit Tickets</a>
		<?php
		 } 
		 }
		 ?>
      </td>
    </tr>
	<?php } ?>-->
    <tr>
      <td align="right" class="bc_label">Minimum Age Allowed:</td>
      <td align="left" class="bc_input_td"><select name="min_age_allow" id="min_age_allow" class="bc_input">
          <option value="">-- Age --</option>
          <?php while($age_r=mysql_fetch_assoc($age_res)){ ?>
          <option value="<?php echo $age_r['id']; ?>" <?php if($age_r['id'] == (int)trim($bc_event_age_suitab)){?> selected="selected" <?php }?>><?php echo $age_r['name']; ?></option>
          <?php } ?>
        </select>
    </tr>
    <!--
<tr>
<td align="right" class="bc_label">Pending Approval:</td>
<td align="left" class="bc_input_td">

<select name="pending_approval" id="pending_approval" class="bc_input">

	<option value="">--Approval--</option>
	<option value = "0" <?php if($pending_approval == '0'){ echo 'selected="selected"'; }?> >Active</option>
	<option value = "1" <?php if($pending_approval == '1'){ echo 'selected="selected"'; } ?> >Pending</option>
	
</select>

</td>
</tr>
-->
    <tr>
      <td align="right" class="bc_label">Preferred Age Demographic:</td>
      <td align="left" class="bc_input_td"><span>Men</span>
        <select name="men_preferred_age" style="width:104px" id="event_age_suitab">
          <option value="">-Select age-</option>
          <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
          <option value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_men_preferred_age)
							{ echo 'selected'; }?>> <?php echo $rowAge['name']?> </option>
          <?php } ?>
        </select>
        &nbsp; &nbsp; <span>Women</span>
        <select name="women_preferred_age" style="width:104px" id="event_age_suitab">
          <option value="">-Select age-</option>
          <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
          <option value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_women_preferred_age)
							{ echo 'selected'; }?>> <?php echo $rowAge['name']?> </option>
          <?php } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td align="right" class="bc_label">Event Status:</td>
      <td align="left" class="bc_input_td"><select name="event_status" id="event_status" class="bc_input">
          <option value="">-- Status --</option>
          <option value = "0" <?php if($bc_event_status == '0'){ echo 'selected="selected"'; }?> >Not Active</option>
          <option value = "1" <?php if($bc_event_status == '1'){ echo 'selected="selected"'; } ?> >Active</option>
          <option value = "2" <?php if($bc_event_status == '2'){ echo 'selected="selected"'; } ?> >Pending Approval</option>
        </select>
        <!--<input type="text" name="event_status" id="event_status" class="bc_input" value="<?php //echo $bc_event_status; ?>"/>-->
      </td>
    </tr>

    <tr>
      <td align="right" class="bc_label">Location:</td>
      <td align="left" class="bc_input_td"><input type="text" name="venue_name" id="venue_name" class="inp" value="<?php echo $bc_venue_name; ?>" style="margin-bottom:2px">
        <input type="hidden" name="venue_id" id="venue_id" value="<?php echo $bc_venue_id; ?>" />
        <?php if($action != "edit"){?>
        <input type="button" value="Save Event &amp; Add New Venue"  onclick="addVenue();" style="background:#eff2dd; font-weight:bold;"/>
        <input type="hidden" name="addvenue" value=""  id="addvenue"/>
        <?php } ?>
        <br />
        <font color="#000">Type Venue Name above. It will show options</font> </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Address:</td>
      <td align="left" class="bc_input_td"><input type="text" name="address1" id="ev_address1" class="inp" value="<?php echo $bc_venue_address; ?>" />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">City:</td>
      <td align="left" class="bc_input_td"><input type="text" name="city" id="ev_city" class="inp" value="<?php echo $bc_venue_city; ?>" />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Zip / Postal Code:</td>
      <td align="left" class="bc_input_td"><input type="text" name="zip" id="ev_zip" class="inp2" value="<?php echo $bc_venue_zip; ?>" />
      </td>
    </tr>
    <?php
	if($bc_event_type!=0){?>
    <!--   <tr>
      <td align="right" class="bc_label">Organization Name:</td>
      <td align="left" class="bc_input_td"><input  type="text" class="bc_input" style="width:350px" value="<?php echo $bc_event_host; ?>" id="event_host" name="event_host"  />
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Organization Description:</td>
      <td align="left" class="bc_input_td"><textarea name="host_description" id="host_description" class="bc_input" style="width:500px; height:200px"><?php echo $bc_host_description; ?></textarea>
      </td>
    </tr>-->
    <?php } ?>
    <tr>
      <td align="right" class="bc_label">Average Rating:</td>
      <td align="left" class="bc_input_td"><input type="text" name="averagerating" id="averagerating" class="bc_input" value="<?php echo $bc_averagerating; ?>"/></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Hosted By:</td>
      <td align="left" class="bc_input_td"><input type="text" name="added_by" id="added_by" class="bc_input" value="<?php echo $bc_added_by; ?>"/></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Featured Event:</td>
      <td align="left" class="bc_input_td"><select name="featured" id="featured" class="bc_input">
          <option value="">-- Select --</option>
          <option value = "0" <?php if($bc_featured == '0'){ echo 'selected="selected"'; }?> >NO</option>
          <option value = "1" <?php if($bc_featured == '1'){ echo 'selected="selected"'; } ?> >YES</option>
        </select>
        <!--<input type="text" name="event_status" id="event_status" class="bc_input" value="<?php //echo $bc_event_status; ?>"/>-->
      </td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Annual Event:</td>
      <td align="left" class="bc_input_td"><select name="specials" id="specials" class="bc_input">
          <option value="0">-- Select --</option>
          <?php
		  $res = mysql_query("select * from `specials`");
		  while($row = mysql_fetch_array($res)){
		  ?>
          <option value="<?php echo $row['id']; ?>" <?php if($bc_specials == $row['id']){ echo 'selected="selected"'; } ?> ><?php echo $row['name']; ?></option>
          <?php
		  }
		  ?>
        </select>
        <!--<input type="text" name="event_status" id="event_status" class="bc_input" value="<?php //echo $bc_event_status; ?>"/>-->
      </td>
    </tr>
    <?php if ($bc_event_type==0){?>
    <tr>
      <td align="right" class="bc_label">Free Event:</td>
      <td align="left" class="bc_input_td"><select name="free_event" id="free_event" class="bc_input" onchange="if(this.value=='0'){document.getElementById('showCostPrice').style.visibility='visible';}else{document.getElementById('showCostPrice').style.visibility='hidden';}">
          <option value = "0" <?php if($bc_free_event != '1'){ echo 'selected="selected"'; }?> >NO</option>
          <option value = "1" <?php if($bc_free_event == '1'){ echo 'selected="selected"'; } ?> >YES</option>
        </select>
        <span id="showCostPrice" style=" <?php if($bc_free_event == '1'){ echo 'visibility:hidden'; } ?>">Event Cost:
        <input type="text" class="new_input" style="width:150px;" value="<?php echo $bc_event_cost; ?>"  name="event_cost" />
        </span> </td>
    </tr>
    <?php 
			}
				else{?>
    <tr>
      <td align="right" class="bc_label">Event Cost:</td>
      <td align="left" class="bc_input_td"><input type="text" class="" style="width:150px;" value="<?php echo $bc_event_cost; ?>" name="event_cost" />
	  </td>
	</tr>
	<tr>
		<td align="right" class="bc_label">Alternative Buy Ticket URL</td>
		<td align="left" class="bc_input_td" style=" padding:0; margin:0">
			<input <?php if($bc_alter!=0){ echo 'checked="checked"'; } ?> type="checkbox" value="1" name="alter" onchange="if(this.checked==true){$('#alter_url').css('visibility','visible');}else{$('#alter_url').css('visibility','hidden');}" />
			<input type="text" style="width:300px; <?php if($bc_alter==0){ echo 'visibility:hidden'; } ?> " id="alter_url" name="alter_url" value="<?php if($bc_alter!=0){ echo $bc_alter_url; } ?>" />
			<!-- visibility:visible -->
		</td>
	</tr>
<?php
if($bc_alter==0){?>
	<tr>
		<td align="right" class="bc_label"></td>
		<td align="left" class="bc_input_td" style=" padding:0; margin:0">
			<a target="_blank" href="tickets.php?event_id=<?php echo $event_id; ?>" style="color:#0066FF;"><strong>Tickets</strong></a>
		</td>
	</tr>
        <?php
		}
			} 
			if($bc_free_event!='1' && $_GET['id']){?>
	<tr>
		<td align="right" height="30" class="bc_label"></td>
		<td align="left" class="bc_input_td" style=" padding:0; margin:0">
			<a target="_blank" href="close_rsvp.php?id=<?php echo $event_id; ?>" style="color:#0066FF;"><strong>Close RSVP</strong></a>
		</td>
	</tr>
	<?php
	}
	
	if($frmID){?>
    <tr>
      <td align="right" class="bc_label">Publishdate:</td>
      <td align="left" class="bc_input_td"><?php echo date("m/d/Y", strtotime($bc_publishdate)); ?></td>
    </tr>
    <tr>
      <td align="right" class="bc_label">Modify Date:</td>
      <td align="left" class="bc_input_td"><?php echo date("m/d/Y", strtotime($bc_modify_date)); ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><img src="images/save_standard.png" name="create" value="Save" style="cursor:pointer" onclick="save();" />
        <input type="hidden" name="create" value="Save" />
        <input type="hidden" name="event_type" value="<?php echo $bc_event_type; ?>">
      </td>
    </tr>
  </table>
</form>
<script type="text/javascript">
//<![CDATA[
(function($, Z){
  $('#z_listing_event_form').event_form({
    listing_class: 'QueuedEvent',
    listing_id: '1120455984',
    session_user: true,
    is_premium_listing: false,
    is_published_event: false,
    skip_campaign_redirect: false,
    partner_id: 0,
    internal_user: false,
    enhanced_paid_for: false,
    promoted_paid_for: false
  });
})($ZJQuery, Zvents);
//]]>
</script>
<?php if ( $bc_subcategory_id > 0 && $bc_category_id > 0 ) { 
	echo "<script>dynamic_Select('subcategory.php', ". $bc_category_id .", ". $bc_subcategory_id ." );</script>";
} ?>
<?php 
require_once("footer.php"); 
?>
<script type="text/javascript" src="tinymce/tiny_mce.js"></script>
<script type="text/javascript">

$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');

		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide()
	// ("#delImg_" + imgInfo[4]).css("display", "none");
	$("#"+imgInfo[4]).attr("src", "admin/images/no_image.png");
	if(imgInfo[5]=='showfile'){
	$('#'+imgInfo[6]).css('display','block');
	$('#showimg'+imgInfo[6]).html('<input type="file" name="images[]" />');
	
	}
	}
});

	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "event_description",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen,|,print,|,ltr,rtl,|,styleprops,hr,removeformat,|,preview,help,code",
		theme_advanced_buttons3 : "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "../style.css",
	});
</script>
<script type="text/javascript">
	function addVenue(){ 
		document.getElementById('addvenue').value="clicked";
	 	 $("#z_listing_event_form").submit();
	}
	
	
tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "host_description",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen,|,print,|,ltr,rtl,|,styleprops,hr,removeformat,|,preview,help,code",
		theme_advanced_buttons3 : "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "../style.css",
	});

</script>