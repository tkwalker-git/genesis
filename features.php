<?php
	require_once('admin/database.php');
	
	$seo_name	=	$_GET['page'];
	
	$meta_res = mysql_query("select * from `site_pages` where `seo_name`='$seo_name'");
	while($meta_row = mysql_fetch_array($meta_res)){
	 	$dmeta_title 		= $meta_row['meta_title'];
		$dmeta_desc 		= DBout($meta_row['meta_desc']);
		$dmeta_keywords 	= DBout($meta_row['meta_keywords']);
	 }
	

	require_once('includes/header.php');
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
    <div class="bc_featuresBox">
      
      <!-- Left Area Start -->
      <div class="bc_featuresLeft">
        <ul>
          <li><a href="<?php echo ABSOLUTE_PATH; ?>features.php" <?php if (!$_GET['page'] || $_GET['page']==''){ echo 'class="active"'; } ?>><img src="<?php echo IMAGE_PATH; ?>icon_feature_eventgrabber.png" alt="" title="" align="left" /> What is Eventgrabber </a></li>
          <?php
		  
		  $tempRemove = array("My Network","CityPulse","Event Marketplace","Friends Manager");
		  
		  $res = mysql_query("select * from `site_pages` where `page_type`='feature'");
		  $images	=	array(
		  	"icon_feature_recommended.png",
			"icon_feature_eventwall.png",
			"icon_feature_eventmanger.png",
			"icon_feature_network.png",
			"icon_feature_friends.png",
			"icon_feature_citypuls.png",
			"icon_feature_marketplace.png",
			"icon_feature_marketplace.png"
			);
		  $i=0;
		  while($row = mysql_fetch_array($res)){
		  	if ( !in_array($row['page_title'],$tempRemove) ) {
		  ?>
          <li><a href="?page=<?php echo $row['seo_name'];?>" <?php if ($_GET['page'] == $row['seo_name']){ echo 'class="active"'; } ?>><img src="<?php echo IMAGE_PATH.$images[$i]; ?>" alt="" title="" align="left" /> <?php echo $row['page_title']; ?> </a></li>
          <?php 
		  	}
		  $i++;
		  	
		  }
		  ?>
        </ul>
      </div>
      <!-- Left Area End -->
      <!-- Right Area Start -->
      <div class="bc_featuresRight">
	  <?php
	   if(!$_GET['page']){?>
		   <img src="<?php echo IMAGE_PATH?>banner_what_is_eventgrabber.jpg" alt="" title="">
	   <?php } ?>
        <!-- bodyInr Start -->
        <div class="bodyInr">
		<?php
		if(!$_GET['page']){?>
		<span class="head">What Can You Do on Eventgrabber?</span> <span class="slogn">Excellent question! Let's take a quick tour</span>
		<?php } ?>
          <!-- eventgrabber_bodyInr Start -->
          <div class="eventgrabber_bodyInr">
		  <?php
		  if($_GET['page']){
		  $seo_name	=	$_GET['page'];
		  $res = mysql_query("select * from `site_pages` where `seo_name`='$seo_name'");
		  while($row = mysql_fetch_array($res)){ 
		  	
		  ?>
		  <div class="title"><?php echo $row['page_title']; ?></div>
		  <div class="clr"></div>
		  <?php 
		  if ( $row['image'] != '' && file_exists(DOC_ROOT . 'images/' . $row['image'] ) ){?>
		  <img src="<?php echo IMAGE_PATH.'th_'.$row['image']; ?>" alt="" title="" align="left" class="borderImage">
		  <?php } ?>
		   
		   <?php  // echo DBout($row['page_content']); ?>
		   
		  <div class="clr"></div>
		  
		  <?php
		  	
		  }
		  
		  }
		  else{
		  $res = mysql_query("select * from `site_pages` where `page_type`='feature'");
		  $i = 0;
		  ?>
		  <div style="height:44px;">&nbsp;</div>
		  <?php
		  while($row = mysql_fetch_array($res)){
		  	if ( !in_array($row['page_title'],$tempRemove) ) {
			  $i++;
			  $image = $row['image'];
		  
		 	if ( $image != '' && file_exists(DOC_ROOT . 'images/' . $image ) ) {
			$image	=	IMAGE_PATH.'th_'.$image;
			}
			else{
			$image	=	IMAGE_PATH."no_image.gif";
			}
			
		  ?>
		  <!--<div style="height:44px;">&nbsp;</div>-->
            <div class="box" <?php if($i%2 == 0){ echo 'style="padding:0"';} ?>>
              <div class="title"><?php echo $row['page_title']; ?></div>
              <div class="imgBox"> <a href="?page=<?php echo $row['seo_name'];?>"><img src="<?php echo $image; ?>" alt="" title=""></a> </div>
              <div class="eventGrabberDesc"> 
			  
			  <?php
			 	$page_content = strip_tags(DBout($row['page_content']));
				echo substr($page_content,0,124)."...";
				echo '<a href="?page='.$row["seo_name"].'">Learn More</a>';
				?>
				</div>
            </div>
			<?php
			if($i%2 == 0){
			echo '<div class="clear"></div>';
			}
			}
			 }} ?>
		
          </div>
          <!-- eventgrabber_bodyInr End -->
        </div>
        <!-- bodyInr End -->
      </div>
      <!-- Right Area End -->
      <div class="clear"></div>
    </div>
  </div>
  <!-- Start Middle-->
</div>
<?php require_once('includes/footer.php');?>