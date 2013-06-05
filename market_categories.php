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
      <div class="markeetPlaceTopMenu"> <a href="market_place.php"><img src="<?=IMAGE_PATH?>featured.gif" alt="" title=""></a> <a href="#"><img src="<?=IMAGE_PATH?>featured_category_over.gif" alt="" title=""></a> <a href="market_purchased.php"><img src="<?=IMAGE_PATH?>purchased.gif" alt="" title=""></a> </div>
      <div class="clear"></div>
    </div>
    <!-- Market Place Top END -->
    <!-- Markeet Place InrBody Start -->
    <div class="marketInrBody">
      <!-- Markeet Place LeftArea Start -->
      <div class="marketLeft">
        <!-- Box Start -->
        <div class="box">
          <div class="heading"> <span class="hd">Categories</span> </div>
          <div class="contentBox">
            <?php
			$showProducts = 6;
			$a = 0;
			$res = mysql_query("select * from `market_category` ORDER BY id DESC");
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
			if($row['image']!='' && file_exists(DOC_ROOT . 'images/category/ico_' . $row['image'] ) ){
			$showImg="<span class='imgOuter'><img src=".IMAGE_PATH."category/ico_".$row['image']." align='left'></span>";
			}
			else{
			$showImg="<img src='admin/images/no_image.png' align='left'>";
			}
			?>
                <a href="<?php echo ABSOLUTE_PATH.'cat/'.$row['seo_name'].'.html'; ?>"><?php echo $showImg; ?></a> <span class="merchantHeading"><a href="<?php echo ABSOLUTE_PATH.'cat/'.$row['seo_name'].'.html'; ?>"><?php echo $row['name'];?></a></span><br />
                <?php echo getFeaturedDeals($row['id'],'ASC','3','10',$row['seo_name']); ?>
                
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
      </div>
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
          </ul>
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