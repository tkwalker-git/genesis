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


if($_GET['event_ticket_id']){
$frmID	=	$_GET['event_ticket_id'];
}elseif($_SESSION['event_ticket_id']){
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

	$mainTitle					=	$_POST["mainTitle"];
	$mainPrice					=	$_POST["mainPrice"];
	$event_id					=	$_POST["event_id"];
	$event_ticket_id			=	$_POST['event_ticket_id'];
	$bc_prometer_service_fee	=	$_POST["prometer_service_free"];
	$bc_buyer_service_fee		=	$_POST["buyer_service_free"];
	if($bc_prometer_service_fee!='' && $bc_buyer_service_fee==''){
	$bc_buyer_service_fee		=	100-$bc_prometer_service_fee;
	}elseif($bc_buyer_service_fee!='' && $bc_prometer_service_fee==''){
	$bc_prometer_service_fee		=	100-$bc_buyer_service_fee;
	}
	
	
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
	$end_sales_date					=	date('Y-m-d', strtotime($_POST['end_sales_date']));
	$endTime						=	$_POST['end_sales_hrs'].":".$_POST['end_sales_min']." ".$_POST['end_sales_ampm'];
	$end_sales_time					= 	date('H:i', strtotime($endTime));
	}
	
	$ticket_description				=	$_POST['ticket_description'];
	if($ticket_description == 'Access for one person to the launch celebration for the new website Eventgrabber'){
	$ticket_description = '';
	}
	
	$sucMessage = "";
	
	$errors = array();
	
	
		
	if ( trim($mainTitle) == '' )
		$errors[] = 'Please enter Ticket Type';

	if	( trim($mainPrice) == '' )
		$errors[] = 'Please enter Price';
		
		
	if ( trim($quantity_available) == '' )
		$errors[] = 'Please enter Quantity Available';
		
	if(	trim($bc_prometer_service_fee)=='' && trim($bc_buyer_service_fee)=='')
		$errors[] = 'Please enter Split Percent';
	if(($bc_prometer_service_fee+$bc_buyer_service_fee)!=100)
$errors[] = 'Please enter Correct Percentage (Split Percent)';
		
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
	$sql = "UPDATE `event_ticket` SET `quantity_available` = '$quantity_available', `start_sales_date` = '$start_sales_date', `start_sales_time` = '$start_sales_time', `end_sales_date` = '$end_sales_date', `end_sales_time` = '$end_sales_time', `ticket_description` = '$ticket_description', `buyer_event_grabber_fee` = '$bc_buyer_service_fee' , `prometer_event_grabber_fee` = '$bc_prometer_service_fee' where `id` = '$ticket_id'";
	$res = mysql_query($sql);
	if($res){
	$sucMessage =	"Record Successfully updated.";
	}
	else{
	$sucMessage	=	"<span style='font-family:Arial, Helvetica, sans-serif;'><span style='font-size:18px;'>Error:</span> <span style='font-size:14px;'>Please try later</span></span>";
	}
	}
	if($action=="save"){
	$sql = "INSERT INTO `event_ticket` (`id`, `quantity_available`, `start_sales_date`, `start_sales_time`, `end_sales_date`, `end_sales_time`, `ticket_description`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$quantity_available', '$start_sales_date', '$start_sales_time', '$end_sales_date', '$end_sales_time', '$ticket_description', '$bc_buyer_service_fee', '$bc_prometer_service_fee', '$event_id')";
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
		$mainPrice					=	$row["price"];
		$quantity_available			=	$row['quantity_available'];
		$event_ticket_id			=	$_POST['event_ticket_id'];
		$start_sales_date			=	$row['start_sales_date'];
		$start_sales_time			=	$row['start_sales_time'];
		$end_sales_date				=	$row['end_sales_date'];
		$end_sales_time				=	$row['end_sales_time'];
		$ticket_description			=	$row['ticket_description'];
		$event_id					=	$row['event_id'];
		$bc_prometer_service_fee	=	$row['prometer_event_grabber_fee'];
		$bc_buyer_service_fee		=	$row['buyer_event_grabber_fee'];
		$ticket_id					=	$row['id'];
		
		}}


//include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/ev_functions.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.core.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/demos.css">
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.theme.css">
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.css">
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>calendar/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/validate.decimal.js"></script>
<script>

	
function add_newRow(id){
	var next_row = id+1;
	var id2 = id;
	var new_url_feild = '<div id="addrow'+next_row+'" class="ticket_row"><div class="evField3">Ticket Type:<b class="clr">*</b></div><div class="evLabal3" style="width:334px"><input onkeyup="updateTable(\'ajax/load_ticket_price_table.php\');" type="text" style=" width:280px" value="" class="new_input" name="title[]" id="title'+next_row+'" /></div><div style="float:left"><img src="images/delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow('+next_row+',\'no\'),updateTable(\'ajax/load_ticket_price_table.php\');"></div><div class="clr"></div><div class="evField3">Price:<b class="clr">*</b></div><div class="evLabal3" style="width:334px;"><input type="text" class="new_input" name="price[]" value="" id="costum_price'+next_row+'" onkeyup="updateTable(\'ajax/load_ticket_price_table.php\'),extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" style="width:60px;"><input type="hidden" name="ac[]" value="insert" /><input type="hidden" name="event_ticket_price_id[]"><input type="hidden" name="del[]" value="" id="del_'+next_row+'"></div><div class="clr"></div></div>';
	
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
		document.getElementById('st').innerHTML='Show<br><br><br><br><br><br>';
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
	
	
	$(document).ready(function() {
		var dates = $("#start_sales_date").datepicker({
			defaultDate: "",
			changeMonth: true,
			dateFormat:'dd-M-yy',
			minDate:'<?php echo date("d-M-Y",strtotime($startDateMain)); ?>',
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "from" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	
	$(document).ready(function() {
		var dates = $("#end_sales_date").datepicker({
			defaultDate: "",
			changeMonth: true,
			dateFormat:'dd-M-yy',
			minDate:'<?php echo date("d-M-Y",strtotime($startDateMain)); ?>',
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "from" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	
	
	function updateTable(ajax_page)      
	 {  
	 var prometer_service_free	=	$('#prometer_service_free').val();
	 var buyer_service_free		=	$('#buyer_service_free').val();
	 var mainPrice				=	$('#mainPrice').val();
	 var event_fee				=	$('#event_fee').val();
	 var mainTitle				=	$('#mainTitle').val();
	 
	var additionalTitles		=	new Array();
	var additionalPrices		=	new Array();
	
	$('input[name=title[]]').each(function() {
	 		if($(this).val()!=''){
		  additionalTitles.push($(this).val());
		  }
	});
	
	$('input[name=price[]]').each(function() {
	 	if($(this).val()!=''){
		  additionalPrices.push($(this).val());
		  }
	});
	 
		 $.ajax({  
			type: "GET",  
			url: ajax_page,  
			data: "mainPrice="+mainPrice+"&prometer_service_free="+prometer_service_free+"&buyer_service_free="+buyer_service_free+"&event_fee="+event_fee+"&mainTitle="+mainTitle+"&additionalTitles="+additionalTitles+"&additionalPrices="+additionalPrices,  
			
			beforeSend: function(){
			$("#priceTable").html('<img src="images/loading.gif">');
			},
			dataType: "text/html",  
			success: function(html){
			$("#priceTable").css('display','block');
			$("#priceTable").html(html);
			$("#showtickets").html(html);
			}
	   	});
	  }
	  
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
      <?php
	   $r = mysql_query("select * from `event_ticket_price` where `ticket_id`='$frmID' && `ticket_id`!='0' ORDER BY `id` ASC LIMIT 0,1");
	   while($ro = mysql_fetch_array($r)){
	   $mainTitle		=	$ro['title'];
	   $mainPrice		=	$ro['price'];
	   $event_ticket_id	=	$ro['id'];
	   }
	?>
      <div class="evField">Ticket Type:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="text" style=" width:280px" onkeyup="updateTable('ajax/load_ticket_price_table.php');" value="<?php echo $mainTitle;?>" class="new_input" name="mainTitle" id="mainTitle" />
      </div>
      <div class="clr"></div>
      <div class="evField">Price:<b class="clr">*</b></div>
      <div class="evLabal">
        <input type="hidden" name="event_ticket_id" value="<?php echo $event_ticket_id; ?>" />
        <input type="text" class="new_input" name="mainPrice" value="<?php echo $mainPrice; ?>" id="mainPrice" onkeyup="updateTable('ajax/load_ticket_price_table.php'),extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" style="width:60px;">
      </div>
      <div class="clr"></div>
      
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
        <div id="addrow<?php echo $next; ?>" class="ticket_row">
          <div class="evField3">Ticket Type:<b class="red">*</b></div>
          <input type="hidden" name="hdErr[]" value="1" id="hdd<?php echo $next; ?>" />
          <div class="evLabal3" style="width:334px" >
            <input type="text" style="width:280px" value="<?php echo $title; ?>" onkeyup="updateTable('ajax/load_ticket_price_table.php')" class="new_input" name="title[]" id="title<?php echo $i; ?>" />
          </div>
          <div style="float:left"><img src="<?=IMAGE_PATH;?>delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow(<?php echo $next; ?>,'yes'),updateTable('ajax/load_ticket_price_table.php');"></div>
          <div class="clr"></div>
          <div class="evField3">Price:<b class="red">*</b></div>
          <div class="evLabal3" style="width:334px" >
            <input type="text" class="new_input" name="price[]" value="<?php if (is_numeric(trim($price))){ echo trim($price); }?>" id="costum_price<?php echo $i; ?>" onkeyup="updateTable('ajax/load_ticket_price_table.php'),extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" style="width:60px;">
            <input type="hidden" name="ac[]" value="update" />
            <input type="hidden" name="event_ticket_price_id[]" value="<?php echo $event_ticket_price_id; ?>" />
            <input type="hidden" name="del[]" value="" id="del_<?php echo $i; ?>" />
       
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
        <input type="text" class="new_input" name="quantity_available" onkeypress="return isNumberKey(event)" value="<?php echo $quantity_available; ?>" style="width:60px;" onkeyup="updateTable('ajax/load_ticket_price_table.php')">
      </div>
      <div class="clr"></div>
      <div style="background:url(<?= IMAGE_PATH;?>blc_hd_bg2.jpg) no-repeat; width:774px; height:28px;">
        <div class="evField" style="padding:6px; color:#FFFFFF">Advanced Options:</div>
        <div class="evLabal" style="padding: 5px;"><span style="color:#fff; text-decoration:underline; cursor:pointer" id="st" onClick="advOptHidSho();">Hide</span></div>
        <div class="clr"></div>
      </div>
      <div id="advance_options">
        <div class="clr"></div>
        <div class="evField">Start Sales</div>
        <div class="evLabal"> Specify day
          <input type="text" class="new_input" name="start_sales_date" <?php if ($start_sales_date=='0000-00-00'){ echo "disabled='disabled'";} ?> 
		value="<?php if ($start_sales_date=='0000-00-00' || $start_sales_date==''){echo '';}else{ echo date('d-M-Y', strtotime($start_sales_date)); } ?>" readonly="" id="start_sales_date" style="width:102px; color:#000000; cursor:pointer">
          <select class="inp3" name="start_sales_hrs" id="start_sales_hrs">
            <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'"';
		   if($start_sales_time && date("h",strtotime($start_sales_time)) == $i){
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
		   if($start_sales_time && date("i",strtotime($start_sales_time)) == $i){
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
 <option value="am" <?php if ($start_sales_time) {if(date("a",strtotime($start_sales_time) == 'am')){ echo 'selected="selected"'; }} ?> >AM</option>
 <option value="pm" <?php if ($start_sales_time) {if(date("a",strtotime($start_sales_time) == 'pm')){ echo 'selected="selected"'; }} ?> >PM</option>
 	</select>
          <div class="clr" style="height:10px"></div>
        </div>
        <div class="clr"></div>
        <div class="evField">End Sales</div>
        <div class="evLabal"> Specify day
          <input type="text" class="new_input" <?php if ($end_sales_date=='0000-00-00'){ echo "disabled='disabled'";} ?> value="<?php if ($end_sales_date=='0000-00-00' || $end_sales_date==''){echo '';}else{ echo date('d-M-Y', strtotime($end_sales_date)); } ?>" readonly="" name="end_sales_date" id="end_sales_date" style="width:102px; cursor:pointer">
          <select class="inp3" name="end_sales_hrs" id="end_sales_hrs">
            <?php
		   for ($i=1;$i<=12;$i++){
		   echo '<option value="'.$i.'"';
		   if($end_sales_time && date("h",strtotime($end_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
          </select>
          :
          <select class="inp3" name="end_sales_min" id="end_sales_min">
            <?php
		   for ($i=00;$i<=59;$i++){
		   if($i<10){
		   $i = "0".$i;
		   }
		   echo '<option value="'.$i.'"';
		   if($end_sales_time && date("i",strtotime($end_sales_time)) == $i){
		   echo 'selected="selected"';
		   }
		   echo '>'.$i.'</option>';
		   }
		   ?>
          </select>
          <select class="inp3" name="end_sales_ampm" id="end_sales_ampm">
            <option value="am" <?php if ($end_sales_time){ if(date("a",strtotime($end_sales_time) == 'am')){ echo 'selected="selected"'; }} ?> >AM</option>
            <option value="pm" <?php if ($end_sales_time){ if(date("a",strtotime($end_sales_time) == 'pm')){ echo 'selected="selected"'; }} ?> >PM</option>
          </select>
          <div class="clr" style="height:10px"></div>
        </div>
        <div class="clr"></div>
        <!--<div class="evField">Min, Tickets Order</div>
        <div class="evLabal">
		  <input type="text" onkeypress="return isNumberKey(event)" class="new_input" style="width:60px;" name="min_tickets"
	  value="<?php if ($_POST['submit']){ echo $_POST['min_tickets']; } else{ if($min_tickets_order!=0){ echo $min_tickets_order;} }?>">
	  
		  
          &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt=""></a> </div>
        <div class="clr"></div>
        <div class="evField">Max, Tickets Order</div>
        <div class="evLabal">
          <input type="text" onkeypress="return isNumberKey(event)" class="new_input" style="width:60px;" name="max_tickets" value="<?php if ($_POST['submit']){ echo $_POST['max_tickets']; } else{ if($max_tickets_order!=0){ echo $max_tickets_order;}}?>">
          &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo IMAGE_PATH; ?>question_icon.gif" alt=""></a> </div>
        <div class="clr"></div>-->
        <div class="evField">Ticket Description</div>
        <div class="evLabal">
          <textarea id="ticket_description" name="ticket_description" class="inp" style="height:50px; width:410px; padding:3px; margin:0" onFocus="removeText(this.value,'Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');" onkeyup="returnText('Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');"><?php if ($_POST['submit']){ echo $_POST['ticket_description']; } else{ if ($ticket_description){ echo $ticket_description;	} else{ echo "Access for one person to the launch celebration for the new website Eventgrabber"; } }?>
</textarea>
          <br>
          <!--  <input type="radio">
        Auto Hide Description &nbsp; <a href="#"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a>-->
        </div>
        <div class="clr"></div>
        <div style="font-size:15px">
          <div class="evField">&nbsp;</div>
          <!--<div class="evLabal"><strong>Service Fee</strong> : <?php echo $event_fee = getSingleColumn('event_fee',"select `event_fee` from default_settings"); ?>% of Ticket Price  (Only show results)<br />
            <br />-->
          <input type="hidden" name="event_fee" id="event_fee" value="<?php echo $event_fee; ?>" />
          <strong>Split fee</strong> : Promoter Pays
          <input type="text" class="new_input" style="width:56px;" name="prometer_service_free" id="prometer_service_free" 
onkeyup="updateTable('ajax/load_ticket_price_table.php');" onkeypress="return isNumberKey(event)"  onblur="splitFee(this.value,'p'),updateTable('ajax/load_ticket_price_table.php');" value="<?php if ($bc_prometer_service_fee || $bc_prometer_service_fee=='0'){ echo $bc_prometer_service_fee; } else{ echo "50" ;} ?>" />
          % Customer Pays
          <input type="text" name="buyer_service_free" onkeypress="return isNumberKey(event)" id="buyer_service_free" value="<?php if ($bc_buyer_service_fee || $bc_buyer_service_fee=='0'){ echo $bc_buyer_service_fee; } else{ echo "50" ;} ?>" onkeyup="updateTable('ajax/load_ticket_price_table.php');"  onblur="splitFee(this.value,'c'),updateTable('ajax/load_ticket_price_table.php');" class="new_input" style="width:56px;" />
          % <br />
          <br />
        </div>
        <div class="clr"></div>
        <div>
          <div class="ev_fltlft" style="width:195px; padding-right:10px"><strong>Ticket Type</strong></div>
          <div class="ev_fltlft" style="width:133px; padding-right:10px; text-align:center"><strong>Initial Ticket Price</strong></div>
          <div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center"><strong>Promoter Fees</strong></div>
          <div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center"><strong>Customer Fees</strong></div>
          <div class="ev_fltlft" style="width:143px; padding-right:10px; text-align:center"><strong>Final Ticket Price</strong></div>
          <div class="clr" style="height:10px"></div>
          <span id="priceTable"></span> </div>
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

<script>
updateTable('ajax/load_ticket_price_table.php');
</script>
