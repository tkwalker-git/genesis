<?php require_once('admin/database.php');?>
<?php require_once('includes/header.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0){
		echo "<script>window.location.href='login.php';</script>";
		exit();
		}
		
if($_REQUEST['addToCart']){
	$product_id		=	$_REQUEST['product_id'];
	$res = mysql_query("select * from `products` where `id`='$product_id'");
	while($row = mysql_fetch_array($res)){
	$price		=	$row['sale_price'];	
	$discount	=	$row['discount'];
	}
	$quantity	=	$_REQUEST['qty'];
	if($quantity==0 || $quantity==''){
	$quantity	=	1;
	}
	
	$res = mysql_query("select * from `cart` where `product_id`='$product_id'");
	if(mysql_num_rows($res)){
	while($row = mysql_fetch_array($res)){
	$qty	=	$row['quantity'];
	}
	$qty	=	$qty + $quantity;
	mysql_query("UPDATE `cart` SET `quantity` = '$qty' WHERE `product_id` = '$product_id'");
	}
	else{
	mysql_query("INSERT INTO `cart` (`id`, `product_id`, `user_id`, `price`, `discount`, `quantity`) VALUES (NULL, '$product_id', '$user_id', '$price', '$discount', '$quantity');");
	}
	echo "<script>window.location.href='cart.php';</script>";
	}
	
if($_REQUEST['del']){
	$id		=	$_REQUEST['del'];
	$res = mysql_query("select * from `cart` where `id`='$id'");
	if(mysql_num_rows($res)){
	mysql_query("DELETE FROM `cart` WHERE `id` = '$id'");
	echo "<script>window.location.href='cart.php';</script>";
	}
	else{
	echo "<script>window.location.href='cart.php?msg=You don't have access to delete this product';</script>";
	}
	
}
?>

<div class="topContainer">
  <script language="javascript">
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <!-- Market Place Top Start -->
    <div class="marketPlaceTop">
      <div class="markeetPlace_title"> Shoping Cart</div>
      <div class="clear"></div>
    </div>
    <!-- Market Place Top END -->
    <!-- Markeet Place InrBody Start -->
    <div class="marketInrBody">
      <?php
		 if($_REQUEST['msg']!=''){?>
      <div align="center" style="color:#FF0000"><br>
        <?php echo $_REQUEST['msg']; ?></div>
      <?php } ?>
      <table class="" width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="margin-top:14px;">
        <tr class="markPurchasedTitle">
          <td width="7%" height="35" align="center"><strong>Sr #</strong></td>
          <td width="48%" align="center"><strong>Deal Name </strong></td>
          <td width="5%" align="center"><strong>Qty</strong></td>
          <td width="10%" align="center"><strong>Price</strong></td>
		  <td width="6%" align="center"><strong>Discount</strong></td>
          <td width="11%" align="center"><strong>Total</strong></td>
          <td width="13%" align="center"><strong>Delete</strong></td>
        </tr>
        <?php
	$res = mysql_query("select * from `cart` where `user_id`='$user_id'");
	$i= 0;
	if(mysql_num_rows($res)){
	while($row = mysql_fetch_array($res)){
	$i++;
	?>
        <tr>
          <td align="center" style="border:#E4E4E4 solid 1px; padding:5px; border-top:none"><?php echo $i; ?></td>
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
           
		   <td align="center" style="border-bottom:#E4E4E4 solid 1px;border-right:#E4E4E4 solid 1px;"><a onClick="return confirm('are you sure you want delete this deal?');" href="cart.php?del=<?php echo $row['id'];?>"><img src="<?php echo IMAGE_PATH; ?>icon_delete2.gif" border="0" title="remove this deal"/></a></td>
        </tr>
        <?php
	
	$sub_total	=	$priceAfterDiscount+$sub_total;
	 } ?>
        <tr style="font-size:15px">
          <td colspan="5" style="border:#E4E4E4 solid 1px; border-top:none; padding:5px;"><strong>Sub total:</strong></td>
          <td align="center" style="border-bottom:#E4E4E4 solid 1px;"><strong><u>$<?php echo $sub_total; ?></u></strong></td>
          <td align="center" style="border-bottom:#E4E4E4 solid 1px;border-right:#E4E4E4 solid 1px;">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" style="border-left:#E4E4E4 solid 1px">&nbsp;</td>
          <td colspan="3" valign="top" style="padding-top:4px;border-right:#E4E4E4 solid 1px;">&nbsp;&nbsp;&nbsp;&nbsp; <img src="<?php echo IMAGE_PATH; ?>back.gif" title="back" border="0" onClick="history.go(-1)" style="cursor:pointer"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo ABSOLUTE_PATH; ?>check_out.php"><img src="<?php echo IMAGE_PATH; ?>check_out.gif" title="checkout" /></a></td>
        </tr>
        <?php } 
	
	else{
	echo '<tr><td colspan="7" align="center" style="padding:20px;border-left:#E4E4E4 solid 1px; border-right:#E4E4E4 solid 1px; color:#FF0000">Your Cart is Empty</td></tr>';
	}?>
      </table>
      <div class="markPurchasedBottom">&nbsp;</div>
    </div>
    <!-- Markeet Place InrBody End -->
  </div>
</div>
<?php require_once('includes/footer.php');?>