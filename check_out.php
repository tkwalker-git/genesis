<?php require_once('admin/database.php');?>
<?php require_once('includes/header.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
		
$res = mysql_query("select * from `cart` where `user_id`='$user_id'");
if(mysql_num_rows($res)==0){
echo "<script>window.location.href='cart.php';</script>";
exit();
}

	if ( isset($_POST['submit']) ) {
	$bc_name			=	$_POST["name"];
	$bc_card_type		=	$_POST['cardType'];
	$bc_month			=	$_POST['month'];
	$bc_year			=	$_POST['year'];
	$bc_card_number		=	$_POST['number'];
	$bc_securityCode	=	$_POST['securityCode'];
	
if ( trim($bc_name) == '' )
		$errors[] = 'Please enter Name of Cardholder';
	if ( trim($bc_card_type) == '' )
		$errors[] = 'Please select Credit Card Type';
/*		if ( trim($bc_month) == '' || trim($bc_card_type) == '' )
		$errors[] = 'Please select Expiration date';
	if ( trim($bc_card_number) == '' )
		$errors[] = 'Please enter Credit Card Number';*/
		
	//if ( trim($bc_securityCode) == '' )
//		$errors[] = 'Please enter Security Code';


	if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	
	$res = mysql_query("select * from `users` where `id`='$user_id'");
	while($row = mysql_fetch_array($res)){
	$bc_firstname	=	$row['firstname'];
	$bc_lastname	=	$row['lastname'];
	$bc_zip_code	=	$row['zip'];
	}
	

	
	$res = mysql_query("select * from `cart` where `user_id`='$user_id'");
	while($row = mysql_fetch_array($res)){
	$gra	=	$row['price']*$row['quantity'];
	$grand			=	$gra*$row['discount'];
	$grand_tot		=	$grand / 100;
	$grand_tota		=	$gra - $grand_tot;
	$grand_total	=	$grand_tota + $grand_total;
	}

			$customer_details['first_name'] 		=	$bc_firstname;
			$customer_details['last_name'] 			=	$bc_lastname;
			$customer_details['address'] 			=	$bc_address_1;
			$customer_details['zip'] 				=	$bc_zip_code;

//			$customer_details['city'] 				=	$bc_city;
//			$customer_details['state'] 				=	$bc_state;
//			$customer_details['country'] 			=	$bc_country;
			
			$customer_details['card_number'] 		=	$bc_card_number;
			$customer_details['card_code'] 			=	$bc_securityCode;
			$customer_details['card_expiry_month'] 	=	$_POST["month"];
			$customer_details['card_expiry_year'] 	=	$_POST["year"];

			$customer_details['totalAmount']		=	$grand_total;
				
			include_once("includes/authorize.net.php");
			$response = authorize_process($customer_details);	
			list($processed, $response_arr) = authorize_isProcessed($response);
			
			if (!$processed) {
			
		$sucMessage = '<table border="0" width="90%"><tr><td class="error" ><ul><li>' . $response_arr[3] . '</li></ul></td></tr></table>';
		
			} else {
	$date		=	date('Y-m-d');
mysql_query("INSERT INTO `orders` (`id`, `user_id`, `total_price`, `date`, `type`) VALUES (NULL, '$user_id', '$total_price', '$date', 'product')");
	
	$order_id	=	mysql_insert_id();
	
	$res = mysql_query("select * from `cart` where `user_id`='$user_id'");
	while($row = mysql_fetch_array($res)){
	$product_id	=	$row['product_id'];
	$quantity	=	$row['quantity'];
	$price		=	$row['price'];
	
	$priceDiscount	=	$row['quantity']*$row['price']*$row['discount']/100;
	$priceAfterDiscount	=	$row['quantity']*$row['price']-$priceDiscount;
	
	$total_price = $priceAfterDiscount+$total_price;
	
	mysql_query("INSERT INTO `order_products` (`id`, `product_id`, `price`, `quantity`, `order_id`) VALUES (NULL, '$product_id', '$priceAfterDiscount', '$quantity', '$order_id')");
	
	}
	mysql_query("INSERT INTO `paymeny_info` (`id`, `order_id`, `name_cardholder`, `card_type`, `exp_month`, `exp_year`, `card_number`, `security_code`) VALUES (NULL, '$order_id', '$bc_name', '$bc_card_type', '$bc_month', '$bc_year', '$bc_card_number', '$bc_securityCode')");
	mysql_query("UPDATE `orders` SET `total_price` = '$total_price' WHERE `id` = $order_id");
	mysql_query("DELETE FROM `cart` WHERE `user_id` = '$user_id'");
	$sucMessage = "<b style='color:#ff0000'>Your order submitted successfully</b>";
	}
	}
	else{
	$sucMessage = $err;
	}}
		
?>
<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">
	
	function submitform(){	
			
	document.forms["searchfrmdate"].submit();

	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="eventMdlBg">
      <div class="eventMdlMain">
	   
	   <table class="" width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="margin-top:14px;">
        <tr class="markPurchasedTitle">
          <td width="8%" height="35" align="center"><strong>Sr #</strong></td>
          <td width="52%" align="center"><strong>Deal Name </strong></td>
          <td width="7%" align="center"><strong>Qty</strong></td>
          <td width="12%" align="center"><strong>Price</strong></td>
		  <td width="8%" align="center"><strong>Discount</strong></td>
          <td width="13%" align="center"><strong>Total</strong></td>
         
        </tr>
        <?php
	$res = mysql_query("select * from `cart` where `user_id`='$user_id'");
	$i= 0;
	if(mysql_num_rows($res)){
	while($row = mysql_fetch_array($res)){
	$i++;
	?>
        <tr>
          <td align="center" style="border:#E4E4E4 solid 1px; padding:8px; border-top:none"><?php echo $i; ?></td>
          <td style="border-right:#E4E4E4 solid 1px; padding-left:5px;border-bottom:#E4E4E4 solid 1px"><?php echo getProductName($row['product_id']); ?></td>
          <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><?php echo $row['quantity']; ?></td>
          <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px">$<?php echo $row['price']; ?></td>
		  <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px"><?php echo $row['discount']; ?>%</td>
          <td align="center" style="border-right:#E4E4E4 solid 1px;border-bottom:#E4E4E4 solid 1px">
		  $<?php
		  $priceDiscount	=	$row['quantity']*$row['price']*$row['discount']/100;
		 echo  $priceAfterDiscount	=	$row['quantity']*$row['price']-$priceDiscount;
		  ?>
		  </td>
        </tr>
        <?php
		$sub_total	=	$priceAfterDiscount+$sub_total;
		}
		 ?>
        <tr style="font-size:15px">
          <td colspan="5" style="border:#E4E4E4 solid 1px; border-top:none; padding:10px;"><strong>Sub total:</strong></td>
          <td align="center" style="border-bottom:#E4E4E4 solid 1px;border-right:#E4E4E4 solid 1px;"><strong><u>$<?php echo $sub_total; ?></u></strong></td>
         
        </tr>
        <?php }
		else{
		echo '<tr><td colspan="6" align="center" style="padding:20px;border-left:#E4E4E4 solid 1px; border-right:#E4E4E4 solid 1px; color:#FF0000">Your Cart is Empty</td></tr>';
			}?>
      </table>
	  <br /><br />
	  
	   <span class="viewevents_title">Order Information</span>
        <div class="featuresBoxNew">
          <div class="featuresBotBgNew">
            <div class="featuresTopBgNew">
              <div class="creatProduct">
                <form method="post" name="bc_form" enctype="multipart/form-data" action="">
                  <div class="error"><?php echo $sucMessage; ?></div>
				  
<div><h3><u>Payment Information</u></h3></div>
                  
				  <div class="evField" style="width:190px">Name of Cardholder <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <input type="text" maxlength="100" class="inp" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
				  
				  <div class="evField" style="width:190px">Credit Card Type<font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <select class="inp" style="width:306px" name="cardType">
					<option value="">--Select--</option> 
					<option value="visa" <?php if($bc_card_type=="visa"){ echo 'selected="selected"'; }?>>Visa</option> 
					<option value="mastercard"<?php if($bc_card_type=="mastercard"){ echo 'selected="selected"'; }?>>MasterCard</option> 
					<option value="AMEX"<?php if($bc_card_type=="AMEX"){ echo 'selected="selected"'; }?>>AMEX</option> 
					<option value="jcb"<?php if($bc_card_type=="jcb"){ echo 'selected="selected"'; }?>>JCB</option> 
					</select>
                  </div>
                  <div class="clr"></div>
				  
				  <div class="evField" style="width:190px">Expiration date (month/year)<font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <select name="month" class="inp2" style="width:148px" id="dalete1"> 
      <option value="">Select</option> 
	  <?php
	  for($i=1;$i<13;$i++){
		?>
		<option value="<?php echo $i; ?>" <?php if($bc_month==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
		<?php } ?>
    </select> 
      &nbsp; 
      <select name="year" class="inp2" style="width:148px" id="delete2"> 
        <option value="">Select</option> 
		<?php
		for($i=date('Y');$i<date('Y')+10;$i++){
		?>
		<option value="<?php echo $i; ?>" <?php if($bc_year==$i){ echo 'selected="selected"'; }?>><?php echo $i; ?></option>
		<?php } ?>
		</select>
                  </div>
                  <div class="clr"></div>
				  
				  <div class="evField" style="width:190px">Credit Card Number <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <input type="text" maxlength="100" class="inp" id="eventname"  name="number" value="<?php echo $bc_card_number; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
				  
				  
				  <div class="evField" style="width:190px">Security Code <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <input type="text" maxlength="100" class="inp" id="eventname"  name="securityCode" value="<?php echo $bc_securityCode; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
				   <div class="ev_submit">
        <input type="image" src="<?php echo IMAGE_PATH; ?>submit_order.gif" name="submit" value="submit" />
        <input type="hidden" name="submit" value="submit">
      </div>
				  
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');?>