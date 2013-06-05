<?php
		require_once('admin/database.php');
		require_once('includes/header.php');

$category_seo_name 	= isset($_GET['seo_name']) ? $_GET['seo_name'] : $_GET['category'];
$catRow			= getCompleteRow("market_category", "where seo_name='$category_seo_name'");
$category_id 	= $catRow['id'];
$catName		= DBout($catRow['name']);
$catDesc		= DBout($catRow['descr']);

?>

<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
      <!-- Market Place Top Start -->
      <div class="marketPlaceTop">
        <div class="markeetPlace_title"> Marketplace</div>
        <div class="markeetPlaceTopMenu"> <a href="<?=ABSOLUTE_PATH;?>market_place.php"><img src="<?=IMAGE_PATH?>featured.gif" alt="" title=""></a> <a href="<?php echo ABSOLUTE_PATH;?>market_categories.php"><img src="<?php echo IMAGE_PATH?>featured_category_over.gif" alt="" title=""></a> <a href="<?php echo ABSOLUTE_PATH;?>market_purchased.php"><img src="<?php echo IMAGE_PATH?>purchased.gif" alt="" title=""></a> </div>
        <div class="clear"></div>
      </div>
      <!-- Market Place Top END -->
      <!-- Markeet Place InrBody Start -->
      <div class="marketInrBody">
        <div class="markPurchasedTitle"> <span class="hd"><?php echo $catName;?></span>
          <div class="clear"></div>
        </div>
        <div class="markPurchasedMdl">
            <?php
			$showProducts = 6;
			$a = 0;
			$res = mysql_query("select * from `products` where `category_id`='$category_id' ORDER BY id DESC LIMIT 0, $showProducts");
			$numRows = mysql_num_rows($res);
			if($numRows){
			?>
            <div <?php if ($numRows > 4){?> class="merchantRow" <?php } ?>>
              <?php
			$i =0;
			while($row = mysql_fetch_array($res)){
			$a++;
			$i++;?>
              <div class="merchant" 
			<?php
			if($i==4){?>
			 style="border-right:none"
			<?php
			}?>
			>
			<?php
				if($row['image']!='' && file_exists(DOC_ROOT . 'images/products/ico_' . $row['image'] ) ){
				$showImg="<span class='imgOuter'><img src=".PRODUCT_IMAGE_PATH."ico_".$row['image']." align='left'></span>";
				}
				else{
				$showImg='<img src="'.ABSOLUTE_PATH.'admin/images/no_image.png" align="left">';
				}
			?>
                <a href="<?php echo ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html"><?php echo $showImg; ?></a> <span class="merchantHeading"><a href="<?php echo ABSOLUTE_PATH?>deal/<?php echo $row['seo_name']."/".$row['id']; ?>.html"><?php echo $row['name'];?></a></span><br />
                
                <span class="merchantStars">
                <?php getRatingAggregate($row['id'],'product'); ?>
                </span> <br />
                <br />
              </div>
              <?php
			  if($i%4==0){?>
            </div>
            <div <?php if (($numRows-1)!=$a && ($numRows-2)!=$a && ($numRows-3)!=$a && $numRows > 4){ echo 'class="merchantRow"'; }?>>
              <?php
			  }
			}
			}
			else{
			echo "<div align='center' style='color:#ff0000'><br>No Record Found<br>&nbsp;</div>";
			}
			?>
            </div>
            <div class="clear"></div>
          </div>
        <div class="markPurchasedBottom">&nbsp;</div>
      </div>
      <!-- Markeet Place InrBody End -->

  </div>
</div>
<?php require_once('includes/footer.php');?>