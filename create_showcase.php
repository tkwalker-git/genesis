<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
if($_GET["id"]){
if(validateEID($_SESSION['LOGGEDIN_MEMBER_ID'],'events',$_GET['id'],'') =='false'){
echo "<script>window.location.href='activity_manager.php';</script>";
}
}

if( isset($_GET["id"]) )
	$frmID	=	$_GET["id"];

if( isset($_GET["type"]) )
	$bc_event_type	=	$_GET['type'];


	if(isset($_GET['private']) && $_GET['private'] == 1)
		$is_private	= $_GET['private'];
	else
		$is_private	= 0;


	if($bc_event_type){
		if($bc_event_type=='simple'){
			$bc_event_type=0;
		}
		elseif($bc_event_type=='showcase'){
			$bc_event_type=1;
		}
		elseif($bc_event_type=='premium'){
			$bc_event_type=2;
		}
		elseif($bc_event_type=='showcase'){
			$bc_event_type=3;
		}
	}

	$action = 'save';

$bc_userid	=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music	=	array();

$already_uploaded	=	0;

if (isset($_POST["create"]) || isset($_POST["create"]) ) {

$errors = array();

	@list($image_width, $image_height, $image_type) = getimagesize($_FILES["event_image"]["tmp_name"]);


	if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])){
			$tmp_bc_name  = time() . "_" . $_FILES["event_image"]["name"] ;
			$tmp_bc_name	=	str_replace(" ","_", $tmp_bc_name);
			move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' . $tmp_bc_name);
			$_SESSION['UPLOADED_TMP_NAME'] = $tmp_bc_name;
	}

	$bc_event_name			=	DBin($_POST["eventname"]);
	$bc_doctor_type			=	DBin($_POST['doctor_type']);
	$bc_event_description	=	DBin($_POST["event_description"]);
	$bc_category_id			=	$_POST["category_id"];
	$bc_subcategory_id		=	$_POST["subcategory_id"];
	$bc_gallery				=	DBin($_POST['gallery']);
	$bc_video_name			=	$_POST['video_name'];
	$bc_video_embed			=	$_POST['video_embed'];

	$bc_assessment_detail	=	DBin($_POST['assessment_detail']);


	$bc_location_name		=	$_POST['location_name'];
	if($bc_location_name == 'Location Name')
		$bc_location_name	=	'';

	$bc_address				=	$_POST['address'];
	if($bc_address == 'Address')
		$bc_address	=	'';

	$bc_city				=	$_POST['city'];
	if($bc_city == 'City')
		$bc_city	=	'';
		
	$bc_zip					=	$_POST['zip'];
	if($bc_zip == 'Zip / Postal Code')
		$bc_zip	=	'';

	$bc_event_type			=	$_POST['event_type'];
	$bc_modify_date			=	date("Y-m-d");
	$bc_assessment_url		=	$_POST['assessment_url'];

	if($bc_assessment_url){
		if ( substr($bc_assessment_url,0,7) == 'http://' || substr($bc_assessment_url,0,8) == 'https://' )
			$bc_assessment_url = $bc_assessment_url;
		else
			$bc_assessment_url = 'http://'.$bc_assessment_url;
	}

	$bc_seo_name			=	make_seo_names($_POST["eventname"],"events","seo_name","");

	if($bc_gallery == 'Create a name for your image gallery (i.e. Dress Code)'){
		$bc_gallery='';
	}

	$sucMessage = "";

	if ( trim($bc_event_name) == '')
		$errors[] = 'Please enter Event Title';
	if ( trim($bc_event_description) == '' )
		$errors[] = 'Please enter Doctor\'s Bio';
	if ( trim($_FILES["event_image"]["name"]) == '' && $_POST['mn_image']=='' )
		$errors[] = 'Please select Clinic Log';
/*
	if ( trim($bc_category_id) == '' )
		$errors[] = 'Please select Primary Category ';
	if ( trim($bc_subcategory_id) == '' )
		$errors[] = 'Please select Secondary Category';
*/

	if ( count( $errors) > 0 ) {
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';
	}


	if (!count($errors)) {

		if($frmID){
			
			if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])){
				$tmp_bc_name  = time() . "_" . $_FILES["event_image"]["name"] ;
				$tmp_bc_name	=	str_replace(" ","_", $tmp_bc_name);
				move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' . $tmp_bc_name);
				makeThumbnail($tmp_bc_name, 'event_images/', '', 275, 375,'th_');
				$sql_img = " , `event_image` = '". $tmp_bc_name ."' ";
			}
			
		
		$bs	=	getSingleColumn('id',"select * from `events` where `event_name`='$bc_event_name' && `id`='$frmID'");
		if($bs){
			$bc_seo_name	=	'';
		}
		else{
			$bc_seo_name	=	"`seo_name` = '$bc_seo_name', ";
		}
		
		$bc_event_status = $_POST['event_status'];
	
		$bc_location_image	= '';
		if ($_FILES['location_image']) {
			$liname = $_FILES['location_image']['name'];
			$ltname = $_FILES['location_image']['tmp_name'];
			if ( $ltname != '') {
				$li_image = str_replace(' ', '_',$liname);
				$li_image = time() . '_' . $liname;
				move_uploaded_file($ltname, 'images/'.$li_image);
				makeThumbnail($li_image, 'images/', '', 460, 318,'th_');
				$location_img_sql	= ", `location_img` = '". $li_image ."'";
			}
		}
		
		
		$bc_assessment_image	= '';
		if ($_FILES['assessment_image']) {
			$ainame		= $_FILES['assessment_image']['name'];
			$atname		= $_FILES['assessment_image']['tmp_name'];
			if ( $ainame != '') {
				$ai_image = str_replace(' ', '_',$ainame);
				$ai_image = time() . '_' . $ainame;
				move_uploaded_file($atname, 'images/'.$ai_image);
				makeThumbnail($ai_image, 'images/', '', 460, 200,'th_');
				$assessment_sql	=	", `assessment_image` = '". $ai_image ."'";
			}
		}

			$sql = "UPDATE `events` SET `event_name` = '". $bc_event_name ."', ". $bc_seo_name ." `doctor_type` = '". $bc_doctor_type ."', `event_description` = '". $bc_event_description ."' ". $sql_img .", `location_name` = '". $bc_location_name ."', `address` = '". $bc_address ."', `city` = '". $bc_city ."', `zip` = '". $bc_zip ."' ". $location_img_sql ." , `assessment_url` = '". $bc_assessment_url ."' ". $assessment_sql ." , `assessment_detail` = '". $bc_assessment_detail ."'   WHERE `id` = '". $frmID ."'";
			$res = mysql_query($sql);
		
		if($res){
			$event_id	=	$frmID;
			
		//	$event_url		= getEventURL($event_id);
		
		
		mysql_query("DELETE from `event_videos` where `event_id`='$event_id'");
		
		mysql_query("UPDATE `event_gallery` SET `name` = '$bc_gallery' WHERE `event_id` = '$event_id'");

	$gallery_id	=	getSingleColumn('id',"select * from `event_gallery` where `event_id`='$frmID'");
	
	if($gallery_id==''){
		mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$bc_gallery', '$event_id')");
		$gallery_id	=	mysql_insert_id();
	}	


	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']['name']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				
				if ( $einame != '') {
				
					$einame = str_replace(' ', '_',$einame);
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 120, 92,'th_');
					makeThumbnail($ei_image, 'event_images/gallery/', '', 480, 600,'sub_');
					//@unlink('images/products/'.$ei_image);
					
					if ( $ei_image != '' && $gallery_id > 0 ){
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
					
					}
				}		
			}
		}
		
		
		
			$sucMessage = "Event Successfully updated";
		}
		else{
			$sucMessage = "Error: Please try Later";
		}
	}
	else{
		
		$bc_image = '';
		//if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
		if ( $_SESSION['UPLOADED_TMP_NAME'] != '' ) {
			$bc_image  = $_SESSION['UPLOADED_TMP_NAME'];
				makeThumbnail($bc_image, 'event_images/', '', 163, 200,'');
				makeThumbnail($bc_image, 'event_images/', '', 163, 200,'th_');
				$sql_img = " event_image = '$bc_image' , ";
		}
		
		
		$bc_location_image	= '';
		if ($_FILES['location_image']) {
			$liname = $_FILES['location_image']['name'];
			$ltname = $_FILES['location_image']['tmp_name'];
			if ( $einame != '') {
				$li_image = str_replace(' ', '_',$liname);
				$li_image = time() . '_' . $liname;
				move_uploaded_file($ltname, 'images/'.$li_image);
				makeThumbnail($li_image, 'images/', '', 460, 318,'th_');
			}
		}
		
		$bc_assessment_image	= '';
		if ($_FILES['assessment_image']) {
			$ainame		= $_FILES['assessment_image']['name'];
			$atname		= $_FILES['assessment_image']['tmp_name'];
			if ( $ainame != '') {
				$ai_image = str_replace(' ', '_',$ainame);
				$ai_image = time() . '_' . $ainame;
				move_uploaded_file($atname, 'images/'.$ai_image);
				makeThumbnail($ai_image, 'images/', '', 460, 200,'th_');
			}
		}

		$bc_source_id	=	"USER-".rand(); 
		$bc_added_date	=	 date("Y-m-d");

		if($is_private)
			$bc_event_status = "0";
		else
			$bc_event_status = "1";

			$bc_event_status = "1";

	$sql	= "INSERT INTO `events` (`id`, `userid`, `event_name`, `seo_name`, `doctor_type`, `event_description`, `event_image`, `location_name`, `address`, `city`, `zip`, `location_img`, `assessment_url`, `assessment_image`, `assessment_detail`, `added_date`, `event_status`, `event_type`) VALUES (NULL, '". $bc_userid ."', '". $bc_event_name ."', '". $bc_seo_name ."', '". $bc_doctor_type ."', '". $bc_event_description ."', '". $bc_image ."', '". $bc_location_name ."', '". $bc_address ."', '". $bc_city ."', '". $bc_zip ."', '".$li_image."', '". $bc_assessment_url ."', '". $ai_image ."', '". $bc_assessment_detail ."', '". $bc_added_date ."', '". $bc_event_status ."', '". $bc_event_type ."');";

		$res		=	mysql_query($sql);
		$frmID		=	mysql_insert_id();
		$event_id 	=	$frmID;

		if ($res) {
	//	$event_url	= getEventURL($event_id);

	// start main gallery & image upload ///
	if($bc_gallery){
		mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '". $bc_gallery ."', '". $event_id ."')");
		$gallery_id	=	mysql_insert_id();
	}

	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']['name']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = str_replace(' ', '_',$einame);
					$ei_image = time() . '_' . $einame;
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 107, 92,'th_');
					makeThumbnail($ei_image, 'event_images/gallery/', '', 480, 600,'sub_');

					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
						mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}
			}
		}

		} else {
			$sucMessage = "Error: Please try Later";
		}
		}}}

	if($res){

///// ADD VIDEO START /////
		if ( is_array($bc_video_embed) ) {
			for($f=0;$f <count($bc_video_embed);$f++) {
				$video_embed	= $bc_video_embed[$f];
				$video_name		= $bc_video_name[$f];
				if($video_name == 'Enter the name of your video')
					$video_name = '';
					if($video_embed!=''){
						mysql_query("INSERT INTO `event_videos` (`id`, `video_name`, `video_embed`, `event_id`) VALUES (NULL, '$video_name', '$video_embed', '$event_id')");
					}
				}
			}
///// ADD VIDEO END /////


	
////////////////////////// //////
	
	if ($bc_venu_id != ''){
				$sql_venue = "insert into venue_events (venue_id, event_id) values('" . $bc_venu_id . "','" . $event_id . "')";
				mysql_query($sql_venue);	
			}
			
			if($bc_event_music){
				foreach($bc_event_music as $bc_event_music_value){
					$sql_event_music = "insert ignore into event_music (event_id, music_id) values('" . $event_id . "','" . $bc_event_music_value . "')";			
					mysql_query($sql_event_music);
				}
			}


	if(!$_GET['id']){
	if($bc_event_type==1 || $bc_event_type==2 || $bc_event_type==3 || $_GET['r']=='py'){
		$custom_order_id = time();
		$date		=	date('Y-m-d');
		$res = mysql_query("INSERT INTO `orders` (`id`, `userid`, `total_price`, `discount`, `net_total`, `date`, `type`, `main_ticket_id`, `coupon_code`, `order_id`) VALUES (NULL, '$userid', '1', '', '', '$date', 'premium', '$event_id', '', '$custom_order_id')");
		
		echo "<script>window.location.href='".ABSOLUTE_PATH."saved.php?type=event&id=".$event_id."'</script>";
	/*	echo "<script>window.location.href='".ABSOLUTE_PATH_SECURE."create_flyer_step2.php?id=".$event_id."'</script>";	  */
		}
	else{
		echo "<script>window.location.href='".ABSOLUTE_PATH."saved.php?type=event&id=".$event_id."'</script>";
		}
	}
	} // end if $res
	
	else{
		$sucMessage	=	$err;
	}


if ($event_id != "" || isset($_POST["submit"])) {

    $dates_q	= "select * from `event_dates` where `event_id` = '$event_id' ORDER BY `event_date` ASC";
	$dates_res	= mysql_query($dates_q);
	$first_date	= "";
	$dates		= "";
	$i			= 0;

	while($dates_r = mysql_fetch_assoc($dates_res)){
		if(mysql_num_rows($dates_res) > 0){
			$date	= date("m/d/Y",strtotime($dates_r['event_date']));
			if($i<1){ $first_date = $date; $i++;}
			$dates	= $dates."'".$date."', ";
		}else{
			$date = $dates_r['event_date'];
			$first_date = $date;
			$dates = "'".date("m/d/Y",strtotime($date))."'";
		}
	}
}

	if($first_date != ''){
		$yr		= date("Y",strtotime($first_date));
		$mon	= date("m",strtotime($first_date));
		$mon1	= $mon - 1;
		$dy		= date("d",strtotime($first_date));
		$first_date = $yr.", ".$mon1.", ".$dy;
	}

if($sucMessage == "Event Successfully updated"){

//	$event_url		= getEventURL($event_id);

	$eventPy		= getSingleColumn("id","select * from `orders` where `main_ticket_id`='$event_id' && `type`='flyer' && `total_price`!=''");
	 if($_GET['r']=='py' || $eventPy=='' || $eventPy==0){
	/*	echo "<script>window.location.href='".ABSOLUTE_PATH_SECURE."create_flyer_step2.php?id=".$event_id."'</script>";  */
		echo "<script>window.location.href='".$event_url."'</script>";	
		}
	else{
		echo "<script>window.location.href='".$event_url."'</script>";
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
		$bc_doctor_type			=	$row['doctor_type'];
		$bc_event_type			=	$row['event_type'];

		if($bc_userid!=$user_id)
			echo "<script>window.location.href='index.php';</script>";

		if($bc_event_type!='1')
			echo "<script>window.location.href='create_event.php?id=$frmID';</script>";

		$bc_location_name		=	$row['location_name'];
		$bc_address				=	$row['address'];
		$bc_city				=	$row['city'];
		$bc_zip					=	$row['zip'];
		$bc_location_img		=	$row['location_img'];

		$bc_assessment_url		=	$row['assessment_url'];
		$bc_assessment_image	=	$row['assessment_image'];
		$bc_assessment_detail	=	DBout($row['assessment_detail']);

		$bc_event_name			=	$row["event_name"];
		$bc_event_description	=	$row["event_description"];
		$bc_image				=	$row["event_image"];
		$bc_event_status		=	$row["event_status"];
		$bc_added_by			=	$row["added_by"];

		$bc_type				=	$row['type'];

		$rt = mysql_query("select * from `event_gallery` where `event_id`='$frmID'");
		while($rq = mysql_fetch_array($rt)){
			$bc_gallery		=	$rq['name'];
			$bc_gallery_id	=	$rq['id'];
		}

		$get_event_videos	=	mysql_query("SELECT * FROM  `event_videos` where `event_id`='$frmID'");
		if(mysql_num_rows($get_event_videos)){
			$bc_video_name = array();
			$bc_video_embed = array();
			while($eventVideos=mysql_fetch_array($get_event_videos)){
				$bc_video_name[]			=	$eventVideos['video_name'];
				$bc_video_embed[]			=	$eventVideos['video_embed'];
			}
		}

		$get_gallery_images	=	mysql_query("SELECT * FROM  `event_gallery_images` where `gallery_id`='$bc_gallery_id'");
		while($galleryImage=mysql_fetch_array($get_gallery_images)){
			if($galleryImage['image']!=''){
				$bc_gallery_images[]	=	$galleryImage['image'];
				$bc_gallery_images_id[]	=	$galleryImage['id'];
			}
		}

	
		$event_id = $frmID;

	}

}


	$sql = "select * from `venues` where `id`='$bc_venu_id'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
		$bc_venue_address	=	$ro['venue_address'];
		$bc_venue_name		=	$ro['venue_name'];
		$bc_venue_city		=	$ro['venue_city'];
		$bc_venue_zip		=	$ro['venue_zip'];
	}

$meta_title	= "Create Showcase";

include_once('includes/header.php');
?>

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.accordion.js"></script>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.tipsy.js"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.core.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.theme.css">
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.css">
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(".fancybox2").fancybox({
		'titleShow'			: false,
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'width'				: 540,
		'height'			: 1250,
		'enableEscapeButton': true,
		'type'				: 'iframe'
	});

	$(".fancybox").fancybox({
		'titleShow'			: false,
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic'
	});

	var unique = $('input.unique');
	unique.click(function(){ 
		unique.removeAttr('checked');
		$(this).attr('checked', true);
	});
});


$(document).ready(function() {
	$("#venue_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>get_venue_list.php", {
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



function add_more_image(id){
//	var limitImage = 5;
//	if(id!=limitImage){  
	var next_row 	= id+1;
	var new_url_feild = '<div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg'+next_row+'"><input type="file" name="images[]" /><img style="padding: 3px 5px 0 0;cursor:pointer" src="images/icon_delete2.gif" align="left" onclick="remove_image('+next_row+')"></div>';
	$('#add_more_image_area').append(new_url_feild);	
	$('#add_more_image_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_image('+next_row+')" />');
//	}
//	else{
//		alert("You can not upload more then "+limitImage+" images");
//	}
}

function remove_image(id){
	$('#showimg'+id).remove();
	var a = $('#add_more_image_btn').html();
	var b = a.split('add_more_image(');
	b = b[1].split(')');
	c = b[0]-1;
	$('#add_more_image_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_image('+c+')" />');
}



function add_more_video(id){
//	var limitVideos = 5;
//	if(id!=limitVideos){  
	var next_row 	= id+1;
	var new_url_feild = '<div id="showvid'+next_row+'"><div style="float:left; width:380px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Video Name:</div><input type="text" name="video_name[]" value="Enter the name of your video" id="video_name'+next_row+'" onFocus="removeText(this.value,\'Enter the name of your video\',\'video_name'+next_row+'\');" onBlur="returnText(\'Enter the name of your video\',\'video_name'+next_row+'\');" class="new_input" style="width:350px;"><img style="padding: 3px 0 0 0;cursor:pointer" src="images/icon_delete2.gif" align="right" onclick="remove_video('+next_row+')"></div><div style="float:left; width:454px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Copy and Paste the Video Embed Code Here:</div><textarea class="new_input" name="video_embed[]" style="width:466px; height:130px;"></textarea></div><div class="clr"></div></div></div>';
	$('#add_more_video_area').append(new_url_feild);	
	$('#add_more_video_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_video('+next_row+')" />');
//	}
//	else{
//		alert("You can not upload more then "+limitVideos+" videos");
//	}
}

function remove_video(id){
	$('#showvid'+id).remove();
	var a = $('#add_more_video_btn').html();

	var b = a.split('add_more_video(');
	b = b[1].split(')');
	c = b[0]-1;

	$('#add_more_video_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_video('+c+')" />');
	
}


function draft(){
$err	=	$('#check_errors').val();
if($err == 1){
$errText	=	$('#dErrors').val();
	alert($errText);
	$('.box').css('display','none');
	$('#box1').css('display','block');
return false;
}
	if(document.getElementById('event').value=='' || document.getElementById('event').value=='Enter only the name of your event'){
	alert("Please enter Event Title before saving your event as draft");
	}
	else{
	$("#z_listing_event_form").attr("action", "draft.php");
	$("#z_listing_event_form").submit();
	}
}


<?php if($_GET['t']){?>
$(document).ready(function(){
	$('.box').css('display','none');
	$('#box<?php echo $_GET["t"]; ?>').css('display','block');
});
<?php } ?>

</script>
<style>
.new_ticket_right td{
	height:48px;
	padding:0 16px;
	}

.ev_new_box_center{
	margin:auto;
	width:936px;
	}

.ev_new_box_center .basic_box, .ev_new_box_center .featured_box, .ev_new_box_center .premium_box, .ev_new_box_center .custom_box{
	width:234px;
	height:528px;
	float:left;
	position:absolute
	}
	
	
.ev_new_box_center .basic_box ul, .ev_new_box_center .featured_box ul, .ev_new_box_center .premium_box ul, .ev_new_box_center .custom_box ul{
	padding:10px 0 0 18px;
	margin:0
}

.ev_new_box_center .basic_box ul li, .ev_new_box_center .featured_box ul li, .ev_new_box_center .premium_box ul li, .ev_new_box_center .custom_box ul li{
	list-style:circle;
	font-size:12px
}

.ev_new_box_center .basic_box{
	background:url(images/basic_box.gif) no-repeat;
	}

.ev_new_box_center .featured_box{
	background:url(images/featured_box.gif) no-repeat;
	left:234px;
	}
	
.ev_new_box_center .premium_box{
	background:url(images/premium_box.gif) no-repeat;
	left:468px;
	}
	
.ev_new_box_center .custom_box{
	background:url(images/custom_box.gif) no-repeat;
	left:702px;
	}
	
	
.ev_new_box_center .basic_box .black, .ev_new_box_center .featured_box .black, .ev_new_box_center .premium_box .black, .ev_new_box_center .custom_box .black{	
	filter:alpha(opacity=15);
	-ms-filter:alpha(opacity=15);
	-moz-opacity:0.15;
	opacity:0.15;
	background:#000000;
	width:234px;
	height:528px;
	position:absolute;
	}
	
	
.ev_new_box_center .black:hover{
	display:none;
	}
	
.ev_new_box_center .basic_box:hover > .black, .ev_new_box_center .featured_box:hover > .black, .ev_new_box_center .premium_box:hover > .black, .ev_new_box_center .custom_box:hover > .black{
	display:none;
	}
	
	
.ev_new_box_center .basic_box:hover, .ev_new_box_center .featured_box:hover, .ev_new_box_center .premium_box:hover, .ev_new_box_center .custom_box:hover{
	z-index:9999;
	-moz-box-shadow:0px 0px 7px 2px #464646;
	-webkit-box-shadow:0px 0px 7px 2px #464646;
	-khtml-box-shadow:0px 0px 7px 2px #464646;
	box-shadow:0px 0px 7px 2px #464646;
	filter: progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=0),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=90),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=180),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=270);
	}
	.ev_new_box_center .detail{
	padding:132px 10px 0;
	height:280px;
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	line-height:18px;
}
	
#showimg1,#showimg3{
	padding: 5px 0 5px 20px;
	width: 45%
	}

#showimg2,#showimg4{
	padding: 5px 0 5px 27px;
	width:45%;
	}

</style>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Create Showcase</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
        <input type="hidden" name="id" id="queued_event_id" value="1120455984" />
        <div align="right" style="padding:5px 15px 9px 0; font-size:16px;">
		<?php
			if($_GET['id']){
			//	$pym = attribValue("orders","total_price"," where main_ticket_id='$event_id' && `type`='flyer' && `total_price`!=''");
			//	if($bc_event_type!=0 && $pym==0){
			//		$disabled = 'yes';
			//	}
		
		?>
			<!--	<strong>Your event is currently	</strong>   -->
			<?php
				if($disabled!='yes'){?>
					<!--<select name="event_status" <?php echo $disabled; ?>>
						<option value="1" <?php if($bc_event_status==1){ echo 'selected="selected"'; } ?>>Active</option>
						<option value="0" <?php if($bc_event_status==0){ echo 'selected="selected"'; } ?>>Inactive</option>
					</select>-->
			<?php
				}
				else{
					if($bc_event_status==1 || $is_private){?>
						<strong>Active</strong>
						<input type="hidden"  value="<?php echo $bc_event_status; ?>" name="event_status"/>
					<?php }
					elseif($bc_event_status==0)
						echo '<strong>Inactive</strong>';
				}
			}
		else{?>
		<!--<img src="<?php echo IMAGE_PATH; ?>save_as_draft_new.png" alt="" value="Save As Draft" title="Save As Draft" onClick="draft();" style="cursor:pointer" align="right">-->
		<?php } ?>
		</div>
        <div class="clr"></div>
        <?php include('dashboard_menu_tk.php'); ?>
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">

                <h3>STEP 1 | <span>ADD DOCTOR INFORMATION</span></h3>
                <div id="box" class="box">
                	<div id="head">Event Title</div>
                  <div class="ev_title">
                    <input type="text" name="eventname" value="<?php if ($bc_event_name){echo $bc_event_name;}?>" id="event" >
                  </div>
                  <div id="head">Doctor Type</div>
                  <div>
                  	<select class="new_input" name="doctor_type">
                    	<?php
							$res = mysql_query("select * from `doctor_type`");
							while($row = mysql_fetch_array($res)){
						?>
                    	<option value="<?php echo $row['id']; ?>" <?php if ($bc_doctor_type == $row['id']){ echo 'selected="selected"'; } ?>><?php echo $row['type']; ?></option>
                        <?php } ?>
                  	</select>
                  </div>

                  <div id="head">Doctor's Bio</div>
                  <div>
                    <textarea name="event_description" id="event_description" class="bc_input" style="width:825px; height:250px"><?php echo $bc_event_description; ?></textarea>
                  </div>
                </div>
                
                <h3>STEP 2 | <span>ADD GALLERIES</span></h3>
                <div id="box" class="box">
                 <div id="head">Photo Gallery:
                    <div class="info" id="info5" title="Provide photo galleries that will help your customers get a better feel for you event. (i.e. What to wear, Past event, What to expect)"></div>
                  </div>
                  <div class="gallery_area">
                    <div id="head" style="padding:16px 0 12px; font-size:22px">Gallery Name: </div>
                    <input type="text" name="gallery" value="<?php if ($bc_gallery){echo $bc_gallery;}else{ echo "Create a name for your image gallery (i.e. Dress Code)"; } ?>" id="gname" onFocus="removeText(this.value,'Create a name for your image gallery (i.e. Dress Code)','gname');" onBlur="returnText('Create a name for your image gallery (i.e. Dress Code)','gname');" class="new_input" style="width:534px;" /><br />
	<span style="color:#FF0000">*if you want upload images must write gallery name</span>
                    <?php
					  $i=0;
					 if(is_array($bc_gallery_images)){
					 $count_images = count($bc_gallery_images);
					 for ($z=0;$z < $count_images;$z++){
						
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
								echo $image_del = '<br><img src="admin/images/remove_img.png" class="delImg" id="'.$gallery_images_id.'" style="cursor:pointer" rel="event_gallery_images|image|'.$gallery_images.'|event_images/gallery/|delImg_image|showfile|'.$i.'" />';
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
								echo $image_del = '<br><img src="admin/images/remove_img.png" class="delImg" id="'.$gallery_images_id.'" style="cursor:pointer" rel="event_gallery_images|image|'.$gallery_images.'|event_images/gallery/|delImg_image|showfile|'.$i.'" />';
								?>
                    </div>
                    <?php
								if($i%2==0){
								echo '<div class="clr"></div>';
								}
								
								}
							} 
							}
							
							if(count($bc_gallery_images)<4){
							
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
                    <div class="ev_fltlft" id="showimg1">
                      <input type="file" name="images[]" />
                    </div>
                    <div class="ev_fltlft" id="showimg2">
                      <input type="file" name="images[]" />
                    </div>
                    <div class="clr"></div>
                    <div class="ev_fltlft" id="showimg3">
                      <input type="file" name="images[]" />
                    </div>
                    <div class="ev_fltlft" id="showimg4">
                      <input type="file" name="images[]" />
                    </div>
                    <?php
							}
							
				   if($bc_event_type==1){?>
                    <div id="add_more_image_area"></div>
                    <div class="clr"></div>
                    <div align="right"><br />
                      <br />
                      <span id="add_more_image_btn"><img src="<?php echo IMAGE_PATH; ?>add_more.png" style="cursor:pointer" id="" onClick="add_more_image(<?php if ($count_images>4){echo $count_images;} else{ echo "4"; } ?>)" /></span> </div>
                    <?php } ?>
                  </div>
                  <br class="clr">

                  <div id="head">Video Gallery:
                    <div class="info" title="If you have a video uploaded to Vimeo or YouTube, simple copy the embed code from that site and paste it in the box below"></div>
                  </div>
                  <div class="gallery_area" style="padding:0px; width:875px">
                    <?php
				  $i=0;
					 if(is_array($bc_video_embed)){
					 $count_videos = count($bc_video_embed);
					 for ($z=0;$z < $count_videos;$z++){

						$video_embed	= $bc_video_embed[$z];
						$video_name		= $bc_video_name[$z];
						$i++;
							?>
                    <div id="showvid<?php echo $i ; ?>">
                      <div style="float:left; width:380px; margin-right:20px">
                        <div id="head" style="padding:16px 0 12px; font-size:22px">Video Name:</div>
                        <input type="text" name="video_name[]" value="<?php if ($video_name){ echo $video_name; } else{ echo "Enter the name of your video"; } ?>" id="video_name<?php echo $i; ?>" onFocus="removeText(this.value,'Enter the name of your video','video_name<?php echo $i; ?>');" onBlur="returnText('Enter the name of your video','video_name<?php echo $i; ?>');" class="new_input" style="width:350px;">
                        <?php
					  if($i>1){?>
                        <img style="padding: 3px 0 0 0;cursor:pointer" src="images/icon_delete2.gif" align="right" onClick="remove_video(<?php echo $i; ?>)">
                        <?php
					  } ?>
                      </div>
                      <div style="float:left; width:454px; margin-right:20px">
                        <div id="head" style="padding:16px 0 12px; font-size:22px">Copy and Paste the Video Embed Code Here:</div>
                        <textarea class="new_input" name="video_embed[]" style="width:466px; height:130px;"><?php if ($video_embed){ echo $video_embed; }?>
</textarea>
                      </div>
                      <div class="clr"></div>
                    </div>
                    <?php				

							} // END FOR
							} // END if is_array
						else{
						?>
                    <div style="float:left; width:380px; margin-right:20px">
                      <div id="head" style="padding:16px 0 12px; font-size:22px">Video Name:</div>
                      <input type="text" name="video_name[]" value="<?php if ($bc_video_name){ echo $bc_video_name; } else{ echo "Enter the name of your video"; } ?>" id="video_name" onFocus="removeText(this.value,'Enter the name of your video','video_name');" onBlur="returnText('Enter the name of your video','video_name');" class="new_input" style="width:350px;">
                    </div>
                    <div style="float:left; width:454px; margin-right:20px">
                      <div id="head" style="padding:16px 0 12px; font-size:22px">Copy and Paste the Video Embed Code Here:</div>
                      <textarea class="new_input" name="video_embed[]" style="width:466px; height:130px;"><?php if ($bc_video_embed){ echo $bc_video_embed; }?>
</textarea>
                    </div>
                    <div class="clr"></div>
                    <?php
						}
					if($bc_event_type==1){?>
                    <div id="add_more_video_area"></div>
                    <div class="clr"></div>
                    <div align="right"><br />
                      <br />
                      <span id="add_more_video_btn"><img src="<?php echo IMAGE_PATH; ?>add_more.png" style="cursor:pointer" id="" onClick="add_more_video(<?php if ($count_videos){echo $count_videos;} else{ echo "1"; } ?>)" /></span> </div>
                    <?php } ?>
                  </div>
                  
                </div>
                
                <h3>STEP 3 | <span>ADD SHOWCASE ATTRIBUTES</span></h3>
                <div id="box" class="box"> 
                
                
                <div id="head">Clinic Logo:
                    <div class="info" id="info4" title="This is the main Clinic Logo we will use for your advertising. Make sure the image you upload is a high quality, appropriate image."></div>
                </div>
                
                <div class="ev_fltlft">
					<?php 
                    if( $bc_image != ''  ) {
	                    if ( substr($bc_image,0,7) != 'http://' && substr($bc_image,0,8) != 'https://' ) 
    		                $bc_image1 = ABSOLUTE_PATH . 'event_images/th_'.$bc_image;
            	        else
							$bc_image1 = $bc_image;
						echo '<img src="'.$bc_image1 .'" class="dynamicImg" id="delImg_image" width="75" height="76" align="left" style="padding:3px"  />';
						echo "<a href='".ABSOLUTE_PATH."event_images/".$bc_image."' class='fancybox'><img src='images/preview_img.png' /></a><br><br>";
						echo $image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'"style="cursor:pointer" rel="events|event_image|'.$bc_image.'|event_images/|delImg_image" />'; ?>
                    	<input type="hidden" name="mn_image" value="<?php echo $bc_image; ?>" />
                    <?php } ?>
                    <br />
                    <input type="file" name="event_image" /><br />
                </div>
                <br class="clear" />
                <div id="head">Clinic Location</div>
				<input type="text" name="location_name" id="location_name" class="new_input" value="<?php if ($bc_location_name){ echo $bc_location_name; }else{ echo "Location Name"; } ?>" onFocus="removeText(this.value,'Location Name','location_name');" onBlur="returnText('Location Name','location_name');" style="margin-bottom:2px; width:200px" />
                  &nbsp;&nbsp;
                  <input type="text" name="address" id="ev_address1" class="new_input" value="<?php if ($bc_address){ echo $bc_address; } else{ echo 'Address'; } ?>"  onFocus="removeText(this.value,'Address','ev_address1');" onBlur="returnText('Address','ev_address1');" style="width:200px">
                  &nbsp;&nbsp;
                  <input type="text" name="city" id="ev_city" class="new_input" value="<?php if ($bc_city){ echo $bc_city; } else{ echo 'City'; } ?>"  onFocus="removeText(this.value,'City','ev_city');" onBlur="returnText('City','ev_city');" style="width:200px">
                  &nbsp;&nbsp;
                  <input type="text" name="zip" id="ev_zip" class="new_input" value="<?php if ($bc_zip){ echo $bc_zip; } else{ echo 'Zip / Postal Code'; } ?>"  onFocus="removeText(this.value,'Zip / Postal Code','ev_zip');" onBlur="returnText('Zip / Postal Code','ev_zip');" style="width:190px">
                  <br>
				<div id="head">Clinic Location Image</div>
                <?php 
                    if( $bc_location_img != ''  ) {
							$bc_image1 = $bc_location_img;
						echo '<img src="'.IMAGE_PATH.$bc_location_img.'" class="dynamicImg" id="del_location_img" width="75" height="76" align="left" style="padding:3px"  />';
						echo "<a href='".IMAGE_PATH.$bc_location_img."' class='fancybox'><img src='images/preview_img.png' /></a><br><br>";
						echo $image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'"style="cursor:pointer" rel="events|location_img|'.$bc_location_img.'|images/|del_location_img" />'; ?>
                    	<input type="hidden" name="" value="<?php echo $bc_image; ?>" />
                    <?php } ?>
                    <br class="clear" />
                    <input type="file" name="location_image" /><br />
                    
                <div id="head">Assessment Summary</div>
                <div>
                	<textarea name="assessment_detail" id="assessment_detail" class="bc_input" style="width:825px; height:250px"><?php echo $bc_assessment_detail; ?></textarea>
                </div>
                
                <div id="head">Assessment demo image</div>
                
                <?php 
                    if( $bc_assessment_image != ''  ) {
						$bc_image1 = $bc_assessment_image;
						echo '<img src="'.IMAGE_PATH.$bc_assessment_image.'" class="dynamicImg" id="del_assessment_image" width="75" height="76" align="left" style="padding:3px"  />';
						echo "<a href='".IMAGE_PATH.$bc_assessment_image."' class='fancybox'><img src='images/preview_img.png' /></a><br><br>";
						echo $image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'"style="cursor:pointer" rel="events|assessment_image|'.$bc_assessment_image.'|images/|del_assessment_image" />'; ?>
                    	<input type="hidden" name="" value="<?php echo $bc_assessment_image; ?>" />
                    <?php } ?>
                    <br class="clear" />
                    <input type="file" name="assessment_image" /><br />
                    <br class="clear" />
    
                <div id="head">Link to take Assessment</div>  
                  	<input type="trex" class="new_input" value="<?php echo $bc_assessment_url; ?>" name="assessment_url" style="width:350px;" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
        
		
          <img src="<?php echo IMAGE_PATH; ?>publish_new.png" name="create" value="Create Event" style="cursor:pointer" onClick="save();"  align="right" />
        
		<?php
		
		 if( $_GET['id'] && $bc_event_type == 1 ){
		 	echo '<a href="'.ABSOLUTE_PATH_SECURE.'fbflayer/index.php?id='.$frmID.'" class="fancybox2"><img src="'.IMAGE_PATH.'/preview_new.png" align="right"  /></a>';
		 }
		 
		 if($_GET['id']==''){
		?>
         <!-- <img src="<?php echo IMAGE_PATH; ?>save_as_draft_new.png" alt="" value="Save As Draft" title="Save As Draft" onClick="draft();" style="cursor:pointer" align="right">-->
		  <?php
		  }
		  ?>
          <!--<a href="#"><img src="<?php echo IMAGE_PATH; ?>preview.gif" alt="" title="Preview"></a>-->
          <input type="hidden" name="evntIdForDraft" value="<?php echo $event_id; ?>" />
          <input type="hidden" name="create" value="Create Event" />
          <input type="hidden" name="event_type" value="<?php echo $bc_event_type; ?>" />
          <div class="clr"></div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php');?>
<script>
	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "event_description,assessment_detail",
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
		content_css : "style.css",
	});
	
	
$(".delImg").click(function(){
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
//		alert(imgID);
//		return false;
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("admin/deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide()
	// ("#delImg_" + imgInfo[4]).css("display", "none");
	$("#"+imgInfo[4]).attr("src", "admin/images/no_image.png");
	if(imgInfo[5]=='showfile'){
	$('#'+imgInfo[6]).css('display','block');
	$('#showimg'+imgInfo[6]).html('<input type="file" name="images[]" />');
	
	}
	}
});

	$(function() {
		$('.info').tipsy({gravity: 'w', fade: true});
	});

	function save(){
		$err	=	$('#check_errors').val();
		if($err == 1){
			$errText	=	$('#dErrors').val();
			alert($errText);
			$('.box').css('display','none');
			$('#box1').css('display','block');
		return false;
		}
			$("#z_listing_event_form").attr("action", "");
			$("#z_listing_event_form").submit();
	} 

</script>