<?php
include_once('admin/database.php'); 
include_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

 $bc_patient_id	=	$_POST["patient_id"];

$sql = "select * from `patients` where `id`='$bc_patient_id'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_patient_address	=	$ro['address'];
	$bc_patient_name		=	$ro['username'];
	$bc_patient_city		=	$ro['city'];
	$bc_patient_zip		=	$ro['zip'];
	}

 $bc_event_description	=	$_POST["event_description"];
$bc_	=	$_POST[""];

$frmID	=	$_GET["id"];

$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

$action = "save";
$sucMessage = "";

$errors = array();
if ($_POST["patient_id"] == "")
	$errors[] = "patient_id can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {

	if (!count($errors)) {

		 if ($action1 == "save") {
			$sql	=	"insert into schedule_patient (patient_id,comments,) values ('" . $bc_patient_id . "','" . $bc_event_description . "','" . $bc_ . "')";
			$res	=	mysql_query($sql);
			$frmID = mysql_insert_id();
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			$sql	=	"update schedule_patient set patient_id = '" . $bc_patient_id . "', comments = '" . $bc_event_description . "',  = '" . $bc_ . "' where id=$frmID";
			$res	=	mysql_query($sql);
			if ($res) {
				$sucMessage = "Record Successfully updated.";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if

	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit
$sql	=	"select * from schedule_patient where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_patient_id	=	$row["patient_id"];
		$bc_event_description	=	$row["comments"];
		$bc_	=	$row[""];
	} // end if row
	$action = "edit";
} // end if 


$meta_title	= "Schedule Patient";
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
			$("#patient_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>get_patient_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
					if(data[0]){
					$("#patient_id").attr("value", data[0]);
					$('#patient_id').css('color', '#000');
					}
					if(data[2]){
					$("#pt_address1").attr("value", jQuery.trim(data[2]));
					$('#pt_address1').attr('readonly', 'readonly');
					$('#patient_id').css('color', '#000');
					}
					if(data[3]){
					$("#pt_city").attr("value", jQuery.trim(data[3]));
					$('#pt_city').attr('readonly', 'readonly');
					$('#patient_id').css('color', '#000');
					}
					if(data[4]){
					$("#pt_zip").attr("value", jQuery.trim(data[4]));
					$('#pt_zip').attr('readonly', 'readonly');
					$('#patient_id').css('color', '#000');
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
//	$('#trow'+id).remove();
	$('#trow'+id).css("display","none");
	$('#del'+id).val('1');
	
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
	var limitImage = 15;
	if(id!=limitImage){  
	var next_row 	= id+1;
	var new_url_feild = '<div class="ev_fltlft" style="width:50%; padding:5px 0" id="showimg'+next_row+'"><input type="file" name="images[]" /><img style="padding: 3px 5px 0 0;cursor:pointer" src="images/icon_delete2.gif" align="left" onclick="remove_image('+next_row+')"></div>';
	$('#add_more_image_area').append(new_url_feild);	
	$('#add_more_image_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_image('+next_row+')" />');
	}
	else{
	alert("You can not upload more then "+limitImage+" images");
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
	var limitVideos = 5;
	if(id!=limitVideos){  
	var next_row 	= id+1;
	var new_url_feild = '<div id="showvid'+next_row+'"><div style="float:left; width:380px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Video Name:</div><input type="text" name="video_name[]" value="Enter the name of your video" id="video_name'+next_row+'" onFocus="removeText(this.value,\'Enter the name of your video\',\'video_name'+next_row+'\');" onBlur="returnText(\'Enter the name of your video\',\'video_name'+next_row+'\');" class="new_input" style="width:350px;"><img style="padding: 3px 0 0 0;cursor:pointer" src="images/icon_delete2.gif" align="right" onclick="remove_video('+next_row+')"></div><div style="float:left; width:454px; margin-right:20px"><div id="head" style="padding:16px 0 12px; font-size:22px">Copy and Paste the Video Embed Code Here:</div><textarea class="new_input" name="video_embed[]" style="width:466px; height:130px;"></textarea></div><div class="clr"></div></div></div>';
	$('#add_more_video_area').append(new_url_feild);	
	$('#add_more_video_btn').html('<img src="images/add_more.png" style="cursor:pointer" id="" onclick="add_more_video('+next_row+')" />');
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
	var buyer_service_free_after_percent	=	mainPrice*<?php echo TICKET_FEE_PERSENT; ?>/100+<?php echo TICKET_FEE_PLUS; ?>;
	var buyer_service_free_after_percent	=	buyer_service_free_after_percent*buyer_service_free/100;
	var prometer_service_free_after_percent	=	mainPrice*<?php echo TICKET_FEE_PERSENT; ?>/100+<?php echo TICKET_FEE_PLUS; ?>;
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
	
	
	var ticketPrices = new Array();
	var i=0;
		jQuery.each(jQuery("input[name='ticketPrices[]']"), function() {
	i++;
	var a = jQuery(this).attr("id");
	var b = a.split('costum_price');
	var ids = b[1];
	var price = Number(jQuery(this).val());
	price = price.toFixed(2);
	if(price!=''){
		var buyer_service_free_after_percent	=	price*<?php echo TICKET_FEE_PERSENT; ?>/100+<?php echo TICKET_FEE_PLUS; ?>;
		var buyer_service_free_after_percent	=	buyer_service_free_after_percent*buyer_service_free/100;
		var prometer_service_free_after_percent	=	price*<?php echo TICKET_FEE_PERSENT; ?>/100+<?php echo TICKET_FEE_PLUS; ?>;
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
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Schedule Patient</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
     
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
        
        <div align="right" style="padding:5px 15px 9px 0; font-size:16px;">
		
				<strong>Your event is currently	</strong>
			<?php
				if($disabled!='yes'){?>
					<select name="event_status" <?php echo $disabled; ?>>
						<option value="1" <?php if($bc_event_status==1){ echo 'selected="selected"'; } ?>>Active</option>
						<option value="0" <?php if($bc_event_status==0){ echo 'selected="selected"'; } ?>>Inactive</option>
					</select>
			<?php
				}
				else{
					if($bc_event_status==1 || $is_private){?>
						<strong>Active</strong>
						<input type="hidden"  value="<?php echo $bc_event_status; ?>" id="event_status" name="event_status"/>
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
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">
                <h3>PATIENT SCHEDULER</span></h3>
                <div id="box" class="box">
                  <!--
<div id="head">Event Title</div>
                  <div class="ev_title">
                    <input type="text" name="eventname" value="<?php if ($bc_event_name){echo $bc_event_name;} else{ echo "Enter only the name of your event";} ?>" id="event" onFocus="removeText(this.value,'Enter only the name of your event','event');" onBlur="returnText('Enter only the name of your event','event');">
                  </div>
-->
                 
                  <div id="head">Patient Name</div>
                  <input type="text" name="patient_name" id="patient_name" class="new_input" value="<?php if ($bc_row){ echo $bc_row; }else{ echo "Start Typing Patient Name"; } ?>" onFocus="removeText(this.value,'Start Typing Patient Name','patient_name');" onBlur="returnText('Start Typing Patient Name','patient_name');" style="margin-bottom:2px; width:200px" />
                  <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $bc_arr_patient_id; ?>" />
                  &nbsp;&nbsp;
                  <input type="text" name="address1" disabled="disabled" id="pt_address1" class="new_input" value="<?php if ($bc_patient_address){ echo $bc_patient_address; } else{ echo 'Address'; } ?>"  onFocus="removeText(this.value,'Address','pt_address1');" onBlur="returnText('Address','ev_address1');" style="width:200px">
                  &nbsp;&nbsp;
                  <input type="text" name="city" disabled="disabled" id="pt_city" class="new_input" value="<?php if ($bc_patient_city){ echo $bc_patient_city; } else{ echo 'City'; } ?>"  onFocus="removeText(this.value,'City','pt_city');" onBlur="returnText('City','pt_city');" style="width:200px">
                  &nbsp;&nbsp;
                  <input type="text" name="zip" id="pt_zip" disabled="disabled" class="new_input" value="<?php if ($bc_patient_zip){ echo $bc_patient_zip; } else{ echo 'Zip / Postal Code'; } ?>"  onFocus="removeText(this.value,'Zip / Postal Code','pt_zip');" onBlur="returnText('Zip / Postal Code','pt_zip');" style="width:190px">
                  <br>
                  <a href="javascript:void(0)" style="color:#0066FF; text-decoration:underline" onClick="windowOpener(525,645,'Add New Patient','create_patient.php')"> New Patient? Add it here </a>
                  <div class="clr"></div>
              
                  <div id="z_listing_event_form_occurrences" class="z-group z-panel-occurrences">
                    <div class="ev_fltlft" style="width:272px">
                      <div id="head"> Select Appointment Time(s)
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
<!--                        <input type="text" class="new_input"  style="width:150px; color:#555555" id="event_end_time" value="<?php // if($bc_event_end_time){echo $bc_event_end_time; } else{ echo "Add End Time here"; } ?>" onFocus="removeText(this.value,'Add End Time here','event_end_time');" onBlur="returnText('Add End Time here','event_end_time');" name="event_end_time" />-->
                         <label for="z_event_end_time" class="z-inline">End Time (optional)</label>
						<input id="z_event_end_time" class="z-input-time" type="text" name="end_time"/>
						<select id="z_event_end_am_pm" name="end_time_am_or_pm">
							<option value="0">AM</option>
							<option selected="selected" value="1">PM</option>
						</select>
                      </div>
                    </div>
                    <div class="ev_fltrght">
                      <div id="head">Select Appointment Date(s)</div>
                      <a name="z_repeat_pattern_list"></a>
                      <ul class="z-tabs" style="display:">
                        <li class="z-current"><a href="#">Calendar View</a></li>
                        <li><a href="#">Advanced View</a></li>
                      </ul>
                      <div id="z_tab_calender_view" class="z-calendar-view z-tab-content" style="display: block">
                        <label><sup>&#42;</sup> Click one or more dates for your appointment or appointment series on the calendars below.</label>
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
                          <p><strong>Appointment Recurrence</strong></p>
                          <p>Choose how often this appointment repeats:</p>
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
				 
if (isset($_POST['occurrences']) && $_POST['occurrences'] != ''){

//print_r($_POST['occurrences']);

	$occurrences	= $_POST['occurrences'];
	$date			= array();
	$start_time		= array();
	$start_am_pm	= array();
	$end_time		= array();
	$end_am_pm		= array();
	$unique_id		= 0;

	foreach($occurrences as $v){
		$date[]			= $v['date'];
		$start_time[]	= $v['start_time'];
		$start_am_pm[]	= $v['start_am_pm'];  // AM = 0, PM = 1
		$end_time[]		= $v['end_time'];
		$end_am_pm[]	= $v['end_am_pm'];	// AM = 0, PM = 1
	}

	$totalOccurrences = count($date);
	for($i=0;$i<count($date);$i++){
	$unique_id++;
	if($endTime[$i] == '' || $endTime[$i] == '00:00:00')
		$end_time = '00:00:00';
	else
		$end_time = date("H:i:s", strtotime($end_time[$i]));

	?>
	 <tr id="z_occurrence_row_<?php echo $unique_id ?>">
	 	<td width="34%" class="z-occurrence-date-cell z-col-1" style="background-color: #d1e5c0;">
			<input type="hidden" class="z-occurrence-id" name="occurrences[<?php echo $unique_id ?>][occurrence_id]" value="" />
			<input type="hidden" class="z-occurrence-date" name="occurrences[<?php echo $unique_id ?>][date]" value="<?php echo date('m/d/Y', strtotime($date[$i])); ?>" />
			<?php echo date('D, d M, Y', strtotime($date[$i])); ?>
		</td>
        <td width="30%" class="z-time-cell z-col-3" style="background-color: #d1e5c0;">
			<div class="z-occurrence-start-time-layer" >
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
			<a class="z-end-time-toggle">[+] end time</a>
		</td>
		<td width="10%" class="z-remove-cell z-col-4" style="background-color: #d1e5c0;">
			<a class="z-occurrence-remove"><img src="<?php echo ABSOLUTE_PATH; ?>images/icon_remove.gif" alt="remove" title="remove"></a>
		</td>
	</tr>
    <?php
	}
}
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
                        <div id="z_total_occurrences_block"> Total Occurrences: <span id="z_total_occurrences">
                          <?php if ($totalOccurrences){ echo $totalOccurrences; } else{ echo 0;} ?>
                          </span> </div>
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
               
                <div id="head">Comments</div>
                  <div>
                    <textarea name="event_description" id="event_description" class="bc_input" style="width:825px; height:250px"><?php echo $bc_event_description; ?></textarea>
                  </div>
               
               
                </div>
                
                
         <!--        Removed Step 3 -- TK Walker  -->
                
                
               
                
				
			
				
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
          <?php
		 if($bc_event_type==1 && !$_GET['id'] || $_GET['r']=='py'){
		 ?>
         <!-- <img src="<?php echo IMAGE_PATH; ?>check_out_new.png" name="create" value="Create Event" style="cursor:pointer" onClick="save();"  align="right" />-->
          <?php
		   }
	   else{?>
	   		 <input type="hidden" name="publish_event" value="1" />
          <img src="<?php echo IMAGE_PATH; ?>publish_new.png" name="create" value="Create Event" style="cursor:pointer" onClick="save();"  align="right" />
          <?php } 
		
		
		
		 
		
		
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