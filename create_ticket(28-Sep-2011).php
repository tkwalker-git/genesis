<?php
include_once('admin/database.php'); 
include_once('site_functions.php'); 

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$add_id	=	$_GET['id'];
if(!$add_id){
echo "<script>window.location.href='create_event.php';</script>";
}
$r = mysql_query("select * from `events` where `id`='$add_id'");
if(mysql_num_rows($r)==0){
echo "<script>window.location.href='myeventwall.php';</script>";
}


$res = mysql_query("select * from `event_ticket` where `event_id`='$add_id'");
if(mysql_num_rows($res) > 0 && $_GET['save']!=1){
echo "<span style='font-family:Arial, Helvetica, sans-serif;'><span style='font-size:18px;'>Error:</span> <span style='font-size:14px;'>Please try later</span></span>";
exit();
}

$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

$already_uploaded = 0;

if (isset($_POST["submit"])) {
	$save_price='';

	$name						=	$_POST["name"];
	$mainTitle					=	$_POST["mainTitle"];
	$mainPrice					=	$_POST["mainPrice"];
	$event_id					=	$_POST["event_id"];
	$service_fee_type			=	$_POST["service_fee_type"];
	$service_fee				=	$_POST["service_fee"];
	$bc_prometer_service_free	=	$_POST["prometer_service_free"];
	$bc_buyer_service_free		=	$_POST["buyer_service_free"];
	if($bc_prometer_service_free!='' && $bc_buyer_service_free==''){
	$bc_buyer_service_free		=	100-$bc_prometer_service_free;
	}elseif($bc_buyer_service_free!='' && $bc_prometer_service_free==''){
	$bc_prometer_service_free		=	100-$bc_buyer_service_free;
	}
	
	if($mainPrice=='costum_price'){
	$mainPrice					=	$_POST['costum_price'];
	}else{
	$mainPrice					=	$_POST["mainPrice"];
	}
	
	$quantity_available			=	$_POST['quantity_available'];
	
	if($_POST['start_sales']=='specify_day'){
	$start_sales_date			=	date('Y-m-d', strtotime($_POST['start_sales_date']));
	$startTime					=	$_POST['start_sales_hrs'].":".$_POST['start_sales_min']." ".$_POST['start_sales_ampm'];
	$start_sales_time			=	date('H:i', strtotime($startTime));
	}elseif($_POST['start_sales']=='time_before'){
	$start_sales_before_days	=	$_POST['start_sales_before_days'];
	$start_sales_before_hrs		=	$_POST['start_sales_before_hrs'];
	$start_sales_before_min		=	$_POST['start_sales_before_min'];
	}
	else{
	$start_sales_date			=	date('Y-m-d');
	$start_sales_time			=	date('H:i');
	}
	
	
	if($_POST['end_sales']=='specify_day2'){
	$end_sales_date				=	date('Y-m-d', strtotime($_POST['end_sales_date']));
	$endTime					=	$_POST['end_sales_hrs'].":".$_POST['end_sales_min']." ".$_POST['end_sales_ampm'];
	$end_sales_time				= 	date('H:i', strtotime($endTime));
	}elseif($_POST['end_sales']='time_before2'){
	$end_sales_before_days		=	$_POST['end_sales_before_days'];
	$end_sales_before_hrs		=	$_POST['end_sales_before_hrs'];
	$end_sales_before_min		=	$_POST['end_sales_before_min'];
	}
	
	$min_tickets				=	$_POST['min_tickets'];
	$max_tickets				=	$_POST['max_tickets'];
	$ticket_description			=	$_POST['ticket_description'];
	if($ticket_description == 'Access for one person to the launch celebration for the new website Eventgrabber'){
	$ticket_description = '';
	}
	
	$sucMessage = "";
	
	$errors = array();
	
	
	if ( trim($name) == '' )
		$errors[] = 'Please enter Ticket Name';
		
	if ( trim($mainTitle) == '' )
		$errors[] = 'Please enter Title';

	if	( trim($mainPrice) == '' )
		$errors[] = 'Please select a Price option';
		
		
	if ( trim($quantity_available) == '' )
		$errors[] = 'Please enter Quantity Available';
		
	if ( trim($service_fee_type)!='' && $service_fee=='')
		$errors[] = 'Please enter Service Free';
		
	if(	trim($bc_prometer_service_free)=='' && trim($bc_buyer_service_free)=='')
		$errors[] = 'Please enter Split Percent';
	if(($bc_prometer_service_free+$bc_buyer_service_free)!=100)
		$errors[] = 'Please enter correct Split Percentage';
	if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	if (!count($errors)) {
 	$sql = "INSERT INTO `event_ticket` (`id`, `name`, `quantity_available`, `start_sales_date`, `start_sales_time`, `start_sales_before_days`, `start_sales_before_hours`, `start_sales_before_minutes`, `end_sales_date`, `end_sales_time`, `end_sales_before_days`, `end_sales_before_hours`, `end_sales_before_minutes`, `min_tickets_order`, `max_tickets_order`, `ticket_description`, `service_fee_type`, `service_fee`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$name', '$quantity_available', '$start_sales_date', '$start_sales_time', '$start_sales_before_days', '$start_sales_before_hrs', '$start_sales_before_min', '$end_sales_date', '$end_sales_time', '$end_sales_before_days', '$end_sales_before_hrs', '$end_sales_before_min', '$min_tickets', '$max_tickets', '$ticket_description', '$service_fee_type', '$service_fee', '$bc_buyer_service_free', '$bc_prometer_service_free', '$event_id')";
	$res = mysql_query($sql);

	if($res){
	$ticket_id = mysql_insert_id();

	$qry2 = "INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id')";
	mysql_query($qry2);

//	if ( is_array($_POST['mtitle']) ) {
//		for($i=0;$i< count($_POST['mtitle']); $i++) {
//		$mtitle =	$_POST['mtitle'][$i];	
//		$mprice	=	$_POST['mprice_'.$i];
//		
//			if($mprice=='costum_price'){
//				$save_price		=	$_POST['mcostum_price_'.$i];
//				}
//			else{
//				$save_price		=	trim($_POST["mprice_".$i]);
//			}
//			if($mtitle!=''){
//				$qry = "INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$mtitle', '$save_price', '$ticket_id')";
//				mysql_query($qry);
//				}			
//				}
//				}





	if ( is_array($_POST['title']) ) {
		for($i=0;$i< count($_POST['title']); $i++) {
		$title					=	$_POST['title'][$i];	
		$price					=	$_POST['price'][$i];


		if($price=='costum_price'){
		$price = $_POST['costum'][$i];
		}

		if($title!='' && $price!=''){
	
		$qry	=	"INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$title', '$price', '$ticket_id')";
	
		mysql_query($qry);
			}
				}
				}
				
				
				
	echo '<script type="text/javascript">window.location.href="create_ticket.php?id='.$event_id.'&save=1"</script>';
	}
	else{
	$sucMessage = "Error: Please try Later";
	}
	}
}
include_once('includes/header.php');
?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".hint").fancybox({
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoDimensions'    : false,
		'width'				: 400,
		'type'				: 'iframe'
	});
	});
	
	function show(){
	document.getElementById('service_feeDiv').style.display='block';
	}
	
</script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>/js/jquery-ui_1.8.7.js"></script>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/redmond/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.multidatespicker.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/ev_functions.js"></script>
<script type="text/javascript">
	$(function() {
		// multi-months
		$('#multi-months').multiDatesPicker({
			numberOfMonths: 3,
			<?php if ( trim($dates) != '' ) { ?>
				addDates: [<?php echo $dates; ?>], 
			<?php } ?>	
			//var myArray = new Array();
			//addDates: [ '05/01/2011', '05/14/2011'],
			onSelect: function(dateText, inst) {
				var dates = $('#multi-months').multiDatesPicker('getDates');
				document.getElementById("selected_dates").value = dates;
							
			}
		});
		
		$('#multi-months').datepicker('setDate', new Date(<?php echo $first_date; ?>));
	});
	
	 function dynamic_Select(ajax_page, category_id,sub_category)      
	 {  
		 $.ajax({  
			type: "GET",  
			url: ajax_page,  
			data: "cat=" + category_id + "&subcat=" + sub_category,  
			//data: "subcat=" + subcat_id,
			dataType: "text/html",  
			success: function(html){       $("#subcategory_id").html(html);     }  
	   	}); 
	  }  
	
	$(document).ready(function() {
		$("#venue_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>admin/get_venue_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
					$("#venue_id").attr("value", data[0]);
				}
			}).setOptions({
				max: '100%'
			});
});


function checkCategory(val)
{
	if ( val == -1 ) {
		alert("You can't select Parent Category");
		document.getElementById("category_id").selectedIndex=0;
	}	
}
	

	
function add_newRow(id){
	var next_row = id+1;
	var id2 = id;
	var new_url_feild = '<div id="addrow'+next_row+'" style=" margin-bottom: 10px; background:#FBFBFB; border:#F2F1F1 solid 1px;width: 426px;"><div class="evField3">Title:<b class="clr">*</b></div><div class="evLabal3" style="width:334px"><input type="text" style=" width:280px" value="" class="inp2" name="title[]" id="title'+next_row+'" /></div><div style="float:left"><img src="images/delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow('+next_row+',\'no\');"></div><div class="clr"></div><div class="evField3">Price:<b class="clr">*</b></div><div class="evLabal3" style="width:334px;"><input type="checkbox" onClick="fldDisabledFalse('+next_row+')" id="fld_'+next_row+'" name="price[]" value="costum_price"> <input type="text" class="inp2" name="costum[]" value="" id="costum_price'+next_row+'" onFocus="slct('+next_row+');" onkeypress="return isNumberKey(event)" style="width:60px;"><input type="hidden" name="ac[]" value="insert" /><input type="hidden" name="event_ticket_price_id[]"><input type="hidden" name="del[]" value="" id="del_'+next_row+'"><input type="checkbox" id="free'+next_row+'" onClick="rmv('+next_row+')" name="price[]" value="free"> Free </div><div class="clr"></div></div>';
	
	
	$('#add_url_ist').append(new_url_feild);
	$('#add_more_btn_1').html('<span style="cursor:pointer; font-size:13px; color:#0033CC" onclick="add_newRow('+next_row+');">&nbsp;&nbsp;Add More</span>');
	  }
	  
	tinyMCE.init({
	mode : "exact",
	elements : "event_description",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,forecolor,backcolor,bullist,numlist,outdent,indent,blockquote,anchor,cleanup",
	theme_advanced_buttons2 : "cut,copy,paste,styleselect,formatselect,fontselect,fontsizeselect,hr,code,image",
	theme_advanced_font_sizes: "10px,11px,12px,13px,14px,15px,16px,17,18px,19px,20px,22px,24px,26px,28px,30px,36px",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	remove_script_host : false,
    convert_urls : false,
	content_css : "site_styles.css?1",
	plugins : 'inlinepopups,imagemanager',
});

tinyMCE.init({
	mode : "exact",
	elements : "organization_description",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,justifyleft,justifycenter,justifyright, fontsizeselect,forecolor,code",
	theme_advanced_buttons2 : "",
	theme_advanced_font_sizes: "10px,11px,12px,13px,14px,15px,16px,17,18px,19px,20px,22px,24px,26px,28px,30px,36px",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	remove_script_host : false,
    convert_urls : false,
	content_css : "site_styles.css?1",
	plugins : 'inlinepopups,imagemanager',
});

	

</script>
<style>

.addEInput
{
	width:225px!important;
	height:30px!important;
}


table.ui-datepicker-calendar {border-collapse: separate;}
.ui-datepicker-calendar td {border: 1px solid transparent;}

.hasDatepicker .ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
	background: #743620 none;
	color: white;
}

#ui-datepicker-div {display:none;}

</style>
<div style="padding-top:20px;">
  <?php
  if(isset($_REQUEST['save'])){
  $event_id = $_GET['id'];
	
		$event_url		= getEventURL($event_id);
	
		?>
  <div class="width96" style="padding:23px 0 250px 0 "> <strong style="font-size:16px;">Your ticket created successfully. See your event listing <a href="<?php echo $event_url;?>" style="text-decoration:underline; color:#0066FF">here</a>.</strong><br>
    <br>
  </div>
  <?php }else{ ?>
  <div class="creatAnEvent">
    <div class="width96">
      <div class="creatAnEventMdl">Create Ticket</div>
    </div>
  </div>
  <!-- /creatAnEvent -->
  <?php
  if($_GET['event']==1){?>
  <div style="width:960px; margin:auto; padding:20px 0 0 0; font-size:16px; line-height:25px"><b>Your event saved successfully, Now Create Ticket</b></div>
  <?php } ?>
  <div class="width96" style="padding-top:23px">
    <form id="" name="" method="post" action="">
      <div class="success"><?php echo $sucMessage; ?></div>
      <div class="error"><?php echo $err; ?></div>
      <div class="evField">Ticket Name:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="text" maxlength="100" value="<?php echo $_REQUEST['name'];?>" name="name" style="width:410px;" class="inp2">
        <br>
        <small>Examples: Member, Non-member, Student, Early Bird</small> </div>
      <div class="clr"></div>
      <div class="evField">Title:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="text" style=" width:280px" value="" class="inp2" name="mainTitle" id="mainTitle" />
      </div>
      <div class="clr"></div>
      <div class="evField">Price:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="checkbox" onClick="fldDisabledFalse('1st')" id="fld_1st" <?php if (is_numeric(trim($mainPrice))){ echo 'checked="checked"'; }?> value="costum_price" name="mainPrice">
        <input type="text" class="inp2" name="costum_price" value="<?php if (is_numeric(trim($mainPrice))){ echo trim($mainPrice); }?>" id="costum_price1st" onkeypress="return isNumberKey(event)" style="width:60px;" onfocus="slct('1st')">
        <input type="checkbox" id="free1st" <?php if (!is_numeric(trim($mainPrice)) && $mainPrice!=''){ echo 'checked="checked"'; }?>  onClick="rmv('1st')" name="mainPrice" value="free">
        Free </div>
      <div class="clr"></div>
      <?php if ( count( $errors) > 0 ) {
	  if ( is_array($_POST['hdErr']) ) {
		$nowStartTo = count($_POST['hdErr']);
		for($i=0;$i< count($_POST['hdErr']); $i++) {
		
		if($_POST['hdErr'][$i]!=''){
		$next = $i+1;
		$mtitle =	$_POST['mtitle'][$i];	
		$mprice	=	$_POST['mprice_'.$i];
		
		
		if($mtitle!='' || $mprice!=''){
		?>
      <div id="addrow<?php echo $next; ?>" style=" margin-bottom: 10px; background:<?php
		
		if($mtitle!='' && $mprice=='' || $mtitle=='' && $mprice!=''){
		echo "#FFFFE3;border:#ECC6B3";
		}
		else{
		echo "#FBFBFB;border:#F2F1F1";
		}
		?> solid 1px;width: 426px;">
        <?php
		if($mtitle!='' && $mprice==''){
		echo  '<div class="err2"><ul><li>Please select a Price option</li></ul></div>';
		}
		elseif($mtitle=='' && $mprice!=''){
		echo '<div class="err2"><ul><li>Please enter Title</li></ul></div>';
		}
		?>
        <div class="evField">Title:<b class="clr">*</b></div>
        <input type="hidden" name="hdErr[]" value="1" id="hdd<?php echo $next; ?>" />
        <div class="evLabal" style="width:306px">
          <input type="text" style="width:280px" value="<?php echo $mtitle; ?>" class="inp2" name="mtitle[]" id="mtitle<?php echo $next; ?>" />
        </div>
        <div style="float:left"><img src="<?=IMAGE_PATH;?>delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow(<?php echo $next; ?>);"></div>
        <div class="clr"></div>
        <div class="evField">Price:<b class="clr">*</b></div>
        <div class="evLabal" style="width:334px;">
          <label>
          <input type="radio" id="cstm<?php echo $next; ?>" name="mprice_<?php echo $i; ?>" value="costum_price" <?php if ($mprice=='costum_price'){ echo "checked='checked'"; } ?> onclick="prc(this.value,<?php echo $i; ?>);">
          <input type="text" class="inp2" name="mcostum_price_<?php echo $i; ?>" value="<?php if($mprice=='costum_price'){	echo $_POST['mcostum_price_'.$i];} ?>" id="costum_price<?php echo $i; ?>" onkeypress="return isNumberKey(event)" style="width:60px;">
          </label>
          <label>
          <input type="radio" id="free<?php echo $next; ?>" name="mprice_<?php echo $i; ?>" value="free" <?php if ($mprice=='free'){ echo "checked='checked'"; } ?> onChange="prc(this.value,<?php echo $i; ?>);">
          Free </label>
        </div>
        <div class="clr"></div>
      </div>
      <?php	}	}	}	}	} ?>
      <div class="evField">Additional Packages:<br />
        <span id="add_more_btn_1"><span style="cursor:pointer; font-size:13px; color:#0033CC" onclick="add_newRow(<?php if ($nowStartTo){ echo $nowStartTo;} else{ echo '0'; } ?>);"><strong>Add More</strong></span></span> </div>
      <div class="evLabal">
        <div id="add_url_ist"></div>
      </div>
      <div class="clear"></div>
      <div class="evField">Quantity Available:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="text" class="inp2" name="quantity_available" value="<?php echo $_REQUEST['quantity_available']; ?>" style="width:60px;">
      </div>
      <div class="clr"></div>
	  <div class="evField" style="padding-top:23px;">Split Percent:<b class="clr">*</b></div>
	  <div class="evLabal"><small class="clr">Event Grabber Service Fee (<?php echo getSingleColumn('event_fee',"select `event_fee` from default_settings"); ?>%)</small><br /> Prometer <input type="text" onkeypress="return isNumberKey(event)" class="inp2" style="width:40px;" name="prometer_service_free" value="<?php echo $bc_prometer_service_free; ?>" /> % &nbsp; Buyer <input type="text" name="buyer_service_free" value="<?php echo $bc_buyer_service_free; ?>" onkeypress="return isNumberKey(event)" class="inp2" style="width:40px;" /> %</div>
      
       <div class="clr"><br /> &nbsp;</div>
      <div style="background:url(images/blc_hd_bg.jpg) no-repeat; width:930px; height:28px;">
        <div class="evField" style="padding:6px; color:#FFFFFF">Advanced Options:</div>
        <div class="evLabal" style="padding: 5px;"><span style="color:#fff; text-decoration:underline; cursor:pointer" id="st" onclick="advOptHidSho();">Hide</span></div>
        <div class="clr"></div>
      </div>
      <div id="advance_options">
        <div class="clr"></div>
        <div class="evField">Start Sales</div>
        <div class="evLabal">
          <label for="specify_day">
          <input type="radio" id="specify_day" value="specify_day" <?php if ($_REQUEST['start_sales']=='specify_day'){ echo "checked='checked'";} ?> name="start_sales" onChange="timeBeforeEventStart(this.value);">
          Specify day
          <input type="text" class="inp2" name="start_sales_date" <?php if ($_REQUEST['start_sales']=='time_before'){ echo "disabled='disabled'";} ?> value="<?php echo $_REQUEST['start_sales_date']; ?>"  readonly="" id="start_sales_date" style="width:76px; color:#000000; cursor:pointer">
          <select class="inp3" name="start_sales_hrs" id="start_sales_hrs"
		<?php
		 if ($_REQUEST['start_sales']=='time_before'){
		   echo 'disabled="disabled"';
		   }
		   ?>
		   >
            <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'"';
		   if($_REQUEST['start_sales_hrs']	==	$i){
		   echo 'selected="selected"';
		   }
		  
		   echo '>'.$i.'</option>';
		   }
		   ?>
          </select>
          :
          <select class="inp3" name="start_sales_min" id="start_sales_min"
		<?php
		 if ($_REQUEST['start_sales']=='time_before'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
            <?php
		   for ($i=00;$i<=59;$i++){
		   if($i<10){
		   $i = "0".$i;
		   }
		   echo '<option value="'.$i.'"';
		   if($_REQUEST['start_sales_min']	==	$i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
          </select>
          <select class="inp3" name="start_sales_ampm" id="start_sales_ampm"
		<?php
		 if ($_REQUEST['start_sales']=='time_before'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
            <option value="am" <?php if ($_REQUEST['start_sales_ampm']=='am'){ echo 'selected="selected"'; } ?> >AM</option>
            <option value="pm" <?php if ($_REQUEST['start_sales_ampm']=='pm'){ echo 'selected="selected"'; } ?> >PM</option>
          </select>
          </label>
          <label for="time_before">
          <input type="radio" id="time_before" value="time_before" name="start_sales" <?php if ($_REQUEST['start_sales'] == 'time_before'){ echo 'checked="checked"';} ?> onChange="timeBeforeEventStart(this.value);">
          Time before event starts
          <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php echo $_REQUEST['start_sales_before_days']; ?>" name="start_sales_before_days" id="start_sales_before_days">
          Days &nbsp;
          <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" name="start_sales_before_hrs" value="<?php echo $_REQUEST['start_sales_before_hrs']; ?>" id="start_sales_before_hrs">
          Hours &nbsp;
          <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" name="start_sales_before_min" value="<?php echo $_REQUEST['start_sales_before_min']; ?>" id="start_sales_before_min">
          Minutes &nbsp;</label>
        </div>
        <div class="clr"></div>
        <div class="evField">End Sales</div>
        <div class="evLabal">
          <label for="specify_day2">
          <input type="radio" id="specify_day2" name="end_sales" <?php if ($_REQUEST['end_sales']=='specify_day2'){ echo 'checked="checked"'; }?> value="specify_day2" onChange="timeBeforeEventEnd(this.value);">
          Specify day
          <input type="text" class="inp2"
		<?php
		 if ($_REQUEST['end_sales']=='time_before2'){
		   echo 'disabled="disabled"';
		   }
		   ?> readonly="" name="end_sales_date" value="<?php echo $_REQUEST['end_sales_date']; ?>" id="end_sales_date" style="width:76px; cursor:pointer">
          <select class="inp3" name="end_sales_hrs" id="end_sales_hrs"
		<?php
		 if ($_REQUEST['end_sales']=='time_before2'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
            <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'"';
		   if($_REQUEST['end_sales_hrs']	==	$i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
          </select>
          :
          <select class="inp3" name="end_sales_min" id="end_sales_min"
		<?php
		 if ($_REQUEST['end_sales']=='time_before2'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
            <?php
		   for ($i=00;$i<=59;$i++){
		   if($i<10){
		   $i = "0".$i;
		   }
		   echo '<option value="'.$i.'"';
		   if($_REQUEST['end_sales_min']	==	$i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
          </select>
          <select class="inp3" name="end_sales_ampm" id="end_sales_ampm"
		<?php
		 if ($_REQUEST['end_sales']=='time_before2'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
            <option value="am" <?php if ($_REQUEST['end_sales_ampm']=='am'){ echo 'selected="selected"'; } ?> >AM</option>
            <option value="pm" <?php if ($_REQUEST['end_sales_ampm']=='pm'){ echo 'selected="selected"'; } ?> >PM</option>
          </select>
          </label>
          <label for="time_before2">
          <input type="radio" id="time_before2" name="end_sales" <?php if ($_REQUEST['end_sales'] == 'time_before2'){ echo 'checked="checked"';} ?> value="time_before2" onChange="timeBeforeEventEnd(this.value);">
          Time before event ends
          <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php echo $_REQUEST['end_sales_before_days']; ?>"  name="end_sales_before_days" id="end_sales_before_days">
          Days &nbsp;
          <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php echo $_REQUEST['end_sales_before_hrs']; ?>"  name="end_sales_before_hrs" id="end_sales_before_hrs">
          Hours &nbsp;
          <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php echo $_REQUEST['end_sales_before_min']; ?>"  name="end_sales_before_min" id="end_sales_before_min">
          Minutes &nbsp;</label>
        </div>
        <div class="clr"></div>
        <div class="evField">Min, Tickets Order</div>
        <div class="evLabal">
          <input type="text" class="inp2" style="width:60px;" name="min_tickets" value="<?php echo $_REQUEST['min_tickets'];?>">
          &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a> </div>
        <div class="clr"></div>
        <div class="evField">Max, Tickets Order</div>
        <div class="evLabal">
          <input type="text" class="inp2" style="width:60px;" name="max_tickets" value="<?php echo $_REQUEST['max_tickets']; ?>">
          &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a> </div>
        <div class="clr"></div>
        <div class="evField">Ticket Description</div>
        <div class="evLabal">
          <textarea id="ticket_description" name="ticket_description" class="inp" style="height:35px; width:410px; padding:3px; margin:0" onFocus="removeText(this.value,'Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');" onBlur="returnText('Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');"><?php if ($_REQUEST['ticket_description']){ echo $_REQUEST['ticket_description'];} else{ echo "Access for one person to the launch celebration for the new website Eventgrabber"; } ?>
</textarea>
          <br>
          <!--  <input type="radio">
        Auto Hide Description &nbsp; <a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a>-->
        </div>
        <div class="clr"></div>
        <div class="evField">Service Fee</div>
        <div class="evLabal">
          <label for="add">
          <input type="radio" name="service_fee_type" onclick="show('service_fee');" class="service_fee" <?php if ($_REQUEST['service_fee_type']==1){ echo 'checked="checked"';} ?> id="add" value="1">
          ADD fees on top of total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a></label>
          <label for="include">
          <input type="radio" name="service_fee_type" onclick="show('service_fee');" class="service_fee" <?php if ($_REQUEST['service_fee_type']==2){ echo 'checked="checked"';} ?> value="2" id="include">
          INCLUDE fees into total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a></label>
          <label for="include_and_add">
          <input type="radio" name="service_fee_type" onclick="show('service_fee');" class="service_fee" id="include_and_add" <?php if ($_REQUEST['service_fee_type']==3){ echo 'checked="checked"';} ?>  value="3">
          INCLUDE credit card processing fee in the total ticket price and ADD the Eventgrabber fee on top of the total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a></label>
          <div id="service_feeDiv" style="display:<?php if ($_POST['service_fee_type']==''){ echo 'none';} else{ echo 'block'; }?>"><b class="clr">*</b>
            <input type="text" style="width:100px" onkeypress="return isNumberKey(event)"  class="inp2" value="<?php if ($_POST['service_fee']==''){ echo 'Enter Service Fee'; } else{ echo $_POST['service_fee']; } ?>" id="service_fee" name="service_fee" onFocus="removeText(this.value,'Enter Service Fee','service_fee');" onBlur="returnText('Enter Service Fee','service_fee');" />
          </div>
        </div>
      </div>
      <div class="clr"></div>
      <div class="evField"></div>
      <div class="evLabal">
        <input type="image"  id="submit" src="<?php echo IMAGE_PATH; ?>submit_contact.jpg" name="submit" value="Submit">
        <input type="hidden" name="event_id" value="<?php echo $_REQUEST['id']; ?>">
        <input type="hidden" name="submit" value="Submit">
      </div>
      <div class="clr"></div>
      <div style="display: none;" id="errmsg" class="errorBox">
        <div id="myspan" class="erroeMessage"><img class="vAlign" alt="" src="<?php echo IMAGE_PATH; ?>error.jpg"> Error Message will gose here</div>
        <div class="clr"></div>
      </div>
      <div class="clr"></div>
    </form>
  </div>
</div>
<?php } ?>
<?php include_once('includes/footer.php');?>
