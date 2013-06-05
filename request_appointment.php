<?php
include_once('admin/database.php');
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
	echo "<script>window.location.href='user_login.php';</script>";

if($_GET["id"]){
	if (validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'request_appt',$_GET["id"]) =='false'){
		echo "<script>window.location.href='user_login.php';</script>";
	}
}



$bc_patient_id  	= $_SESSION['LOGGEDIN_MEMBER_ID'];
$bc_reason   		= $_POST["reason"];
$bc_date_requested  = date("Y-m-d",strtotime($_POST["date_requested"]));
$bc_time_requested  = $_POST["time_requested"];
$bc_time_ampm	    = $_POST["time_ampm"];
$bc_date_created  	= date("Y-m-d");
$bc_clinic_id		= getSingleColumn("clinicid","select * from `patients` where `id`='$bc_patient_id'");

if($bc_time_requested!='' && $bc_time_requested!='0:00'){
if($bc_time_ampm=='am'){
$bc_time_requested .= " AM";
}else{
$bc_time_requested .= " PM";
}
 $bc_time_requested = date("H:i", strtotime($bc_time_requested));
}


$frmID = $_GET["id"];


$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();

if ($_POST["reason"] == "")
	$errors[] = "plan detail can not be empty";
if ($_POST["time_requested"] == "" || $_POST["time_requested"]=='0:00' )
	$errors[] = "Request Time can not be empty";


$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';

for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		if ($action1 == "save") {
				$chek_prev_req = mysql_query("select * from `request_appt` where `patient_id`='".$bc_patient_id."' && `clinic_id`='".$bc_clinic_id."'");
				$check_have_res = mysql_num_rows($chek_prev_req);
				if($check_have_res){	
				while($get_id_pre = mysql_fetch_array($chek_prev_req)){
				$pre_id = $get_id_pre['id'];
				$sql = "update `request_appt` set `clinic_id`='".$bc_clinic_id."',`patient_id`='".$bc_patient_id."',`reason`='".$bc_reason."',`date_requested`='".$bc_date_requested."',`time_requested`='".$bc_time_requested."',`time_requested`='".$bc_time_requested."' , `date_created`='".$bc_date_created."' where id='".$pre_id."'";
				$res = mysql_query($sql);
			
					if ($res) {
						$sucMessage = "Record Successfully inserted.";
						echo "<script>window.location.href='dashboard.php';</script>";
					} else {
						$sucMessage = "Error: Please try Later";
					} // end if res
		
				}			
				}else {
			  $sql = "insert into request_appt (clinic_id,patient_id,reason,date_requested,time_requested,date_created) values ('" . $bc_clinic_id . "','" . $bc_patient_id . "','" . $bc_reason . "','" . $bc_date_requested . "','" . $bc_time_requested . "','" . $bc_date_created . "')";
			$res = mysql_query($sql);
			
		if ($res) {
			$sucMessage = "Record Successfully inserted.";
			/* echo "<script>window.alert("Success! Your request has been sent.");</script>"; */
			echo "<script>window.location.href='dashboard.php';</script>";
		} else {
			$sucMessage = "Error: Please try Later";
		} // end if res
}
	} // end if errors
	else {
		$sucMessage = $err;
	}
	}
} // end if submit



$sql = "select * from `request_appt` where `id`=$frmID";
$res = mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
/* 		$bc_clinic_id  		= $row["clinic_id"]; */
		$bc_patient_id  	= $row["patient_id"];
		$bc_reason   		= $row["reason"];
		$bc_date_requested 	= $row["date_requested"];
		$bc_date_created 	= $row["date_created"];
		
		$res2	= mysql_query("select * from `patients` where `id`='". $bc_patient_id ."'");
		while($row2 = mysql_fetch_array($res2))
			$bc_clinic_id[]		=	$row2['clinicid'];
		
	} // end if row
	$action = "edit";
} // end if

 $sql = "select * from `patients` where `id`='$bc_patient_id'";
$r = mysql_query($sql);
while($ro = mysql_fetch_array($r)){
	$bc_patient_address  = $ro['address'];
	$bc_patient_name  = $ro['username'];
	$bc_patient_city  = $ro['city'];
	$bc_patient_zip   = $ro['zip'];
}




$meta_title = "Request Appointment";

include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/humanity/jquery-ui.css" type="text/css" media="all" />

<script>
    $(document).ready(function() {

        $('.savedate').datepicker();

    });
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
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.tipsy.js"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.core.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.theme.css">
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.css">
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.js"></script>
<script>
	$(function() {
		$( "#date_requested" ).datepicker({
			dateFormat: "mm/dd/yy",
			changeMonth: true,
			changeYear: false,
			yearRange: '2013:2014'
			});
	});
	
</script>
<style>
.savedate {
background: none repeat scroll 0 0 #FFFFFF;
    color: #000000;
	 border: 1px solid #B8B8B8;
    border-radius: 5px 5px 5px 5px;
	font-size: 15px;
    padding: 5px 3px;
}
</style>
<script type="text/javascript">


function remove(value){
	$('#row'+value).remove();
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


<?php if($_GET['t']){?>
$(document).ready(function(){
	$('.box').css('display','none');
	$('#box<?php echo $_GET["t"]; ?>').css('display','block');
});
<?php } ?>

</script>
<style>

.ev_title input{
	color: #808080;
	font-weight:normal;
	}

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
  <script language="javascript">



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
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Request Appointment</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
		<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
        <?php include('dashboard_menu_tk.php'); ?>
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">
                <h3>STEP 1 | <span>Select the Reason for Appointment</span></h3>
                <div id="box" class="box">


	            <div id="head">Reason for Appointment (Please Provide Details of Your Concerns)</div>
	               <div>
                    <textarea name="reason" id="reason" class="bc_input" style="width:825px; height:250px"><?php echo $bc_reason; ?></textarea>
                  </div>
               
                <div class="clr"></div>
                
                <div class="bxs">
                <div id="head">Preferred Appointment Date</div>
                	<div>
                    	<input type="text" class="new_input" name="date_requested" id="date_requested" value="" />
                	</div>
                </div>
				<div class="bxs">
                <div id="head">Preferred Appointment Time</div>
                	<div>
                    	<input type="text" class="new_input" name="time_requested" id="time_requested" value="<?php if($bc_time_requested){}else {echo "0:00";} ?>" />&nbsp;<select name="time_ampm">
						<option value="am">AM</option>
						<option value="pm">PM</option>
						</select>
                	</div>
                </div>
	    </div>	

  <div class="create_event_submited">
		
		<input type="hidden" name="submit" value="1" />
		<a href="<?php echo ABSOLUTE_PATH; ?>patient_calendar.php"><input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" name="create" value="Create Event" style="cursor:pointer" onClick="save();"  align="right" /></a> 
       
        
         
          
          <div class="clr"></div>
        </div>
      </form>
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
		theme : "simple",
		elements : "reason",
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
