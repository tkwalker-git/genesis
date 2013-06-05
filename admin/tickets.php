<?php

require_once("database.php"); 
require_once("header.php"); 

//$bc_source_id	=	"Admin-".rand();
$bc_event_source = 'Admin'; 

if(isset($_GET["id"]))
	$frmID	=	$_GET["id"];
	$ticket_id	=	$frmID;
	
if($_GET['event_id']){
	$event_id = $_GET['event_id'];
	$frmID = 	$gallery_id	=	getSingleColumn('id',"select * from `event_ticket` where `event_id`='$event_id'");
}


$action = "save";
$sucMessage = "";


$bc_userid				=	$_SESSION['LOGGEDIN_MEMBER_ID'];

$bc_event_music = array();

$already_uploaded = 0;

if (isset($_POST["submit"])) {
	$save_price='';
	$mainTitle		= $_POST["mainTitle"];
	$mainTitleOp	= $_POST['mainTitleOp'];
	$mainQty		= $_POST['mainQty'];
	
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
		$start_sales_time			=	"00:00:00";
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


	$event_name	=	$_POST['event_name'];
			
		if ( trim($mainTitle) == '' )
			$errors[] = 'Please enter Ticket Type';
		if	( trim($mainPrice) == '' )
			$errors[] = 'Please enter Ticket Price';
		if ( trim($mainQty) == '' )
			$errors[] = 'Please enter Quantity Available';
		if(	trim($bc_prometer_service_fee)=='' && trim($bc_buyer_service_fee)=='')
			$errors[] = 'Please enter Split Percent';
		if(($bc_prometer_service_fee+$bc_buyer_service_fee)!=100)
			$errors[] = 'Please enter Correct Percentage (Split Percent)';
	
	
		if ( count( $errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';
	}
	

	if (!count($errors)) {
	
		$mainTckId			= $_POST['mainTckId'];
		$mainDescription	= $_POST['mainDescription'];	
		$ticket_description = $mainDescription;
			
		if($frmID){
			$bc_event_ticket_id	= $frmID;
			$t_id = $bc_event_ticket_id;
			
			
			mysql_query("UPDATE `event_ticket` SET `quantity_available` = '$quantity_available', `start_sales_date` = '$start_sales_date', `start_sales_time` = '$start_sales_time', `end_sales_date` = '$end_sales_date', `end_sales_time` = '$end_sales_time', `ticket_description` = '$ticket_description', `buyer_event_grabber_fee` = '$bc_buyer_service_fee', `prometer_event_grabber_fee` = '$bc_prometer_service_fee' WHERE `id` = '$t_id'");
			$ticket_id = $t_id;
			if($mainTckId){
				$res = mysql_query("UPDATE `event_ticket_price` set  `title`='$mainTitle', `price`='$mainPrice', `desc`='$mainDescription', `qty`='$mainQty' WHERE `id`='$mainTckId'");
			}
			else{
				$res = mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`, `qty`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id', '$mainDescription', '$mainQty');");
			}
			
			if ($res) {
			 if ( is_array($_POST['ticketTypesOp']) ) {
				 
						for($i=0;$i< count($_POST['ticketTypesOp']); $i++) {
							
							if($_POST['ticketTypesOp'][$i] == 'Other')
								$title	= $_POST['ticketTypes'][$i];
							else
								$title	= $_POST['ticketTypesOp'][$i];
								
								$price				= $_POST['ticketPrices'][$i];
								$id					= $_POST['ticketIds'][$i];
								$delT				= $_POST['delT'][$i];
								$ticketDescription	= $_POST['ticketDescription'][$i];
								$ticketQty			= $_POST['ticketQty'][$i];
							
							if($id && $delT!=1){
								mysql_query("UPDATE `event_ticket_price` set  `title`='$title', `price`='$price', `desc`='$ticketDescription', `qty`='$ticketQty' WHERE `id`='$id'");
							} // if($id)
							elseif($delT==1){
								mysql_query("DELETE FROM `event_ticket_price` WHERE `id` = '$id'");
							}
							else{
								if($title!='' && $price!=''){
									mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`, `qty`) VALUES (NULL, '$title', '$price', '$ticket_id', '$ticketDescription', '$ticketQty')");
								} // END IF $title!='' && $price!=''
							} // END else
						} // END FOR
					} // END IF is_array($_POST['ticketTypesOp']
					$sucMessage = "Record Successfully updated.";
				}
				else {
				$sucMessage = "Error: Please try Later";
				} // end if res
				
				
		} // END if($frmID)
		else{
		
		$ticket_sql = "INSERT INTO `event_ticket` (`id`, `quantity_available`, `start_sales_date`, `start_sales_time`, `end_sales_date`, `end_sales_time`, `ticket_description`, `buyer_event_grabber_fee`, `prometer_event_grabber_fee`, `event_id`) VALUES (NULL, '$quantity_available', '$start_sales_date', '$start_sales_time', '$end_sales_date', '$end_sales_time', '$ticket_description', '$bc_buyer_service_fee', '$bc_prometer_service_fee', '$event_id')";
					$res = mysql_query($ticket_sql);
					$ticket_id	=	mysql_insert_id();
					
				if($ticket_id){		
					mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`, `qty`) VALUES (NULL, '$mainTitle', '$mainPrice', '$ticket_id', '$mainDescription', '$mainQty')");
					
					if ( is_array($_POST['ticketTypesOp']) ) {
						$ticketTypesOp ='';
						for($i=0;$i< count($_POST['ticketTypesOp']); $i++) {
							
							if($_POST['ticketTypesOp'][$i] == 'Other')
								$title		= $_POST['ticketTypes'][$i];
							else
								$title		= $_POST['ticketTypesOp'][$i];
								$price				= $_POST['ticketPrices'][$i];
								$ticketQty			= $_POST['ticketQty'][$i];
								
							
							if($title!='' && $price!=''){
								mysql_query("INSERT INTO `event_ticket_price` (`id`, `title`, `price`, `ticket_id`, `desc`, `qty`) VALUES (NULL, '$title', '$price', '$ticket_id', '$ticketDescription', '$ticketQty')");
							} // END IF $title!='' && $price!=''
						} // END FOR
					} // END IF is_array($_POST['ticketTypesOp']
					$sucMessage = "Record Successfully inserted.";
				} // END IF ($ticket_id)
				else{
					$sucMessage = "Error: Please try Later";
					}

		
		} // END else($frmID)
	} // END if (!count($errors))
	else{
		$sucMessage	=	$err;
	}
}

$sql	=	"select * from event_ticket where id='$frmID'";
$res	=	mysql_query($sql);
if ($res) {
	if ($rowTicketDetails = mysql_fetch_assoc($res) ) {
		 $quantity_available	=	$rowTicketDetails['quantity_available'];
			  $start_sales_date		=	$rowTicketDetails['start_sales_date'];
			  $end_sales_date		=	$rowTicketDetails['end_sales_date'];
			  $start_sales_time		=	$rowTicketDetails['start_sales_time'];
			  $end_sales_time		=	$rowTicketDetails['end_sales_time'];
			  $ticket_description	=	$rowTicketDetails['ticket_description'];
			  $mainTicketId			=	$rowTicketDetails['id'];
			  $buyer_ser_fee		=	$rowTicketDetails['buyer_event_grabber_fee'];
			  $prometer_ser_fee		=	$rowTicketDetails['prometer_event_grabber_fee'];
			  $event_id				=	$rowTicketDetails['event_id'];
		
		}}
		
 $resTicket = mysql_query("select * from `event_ticket_price` where `ticket_id`='$mainTicketId' && `ticket_id`!='' ORDER BY ID ASC LIMIT 0,1");
		  while($rowTicket = mysql_fetch_array($resTicket)){
		  $mainTitle			= $rowTicket['title'];
		  $mainPrice			= $rowTicket['price'];
		  $mainTckId			= $rowTicket['id'];
		  $ticket_description	= $rowTicket['desc'];
		  $mainQty				= $rowTicket['qty'];
	  }


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
	var new_url_feild = '<div id="addrow'+next_row+'" style=" margin-bottom: 5px; background:#FBFBFB; border:#F2F1F1 solid 1px;width:528px;"><div class="evFieldT">Ticket Type:<span style="color:#FF0000">*</span></div><div class="evLabalT" style="width:386px" ><select class="selectO" id="ticketTypesOp'+next_row+'" name="ticketTypesOp[]" onChange="showTh(this.value,\'title'+next_row+'\'),updateTable()"><option value="General Admission">General Admission</option><option value="VIP Admission">VIP Admission</option><option value="Other">Other</option></select><input type="text" style=" width:220px; display:none" onKeyUp="updateTable();" value="" class="new_input" name="ticketTypes[]" id="title'+next_row+'" /></div><div style="float:left"><img src="images/delete.png" style="cursor:pointer;padding:10px;" title="Delete" onclick="removeRow('+next_row+',\'no\'),updateTable(\'../ajax/load_ticket_price_table.php\');"></div><div class="clr"></div><div class="evFieldT">Price:<span style="color:#FF0000">*</span></div><div class="evLabalT" ><input type="text" class="new_input" name="ticketPrices[]" value="" id="costum_price'+next_row+'" onKeyUp="updateTable(),extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="width:60px;"></div><div class="clr"></div></div>';
	
	$('#add_url_ist').append(new_url_feild);
	$('#add_more_btn_1').html('<span style="cursor:pointer; font-size:13px; color:#0033CC" onclick="add_newRow('+next_row+');">&nbsp;&nbsp;<b>Add More</b></span>');
	
	  }
	  
function showTh(value,id){
	if(value == 'Other')
		$('#'+id).show();
	else
		$('#'+id).hide();
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

function showTh(value,id){
	if(value == 'Other')
		$('#'+id).show();
	else
		$('#'+id).hide();
	}
	
	
	
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
	var price = jQuery(this).val();
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


function add_anOtherTicket(id){
	var next_row = id+1;
	var new_url_feild = '<div id="trow'+next_row+'" style="border-bottom:#CCCCCC solid 1px;"><table width="100%"><tr><td width="16%" align="right" class="bc_label">Ticket Type:<b class="clr">*</b></td><td width="49%" align="left" class="bc_input_td"><select class="selectO" id="ticketTypesOp'+next_row+'" name="ticketTypesOp[]" onChange="showTh(this.value,\'title'+next_row+'\'),updateTable()"><option value="General Admission">General Admission</option><option value="VIP Admission">VIP Admission</option><option value="Other">Other</option></select><input type="text" style=" width:220px; display:none" onKeyUp="updateTable();" value="" class="new_input" name="ticketTypes[]" id="title'+next_row+'" /></td><td><td rowspan="5" valign="top"><table class="rightRows" width="100%" border="0" cellspacing="0" cellpadding="10"><tr bgcolor="#e4f0d8"><td><strong>Ticket Type</strong></td><td align="right"><span id="t_type'+next_row+'"></span></td></tr><tr bgcolor="#d1e5c0"><td>Initial Price</td><td align="right"><span id="t_price'+next_row+'"></span></td></tr><tr bgcolor="#e4f0d8"><td>Promoter Fees</td><td align="right"><span id="t_pfees'+next_row+'"></span></td></tr><tr bgcolor="d1e5c0"><td>Customer Fees</td><td align="right"><span id="t_cfees'+next_row+'"></span></td></tr><tr bgcolor="e4f0d8"><td><strong>Final Price</strong></td><td align="right"><span class="t_finalPrice" id="t_finalPrice'+next_row+'"></span></td></tr></table></td></td><tr><td align="right" class="bc_label">Price:</td><td align="left" class="bc_input_td"><input type="text" class="new_input" name="ticketPrices[]" value="" id="costum_price'+next_row+'" onKeyUp="updateTable(),extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="width:60px;"><div style="padding:10px; float: right; cursor:pointer"><img src="../images/ticket_remove.png" onClick="removeTicket('+next_row+');" /></div></td></tr><tr><td align="right" class="bc_label">Quantity Available:</td><td align="left" class="bc_input_td"><input type="text" class="new_input" name="ticketQty[]" onKeyPress="return isNumberKey(event)" value="" style="width:60px;" onKeyUp="updateTable()"></td></tr><tr><td align="right" class="bc_label">Ticket Description:</td><td align="left" class="bc_input_td"><textarea id="" name="ticketDescription[]" class="ticket_description"></textarea></td></tr></table></div>';
	$('#add_tckt').append(new_url_feild);
	$('#add_tc').html('<span style="cursor:pointer;  font-size:13px; color:#0033CC" onClick="add_anOtherTicket('+next_row+'),updateTable()"><strong>Add More</strong> 	</span>');
}

function removeTicket(id){
//	$('#trow'+id).remove();
	$('#trow'+id).css("display","none");
	$('#del'+id).val('1');
	var onclAttr = $('#add_tc').html();
	}
	
</script>
<style>

.rightRows td{
	}
	
	
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
    width: 77px;
}

.evLabalT
{
	float: left;
    padding: 8px 0;
	width:290px;
}
.clr{
	color:#FF0000
	}
#ui-datepicker-div {display:none;}

</style>
<form method="post" name="bc_form" enctype="multipart/form-data" action="" autocomplete="off" >
  <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
  <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center" bgcolor="#F3F6EA">
    <tr class="bc_heading">
      <td colspan="3" align="left">Add/Edit Ticket</td>
    </tr>
    <tr>
      <td colspan="3" align="center" class="success" ><?php echo $sucMessage; ?></td>
    </tr>
    <tr bgcolor="#F3F6EA">
      <td width="16%" height="53" align="right" class="bc_label">
	  	Event Name:
	  </td>
      <td width="49%" align="left" class="bc_input_td"><?php
	if(isset($_GET["id"]) || $_GET['event_id'])
	 	echo '<h3>'. getEventName($event_id) . '</h3>';
	 ?>
	 <input type="hidden" value="<?php echo $event_id;?>" id="event_id" name="event_id" />
      </td>
	  <td></td>
      
    </tr>
    <tr bgcolor="#F3F6EA">
      <td align="right" class="bc_label">Ticket Type:<b class="clr">*</b></td>
      <td align="left" class="bc_input_td"><input type="hidden" name="mainTckId" value="<?php echo $mainTckId; ?>" />
        <select class="selectO" id="mainTitleOp" name="mainTitleOp" onChange="showTh(this.value,'mainTitle'),updateTable()">
          <option value="General Admission" <?php if ($mainTitle=='General Admission'){ echo "selected='selected'"; } ?>>General Admission</option>
          <option value="VIP Admission" <?php if ($mainTitle=='VIP Admission'){ echo "selected='selected'"; } ?>>VIP Admission</option>
          <option value="Other" <?php if ($mainTitle!='VIP Admission' && $mainTitle!='General Admission' && $mainTitle!=''){ echo "selected='selected'"; } ?>>Other</option>
        </select>
        <input type="text" style="width:220px;<?php if ($mainTitle=='VIP Admission' || $mainTitle=='General Admission' || $mainTitle==''){ echo 'display:none'; } ?>" onKeyUp="updateTable();" value="<?php if ($_POST['create']){echo $_POST['mainTitle'];}else{ if ($mainTitle!='VIP Admission' && $mainTitle!='General Admission'){echo $mainTitle;} }?>" class="new_input" name="mainTitle" id="mainTitle" /></td>
		<td width="35%" rowspan="5" class="top">
	  <table width="100%" border="0" cellspacing="0" cellpadding="10" class="rightRows">
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
        </table></td>
    </tr>
    <tr bgcolor="#F3F6EA">
      <td align="right" class="bc_label">Price:<b class="clr">*</b></td>
      <td align="left" class="bc_input_td"><input type="text" class="new_input" name="mainPrice" value="<?php if ($_POST['create']){echo $_POST['mainPrice'];}else{ echo $mainPrice; }?>" id="mainPrice" onKeyUp="updateTable(),extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="width:60px;"></td>
    </tr>
	
	<tr>
      <td align="right" class="bc_label">Quantity Available:<b class="clr">*</b></td>
	  <td><input type="text" class="new_input" name="mainQty" onKeyPress="return isNumberKey(event)" value="<?php echo $mainQty; ?>" style="width:60px;" onKeyUp="updateTable()"></td>
	 </tr>		  
    <tr>
      <td align="right" class="bc_label">Ticket Description:</td>
      <td align="left" class="bc_input_td"><textarea id="mainDescription" name="mainDescription" class="ticket_description"><?php
	  	 echo $ticket_description;?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="3" style="border-bottom:#CCCCCC solid 1px;"></td>
    </tr>
    <tr>
      <td colspan="3" valign="top"><span id="add_tckt">
        <?php
				  $resTicket = mysql_query("select * from `event_ticket_price` where `ticket_id`='$mainTicketId' && `ticket_id`!=''");
				  $i=0;
				  $z=0;
				  $ticketNumRows = mysql_num_rows($resTicket);
				  while($rowTicket = mysql_fetch_array($resTicket)){
				  $i++;
				  if($i!=1){
				  $z++;
				  
				  $ticketType	= $rowTicket['title'];
				  $ticketPrice	= $rowTicket['price'];
				  $ticketIds	= $rowTicket['id'];
				  $ticketDescription	= $rowTicket['desc'];
				  $ticketQty			= $rowTicket['qty'];
				  
				  
				  $ticketSold	= getSingleColumn("id","select * from `order_tickets` where `ticket_id`='$ticketIds'");
				  
				  ?>
        <div id="trow<?php echo $z; ?>" style="border-bottom:#CCCCCC solid 1px;">
          <table width="100%">
            <tr>
              <td width="16%" align="right" class="bc_label">Ticket Type:<b class="clr">*</b></td>
              <td width="49%" align="left" class="bc_input_td"><input type="hidden" name="delT[]"  id="del<?php echo $z; ?>" />
                <input type="hidden" name="ticketIds[]" value="<?php echo $ticketIds; ?>" />
                <select class="selectO" id="ticketTypesOp<?php echo $z; ?>" name="ticketTypesOp[]"  onChange="showTh(this.value,'title<?php echo $z; ?>'),updateTable()">
                  <option value="General Admission" <?php if ($ticketType=='General Admission'){ echo "selected='selected'"; } ?>>General Admission</option>
                  <option value="VIP Admission" <?php if ($ticketType=='VIP Admission'){ echo "selected='selected'"; } ?>>VIP Admission</option>
                  <option value="Other" <?php if ($ticketType!='VIP Admission' && $ticketType!='General Admission' && $ticketType!=''){ echo "selected='selected'"; } ?>>Other</option>
                </select>
                <input type="text" style="width:220px;<?php if ($ticketType=='VIP Admission' || $ticketType=='General Admission' || $ticketType==''){ echo 'display:none'; } ?>" onKeyUp="updateTable();" value="<?php if ($ticketType!='VIP Admission' && $ticketType!='General Admission'){ echo $ticketType; }?>" class="new_input" name="ticketTypes[]" id="title<?php echo $z; ?>" />
              </td>
              <td rowspan="5" valign="top">
			  <table width="100%" border="0" cellspacing="0" cellpadding="10" class="rightRows">
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
                </table></td>
            </tr>
            <tr>
              <td align="right" class="bc_label">Price:</td>
              <td align="left" class="bc_input_td"><input type="text" class="new_input" name="ticketPrices[]" value="<?php echo $ticketPrice; ?>" id="costum_price<?php echo $z; ?>" onKeyUp="updateTable(),extractNumber(this,2,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" style="width:60px;">
                <div style="padding:10px; cursor:pointer; float:right">
                <img src="<?php echo IMAGE_PATH; ?>ticket_remove.png" <?php if ($ticketSold){?>onClick="alert('This ticket has been sold, So you cannot remove this ticket');"<?php }else{ ?>onClick="removeTicket(<?php echo $z; ?>);" <?php } ?> /> </td>
            </tr>
			<tr>
			  <td align="right" class="bc_label">Quantity Available:<b class="clr">*</b></td>
			  <td><input type="text" class="new_input" name="ticketQty[]" onKeyPress="return isNumberKey(event)" value="<?php echo $ticketQty; ?>" style="width:60px;" onKeyUp="updateTable()"></td>
			</tr>
			  
			<tr>
			<td align="right" class="bc_label">Ticket Description:</td>
      <td align="left" class="bc_input_td"><textarea id="" name="ticketDescription[]" class="ticket_description"><?php echo $ticketDescription; ?></textarea></td>
			</tr>
          </table>
        </div>
        <?php
				  
				  
				  }}
				  ?>
        </span> </td>
    </tr>
    <tr>
      <td align="right" class="bc_label" valign="top">Additional Packages:<br>
        <span id="add_tc"> <span style="cursor:pointer;  font-size:13px; color:#0033CC" onClick="add_anOtherTicket(<?php if($ticketNumRows){ echo $ticketNumRows-1;} else{ echo 0; }?>),updateTable()"> <strong>Add More</strong> </span> </span> </td>
      <!--<span id="add_more_btn_1"><span style="cursor:pointer; font-size:13px; color:#0033CC" onclick="add_newRow(<?php if ($nowStartTo){ echo $nowStartTo;} else{ echo '0'; } ?>);"><strong>Add More</strong></span></span>-->
      <td colspan="2"></td>
    </tr>
    <tr>
      <td colspan="3"><div class="ticket_advance_options" id="ticket_advance_options" style="">
	  
	  
          <div class="ev_fltlft" style="width:66%">
			  <div class="evField">&nbsp;</div>
			  <div class="evLabal" style="width:300px"><strong>Current Date/Time &nbsp; <?php echo date('d-M-Y')." &nbsp; ".date('h:i A'); ?></strong></div>
			  <div class="clr"></div>
					  
					  
            <div class="evField">Start Sale: </div>
            <div class="evLabal" style="width:320px">
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
            <div class="evLabal" style="width:320px">
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
          </div>
          <div class="split_fee">
            <input type="radio" name="split_fee" value="prometer" onChange="spltfee('b');" <?php if($prometer_ser_fee==0 && $buyer_ser_fee!=0){ echo 'checked="checked"'; } ?>  />
            Pass on fees to buyer<br />
            <br />
            <input type="radio" name="split_fee" value="buyer" onChange="spltfee('a');" <?php if($buyer_ser_fee==0 && $prometer_ser_fee!=0){ echo 'checked="checked"'; } ?> />
            Absorb the fees<br />
            <br />
            <input type="radio" name="split_fee" value="split" onChange="spltfee('s');" <?php if($event_id){if($buyer_ser_fee!=0 && $prometer_ser_fee!=0){ echo 'checked="checked"'; }} else{ echo 'checked="checked"'; } ?> />
            Split the fees <br />
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
            <div class="ev_fltlft" style="line-height:89px;"> <img src="<?php echo IMAGE_PATH; ?>percent.png" align="left" style="padding:9px 4px" /> <strong>Split Fee</strong> </div>
            <div class="clr"></div>
          </div>
          <div class="clr"></div>
        </div></td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="center"><input name="submit" type="submit" value="Save" class="bc_button" id="submit" />
        <input type="hidden" name="ticket_id" value="<?php echo $_REQUEST['id']; ?>" />
      </td>
    </tr>
	 <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
</form>
<?php include_once('footer.php');?>
<script>
updateTable('../ajax/load_ticket_price_table.php');
</script>
