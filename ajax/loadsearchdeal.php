<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	
	$text = DBin($_POST['text']);
	if($text!=''){
	
	$sql = "select * from `products` where `name` like '%$text%' || `desc` like '%$text%' ORDER BY `id` DESC LIMIT 0, 2";
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
	<?php
	}
	}else{
	echo "<strong style='color:#ed6c6c; font-size: 25px;'> &nbsp;  &nbsp;  &nbsp;  &nbsp; No record found</strong>";
	}}
	else{
	echo "<strong style='color:#ed6c6c; font-size: 25px;'> &nbsp;  &nbsp;  &nbsp;  &nbsp; No record found</strong>";
	}
	?>
	 <div class="clr"></div>
	 
	 <?php
	$re = mysql_query("select * from `products` where `name` like '%$text%' || `desc` like '%$text%' ORDER BY `id` DESC");
	if(mysql_num_rows($re)>2 && $text!=''){
	 ?>
	  <div class="ev_fltlft" style="padding:10px 0 0 25px;"><img src="<?php echo IMAGE_PATH; ?>prevdisable.png"></div>
      <div class="ev_fltrght" style="padding:10px 25px 0 0;"><img src="<?php echo IMAGE_PATH; ?>nxt.png" style="cursor:pointer" onclick="loadsearchdel('<?php echo ABSOLUTE_PATH;?>','next',1,'<?php echo $text; ?>')"></div>
	<?php
	 }
	 ?>