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
	$mainTitle					=	$_POST["mainTitle"];
	$price						=	$_POST["price"];
	$event_id					=	$_POST["event_id"];
	$mainPrice					=	$_REQUEST['mainPrice'];
	$event_ticket_id			=	$_POST['event_ticket_id'];
	
	$bc_prometer_service_free	=	$_POST["prometer_service_free"];
	$bc_buyer_service_free		=	$_POST["buyer_service_free"];
	if($bc_prometer_service_free!='' && $bc_buyer_service_free==''){
	$bc_buyer_service_free		=	100-$bc_prometer_service_free;
	}elseif($bc_buyer_service_free!='' && $bc_prometer_service_free==''){
	$bc_prometer_service_free		=	100-$bc_buyer_service_free;
	}
	
	if($mainPrice=='costum_price'){
	$mainPrice					=	$_POST['costum_price'];
	}
	else{
	$mainPrice=$mainPrice;
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
	$start_sales_time			=	'';
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
	
	$event_name	=	$_POST['event_name'];
	
	if(!isset($_GET["id"])){
	if ( trim($event_name) == '' )
		$errors[] = 'Please enter Event Name';

	if ( trim($event_id) == '' )
		$errors[] = 'Event name not matched';
	}
	
	if ( trim($name) == '' )
		$errors[] = 'Please enter Ticket Name';
		
	if ( trim($mainTitle) == '' )
		$errors[] = 'Please enter Title';		
		
	if ( trim($quantity_available) == '' )
		$errors[] = 'Please enter Quantity Available';
		
	if(	trim($bc_prometer_service_free)=='' && trim($bc_buyer_service_free)=='')
		$errors[] = 'Please enter Split Percent';
	if(($bc_prometer_service_free+$bc_buyer_service_free)!=100)
$errors[] = 'Please enter Correct Percentage (Split Percent)';

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
	
	if($start_sales_date=='1970-01-01'){
	$start_sales_date='0000-00-00';
	}
	
	if($end_sales_date=='1970-01-01'){
	$end_sales_date='0000-00-00';
	}
	
 	$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

 if($action=='edit'){
	$sql = "UPDATE `event_ticket` SET `name` = '$name', `quantity_available` = '$quantity_available', `start_sales_date` = '$start_sales_date', `start_sales_time` = '$start_sales_time', `start_sales_before_days` = '$start_sales_before_days', `start_sales_before_hours` = '$start_sales_before_hrs', `start_sales_before_minutes` = '$start_sales_before_min', `end_sales_date` = '$end_sales_date', `end_sales_time` = '$end_sales_time', `end_sales_before_days` = '$end_sales_before_days', `end_sales_before_hours` = '$end_sales_before_hrs', `end_sales_before_minutes` = '$end_sales_before_min', `ticket_description` = '$ticket_description', `buyer_event_grabber_fee` = '$bc_buyer_service_free' , `prometer_event_grabber_fee` = '$bc_prometer_service_free' where `id` = '$ticket_id'";
	$res = mysql_query($sql);
	if($res){
	mysql_query("UPDATE `events` SET `event_cost` = '' WHERE `id` = '$event_id'");
	$sucMessage =	"Record Successfully updated.";
	}
	else{
	$sucMessage	=	"<span style='font-family:Arial, Helvetica, sans-serif;'><span style='font-size:18px;'>Error:</span> <span style='font-size:14px;'>Please try later</span></span>";
	}
	
	}
	if($action=='save'){
	$sql = "INSERT INTO `event_ticket` (`id`, `name`, `quantity_available`, `start_sales_date`, `start_sales_time`, `start_sales_before_days`, `start_sales_before_hours`, `start_sales_before_minutes`, `end_sales_date`, `end_sales_time`, `end_sales_before_days`, `end_sales_before_hours`, `end_sales_before_minutes`, `ticket_description`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$name', '$quantity_available', '$start_sales_date', '$start_sales_time', '$start_sales_before_days', '$start_sales_before_hrs', '$start_sales_before_min', '$end_sales_date', '$end_sales_time', '$end_sales_before_days', '$end_sales_before_hrs', '$end_sales_before_min', '$ticket_description', '$bc_buyer_service_free', '$bc_prometer_service_free', '$event_id');";

	$res = mysql_query($sql);
	$ticket_id	=	mysql_insert_id();
	if($res){
	mysql_query("UPDATE `events` SET `event_cost` = '' WHERE `id` = '$event_id'");
	$sucMessage	=	"Record Successfully inserted.";
	}
	else{
	$sucMessage	=	"<span style='font-family:Arial, Helvetica, sans-serif;'><span style='font-size:18px;'>Error:</span> <span style='font-size:14px;'>Please try later</span></span>";
	}
	}
	
	if($res){
	if($event_ticket_id){
	$qry2 	=	"UPDATE `event_ticket_price` SET `title` = '$mainTitle', `$mainPrice` = '$price' , `$ticket_id` = '$ticket_id'  WHERE `id` = '$event_ticket_id'";
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
		
		}}



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
</script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>/js/jquery-ui_1.8.7.js"></script>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/redmond/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-ui.multidatespicker.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/ev_functions.js"></script>
<script type="text/javascript">


$(document).ready(function() {
		$("#event_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>/get_event_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
//				alert(data);
	//			return false;
					if(data[0]){
					$("#event_id").attr("value", data[0]);
					$('#event_id').css('color', '#000');
					}
					if(data[2]){
					$("#ev_address1").attr("value", jQuery.trim(data[2]));
					$('#ev_address1').attr('readonly', 'readonly');
					}
					if(data[3]){
					$("#ev_city").attr("value", jQuery.trim(data[3]));
					$('#ev_city').attr('readonly', 'readonly');
					}
					if(data[4]){
					$("#ev_zip").attr("value", jQuery.trim(data[4]));
					$('#ev_zip').attr('readonly', 'readonly');
					
					}
				}
			}).setOptions({
				max: '100%'
			});
});


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
	
	function add_newRow(id){
	var next_row = id+1;
	var id2 = id;
	var new_url_feild = '<div id="addrow'+next_row+'" style=" margin-bottom: 5px; background:#FBFBFB; border:#F2F1F1 solid 1px;width:410px;"><div class="evFieldT">Title:<span style="color:#FF0000">*</span></div><div class="evLabalT" ><input type="text" onkeyup="updateTable(\'../ajax/load_ticket_price_table.php\');" style=" width:280px" value="" class="inp2" name="title[]" id="title'+next_row+'" /></div><div style="float:left"><img src="images/delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow('+next_row+',\'no\'),updateTable(\'../ajax/load_ticket_price_table.php\');"></div><div class="clr"></div><div class="evFieldT">Price:<span style="color:#FF0000">*</span></div><div class="evLabalT" ><input type="checkbox" onClick="fldDisabledFalse('+next_row+'),updateTable(\'../ajax/load_ticket_price_table.php\')" id="fld_'+next_row+'" name="price[]" value="costum_price"><input type="text" class="inp2" name="costum[]" onkeyup="updateTable(\'../ajax/load_ticket_price_table.php\'),extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" value="" id="costum_price'+next_row+'" onFocus="slct('+next_row+');" style="width:60px;"><input type="hidden" name="ac[]" value="insert" /><input type="hidden" name="event_ticket_price_id[]"><input type="hidden" name="del[]" value="" id="del_'+next_row+'">&nbsp;&nbsp;&nbsp;<label><input type="checkbox" id="free'+next_row+'" onClick="rmv('+next_row+'),updateTable(\'../ajax/load_ticket_price_table.php\')" name="price[]" value="free"> Free </label></div><div class="clr"></div></div>';
	
	$('#add_url_ist').append(new_url_feild);
	$('#add_more_btn_1').html('<span style="cursor:pointer; font-size:13px; color:#0033CC" onclick="add_newRow('+next_row+');">&nbsp;&nbsp;<b>Add More</b></span>');
	
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
	

	function show(){
	document.getElementById('service_feeDiv').style.display='block';
	}
	
	function updateTable(ajax_page)      
	 {  
	 var prometer_service_free	=	$('#prometer_service_free').val();
	 var buyer_service_free		=	$('#buyer_service_free').val();
	 var costum_price1st		=	$('#costum_price1st').val();
	 var event_fee				=	$('#event_fee').val();
	 var mainTitle				=	$('#mainTitle').val();
	 var free1st				=	$('#free1st').attr('checked');
	 
	var additionalTitles		=	new Array();
	var additionalPrices		=	new Array();
	
	$('input[name=title[]]').each(function() {
	 		if($(this).val()!=''){
		  additionalTitles.push($(this).val());
		  }
	});
	
	$('input[name=costum[]]').each(function() {
	 	if($('input[name=title[]]').val()!=''){
		  additionalPrices.push($(this).val());
		  }
	});
	 
	 if(free1st==true){
	 costum_price1st	=	'Free';
	 }
	 
		 $.ajax({  
			type: "GET",  
			url: ajax_page,  
			data: "costum_price1st="+costum_price1st+"&prometer_service_free="+prometer_service_free+"&buyer_service_free="+buyer_service_free+"&event_fee="+event_fee+"&mainTitle="+mainTitle+"&additionalTitles="+additionalTitles+"&additionalPrices="+additionalPrices,  
			
			beforeSend: function(){
			$("#priceTable").html('<img src="../images/loading.gif">');
			},
			dataType: "text/html",  
			success: function(html){
			$("#priceTable").css('display','block');
			$("#priceTable").html(html);
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
		value = value.toFixed(2);
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
		value = value.toFixed(2);
			$('#prometer_service_free').val(value);
		}
	}
}
	
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

.evFieldT
{
	color: #666666;
    float: left;
    padding: 11px 7px 11px 0;
    text-align: right;
    width: 75px;
}

.evLabalT
{
	float: left;
    padding: 8px 0;
	width:290px;
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
      <td align="right" class="bc_label">Event Name:
        <?php
	if(!isset($_GET["id"])){?>
        <span class="red">*</span>
        <?php } ?></td>
      <td><?php
	if(!isset($_GET["id"])){?>
        <input type="text" style="width:410px;" class="inp2" value="<?php echo $event_name; ?>" name="event_name" id="event_name" />
        <input type="hidden" value="<?php echo $event_id;?>" id="event_id" name="event_id" />
        <br />
        <small>Start Typing Event Name</small>
        <?php }
	 else{
	 	echo '<h3>'. getEventName($event_id) . '</h3>';
	 }
	 ?>
      </td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Ticket Name:<span class="red">*</span></td>
      <td width="80%" align="left" class="bc_input_td"><input type="text" maxlength="100" value="<?php if ($_POST['submit']){ echo $_POST['name']; } else{ echo $name; }?>" name="name" style="width:410px;" class="inp2">
        <br>
        <small>Examples: Member, Non-member, Student, Early Bird</small> </td>
    </tr>
    <?php
	   $r = mysql_query("select * from `event_ticket_price` where `ticket_id`='$frmID' && `ticket_id`!='0' ORDER BY `id` ASC LIMIT 0,1");
	   while($ro = mysql_fetch_array($r)){
	   $mainTitle		=	$ro['title'];
	   $mainPrice		=	$ro['price'];
	   $event_ticket_id	=	$ro['id'];
	   
	   }
	?>
    <tr>
      <td align="right" class="bc_label">Title:<span class="red">*</span></td>
      <td><input type="text" style=" width:280px" onkeyup="updateTable('../ajax/load_ticket_price_table.php');"  value="<?php echo $mainTitle; ?>" class="inp2" name="mainTitle" id="mainTitle" /></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Price:<span class="red">*</span></td>
      <td width="80%" align="left" class="bc_input_td">
        <input type="checkbox"  onClick="fldDisabledFalse('1st'),updateTable('../ajax/load_ticket_price_table.php')" id="fld_1st" <?php if (is_numeric(trim($mainPrice))){ echo 'checked="checked"'; }?> value="costum_price" name="mainPrice">
        <label for="fld_1st"><input type="text" class="inp2" name="costum_price" onkeyup="updateTable('../ajax/load_ticket_price_table.php'),extractNumber(this,2,true);" value="<?php if (is_numeric(trim($mainPrice))){ echo trim($mainPrice); }?>" id="costum_price1st" style="width:60px;" onkeypress="return blockNonNumbers(this, event, true, true);">
        <input type="hidden" name="event_ticket_id" value="<?php echo $event_ticket_id; ?>" />
        </label>
        <label for="free">
        <input type="checkbox"  onchange="updateTable('../ajax/load_ticket_price_table.php');" id="free1st" <?php if (!is_numeric(trim($mainPrice)) && $mainPrice!=''){ echo 'checked="checked"'; }?>  onClick="rmv('1st')" name="mainPrice" value="free">
        Free </label>
      </td>
    </tr>
    <tr>
	<?php
	   $r = mysql_query("select * from `event_ticket_price` where `ticket_id`='$ticket_id' && `ticket_id`!='0' ORDER BY `id` ASC");
	   $i=0;
	   $numrows	=	mysql_num_rows($r);
	   $nowStartTo=$numrows;
	   $z=0;
	 ?>
      <td align="right" class="bc_label" valign="top"> Additional Packages: <br>
        <span id="add_more_btn_1"><span style="cursor:pointer; font-size:13px; color:#0033CC" onclick="add_newRow(<?php if ($nowStartTo){ echo $nowStartTo;} else{ echo '0'; } ?>);"><strong>Add More</strong></span></span> </td>
      <td valign="top" ><?php
	   while($ro = mysql_fetch_array($r)){
	   $i++;
	   $next = $i;
	   if($i!=1){
	   $title					=	$ro['title'];
	   $price					=	$ro['price'];
	   $event_ticket_price_id	=	$ro['id'];
	?>
        <div id="addrow<?php echo $next; ?>" style=" margin-bottom: 5px; background:#FBFBFB;border:#F2F1F1 solid 1px;width: 410px;">
          <div class="evFieldT">Title:<b class="red">*</b></div>
          <input type="hidden" name="hdErr[]" value="1" id="hdd<?php echo $next; ?>" />
          <div class="evLabalT" >
            <input type="text" style="width:280px" onkeyup="updateTable('../ajax/load_ticket_price_table.php');" value="<?php echo $title; ?>" class="inp2" name="title[]" id="title<?php echo $i; ?>" />
          </div>
          <div style="float:left"><img src="<?=IMAGE_PATH;?>delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow(<?php echo $next; ?>,'yes');"></div>
          <div class="clr"></div>
          <div class="evFieldT">Price:<b class="red">*</b></div>
          <div class="evLabalT" >
            <input type="checkbox" onClick="fldDisabledFalse('<?php echo $i; ?>'),updateTable('../ajax/load_ticket_price_table.php')" id="fld_<?php echo $i; ?>" <?php if (is_numeric(trim($price))){ echo 'checked="checked"'; }?>  name="price[]" value="costum_price">
            <input type="text" class="inp2" name="costum[]" value="<?php if (is_numeric(trim($price))){ echo trim($price); }?>" id="costum_price<?php echo $i; ?>" onFocus="slct(<?php echo $i; ?>);" onkeyup="updateTable('../ajax/load_ticket_price_table.php'),extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" style="width:60px;">
            <input type="hidden" name="ac[]" value="update" />
            <input type="hidden" name="event_ticket_price_id[]" value="<?php echo $event_ticket_price_id; ?>" />
            <input type="hidden" name="del[]" value="" id="del_<?php echo $i; ?>" />
            &nbsp;&nbsp;&nbsp;
            <label>
            <input type="checkbox" id="free<?php echo $i; ?>" <?php if (!is_numeric(trim($price)) && $price!=''){ echo 'checked="checked"'; }?>  onClick="rmv(<?php echo $i; ?>),updateTable('../ajax/load_ticket_price_table.php')" name="price[]" value="free">
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
        <div id="add_url_ist"></div></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label">Quantity Available:<span class="red">*</span></td>
      <td width="80%" align="left" class="bc_input_td"><input type="text" class="inp2" name="quantity_available"
	  value="<?php if ($_POST["submit"]){ echo $_POST['quantity_available']; } else{ echo $quantity_available; }?>" onkeypress="return isNumberKey(event)" style="width:60px;"></td>
    </tr>
   
    <tr>
      <td width="20%" align="right" class="bc_label">Start Sales:</td>
      <td width="80%" align="left" class="bc_input_td"><label for="specify_day">
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
        <div class="clr" style="height:5px"></div>
        <label for="time_before">
        <input type="radio" id="time_before" value="time_before" name="start_sales" <?php if ($start_sales_before_days!='0' && $start_sales_before_days!=''){ echo 'checked="checked"';} ?> onChange="timeBeforeEventStart(this.value);">
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
        <div class="clr" style="height:5px"></div>
        <label for="time_before2">
        <input type="radio" id="time_before2" name="end_sales"  <?php if ($end_sales_before_days!='0' && $end_sales_before_days!=''){ echo 'checked="checked"';} ?>  value="time_before2" onChange="timeBeforeEventEnd(this.value);">
        Time before event ends
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_days!=0){echo $end_sales_before_days;} ?>" <?php if($end_sales_before_days==0){ echo 'disabled="disabled"';} ?> name="end_sales_before_days" id="end_sales_before_days">
        Days &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_hours!=0){echo $end_sales_before_hours;} ?>" <?php if($end_sales_before_hours==0){ echo 'disabled="disabled"';} ?>   name="end_sales_before_hrs" id="end_sales_before_hrs">
        Hours &nbsp;
        <input type="text" class="inp2" style="width:30px;" onkeypress="return isNumberKey(event)" value="<?php if($end_sales_before_minutes!=0){echo $end_sales_before_minutes;} ?>" <?php if($end_sales_before_minutes==0){ echo 'disabled="disabled"';} ?>  name="end_sales_before_min" id="end_sales_before_min">
        Minutes &nbsp;</label></td>
  </tr>
  <tr>
      <td width="20%" align="right" class="bc_label">Ticket Description:</td>
      <td width="80%" align="left" class="bc_input_td"><textarea id="ticket_description" name="ticket_description" class="inp" style="height:35px; width:410px; padding:3px; margin:0" onFocus="removeText(this.value,'Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');" onBlur="returnText('Access for one person to the launch celebration for the new website Eventgrabber','ticket_description');"><?php if ($_POST['submit']){ echo $_POST['ticket_description']; } else{ if ($ticket_description){ echo $ticket_description;	} else{ echo "Access for one person to the launch celebration for the new website Eventgrabber"; } }?>
</textarea></td>
    </tr>
    <tr>
      <td width="20%" align="right" class="bc_label" valign="top"><br />&nbsp;</td>
      <td width="80%" align="left" class="bc_input_td"><div class="evLabal">
			<input type="hidden" name="event_fee" id="event_fee" value="<?php echo $event_fee; ?>" />
            <strong>Split fee</strong> : Promoter Pays
            <input type="text" class="new_input" style="width:56px;" name="prometer_service_free" id="prometer_service_free" 
onkeyup="updateTable('../ajax/load_ticket_price_table.php'),extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" onblur="splitFee(this.value,'p'),updateTable('../ajax/load_ticket_price_table.php');" value="<?php if ($bc_prometer_service_free || $bc_prometer_service_free=='0'){ echo $bc_prometer_service_free; } else{ echo "50" ;} ?>" />

            % Customer Pays

<input type="text" name="buyer_service_free" id="buyer_service_free" value="<?php if ($bc_buyer_service_free || $bc_buyer_service_free=='0'){ echo $bc_buyer_service_free; } else{ echo "50" ;} ?>" onkeyup="updateTable('../ajax/load_ticket_price_table.php'),extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);"  onblur="splitFee(this.value,'c'),updateTable('../ajax/load_ticket_price_table.php')" class="new_input" style="width:56px;" />

            %
			<br />
            <br />
          </div></td>
    </tr>
	<tr>
	<td colspan="2">
	<div>
            <div class="ev_fltlft" style="width:195px; padding-right:10px"><strong>Ticket Title</strong></div>
            <div class="ev_fltlft" style="width:133px; padding-right:10px; text-align:center"><strong>Initial Ticket Price</strong></div>
			<div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center"><strong>Promoter Fees</strong></div>
            <div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center"><strong>Customer Fees</strong></div>
            <div class="ev_fltlft" style="width:143px; padding-right:10px; text-align:center"><strong>Final Ticket Price</strong></div>
            <div class="clr" style="height:10px"></div>
            <span id="priceTable"></span>
          </div>
		 </td>
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
<script>
updateTable('../ajax/load_ticket_price_table.php');
</script>