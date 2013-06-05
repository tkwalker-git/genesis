<?php require_once('admin/database.php');

if(isset($_GET["id"])){
	$frmID	=	$_GET["id"];
	$meta_title	=	"Update";
	}
	else{
	$meta_title	=	"Create";
	}
$meta_title	.=	" Deal";


require_once('includes/header.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";


		



$action = "save";
$sucMessage = "";

	if ( isset($_POST['submit']) ) {
	$bc_name			=	DBin($_POST["name"]);
	$bc_desc			=	DBin($_POST["desc"]);
	$bc_category		=	DBin($_POST["category"]);
	$bc_condition		=	DBin($_POST["condition"]);
	$bc_discount		=	DBin($_POST["discount"]);
	$seo_name			=	DBin($_POST["name"]);
	$bc_list_price		=	DBin($_POST["list_price"]);
	$bc_sale_price		=	DBin($_POST["sale_price"]);
	$bc_model			=	DBin($_POST["model"]);
	$bc_manufacturer	=	$_POST["manufacturer"];
	$bc_featured		=	$_POST["featured"];
	
	$bc_seo_name = make_seo_names($seo_name,"products","seo_name","");
	
	if ( trim($bc_name) == '' )
		$errors[] = 'Please enter Deal Title';
	if ( trim($bc_category) == '' )
		$errors[] = 'Please select Category';
	if ( trim($bc_desc) == '' )
		$errors[] = 'Please enter Description';
	if ( trim($bc_sale_price) == '' )
		$errors[] = 'Please enter Sale Price';
		
		
	if ( count($errors) > 0 ) {
	
		$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
		for ($i=0;$i<count($errors); $i++) {
			$err .= '<li>' . $errors[$i] . '</li>';
		}
		$err .= '</ul></td></tr></table>';	
	}
	
	if (!count($errors)) {
	$bc_image = '';
	
	if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["tmp_name"])) {
	
		$bc_image  = time() . "_" . $_FILES["image"]["name"] ;
		
			if ($action1 == "edit") {
				deleteImage($frmID,"products","image");
			}
			move_uploaded_file($_FILES["image"]["tmp_name"], 'images/products/'.$bc_image);
			makeThumbnail($bc_image, 'images/products/', '', 250, 500,'th_');
			makeThumbnail($bc_image, 'images/products/', '', 88, 86,'ico_');
			//@unlink('images/products/'.$bc_image);
			$sql_img = " ,  image = '$bc_image'";
		}
		
	$rs = mysql_query("select * from `products` where `id`='$frmID'");
	if(mysql_num_rows($rs)){
	$action = "edit";
	}
	
	
	if($action == "save"){
	$res = mysql_query("INSERT INTO `products` (`id`, `name`, `desc`, `category_id`, `condition`, `discount`, `seo_name`, `list_price`, `sale_price`, `model`, `manufacturer`, `user_id`, `featured`, `image`) VALUES (NULL, '$bc_name', '$bc_desc', '$bc_category', '$bc_condition', '$bc_discount', '$bc_seo_name', '$bc_list_price', '$bc_sale_price', '$bc_model', '$bc_manufacturer', '$user_id', '$bc_featured', '$bc_image');");
$product_id = mysql_insert_id();
}
if($action=="edit"){
$res2 = mysql_query("UPDATE `products` SET `name` = '$bc_name', `desc` = '$bc_desc', `category_id` = '$bc_category', `condition` = '$bc_condition', `discount` = '$bc_discount', `seo_name` = '$bc_seo_name', `list_price` = '$bc_list_price', `sale_price` = '$bc_sale_price', `model` = '$bc_model', `manufacturer` = '$bc_manufacturer', `featured`='$bc_featured' $sql_img WHERE `id` = '$frmID'");

$product_id = $frmID;	
}
	$frmID = $product_id;
if ( is_array($_FILES['eimage']) ) {
			for($i=0;$i< count($_FILES['eimage']); $i++) {
				$einame = $_FILES['eimage']['name'][$i];
				$etname = $_FILES['eimage']['tmp_name'][$i];
				if ( $einame != '') {
					$ei_image = time() . '_' . $einame; 
					move_uploaded_file($etname, 'images/products/'.$ei_image);
					makeThumbnail($ei_image, 'images/products/', '', 250, 500,'th_');
					makeThumbnail($ei_image, 'images/products/', '', 88, 86,'ico_');
					//@unlink('images/products/'.$ei_image);
					if ( $ei_image != '' && $product_id > 0 )
						mysql_query("INSERT INTO `products_images` (`id`, `product_id`, `image`) VALUES (NULL, '$product_id', '$ei_image')");
				}		
			}
		}
		

	echo "<script>window.location.href='".ABSOLUTE_PATH."saved.php?type=deal&id=".$product_id."'</script>";
	}

	else{
	$sucMessage = $err;
	}
	}
		

if ( $_GET['delete'] > 0 ) {
	$r = mysql_query("select * from products_images where id='". $_GET['delete'] ."'");
	if ( $rr = mysql_fetch_assoc($r) ) {
		@unlink('images/products/'.$rr['image']);
		@unlink('images/products/th_'.$rr['image']);
	}	
	mysql_query("delete from products_images where id='". $_GET['delete'] ."'");
	?>
<script>window.location.href="create_deal.php?id=<?php echo $frmID;?>";</script>
<?php
}
		
		
	if($frmID){
	$rs = mysql_query("select * from `products` where `id`='$frmID'");
	while($r = mysql_fetch_array($rs)){
	$bc_name				=	$r["name"];
	$bc_desc				=	$r["desc"];
	$bc_category			=	$r["category_id"];
	$bc_condition			=	$r["condition"];
	$bc_discount			=	$r['discount'];
	$bc_seo_name			=	$r["seo_name"];
	$bc_list_price			=	$r['list_price'];
	$bc_sale_price			=	$r['sale_price'];
	$bc_model				=	$r["model"];
	$bc_manufacturer		=	$r["manufacturer"];
	$bc_user_id				=	$r["user_id"];
	$bc_image				=	$r["image"];
	$bc_featured			=	$r["featured"];
	}
	if($bc_user_id!=$user_id){
		echo "<script>window.location.href='manage_deals.php'</script>";
	}
	}
		
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<script language="javascript">
function add_newImage(id)
{
	var next_tr = id+1;
	var new_url_feild = '<tr id="image_tr_'+next_tr+'"><td align="right" width="20%"  class="evField">Extra Images(s):</td><td width="80%" align="left" class="bc_input_td"><input type="hidden" value="'+next_tr+'" /><input type="file" name="eimage[]" id="eimage'+next_tr+'" class="" /><span id="add_more_btn_'+next_tr+'"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newImage('+next_tr+');">&nbsp;&nbsp;Add More</span></span></td></tr>';
	$('#add_more_btn_'+id).html('&nbsp;&nbsp;<img src="images/delete.png" onclick="remove_image('+id+')" style="cursor:pointer">');
	$('#add_url_ist').append(new_url_feild);
	
}

function remove_image(id){
document.getElementById('eimage'+id).value='';
document.getElementById('image_tr_'+id).style.display='none';
}

function deleteExtraImage(id)
{
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		window.location.href= 'create_deal.php?id=<?php echo $frmID;?>&delete='+id;
	}
}


	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "advanced",
		elements : "desc",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen,|,print,|,ltr,rtl,|,styleprops,hr,removeformat,|,preview,help,code",
		theme_advanced_buttons3 : "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "style.css",
	});
	
	
</script>

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
    <div class="eventMdlBg">
      <div class="eventMdlMain"> <span class="viewevents_title">
        <?php
			if ($frmID){
			echo "Update";}
			else{ echo "Create"; }?>
        <strong>Deal</strong></span>
        <div class="featuresBoxNew">
          <div class="featuresBotBgNew">
            <div class="featuresTopBgNew">
              <div class="creatProduct">
                <form method="post" name="bc_form" enctype="multipart/form-data" action="">
                  <div class="error"><?php echo $sucMessage; ?></div>
                  <div class="evField">Deal Title: <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <input type="text" maxlength="100" class="inp" id="eventname"  name="name" value="<?php echo $bc_name; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Category: <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <select name="category" class="inp" style="width:305px">
                      <option value="">~Select~</option>
                      <?php
		$res = mysql_query("select * from `market_category` ORDER BY `name` ASC");
		while($row = mysql_fetch_array($res)){?>
                      <option value="<?php echo $row['id']; ?>" <?php if ($bc_category==$row['id']){ echo 'selected="selected"';} ?>><?php echo $row['name']; ?></option>
                      <?php		
		}
		?>
                    </select>
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Description: <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <textarea name="desc" class="bc_input" style="width:550px;height:250px;" />
                    <?php echo $bc_desc; ?>
                    </textarea>
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Featured: </div>
                  <div class="evLabal">
                    <input type="radio" name="featured" value="0" id="no" <?php if ($bc_featured!='1'){ echo 'checked="checked"'; } ?> />
                    No
                    &nbsp;
                    <input type="radio" name="featured" value="1" id="yes" <?php if ($bc_featured=='1'){ echo 'checked="checked"'; } ?> />
                    Yes </div>
                  <div class="clr"></div>
                  <div class="evField">Condition: </div>
                  <div class="evLabal">
                    <input type="radio" name="condition" value="New" id="new" <?php if ($bc_condition=='New'){ echo 'checked="checked"'; } ?> />
                    New
                    &nbsp;
                    <input type="radio" name="condition" value="Used" id="used" <?php if ($bc_condition=='Used'){ echo 'checked="checked"'; } ?> />
                    Used </div>
                  <div class="clr"></div>
                  <div class="evField">Discount: </div>
                  <div class="evLabal">
                    <input type="text" class="inp" name="discount" value="<?php echo $bc_discount; ?>" style="width:30px" />
                    % </div>
                  <div class="clr"></div>
                  <div class="evField">List Price: </div>
                  <div class="evLabal">
                    <input type="text" class="inp" name="list_price" value="<?php echo $bc_list_price; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Sale Price: <font color="#FF0000">*</font></div>
                  <div class="evLabal">
                    <input type="text" class="inp" name="sale_price" value="<?php echo $bc_sale_price; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Model: </div>
                  <div class="evLabal">
                    <input type="text" class="inp" name="model" value="<?php echo $bc_model; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Manufacturer: </div>
                  <div class="evLabal">
                    <input type="text" class="inp" name="manufacturer" value="<?php echo $bc_manufacturer; ?>" style="width:300px" />
                  </div>
                  <div class="clr"></div>
                  <div class="evField">Main Image: </div>
                  <div class="evLabal">
                    <?php 
if( $bc_image != '' ) {
	echo '<img src="images/products/ico_'.$bc_image .'" class="dynamicImg" id="delImg_image"/>';
	$image_del = '<img src="admin/images/remove_img.png" class="delImg" id="'.$frmID.'" style="cursor:pointer" rel="products|image|'.$bc_image.'|images/products/" />';
}
else
	echo '<img src="admin/images/no_image.png" class="dynamicImg"width="75" height="76" />';
?>
                    <input type="file" name="image" id="image" />
                    <br />
                    <?php echo $image_del?> </div>
                  <div class="clr"></div>
                  <div>
                    <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center" id="add_url_ist">
                      <?php 

if($frmID) { 
	$msql	=	"select * from products_images where product_id	 = $frmID";
	$mres	=	mysql_query($msql);
	$count = 0;
	if ( mysql_num_rows($mres) > 0 ) {
	?>
                      <tr >
                        <td align="right" width="22%" class="evField">Extra Images(s):</td>
                        <td align="left" width="78%" class="bc_input_td"><?php
	while ($mrow = mysql_fetch_assoc($mres))
	{
		$count ++;
		$bce_image = $mrow['image'];
		echo '<div style="float:left; margin-right:10px"><img src="images/products/ico_'.$bce_image .'" class="dynamicImg" id="delImg_image" align="left" />';
	?>
                          <a href="javascript:deleteExtraImage(<?php echo $mrow['id'] ;?>)"><img src="images/delete.png" border="0" align="left" ></a>
                          <?php } ?></td>
                      </tr>
                      <?php } ?>
                      <?php } ?>
                      <tr id="image_tr_1">
                        <td align="right" width="22%" class="evField">Extra Images(s):</td>
                        <td width="78%" align="left" class="bc_input_td"><input type="hidden" value="1" />
                          <input type="file" name="eimage[]" id="eimage" class="" value=""/>
                          <span id="add_more_btn_1"><span style="cursor:pointer; font-size:12px; color:#0033CC" onclick="add_newImage(1);">&nbsp;&nbsp;Add More</span></span> </td>
                      </tr>
                    </table>
                  </div>
                  <div align="center"><br>
                    <input type="image" src="<?php echo IMAGE_PATH;?>saveDeal.gif" name="submit" value="submit">
                    <input type="hidden" name="submit" value="submit">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');?>
<script type="text/javascript">
$(".delImg").click(function() {
	var con = confirm("Are you sure you want to delete this image?");
	if( con == true ) {
		var imgID = $(this).attr('id');
		var imgInfo = $(this).attr('rel').split('|');
		$(this).load("admin/deleteImg.php?id=" + imgID + "&tbl=" + imgInfo[0] + "&fld=" + imgInfo[1] + "&img=" + imgInfo[2] + "&dir=" + imgInfo[3] );
		$(this).hide();
		$("#delImg_" + imgInfo[1]).attr("src", "admin/images/no_image.png");
	}
});
</script>