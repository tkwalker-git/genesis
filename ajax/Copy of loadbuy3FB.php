<?php
require_once('../admin/database.php');
require_once('../site_functions.php');
	
	$active='buy';

	$event_id		= $_POST['event_id'];

	$_SESSION['orderMember']='';
	$_SESSION['orderMember']['ticket_buyer_name']	= $_POST['ticket_buyer_name'];
	$_SESSION['orderMember']['ticket_buyer_email']	= $_POST['ticket_buyer_email'];
	$_SESSION['orderMember']['gender']				= $_POST['gender'];
	$_SESSION['orderMember']['fname']				= $_POST['fname'];
	$_SESSION['orderMember']['lname']				= $_POST['lname'];
	$_SESSION['orderMember']['city']				= $_POST['city'];
	$_SESSION['orderMember']['dob']					= $_POST['dob'];
	$_SESSION['orderMember']['email']				= $_POST['email'];
	$_SESSION['orderMember']['event_id']			= $_POST['event_id'];
	$_SESSION['event_id']			= $_POST['event_id'];
	$res = mysql_query("select * from `events` where `id`='$event_id'");
		while($row = mysql_fetch_array($res)){
			$event_name = $row['event_name'];
			$venue_id = getSingleColumn('venue_id',"select * from `venue_events` where `event_id` = '$event_id'");
			$venue = getEventLocations($venue_id);
		}

include("../flayerMenuFB.php");

?>
</script>

<div id="message"></div>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH_SECURE; ?>js/jquery.tipsy.js"></script>
<script>
function checkValid2(){
	var abs_url			= '<?php echo ABSOLUTE_PATH; ?>';
	var abs_url_secure	= '<?php echo ABSOLUTE_PATH_SECURE; ?>';
	var country			= $('#country').val();
	var c_number		= $('#c_number').val();
	var c_type			= '';
	if($('#master_card').attr('checked')==true)
		c_type	= 'mastercard';
	else if($('#visa_card').attr('checked')==true)
		c_type	= 'visa';
	else if($('#amex_card').attr('checked')==true)
		c_type	= 'AMEX';
	else if($('#discover_card').attr('checked')==true)
		c_type	= 'discovercard';
		
	var c_month			= $('#c_month').val();
	var c_year			= $('#c_year').val();
	var c_csc			= $('#csc').val();
	var c_name			= $('#c_name').val();
	var c_address		= $('#c_address').val();
	var c_city			= $('#c_city').val();
	var c_province		= $('#c_province').val();
	var c_postal_code	= $('#c_postal_code').val();
	var c_telephone		= $('#c_telephone').val();
	var c_email			= $('#c_email').val();
	
	
	if(country==''){
		alert('Please select a Country');
		return false;
	}
	if(c_number==''){
		alert('Please enter a Card number');
		return false;
	}
	if(c_month=='' || c_month=='mm' || c_year=='' || c_year=='yyyy'){
		alert('Credit card expiration date is invalid.');
		return false;	
	}

/*	if(c_csc==''){
		alert('Please enter CSC');
		return false;
	}
*/
	if(c_name==''){
		alert('Please enter Name on card');
		return false;
	}

	if(c_address==''){
		alert('Please enter Address');
		return false;
	}

	if(c_city==''){
		alert('Please enter City');
		return false;
	}
	if(c_province==''){
		alert('Please select Province');
		return false;
	}
	if(c_postal_code==''){
		alert('Please enter Postal code');
		return false;
	}
	if(c_telephone==''){
		alert('Please enter Telephone Number');
		return false;
	}
	if(c_email==''){
		alert('Please enter E-mail');
		return false;
	}
	
	var str = c_email;
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	if (filter.test(str))
		testresults=true;
	else{
		alert("Please input a valid email address!");
		return false;
		}
		 $.ajax({  
			type: "POST",
			url: abs_url_secure+"ajax/checkTicketPayment.php",
			data: "country=" + country + "&c_number=" + c_number + "&c_month=" +c_month + "&c_year=" +c_year+ "&c_csc=" +c_csc+ "&c_name=" +c_name+"&c_address="+c_address+"&c_city="+c_city+"&c_province="+c_province+"&c_postal_code="+c_postal_code+"&c_telephone="+c_telephone+"&c_type="+c_type+"&c_email="+c_email,
			dataType: "text/html",
			beforeSend: function()
			{
				showOverlayer(abs_url_secure+'ajax/loader.php');
			},
			success: function(html)
			{
			
			var ht = html.split('|');
			var ht2 = ht[0].split('<order>');
			
			if(ht2[1]=='order_id'){
			$.ajax({  
					type: "POST",
					url: abs_url_secure+"ajax/loadbuy4FB.php",
					data: "order_id="+ht[1],
					dataType: "text/html",
					success: function(html){
					$("#flayer").html(html);
					}
				});
			} // end if ht[0]=='order_id'
			else{
				alert(html);
			//	$("#show").css('display','block');
			//	$("#show").html(html);
			} // end else
			
			},
			complete: function()
			{
				hideOverlayer();
			}
	   	});
}

$('#reset').click(function(){
	$conf = confirm('Are you sure you want to reset you Payment information');
	if($conf == true){
		$('#country').val('');
		$('#c_number').val('');
		$('#c_month').val('mm');
		$('#c_year').val('yyyy');
		$('#csc').val('');
		$('#c_name').val('');
		$('#c_address').val('');
		$('#c_city').val('');
		$('#c_province').val('');
		$('#c_postal_code').val('');
		$('#c_telephone').val('');
		$('#c_email').val('');
	}
});
</script>
<div class="inrDiv"><br />
  <div class="progresbar3"></div>

  <br />
  <span id="show" style="color:#FF0000; background:#feda00; font-size:12px; font-weight:bold; display:none; padding:5px;"></span><br />
  <table cellpadding="0" cellspacing="0" width="100%">
    <tr bgcolor="#f3f6ea">
      <td valign="top" colspan="2"><?php include('orderticketInfo.php');?>
      </td>
    </tr>
    <tr bgcolor="#d1e5c0">
      <td colspan="4" style="padding:7px 10px"><strong>Payment information</strong> </td>
    </tr>
    <tr bgcolor="#f3f6ea">
      <td colspan="4" valign="top" style="border-bottom:#c2c5bb solid 1px"><table cellpadding="10" cellspacing="0" width="100%">
        </table></td>
    </tr>
    <tr bgcolor="#f3f6ea">
      <td  valign="top" colspan="4"><table cellpadding="10" cellspacing="0" width="100%">
        </table></td>
    </tr>
    <tr bgcolor="#f3f6ea">
      <td colspan="4" valign="top">
	  <form method="post">    
		  <table cellpadding="0" cellspacing="10" width="86%" align="center">
            <tr>
              <td width="27%">Country</td>
              <td width="73%"><select style="padding:2px; width:250px" name="country" id="country">
                  <option value="US">United States</option>
                </select></td>
            </tr>
            <tr>
              <td>Card number</td>
              <td><input type="text" style="width:250px; height:20px;" value="" name="c_number" id="c_number" /></td>
            </tr>
            <tr>
              <td>Payment types</td>
              <td><label>
                <input type="radio" checked="checked" id="master_card" name="c_type" />
                <img src="<?php echo IMAGE_PATH; ?>master_card.gif" /></label>
                &nbsp;
                <label>
                <input type="radio" name="c_type" id="visa_card" />
                <img src="<?php echo IMAGE_PATH; ?>visa_card.gif" /></label>
                &nbsp;
                <label>
                <input type="radio" name="c_type" id="amex_card" />
                <img src="<?php echo IMAGE_PATH; ?>amex_card.gif" /></label>
                &nbsp;
                <label>
                <input type="radio" name="c_type" id="discover_card" />
                <img src="<?php echo IMAGE_PATH; ?>discover_card.gif" /></label>
                &nbsp; </td>
            </tr>
            <tr>
              <td>Expiration date</td>
              <td><select name="c_month" style="width:60px" id="c_month">
                    <option value="">Month</option>
                    <?php
	  for($i=1;$i<13;$i++){
		?>
                    <option value="<?php echo $i; ?>" <?php if($bc_month==$i){ echo 'selected="selected"'; }?>><?php echo str_pad($i,2,0,STR_PAD_LEFT); ?></option>
                    <?php } ?>
                  </select>
                /
                <select name="c_year" class="" style="width:85px" id="c_year">
                    <option value="">Year</option>
                    <?php
		for($i=date('Y');$i<date('Y')+10;$i++){
		?>
                    <option value="<?php echo $i; ?>" <?php if($bc_year==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
                    <?php } ?>
                  </select></td>
            </tr>
            <tr>
              <td>CSC <span class="new_blue" style="font-size:10px; cursor:help"><span class="info" title="CSC (Card Security Code) is a new authentication scheme established by credit card companies to reduce the risk of credit card fraud for online transactions. It consists of requiring a card holder to enter the CSC number in at transaction time to verify that the card is on hand. Most banks have upgraded their card holders card using this new system. If your card does not have a CSC number, please enter 000 in the CSC field on our order form. The location of the CSC number depends on the type of credit card you are using. The graphics below will help you find the location of your CSC number based on the type of credit card you wish to use">[What is this?]</span></span></td>
              <td><input type="text" style="width:30px; height:20px;" name="csc" id="csc" /></td>
            </tr>
            <tr>
              <td>Name on card</td>
              <td><input type="text" style="width:250px; height:20px;" name="c_name" id="c_name" /></td>
            </tr>
            <tr>
              <td>Address</td>
              <td><input type="text" style="width:250px; height:20px;" name="c_address" id="c_address" /></td>
            </tr>
            <tr>
              <td>City</td>
              <td><input type="text" style="width:250px; height:20px;" name="c_city" id="c_city" value="<?php echo $_SESSION['orderMember']['city']; ?>" /></td>
            </tr>
            <tr>
              <td>Province</td>
              <td><select style="padding:2px; width:250px" name="c_province" id="c_province">
                  <option value="">Choose</option>
                  <?php
				foreach($usStates as $index => $value){
				?>
                  <option value="<?php echo $index; ?>"><?php echo $value; ?></option>
                  <?php
				}
				?>
                </select>
              </td>
            </tr>
            <tr>
              <td>Postal code</td>
              <td><input type="text" style="width:50px; height:20px;" name="c_postal_code" id="c_postal_code" /></td>
            </tr>
            <tr>
              <td>Telephone</td>
              <td><input type="text" style="width:250px; height:20px;" name="c_telephone" id="c_telephone" /></td>
            </tr>
			 <tr>
              <td>Email</td>
              <td><input type="text" style="width:250px; height:20px;" name="c_email" id="c_email" value="<?php echo $_SESSION['orderMember']['email']; ?>" /></td>
            </tr>
          </table>
        </form>
		</td>
    </tr>
</table>
</div>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr bgcolor="#e4f0d8">
      <td colspan="2">&nbsp;</td>
      <td align="right" style="padding:5px" valign="bottom" colspan="2"><img onclick="checkValid2();" src="<?php echo IMAGE_PATH; ?>new_flayer_continueButton.png" style="cursor:pointer" align="right" /> <img src="<?php echo IMAGE_PATH; ?>new_flayer_resetButton.png" style="cursor:pointer" id="reset" align="right" /></td>
    </tr>
  </table>
  </div>
<script>
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
