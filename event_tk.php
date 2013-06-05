<?php 

require_once('admin/database.php');
require_once('site_functions.php');

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

$category_seo 	= $_GET['category'];
$sub_cat_seo	= $_GET['sub_cat'];

$event_seo_name	= $_GET['event_id'];


$event_id		= getSingleColumn('id',"select * from `events` where `seo_name`='$event_seo_name'");


updateView($event_id);


$type			= $_GET['type'];

if ( !is_numeric($event_id) || $event_id <= 0 )
	die("Direct access to this page is not allowed.");

$category_link 		= ABSOLUTE_PATH . 'category/' . $category_seo . '.html';
$sub_category_link	= ABSOLUTE_PATH . 'category/' . $category_seo . '/' . $sub_cat_seo . '.html';

// $sql = "select * from events where id='". $event_id ."' && `type`!='draft' && `event_status`='1'";
$sql = "select * from events where id='". $event_id ."'";
$res = mysql_query($sql);
if ( mysql_num_rows($res) ) {
if ( $row = mysql_fetch_assoc($res) ) {
	//d($_SERVER,1);
	$c_event_id			= $row["id"];
	$fb_event_id		= $row["fb_event_id"];
	$userid				= $row["userid"];
	$category			= attribValue("categories","name","where id=" . $row["category_id"] );
	$subcategory_id		= DBout($row["subcategory_id"]);
	$subcategory		= attribValue("sub_categories","name","where id=" . $row["subcategory_id"] );;
	$event_name			= DBout($row["event_name"]);
	
	$event_start_time	= $row["event_start_time"];
	$event_end_time		= $row["event_end_time"];
	$event_start_am_time= $row["event_start_am_time"];
	$event_end_am_time	= $row["event_end_am_time"];
	$event_description	= DBout($row["event_description"]);
	$event_cost			= DBout($row["event_cost"]);
	$event_image		= $row["event_image"];
	$event_sell_ticket	= $row["event_sell_ticket"];
	$event_age_suitab	= $row["event_age_suitab"];
	$event_status		= $row["event_status"];
	$publishdate		= $row["publishdate"];
	$averagerating		= $row["averagerating"];
	$modify_date		= $row["modify_date"];
	$added_by			= $row["added_by"];
	$source				= $row['event_source'];
	$video_embed		= $row['video_embed'];
	$video_name 		= $row['video_name'];
	$event_type			= $row['event_type'];
	$event_seo_name		= $row['seo_name'];
	$draft_type 		= $row['type'];
	$is_private			= $row['is_private'];
	$event_date			= getEventDates($event_id);
	$venue_attrib		= getEventLocations($event_id);
	$event_locations	= $venue_attrib[0];
	$free_event			= $free_event;
	$bc_alter			= $row['alter'];
	$bc_alter_url		= $row['alter_url'];
	
	if($member_id!=$userid){
		if($is_private && $member_id!=''){
			$loginEmail	= getSingleColumn("email","select * from `users` where `id`='". $member_id ."'");
			$acess		= getSingleColumn("id","select * from `private_emails` where `email`='". $loginEmail ."' && `event_id`='". $c_event_id ."'");
			if(!$acess)
				echo "<script>window.location.href='". ABSOLUTE_PATH ."login.php'</script>";	
		}
		if($is_private && $member_id=='')
				echo "<script>window.location.href='". ABSOLUTE_PATH ."login.php'</script>";
	}

//	$time				= $event_start_time . ' - ' . $event_end_time;
	$cost				= $event_cost;
	
if($added_by==''){
	$usertype		= getSingleColumn('usertype',"select * from `users` where `id`='$userid'");
	if($usertype==2){
		$hosted_by		= getSingleColumn('business_name',"select * from `promoter_detail` where `promoterid`='$userid'");
		}
	if($usertype==1 || $hosted_by==''){
			$name		= getSingleColumn('firstname',"select * from `users` where `id`='$userid'");
			$lname		= getSingleColumn('lastname',"select * from `users` where `id`='$userid'");
			$hosted_by = $name." ".$lname;
		}
}
else{
	$hosted_by = $added_by;
	}
	$event_dateT		= getEventStartDateFB($event_id);
	
	$event_time			= getEventTime($event_dateT[1]);
	
	if ( $event_time['start_time'] != '' && $event_time['start_time'] != '00:00:00' ) 
		$time = date("h:i A", strtotime($event_time['start_time']));
		
	if ( $event_time['end_time'] != '' && $event_time['end_time'] != '00:00:00' ) 
		$time = $time . ' - ' . date("h:i A", strtotime($event_time['end_time']));	
	$meta_title = $event_name;
	
	
	$event_description_s = strip_tags($event_description);
//	$event_description_s = breakStringIntoMaxChar($event_description_s,200);
		
	if (trim($event_image) != '') {

		if ( substr($event_image,0,7) != 'http://' && substr($event_image,0,8) != 'https://' ) {
		
		if ( file_exists(DOC_ROOT . 'event_images/' . $event_image ) ) {
		
			$event_image = removeSpaces("events","event_image",$event_image,DOC_ROOT."event_images/");
		
		
			$image = ABSOLUTE_PATH .'event_images/' . $event_image;
			$imageE = ABSOLUTE_PATH .'event_images/' . $event_image;
			
			}
			else{
			$img_params = ' src="'. ABSOLUTE_PATH .'images/bigAvatar_photo.png" width="163" border="0" ';
			$viw = "163";
			$kk = 1;
			}
		} else {
			if ( $source == "EventFull") {
				if ( strtolower(substr($event_image,-4,4)) != '.gif')
					$image = str_replace("/medium/","/large/",$event_image);	
			}else {
				$image = $event_image;
			}	
		}
	//	$img_params = returnImage($image,272,375);
	
	if($kk!=1){
		list($viw, $vih) = getimagesize($image);
		if($viw < 420 && ($viw > 163 || $vih > 200)){
			list($viw, $vih) = getPropSize($viw, $vih, 163,200);
		}
		else{
			list($viw, $vih) = getPropSize($viw, $vih, 420,650);
			}
	$img_params = 'src="'.$image.'" height="'.$vih.'" width="'.$viw.'" ';
		}
	} else {
		$img_params = ' src="'. ABSOLUTE_PATH .'images/bigAvatar_photo.png" width="163" border="0" ';
		$viw = "163";
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
}
else{

echo "<script>window.location.href='".ABSOLUTE_PATH."index.php'</script>";
}




///// RSVP //////
if($_POST['submit_rsvp']){
	$fname_rsvp	= $_POST['firstname'];
	$lname_rsvp	= $_POST['lastname'];
	$email_rsvp	= $_POST['email'];
	$how_did_rsvp= $_POST['how_did'];
	if($how_did_rsvp=='Other')
		$how_did_rsvp= $_POST['how_other'];
	if($how_did_rsvp=='Type here')
		$how_did_rsvp= '';
	$name = $fname_rsvp." ".$lname_rsvp;
	$rs = mysql_query("select * from `events_rsvp` where `event_id`='$event_id' && `email`='$email_rsvp'");
	if(!mysql_num_rows($rs)){
		mysql_query("INSERT INTO `events_rsvp` (`rsvp_id`, `event_id`, `name`, `email`, `how_did_hear`) VALUES (NULL, '$event_id', '$name', '$email_rsvp', '$how_did_rsvp')");
	}
}


$meta_descrp = substr($event_description_s,0,200);

include_once('includes/header.php');

if ( $event_id == 23279 ) {
?>
<script type="text/javascript" src="http://promoshq.wildfireapp.com/website/302/companies/219717/widget_loader"></script>
<?php
}

?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a#eventImage").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
		
		$("#like").click(function(){
		var type = "like";
			$.ajax({  
				type: "POST",
				url: "<?php echo ABSOLUTE_PATH;?>ajax/likedilike.php",
				data: "type=" +type+"&event_id="+<?php echo $event_id; ?>,
				dataType: "text/html",
				success: function(html){
					$('#lk').show();
					$('#lk').html(html);
					var abc = function(){
						$('#lk').fadeOut(1000);
					}
				
				setTimeout(abc,3000);
				},
			});
		});
		
		$("#dislike").click(function(){
		var type = "dislike";
			$.ajax({  
				type: "POST",
				url: "<?php echo ABSOLUTE_PATH;?>ajax/likedilike.php",
				data: "type=" +type+"&event_id="+<?php echo $event_id; ?>,
				dataType: "text/html",
				success: function(html){
				$('#lk').show();
					$('#lk').html(html);
					var abc = function(){
						$('#lk').fadeOut(1000);
					}
				
				setTimeout(abc,3000);
				},
			});
		});
	});
	
	
function checkRsvp(){
	if($('#firstname').val()==''){
		alert("Enter First name");
		return false;}
	if($('#lastname').val()==''){
		alert("Enter Last name");
		return false;}
	if($('#email').val()==''){
		alert("Enter Email address");
		return false;}
	var str = $('#email').val();
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	if (filter.test(str))
		testresults=true;
	else{
		alert("Invalid Email address!");
		return false;
		}
}

</script>
<style>
.nav_new {
    background: none repeat scroll 0 0 #FFFFFF;
    border-radius: 5px 5px 5px 5px;
    box-shadow: 0 0 7px 2px #C7C7C7;
	overflow:hidden;
	margin:0;
	}

.nav_new ul li {
    border-right: 1px solid #B4B4B4;
    float: left;
    list-style: none outside none;
}

.nav_new ul li a {
    color: #000000;
    float: left;
    font-size: 15px;
    font-weight: bold;
    padding: 13px 24px;
    text-decoration: none;
}
.basic-modal-content {
/*	display:none;*/
}
/* Overlay */
#simplemodal-overlay {
	background-color:#000;
	cursor:wait;
}
/* Container */
#simplemodal-container {
	height:250px;
	width:400px;
	color:#000;
	-moz-border-radius:10px;
	border-radius:10px;
	-webkit-border-radius:10px;
	-moz-box-shadow:0 1px 3px #777;
	-webkit-box-shadow:0 2px 3px #777;
	box-shadow:0 2px 3px #777;
	background: #E7FCFF;
	background:-webkit-gradient(linear, left bottom, left top, color-stop(0.4, #E7FCFF), color-stop(0.70, #FFFFFF));
	background:-moz-linear-gradient(center bottom, #E7FCFF 40%, #FFFFFF 70%) repeat scroll 0 0 transparent;
	-pie-background:linear-gradient(90deg, #E7FCFF, #FFFFFF 30px);
	padding:12px;
    behavior: url(http://www.eventgrabber.com/css/PIE.htc);
}
#simplemodal-container .simplemodal-data {
	padding:8px;
}
#simplemodal-container code {
	background:#141414;
	border-left:3px solid #65B43D;
	color:#bbb;
	display:block;
	font-size:12px;
	margin-bottom:12px;
	padding:4px 6px 6px;
}
#simplemodal-container a {
	color:#ddd;
}
#simplemodal-container a.modalCloseImg {
	background:url(<?php echo IMAGE_PATH; ?>x.png) no-repeat;
	width:25px;
	height:29px;
	display:inline;
	z-index:3200;
	position:absolute;
	top:-15px;
	right:-16px;
	cursor:pointer;
}
#simplemodal-container h3 {
	background: none repeat scroll 0 0 #F2F2F2;
    border-left: 1px solid #CCCCCC;
    border-right: 1px solid #CCCCCC;
    border-top: 1px solid #CCCCCC;
    color: #84B8D9;
    margin: 0;
    padding: 10px;
}
.formfield{
	overflow:hidden;
	padding-top: 8px;
	}
.formfield label{
	float: left;
	color: #666666;
    font-weight: bold;
    padding-right: 10px;
    text-align: right;
	width:115px;
	}
.formfield input,.formfield select{
	width:232px;
	border: 1px solid #BDC7D8;
	height:18px;
    font-size: 11px;
	}
	
.formfield select{
	height: 22px;
    padding: 2px;
    width: 234px;
	}
.rsvpBox{
	border: 1px solid #C1C1C1;
	background:#FFFFFF;
	min-height:150px;
	padding-top:10px;
	}
</style>
<!--<link type='text/css' href='<?php echo ABSOLUTE_PATH;?>css/basic.css' rel='stylesheet' media='screen' />    -->
<!--[if lt IE 7]>

    <link type='text/css' href='<?php echo ABSOLUTE_PATH;?>css/basic_ie.css' rel='stylesheet' media='screen' />

<![endif]-->


<link type="text/css" rel="stylesheet" media="screen, projection" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/black-tie/jquery-ui.css" />
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH;?>js/jquery.simplemodal.js'></script>
<style type="text/css">
    #simplemodal-container {
 /*       width:  440px;
        height: 360px; */
    }
    .page_like_box {
        z-index: 10001;
        position: absolute;
        width: 420px;
        height: 340px;
        display: none;
    }
</style>
<script type='text/javascript'>
    function showPopup(){
        $('#basic-modal-content').modal();
    }
</script>

<div class="topContainer">
  <div class="welcomeBox"><?php
  if($_SESSION['admin_user']){?>
	<a href="<?php echo ABSOLUTE_PATH; ?>admin/events.php?id=<?php echo $event_id; ?>" target="_blank" style="color:#ff4e1f; font-weight:normal">Edit Event</a>
  <?php } ?></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> </div>
    <div class="clr"></div>
    <div class="gredBox">
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:1px;">
            <!--Start new code-->
            <div class="buyTicketp1">
              <div style="overflow:hidden; float:left" >
                <div class="thumb-1"> <?php echo $image_display; ?>
                </div>
                <!--end thumb-1-->
                <div style="text-align:center">
                  <?php echo  getAddToWallButton($event_id,'');
						
					//	echo $viw;
						if($viw < 420){
						echo "<div class='clr'><br /></div>";
						}
						 ?></div>
                <span id="lk"></span> </div>
              <div class="thumb-1-detail" style="width:<?php if($viw >= 420) {} else{ echo '700px'; } ?>">
			  <div class="ew-heading"><?php echo $event_name; ?></div>
                <!--end ew-heading-->
               <!--	   <div class="ew-heading-behind">
                <span>Celebration</span>
                </div> -->
                <!--end ew-heading-behind-->
              <!--  <span class="ew-heading-a" style="margin-top:11px; display: block;"><?php echo date('M d, Y',strtotime($event_dateT[0])); // $event_date; ?></span>
				<span class="ew-heading-a" style="margin-top:11px; display: block;"><?php echo $venue_attrib[1]['venue_name']; ?></span>-->
				
				<div class="hosted_by">Hosted by: <?php echo $hosted_by; ?></div>
				
                
                
                  <?php
                  	if($cost) { ?>
                    	<div class="ew-price-area"> <span  class="ew-heading-a">Price:&nbsp;<span style="color:#ff4e1f;"><?php echo $cost; ?></span></span></div>
                  <?php
				  		}
						else { ?>
					<div class="ew-price-area"> <span  class="ew-heading-a">Price:&nbsp;<span style="color:#ff4e1f;">See Details</span></span></div>
				<?php } ?>
                <div class="ew-when-where" style="float: left; padding-left: 10px; width: <?php if ($viw < 420 ){ echo "23%"; } else{ echo "47%"; } ?>;"> <span class="ew-when-heading">When</span> <span> <?php echo date('l, F dS',strtotime($event_dateT[0]));
							echo "<br>".$time;
							 ?> </span> </div>
		
                <!--end ew-when-where-->
                <div class="ew-when-where" style="float:left; width:<?php if ($viw < 420 ){ echo "29%"; } else{ echo "47%"; } ?>;"> <span class="ew-when-heading">Where</span> <span> <?php echo substr($event_locations, 0, -4); ?> <a href="?type=location#l">[+] See Map</a> </span> </div>
                <!--end ew-when-where-->
				<?php if ($viw < 420){?>
				
				<div class="ew-when-where" style="float: right; padding-left: 10px; width: 45%;"> <span class="ew-when-heading">Share with Friends</span> <span>
                  <table cellpadding="0" cellspacing="0" align="right">
                    <tr>
                      <td width="70" valign="bottom"><!-- Twitter -->
                        <a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical" data-via="pangea" data-related="general_tips">Tweet</a>                        <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                      </td>
                      <td width="60" valign="bottom">
                        <g:plusone size="tall"></g:plusone>
                        <script type="text/javascript">
						  (function() {
							var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
							po.src = 'https://apis.google.com/js/plusone.js';
							var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
						  })();
						</script>
					</td>
                      <td width="71" valign="bottom"><!-- Linkedin -->
					 <style>
					  .ew-when-where span {
						margin-top: 0;
						}
					</style>
                        <script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
                        <script type="IN/Share" data-counter="top"></script>
					
						</td>
                      <td width="30" valign="bottom">
					  <div id="fb-root"></div>
                        <script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
                        <div class="fb-like" data-href="<?php echo $page_url; ?>" data-send="false" data-layout="box_count" data-width="30" data-show-faces="false"></div>
                      </td>
                      <td width="69" valign="bottom">
							<div class="fb-send" data-href="<?php echo $page_url; ?>"></div>
                      </td>
					
                    </tr>
                  </table>
                </div>
                <div class="clr"></div>
				<?php if($event_type!=0){?>
					<div class="ew-when-where" style=""><span class="ew-when-heading">Make Health Plans</span>
					  <table cellpadding="0" cellspacing="0" align="left" style="padding-top:5px;">
						  <tr>
						  	<td>
						  		<!--<button class="nav_new active" style="cursor:pointer;" onclick="showPopup();return false;">RSVP</button>-->
			<button class="nav_new active" style="cursor:pointer;" onclick="rsvp('/','<?php echo $event_id; ?>');">Start</button>
			
							</td>
						</tr>
					  </table>
					</div>
				<?php
					}
				} 
				?>
				
                <div class="clr"></div>
                <div class="ew-when-where"> <span class="ew-when-heading">Consultation Summary</span> <span>
                  <?php
							if(strlen($event_description_s) > 200){
								echo substr($event_description_s,0,200).'... <a href="?type=details#l">[More]</a>';
							}
							else{
								echo $event_description_s;
							}
							?>
                  <?php  //echo $event_description; ?>
                  </span> </div>
                <!-- end ew-when-where -->
                <div class="clr"></div>
				<?php
				if($viw == 420){?>
                <div class="ew-when-where"> <span class="ew-when-heading">Share with Friends</span>
                  <table cellpadding="0" cellspacing="0" align="right" style="padding-top:5px;">
                    <tr>
                      <td width="70" valign="bottom"><!-- Twitter -->
                        <a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical" data-via="mayank1509" data-related="general_tips">Tweet</a>
                        <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                        
                      </td>
                      <td width="60" valign="bottom"><!-- Google -->
                        <g:plusone size="tall"></g:plusone>
                        <!-- Place this render call where appropriate -->
                        <script type="text/javascript">
						  (function() {
							var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
							po.src = 'https://apis.google.com/js/plusone.js';
							var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
						  })();
						</script>
					</td>
                      <td width="71" valign="bottom"><!-- Linkedin -->
					 <style>
					  .ew-when-where span {
						margin-top: 0;
						}
					</style>
                        <script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
                        <script type="IN/Share" data-counter="top"></script>
					
						</td>
                      <td  valign="bottom" style="width:100px">
					  <div id="fb-root"></div>
                        <script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>							
							<div class="fb-like" style="width:266px; height:24px" data-href="<?php echo $page_url; ?>" data-send="true" data-width="100" data-show-faces="false"></div>
                      </td>
					  
                    </tr>
                  </table>
                </div>
				<?php if($event_type!=0){?>
					<div class="ew-when-where" style="padding-top:70px;"><span class="ew-when-heading">Make Health Plans</span>
					  <table cellpadding="0" cellspacing="0" align="left" style="padding-top:5px;">
						  <tr>
						  	<td>
						  		<button class="nav_new active" style="cursor:pointer;" onclick="rsvp('/','<?php echo $event_id; ?>');">Start</button>
							</td>
						</tr>
					  </table>
					</div>
			<?php
				}
			}
			?>
                
                
              </div>
              <!-- end thumb-1-detail -->
              <div class="clr"><br />
                <br />
              </div>
            </div>
            <!-- end buyTicketp1 -->
            <div class="nav_new">
              <ul>
                <li <?php if ($type=='deals'){ echo 'class="active"'; } ?>><a href="?type=deals#l">Patient Library</a></li>
                <li <?php if ($type=='location'){ echo 'class="active"'; } ?>><a href="?type=location#l">Plans and Comments</a></li>
                <li <?php if ($type=='details' || $type==''){ echo 'class="active"'; } ?> ><a href="?type=details#l">EMR 1</a></li>
				<?php if ( $event_type > 0 ) { ?>
                <li <?php if ($type=='gallery'){ echo 'class="active"'; } ?>><a href="?type=gallery#l">EMR 2</a></li>
                <li <?php if ($type=='videos'){ echo 'class="active"'; } ?>><a href="?type=videos#l">EMR 3</a></li>
				<?php } else { ?>
				<li ><a href="javascript:void(0)">Gallery</a></li>
                <li ><a href="javascript:void(0)">Videos</a></li>
				<?php } ?>
                <li <?php if ($type=='similar'){ echo 'class="active"'; } ?>><a href="?type=similar#l">Billing</a></li>
				<li <?php if ($type=='comments'){ echo 'class="active"'; } ?>><a href="?type=comments">History</a></li>
				
              </ul>
            </div>
            <!--end nav_new-->
            <br class="clear" />
			<a id="l"></a>
            <?php 
				if ( $type == 'location' )
					include_once("widget_location.php");
				else if ( $type == 'similar' )
					include_once("widget_special.php");
				else if ( $type == 'eventrating' )
					include_once("widget_eventrating.php");
				else if ( $type == 'deals' )
					include_once("widget_deals.php");
				else if ( $type == 'gallery' )
					include_once("widget_gallery.php");
				else if ( $type == 'videos' )
					include_once("widget_videos.php");
				else if ( $type == 'comments' )
					include_once("widget_comments.php");	
				else
					include_once("widget_eventdetails.php");
								
			?>
            <!--End new code-->
          </div>
        </div>
      </div>
      <div class="create_event_submited"> </div>
      </form>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php');?>