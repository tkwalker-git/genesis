<?php
include_once('admin/database.php'); 
include_once('site_functions.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";



//$res = mysql_query("select * from `event_ticket` where `event_id`='$add_id'");
//if(mysql_num_rows($res) > 0 && $_GET['save']!=1){
//echo "<span style='font-family:Arial, Helvetica, sans-serif;'><span style='font-size:18px;'>Error:</span> <span style='font-size:14px;'>Please try later</span></span>";
//exit();
//}



if($_SESSION['event_ticket_id_for_ticket']){
$ev	=	$_SESSION['event_ticket_id_for_ticket'];
$frmID	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$ev'");
}
elseif($_SESSION['event_ticket_id']){
$frmID	=	$_SESSION['event_ticket_id'];
}

$ticket_id	=	$frmID;

$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

$already_uploaded = 0;

$action = "save";
$sucMessage = "";

if (isset($_POST["submit"])) {
	$save_price='';

	$name						=	$_POST["name"];
	$mainTitle					=	$_POST["mainTitle"];
	$mainPrice					=	$_POST["mainPrice"];
	$event_id					=	$_POST["event_id"];
	$service_fee_type			=	$_POST["service_fee_type"];
	$event_ticket_id			=	$_POST['event_ticket_id'];
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
		
	if(	trim($bc_prometer_service_free)=='' && trim($bc_buyer_service_free)=='')
		$errors[] = 'Please enter Split Percent';
	if(($bc_prometer_service_free+$bc_buyer_service_free)!=100)
$errors[] = 'Please enter Correct Percentage (Split Percent)';
	
	if ( trim($service_fee_type)!='' && $service_fee=='' || (trim($service_fee_type)!='' && trim($service_fee=='Enter Service Fee')))
		$errors[] = 'Please enter Service Free:';
		
	if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="81%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	$r4 = mysql_query("select * from event_ticket where id='$frmID'");
	if(mysql_num_rows($r4)){
	$action = "edit";
	}
	if($action=='edit'){
	$sql = "UPDATE `event_ticket` SET `name` = '$name', `quantity_available` = '$quantity_available', `start_sales_date` = '$start_sales_date', `start_sales_time` = '$start_sales_time', `start_sales_before_days` = '$start_sales_before_days', `start_sales_before_hours` = '$start_sales_before_hrs', `start_sales_before_minutes` = '$start_sales_before_min', `end_sales_date` = '$end_sales_date', `end_sales_time` = '$end_sales_time', `end_sales_before_days` = '$end_sales_before_days', `end_sales_before_hours` = '$end_sales_before_hrs', `end_sales_before_minutes` = '$end_sales_before_min', `min_tickets_order` = '$min_tickets', `max_tickets_order` = '$max_tickets', `ticket_description` = '$ticket_description', `service_fee_type` = '$service_fee_type' , `service_fee` = '$service_fee' , `buyer_event_grabber_fee` = '$bc_buyer_service_free' , `prometer_event_grabber_fee` = '$bc_prometer_service_free' where `id` = '$ticket_id'";
	$res = mysql_query($sql);
	if($res){
	$sucMessage =	"Record Successfully updated.";
	}
	else{
	$sucMessage	=	"<span style='font-family:Arial, Helvetica, sans-serif;'><span style='font-size:18px;'>Error:</span> <span style='font-size:14px;'>Please try later</span></span>";
	}
	}
	if($action=="save"){
	$sql = "INSERT INTO `event_ticket` (`id`, `name`, `quantity_available`, `start_sales_date`, `start_sales_time`, `start_sales_before_days`, `start_sales_before_hours`, `start_sales_before_minutes`, `end_sales_date`, `end_sales_time`, `end_sales_before_days`, `end_sales_before_hours`, `end_sales_before_minutes`, `min_tickets_order`, `max_tickets_order`, `ticket_description`, `service_fee_type`, `service_fee`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$name', '$quantity_available', '$start_sales_date', '$start_sales_time', '$start_sales_before_days', '$start_sales_before_hrs', '$start_sales_before_min', '$end_sales_date', '$end_sales_time', '$end_sales_before_days', '$end_sales_before_hrs', '$end_sales_before_min', '$min_tickets', '$max_tickets', '$ticket_description', '$service_fee_type', '$service_fee', '$bc_buyer_service_free', '$bc_prometer_service_free', '$event_id')";
	$res = mysql_query($sql);
	$_SESSION['event_ticket_id']	=	mysql_insert_id();
	$ticket_id	=	$_SESSION['event_ticket_id'];
	if($res){
	$sucMessage	=	"Record Successfully inserted.";
	}
	else{
	$sucMessage	=	"<span style='font-family:Arial, Helvetica, sans-serif;'><span style='font-size:18px;'>Error:</span> <span style='font-size:14px;'>Please try later</span></span>";
	}
	}

if($res){
	if($event_ticket_id){
	$qry2 	=	"UPDATE `event_ticket_price` SET `title` = '$mainTitle', `price` = '$mainPrice', `ticket_id` = '$ticket_id' WHERE `id` ='$event_ticket_id'";
	}
	else{
	$qry2	=	"INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id')";
	}
	mysql_query($qry2);

	if ( is_array($_POST['title']) ) {
		for($i=0;$i< count($_POST['title']); $i++) {
		$title					=	$_POST['title'][$i];	
		$price					=	$_POST['price'][$i];
		$event_ticket_price_id	=	$_POST['event_ticket_price_id'][$i];

		if($price=='costum_price'){
		$price = $_POST['costum'][$i];
		}
	if(trim($_POST['del'][$i])=='yes' && $event_ticket_price_id!=''){
	mysql_query("DELETE FROM `event_ticket_price` WHERE `id` = '$event_ticket_price_id'");
	}
else{
		if($title!='' && $price!=''){
	if($_POST['ac'][$i]=='update'){
	
		$qry 	=	"UPDATE `event_ticket_price` SET `title` = '$title', `price` = '$price' WHERE `id` = '$event_ticket_price_id'";
		}
		elseif ($_POST['ac'][$i]=='insert'){
		$qry	=	"INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$title', '$price', '$ticket_id')";
		}
		mysql_query($qry);
			}
				}
				}
				}
	
	
	}
	
	}
	else{
	$sucMessage = $err;
	}
	
}





$sql	=	"select * from event_ticket where id='$frmID'";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$name		 				=	$row["name"]; 
		$mainPrice					=	$row["price"];
		$quantity_available			=	$row['quantity_available'];
		$event_ticket_id			=	$_POST['event_ticket_id'];
		$start_sales_date			=	$row['start_sales_date'];
		$start_sales_time			=	$row['start_sales_time'];
		$start_sales_before_days	=	$row['start_sales_before_days'];
		$start_sales_before_hours	=	$row['start_sales_before_hours'];
		$start_sales_before_minutes	=	$row['start_sales_before_minutes'];
		$end_sales_date				=	$row['end_sales_date'];
		$end_sales_time				=	$row['end_sales_time'];
		$end_sales_before_days		=	$row['end_sales_before_days'];
		$end_sales_before_hours		=	$row['end_sales_before_hours'];
		$end_sales_before_minutes	=	$row['end_sales_before_minutes'];
		$min_tickets_order			=	$row['min_tickets_order'];
		$max_tickets_order			=	$row['max_tickets_order'];
		$ticket_description			=	$row['ticket_description'];
		$event_id					=	$row['event_id'];
		$service_fee_type			=	$row['service_fee_type'];
		$service_fee				=	$row['service_fee'];
		$bc_prometer_service_free	=	$row['prometer_event_grabber_fee'];
		$bc_buyer_service_free		=	$row['buyer_event_grabber_fee'];
		$ticket_id					=	$row['id'];
		
		}}






//include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script>

	 function show(){
	document.getElementById('service_feeDiv').style.display='block';
	}
	
function add_newRow(id){
	var next_row = id+1;
	var id2 = id;
	var new_url_feild = '<div id="addrow'+next_row+'" style=" margin-bottom: 10px; background:#FBFBFB; border:#F2F1F1 solid 1px;width: 426px;"><div class="evField3">Title:<b class="clr">*</b></div><div class="evLabal3" style="width:334px"><input type="text" style=" width:280px" value="" class="inp2" name="title[]" id="title'+next_row+'" /></div><div style="float:left"><img src="images/delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow('+next_row+',\'no\');"></div><div class="clr"></div><div class="evField3">Price:<b class="clr">*</b></div><div class="evLabal3" style="width:334px;"><input type="checkbox" onClick="fldDisabledFalse('+next_row+')" id="fld_'+next_row+'" name="price[]" value="costum_price"> <input type="text" class="inp2" name="costum[]" value="" id="costum_price'+next_row+'" onFocus="slct('+next_row+');" onkeypress="return isNumberKey(event)" style="width:60px;"><input type="hidden" name="ac[]" value="insert" /><input type="hidden" name="event_ticket_price_id[]"><input type="hidden" name="del[]" value="" id="del_'+next_row+'"><input type="checkbox" id="free'+next_row+'" onClick="rmv('+next_row+')" name="price[]" value="free"> Free </div><div class="clr"></div></div>';
	
	
	$('#add_url_ist').append(new_url_feild);
	$('#add_more_btn_1').html('<span style="cursor:pointer; font-size:13px; color:#0033CC" onclick="add_newRow('+next_row+');">&nbsp;&nbsp;Add More</span>');
	  }


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
	
function advOptHidSho(){
	var status = document.getElementById('st').innerHTML;
	if(status=='Hide'){
		document.getElementById('advance_options').style.display='none';
		document.getElementById('st').innerHTML='Show<br><br><br><br><br><br><br><br><br><br><br><br>';
		}
		else{
		document.getElementById('advance_options').style.display='block';
		document.getElementById('st').innerHTML='Hide';			
			}
	}
	

	function writ(value,id){
	  document.getElementById('fld_'+id).value=value;
	  }
	function rmv(id){
	  document.getElementById('fld_'+id).value='';
	  document.getElementById('costum_price'+id).value='';
	  document.getElementById('fld_'+id).checked=false;
	  }
	function slct(id){
	  document.getElementById('fld_'+id).checked=true;
	  document.getElementById('free'+id).checked=false;
	  }
	function fldDisabledFalse(id){
	  document.getElementById('free'+id).checked=false;
	  document.getElementById('costum_price'+id).focus();
	  }
	function removeRow(id,del){
	  document.getElementById("title"+id).value='';
	  document.getElementById("costum_price"+id).value='';
	  if(del=='yes'){
	  document.getElementById("del_"+id).value='yes';
	  }else{
		  document.getElementById("del_"+id).value='no';
		  }
	  document.getElementById("addrow"+id).style.display='none';
	  }
	  
function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57)){
		 alert ("This is not a number! Please enter a valid number");
		 return false;
		 }
         return true;
}

function removeText(value,text,id){
		if(value==text){
			document.getElementById(id).value='';
			document.getElementById(id).style.color='#000';
			}
		}

function returnText(text,id){
	if(document.getElementById(id).value==''){
	document.getElementById(id).value=text;
	document.getElementById(id).style.color='#555';
	}
	}
</script>
<link href="<?= ABSOLUTE_PATH; ?>style.css" rel="stylesheet" type="text/css" />
<style>
body{
	min-width:0;
	}
</style>

<div style="padding:0 10px">

  <div class="" style="padding-top:23px">
    <form id="" name="" method="post" action="" >
	  <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
      <div class="success"><?php echo $sucMessage; ?></div>
      <div class="evField">Ticket Name:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="text" maxlength="100" value="<?php echo $name;?>" name="name" style="width:410px;" class="inp2">
        <br>
        <small>Examples: Member, Non-member, Student, Early Bird</small> </div>
      <div class="clr"></div>
	  <?php
	   $r = mysql_query("select * from `event_ticket_price` where `ticket_id`='$frmID' && `ticket_id`!='0' ORDER BY `id` ASC LIMIT 0,1");
	   while($ro = mysql_fetch_array($r)){
echo	   $mainTitle		=	$ro['title'];
	echo   $mainPrice		=	$ro['price'];
	   $event_ticket_id	=	$ro['id'];
	   }
	?>
      <div class="evField">Title:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="text" style=" width:280px" value="<?php echo $mainTitle;?>" class="inp2" name="mainTitle" id="mainTitle" />
      </div>
      <div class="clr"></div>
      <div class="evField">Price:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="checkbox" onClick="fldDisabledFalse('1st')" id="fld_1st" <?php if (is_numeric(trim($mainPrice))){ echo 'checked="checked"'; }?> value="costum_price" name="mainPrice">
		<input type="hidden" name="event_ticket_id" value="<?php echo $event_ticket_id; ?>" />
        <input type="text" class="inp2" name="costum_price" value="<?php if (is_numeric(trim($mainPrice))){ echo trim($mainPrice); }?>" id="costum_price1st" onKeyPress="return isNumberKey(event)" style="width:60px;" onFocus="slct('1st')">
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
        <div style="float:left"><img src="<?=IMAGE_PATH;?>delete.png" style="cursor:pointer;padding:10px;" title="Delete" onClick="removeRow(<?php echo $next; ?>);"></div>
        <div class="clr"></div>
        <div class="evField">Price:<b class="clr">*</b></div>
        <div class="evLabal" style="width:334px;">
          <label>
          <input type="radio" id="cstm<?php echo $next; ?>" name="mprice_<?php echo $i; ?>" value="costum_price" <?php if ($mprice=='costum_price'){ echo "checked='checked'"; } ?> onClick="prc(this.value,<?php echo $i; ?>);">
          <input type="text" class="inp2" name="mcostum_price_<?php echo $i; ?>" value="<?php if($mprice=='costum_price'){	echo $_POST['mcostum_price_'.$i];} ?>" id="costum_price<?php echo $i; ?>" onKeyPress="return isNumberKey(event)" style="width:60px;">
          </label>
          <label>
          <input type="radio" id="free<?php echo $next; ?>" name="mprice_<?php echo $i; ?>" value="free" <?php if ($mprice=='free'){ echo "checked='checked'"; } ?> onChange="prc(this.value,<?php echo $i; ?>);">
          Free </label>
        </div>
        <div class="clr"></div>
      </div>
      <?php	}	}	}	}	} ?>
      <div class="evField">Additional Packages:<br />
	   <?php
	   $r = mysql_query("select * from `event_ticket_price` where `ticket_id`='$ticket_id' && `ticket_id`!='0' ORDER BY `id` ASC");
	   $i=0;
	   $numrows	=	mysql_num_rows($r);
	   $nowStartTo=$numrows;
	   $z=0;
	   ?>
        <span id="add_more_btn_1"><span style="cursor:pointer; font-size:13px; color:#0033CC" onClick="add_newRow(<?php if ($nowStartTo){ echo $nowStartTo;} else{ echo '0'; } ?>);"><strong>Add More</strong></span></span> </div>
      <div class="evLabal">
	 <?php
	   while($ro = mysql_fetch_array($r)){
	   $i++;
	   $next = $i;
	   if($i!=1){
	   $title					=	$ro['title'];
	   $price					=	$ro['price'];
	   $event_ticket_price_id	=	$ro['id'];
	?>
        <div id="addrow<?php echo $next; ?>" style="margin-bottom: 10px; background:#FBFBFB; border:#F2F1F1 solid 1px;width: 426px;">
          <div class="evField3">Title:<b class="red">*</b></div>
          <input type="hidden" name="hdErr[]" value="1" id="hdd<?php echo $next; ?>" />
          <div class="evLabal3" style="width:334px" >
            <input type="text" style="width:280px" value="<?php echo $title; ?>" class="inp2" name="title[]" id="title<?php echo $i; ?>" />
          </div>
          <div style="float:left"><img src="<?=IMAGE_PATH;?>delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow(<?php echo $next; ?>,'yes');"></div>
          <div class="clr"></div>
          <div class="evField3">Price:<b class="red">*</b></div>
          <div class="evLabal3" style="width:334px" >
            <input type="checkbox" onClick="fldDisabledFalse('<?php echo $i; ?>')" id="fld_<?php echo $i; ?>" <?php if (is_numeric(trim($price))){ echo 'checked="checked"'; }?>  name="price[]" value="costum_price">
            <input type="text" class="inp2" name="costum[]" value="<?php if (is_numeric(trim($price))){ echo trim($price); }?>" id="costum_price<?php echo $i; ?>" onFocus="slct(<?php echo $i; ?>);" onkeypress="return isNumberKey(event)" style="width:60px;">
            <input type="hidden" name="ac[]" value="update" />
            <input type="hidden" name="event_ticket_price_id[]" value="<?php echo $event_ticket_price_id; ?>" />
            <input type="hidden" name="del[]" value="" id="del_<?php echo $i; ?>" /> 
			<input type="checkbox" id="free<?php echo $i; ?>" <?php if (!is_numeric(trim($price)) && $price!=''){ echo 'checked="checked"'; }?>  onClick="rmv(<?php echo $i; ?>)" name="price[]" value="free">
            Free 
            <!--  <label>
          <input type="radio" <?php if ($mprice=='donation'){ echo "checked='checked'"; } ?>  id="donation<?php echo $next; ?>" name="mprice_<?php echo $i; ?>" value="donation" onChange="prc(this.value,<?php echo $i; ?>);">
          Donation Format (Attendee can specify the payment amount)</label>-->
          </div>
          <div class="clr"></div>
        </div>
        <?php	 
	 $z++;  }
	 
	  }
	  ?>
        <div id="add_url_ist"></div>
      </div>
      <div class="clear"></div>
      <div class="evField">Quantity Available:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="text" class="inp2" name="quantity_available" value="<?php echo $quantity_available; ?>" style="width:60px;">
      </div>
      <div class="clr"></div>
      <div class="evField" style="padding-top:23px;">Split Percent:<b class="clr">*</b></div>
      <div class="evLabal"><small class="clr">Event Grabber Service Fee (<?php echo getSingleColumn('event_fee',"select `event_fee` from default_settings"); ?>%)</small><br />
        Prometer
        <input type="text" onKeyPress="return isNumberKey(event)" class="inp2" style="width:40px;" name="prometer_service_free" value="<?php echo $bc_prometer_service_free; ?>" />
        % &nbsp; Buyer
        <input type="text" name="buyer_service_free" value="<?php echo $bc_buyer_service_free; ?>" onKeyPress="return isNumberKey(event)" class="inp2" style="width:40px;" />
        %</div>
      <div class="clr"></div>
      <div style="background:url(<?= IMAGE_PATH;?>blc_hd_bg2.jpg) no-repeat; width:774px; height:28px;">
        <div class="evField" style="padding:6px; color:#FFFFFF">Advanced Options:</div>
        <div class="evLabal" style="padding: 5px;"><span style="color:#fff; text-decoration:underline; cursor:pointer" id="st" onClick="advOptHidSho();">Hide</span></div>
        <div class="clr"></div>
      </div>
      <div id="advance_options">
        <div class="clr"></div>
        <div class="evField">Start Sales</div>
        <div class="evLabal">
          <label for="specify_day">
          <input type="radio" id="specify_day" value="specify_day" <?php if ($start_sales_date!='0000-00-00' && $start_sales_date!=''){ echo "checked='checked'";} ?> name="start_sales" onChange="timeBeforeEventStart(this.value);">
        Specify day
        <input type="text" class="inp2" name="start_sales_date" <?php if ($start_sales_date=='0000-00-00'){ echo "disabled='disabled'";} ?> 
		value="<?php if ($start_sales_date=='0000-00-00' || $start_sales_date==''){echo '';}else{ echo date('d-M-Y', strtotime($start_sales_date)); } ?>" readonly="" id="start_sales_date" style="width:76px; color:#000000; cursor:pointer">
        <select class="inp3" name="start_sales_hrs" id="start_sales_hrs"
		<?php
		 if ($start_sales_date=='0000-00-00'){
		   echo 'disabled="disabled"';
		   }
		   ?>
		   >
          <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'"';
		   if(date("h",strtotime($start_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
        </select>
        :
        <select class="inp3" name="start_sales_min" id="start_sales_min"
		<?php
		 if ($start_sales_date=='0000-00-00'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
          <?php
		   for ($i=00;$i<=59;$i++){
		   if($i<10){
		   $i = "0".$i;
		   }
		   echo '<option value="'.$i.'"';
		   if(date("i",strtotime($start_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
        </select>
        <select class="inp3" name="start_sales_ampm" id="start_sales_ampm"
		<?php
		 if ($start_sales_date=='0000-00-00'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
          <option value="am" <?php if(date("a",strtotime($start_sales_time) == 'am')){ echo 'selected="selected"'; } ?> >AM</option>
          <option value="pm" <?php if(date("a",strtotime($start_sales_time) == 'pm')){ echo 'selected="selected"'; } ?> >PM</option>
        </select>
          </label>
          <label for="time_before">
          <input type="radio" id="time_before" value="time_before" name="start_sales" <?php if ($start_sales_before_days!='0' && $start_sales_before_days!=''){ echo 'checked="checked"';} ?> onChange="timeBeforeEventStart(this.value);">
        Time before event starts
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($start_sales_before_days!=0){echo $start_sales_before_days;} ?>" <?php if($start_sales_before_days==0){ echo 'disabled="disabled"';} ?> name="start_sales_before_days" id="start_sales_before_days">
        Days &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" name="start_sales_before_hrs" value="<?php if($start_sales_before_hours!=0){echo $start_sales_before_hours;} ?>" <?php if($start_sales_before_hours==0){ echo 'disabled="disabled"';} ?> id="start_sales_before_hrs">
        Hours &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" name="start_sales_before_min" value="<?php if($start_sales_before_minutes!=0){echo $start_sales_before_minutes;} ?>" <?php if($start_sales_before_minutes==0){ echo 'disabled="disabled"';} ?> id="start_sales_before_min">
        Minutes &nbsp;</label>
        </div>
        <div class="clr"></div>
        <div class="evField">End Sales</div>
        <div class="evLabal">
          <label for="specify_day2">
          <input type="radio" id="specify_day2" name="end_sales" <?php if ($end_sales_date!='0000-00-00' && $end_sales_date!=''){ echo "checked='checked'";} ?> value="specify_day2" onChange="timeBeforeEventEnd(this.value);">
        Specify day
        <input type="text" class="inp2" <?php if ($end_sales_date=='0000-00-00'){ echo "disabled='disabled'";} ?> value="<?php if ($end_sales_date=='0000-00-00' || $end_sales_date==''){echo '';}else{ echo date('d-M-Y', strtotime($end_sales_date)); } ?>" readonly="" name="end_sales_date" id="end_sales_date" style="width:76px; cursor:pointer">
        <select class="inp3" name="end_sales_hrs" id="end_sales_hrs"
		<?php
		 if ($end_sales_date=='0000-00-00'){
		   echo 'disabled="disabled"';
		   }
		   ?>
		   >
          <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'"';
		   if(date("h",strtotime($end_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
        </select>
        :
        <select class="inp3" name="end_sales_min" id="end_sales_min"
		<?php
		 if ($end_sales_date=='0000-00-00'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
          <?php
		   for ($i=00;$i<=59;$i++){
		   if($i<10){
		   $i = "0".$i;
		   }
		   echo '<option value="'.$i.'"';
		   if(date("i",strtotime($end_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
        </select>
        <select class="inp3" name="end_sales_ampm" id="end_sales_ampm"
		<?php
		 if ($end_sales_date=='0000-00-00'){
		   echo 'disabled="disabled"';
		   }
		   ?>>
          <option value="am" <?php if(date("a",strtotime($end_sales_time) == 'am')){ echo 'selected="selected"'; } ?> >AM</option>
          <option value="pm" <?php if(date("a",strtotime($end_sales_time) == 'pm')){ echo 'selected="selected"'; } ?> >PM</option>
        </select>
          </label>
          <label for="time_before2">
          <input type="radio" id="time_before2" name="end_sales"  <?php if ($end_sales_before_days!='0' && $end_sales_before_days!=''){ echo 'checked="checked"';} ?>  value="time_before2" onChange="timeBeforeEventEnd(this.value);">
        Time before event ends
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_days!=0){echo $end_sales_before_days;} ?>" <?php if($end_sales_before_days==0){ echo 'disabled="disabled"';} ?> name="end_sales_before_days" id="end_sales_before_days">
        Days &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_hours!=0){echo $end_sales_before_hours;} ?>" <?php if($end_sales_before_hours==0){ echo 'disabled="disabled"';} ?>   name="end_sales_before_hrs" id="end_sales_before_hrs">
        Hours &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_minutes!=0){echo $end_sales_before_minutes;} ?>" <?php if($end_sales_before_minutes==0){ echo 'disabled="disabled"';} ?>  name="end_sales_before_min" id="end_sales_before_min">
        Minutes &nbsp;</label>
        </div>
        <div class="clr"></div>
        <!--<div class="evField">Min, Tickets Order</div>
        <div class="evLabal">
		  <input type="text" onkeypress="return isNumberKey(event)" class="inp2" style="width:60px;" name="min_tickets"
	  value="<?php if ($_POST['submit']){ echo $_POST['min_tickets']; } else{ if($min_tickets_order!=0){ echo $min_tickets_order;} }?>">
	  
		  
          &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt=""></a> </div>
        <div class="clr"></div>
        <div class="evField">Max, Tickets Order</div>
        <div class="evLabal">
          <input type="text" onkeypress="return isNumberKey(event)" class="inp2" style="width:60px;" name="max_tickets" value="<?php if ($_POST['submit']){ echo $_POST['max_tickets']; } else{ if($max_tickets_order!=0){ echo $max_tickets_order;}}?>">
          &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt=""></a> </div>
        <div class="clr"></div>-->
        <div class="evField">Ticket Description</div>
        <div class="evLabal">
          <textarea id="ticket_description" name="ticket_description" class="inp" style="height:35px; width:410px; padding:3px; margin:0" onFocus="removeText(this.value,'Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');" onBlur="returnText('Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');"><?php if ($_POST['submit']){ echo $_POST['ticket_description']; } else{ if ($ticket_description){ echo $ticket_description;	} else{ echo "Access for one person to the launch celebration for the new website Eventgrabber"; } }?>
</textarea>
          <br>
          <!--  <input type="radio">
        Auto Hide Description &nbsp; <a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a>-->
        </div>
        <div class="clr"></div>
        <div class="evField">Service Fee</div>
        <div class="evLabal">
          <label for="add">
          <input type="radio" name="service_fee_type" onclick="show();" <?php if (trim($service_fee_type)==1){ echo 'checked="checked"';} ?> id="add" value="1">
          ADD fees on top of total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt=""></a></label>
          <label for="include">
          <input type="radio" name="service_fee_type" onclick="show();" <?php if (trim($service_fee_type)==2){ echo 'checked="checked"';} ?> value="2" id="include">
          INCLUDE fees into total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt=""></a></label>
          <label for="include_and_add">
          <input type="radio" name="service_fee_type"onclick="show();" id="include_and_add" <?php if (trim($service_fee_type)==3){ echo 'checked="checked"';} ?>  value="3">
          INCLUDE credit card processing fee in the total ticket price and ADD the Eventgrabber fee on top of the total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt=""></a></label>
         <div id="service_feeDiv" style="display:<?php if ($service_fee_type==''){ echo 'none';} else{ echo 'block'; }?>"><b class="red">*</b>
          <input type="text" style="width:112px" onkeypress="return isNumberKey(event)"  class="inp2" value="<?php if ($service_fee=='' || $service_fee==0){ echo 'Enter Service Fee'; } else{ echo $service_fee; } ?>" id="service_fee" name="service_fee" onFocus="removeText(this.value,'Enter Service Fee','service_fee');" onBlur="returnText('Enter Service Fee','service_fee');" />
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