<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

if( isset($_GET["id"]) )
	$frmID	=	$_GET["id"];
if( isset($_GET["type"]) )
	$bc_event_type	=	$_GET['type'];
	
	if($bc_event_type){
		if($bc_event_type=='simple'){
			$bc_event_type=0;
		}
		elseif($bc_event_type=='flyer'){
			$bc_event_type=1;
		}
		elseif($bc_event_type=='premium'){
			$bc_event_type=2;
		}
	}
	$action = 'save';

$bc_userid	=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music	=	array();

$already_uploaded	=	0;

if (isset($_POST["create"]) || isset($_POST["create"]) ) {

	if (isset($_FILES["event_image"]) && !empty($_FILES["event_image"]["tmp_name"])) {
			$tmp_bc_name  = time() . "_" . $_FILES["event_image"]["name"] ;
			$tmp_bc_name	=	str_replace(" ","_", $tmp_bc_name);
			move_uploaded_file($_FILES["event_image"]["tmp_name"], 'event_images/' . $tmp_bc_name);
			$_SESSION['UPLOADED_TMP_NAME'] = $tmp_bc_name;
	}
	
	$occurrences			=	$_POST['occurrences'];
	$bc_event_source 		= 	($_SESSION['usertype']==2) ? 'Promoter' : 'User';
	$bc_event_name			=	$_POST["eventname"];
	$bc_event_description	=	DBin($_POST["event_description"]);
	$bc_category_id			=	$_POST["category_id"];
	$bc_subcategory_id		=	$_POST["subcategory_id"];
	$bc_event_age_suitab	=	$_POST['min_age_allow'];
	$bc_men_preferred_age	=	$_POST['men_preferred_age'];
	$bc_women_preferred_age	=	$_POST['women_preferred_age'];
	$bc_event_music			=	$_POST['event_music'];
	$bc_occupation_target	=	$_POST['occupation_target'];
	$bc_gallery				=	$_POST['gallery'];
	$bc_video_name			=	$_POST['video_name'];
	$bc_video_embed			=	$_POST['video_embed'];
	$bc_venu_id				=	$_POST['venue_id'];
	$bc_event_type			=	$_POST['event_type'];
	$bc_modify_date			=	date("Y-m-d");
	$bc_free_event			=	$_POST['free_event'];
	$bc_specials			=	$_POST['specials'];
	$bc_seo_name			=	make_seo_names($bc_event_name,"events","seo_name","");
	
	
// FOR TICKET //
	$mainTitle		= $_POST["mainTitle"];
	$mainTitleOp	= $_POST['mainTitleOp'];
	
	if($mainTitleOp == 'Other')
		$mainTitle	= $_POST['mainTitle'];
	else
		$mainTitle	= $_POST['mainTitleOp'];
		
	$mainPrice			= $_POST["mainPrice"];
	$event_ticket_id	= $_POST['event_ticket_id'];
	
	if($_POST['split_fee']=='prometer'){
		$bc_prometer_service_fee = 0;
		$bc_buyer_service_fee	 = 100;
	}
	elseif($_POST['split_fee']=='buyer'){
		$bc_prometer_service_fee = 100;
		$bc_buyer_service_fee	 = 0;
	}
	else{
		$bc_prometer_service_fee	=	$_POST["prometer_service_free"];
		$bc_buyer_service_fee		=	$_POST["buyer_service_free"];
	}
		$start_sales_date			= 	$_POST['start_sales_date'];
		$end_sales_date				=	$_POST['end_sales_date'];
		$quantity_available			=	$_POST['quantity_available'];
	
	
	if($start_sales_date){
		$start_sales_date			=	date('Y-m-d', strtotime($_POST['start_sales_date']));
	 	$startTime					=	$_POST['start_sales_hrs'].":".$_POST['start_sales_min']." ".$_POST['start_sales_ampm'];
		$start_sales_time			=	date('H:i', strtotime($startTime));
	}
	else{
		$start_sales_date			=	date('Y-m-d');
		$start_sales_time			=	date('H:i');
	}
	
	if($end_sales_date){
		$end_sales_date				=	date('Y-m-d', strtotime($_POST['end_sales_date']));
		$endTime					=	$_POST['end_sales_hrs'].":".$_POST['end_sales_min']." ".$_POST['end_sales_ampm'];
		$end_sales_time				= 	date('H:i', strtotime($endTime));
	}
	
	$ticket_description				=	$_POST['ticket_description'];
	if($ticket_description == 'Access for one person to the launch celebration for the new website Eventgrabber'){
		$ticket_description = '';
	}
// FOR TICKET END//
	
	if($bc_gallery=='Create a name for your image gallery (i.e. Dress Code)'){
		$bc_gallery='';
	}
	
	if($_POST['noTicket'] || $bc_free_event=='0'){
		$bc_event_cost			=	$_POST['event_cost'];
	}
	else{
		$bc_event_cost			=	'';
	}

	
	$sucMessage = "";
	
	$errors = array();

	if ( trim($bc_event_name) == '' || $bc_event_name == 'Enter only the name of your event' )
		$errors[] = 'Please enter Eevent Title';
	if ( trim($bc_event_description) == '' )
		$errors[] = 'Please enter Eevent Details';
	if ( trim($bc_venu_id) == '' )
		$errors[] = 'Please enter Location';
	if($_POST['noTicket'] || $bc_free_event=='0'){
		if ( trim($bc_event_cost) == '' )
			$errors[] = 'Please enter Event Cost';
		}
	if ( count($occurrences) < 1)
		$errors[] = 'Please select Event Date';

	// FOR TICKET ERRORS START //
	
if($bc_event_type==1 || $bc_event_type==2){	
	
	if ( trim($mainTitle) == '' )
		$errors[] = 'Please enter Ticket Type';
	if	( trim($mainPrice) == '' )
		$errors[] = 'Please enter Ticket Price';
	if ( trim($quantity_available) == '' )
		$errors[] = 'Please enter Quantity Available';
	if(	trim($bc_prometer_service_fee)=='' && trim($bc_buyer_service_fee)=='')
		$errors[] = 'Please enter Split Percent';
	if(($bc_prometer_service_fee+$bc_buyer_service_fee)!=100)
		$errors[] = 'Please enter Correct Percentage (Split Percent)';

}
	// FOR TICKET ERRORS END //

	if ( trim($bc_category_id) == '' )
		$errors[] = 'Please select Primary Category ';
	if ( trim($bc_subcategory_id) == '' )
		$errors[] = 'Please select Secondary Category';
	if ( trim($bc_event_age_suitab) == '' )
		$errors[] = 'Please select Age Requirements (Min Age Allowed)';
	if ( trim($_FILES["event_image"]["name"]) == '' && $_POST['mn_image']=='' )
		$errors[] = 'Please select Main Event Image';

		
	if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';
	}
	
	
	if (!count($errors)) {
	
		if($bc_occupation_target){
			$bc_occupation_targets	=	'';
				for($d=0;$d<count($bc_occupation_target);$d++){
				$bc_occupation_targets.=	$bc_occupation_target[$d].",";
				}
			}
	
		if($frmID){
			if ( $_SESSION['UPLOADED_TMP_NAME'] != '' ) {
				$bc_image  = $_SESSION['UPLOADED_TMP_NAME'];
				makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
				$sql_img = " event_image = '$bc_image' , ";
				$_SESSION['UPLOADED_TMP_NAME']='';
			}
		
		$bs	=	getSingleColumn('id',"select * from `events` where `event_name`='$bc_event_name' && `id`='$frmID'");
		if($bs){
			$bc_seo_name	=	'';
		}
		else{
			$bc_seo_name	=	"`seo_name` = '$bc_seo_name', ";
		}
	
	$sql = "UPDATE `events` SET `category_id` = '$bc_category_id', `subcategory_id` = '$bc_subcategory_id', `event_name` = '$bc_event_name',  $bc_seo_name `event_description` = '$bc_event_description', `event_cost` = '$bc_event_cost', $sql_img `event_age_suitab` = '$bc_event_age_suitab', `men_preferred_age` = '$bc_men_preferred_age', `women_preferred_age` = '$bc_women_preferred_age', `occupation_target` = '$bc_occupation_targets', `video_name` = '$bc_video_name', `video_embed` = '$bc_video_embed', `type` = '', `free_event`='$bc_free_event', `event_type`='$bc_event_type' WHERE `id` = '$frmID'";
 
 	$res = mysql_query($sql);
		
		if($res){
			$event_id	=	$frmID;
			$bc_event_ticket_id	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$frmID'");	
			$t_id = $bc_event_ticket_id;
			if($bc_event_ticket_id){
				mysql_query("DELETE FROM `event_ticket_price` WHERE `ticket_id` = '$t_id'");
			}
			
		$re = mysql_query("select * from `event_dates` where `event_id`='$event_id'");
		while($ro = mysql_fetch_array($re)){
			$date_id = $ro['id'];
			mysql_query("DELETE FROM `event_times` WHERE `date_id`='$date_id'");
			}
		
		mysql_query("DELETE from `event_dates` where `event_id`='$event_id'");
		mysql_query("DELETE from `event_music` where `event_id`='$event_id'");
		mysql_query("DELETE from `venue_events` where `event_id`='$event_id'");	
		mysql_query("DELETE from `event_videos` where `event_id`='$event_id'");
		
		mysql_query("UPDATE `event_gallery` SET `name` = '$bc_gallery' WHERE `event_id` = '$event_id'");
		
		/////// UPDATE DATE && TICKETS START ///////
		if($bc_event_type!=0){
			if($t_id){
				mysql_query("UPDATE `event_ticket` SET `quantity_available` = '$quantity_available', `start_sales_date` = '$start_sales_date', `start_sales_time` = '$start_sales_time', `end_sales_date` = '$end_sales_date', `end_sales_time` = '$end_sales_time', `ticket_description` = '$ticket_description', `buyer_event_grabber_fee` = '$bc_buyer_service_fee', `prometer_event_grabber_fee` = '$bc_prometer_service_fee' WHERE `id` = '$t_id'");
				$ticket_id = $t_id;
				}
				else{
					$ticket_sql = "INSERT INTO `event_ticket` (`id`, `quantity_available`, `start_sales_date`, `start_sales_time`, `end_sales_date`, `end_sales_time`, `ticket_description`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$quantity_available', '$start_sales_date', '$start_sales_time', '$end_sales_date', '$end_sales_time', '$ticket_description', '$bc_buyer_service_fee', '$bc_prometer_service_fee', '$event_id')";
					$res = mysql_query($ticket_sql);
					$ticket_id	=	mysql_insert_id();
				}
			}
	if($ticket_id){
	
		mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id')");
		
		
		if ( is_array($_POST['ticketTypesOp']) ) {
			$ticketTypesOp ='';
				for($i=0;$i< count($_POST['ticketTypesOp']); $i++) {
				
					if($_POST['ticketTypesOp'][$i] == 'Other')
						$title		= $_POST['ticketTypes'][$i];
					else
						$title		= $_POST['ticketTypesOp'][$i];
						$price				=	$_POST['ticketPrices'][$i];
		
					if($title!='' && $price!=''){
						mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$title', '$price', '$ticket_id')");
				} // END IF $title!='' && $price!=''
			} // END FOR
		} // END IF is_array($_POST['ticketTypesOp']
	} // END IF ($ticket_id)
		
		/////// UPDATE DATE && TICKETS START ///////

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
			makeThumbnail($bc_image, 'event_images/', '', 275, 375,'th_');
			$sql_img = " event_image = '$bc_image' , ";
		}
	
		$bc_source_id	=	"USER-".rand(); 
		$bc_publishdate	=	 date("Y-m-d");
		
		if($bc_event_type == 0){
			$bc_event_status = 1;
		}
		else{
			$bc_event_status = 0;
		}
		
		
		$sql	=	"insert into events (event_source,source_id,userid,category_id,subcategory_id,event_name,seo_name,musicgenere_id,event_description,event_cost,event_image,	event_sell_ticket,event_age_suitab,
		event_status,publishdate,averagerating,modify_date,del_status,added_by,men_preferred_age,women_preferred_age,occupation_target,repeat_event,repeat_freq,tags,privacy,pending_approval,type,free_event,event_type,is_expiring) values ('" .$bc_event_source . "','" .$bc_source_id . "','" . $bc_userid . "','" . $bc_category_id . "','" . $bc_subcategory_id . "','" . $bc_event_name . "','" . $bc_seo_name . "','" . $bc_musicgenere_id . "','" . $bc_event_description . "','" . $bc_event_cost . "','" . $bc_image . "','" . $bc_event_sell_ticket . "','" . $bc_event_age_suitab . "','" . $bc_event_status . "','" . $bc_publishdate . "','" . $bc_averagerating . "','" . $bc_modify_date . "','" . $bc_del_status . "','" . $bc_added_by . "','".$bc_men_preferred_age. "','".$bc_women_preferred_age. "','" . $bc_occupation_targets . "','".$repeat."','".$frequency."','','". $privacy ."','0','','$bc_free_event','$bc_event_type','1')";
		
		$res	=	mysql_query($sql);
		$frmID	=	mysql_insert_id();
		$event_id 	=	$frmID;
		
		if ($res) {
		
// FOR ADD TICKET START // 
	if($bc_event_type!=0){
		$ticket_sql = "INSERT INTO `event_ticket` (`id`, `quantity_available`, `start_sales_date`, `start_sales_time`, `end_sales_date`, `end_sales_time`, `ticket_description`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$quantity_available', '$start_sales_date', '$start_sales_time', '$end_sales_date', '$end_sales_time', '$ticket_description', '$bc_buyer_service_fee', '$bc_prometer_service_fee', '$event_id')";
	$res = mysql_query($ticket_sql);
	$ticket_id	=	mysql_insert_id();
		}
	if($ticket_id){
	mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id')");
		
				if ( is_array($_POST['ticketTypesOp']) ) {
		$ticketTypesOp ='';
			for($i=0;$i< count($_POST['ticketTypesOp']); $i++) {
				
				if($_POST['ticketTypesOp'][$i] == 'Other')
					$title		= $_POST['ticketTypes'][$i];
				else
					$title		= $_POST['ticketTypesOp'][$i];
		
				$price				=	$_POST['ticketPrices'][$i];
		
				if($title!='' && $price!=''){
				
				mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$title', '$price', '$ticket_id')");
				} // END IF $title!='' && $price!=''
			} // END FOR
		} // END IF is_array($_POST['ticketTypesOp']
	} // END IF ($ticket_id)
		
	
		
		
// FOR ADD TICKET END // 	
		
// FOR DATE & TIME //

	
	
// start main gallery & image upload ///
	if($bc_gallery){
	mysql_query("INSERT INTO `event_gallery` (`id`, `name`, `event_id`) VALUES (NULL, '$bc_gallery', '$event_id')");
	$gallery_id	=	mysql_insert_id();
	}
	if ( is_array($_FILES['images']) ) {
			for($i=0;$i< count($_FILES['images']['name']); $i++) {
				$einame = $_FILES['images']['name'][$i];
				$etname = $_FILES['images']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'event_images/gallery/'.$ei_image);
					makeThumbnail($ei_image, 'event_images/gallery/', '', 107, 92,'th_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $gallery_id > 0 )
					
					mysql_query("INSERT INTO `event_gallery_images` (`id`, `image`, `gallery_id`) VALUES (NULL, '$ei_image', '$gallery_id')");
				}		
			}
		}
		
		} else {
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
						mysql_query("INSERT INTO `event_videos` (`id`, `video_name`, `video_embed`, `event_id`) VALUES (NULL, '$video_name', '$video_embed', '$event_id')");
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
	$endTime = $end_time[$i];
	if($endTime!='' && $endTime!="00:00:00"){
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
	if($bc_event_type==1 || $bc_event_type==2){
	echo "<script>window.location.href='".ABSOLUTE_PATH_SECURE."create_flyer_step2.php?id=".$event_id."'</script>";	
		}
	else{
	echo "<script>window.location.href='".ABSOLUTE_PATH."saved.php?type=event&id=".$event_id."'</script>";
	}}
	
	} // end if $res
	
			
			
	}
	else{
	
	$sucMessage	=	$err;
	}

}
else{
$_SESSION['event_ticket_id']='';
}

if ($event_id != "" || isset($_POST["submit"])) {

    $dates_q = "select * from event_dates where event_id = '$event_id' ORDER BY event_date ASC";
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
}

if($first_date != ''){
	$yr = date("Y",strtotime($first_date));
	$mon = date("m",strtotime($first_date));
	$mon1 = $mon - 1;
	$dy = date("d",strtotime($first_date));
	$first_date = $yr.", ".$mon1.", ".$dy;
}




if($sucMessage == "Event Successfully updated"){

	$event_url		= getEventURL($event_id);
	echo "<script>window.location.href='".$event_url."'</script>";	
}

if ($frmID){
	$qry	=	"select * from `events` where `id`='$frmID'";
	$res = mysql_query($qry);
	while($row = mysql_fetch_array($res)){
	
		$bc_event_source 		=	$row["event_source"]; 
		$bc_source_id			=	$row["source_id"];
		$bc_fb_event_id			=	$row["fb_event_id"];
		$bc_userid				=	$row["userid"];
		
		if($bc_userid!=$user_id)
			echo "<script>window.location.href='index.php';</script>";
			
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
		$bc_event_cost			=	$row['event_cost'];
		$bc_type				=	$row['type'];
		$bc_event_type			=	$row['event_type'];
		$bc_free_event			=	$row['free_event'];
		
		$event_ticket_id	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$frmID'");
		
		if($event_ticket_id){
		$_SESSION['event_ticket_id'] = $event_ticket_id;
		}
		
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
		}}
		
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
	$bc_venu_id	=	$ro['venue_id'];
	}
	$sql = "select * from `venues` where `id`='$bc_venu_id'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_venue_address	=	$ro['venue_address'];
	$bc_venue_name		=	$ro['venue_name'];
	$bc_venue_city		=	$ro['venue_city'];
	$bc_venue_zip		=	$ro['venue_zip'];
	}
	
	$qry = "select * from `event_hosts` where `event_id`='$frmID'";
	$r = mysql_query($qry);
	while($ro = mysql_fetch_array($r)){
	$bc_event_host			=	$ro['host_name'];
	$bc_host_description	=	$ro['host_description'];
	}
	
	
	$special_qry = "select * from `special_event` where `event_id`='$frmID'";
	$special_res = mysql_query($special_qry);
	while($special_row = mysql_fetch_array($special_res)){
		$bc_specials = $special_row['specials_id'];
	}
	
		$event_id = $frmID;
		
	//$_SESSION['event_ticket_id_for_ticket']=$event_id;
//		$action = "edit";
//	
//	$dates_q = "select * from event_dates where event_id = '$frmID' ORDER BY event_date ASC";
//	$dates_res = mysql_query($dates_q);
//	$first_date = "";
//	$dates = "";
//	$i = 0;
//	while($dates_r = mysql_fetch_assoc($dates_res)){
//			if(mysql_num_rows($dates_res) > 0){
//				$date = date("m/d/Y",strtotime($dates_r['event_date']));
//				if($i<1){ $first_date = $date; $i++;}
//				$dates = $dates."'".$date."', ";
//			}else{
//				$date = $dates_r['event_date'];
//				$first_date = $date;
//				$dates = "'".date("m/d/Y",strtotime($date))."'";
//			}
//	}
//
//if($first_date != ''){
//	$yr = date("Y",strtotime($first_date));
//	$mon = date("m",strtotime($first_date));
//	$mon1 = $mon - 1;
//	$dy = date("d",strtotime($first_date));
//	$first_date = $yr.", ".$mon1.", ".$dy;
//}
//
	
	$resDate = mysql_query("select * from `event_dates` where `event_id`='$event_id' ORDER BY `event_date` ASC");
	
	}

}

$meta_title	= "Create Event";

include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<script>
function dynamic_Select(ajax_page, category_id,sub_category)      
	 {  
		 $.ajax({  
			type: "GET",  
			url: ajax_page,  
			data: "cat=" + category_id + "&subcat=" + sub_category + "&class=selectBig",  
			dataType: "text/html",  
			success: function(html){
			$("#subcategory_id").html(html);
			}
	   	});
	  }	
</script>
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

$(document).ready(function(){
$('#noTicket').click(function(){
if($(this).attr("checked")==true){
$('#showCostPrice').css('visibility','visible');
}
else{
$('#showCostPrice').css('visibility','hidden');
}
});

$('#addDate').css('cursor','pointer');

});

function remove(value){
	$('#row'+value).remove();
	}

function removeTicket(id){
	$('#trow'+id).remove();
	
	var onclAttr = $('#add_tc').html();
	
/*	var a = onclAttr.split('add_anOtherTicket(');
	var b = a[1].split(')');
	var c = b[0];
	var c =c-1;
	$('#add_tc').html('<img src="images/add_ticket.png" id="add_ticket" style="margin:10px 0 -20px 10px; cursor:pointer" onclick="add_anOtherTicket('+c+')" />'); */
	
	}

function add_date(id){
	var next_row 			= id+1;
	var event_date			= $('#event_date').val();
	var event_start_hrs		= $('#event_start_hrs').val();
	var event_start_min		= $('#event_start_min').val();
	var event_start_ampm	= $('#event_start_ampm').val();
	
	if(event_start_ampm=='AM')
		var event_start_amp=0;
	else
		var event_start_amp=1;
	
	var event_end_hrs		= $('#event_end_hrs').val();
	var event_end_min		= $('#event_end_min').val();
	var event_end_ampm		= $('#event_end_ampm').val();
	
	if(event_end_ampm=='AM')
		var event_end_amp=0;
	else
		var event_end_amp=1;
		
	if(event_date==''){
	alert('Please select Date');
	return false;
	}
	
	var new_url_feild = '<div style="background:#D1E5C0;border-bottom: 1px solid #45BB96;font-size: 12px;font-weight: bold;line-height: 26px;padding: 10px;" id="row'+next_row+'"><div class="ev_fltlft" style="width:32%">'+event_date+'<input type="hidden" name="occurrences['+next_row+'][date]" value="'+event_date+'"></div><div class="ev_fltlft" style="width:28%">'+event_start_hrs+':'+event_start_min+' '+event_start_ampm+'<input type="hidden" name="occurrences['+next_row+'][start_time]" value="'+event_start_hrs+':'+event_start_min+'"><input type="hidden" name="occurrences['+next_row+'][start_am_pm]" value="'+event_start_amp+'"></div><div class="ev_fltlft" style="width:21%">'+event_end_hrs+':'+event_end_min+' '+event_end_ampm+'<input type="hidden" name="occurrences['+next_row+'][end_time]" value="'+event_end_hrs+':'+event_end_min+'"><input type="hidden" name="occurrences['+next_row+'][end_am_pm]" value="'+event_end_amp+'"></div><div class="ev_fltlft" style="width:19%" align="center"><img src="images/closegreen.png" onclick="remove('+next_row+');" style="cursor:pointer" title="Remove this date"></div><div class="clr"></div></div>';
	
	$('#preview_date').append(new_url_feild);

	$('#addDateButton').html('<img src="images/add_date.png" id="addDate" onclick="add_date('+next_row+');" style="cursor:pointer" />');
	// checkValidDateTime();
	
//	" onclick="add_date(0);"
	  }


function add_more_image(id){
	
	if(id!=10){  
	var next_row 	= id+1;
	var new_url_feild = '<div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg'+next_row+'"><input type="file" name="images[]" /><img style="padding: 3px 53px 0 0;cursor:pointer" src="images/icon_delete2.gif" align="right" onclick="remove_image('+next_row+')"></div>';
	$('#add_more_image_area').append(new_url_feild);	
	$('#add_more_image_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_image('+next_row+')" />');
	}
	else{
	alert("You can not upload more then 10 images");
	}

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
	if(id!=4){  
	var next_row 	= id+1;
	var new_url_feild = '<div id="showvid'+next_row+'"><div style="float:left; width:380px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Video Name:</div><input type="text" name="video_name[]" value="Enter the name of your video" id="video_name'+next_row+'" onFocus="removeText(this.value,\'Enter the name of your video\',\'video_name'+next_row+'\');" onBlur="returnText(\'Enter the name of your video\',\'video_name'+next_row+'\');" class="new_input" style="width:350px;"><img style="padding: 3px 0 0 0;cursor:pointer" src="images/icon_delete2.gif" align="right" onclick="remove_video('+next_row+')"></div><div style="float:left; width:454px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Copy and Paste the Video Embed Code Here:</div><textarea class="new_input" name="video_embed[]" style="width:466px; height:130px;"></textarea></div><div class="clr"></div></div></div>';
	$('#add_more_video_area').append(new_url_feild);	
	$('#add_more_video_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_video('+next_row+')" />');
	}
	else{
	alert("You can not upload more then 4 videos");
	}

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


function updateEventTickets()      
	 {
		 $.ajax({  
			type: "GET",  
			url: "ajax/loadtickets.php",  
			data: "",  
			dataType: "text/html",  
			success: function(html){
			$("#showtickets").html(html);
			}
	   	});
	  }
	  
updateEventTickets();

$(document).ready(function(){
$('input[name=free_event]').click(function(){
if($(this).val()=='0'){
$('#showCostPrice').css('visibility','visible');
}
else{
$('#showCostPrice').css('visibility','hidden');
}
});
});

function advancedOptions(value){
if(value=='h'){
	$('#ticket_advance_options').hide();
	$('#advanceS').show();
	}
if(value=='s'){
	$('#ticket_advance_options').show();
	$('#advanceS').hide();
	
}
}


</script>
<style>
.ev_new_box_center{
	margin:auto;
	width:936px;
	}
.ev_new_box_center .basic_box, .ev_new_box_center .featured_box, .ev_new_box_center .premium_box, .ev_new_box_center .custom_box{
	width:234px;
	height:468px;
	float:left;
	position:absolute
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
	height:468px;
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
	padding:140px 10px 0;
	height:192px;
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	line-height:18px;
}
	

</style>
<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">
	
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	
	$(document).ready(function() {
		$(".fancybox2").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'width'			: 540,
			'height'		: 700,
			'type'			: 'iframe'
		});
	});
	
	
function updateTable(){
	
	
	var mainTitleOp = $('#mainTitleOp').val();
	
	if(mainTitleOp=='Other')
		var mainTitle = $('#mainTitle').val();		
	else
		var mainTitle = $('#mainTitleOp').val();
		
	var mainPrice = Number($('#mainPrice').val());
	var prometer_service_free = $('#prometer_service_free').val();
	var buyer_service_free = $('#buyer_service_free').val();
	var buyer_service_free_after_percent	=	mainPrice*5.50/100+.99;
	var buyer_service_free_after_percent	=	buyer_service_free_after_percent*buyer_service_free/100;
	var prometer_service_free_after_percent	=	mainPrice*5.50/100+.99;
	var prometer_service_free_after_percent	=	prometer_service_free_after_percent*prometer_service_free/100;
	prometer_service_free_after_percent = prometer_service_free_after_percent.toFixed(2);
	buyer_service_free_after_percent = buyer_service_free_after_percent.toFixed(2);
	mainPrice = mainPrice.toFixed(2);
	var totalPrice = Number(buyer_service_free_after_percent) + Number(mainPrice);
	totalPrice = totalPrice.toFixed(2);
	$('#t_type').html(mainTitle);
	if(mainPrice!='' && mainPrice!='0.00'){
		$('#t_price').html("$"+mainPrice);
		$('#t_pfees').html("$"+prometer_service_free_after_percent);
		$('#t_cfees').html("$"+buyer_service_free_after_percent);
		$('#t_finalPrice').html("$"+totalPrice);
	}else{
		$('#t_price').html('');
		$('#t_pfees').html('');
		$('#t_cfees').html('');
		$('#t_finalPrice').html('');
	}
	
	
	var ticketTypesOp = new Array();
	jQuery.each(jQuery("select[name='ticketTypesOp[]']"), function() {
		
		var a = jQuery(this).attr("id");
			var b = a.split('ticketTypesOp');
			var ids = b[1];	
		if(jQuery(this).val()!='Other'){
			$('#t_type'+ids).html(jQuery(this).val());			
		}
		else{
		$('#t_type'+ids).html($('#title'+ids).val());
		}
	});
	
	
	/*var ticketTypes = new Array();
	var i=0;
	jQuery.each(jQuery("input[name='ticketTypes[]']"), function() {
	i++;
	var a = jQuery(this).attr("id");
	var b = a.split('title');
	var ids = b[1];
		$('#t_type'+ids).html(jQuery(this).val());		
	});*/
	
	var ticketPrices = new Array();
	var i=0;
		jQuery.each(jQuery("input[name='ticketPrices[]']"), function() {
	i++;
	var a = jQuery(this).attr("id");
	var b = a.split('costum_price');
	var ids = b[1];
	var price = jQuery(this).val();
	if(price!=''){
		var buyer_service_free_after_percent	=	price*5.50/100+.99;
		var buyer_service_free_after_percent	=	buyer_service_free_after_percent*buyer_service_free/100;
		var prometer_service_free_after_percent	=	price*5.50/100+.99;
		var prometer_service_free_after_percent	=	prometer_service_free_after_percent*prometer_service_free/100;
		prometer_service_free_after_percent 	=	prometer_service_free_after_percent.toFixed(2);
		buyer_service_free_after_percent 		=	buyer_service_free_after_percent.toFixed(2);
		var totalPrice = Number(buyer_service_free_after_percent) + Number(price);
		
		$('#t_price'+ids).html("$"+price);
		$('#t_pfees'+ids).html("$"+prometer_service_free_after_percent);
		$('#t_cfees'+ids).html("$"+buyer_service_free_after_percent);
		$('#t_finalPrice'+ids).html("$"+totalPrice);
		
	}
	else{
		$('#t_price'+ids).html('');
		$('#t_pfees'+ids).html('');
		$('#t_cfees'+ids).html('');
		$('#t_finalPrice'+ids).html('');
	}
});
}
/* END Update Table	*/

	function spltfee(value){
	if(value=='b'){
		$('#prometer_service_free').val(0);
		$('#buyer_service_free').val(100);
		$('#buyer_service_free').attr('disabled','disabled');
		$('#prometer_service_free').attr('disabled','disabled');
	}
	else if(value=='a'){
		$('#prometer_service_free').val(100);
		$('#buyer_service_free').val(0);
		$('#buyer_service_free').attr('disabled','disabled');
		$('#prometer_service_free').attr('disabled','disabled');
	}
	else{
		$('#prometer_service_free').val(50);
		$('#buyer_service_free').val(50);
		$('#buyer_service_free').attr('disabled','');
		$('#prometer_service_free').attr('disabled','');
	}
		updateTable();
	}
/* END spltfee Table	*/

	
	
function splitFee(value,type){
	if(type=='p'){
		if(value>100){
			$('#prometer_service_free').val(100);
			$('#buyer_service_free').val(0);
		}
		else{
		var value = 100-value;
		//value = value.toFixed(2);
			$('#buyer_service_free').val(value);
		}
	}
	else{
		if(value>100){
			$('#buyer_service_free').val(100);
			$('#prometer_service_free').val(0);
		}
		else{
		var value = 100-value;
		//value = value.toFixed(2);
			$('#prometer_service_free').val(value);
		}
	}
	updateTable();
}


$(document).ready(function() {
	var dates = $("#start_sales_date").datepicker({
		dateFormat: "dd-M-yy",
		changeMonth: true,
		changeYear: true	
	});

	var dates = $("#end_sales_date").datepicker({
		dateFormat: "dd-M-yy",
		changeMonth: true,
		changeYear: true	
	});


	var dates = $("#event_date").datepicker({
		dateFormat: "dd M yy",
		changeMonth: true,
		changeYear: true	
	});
	
	updateTable();
	
});



function add_anOtherTicket(id){
	var next_row = id+1;
	var new_url_feild = '<div id="trow'+next_row+'"><div class="new_ticket_left"><div class="evField">Ticket Type: </div><div class="evLabal" style="width:400px"><select class="selectO" id="ticketTypesOp'+next_row+'" name="ticketTypesOp[]" onChange="showTh(this.value,\'title'+next_row+'\'),updateTable()"><option value="General Admission">General Admission</option><option value="VIP Admission">VIP Admission</option><option value="Other">Other</option></select><input type="text" style=" width:234px; display:none" onKeyUp="updateTable();" value="" class="new_input" name="ticketTypes[]" id="title'+next_row+'" /></div><div class="clr"></div><div class="evField">Price:</div><div class="evLabal" style="width:300px"><input type="text" class="new_input" name="ticketPrices[]" value="" id="costum_price'+next_row+'" onKeyUp="updateTable(),extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="width:60px;"></div><div class="ev_fltrght" style="padding:10px; cursor:pointer"><img src="images/ticket_remove.png" onClick="removeTicket('+next_row+');" /></div><div class="clr"></div><div class="advanced_options" onClick="advancedOptions(\'s\');" id="advanceS" style="display:none;">Advanced Options <img src="images/arrow_dn.png" /></div><div class="clr"></div></div><div class="new_ticket_right"><table width="100%" border="0" cellspacing="0" cellpadding="10"><tr bgcolor="#e4f0d8"><td><strong>Ticket Type</strong></td><td align="right"><span id="t_type'+next_row+'"></span></td></tr><tr bgcolor="#d1e5c0"><td>Initial Price</td><td align="right"><span id="t_price'+next_row+'"></span></td></tr><tr bgcolor="#e4f0d8"><td>Promoter Fees</td><td align="right"><span id="t_pfees'+next_row+'"></span></td></tr><tr bgcolor="d1e5c0"><td>Customer Fees</td><td align="right"><span id="t_cfees'+next_row+'"></span></td></tr><tr bgcolor="e4f0d8"><td><strong>Final Price</strong></td><td align="right"><span class="t_finalPrice" id="t_finalPrice'+next_row+'"></span></td></tr></table></div></div>';
	$('#add_tckt').append(new_url_feild);
	$('#add_tc').html('<img src="images/add_ticket.png" id="add_ticket" style="margin:10px 0 -20px 10px; cursor:pointer" onclick="add_anOtherTicket('+next_row+')" />');
	
}


function checkValidDateTime(){

var event_start_dates = new Array();
var event_start_times = new Array();
var event_end_times = new Array();

jQuery.each(jQuery("input[name='event_start_date[]']"), function() {
    event_start_dates.push(jQuery(this).val());
});

jQuery.each(jQuery("input[name='event_start_time[]']"), function() {
    event_start_times.push(jQuery(this).val());
});

jQuery.each(jQuery("input[name='event_end_time[]']"), function() {
    event_end_times.push(jQuery(this).val());
});

/*$.ajax({  
		type: "POST",  
		url: "ajax/checkValidDateTime.php",  
		data: "event_start_dates=" + event_start_dates + "&event_start_times=" + event_start_times + "&event_end_times=" + event_end_times,
		dataType: "text/html",  
		success: function(html){
		$("#subcategory_id").html(html);
		}
	   	});
*/

}


function showTh(value,id){
	if(value == 'Other')
		$('#'+id).show();
	else
		$('#'+id).hide();
	}
</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Create Event</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
      <?php
		 if($bc_event_type!='0' && $bc_event_type!='1' && $bc_event_type!='2'){
		 ?>
      <div class="ev_new_box_center">
        <div style="position:relative; height:520px">
          <div class="basic_box">
            <div class="black">&nbsp;</div>
            <div class="detail">Add a basic listing of your event to our database for free.</div>
            <div align="center"><a href="javascript:voild(0)" onClick="window.location.href='create_event.php?type=simple';"><img src="images/ev_new_create_event.png" /></a></div>
          </div>
          <!-- end basic_box -->
          <div class="featured_box">
            <div class="black">&nbsp;</div>
            <div class="detail">Utilize our proprietary Showcase Creator<strong>&reg;</strong> to create an embedded event flyer that can be  integrated into your Facebook Page, Twitter Page, and Featured on  Eventgrabber.&nbsp; Featured Campaigns  includes the ability to sell and manage tickets and special offers to your  target audience.</div>
            <div align="center"><a  href="javascript:voild(0)" onClick="alert('This feature is coming soon');"><img src="images/ev_new_create_event.png" /></a></div>
          </div>
          <!-- end featured_box -->
          <div class="premium_box">
            <div class="black">&nbsp;</div>
            <div class="detail">Utilize our proprietary Showcase Creator<strong>&reg;</strong> to create an embedded event flyer that can be  integrated into your Website, Facebook Page, Twitter Page, and listed as a  Premium event on Eventgrabber.&nbsp; Premium  Showcases includes the ability to sell and manage tickets and special offers to  your target audience.</div>
            <div align="center">
			
			<a href="javascript:voild(0)"  onclick="alert('This feature is coming soon');" ><img src="images/ev_new_create_event.png" /></a>
			
			<!--<a href="javascript:voild(0)"  onclick="window.location.href='create_event.php?type=premium';" ><img src="images/ev_new_create_event.png" /></a>-->
			</div>
          </div>
          <!-- end premium_box -->
          <div class="custom_box">
            <div class="black">&nbsp;</div>
            <div class="detail">Custom Campaigns are tailored made to fit your  specific need and includes White Label.&nbsp;  Contact us for more details on this product.</div>
            <div align="center"><a  href="javascript:voild(0)" onClick="alert('This feature is coming soon');"><img src="images/ev_new_create_event.png" /></a></div>
          </div>
          <!-- end custom_box -->
        </div>
        <!-- end position:relative -->
      </div>
      <?php } 
	else{ ?>
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
        <input type="hidden" name="id" id="queued_event_id" value="1120455984" />
        <?php
		if($bc_event_type=='0' || $bc_event_type=='1' || $bc_event_type=='2'){
		 if($_GET['id']){
		 if($bc_type=='draft'){?>
        <div align="right" style="padding:5px 5px 9px 0"><img src="<?php echo IMAGE_PATH; ?>save_as_draft_new.png" alt="" value="Save As Draft" title="Save As Draft" onClick="draft();" style="cursor:pointer" align="right"></div>
        <?php }}else{
		 ?>
        <div align="right" style="padding:5px 5px 9px 0"><img src="<?php echo IMAGE_PATH; ?>save_as_draft_new.png" alt="" value="Save As Draft" title="Save As Draft" onClick="draft();" style="cursor:pointer" align="right"></div>
        <?php } } ?>
        <div class="clr"></div>
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">
                <h3>STEP 1 | <span>ADD EVENT INFORMATION</span></h3>
                <div id="box" class="box">
                  <div id="head">Event Title</div>
                  <div class="ev_title">
                    <input type="text" name="eventname" value="<?php if ($bc_event_name){echo $bc_event_name;} else{ echo "Enter only the name of your event";} ?>" id="event" onFocus="removeText(this.value,'Enter only the name of your event','event');" onBlur="returnText('Enter only the name of your event','event');">
                  </div>
                  <div id="head">Event Details</div>
                  <div>
                    <textarea name="event_description" id="event_description" class="bc_input" style="width:825px; height:250px"><?php echo $bc_event_description; ?></textarea>
                  </div>
                  <div id="head">Location</div>
                  <input type="text" name="venue_name" id="venue_name" class="new_input" value="<?php if ($bc_venue_name){ echo $bc_venue_name; }else{ echo "Start Typing Location Name"; } ?>" onFocus="removeText(this.value,'Start Typing Location Name','venue_name');" onBlur="returnText('Start Typing Location Name','venue_name');" style="margin-bottom:2px; width:200px" />
                  <input type="hidden" name="venue_id" id="venue_id" value="<?php echo $bc_venu_id; ?>" />
                  &nbsp;&nbsp;
                  <input type="text" name="address1" disabled="disabled" id="ev_address1" class="new_input" value="<?php if ($bc_venue_address){ echo $bc_venue_address; } else{ echo 'Address'; } ?>"  onFocus="removeText(this.value,'Address','ev_address1');" onBlur="returnText('Address','ev_address1');" style="width:200px">
                  &nbsp;&nbsp;
                  <input type="text" name="city" disabled="disabled" id="ev_city" class="new_input" value="<?php if ($bc_venue_city){ echo $bc_venue_city; } else{ echo 'City'; } ?>"  onFocus="removeText(this.value,'City','ev_city');" onBlur="returnText('City','ev_city');" style="width:200px">
                  &nbsp;&nbsp;
                  <input type="text" name="zip" id="ev_zip" disabled="disabled" class="new_input" value="<?php if ($bc_venue_zip){ echo $bc_venue_zip; } else{ echo 'Zip / Postal Code'; } ?>"  onFocus="removeText(this.value,'Zip / Postal Code','ev_zip');" onBlur="returnText('Zip / Postal Code','ev_zip');" style="width:190px">
                  <br>
                  <a href="javascript:void(0)" style="color:#0066FF; text-decoration:underline" onClick="windowOpener(525,645,'Add New Location','add_venue.php')"> Can't find your location? Add it here </a>
                  <div class="clr"></div>
                  <?php if ($bc_event_type==0){ ?>
                  <div id="head">Free Event</div>
                  <div style="font-size:14px;">
                    <label>
                    <input type="radio" name="free_event" value="1" <?php if ($bc_free_event=='1'){ echo 'checked="checked"';}?> />
                    Yes</label>
                    &nbsp; &nbsp; &nbsp;
                    <label>
                    <input type="radio" name="free_event" id="not_free_event" value="0" <?php if ($bc_free_event!='1'){ echo 'checked="checked"';}?> />
                    No</label>
                    <span id="showCostPrice" style=" <?php if ($bc_free_event=='1'){ echo 'visibility:hidden';} ?>"><strong>Event Cost:</strong>
                    <input type="text" class="new_input"  style="width:100px; font-weight:bold" value="<?php echo $bc_event_cost; ?>" name="event_cost" />
                    </span> </div>
                  <?php
		  }
		  ?>
		 
		  <div id="z_listing_event_form_occurrences" class="z-group z-panel-occurrences">
            <div class="ev_fltlft">
              <div id="head">Select Event Date(s)</div>
              <a name="z_repeat_pattern_list"></a>
            <ul class="z-tabs" style="display:">
            <li class="z-current"><a href="#">Calendar View</a></li>
            <li><a href="#">Advanced View</a></li>
          </ul>
              <div id="z_tab_calender_view" class="z-calendar-view z-tab-content" style="display: block">
                <label><sup>&#42;</sup> Click one or more dates for your event or event series on the calendars below.</label>
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
                  <input class="z-input-date" id="z_end_date_advanced" readonly="" type="text" value="1/1/2009"  /> </div>
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
                    <tr>
                      <td><input type="radio" id="z_monthly_day" class="z-monthly-repeat-type" name="monthly_repeat_type" value="day" />
                      </td>
                      <td> On Day
                        <input type="text" class="z-date" id="z_monthly_day_of_month" name="Within" />
                        of every month </td>
                    </tr>
                    <tr>
                      <td><input type="radio" id="z_monthly_pattern" class="z-monthly-repeat-type" name="monthly_repeat_type" value="pattern" />
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
		<div class="ev_fltrght" style="width:272px">
              <div id="head"> Select Event Time(s)
                <div id="info"></div>
              </div>
              <div style="padding:21px 0 0 0">
                <label for="z_event_start_time" class="z-inline"><sup>*</sup> Start Time</label>
                <input id="z_event_start_time" class="z-input-time" type="text" value="7:00" name="start_time"/>
                <select id="z_event_start_am_pm" name="start_time_am_or_pm">
                  <option value="0">AM</option>
                  <option selected="selected" value="1">PM</option>
                </select>
                <div class="clr" style="height:10px">&nbsp;</div>
                <label for="z_event_end_time" class="z-inline">End Time (optional)</label>
                <input id="z_event_end_time" class="z-input-time" type="text" name="end_time"/>
                <select id="z_event_end_am_pm" name="end_time_am_or_pm">
                  <option value="0">AM</option>
                  <option selected="selected" value="1">PM</option>
                </select>
              </div>
            </div>
			<div class="clr"></div>
           <div id="head">Dates Selected
                    <div id="info33" class="info" title="Select date and time for your event above and click 'Add Date'. If your event span more than one date, you can keep on selecting different dates and times for your event."></div>
                  </div>
            
            <a name="z_a_repeat_pattern_list"></a>
			<div id="z_review_errors"></div>
			<input type="hidden" id="check_errors" value=""  />
			<input type="hidden" id="dErrors" value="" />
            
            
            <div class="z-simple-box">
              <table width="100%" cellspacing="0" class="z-event-occurrences-heading">
                <tbody>
                  <tr>
                    <th width="26%" class="z-col-1">Event Date</th>
                    <th width="35%" class="z-col-3">Start Time</th>
                    <th width="10%" class="z-col-4">Remove</th>
                  </tr>
                </tbody>
              </table>
              <div class="z-block-event-occurrences">
                <table class="z-event-occurrences" cellspacing="0">
                  <tbody>
				  <?php
				  if($frmID){
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
                      <td class="z-occurrence-date-cell z-col-1" style="background-color: #d1e5c0;"><input type="hidden" class="z-occurrence-id" name="occurrences[<?php echo $unique_id ?>][occurrence_id]" value="" />
                <input type="hidden" class="z-occurrence-date" name="occurrences[<?php echo $unique_id ?>][date]" value="<?php echo date('m/d/Y', strtotime($row['event_date'])); ?>" />
                        <?php echo date('D, d M, Y', strtotime($row['event_date'])); ?> </td>
                      
                      <td class="z-time-cell z-col-3" style="background-color: #d1e5c0;"><div class="z-occurrence-start-time-layer" >
                          <input type="text" class="z-occurrence-start-time z-input-time" name="occurrences[<?php echo $unique_id ?>][start_time]" value="<?php echo date('h:i',strtotime($start_time)); ?>"  />
                          <select class="z-occurrence-start-am-pm" name="occurrences[<?php echo $unique_id ?>][start_am_pm]" >
                            <option value="0" <?php if (date('A',strtotime($start_time))=='AM'){ echo "selected='selected'";}; ?>>AM</option>
                            <option value="1" <?php if (date('A',strtotime($start_time))=='PM'){ echo "selected='selected'";}; ?>>PM</option>
                          </select>
                        </div>
                        <div class="z-occurrence-end-time-layer" <?php if ($end_time=='00:00:00'){ echo 'style="display:none"'; } ?>>
						
<input type="text" class="z-occurrence-end-time z-input-time" name="occurrences[<?php echo $unique_id ?>][end_time]" value="<?php
if ($end_time!='00:00:00'){ echo date('h:i',strtotime($end_time)); }?>"  />
                          <select class="z-occurrence-end-am-pm" name="occurrences[<?php echo $unique_id ?>][end_am_pm]"  >
                            <option value="0" <?php if (date('A',strtotime($end_time))=='AM'){ echo "selected='selected'";}; ?>>AM</option>
                            <option value="1" <?php if (date('A',strtotime($end_time))=='PM'){ echo "selected='selected'";}; ?>>PM</option>
                          </select>
                        </div>
                        <a class="z-end-time-toggle">[+] end time</a> </td>
                      <td class="z-remove-cell z-col-4" style="background-color: #d1e5c0;"><a class="z-occurrence-remove"><img src="<?php echo IMAGE_PATH; ?>icon_remove.gif" alt="remove" title="remove"></a> </td>
                    </tr>
                    <?php
}
}
?>
		</tbody>
                </table>
              </div>
              <div class="z-table-bottom">
                <div id="z_total_occurrences_block"> Total Occurrences: <span id="z_total_occurrences"><?php if ($totalOccurrences){ echo $totalOccurrences; } else{ echo 0;} ?></span> </div>
                <div id="z_clear_occurrences_block"> <a href="#" id="z_clear_occurrences">Clear Occurrences</a> </div>
                <div class="z-clear"></div>
              </div>
            </div>
            <div class="z-bottom"></div>
            <script type="text/plain" id="occurrence_template">
<tr id="z_occurrence_row_<@=unique_id@>">
  <td class="z-occurrence-date-cell z-col-1">
    <input type="hidden" class="z-occurrence-id" name="occurrences[<@=unique_id@>][occurrence_id]" value="<@=occurrence_id@>" />
    <input type="hidden" class="z-occurrence-date" name="occurrences[<@=unique_id@>][date]" value="<@=date@>" />
    <@=display_date@>
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
    <a class="z-occurrence-remove"><img src="images/icon_remove.gif" alt="remove" title="remove"></a>
  </td>
</tr>
</script>
          </div>
                  
                </div>
                <?php if ($bc_event_type!='0'){?>
                <h3>STEP 2 | <span>CREATE TICKETS</span></h3>
                <div id="box" class="box" style="padding:30px 0">
                  <div class="new_ticket_left">
                    <?php
					
				  $resTicketDetails = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
				  while($rowTicketDetails = mysql_fetch_array($resTicketDetails)){
				  $quantity_available	=	$rowTicketDetails['quantity_available'];
				  $start_sales_date		=	$rowTicketDetails['start_sales_date'];
				  $end_sales_date		=	$rowTicketDetails['end_sales_date'];
				  $start_sales_time		=	$rowTicketDetails['start_sales_time'];
				  $end_sales_time		=	$rowTicketDetails['end_sales_time'];
				  $ticket_description	=	$rowTicketDetails['ticket_description'];
				  $mainTicketId			=	$rowTicketDetails['id'];
				  $buyer_ser_fee		=	$rowTicketDetails['buyer_event_grabber_fee'];
				  $prometer_ser_fee		=	$rowTicketDetails['prometer_event_grabber_fee'];
				  }
				  
				   
				  $resTicket = mysql_query("select * from `event_ticket_price` where `ticket_id`='$mainTicketId' ORDER BY ID ASC LIMIT 0,1");
				  while($rowTicket = mysql_fetch_array($resTicket)){
				  $mainTitle	= $rowTicket['title'];
				  $mainPrice	= $rowTicket['price'];
				  }
				
				   ?>
                    <div class="evField">Ticket Type:<b class="clr">*</b> </div>
                    <div class="evLabal" style="width:400px">
                      <select class="selectO" id="mainTitleOp" name="mainTitleOp" onChange="showTh(this.value,'mainTitle'),updateTable()">
					  <option value="General Admission" <?php if ($mainTitle=='General Admission'){ echo "selected='selected'"; } ?>>General Admission</option>
					  <option value="VIP Admission" <?php if ($mainTitle=='VIP Admission'){ echo "selected='selected'"; } ?>>VIP Admission</option>
					  <option value="Other" <?php if ($mainTitle!='VIP Admission' && $mainTitle!='General Admission' && $mainTitle!=''){ echo "selected='selected'"; } ?>>Other</option>
					  </select>
					  <input type="text" style="width:234px;<?php if ($mainTitle=='VIP Admission' || $mainTitle=='General Admission' || $mainTitle==''){ echo 'display:none'; } ?>" onKeyUp="updateTable();" value="<?php if ($_POST['create']){echo $_POST['mainTitle'];}else{ if ($mainTitle!='VIP Admission' && $mainTitle!='General Admission'){echo $mainTitle;} }?>" class="new_input" name="mainTitle" id="mainTitle" />
                    </div>
                    <div class="clr"></div>
                    <div class="evField">Price:<b class="clr">*</b></div>
                    <div class="evLabal" style="width:300px">
                      <input type="text" class="new_input" name="mainPrice" value="<?php if ($_POST['create']){echo $_POST['mainPrice'];}else{ echo $mainPrice; }?>" id="mainPrice" onKeyUp="updateTable(),extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="width:60px;">
                    </div>
                    <div class="clr"></div>
                    <div class="evField">Quantity Available:<b class="clr">*</b></div>
                    <div class="evLabal" style="width:300px">
                      <input type="text" class="new_input" name="quantity_available" onKeyPress="return isNumberKey(event)" value="<?php echo $quantity_available; ?>" style="width:60px;" onKeyUp="updateTable()">
                    </div>
                    <div class="clr"></div>
                    <div class="advanced_options" onClick="advancedOptions('s');" id="advanceS" style="display:none;">Advanced Options <img src="images/arrow_dn.png" /></div>
                    <div class="clr"></div>
                  </div>
                  <div class="new_ticket_right">
                    <table width="100%" border="0" cellspacing="0" cellpadding="10">
                      <tr bgcolor="#e4f0d8">
                        <td><strong>Ticket Type</strong></td>
                        <td align="right"><span id="t_type"></span></td>
                      </tr>
                      <tr bgcolor="#d1e5c0">
                        <td>Initial Price</td>
                        <td align="right"><span id="t_price"></span></td>
                      </tr>
                      <tr bgcolor="#e4f0d8">
                        <td>Promoter Fees</td>
                        <td align="right"><span id="t_pfees"></span></td>
                      </tr>
                      <tr bgcolor="d1e5c0">
                        <td>Customer Fees</td>
                        <td align="right"><span id="t_cfees"></span></td>
                      </tr>
                      <tr bgcolor="e4f0d8">
                        <td><strong>Final Price</strong></td>
                        <td align="right"><span id="t_finalPrice" class="t_finalPrice"></span></td>
                      </tr>
                    </table>
                  </div>
                  <div class="clr"></div>
                  <span id="add_tckt">
                  <?php
				  $resTicket = mysql_query("select * from `event_ticket_price` where `ticket_id`='$mainTicketId'");
				  $i=0;
				  $z=0;
				  $ticketNumRows = mysql_num_rows($resTicket);
				  while($rowTicket = mysql_fetch_array($resTicket)){
				  $i++;
				  if($i!=1){
				  $z++;
				  
				  $ticketType	= $rowTicket['title'];
				  $ticketPrice	= $rowTicket['price'];
				  
				  ?>
                  <div id="trow<?php echo $z; ?>">
                    <div class="new_ticket_left">
                      <div class="evField">Ticket Type: </div>
					  
                      <div class="evLabal" style="width:400px">
                      <select class="selectO" id="ticketTypesOp<?php echo $z; ?>" name="ticketTypesOp[]"  onChange="showTh(this.value,'title<?php echo $z; ?>'),updateTable()">
					  <option value="General Admission" <?php if ($ticketType=='General Admission'){ echo "selected='selected'"; } ?>>General Admission</option>
					  <option value="VIP Admission" <?php if ($ticketType=='VIP Admission'){ echo "selected='selected'"; } ?>>VIP Admission</option>
					  <option value="Other" <?php if ($ticketType!='VIP Admission' && $ticketType!='General Admission' && $ticketType!=''){ echo "selected='selected'"; } ?>>Other</option>
					  </select>
					  <input type="text" style="width:234px;<?php if ($ticketType=='VIP Admission' || $ticketType=='General Admission' || $ticketType==''){ echo 'display:none'; } ?>" onKeyUp="updateTable();" value="<?php if ($ticketType!='VIP Admission' && $ticketType!='General Admission'){ echo $ticketType; }?>" class="new_input" name="ticketTypes[]" id="title<?php echo $z; ?>" />
                    </div>
					  
					  
					 <!-- <div class="evLabal" style="width:300px">
                        <input type="text" style=" width:280px" onkeyup="updateTable();" value="<?php echo $ticketType; ?>" class="new_input" name="ticketTypes[]" id="title<?php echo $z; ?>" />
                      </div>-->
					  
					  
					  
					  
                      
                      <div class="clr"></div>
                      <div class="evField">Price:</div>
                      <div class="evLabal" style="width:300px">
                        <input type="text" class="new_input" name="ticketPrices[]" value="<?php echo $ticketPrice; ?>" id="costum_price<?php echo $z; ?>" onKeyUp="updateTable(),extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="width:60px;">
                      </div>
					  <div class="ev_fltrght" style="padding:10px; cursor:pointer"><img src="<?php echo IMAGE_PATH; ?>ticket_remove.png" onClick="removeTicket(<?php echo $z; ?>);" /></div>
                      <div class="clr"></div>
                      <div class="advanced_options" onClick="advancedOptions(\'s\');" id="advanceS" style="display:none;">Advanced Options <img src="images/arrow_dn.png" /></div>
                      <div class="clr"></div>
                    </div>
                    <div class="new_ticket_right">
                      <table width="100%" border="0" cellspacing="0" cellpadding="10">
                        <tr bgcolor="#e4f0d8">
                          <td><strong>Ticket Type</strong></td>
                          <td align="right"><span id="t_type<?php echo $z; ?>"></span></td>
                        </tr>
                        <tr bgcolor="#d1e5c0">
                          <td>Initial Price</td>
                          <td align="right"><span id="t_price<?php echo $z; ?>"></span></td>
                        </tr>
                        <tr bgcolor="#e4f0d8">
                          <td>Promoter Fees</td>
                          <td align="right"><span id="t_pfees<?php echo $z; ?>"></span></td>
                        </tr>
                        <tr bgcolor="d1e5c0">
                          <td>Customer Fees</td>
                          <td align="right"><span id="t_cfees<?php echo $z; ?>"></span></td>
                        </tr>
                        <tr bgcolor="e4f0d8">
                          <td><strong>Final Price</strong></td>
                          <td align="right"><span class="t_finalPrice" id="t_finalPrice<?php echo $z; ?>"></span></td>
                        </tr>
                      </table>

                    </div>
                  </div>
                  <?php
				  
				  
				  }}
				  ?>
                  </span>
                  <div class="ticket_advance_options" id="ticket_advance_options">
                    <div class="ev_fltlft" style="width:65%">
                      <div class="advanced_options" onClick="advancedOptions('h');">Advanced Options <img src="<?php echo IMAGE_PATH; ?>arrow_up.png" /></div>
                      <div class="evField">Start Sale: </div>
                      <div class="evLabal" style="width:300px">
                        <input type="text" class="new_input" name="start_sales_date"
		value="<?php if ($start_sales_date!='0000-00-00' && $start_sales_date!=''){ echo date('d-M-Y', strtotime($start_sales_date)); } ?>" readonly="" id="start_sales_date" style="width:102px; color:#000000; cursor:pointer">
                        <select class="inp3" name="start_sales_hrs" id="start_sales_hrs">
                          <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'"';
		   if($start_sales_time && date("h",strtotime($start_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>';
		   }
		   ?>
                        </select>
                        :
                        <select class="inp3" name="start_sales_min" id="start_sales_min">
                          <?php
		   for ($i=00;$i<=59;$i++){
		   echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'"';
		   if($start_sales_time && date("i",strtotime($start_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>';
		   }
		  
		   ?>
                        </select>
                        <select class="inp3" name="start_sales_ampm" id="start_sales_ampm">
                          <option value="am" <?php if(date("a",strtotime($start_sales_time)) == 'am'){ echo 'selected="selected"'; } ?>  >AM</option>
                          <option value="pm" <?php if(date("a",strtotime($start_sales_time)) == 'pm'){ echo 'selected="selected"'; } ?> >PM</option>
                        </select>
                      </div>
                      <div class="clr"></div>
                      <div class="evField">End Sale:</div>
                      <div class="evLabal" style="width:300px">
                        <input type="text" class="new_input"  value="<?php if ($end_sales_date!='0000-00-00' && $end_sales_date!=''){ echo date('d-M-Y', strtotime($end_sales_date)); } ?>" readonly="" name="end_sales_date" id="end_sales_date" style="width:102px; cursor:pointer">
                        <select class="inp3" name="end_sales_hrs" id="end_sales_hrs">
                          <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'"';
		   if($end_sales_time && date("h",strtotime($end_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>';
		   }
		   ?>
                        </select>
                        :
                        <select class="inp3" name="end_sales_min" id="end_sales_min">
                          <?php
		   for ($i=00;$i<=59;$i++){
		   echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'"';
		   if($end_sales_time && date("i",strtotime($end_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>';
		   }
		   ?>
                        </select>
                        <select class="inp3" name="end_sales_ampm" id="end_sales_ampm">
                          <option value="am" <?php if ($end_sales_time){ if(date("a",strtotime($end_sales_time)) == 'am'){ echo 'selected="selected"'; }} ?> >AM</option>
                          <option value="pm" <?php if ($end_sales_time){ if(date("a",strtotime($end_sales_time)) == 'pm'){ echo 'selected="selected"'; }} ?> >PM</option>
                        </select>
                      </div>
                      <div class="clr"></div>
                      <div class="evField">Ticket Description:</div>
                      <div class="evLabal" style="width:300px;">
                        <textarea id="ticket_description" name="ticket_description" class="ticket_description"><?php if ($_POST['submit']){ echo $_POST['ticket_description']; } else{  echo $ticket_description; }?>
</textarea>
                      </div>
                      <div class="clr"></div>
                    </div>
                    <div class="split_fee">
                      <input type="radio" name="split_fee" value="prometer" onChange="spltfee('b');" <?php if($prometer_ser_fee==0 && $buyer_ser_fee!=0){ echo 'checked="checked"'; } ?>  />
                      Pass on fees to buyer<br />
                      <br />
                      <input type="radio" name="split_fee" value="buyer" onChange="spltfee('a');" <?php if($buyer_ser_fee==0 && $prometer_ser_fee!=0){ echo 'checked="checked"'; } ?> />
                      Absorb the fees<br />
                      <br />
                      <input type="radio" name="split_fee" value="split" onChange="spltfee('s');" <?php if($event_id){if($buyer_ser_fee!=0 && $prometer_ser_fee!=0){ echo 'checked="checked"'; }} else{ echo 'checked="checked"'; } ?> />
                      Split the fees
                      <div class="clr"></div>
                      <br />
                      <br />
                      <div class="ev_fltlft" style="text-align:right"> Promoter Pays:
                        <input type="text" class="new_input" style="width:56px;" <?php if($event_id){if($buyer_ser_fee==0 || $prometer_ser_fee==0){ echo 'disabled="disabled"'; }}?> name="prometer_service_free" id="prometer_service_free" 
onkeyup="updateTable('ajax/load_ticket_price_table.php');" onKeyPress="return isNumberKey(event)"  onblur="splitFee(this.value,'p'),updateTable();" value="<?php if($prometer_ser_fee || $prometer_ser_fee=='0'){ echo $prometer_ser_fee; }else{ echo 50; } ?>" />
                        <br />
                        <br />
                        <br />
                        Customer Pays:
                        <input type="text" name="buyer_service_free" onKeyPress="return isNumberKey(event)" <?php if($event_id){if($buyer_ser_fee==0 || $prometer_ser_fee==0){ echo 'disabled="disabled"'; }} ?> id="buyer_service_free" value="<?php if($buyer_ser_fee || $buyer_ser_fee=='0'){ echo $buyer_ser_fee; }else{ echo 50; } ?>" onKeyUp="updateTable();"  onblur="splitFee(this.value,'c'),updateTable();" class="new_input" style="width:56px;" />
                      </div>
                      <div class="ev_fltlft" style="line-height:92px;"> <img src="<?php echo IMAGE_PATH; ?>percent.png" align="left" style="padding:10px 4px" /> <strong>Split Fee</strong> </div>
                      <div class="clr"></div>
                    </div>
                    <div class="clr"></div>
                  </div>
                  <span id="add_tc"><img src="<?php echo IMAGE_PATH; ?>add_ticket.png" id="add_ticket" style="margin:10px 0 -20px 10px; cursor:pointer" onClick="add_anOtherTicket(<?php if($ticketNumRows){ echo $ticketNumRows-1;} else{ echo 0; }?>),updateTable()" /></span> </div>
                <?php } ?>
                <h3>STEP
                  <?php if ($bc_event_type=='0'){ echo "2";} else{ echo "3";} ?>
                  | <span>ADD EVENT ATTRIBUTES</span></h3>
                <div id="box" class="box">
                  <div  class="ev_fltlft" style="width:33%">
                    <div id="head" >Primary Category</div>
                    <select name="category_id" id="category_id" class="selectBig" <?php if ($privacy=='Private'){ echo 'disabled="disabled"'; }?> onChange="dynamic_Select('admin/subcategory.php', this.value, 0 );">
                      <option value="">-- Select Primary Category --</option>
                      <?php
			$res = mysql_query("select * from `categories` ORDER BY `name` ASC");
			while($row = mysql_fetch_array($res)){?>
                      <option value="<?php echo $row['id']; ?>" <?php if ($bc_category_id==$row['id']){ echo 'selected="selected"';} ?>><?php echo $row['name']; ?></option>
                      <?php
			}
			?>
                    </select>
                  </div>
                  <div  class="ev_fltlft" style="width:33%">
                    <div id="head" >Secondary Category</div>
                    <span id="subcategory_id">
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
                    <select name="subcategory_id" class="selectBig">
                      <option value="">-- Select Secondary Category --</option>
                    </select>
                    <?php } ?>
                    </span> </div>
                  <div  class="ev_fltlft" style="width:33%">
                    <div id="head" >Annual Event</div>
                    <select name="specials" id="specials" class="selectBig">
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
                  </div>
                  <div class="clr" style="height:38px">&nbsp;</div>
                  <div class="stpBox">
                    <div class="title">Age Requirements</div>
                    <div class="data"><b>Minimum Age Allowed:</b>
                      <div id="info1" class="info" title="The no kidding minimum age allowed into your event"></div>
                      <div class="age">
                        <?php
						if($_POST['min_age_allow']){
							$bc_event_age_suitab = $_POST['min_age_allow'];
						}
						 $sqlAge = "SELECT name,id FROM age";
						$resAge = mysql_query($sqlAge);
						$totalAge= mysql_num_rows($resAge);
						while($rowAge = mysql_fetch_array($resAge))
						{	
						?>
                        <div style="float:left; width:50%;padding: 3px 0;"> &nbsp;
                          <input name="min_age_allow" class="unique" type="checkbox" value="<?php echo $rowAge['id']; ?>" <?php if($rowAge['id']==$bc_event_age_suitab)
							{ echo 'checked="checked"'; }?>>
                          <?php echo $rowAge['name']; ?> </div>
                        <?php } ?>
                        <div class="clr"></div>
                        <b>Preferred Age Demographic:</b>
                        <div class="info" id="info2" title="Despite your minimum age requirement, what age group are you primarily targeting."></div>
                      </div>
                      <div class="preferredAge"> <span>Men</span>
                        <select name="men_preferred_age" style="width:104px" id="event_age_suitab">
                          <option value="">-Select age-</option>
                          <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
                          <option value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_men_preferred_age)
							{ echo 'selected="selected"'; }?>> <?php echo $rowAge['name']?> </option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="preferredAge"> <span>Women</span>
                        <select name="women_preferred_age" style="width:104px" id="event_age_suitab">
                          <option value="">-Select age-</option>
                          <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
                          <option value="<?php echo $rowAge['id']?>"
						  <?php if($rowAge['id'] == $bc_women_preferred_age){
						  echo 'selected="selected"'; }?>><?php echo $rowAge['name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="clr"></div>
                    </div>
                    <div>
                      <div class="clr"></div>
                    </div>
                  </div>
                  <div class="stpBox" style="float:right; width:450px">
                    <div class="title" style="width:435px;">Music Details</div>
                    <div class="data" style="width:430px">
                      <ul style="list-style:none; margin:0px; padding:0 0 0 6px">
                        <?php 
								$sqlMusic = "SELECT name,id FROM music";
								$resMusic = mysql_query($sqlMusic);
								$totalMusic= mysql_num_rows($resMusic);
								$no = 0;
								
								if ( !is_array($bc_event_music) )
									$bc_event_music = array();
								
								while($rowMusic = mysql_fetch_array($resMusic))
								{
									if ( in_array($rowMusic['id'],$bc_event_music) )
										$che = 'checked="checked"';
									else
										$che = '';		
								?>
                        <li style="width:33%; float:left; padding:3px 0">
                          <label for="<?php echo $no; ?>">
                          <input <?php echo $che;?> id="<?php echo $no; ?>" type="checkbox" style="float:left" name="event_music[]" value="<?php echo $rowMusic['id']?>"   />
                          <div style="float:left; margin-right:5px"> <?php echo $rowMusic['name']?> </div>
                          </label>
                        </li>
                        <?php $no++;} ?>
                      </ul>
                      <div class="clr"></div>
                    </div>
                    <div>
                      <div class="clr"></div>
                    </div>
                  </div>
                  <div class="clr"></div>
                  <?php if ($bc_event_type=='1'){?>
                  <div class="occupation">
                    <div class="title">Occupation Target
                      <div id="info3" class="info" title="If your event caters to a particular industry of professionals, let us know what and we will aid in marketing to this group"></div>
                    </div>
                    <div class="data">
                      <?php
			$rt = mysql_query("select * from `occupation` ORDER BY `id` ASC");
			while($rw = mysql_fetch_array($rt)){
			$selected = checkForSelected($rw['id'],$frmID);
			echo '<div style="float:left; width:33%; padding:3px 0"><label><input type="checkbox" '.$selected.' value="'.$rw['id'].'" name="occupation_target[]" /> &nbsp;'.$rw['occupation'].'</label></div>';
			}
			?>
                      <div class="clr"></div>
                    </div>
                  </div>
                  <?php } ?>
                </div>
                <h3>STEP
                  <?php if ($bc_event_type=='0'){ echo "3";} else{ echo "4"; } ?>
                  | <span>ADD
                  <?php if ($bc_event_type=='0'){ echo "EVENT IMAGE"; } else{ echo "IMAGES AND VIDEO"; }?>
                  </span> </h3>
                <div id="box" class="box">
                  <div id="head">Main Event Image:
                    <div class="info" id="info4" title="This is the main event image we will use for your advertising. Make sure the image you upload is a high quality, appropriate image."></div>
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
		echo $image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'"style="cursor:pointer"
		rel="events|event_image|'.$bc_image.'|event_images/|delImg_image" />';
		?>
		<input type="hidden" name="mn_image" value="<?php echo $bc_image; ?>" />
	<?php
	}
	?>
                    <br />
                    <input type="file" name="event_image" />
                  </div>
                  <div class="ev_fltlft" style="padding:0 0 0 10px;">Must be JPG, GIF or PNG.<br />
                    Main Event Image should be in portrait view for best display.</div>
                  <div class="clr"></div>
                  <?php if ($bc_event_type!='0'){?>
                  <div id="head">Image Galleries:
                    <div class="info" id="info5" title="Provide image galleries that will help your customers get a better feel for you event. (i.e. What to wear, Past event, What to expect)"></div>
                  </div>
                  <div class="gallery_area">
                    <div id="head" style="padding:16px 0 12px; font-size:22px">Gallery Name: </div>
                    <input type="text" name="gallery" value="<?php if ($bc_gallery){echo $bc_gallery;}else{ echo "Create a name for your image gallery (i.e. Dress Code)"; } ?>" id="gname" onFocus="removeText(this.value,'Create a name for your image gallery (i.e. Dress Code)','gname');" onBlur="returnText('Create a name for your image gallery (i.e. Dress Code)','gname');" class="new_input" style="width:534px;" />
                    <?php
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
							
				   if($bc_event_type==2){?>
                    <div id="add_more_image_area"></div>
                    <div class="clr"></div>
                    <div align="right"><br />
                      <br />
                      <span id="add_more_image_btn"><img src="<?php echo IMAGE_PATH; ?>/add_more.png" style="cursor:pointer" id="" onClick="add_more_image(<?php if ($count_images){echo $count_images;} else{ echo "4"; } ?>)" /></span> </div>
                    <?php } ?>
                  </div>
				  <div class="clr"></div>
                  <div id="head">Event Video:
                    <div class="info" title="If you have a video uploaded to Vimeo or YouTube, simple copy the embed code from that site and paste it in the box below"></div>
                  </div>
                  <div class="gallery_area" style="padding:0px; width:875px">
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
                    <div style="float:left; width:380px; margin-right:20px">
                      <div id="head" style="padding:16px 0 12px; font-size:22px">Video Name:</div>
                      <input type="text" name="video_name[]" value="<?php if ($video_name){ echo $video_name; } else{ echo "Enter the name of your video"; } ?>" id="video_name<?php echo $i; ?>" onFocus="removeText(this.value,'Enter the name of your video','video_name<?php echo $i; ?>');" onBlur="returnText('Enter the name of your video','video_name<?php echo $i; ?>');" class="new_input" style="width:350px;">
                    </div>
                    <div style="float:left; width:454px; margin-right:20px">
                      <div id="head" style="padding:16px 0 12px; font-size:22px">Copy and Paste the Video Embed Code Here:</div>
                      <textarea class="new_input" name="video_embed[]" style="width:466px; height:130px;"><?php if ($video_embed){ echo $video_embed; }?>
</textarea>
                    </div>
                    <div class="clr"></div>
                    <?php
					} // END $i <=1 
							} // END $bc_event_type==1
						else{ ?>
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
                      <textarea class="new_input" name="video_embed[]" style="width:466px; height:130px;"><?php if ($video_embed){ echo $video_embed; }?></textarea>
                    </div>
                    <div class="clr"></div>
					</div>
                    <?php				
						
						}
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
							 if($bc_event_type==2){?>
                    <div id="add_more_video_area"></div>
                    <div class="clr"></div>
                    <div align="right"><br />
                      <br />
                      <span id="add_more_video_btn"><img src="<?php echo IMAGE_PATH; ?>/add_more.png" style="cursor:pointer" id="" onClick="add_more_video(<?php if ($count_videos){echo $count_videos;} else{ echo "1"; } ?>)" /></span> </div>
                    <?php } ?>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
          <?php
		 if($bc_event_type==1 && !$_GET['id']){
		 ?>
          <img src="<?php echo IMAGE_PATH; ?>check_out_new.png" name="create" value="Create Event" style="cursor:pointer" onClick="save();"  align="right" />
          <?php
		   }
	   else{?>
          <img src="<?php echo IMAGE_PATH; ?>publish_new.png" name="create" value="Create Event" style="cursor:pointer" onClick="save();"  align="right" />
          <?php } 
		
		
		 if( $_GET['id'] && $bc_event_type == 1 ){
		 echo '<a href="'.ABSOLUTE_PATH_SECURE.'fbflayer/index.php?id='.$frmID.'" class="fancybox2"><img src="'.IMAGE_PATH.'/preview_new.png" align="right"  /></a>';
		 }
		if($bc_event_type=='0' || $bc_event_type=='1' || $bc_event_type=='2'){
		 if($_GET['id']){
		 if($bc_type=='draft'){?>
          <img src="<?php echo IMAGE_PATH; ?>save_as_draft_new.png" alt="" value="Save As Draft" title="Save As Draft" onClick="draft();" style="cursor:pointer" align="right">
          <!--<a href="#"><img src="<?php echo IMAGE_PATH; ?>preview.gif" alt="" title="Preview"></a>-->
          <?php }}else{
		 ?>
          <img src="<?php echo IMAGE_PATH; ?>save_as_draft_new.png" alt="" value="Save As Draft" title="Save As Draft" onClick="draft();" style="cursor:pointer" align="right">
          <!--<a href="#"><img src="<?php echo IMAGE_PATH; ?>preview.gif" alt="" title="Preview"></a>-->
          <?php
		 } }
		 ?>
          <input type="hidden" name="evntIdForDraft" value="<?php echo $event_id; ?>" />
          <input type="hidden" name="create" value="Create Event" />
          <input type="hidden" name="event_type" value="<?php echo $bc_event_type; ?>" />
          <div class="clr"></div>
        </div>
      </form>
      <?php } ?>
    </div>
  </div>
</div>
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
<?php include_once('includes/footer.php');?>
<script>

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
		content_css : "style.css",
	});
	
	
$(".delImg").click(function() {
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
   /* $('#example-1').tipsy();
    $('#north').tipsy({gravity: 'n'});
    $('#south').tipsy({gravity: 's'});
    $('#east').tipsy({gravity: 'e'});*/
    $('.info').tipsy({gravity: 'w', fade: true});
   /* $('#auto-gravity').tipsy({gravity: $.fn.tipsy.autoNS});
    $('#example-fade').tipsy({fade: true});
    $('#example-custom-attribute').tipsy({title: 'id'});
    $('#example-callback').tipsy({title: function() { return this.getAttribute('original-title').toUpperCase(); } });
    $('#example-fallback').tipsy({fallback: "Where's my tooltip yo'?" });
    $('#example-html').tipsy({html: true });*/
  });
</script>