<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

	
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	$text			= $_POST['text'];
	
	$sql = "select * from `products` where `name` like '%$text%' || `desc` like '%$text%'";
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
	$sql = "select * from `products` where `name` like '%$text%' || `desc` like '%$text%' ORDER BY `id` DESC" . $limit;
	$res = mysql_query($sql);
	if(mysql_num_rows($res)){
	while($ro = mysql_fetch_array($res)){
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
		  $show_products_image	=	"<img src='".IMAGE_PATH."small_noimage.gif' width='180' height='180' align='left' />";
		  }}
		  else{
		  $show_products_image	=	"<img src='".IMAGE_PATH."small_noimage.gif' width='180' height='180' align='left' />";
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
	
	<?php } }
	?>
	<div class="clr"></div>
	
	<?php
	if ($pagenum > 1) { ?>
			 <div class="ev_fltlft" style="padding:10px 0 0 25px;"><img style="cursor:pointer"  onClick="loadsearchdel('<?php echo ABSOLUTE_PATH;?>','prev',<?php echo $pagenum;?>,'<?php echo $text; ?>');" src="<?php echo IMAGE_PATH; ?>prev.png"></div>
		<?php } else { ?>
			<div class="ev_fltlft" style="padding:10px 0 0 25px;"><img src="<?php echo IMAGE_PATH;?>prevdisable.png" alt="" title=""></div>
		<?php } ?>
		
		&nbsp; &nbsp;
		
		
		<?php if ( $pagenum < $total_pages ) { ?>
		<div class="ev_fltrght" style="padding:10px 25px 0 0;"><img  style="cursor:pointer" onClick="loadsearchdel('<?php echo ABSOLUTE_PATH;?>','next',<?php echo $pagenum;?>,'<?php echo $text; ?>');" src="<?php echo IMAGE_PATH;?>nxt.png" alt="" title=""></div>
		<?php } else { ?>
			<div class="ev_fltrght" style="padding:10px 25px 0 0;"><img src="<?php echo IMAGE_PATH;?>nxtdisable.png" alt="" title=""></div>
		<?php } ?>
		
			<div class="clr"></div>
	