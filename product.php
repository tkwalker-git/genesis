<?php 

require_once('admin/database.php');
require_once('site_functions.php');

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

$product_id 	= $_GET['product_id'];




$sql = "select * from products where id='". $product_id ."'";
$res = mysql_query($sql);
while($r = mysql_fetch_array($res)){
	$bc_name				=	$r["name"];
	
	$bc_category			=	$r["category_id"];
	
	$bc_categoryName		=	getMarketCategoryName($bc_category);
	
	$bc_condition			=	$r["condition"];
	$bc_seo_name			=	$r["seo_name"];
	$bc_list_price			=	$r['list_price'];
	$bc_sale_price			=	$r['sale_price'];
	$bc_model				=	$r["model"];
	$bc_manufacturer		=	$r["manufacturer"];
	$bc_desc				=	DBout($r["desc"]);
	$bc_user_id				=	$r["user_id"];
	$bc_discount			=	$r["discount"];
	$bc_image				=	$r["image"];
	$meta_title				= 	$bc_name;
}

include_once('includes/header.php');

?>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>skin.css"/>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.jcarousel.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>/js/jquery-ui_1.8.7.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#mycarousel').jcarousel();
	});
	
	$(document).ready(function() {
		$(".fancybox").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	});
</script>

<div id="main">
	<div id="main-inner">
		<div id="contents">
			<div id="contents-top"></div>
				<div id="contents-middle">
					<div id="event-details">
					 <div id="event-left">
						<div id="event-flyer-bg">
							<div id="event-flyer">
								<div style="width:272px; overflow:hidden; max-height:375px">
								<?php
								if($bc_image!='' && file_exists(DOC_ROOT . 'images/products/th_' . $bc_image ) ){
									$showImg=PRODUCT_IMAGE_PATH."th_".$bc_image;
								}
								else{
									$showImg=ABSOLUTE_PATH."admin/images/no_image.png";
								}
								?>
									
									<img src="<?php echo $showImg; ?>" />
									
								</div>	
							</div>
						</div><!-- event-flyer-bg -->
						<br>
						<div class="clear"></div>
						<center>
						<div style="height: 25px;line-height: 27px;padding-left: 58px;">
						<form method="post" action="<?=ABSOLUTE_PATH;?>cart.php" onSubmit="return checkQty();">
						<label><strong>Qty:</strong> <input onkeypress="return isNumberKey(event)" name="qty" type="text" style="width:25px" value="1"> &nbsp; 
							</label>
							<label>
							<input type="image" src="<?=IMAGE_PATH;?>addtocart.gif" name="addToCart" value="Add to Cart">
							<input type="hidden" name="addToCart" value="Add to Cart">
							<input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
							<br><br>&nbsp;</label>
							</form></div>
						</center>
					 </div><!-- event-left -->	
					 <div id="event-right">
					 	<table width="100%" cellpadding="10" cellspacing="0" align="left">
						<tr>
							<td align="left">
								<span class="event-name-big"><?php echo ucwords($bc_name); ?></span>
							</td>
						</tr>
						<tr>
							<td align="left">
								<span class="title">Category:</span> <span class="cat-name">
								<a class="cate_link" href="#"><?php echo ucwords($bc_categoryName); ?></a></span>
							</td>
						</tr>
					<?php
					if($bc_manufacturer!=''){?>
							<tr>
							<td align="left">
								<span class="title">Manufacturer:</span>
								<span class="cost"><?php echo $bc_manufacturer;?></span>
							</td>
						</tr>
						<?php } 
						if($bc_model!=''){?>
							<tr>
							<td align="left">
								<span class="title">Model:</span>
								<span class="cost"><?php echo $bc_model;?></span>
							</td>
						</tr>
						<?php } 
						if($bc_condition!=''){?>
							<tr>
							<td align="left">
								<span class="title">Condition:</span>
								<span class="cost"><?php echo $bc_condition;?></span>
							</td>
						</tr>
						<?php } 
						if($bc_sale_price!=''){?>
							<tr>
							<td align="left">
								<span class="title">Sale Price:</span>
								<span class="cost"><?php echo $bc_sale_price;?></span>
							</td>
						</tr>
						<?php } 
						
						if($bc_list_price!='' && $bc_list_price!=0){?>
							<tr>
							<td align="left">
								<span class="title">List Price:</span>
								<span class="cost"><?php echo $bc_list_price;?></span>
							</td>
						</tr>
						<?php } 
						
						if($bc_discount!='0'){?>
							<tr>
							<td align="left">
								<span class="title">Discount:</span>
								<span class="cost"><?php echo $bc_discount;?>%</span>
							</td>
						</tr>
						<?php } 
						?>
						<tr>
							<td align="left">
								<span class="title">Summary:</span>
								<p><?php echo $bc_desc;?></p>
							</td>
						</tr>
						<tr>
							<td align="left">
								<span class="heading">
									Share this product:
									<script type="text/javascript">var addthis_pub = "UnitedDatabase";</script>
									<a  href="http://www.addthis.com/bookmark.php" onMouseOver="return addthis_open(this, '', '[URL]', '[TITLE]')" onMouseOut="addthis_close()" onClick="return addthis_sendto()" ><img src="<?php echo ABSOLUTE_PATH;?>images/share_this.png" align="absmiddle" /></a>
									<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
								</span>
								<span class="social-imgs">
									<!--
									<table cellpadding="3" cellspacing="0">
									<tr>
									<td><img src="<?php echo IMAGE_PATH;?>share-icon-1.png" /></td>
									<td>
									<script type="text/javascript">var addthis_pub = "UnitedDatabase";</script>
									<a  href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()" ><img src="<?php echo ABSOLUTE_PATH;?>images/share_this.png" align="absmiddle" /></a>
									<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
									</td>
									<td><?php //getFShareBtn($page_url);?></td>
									<td><?php //getReTweetBtn($page_url);?></td>
									</tr></table>-->
									
									
									
								</span>
							</td>
						</tr>
						</table>
					 </div><!-- event-right -->
					</div><!-- Event-details -->
					<!-- id-nav -->
					<div class="clear"></div>
					<?php
					productImagesGallery($product_id);
					getReviewsList($product_id,'product',$reviews); 
					?>
				</div><!-- contents-middle-->
			<div id="contents-bottom"></div>
		</div><!-- contents -->
	</div><!-- main-inner -->
</div><!-- main -->
<div class="clr"></div>
<?php
include_once('includes/footer.php');
?>