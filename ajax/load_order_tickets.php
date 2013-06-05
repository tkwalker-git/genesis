<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

?>
<script language="javascript">
$(document).ready(function(){
$('#submit').css("cursor","pointer");
$('#submit').click(function(){

	var country			=	$('#bill_country').val();
	var address			=	$('#b_address').val();
	var city			=	$('#b_city').val();
	var state			=	$('#b_state').val();
	var name			=	$('#name').val();
	var cardType		=	$('#cardType').val();
	var month			=	$('#month').val();
	var year			=	$('#year').val();
	var number			=	$('#number').val();
	var securityCode	=	$('#securityCode').val();
	if(country==''){
		alert('Country is required');
		$('#bill_country').focus();
	return false;
	}
	if(address==''){
		alert('Address is required');
		$('#b_address').focus();
	return false;
	}
	if(city==''){
		alert('City is required');
		$('#b_city').focus();
	return false;
	}
	if(state==''){
		alert('State / Province is required');
		$('#b_state').focus();
	return false;
	}
	if(name==''){
		alert('Cardholder name is required');
		$('#name').focus();
	return false;
	}
	if(cardType==''){
		alert('Credit Card Type is required');
		$('#cardType').focus();
	return false;
	}

	var values = new Array();
	
		values[0]	=	country;
		values[1]	=	address;
		values[2]	=	city;
		values[3]	=	state;
		values[4]	=	name;
		values[5]	=	cardType;
		values[6]	=	month;
		values[7]	=	year;
		values[8]	=	number;
		values[9]	=	securityCode;
		
	var ids		=	new Array();
	var qtys	=	new Array();
	var dates	=	new Array();

jQuery.each(jQuery("input[name='id[]']"), function() {
	 		if($(this).val()!=''){
		ids.push(jQuery(this).val());
		  }
	});

jQuery.each(jQuery("input[name='qty[]']"), function() {
	 		if($(this).val()!=''){
		qtys.push(jQuery(this).val());
		  }
	});
	
jQuery.each(jQuery("input[name='date[]']"), function() {
	 		if($(this).val()!=''){
		dates.push(jQuery(this).val());
		  }
	});
	
var event_id = $('#event_id').val();

  $.ajax({  
			type: "GET",  
			url: abs_url+"ajax/load_submit_tickets_order.php",
			data: "values=" + values + "&ids=" + ids + "&qtys=" +qtys + "&dates=" +dates + "&event_id=" +event_id,
			dataType: "text/html",  
			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
			$("#message").html(html);
			}
			, 

			complete: function()
			{
				hideOverlayer();
			}
	   	});


});
});

</script>
<div>
  <h3> <u>Order Summary</u></h3>
</div>
<?php
$res = mysql_query("select * from `event_ticket` where `event_id`='$bc_event_id'");
while($row = mysql_fetch_array($res)){
	$ticket_name			=	$row['name'];
	$ticket_description		=	$row['ticket_description'];
	$bc_service_fee_type	=	$row['service_fee_type'];
	$bc_service_fee			=	$row['service_fee'];
}
if($bc_service_fee_type==1 || $bc_service_fee_type==3){
echo "<div class='evField' style='width:190px;'>Service Fee:</div><div class='evLabal' style='padding:11px 0 0 0'> $".$bc_service_fee."</div>";
}
?>
<table cellpadding="0" cellspacing="0" border="1" bordercolor="#E1E1E1" style="color:#FFFFFF" width="100%" align="center">
  <tr bgcolor="#383838">
    <td width="41%" height="30" align="center"><strong>Ticket Title</strong></td>
    <td width="16%" align="center"><strong>Price</strong></td>
    <?php
			if ($bc_service_fee_type==2){?>
    <td width="10%" align="center"><strong>Service Fee</strong></td>
    <?php } ?>
    <td width="9%" align="center"><strong>Quantity</strong></td>
    <td width="13%" align="center"><strong>Date</strong></td>
    <td width="11%" align="center"><strong>Total</strong></td>
  </tr>
  <input type="hidden" name="event_id" value="<?php echo $_POST['event_id']; ?>" />
  <?php
	$bc_event_id	=	$_GET['event_id'];
	$id				=	$_GET['ids'];
	$qty			=	$_GET['qtys'];
	$date			=	$_GET['dates'];
		
	$ids	=	explode(",", $id);
	$qtys	=	explode(",", $qty);
	$dates	=	explode(",", $date);

	$total_price	=	0;
	for($i=0;$i< count($ids); $i++) {
		$id		=	$ids[$i];
		$qty	=	$qtys[$i];
		$total_price='';
	if($qty!='' && $qty!=0){
	$res = mysql_query("select * from `event_ticket_price` where `id`='$id'");
	while($row = mysql_fetch_array($res)){	
		$price = $row['price'];
?>
  <tr style="color:#000000">
    <td width="41%" height="30" align="center">
	<input type="hidden" name="id[]" value="<?php echo $row['id'];?>" />
	<?php echo $row['title']; ?></td>
    <td width="16%" align="center"><?php if(is_numeric($row['price'])){ echo "$".$row['price'];} else{ echo $row['price']; } ?>
      <br />
      <small>+ $
      <?php
			
			$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$bc_event_id");
			
			$buyer_service_free_after_percent		=	$row['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
			$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
			echo	$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');?>
      Service Charge</small></td>
    <?php
			if ($bc_service_fee_type==2){?>
    <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><?php echo "$".$bc_service_fee; ?></td>
    <?php } ?>
    <td width="9%" align="center">
	<input type="hidden" name="qty[]" value="<?php echo $qty; ?>"><?php echo $qty; ?></td>
    <td width="13%" align="center">
	<input type="hidden" name="date[]" value="<?php echo date('d M Y', strtotime($dates[$i])); ?>">
	
	<?php echo $dates[$i]; ?></td>
    <td width="11%" align="center"><?php
			  if($bc_service_fee_type==2){
			  $ml = $qty*$row['price'];
			 // echo "$"; echo ($ml+$bc_service_fee)+($finalServiceCharges);
			  //echo "$"; echo $ml+$bc_service_fee;
			  $ttl	=	$ml+$bc_service_fee;
			  }
			  else{
			 // echo "$".($qty*$row['price'])+($finalServiceCharges);
			 $ttl	=	$qty*$row['price'];
			  }
			  echo "$";
			  echo $fnl	= number_format($ttl+($finalServiceCharges*$qty), 2,'.',''); ?></td>
  </tr>
  <?php
			$finalServiceChargesTotal	=	$finalServiceChargesTotal+$fnl;
	if($bc_service_fee_type==2){
	$ml = $qty*$row['price'];
	$tot = $ml+$bc_service_fee;
	$total = $tot+$total;
	}
	else{
	$total = $qty*$row['price']+$total;
	}
	}}
	}
?>
  <tr style="color:#000000">
    <td colspan="6" align="right" style="padding:10px;">Total Amount Due:&nbsp; &nbsp;
      <?php
						
if($bc_service_fee_type==1 || $bc_service_fee_type==3){

$totalAmountDue	=	number_format($bc_service_fee+$finalServiceChargesTotal, 2,'.','');
}
else{
$totalAmountDue	=	number_format($finalServiceChargesTotal, 2,'.','');
}
echo "$".$totalAmountDue;
?></td>
</table>
<div>
  <h3> <u>Billing Information</u> </h3>
</div>
<div class="evField" style="width:121px">Country <font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <select  class="inp" style="width:229px" name="b_country" id="bill_country">
    <option value="">Select a Country</option>
    <option <?php if ($b_country=='AF'){ echo 'selected="selected"'; } ?> value="AF">Afghanistan</option>
    <option <?php if ($b_country=='AX'){ echo 'selected="selected"'; } ?> value="AX">Aland Islands</option>
    <option <?php if ($b_country=='DZ'){ echo 'selected="selected"'; } ?> value="AL">Albania</option>
    <option <?php if ($b_country=='AF'){ echo 'selected="selected"'; } ?> value="DZ">Algeria</option>
    <option <?php if ($b_country=='AS'){ echo 'selected="selected"'; } ?> value="AS">American Samoa</option>
    <option <?php if ($b_country=='AD'){ echo 'selected="selected"'; } ?> value="AD">Andorra</option>
    <option <?php if ($b_country=='AO'){ echo 'selected="selected"'; } ?> value="AO">Angola</option>
    <option <?php if ($b_country=='AI'){ echo 'selected="selected"'; } ?> value="AI">Anguilla</option>
    <option <?php if ($b_country=='AQ'){ echo 'selected="selected"'; } ?> value="AQ">Antarctica</option>
    <option <?php if ($b_country=='AG'){ echo 'selected="selected"'; } ?> value="AG">Antigua and Barbuda</option>
    <option <?php if ($b_country=='AR'){ echo 'selected="selected"'; } ?> value="AR">Argentina</option>
    <option <?php if ($b_country=='AM'){ echo 'selected="selected"'; } ?> value="AM">Armenia</option>
    <option <?php if ($b_country=='AW'){ echo 'selected="selected"'; } ?> value="AW">Aruba</option>
    <option <?php if ($b_country=='AU'){ echo 'selected="selected"'; } ?> value="AU">Australia</option>
    <option <?php if ($b_country=='AT'){ echo 'selected="selected"'; } ?> value="AT">Austria</option>
    <option <?php if ($b_country=='AZ'){ echo 'selected="selected"'; } ?> value="AZ">Azerbaijan</option>
    <option <?php if ($b_country=='BS'){ echo 'selected="selected"'; } ?> value="BS">Bahamas</option>
    <option <?php if ($b_country=='BH'){ echo 'selected="selected"'; } ?> value="BH">Bahrain</option>
    <option <?php if ($b_country=='BD'){ echo 'selected="selected"'; } ?> value="BD">Bangladesh</option>
    <option <?php if ($b_country=='BB'){ echo 'selected="selected"'; } ?> value="BB">Barbados</option>
    <option <?php if ($b_country=='BY'){ echo 'selected="selected"'; } ?> value="BY">Belarus</option>
    <option <?php if ($b_country=='BE'){ echo 'selected="selected"'; } ?> value="BE">Belgium</option>
    <option <?php if ($b_country=='BZ'){ echo 'selected="selected"'; } ?> value="BZ">Belize</option>
    <option <?php if ($b_country=='BJ'){ echo 'selected="selected"'; } ?> value="BJ">Benin</option>
    <option <?php if ($b_country=='BM'){ echo 'selected="selected"'; } ?> value="BM">Bermuda</option>
    <option <?php if ($b_country=='BT'){ echo 'selected="selected"'; } ?> value="BT">Bhutan</option>
    <option <?php if ($b_country=='BO'){ echo 'selected="selected"'; } ?> value="BO">Bolivia</option>
    <option <?php if ($b_country=='BA'){ echo 'selected="selected"'; } ?> value="BA">Bosnia and Herzegovina</option>
    <option <?php if ($b_country=='BW'){ echo 'selected="selected"'; } ?> value="BW">Botswana</option>
    <option <?php if ($b_country=='BV'){ echo 'selected="selected"'; } ?> value="BV">Bouvet Island</option>
    <option <?php if ($b_country=='BR'){ echo 'selected="selected"'; } ?> value="BR">Brazil</option>
    <option <?php if ($b_country=='BQ'){ echo 'selected="selected"'; } ?> value="BQ">British Antarctic Territory</option>
    <option <?php if ($b_country=='IO'){ echo 'selected="selected"'; } ?> value="IO">British Indian Ocean Territory</option>
    <option <?php if ($b_country=='VG'){ echo 'selected="selected"'; } ?> value="VG">British Virgin Islands</option>
    <option <?php if ($b_country=='BN'){ echo 'selected="selected"'; } ?> value="BN">Brunei</option>
    <option <?php if ($b_country=='BG'){ echo 'selected="selected"'; } ?> value="BG">Bulgaria</option>
    <option <?php if ($b_country=='BF'){ echo 'selected="selected"'; } ?> value="BF">Burkina Faso</option>
    <option <?php if ($b_country=='BI'){ echo 'selected="selected"'; } ?> value="BI">Burundi</option>
    <option <?php if ($b_country=='KH'){ echo 'selected="selected"'; } ?> value="KH">Cambodia</option>
    <option <?php if ($b_country=='CM'){ echo 'selected="selected"'; } ?> value="CM">Cameroon</option>
    <option <?php if ($b_country=='CA'){ echo 'selected="selected"'; } ?> value="CA">Canada</option>
    <option <?php if ($b_country=='CT'){ echo 'selected="selected"'; } ?> value="CT">Canton and Enderbury Islands</option>
    <option <?php if ($b_country=='CV'){ echo 'selected="selected"'; } ?> value="CV">Cape Verde</option>
    <option <?php if ($b_country=='KY'){ echo 'selected="selected"'; } ?> value="KY">Cayman Islands</option>
    <option <?php if ($b_country=='CF'){ echo 'selected="selected"'; } ?> value="CF">Central African Republic</option>
    <option <?php if ($b_country=='TD'){ echo 'selected="selected"'; } ?> value="TD">Chad</option>
    <option <?php if ($b_country=='CL'){ echo 'selected="selected"'; } ?> value="CL">Chile</option>
    <option <?php if ($b_country=='CN'){ echo 'selected="selected"'; } ?> value="CN">China</option>
    <option <?php if ($b_country=='CX'){ echo 'selected="selected"'; } ?> value="CX">Christmas Island</option>
    <option <?php if ($b_country=='CC'){ echo 'selected="selected"'; } ?> value="CC">Cocos Islands</option>
    <option <?php if ($b_country=='CO'){ echo 'selected="selected"'; } ?> value="CO">Colombia</option>
    <option <?php if ($b_country=='KM'){ echo 'selected="selected"'; } ?> value="KM">Comoros</option>
    <option <?php if ($b_country=='CG'){ echo 'selected="selected"'; } ?> value="CG">Congo - Brazzaville</option>
    <option <?php if ($b_country=='CD'){ echo 'selected="selected"'; } ?> value="CD">Congo - Kinshasa</option>
    <option <?php if ($b_country=='CK'){ echo 'selected="selected"'; } ?> value="CK">Cook Islands</option>
    <option <?php if ($b_country=='CR'){ echo 'selected="selected"'; } ?> value="CR">Costa Rica</option>
    <option <?php if ($b_country=='HR'){ echo 'selected="selected"'; } ?> value="HR">Croatia</option>
    <option <?php if ($b_country=='CU'){ echo 'selected="selected"'; } ?> value="CU">Cuba</option>
    <option <?php if ($b_country=='CY'){ echo 'selected="selected"'; } ?> value="CY">Cyprus</option>
    <option <?php if ($b_country=='CZ'){ echo 'selected="selected"'; } ?> value="CZ">Czech Republic</option>
    <option <?php if ($b_country=='DK'){ echo 'selected="selected"'; } ?> value="DK">Denmark</option>
    <option <?php if ($b_country=='DJ'){ echo 'selected="selected"'; } ?> value="DJ">Djibouti</option>
    <option <?php if ($b_country=='DM'){ echo 'selected="selected"'; } ?> value="DM">Dominica</option>
    <option <?php if ($b_country=='DO'){ echo 'selected="selected"'; } ?> value="DO">Dominican Republic</option>
    <option <?php if ($b_country=='NQ'){ echo 'selected="selected"'; } ?> value="NQ">Dronning Maud Land</option>
    <option <?php if ($b_country=='TL'){ echo 'selected="selected"'; } ?> value="TL">East Timor</option>
    <option <?php if ($b_country=='EC'){ echo 'selected="selected"'; } ?> value="EC">Ecuador</option>
    <option <?php if ($b_country=='EG'){ echo 'selected="selected"'; } ?> value="EG">Egypt</option>
    <option <?php if ($b_country=='SV'){ echo 'selected="selected"'; } ?> value="SV">El Salvador</option>
    <option <?php if ($b_country=='GQ'){ echo 'selected="selected"'; } ?> value="GQ">Equatorial Guinea</option>
    <option <?php if ($b_country=='ER'){ echo 'selected="selected"'; } ?> value="ER">Eritrea</option>
    <option <?php if ($b_country=='EE'){ echo 'selected="selected"'; } ?> value="EE">Estonia</option>
    <option <?php if ($b_country=='ET'){ echo 'selected="selected"'; } ?> value="ET">Ethiopia</option>
    <option <?php if ($b_country=='FK'){ echo 'selected="selected"'; } ?> value="FK">Falkland Islands</option>
    <option <?php if ($b_country=='FO'){ echo 'selected="selected"'; } ?> value="FO">Faroe Islands</option>
    <option <?php if ($b_country=='FJ'){ echo 'selected="selected"'; } ?> value="FJ">Fiji</option>
    <option <?php if ($b_country=='FI'){ echo 'selected="selected"'; } ?> value="FI">Finland</option>
    <option <?php if ($b_country=='FR'){ echo 'selected="selected"'; } ?> value="FR">France</option>
    <option <?php if ($b_country=='GF'){ echo 'selected="selected"'; } ?> value="GF">French Guiana</option>
    <option <?php if ($b_country=='PF'){ echo 'selected="selected"'; } ?> value="PF">French Polynesia</option>
    <option <?php if ($b_country=='TF'){ echo 'selected="selected"'; } ?> value="TF">French Southern Territories</option>
    <option <?php if ($b_country=='FQ'){ echo 'selected="selected"'; } ?> value="FQ">French Southern and Antarctic Territories</option>
    <option <?php if ($b_country=='GA'){ echo 'selected="selected"'; } ?> value="GA">Gabon</option>
    <option <?php if ($b_country=='GM'){ echo 'selected="selected"'; } ?> value="GM">Gambia</option>
    <option <?php if ($b_country=='GE'){ echo 'selected="selected"'; } ?> value="GE">Georgia</option>
    <option <?php if ($b_country=='DE'){ echo 'selected="selected"'; } ?> value="DE">Germany</option>
    <option <?php if ($b_country=='GH'){ echo 'selected="selected"'; } ?> value="GH">Ghana</option>
    <option <?php if ($b_country=='GI'){ echo 'selected="selected"'; } ?> value="GI">Gibraltar</option>
    <option <?php if ($b_country=='GR'){ echo 'selected="selected"'; } ?> value="GR">Greece</option>
    <option <?php if ($b_country=='GL'){ echo 'selected="selected"'; } ?> value="GL">Greenland</option>
    <option <?php if ($b_country=='GD'){ echo 'selected="selected"'; } ?> value="GD">Grenada</option>
    <option <?php if ($b_country=='GP'){ echo 'selected="selected"'; } ?> value="GP">Guadeloupe</option>
    <option <?php if ($b_country=='GU'){ echo 'selected="selected"'; } ?> value="GU">Guam</option>
    <option <?php if ($b_country=='GT'){ echo 'selected="selected"'; } ?> value="GT">Guatemala</option>
    <option <?php if ($b_country=='GG'){ echo 'selected="selected"'; } ?> value="GG">Guernsey</option>
    <option <?php if ($b_country=='GN'){ echo 'selected="selected"'; } ?> value="GN">Guinea</option>
    <option <?php if ($b_country=='GW'){ echo 'selected="selected"'; } ?> value="GW">Guinea-Bissau</option>
    <option <?php if ($b_country=='GY'){ echo 'selected="selected"'; } ?> value="GY">Guyana</option>
    <option <?php if ($b_country=='HT'){ echo 'selected="selected"'; } ?> value="HT">Haiti</option>
    <option <?php if ($b_country=='HM'){ echo 'selected="selected"'; } ?> value="HM">Heard Island and McDonald Islands</option>
    <option <?php if ($b_country=='HN'){ echo 'selected="selected"'; } ?> value="HN">Honduras</option>
    <option <?php if ($b_country=='HK'){ echo 'selected="selected"'; } ?> value="HK">Hong Kong SAR China</option>
    <option <?php if ($b_country=='HU'){ echo 'selected="selected"'; } ?> value="HU">Hungary</option>
    <option <?php if ($b_country=='IS'){ echo 'selected="selected"'; } ?> value="IS">Iceland</option>
    <option <?php if ($b_country=='IN'){ echo 'selected="selected"'; } ?> value="IN">India</option>
    <option <?php if ($b_country=='ID'){ echo 'selected="selected"'; } ?> value="ID">Indonesia</option>
    <option <?php if ($b_country=='IR'){ echo 'selected="selected"'; } ?> value="IR">Iran</option>
    <option <?php if ($b_country=='IQ'){ echo 'selected="selected"'; } ?> value="IQ">Iraq</option>
    <option <?php if ($b_country=='IE'){ echo 'selected="selected"'; } ?> value="IE">Ireland</option>
    <option <?php if ($b_country=='IM'){ echo 'selected="selected"'; } ?> value="IM">Isle of Man</option>
    <option <?php if ($b_country=='IL'){ echo 'selected="selected"'; } ?> value="IL">Israel</option>
    <option <?php if ($b_country=='IT'){ echo 'selected="selected"'; } ?> value="IT">Italy</option>
    <option <?php if ($b_country=='CI'){ echo 'selected="selected"'; } ?> value="CI">Ivory Coast</option>
    <option <?php if ($b_country=='JM'){ echo 'selected="selected"'; } ?> value="JM">Jamaica</option>
    <option <?php if ($b_country=='JP'){ echo 'selected="selected"'; } ?> value="JP">Japan</option>
    <option <?php if ($b_country=='JE'){ echo 'selected="selected"'; } ?> value="JE">Jersey</option>
    <option <?php if ($b_country=='JT'){ echo 'selected="selected"'; } ?> value="JT">Johnston Island</option>
    <option <?php if ($b_country=='JO'){ echo 'selected="selected"'; } ?> value="JO">Jordan</option>
    <option <?php if ($b_country=='KZ'){ echo 'selected="selected"'; } ?> value="KZ">Kazakhstan</option>
    <option <?php if ($b_country=='KE'){ echo 'selected="selected"'; } ?> value="KE">Kenya</option>
    <option <?php if ($b_country=='KI'){ echo 'selected="selected"'; } ?> value="KI">Kiribati</option>
    <option <?php if ($b_country=='KW'){ echo 'selected="selected"'; } ?> value="KW">Kuwait</option>
    <option <?php if ($b_country=='KG'){ echo 'selected="selected"'; } ?> value="KG">Kyrgyzstan</option>
    <option <?php if ($b_country=='LA'){ echo 'selected="selected"'; } ?> value="LA">Laos</option>
    <option <?php if ($b_country=='LV'){ echo 'selected="selected"'; } ?> value="LV">Latvia</option>
    <option <?php if ($b_country=='LB'){ echo 'selected="selected"'; } ?> value="LB">Lebanon</option>
    <option <?php if ($b_country=='LS'){ echo 'selected="selected"'; } ?> value="LS">Lesotho</option>
    <option <?php if ($b_country=='LR'){ echo 'selected="selected"'; } ?> value="LR">Liberia</option>
    <option <?php if ($b_country=='LY'){ echo 'selected="selected"'; } ?> value="LY">Libya</option>
    <option <?php if ($b_country=='LI'){ echo 'selected="selected"'; } ?> value="LI">Liechtenstein</option>
    <option <?php if ($b_country=='LT'){ echo 'selected="selected"'; } ?> value="LT">Lithuania</option>
    <option <?php if ($b_country=='LU'){ echo 'selected="selected"'; } ?> value="LU">Luxembourg</option>
    <option <?php if ($b_country=='MO'){ echo 'selected="selected"'; } ?> value="MO">Macau SAR China</option>
    <option <?php if ($b_country=='MK'){ echo 'selected="selected"'; } ?> value="MK">Macedonia</option>
    <option <?php if ($b_country=='MG'){ echo 'selected="selected"'; } ?> value="MG">Madagascar</option>
    <option <?php if ($b_country=='MW'){ echo 'selected="selected"'; } ?> value="MW">Malawi</option>
    <option <?php if ($b_country=='MY'){ echo 'selected="selected"'; } ?> value="MY">Malaysia</option>
    <option <?php if ($b_country=='MV'){ echo 'selected="selected"'; } ?> value="MV">Maldives</option>
    <option <?php if ($b_country=='ML'){ echo 'selected="selected"'; } ?> value="ML">Mali</option>
    <option <?php if ($b_country=='MT'){ echo 'selected="selected"'; } ?> value="MT">Malta</option>
    <option <?php if ($b_country=='MH'){ echo 'selected="selected"'; } ?> value="MH">Marshall Islands</option>
    <option <?php if ($b_country=='MQ'){ echo 'selected="selected"'; } ?> value="MQ">Martinique</option>
    <option <?php if ($b_country=='MR'){ echo 'selected="selected"'; } ?> value="MR">Mauritania</option>
    <option <?php if ($b_country=='MU'){ echo 'selected="selected"'; } ?> value="MU">Mauritius</option>
    <option <?php if ($b_country=='YT'){ echo 'selected="selected"'; } ?> value="YT">Mayotte</option>
    <option <?php if ($b_country=='MX'){ echo 'selected="selected"'; } ?> value="MX">Mexico</option>
    <option <?php if ($b_country=='FM'){ echo 'selected="selected"'; } ?> value="FM">Micronesia</option>
    <option <?php if ($b_country=='MI'){ echo 'selected="selected"'; } ?> value="MI">Midway Islands</option>
    <option <?php if ($b_country=='MD'){ echo 'selected="selected"'; } ?> value="MD">Moldova</option>
    <option <?php if ($b_country=='MC'){ echo 'selected="selected"'; } ?> value="MC">Monaco</option>
    <option <?php if ($b_country=='MN'){ echo 'selected="selected"'; } ?> value="MN">Mongolia</option>
    <option <?php if ($b_country=='ME'){ echo 'selected="selected"'; } ?> value="ME">Montenegro</option>
    <option <?php if ($b_country=='MS'){ echo 'selected="selected"'; } ?> value="MS">Montserrat</option>
    <option <?php if ($b_country=='MA'){ echo 'selected="selected"'; } ?> value="MA">Morocco</option>
    <option <?php if ($b_country=='MZ'){ echo 'selected="selected"'; } ?> value="MZ">Mozambique</option>
    <option <?php if ($b_country=='MM'){ echo 'selected="selected"'; } ?> value="MM">Myanmar</option>
    <option <?php if ($b_country=='NA'){ echo 'selected="selected"'; } ?> value="NA">Namibia</option>
    <option <?php if ($b_country=='NR'){ echo 'selected="selected"'; } ?> value="NR">Nauru</option>
    <option <?php if ($b_country=='NP'){ echo 'selected="selected"'; } ?> value="NP">Nepal</option>
    <option <?php if ($b_country=='NL'){ echo 'selected="selected"'; } ?> value="NL">Netherlands</option>
    <option <?php if ($b_country=='AN'){ echo 'selected="selected"'; } ?> value="AN">Netherlands Antilles</option>
    <option <?php if ($b_country=='NT'){ echo 'selected="selected"'; } ?> value="NT">Neutral Zone</option>
    <option <?php if ($b_country=='NC'){ echo 'selected="selected"'; } ?> value="NC">New Caledonia</option>
    <option <?php if ($b_country=='NZ'){ echo 'selected="selected"'; } ?> value="NZ">New Zealand</option>
    <option <?php if ($b_country=='NI'){ echo 'selected="selected"'; } ?> value="NI">Nicaragua</option>
    <option <?php if ($b_country=='NE'){ echo 'selected="selected"'; } ?> value="NE">Niger</option>
    <option <?php if ($b_country=='NG'){ echo 'selected="selected"'; } ?> value="NG">Nigeria</option>
    <option <?php if ($b_country=='NU'){ echo 'selected="selected"'; } ?> value="NU">Niue</option>
    <option <?php if ($b_country=='NF'){ echo 'selected="selected"'; } ?> value="NF">Norfolk Island</option>
    <option <?php if ($b_country=='KP'){ echo 'selected="selected"'; } ?> value="KP">North Korea</option>
    <option <?php if ($b_country=='MP'){ echo 'selected="selected"'; } ?> value="MP">Northern Mariana Islands</option>
    <option <?php if ($b_country=='NO'){ echo 'selected="selected"'; } ?> value="NO">Norway</option>
    <option <?php if ($b_country=='OM'){ echo 'selected="selected"'; } ?> value="OM">Oman</option>
    <option <?php if ($b_country=='QO'){ echo 'selected="selected"'; } ?> value="QO">Outlying Oceania</option>
    <option <?php if ($b_country=='PC'){ echo 'selected="selected"'; } ?> value="PC">Pacific Islands Trust Territory</option>
    <option <?php if ($b_country=='PK'){ echo 'selected="selected"'; } ?> value="PK">Pakistan</option>
    <option <?php if ($b_country=='PW'){ echo 'selected="selected"'; } ?> value="PW">Palau</option>
    <option <?php if ($b_country=='PS'){ echo 'selected="selected"'; } ?> value="PS">Palestinian Territory</option>
    <option <?php if ($b_country=='PA'){ echo 'selected="selected"'; } ?> value="PA">Panama</option>
    <option <?php if ($b_country=='PZ'){ echo 'selected="selected"'; } ?> value="PZ">Panama Canal Zone</option>
    <option <?php if ($b_country=='PG'){ echo 'selected="selected"'; } ?> value="PG">Papua New Guinea</option>
    <option <?php if ($b_country=='PY'){ echo 'selected="selected"'; } ?> value="PY">Paraguay</option>
    <option <?php if ($b_country=='PE'){ echo 'selected="selected"'; } ?> value="PE">Peru</option>
    <option <?php if ($b_country=='PH'){ echo 'selected="selected"'; } ?> value="PH">Philippines</option>
    <option <?php if ($b_country=='PN'){ echo 'selected="selected"'; } ?> value="PN">Pitcairn</option>
    <option <?php if ($b_country=='PL'){ echo 'selected="selected"'; } ?> value="PL">Poland</option>
    <option <?php if ($b_country=='PT'){ echo 'selected="selected"'; } ?> value="PT">Portugal</option>
    <option <?php if ($b_country=='PR'){ echo 'selected="selected"'; } ?> value="PR">Puerto Rico</option>
    <option <?php if ($b_country=='QA'){ echo 'selected="selected"'; } ?> value="QA">Qatar</option>
    <option <?php if ($b_country=='RE'){ echo 'selected="selected"'; } ?> value="RE">Reunion</option>
    <option <?php if ($b_country=='RO'){ echo 'selected="selected"'; } ?> value="RO">Romania</option>
    <option <?php if ($b_country=='RU'){ echo 'selected="selected"'; } ?> value="RU">Russia</option>
    <option <?php if ($b_country=='RW'){ echo 'selected="selected"'; } ?> value="RW">Rwanda</option>
    <option <?php if ($b_country=='BL'){ echo 'selected="selected"'; } ?> value="BL">Saint Barthélemy</option>
    <option <?php if ($b_country=='SH'){ echo 'selected="selected"'; } ?> value="SH">Saint Helena</option>
    <option <?php if ($b_country=='KN'){ echo 'selected="selected"'; } ?> value="KN">Saint Kitts and Nevis</option>
    <option <?php if ($b_country=='LC'){ echo 'selected="selected"'; } ?> value="LC">Saint Lucia</option>
    <option <?php if ($b_country=='MF'){ echo 'selected="selected"'; } ?> value="MF">Saint Martin</option>
    <option <?php if ($b_country=='PM'){ echo 'selected="selected"'; } ?> value="PM">Saint Pierre and Miquelon</option>
    <option <?php if ($b_country=='VC'){ echo 'selected="selected"'; } ?> value="VC">Saint Vincent and the Grenadines</option>
    <option <?php if ($b_country=='WS'){ echo 'selected="selected"'; } ?> value="WS">Samoa</option>
    <option <?php if ($b_country=='SM'){ echo 'selected="selected"'; } ?> value="SM">San Marino</option>
    <option <?php if ($b_country=='ST'){ echo 'selected="selected"'; } ?> value="ST">Sao Tome and Principe</option>
    <option <?php if ($b_country=='SA'){ echo 'selected="selected"'; } ?> value="SA">Saudi Arabia</option>
    <option <?php if ($b_country=='SN'){ echo 'selected="selected"'; } ?> value="SN">Senegal</option>
    <option <?php if ($b_country=='RS'){ echo 'selected="selected"'; } ?> value="RS">Serbia</option>
    <option <?php if ($b_country=='CS'){ echo 'selected="selected"'; } ?> value="CS">Serbia and Montenegro</option>
    <option <?php if ($b_country=='SC'){ echo 'selected="selected"'; } ?> value="SC">Seychelles</option>
    <option <?php if ($b_country=='SL'){ echo 'selected="selected"'; } ?> value="SL">Sierra Leone</option>
    <option <?php if ($b_country=='SG'){ echo 'selected="selected"'; } ?> value="SG">Singapore</option>
    <option <?php if ($b_country=='SK'){ echo 'selected="selected"'; } ?> value="SK">Slovakia</option>
    <option <?php if ($b_country=='SI'){ echo 'selected="selected"'; } ?> value="SI">Slovenia</option>
    <option <?php if ($b_country=='SB'){ echo 'selected="selected"'; } ?> value="SB">Solomon Islands</option>
    <option <?php if ($b_country=='SO'){ echo 'selected="selected"'; } ?> value="SO">Somalia</option>
    <option <?php if ($b_country=='ZA'){ echo 'selected="selected"'; } ?> value="ZA">South Africa</option>
    <option <?php if ($b_country=='GS'){ echo 'selected="selected"'; } ?> value="GS">South Georgia and the South Sandwich Islands</option>
    <option <?php if ($b_country=='KR'){ echo 'selected="selected"'; } ?> value="KR">South Korea</option>
    <option <?php if ($b_country=='ES'){ echo 'selected="selected"'; } ?> value="ES">Spain</option>
    <option <?php if ($b_country=='LK'){ echo 'selected="selected"'; } ?> value="LK">Sri Lanka</option>
    <option <?php if ($b_country=='SD'){ echo 'selected="selected"'; } ?> value="SD">Sudan</option>
    <option <?php if ($b_country=='SR'){ echo 'selected="selected"'; } ?> value="SR">Suriname</option>
    <option <?php if ($b_country=='SJ'){ echo 'selected="selected"'; } ?> value="SJ">Svalbard and Jan Mayen</option>
    <option <?php if ($b_country=='SZ'){ echo 'selected="selected"'; } ?> value="SZ">Swaziland</option>
    <option <?php if ($b_country=='SE'){ echo 'selected="selected"'; } ?> value="SE">Sweden</option>
    <option <?php if ($b_country=='CH'){ echo 'selected="selected"'; } ?> value="CH">Switzerland</option>
    <option <?php if ($b_country=='SY'){ echo 'selected="selected"'; } ?> value="SY">Syria</option>
    <option <?php if ($b_country=='TW'){ echo 'selected="selected"'; } ?> value="TW">Taiwan</option>
    <option <?php if ($b_country=='TJ'){ echo 'selected="selected"'; } ?> value="TJ">Tajikistan</option>
    <option <?php if ($b_country=='TZ'){ echo 'selected="selected"'; } ?> value="TZ">Tanzania</option>
    <option <?php if ($b_country=='TH'){ echo 'selected="selected"'; } ?> value="TH">Thailand</option>
    <option <?php if ($b_country=='TL'){ echo 'selected="selected"'; } ?> value="TL">Timor Leste</option>
    <option <?php if ($b_country=='TG'){ echo 'selected="selected"'; } ?> value="TG">Togo</option>
    <option <?php if ($b_country=='TK'){ echo 'selected="selected"'; } ?> value="TK">Tokelau</option>
    <option <?php if ($b_country=='TO'){ echo 'selected="selected"'; } ?> value="TO">Tonga</option>
    <option <?php if ($b_country=='TT'){ echo 'selected="selected"'; } ?> value="TT">Trinidad and Tobago</option>
    <option <?php if ($b_country=='TN'){ echo 'selected="selected"'; } ?> value="TN">Tunisia</option>
    <option <?php if ($b_country=='TR'){ echo 'selected="selected"'; } ?> value="TR">Turkey</option>
    <option <?php if ($b_country=='TM'){ echo 'selected="selected"'; } ?> value="TM">Turkmenistan</option>
    <option <?php if ($b_country=='TC'){ echo 'selected="selected"'; } ?> value="TC">Turks and Caicos Islands</option>
    <option <?php if ($b_country=='TV'){ echo 'selected="selected"'; } ?> value="TV">Tuvalu</option>
    <option <?php if ($b_country=='PU'){ echo 'selected="selected"'; } ?> value="PU">U.S. Miscellaneous Pacific Islands</option>
    <option <?php if ($b_country=='VI'){ echo 'selected="selected"'; } ?> value="VI">U.S. Virgin Islands</option>
    <option <?php if ($b_country=='UG'){ echo 'selected="selected"'; } ?> value="UG">Uganda</option>
    <option <?php if ($b_country=='UA'){ echo 'selected="selected"'; } ?> value="UA">Ukraine</option>
    <option <?php if ($b_country=='AE'){ echo 'selected="selected"'; } ?> value="AE">United Arab Emirates</option>
    <option <?php if ($b_country=='GB'){ echo 'selected="selected"'; } ?> value="GB">United Kingdom</option>
    <option <?php if ($b_country=='US'){ echo 'selected="selected"'; } ?> value="US">United States</option>
    <option <?php if ($b_country=='UM'){ echo 'selected="selected"'; } ?> value="UM">United States Minor Outlying Islands</option>
    <option <?php if ($b_country=='UY'){ echo 'selected="selected"'; } ?> value="UY">Uruguay</option>
    <option <?php if ($b_country=='UZ'){ echo 'selected="selected"'; } ?> value="UZ">Uzbekistan</option>
    <option <?php if ($b_country=='VU'){ echo 'selected="selected"'; } ?> value="VU">Vanuatu</option>
    <option <?php if ($b_country=='VA'){ echo 'selected="selected"'; } ?> value="VA">Vatican</option>
    <option <?php if ($b_country=='VE'){ echo 'selected="selected"'; } ?> value="VE">Venezuela</option>
    <option <?php if ($b_country=='VN'){ echo 'selected="selected"'; } ?> value="VN">Vietnam</option>
    <option <?php if ($b_country=='WK'){ echo 'selected="selected"'; } ?> value="WK">Wake Island</option>
    <option <?php if ($b_country=='WF'){ echo 'selected="selected"'; } ?> value="WF">Wallis and Futuna</option>
    <option <?php if ($b_country=='EH'){ echo 'selected="selected"'; } ?> value="EH">Western Sahara</option>
    <option <?php if ($b_country=='YE'){ echo 'selected="selected"'; } ?> value="YE">Yemen</option>
    <option <?php if ($b_country=='ZM'){ echo 'selected="selected"'; } ?> value="ZM">Zambia</option>
    <option <?php if ($b_country=='ZW'){ echo 'selected="selected"'; } ?> value="ZW">Zimbabwe</option>
  </select>
</div>
<div class="clr"></div>
<div class="evField" style="width:121px">Address<font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <input type="text" class="inp" name="b_address" id="b_address" value="<?php echo $b_address; ?>" style="width:229px">
</div>
<div class="clr"></div>
<!-- <div class="evField" style="width:190px">Address</div>
                  <div class="evLabal">
                 <input type="text" class="inp" style="width:306px">
                  </div>
                  <div class="clr"></div>-->
<div class="evField" style="width:121px">City <font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <input type="text" maxlength="100" class="inp" name="b_city" id="b_city" value="<?php echo $b_city; ?>" style="width:229px" />
</div>
<div class="clr"></div>
<div class="evField" style="width:121px"> State / Province <font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <input type="text" maxlength="100" class="inp" id="b_state"  name="b_state" value="<?php echo $b_state; ?>" style="width:229px" />
</div>
<div class="clr"></div>
<div>
  <h3> <u>Payment Information</u> </h3>
</div>
<div class="evField" style="width:121px">Name of Cardholder <font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <input type="text" maxlength="100" class="inp" id="name"  name="name" value="<?php echo $bc_name; ?>" style="width:229px" />
</div>
<div class="clr"></div>
<div class="evField" style="width:121px">Credit Card Type<font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <select class="inp" style="width:229px" name="cardType" id="cardType">
    <option value="">--Select--</option>
    <option value="visa" <?php if($bc_card_type=="visa"){ echo 'selected="selected"'; }?>>Visa</option>
    <option value="mastercard"<?php if($bc_card_type=="mastercard"){ echo 'selected="selected"'; }?>>MasterCard</option>
    <option value="AMEX"<?php if($bc_card_type=="AMEX"){ echo 'selected="selected"'; }?>>AMEX</option>
    <option value="jcb"<?php if($bc_card_type=="jcb"){ echo 'selected="selected"'; }?>>JCB</option>
  </select>
</div>
<div class="clr"></div>
<div class="evField" style="width:121px">Expiration date (month/year)<font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <select name="month" class="inp2" style="width:110px" id="month">
    <option value="">Select</option>
    <?php
	  for($i=1;$i<13;$i++){
		?>
    <option value="<?php echo $i; ?>" <?php if($bc_month==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
    <?php } ?>
  </select>
  &nbsp;
  <select name="year" class="inp2" style="width:110px" id="year">
    <option value="">Select</option>
    <?php
		for($i=date('Y');$i<date('Y')+10;$i++){
		?>
    <option value="<?php echo $i; ?>" <?php if($bc_year==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
    <?php } ?>
  </select>
</div>
<div class="clr"></div>
<div class="evField" style="width:121px">Credit Card Number <font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <input type="text" maxlength="100" class="inp" id="number"  name="number" value="<?php // echo $bc_card_number; ?>5105105105105100" style="width:229px" />
</div>
<div class="clr"></div>
<div class="evField" style="width:121px">Security Code <font color="#FF0000">*</font></div>
<div class="evLabal" style="width:353px; height:20px">
  <input type="text" maxlength="100" class="inp" id="securityCode"  name="securityCode" value="<?php echo $bc_securityCode; ?>" style="width:229px" />
</div>
<div class="clr"></div>
<div class="ev_submit">
<input type="hidden" name="event_id" value="<?php echo $_GET['event_id']; ?>" id="event_id">
  <img src="<?php echo IMAGE_PATH; ?>submit_order.gif" name="submit" id="submit" value="submit">
</div>
