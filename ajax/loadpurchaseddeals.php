<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

	
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	
	$sql = "select * from `orders` where `user_id`='$user_id' && `type`='product'";
	$rsd = mysql_query($sql);
	
	$total_rec		= mysql_num_rows($rsd);
	$total_pages 	= ceil($total_rec/2);
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum * 2 ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * 2 ; 
	}
			
	$limit = ' LIMIT '. $start . ' , 2';
	
	$sql = "select * from `orders` where `user_id`='$user_id' && `type`='product' order by id DESC " . $limit;
	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res))
	{
	$order_id	=	$row['id'];
		  $product_id	=	getSingleColumn('product_id',"select * from `order_products` where `order_id`='$order_id'");
		  $re = mysql_query("select * from `products` where `id`='$product_id'");
		  while($ro = mysql_fetch_array($re)){
		  $product_image	=	$ro['image'];
		  $product_name		=	$ro['name'];
		  $product_desc		=	DBOut($ro['desc']);
		  
		  if($product_image){
		  if (file_exists(DOC_ROOT . 'images/products/' . $product_image ) ) {
		  
		  $product_imageWithPath = "../images/products/".$product_image;
		list($width, $height, $type, $attr) = @getimagesize($product_imageWithPath);
		list($width, $height) = getPropSize($width, $height, "180","180" );
		  $show_products_image	=	"<img src='".IMAGE_PATH."products/".$product_image."' width='".$width."' height='".$height."'/>";
		  }
		  else{
		  $show_products_image	=	"<img src='".IMAGE_PATH."small_noimage.gif' width='180' height='180' />";
		  }}
		  else{
		  $show_products_image	=	"<img src='".IMAGE_PATH."small_noimage.gif' width='180' height='180' />";
		  }
		  }
		  ?>
          <div class="bx">
            <div class="ev_fltlft" style=" background: none repeat scroll 0 0 #FFFFFF; margin: 0 10px 0 0;  padding: 10px;  text-align: center;   width: 180px;height:200px;"><?php  echo $show_products_image; ?></div>
            <div class="ev_fltlft" style="width:180px;">
			<span class="dealTitle"> <?php echo $product_name; ?></span> <br />
            <span class="dealDesc"><?php echo $product_desc; ?></span>
            <div class="clr"></div>
			</div>
			<div class="clr"></div>
          </div>
          <?php
		  }
		  ?>
          <div class="clr"></div>
		  
<div class="ev_fltlft" style="padding:10px 0 0 25px;">
		<?php if ($pagenum > 1) { ?>
		<img src="<?php echo IMAGE_PATH;?>prev.png" style="cursor:pointer" onclick="loadPurchasedDeals('<?php echo ABSOLUTE_PATH;?>','prev',<?php echo $pagenum;?>)" />
		<?php } else { ?>
			<img src="<?php echo IMAGE_PATH;?>prevdisable.png" />
		<?php } ?>
		</div>	
		
		
		 <div class="ev_fltrght" style="padding:10px 25px 0 0;">
		<?php if ( $pagenum < $total_pages ) { ?>
		<img src="<?php echo IMAGE_PATH;?>nxt.png" style="cursor:pointer" onclick="loadPurchasedDeals('<?php echo ABSOLUTE_PATH;?>','next',<?php echo $pagenum;?>)" />
		<?php } else { ?>
			<img src="<?php echo IMAGE_PATH;?>nxtdisable.png" />
		<?php } ?>
		</div>