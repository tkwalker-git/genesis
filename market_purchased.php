<?php require_once('admin/database.php');?>
<?php require_once('includes/header.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
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
        <div class="markeetPlace_title"> Marketplace</div>
        <div class="markeetPlaceTopMenu"> <a href="market_place.php"><img src="<?=IMAGE_PATH?>featured.gif" alt="" title=""></a> <a href="market_categories.php"><img src="<?=IMAGE_PATH?>featured_category.gif" alt="" title=""></a> <a href="market_purchased.php"><img src="<?=IMAGE_PATH?>purchased_over.gif" alt="" title=""></a> </div>
        <div class="clear"></div>
      </div>
      <!-- Market Place Top END -->
      <!-- Markeet Place InrBody Start -->
      <div class="marketInrBody">
        <div class="markPurchasedTitle"> <span class="hd">Purchased</span> <span class="ev_fltlft">Purchased Date</span>
          <div class="clear"></div>
        </div>
        <div class="markPurchasedMdl">
         
		<?php
		$res = mysql_query("select * from `orders` where `user_id`='$user_id' ORDER BY `id` DESC");
		$numRows = mysql_num_rows($res);
		$a=0;
		if($numRows){
		while($row = mysql_fetch_array($res)){
		$date		=	$row['date'];
		$order_id	=	$row['id'];
		
		$re = mysql_query("select * from `order_products` where `order_id`='$order_id'");
		while($ro = mysql_fetch_array($re)){
		$a++;
		?>
		
		  <div class="markPurchasedProduct" <?php if ($numRows == $a){echo 'style="border-bottom:none;"';} ?>> <span class="tile">
		  <?php echo getProductImg($ro['product_id'],'','','left','ico_'); ?>
		  <strong><?php echo getProductTitle($ro['product_id']); ?></strong><br>
            <small><?php echo getCatNameFromProductId($ro['product_id']);?></small> </span> <span class="date"> <?php echo date('F d, Y', strtotime($date)); ?> </span> <span class="view_special"> <a href="#"><img src="<?=IMAGE_PATH?>view_special.gif" alt="" title=""></a></span>
            <div class="clear"></div>
          </div>
		
		<?php
		}}}
		else{
		echo "<div style='color: #FF0000;font-weight: bold;text-align:center;padding: 20px;'>No Record Found</div>";
		}
		?>
		  
		  
        </div>
        <div class="markPurchasedBottom">&nbsp;</div>
      </div>
      <!-- Markeet Place InrBody End -->

  </div>
</div>
<?php require_once('includes/footer.php');?>
