<?php

require_once("database.php"); 
require_once("header.php"); 

//$bc_source_id	=	"Admin-".rand();
$bc_event_source = 'Admin'; 

if(isset($_GET["id"]))
	$frmID	=	$_GET["id"];
	$ticket_id	=	$frmID;

$action = "save";
$sucMessage = "";


$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

$already_uploaded = 0;

if (isset($_POST["submit"])) {
	$save_price='';

	$name						=	$_POST["name"];
	$title						=	$_POST["title"];
	$price						=	$_POST["price"];
	$event_id					=	$_POST["event_id"];
	
	if($price=='costum_price'){
	$save_price2					=	$_POST['costum_price'];
	}else{
	$save_price2					=	$_POST["price"];
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
	$service_fee				=	$_POST['service_fee'];
	
	$sucMessage = "";
	
	$errors = array();
	
	
	if ( trim($name) == '' )
		$errors[] = 'Please enter Ticket Name';
		
	if ( trim($title) == '' )
		$errors[] = 'Please enter Title';

	if	( trim($save_price2) == '' )
		$errors[] = 'Please select a Price option';
		
		
	if ( trim($quantity_available) == '' )
		$errors[] = 'Please enter Quantity Available:';
	
	
	
	//if ( is_array($_POST['mtitle']) ) {
//		for($i=0;$i< count($_POST['mtitle']); $i++) {
//		$mtitle2 =	$_POST['mtitle'][$i];	
//		$mprice2	=	$_POST['mprice_'.$i];
//		
//		if($mtitle2!='' && $mprice2==''){
//		$errors[] = '.';
//		}
//		elseif($mtitle2=='' && $mprice2!=''){
//		$errors[] = '.';
//		
//		}}}
	

	
	
	if ( count( $errors) > 0) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	if (!count($errors)) {
	
	$r4 = mysql_query("select * from event_ticket where id='$frmID'");
	if(mysql_num_rows($r4)){
		$action = "edit";
	} // end if row
	
 	$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

 if($action=='edit'){
	$sql = "UPDATE `event_ticket` SET `name` = '$name', `quantity_available` = '$quantity_available', `start_sales_date` = '$start_sales_date', `start_sales_time` = '$start_sales_time', `start_sales_before_days` = '$start_sales_before_days', `start_sales_before_hours` = '$start_sales_before_hrs', `start_sales_before_minutes` = '$start_sales_before_min', `end_sales_date` = '$end_sales_date', `end_sales_time` = '$end_sales_time', `end_sales_before_days` = '$end_sales_before_days', `end_sales_before_hours` = '$end_sales_before_hrs', `end_sales_before_minutes` = '$end_sales_before_min', `min_tickets_order` = '$min_tickets', `max_tickets_order` = '$max_tickets', `ticket_description` = '$ticket_description', `servise_fee` = '$service_fee' where `id` = '$ticket_id'";
	
	$sucMessage =	"Record Successfully updated.";
	
	}
	if($action=='save'){
	$sql = "INSERT INTO `event_ticket` (`id`, `name`, `price`, `quantity_available`, `start_sales_date`, `start_sales_time`, `start_sales_before_days`, `start_sales_before_hours`, `start_sales_before_minutes`, `end_sales_date`, `end_sales_time`, `end_sales_before_days`, `end_sales_before_hours`, `end_sales_before_minutes`, `min_tickets_order`, `max_tickets_order`, `ticket_description`, `servise_fee`, `event_id`) VALUES (NULL, '$name', '$save_price', '$quantity_available', '$start_sales_date', '$start_sales_time', '$start_sales_before_days', '$start_sales_before_hrs', '$start_sales_before_min', '$end_sales_date', '$end_sales_time', '$end_sales_before_days', '$end_sales_before_hrs', '$end_sales_before_min', '$min_tickets', '$max_tickets', '$ticket_description', '$service_fee', '$event_id');";
	$res = mysql_query($sql);
	$sucMessage	=	"Record Successfully inserted.";
	}
	
	$res = mysql_query($sql);
	if($res){
	mysql_query("DELETE FROM `event_ticket_price` WHERE `ticket_id` = '$ticket_id'");

	$qry2 = "INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$title', '$save_price2', '$ticket_id')";
	mysql_query($qry2);

	if ( is_array($_POST['mtitle']) ) {
		for($i=0;$i< count($_POST['mtitle']); $i++) {
		$mtitle =	$_POST['mtitle'][$i];	
		$mprice	=	$_POST['mprice_'.$i];
		
		$save_price='';
			if($mprice=='costum_price'){
				$save_price		=	$_POST['mcostum_price_'.$i];
				}
			elseif($mprice=='free'){
				$save_price		=	trim($_POST["mprice_".$i]);
			}
			
		if($mtitle!='' && $save_price!=''){
			
//			if($mtitle!=''){
			$qry = mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`) VALUES (NULL, '$mtitle', '$save_price', '$ticket_id')");
			
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
		$price						=	$row["price"];
		$quantity_available			=	$row['quantity_available'];
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
		$servise_fee				=	$row['servise_fee'];
		$event_id					=	$row['event_id'];
		}}



?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".hint").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	});
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


	function removeRow(id){
	document.getElementById("mtitle"+id).value='';
	document.getElementById("cstm"+id).checked=false;
	document.getElementById("free"+id).checked=false;
//	document.getElementById("donation"+id).checked=false;
	document.getElementById("hdd"+id).value='';
	document.getElementById("addrow"+id).style.display='none';
	} 
	function add_newRow(id){
	var next_row = id+1;
	var id2 = id-1;
	/******************** With Donation Option ********************/
/*	var new_url_feild = '<div id="addrow'+next_row+'" style=" margin-bottom: 10px; background:#FBFBFB; border:#F2F1F1 solid 1px;width: 74%;"><div class="evField">Title:<b class="clr">*</b></div><input type="hidden" name="hdErr[]" value="1" id="hdd'+next_row+'" /><div class="evLabal" style="width:418px"><input type="text" style=" width:280px" value="" class="inp2" name="mtitle[]" id="mtitle'+next_row+'" /></div><div style="float:left"><img src="images/delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow('+next_row+');"></div><div class="clr"></div><div class="evField">Price:<b class="clr">*</b></div><div class="evLabal" style="width:410px;"><label><input type="radio" id="cstm'+next_row+'" name="mprice_'+id+'" value="costum_price" onclick="prc(this.value,'+next_row+');"><input type="text" class="inp2" name="mcostum_price_'+id+'" value="" id="costum_price'+next_row+'" onkeypress="return isNumberKey(event)" style="width:60px;">  </label><label><input type="radio" id="free'+next_row+'" name="mprice_'+id+'" value="free" onChange="prc(this.value,'+next_row+');">Free </label><label><input type="radio" id="donation'+next_row+'" name="mprice_'+id+'" value="donation" onChange="prc(this.value,'+next_row+');">Donation Format (Attendee can specify the payment amount) </label></div><div class="clr"></div></div>';
*/	
	
/******************** WithOut Donation Option ********************/
	var new_url_feild = '<div id="addrow'+next_row+'" style=" margin-bottom: 10px; background:#FBFBFB; border:#F2F1F1 solid 1px;width: 74%;"><div class="evField">Title:<b class="clr">*</b></div><input type="hidden" name="hdErr[]" value="1" id="hdd'+next_row+'" /><div class="evLabal" style="width:374px"><input type="text" style=" width:280px" value="" class="inp2" name="mtitle[]" id="mtitle'+next_row+'" /></div><div style="float:left"><img src="images/delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow('+next_row+');"></div><div class="clr"></div><div class="evField">Price:<b class="clr">*</b></div><div class="evLabal" style="width:410px;"><label><input type="radio" id="cstm'+next_row+'" name="mprice_'+id2+'" value="costum_price" onclick="prc(this.value,'+next_row+');"><input type="text" class="inp2" name="mcostum_price_'+id2+'" value="" id="costum_price'+next_row+'" onkeypress="return isNumberKey(event)" style="width:60px;">  </label><br><label><input type="radio" id="free'+next_row+'" name="mprice_'+id2+'" value="free" onChange="prc(this.value,'+next_row+');">Free </label></div><div class="clr"></div></div>';
	
	
	$('#add_url_ist').append(new_url_feild);
	$('#add_more_btn_1').html('<span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newRow('+next_row+');">&nbsp;&nbsp;Add More</span>');
	
	  }
	
	
function checkCategory(val)
{
	if ( val == -1 ) {
		alert("You can't select Parent Category");
		document.getElementById("category_id").selectedIndex=0;
	}	
}
	
	
	$(function() {
		var dates = $( "#start_sales_date, #end_sales_date" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat:'dd-M-yy',
			minDate:'<?php echo date('d-M-Y',strtotime(' +1 day')) ?>',
		/*		dateFormat:'yy-m-dd',
			minDate:'<?php// echo date('Y-m-d');?>',*/
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "start_sales_date" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
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
<form method="post" name="bc_form" enctype="multipart/form-data" action="" autocomplete="off" >
<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
  <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
    <tr class="bc_heading">
      <td colspan="2" align="left">Add/Edit Ticket</td>
    </tr>
    <tr>
      <td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
    </tr>
    <tr>
	<?php  if ($_GET['submit']){ echo "est event ticket"; }?>
      <td width="20%" align="right" class="bc_label">Ticket Name:<b class="clr">*</b></td>
      <td width="80%" align="left" class="bc_input_td"><input type="text" maxlength="100" value="<?php if ($_POST['submit']){ echo $_POST['name']; } else{ echo $name; }?>" name="name" style="width:410px;" class="inp2">
        <br>
        <small>Examples: Member, Non-member, Student, Early Bird</small> </td>
    </tr>
	<?php
	   $r = mysql_query("select * from `event_ticket_price` where `ticket_id`='$frmID' ORDER BY `id` ASC LIMIT 0,1");
	   while($ro = mysql_fetch_array($r)){
	   
	   
	   
	?>
	<tr>
	<td align="right" class="bc_label">Title:*</td>
	<td><input type="text" style=" width:280px" value="<?php echo $ro['title']; ?>" class="inp2" name="title" id="title" /></td>
	</tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Price:<b class="clr">*</b></td>
      <td width="80%" align="left" class="bc_input_td">
       
        <label for="cstm">
        <input type="radio" id="cstm" name="price" value="costum_price" <?php if (is_numeric(trim($ro['price']))){ echo 'checked="checked"'; }?> onChange="prc(this.value,'');">
        <input type="text" class="inp2" name="costum_price" value="<?php if (is_numeric(trim($ro['price']))){ echo trim($ro['price']); }?>" id="costum_price" onkeypress="return isNumberKey(event)" style="width:60px;">
        </label>
		<br />
        <label for="free">
        <input type="radio" id="free" <?php if (!is_numeric(trim($ro['price']))){ echo 'checked="checked"'; }?> name="price" value="free" onChange="prc(this.value,'');">
        Free </label>
		<?php } ?></td>
    </tr>
	<tr>
	<td colspan="2">
	 
	 <?php
	   $r = mysql_query("select * from `event_ticket_price` where `ticket_id`='$frmID' ORDER BY `id` ASC");
	   $i=0;
	   $numrows	=	mysql_num_rows($r);
//	   $nowStartTo=$numrows+2;
	   $nowStartTo=$numrows;
	   $z=0;
	   while($ro = mysql_fetch_array($r)){
	   $i++;
	 //  $next = $i+2;
	   $next = $i;
	   if($i!=1){
	  // if($_POST['submit']){
//	   $mtitle	=	$_POST['mtitle'][$z];
//	   if($_POST['mprice_'.$z]=='free'){
//	   $mprice	=	'free';
//	   }
//	   else{
	 //  $mprice	=	$_POST['mcostum_price_'.$z];
	//   }
	   
	//   }
//	   else{
	   $mtitle	=	$ro['title'];
	   $mprice	=	$ro['price'];
//	   }
	   
	?>
	 
      <div id="addrow<?php echo $next; ?>" style=" margin-bottom: 10px; background:<?php
		
		if($mtitle!='' && $mprice=='' || $mtitle=='' && $mprice!=''){
		echo "#FFFFE3;border:#ECC6B3";
		}
		else{
		echo "#FBFBFB;border:#F2F1F1";
		}
		?> solid 1px;width: 74%;">
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
        <div class="evLabal" style="width:374px">
          <input type="text" style="width:280px" value="<?php echo $mtitle; ?>" class="inp2" name="mtitle[]" id="mtitle<?php echo $next; ?>" />
        </div>
        <div style="float:left"><img src="<?=IMAGE_PATH;?>delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow(<?php echo $next; ?>);"></div>
        <div class="clr"></div>
        <div class="evField">Price:<b class="clr">*</b></div>
        <div class="evLabal" style="width:410px;">
          <label>
          <input type="radio" id="cstm<?php echo $i; ?>" name="mprice_<?php echo $z; ?>" value="costum_price"  <?php if (is_numeric(trim($mprice))){ echo 'checked="checked"'; }?> onclick="prc(this.value,<?php echo $i; ?>);">
          <input type="text" class="inp2" name="mcostum_price_<?php echo $z; ?>" value="<?php if (is_numeric(trim($mprice))){ echo trim($mprice); }?>" id="costum_price<?php echo $i; ?>" onkeypress="return isNumberKey(event)" style="width:60px;">
          </label>
		  <br />
          <label>
          <input type="radio" id="free<?php echo $next; ?>" name="mprice_<?php echo $z; ?>" value="free" <?php if (!is_numeric(trim($mprice))){ echo 'checked="checked"'; }?> onChange="prc(this.value,<?php echo $i; ?>);">
          Free </label>
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
	 </td></tr>
	 <tr>
	 <td>&nbsp;</td>
	 <td> <span id="add_more_btn_1"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newRow(<?php if ($nowStartTo){ echo $nowStartTo;} else{ echo '0'; } ?>);">&nbsp;&nbsp;Add More</span></span> </td></tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Quantity Available:<b class="clr">*</b></td>
      <td width="80%" align="left" class="bc_input_td"><input type="text" class="inp2" name="quantity_available"
	  value="<?php if ($_POST["submit"]){ echo $_POST['quantity_available']; } else{ echo $quantity_available; }?>" style="width:60px;"></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Start Sales:</td>
      <td width="80%" align="left" class="bc_input_td"><label for="specify_day">
        <input type="radio" id="specify_day" value="specify_day" <?php if ($start_sales_date!='0000-00-00'){ echo "checked='checked'";} ?> name="start_sales" onChange="timeBeforeEventStart(this.value);">
        Specify day
        <input type="text" class="inp2" name="start_sales_date" <?php if ($start_sales_date=='0000-00-00'){ echo "disabled='disabled'";} ?> 
		value="<?php if ($start_sales_date=='0000-00-00'){echo '';}else{ echo date('d-M-Y', strtotime($start_sales_date)); } ?>" readonly="" id="start_sales_date" style="width:76px; color:#000000; cursor:pointer">
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
        <div class="clr"></div>
        <label for="time_before">
        <input type="radio" id="time_before" value="time_before" name="start_sales" <?php if ($start_sales_before_days!='0'){ echo 'checked="checked"';} ?> onChange="timeBeforeEventStart(this.value);">
        Time before event starts
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($start_sales_before_days!=0){echo $start_sales_before_days;} ?>" <?php if($start_sales_before_days==0){ echo 'disabled="disabled"';} ?> name="start_sales_before_days" id="start_sales_before_days">
        Days &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" name="start_sales_before_hrs" value="<?php if($start_sales_before_hours!=0){echo $start_sales_before_hours;} ?>" <?php if($start_sales_before_hours==0){ echo 'disabled="disabled"';} ?> id="start_sales_before_hrs">
        Hours &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" name="start_sales_before_min" value="<?php if($start_sales_before_minutes!=0){echo $start_sales_before_minutes;} ?>" <?php if($start_sales_before_minutes==0){ echo 'disabled="disabled"';} ?> id="start_sales_before_min">
        Minutes &nbsp;</label></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">End Sales:</td>
      <td width="80%" align="left" class="bc_input_td"><label for="specify_day2">
        <input type="radio" id="specify_day2" name="end_sales" <?php if ($end_sales_date!='0000-00-00'){ echo "checked='checked'";} ?> value="specify_day2" onChange="timeBeforeEventEnd(this.value);">
        Specify day
        <input type="text" class="inp2" <?php if ($end_sales_date=='0000-00-00'){ echo "disabled='disabled'";} ?> value="<?php if ($end_sales_date=='0000-00-00'){echo '';}else{ echo $end_sales_date; } ?>" readonly="" name="end_sales_date" id="end_sales_date" style="width:76px; cursor:pointer">
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
        <div class="clr"></div>
        <label for="time_before2">
        <input type="radio" id="time_before2" name="end_sales"  <?php if ($end_sales_before_days!='0'){ echo 'checked="checked"';} ?>  value="time_before2" onChange="timeBeforeEventEnd(this.value);">
        Time before event ends
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($start_sales_before_days!=0){echo $end_sales_before_days;} ?>" <?php if($end_sales_before_days==0){ echo 'disabled="disabled"';} ?> name="end_sales_before_days" id="end_sales_before_days">
        Days &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_hours!=0){echo $end_sales_before_hours;} ?>" <?php if($end_sales_before_hours==0){ echo 'disabled="disabled"';} ?>   name="end_sales_before_hrs" id="end_sales_before_hrs">
        Hours &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_minutes!=0){echo $end_sales_before_minutes;} ?>" <?php if($end_sales_before_minutes==0){ echo 'disabled="disabled"';} ?>  name="end_sales_before_min" id="end_sales_before_min">
        Minutes &nbsp;</label></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Min, Tickets Order:</td>
      <td width="80%" align="left" class="bc_input_td"><input type="text" class="inp2" style="width:60px;" name="min_tickets"
	  value="<?php if ($_POST['submit']){ echo $_POST['min_tickets']; } else{ if($min_tickets_order!=0){ echo $min_tickets_order;} }?>"></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Max, Tickets Order:</td>
      <td width="80%" align="left" class="bc_input_td"><input type="text" class="inp2" style="width:60px;" name="max_tickets" value="<?php if ($_POST['submit']){ echo $_POST['max_tickets']; } else{ if($max_tickets_order!=0){ echo $max_tickets_order;}}?>"></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Ticket Description:</td>
      <td width="80%" align="left" class="bc_input_td"><textarea id="ticket_description" name="ticket_description" class="inp" style="height:35px; width:410px; padding:3px; margin:0" onFocus="removeText(this.value,'Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');" onBlur="returnText('Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');"><?php if ($_POST['submit']){ echo $_POST['ticket_description']; } else{ if ($ticket_description){ echo $ticket_description;	} else{ echo "Access for one person to the launch celebration for the new website Eventgrabber"; } }?>
</textarea></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Service Fee:</td>
      <td width="80%" align="left" class="bc_input_td"><label for="add">
        <input type="radio" name="service_fee" <?php if (trim($servise_fee)=='ADD fees on top of total ticket price'){ echo 'checked="checked"';} ?> id="add" value="ADD fees on top of total ticket price">
        ADD fees on top of total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a></label>
        <div class="clr"></div>
        <label for="include">
        <input type="radio" name="service_fee" <?php if (trim($servise_fee)=='INCLUDE fees into total ticket price'){ echo 'checked="checked"';} ?> value="INCLUDE fees into total ticket price" id="include">
        INCLUDE fees into total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a></label>
        <div class="clr"></div>
        <label for="include_and_add">
        <input type="radio" name="service_fee" id="include_and_add" <?php if (trim($servise_fee)=='INCLUDE credit card processing fee in the total ticket price and ADD the Eventgrabber fee on top of the total ticket price'){ echo 'checked="checked"';} ?>  value="INCLUDE credit card processing fee in the total ticket price and ADD the Eventgrabber fee on top of the total ticket price">
        INCLUDE credit card processing fee in the total ticket price and ADD the Eventgrabber fee on top of the total ticket price &nbsp; <a class="hint" href="<?php echo ABSOLUTE_PATH; ?>ajax/hint.php"><img src="<?php echo ABSOLUTE_PATH; ?>images/question_icon.gif" alt=""></a></label></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">&nbsp;</td>
      <td width="80%" align="left" class="bc_input_td">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><input name="submit" type="submit" value="Save" class="bc_button" id="submit" />
	  <input type="hidden" name="ticket_id" value="<?php echo $_REQUEST['id']; ?>" />
      </td>
    </tr>
  </table>
</form>
<?php include_once('footer.php');?>