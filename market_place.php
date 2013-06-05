<?php require_once('admin/database.php');?>
<?php require_once('includes/header.php');
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
      <div class="markeetPlaceTopMenu"> <a href="market_place.php"><img src="<?=IMAGE_PATH?>featured_over.gif" alt="" title=""></a> <a href="market_categories.php"><img src="<?=IMAGE_PATH?>featured_category.gif" alt="" title=""></a> <a href="market_purchased.php"><img src="<?=IMAGE_PATH?>purchased.gif" alt="" title=""></a> </div>
      <div class="clear"></div>
    </div>
    <!-- Market Place Top END -->
    <div> <img src="<?=IMAGE_PATH?>featured_banner.gif" alt="" title=""> </div>
    <!-- Markeet Place InrBody Start -->
    <div class="marketInrBody">
      <!-- Markeet Place LeftArea Start -->
      <div class="marketLeft">
        <!-- Box Start -->
        <div class="box"><a id="id"></a>
          <div class="heading"> <span class="hd">Featured Deals </span> <span class="all"> <a href="?show=all&#id">See all</a> &gt;</span> </div>
          <div class="contentBox">
            <?php
			if($_GET['show']=='all'){
			$qry = "select * from `products` where `featured`='1' ORDER BY id DESC";
			}
			else{
			$showProducts = 6;
			$qry = "select * from `products` where `featured`='1' ORDER BY id DESC LIMIT 0, $showProducts";
			}
			$res = mysql_query($qry);
			$a = 0;
			$numRows = mysql_num_rows($res);
			?>
            <div <?php if ($numRows > 3){?> class="merchantRow" <?php } ?>>
              <?php
			$i =0;
			while($row = mysql_fetch_array($res)){
			$a++;
			$i++;?>
              <div class="merchant" 
			<?php
			if($i==3){?>
			 style="border-right:none"
			<?php
			}?>
			>
                <?php
				if($row['image']!='' && file_exists(DOC_ROOT . 'images/products/ico_' . $row['image'] ) ){
				$showImg="<span class='imgOuter'><img src=".PRODUCT_IMAGE_PATH."ico_".$row['image']." align='left'></span>";
				}
				else{
				$showImg='<img src="admin/images/no_image.png" align="left">';
				}
			?>
                <a href="<?=ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html">
				
				<?php echo $showImg; ?>
				
				</a> <span class="merchantHeading"><a href="<?=ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html"><?php echo $row['name'];?></a></span><br />
                <?php echo getMarketCategoryName($row['category_id']); ?><br>
                <span class="merchantStars">
                <?php getRatingAggregate($row['id'],'product'); ?>
                </span> <br />
                <br />
              </div>
              <?php
			  if($i%3==0){?>
            </div>
            <div <?php if (($numRows-1)!=$a && ($numRows-2)!=$a && ($numRows-3)!=$a && $numRows > 3){ echo 'class="merchantRow"'; }?>>
              <?php
			  }
			}
			?>
            </div>
            <div class="clear"></div>
          </div>
        </div>
        <!-- Box End -->
        <!-- Sponsore Start -->
        <div class="sponsore">
          <div class="sponsoreTitle">Sponsored Brands</div>
          <?php echo sponsored(); ?> </div>
        <!-- Sponsore End -->
        <!-- Box Start -->
        <br />
        <div class="box">
          <div class="heading"> <span class="hd">What's Hot</span> </div>
          <div class="contentBox">
            <?php
			$res = mysql_query("select * from `order_products` GROUP BY `product_id`");
$save_settings = array();
while($row= mysql_fetch_array($res)){
$product_id	=	$row['product_id'];
$res2 = mysql_query("select * from`order_products` where `product_id`='$product_id'");
$num = mysql_num_rows($res2);
$save_settings[$num] = $product_id;
}
krsort($save_settings);
$a = 0;
$arrayNumRows = count($save_settings);
if($arrayNumRows>0){
?>
            <div <?php if ($arrayNumRows > 3){?> class="merchantRow" <?php } ?>>
              <?php
			  $i =0;
foreach ($save_settings as $key => $product_id) {

			$qry = "select * from `products` where `id`='$product_id' ORDER BY id DESC";
			$res = mysql_query($qry);
			
			$numRows = mysql_num_rows($res);
			
			while($row = mysql_fetch_array($res)){
			$a++;
			$i++;?>
              <div class="merchant" 
			<?php
			if($i==3){?>
			 style="border-right:none"
			<?php
			}?>
			>
                <?php
				if($row['image']!='' && file_exists(DOC_ROOT . 'images/products/ico_' . $row['image'] ) ){
				$showImg="<span class='imgOuter'><img src=".PRODUCT_IMAGE_PATH."ico_".$row['image']." align='left'></span>";
				}
				else{
				$showImg='<img src="admin/images/no_image.png" align="left">';
				}
			?>
                <a href="<?=ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html"><?php echo $showImg; ?></a> <span class="merchantHeading"><a href="<?=ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html"><?php echo $row['name'];?></a></span><br />
                <?php echo getMarketCategoryName($row['category_id']); ?><br>
                <span class="merchantStars">
                <?php getRatingAggregate($row['id'],'product'); ?>
                </span> <br />
                <br />
              </div>
              <?php
			  if($i%3==0){?>
            </div>
            <div <?php if (($arrayNumRows-1)!=$a && ($arrayNumRows-2)!=$a && ($arrayNumRows-3)!=$a && ($arrayNumRows > 3)){ echo 'class="merchantRow"'; }?>>
              <?php
			  }
			  }
			}}
			else{
			echo "<br><strong>No Record Found</strong><br>";
			}
			?>
            </div>
            <div class="clear"></div>
          </div>
        </div>
        <!-- Box End -->
      </div>
      <!-- Markeet Place LeftArea End -->
      <!-- Markeet Place RightArea Start -->
      <div class="marketRight">
        <div class="box">
          <div class="heading">Quick Links</div>
          <div class="sidebarMdl">
            <ul>
              <li>Welcome, <?php echo $logged_in_member_name; ?></li>
              <li><a href="profile_setting.php">My Account</a></li>
              <li></li>
			  <li><span>Categories</span></li>
              <?php
			$res = mysql_query("select * from `market_category` ORDER BY id DESC");
			while($row = mysql_fetch_array($res)){
			?>
              <li><a href="<?php echo ABSOLUTE_PATH.'cat/'.$row['seo_name'].'.html'; ?>"><?php echo $row['name']; ?></a></li>
              <?php
			  }
			  ?>
            </ul>
          </div>
        </div>
        <br>
        <br>
        <div class="box">
          <div class="heading">Top Deals</div>
          <div class="sidebarMdl">
            <?php
		  $res = mysql_query("select * from `products` ORDER BY `discount` DESC LIMIT 0,5");
		  while ($row = mysql_fetch_array($res)){
				if($row['image']!='' && file_exists(DOC_ROOT . 'images/products/ico_' . $row['image'] ) ){
				$showImg="<span class='imgOuter'><img src=".PRODUCT_IMAGE_PATH."ico_".$row['image']." align='left'></span>";
				}
				else{
				$showImg='<img src="admin/images/no_image.png" align="left">';
				}
		  ?>
            <div class="marketDeals"> <a href="<?=ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html"><?php echo $showImg; ?></a> <span class="merchantHeading"><a href="<?=ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html"><?php echo $row['name'];?></a></span> <br>
              <?php echo getMarketCategoryName($row['category_id']); ?><br>
              <br>
              <input type="image" src="<?=IMAGE_PATH?>buy_special.gif" onclick="window.location.href='<?=ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html'" value="Buy Special" name="buy">
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <!-- Markeet Place RightArea End -->
      <div class="clear"></div>
    </div>
    <!-- Markeet Place InrBody End -->
  </div>
</div>
<?php require_once('includes/footer.php');?>
