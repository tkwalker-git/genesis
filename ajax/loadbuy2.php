<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$active='buy';
	
	$_SESSION['ticketOrder']='';
		
	$event_id	=	$_POST['event_id'];
	
	$qtys	= explode(",", $_POST['qtys']);
	$ids	= explode(",", $_POST['ids']);
	$dates	= explode(",", $_POST['dates']);
	
	$_SESSION['ticketOrder']['event_id']	=	$event_id;
	
	$s=0;
	for($i=0;$i < count($ids);$i++){
	$s++;
		if($qtys[$i]!='' && $qtys[$i]!='0'){
			if($s!=count($ids))
			$coma = ",";
			else
			$coma = "";
			
		$_SESSION['ticketOrder']['ticket_id']	.=	$ids[$i].$coma;
		$_SESSION['ticketOrder']['ticket_qty']	.=	$qtys[$i].$coma;
		$_SESSION['ticketOrder']['ticket_date']	.=	$dates[$i].$coma;
		}
	}
	
	
		$res = mysql_query("select * from `event_ticket` where `event_id`='$event_id'");
		while($row = mysql_fetch_array($res)){
			$bc_ticket_id			=	$row['id'];
			$bc_name				=	$row['name'];
			$bc_price				=	$row['price'];
			$bc_ticket_description	=	$row['ticket_description'];
			$bc_ticket_id			=	$row['id'];
			$bc_max_order			=	$row['max_tickets_order'];
			$bc_min_order			=	$row['min_tickets_order'];
			$bc_service_fee_type	=	$row['service_fee_type'];
			$bc_service_fee			=	$row['service_fee'];
			$bc_quantity_available	=	$row['quantity_available'];
		}
		
		$res = mysql_query("select * from `events` where `id`='$event_id'");
		while($row = mysql_fetch_array($res)){
		$event_name = $row['event_name'];
		
		$venue_id = getSingleColumn('venue_id',"select * from `venue_events` where `event_id` = '$event_id'");
		$venue = getEventLocations($venue_id);
		
		}
		
	 include("../flayerMenu.php"); ?>
<script>

function checkValid(){

	var gender		= $('#gender').val();
	var fname		= $('#fname').val();
	var lname		= $('#lname').val();
	var city		= $('#city').val();
	var dob			= $('#dob').val();
	var email		= $('#email').val();
	var dv_method 	= $('#dv_method').val();
	
	if($('#agree').attr('checked') == true){
	var agree = 'yes';
	}
	else{
	var agree = 'no';
	}
	
		var ticket_buyer_name = new Array();
	var s	= 0;
	var er	= 0;
	jQuery.each(jQuery("input[name='t_buyer_name[]']"), function() {
	s++;
		ticket_buyer_name.push(jQuery(this).val());
		if(jQuery(this).val()==''){
			alert("Full Name is required. (Ticket #"+s+")");
			er = 1;
			return false;
		}
	 });
	 if(er==1)
	 	return false;
		
		
	var ticket_buyer_email = new Array();
	var s	= 0;
	var er	= 0;
	jQuery.each(jQuery("input[name='t_buyer_email[]']"), function() {
	s++;
		ticket_buyer_email.push(jQuery(this).val());
		if(jQuery(this).val()==''){
			alert("Email is required. (Ticket #"+s+")");
			er = 1;
			return false;
		}
		var str = jQuery(this).val();
		var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
		if (filter.test(str))
			testresults=true;
		else{
			alert("Invalid email address. (Ticket #"+s+")");
			er = 1;
			return false;
		}
		
		
	 });
	 if(er==1)
	 	return false;

	
	if(gender==''){
		alert('Please select Gender');
		return false;
	}
	if(fname==''){
		alert('Please enter First name');
		return false;
	}
	if(lname==''){
		alert('Please enter Last name');
		return false;
	}
	if(city==''){
		alert('Please enter City');
		return false;
	}
	if(dob==''){
		alert('Please select Date of birth');
		return false;
	}
	if(email==''){
		alert('Please enter E-mail');
		return false;
	}
	
	var str = email;
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	if (filter.test(str))
		testresults=true;
	else{
		alert("Please input a valid email address!");
		return false;
		}
		
		
	if(agree=='no'){
		alert('You are not agree with our terms');
		return false;
	}
	
	
	 $.ajax({  
			type: "POST",
			url: abs_url+"ajax/loadbuy3.php",
			data: "ticket_buyer_name=" + ticket_buyer_name + "&ticket_buyer_email=" + ticket_buyer_email + "&gender=" + gender + "&fname=" + fname + "&lname=" +lname + "&city=" +city+ "&dob=" +dob+ "&email=" +email+"&event_id="+<?php echo $event_id; ?>,
			dataType: "text/html",
			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
			$("#flayer").html(html);
			},
			complete: function()
			{
				hideOverlayer();
			}
	   	});
	}


</script>

<script src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.core.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.theme.css">
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.css">
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>calendar/jquery.ui.datepicker.js"></script>
<div id="message"></div>
<div class="inrDiv">
        <div class="progresbar2"></div>
        <br />
        <br />
         <?php
		 $sc=1;
		  include('orderticketInfo.php');?>
  </td>
  </tr>
  <tr bgcolor="#d1e5c0">
    <td colspan="4" style="padding:7px 10px"><strong>Ticket Information</strong> </td>
  </tr>
  <tr bgcolor="#f3f6ea">
    <td colspan="4" valign="top" style="padding:10px">
	<table width="100%" border="0" cellpadding="0" cellspacing="10">
	<?php
	$ids	= explode(",", $_SESSION['ticketOrder']['ticket_id']);
	$qtys	= explode(",", $_SESSION['ticketOrder']['ticket_qty']);
	$dates	= explode(",", $_SESSION['ticketOrder']['ticket_date']);
	$s=0;
	$x=0;
	for($i=0;$i< count($ids); $i++) {
		$id		=	$ids[$i];
		$qty	=	$qtys[$i];
		for($z=0;$z < $qty; $z++) {
			$res = mysql_query("select * from `event_ticket_price` where `id`='$id'");
			while($row = mysql_fetch_array($res)){	
				$price = $row['price'];
				$title = $row['title'];
				$s++;
				?>
			<tr>
				<td colspan="2"><strong>Ticket #<?php echo $s; ?> - <?php echo $title; ?></strong></td>
			</tr>
			<tr>
                <td>Full Name:</td>
                <td><input type="text" maxlength="100" id="" name="t_buyer_name[]" value="<?php echo $bc_t_buyer_name[$x]; ?>" style="width:150px; height:20px" /></td>
			</tr>
			<tr>
				<td>Email Address:</td>
				<td><input type="text" maxlength="100" id="" name="t_buyer_email[]" value="<?php echo $bc_t_buyer_email[$x]; ?>" style="width:250px; height:20px" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><div class="clr" style="border-bottom:#eee solid 1px; margin-bottom:6px; width:100%"></div></td>
			</tr>
				
                <?php
			  $x++;
				}
			}
		}
				?>
	</table>
	</td>
  </tr>
  <tr bgcolor="#d1e5c0">
            <td colspan="4" style="padding:7px 10px"><strong>Your information </strong> </td>
          </tr>
          <tr bgcolor="#f3f6ea">
            <td colspan="4" valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="10">
                <tr>
                  <td>Gender / First name</td>
                  <td><select name="gender" id="gender" style="padding:3px">
                      <option value="">Choose</option>
                      <option value="M">Male</option>
                      <option value="F">Female</option>
                    </select>
                    &nbsp;
                    <input type="text" style="height:20px" name="fname" id="fname" />
                  </td>
                </tr>
                <tr>
                  <td>Last name</td>
                  <td><input type="text" style="width:300px; height:20px" name="lname" id="lname" /></td>
                </tr>
                <tr>
                  <td>City</td>
                  <td><input type="text" style="width:300px; height:20px" name="city" id="city" /></td>
                </tr>
                <tr>
                  <td>Date of birth</td>
                  <td><input type="text" style="width:150px; height:20px" name="dob" id="dob" readonly=""  /></td>
                </tr>
                <tr>
                  <td>E-mail</td>
                  <td><input type="text" style="width:300px; height:20px" name="email" id="email"  /></td>
                </tr>
                <tr>
                  <td>Delivery method</td>
                  <td><select name="dv_method" id="dv_method" style="width:150px; padding:3px">
                      <option>E-ticket</option>
                    </select></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td valign="top"><table width="308">
                      <tr>
                        <td width="95%">Yes, keep me informed about future events.</td>
                        <td width="5%"><input type="checkbox" /></td>
                      </tr>
					 <tr>
                        <td width="95%">I agree to the terms of use</td>
                        <td width="5%"><input type="checkbox" name="agree" id="agree" value="yes" /></td>
                      </tr>
					 
                    </table>
                </td>
                </tr>
              </table></td>
          </tr>
          <tr bgcolor="#e4f0d8">
            <td colspan="2">&nbsp;</td>
            <td align="right" style="padding:5px" valign="bottom" colspan="2">
			<img onclick="checkValid();" src="<?php echo IMAGE_PATH; ?>new_flayer_continueButton.png" style="cursor:pointer" align="right" />
			<img src="<?php echo IMAGE_PATH; ?>new_flayer_backButton.png" style="cursor:pointer" onClick="getPages('<?php echo  ABSOLUTE_PATH; ?>','loadbuy.php','flayer','<?php echo  $event_id; ?>');"  align="right" /></td>
          </tr>
        </table>
        <div align="right"><br />
          <a href="<?php echo ABSOLUTE_PATH; ?>"><img src="<?php echo IMAGE_PATH; ?>powered_by.png" width="225" height="26" border="0" /></a></div>
      </div>
<script>
var dates = $("#dob").datepicker({
		dateFormat: "dd-M-yy",
		changeMonth: true,
		changeYear: true,
		yearRange: '1940:2011',
		defaultDate: '01-Jan-1970'
		
});

</script>