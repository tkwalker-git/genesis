<?php
/*<?php if($r['id'] == $bc_subcategory_id){?> selected="selected" <?php } ?> */
require_once('database.php');

 if (isset($_GET['cat']) && $_GET['cat'] != '' ) {
  		$cat = $_GET['cat'];
		//echo $subcat = $_GET['subcat'];
		$subcat_q = "SELECT * FROM disease_category WHERE id = '$cat' ORDER BY id ASC";
		$res = mysql_query($subcat_q) or die("Error in Query");
 }
 ?>  
 
<select name="subcategory_id"
<?php if (isset($_GET['class'])){ echo "class='".$_GET['class']."'"; } 
else{ echo 'class="bc_input"'; } ?>>
	  <option value=""><?php if (isset($_GET['class'])){ echo "-- Select Secondary Category --"; } else{ echo ''; } ?></option> 
	  <?php 
	  	while( $r = mysql_fetch_assoc($res) ){  
	  		if ( $r['id'] == $_GET['subcat'] )
				$sele = 'selected="selected"';
			else
				$sele = '';	
	  ?>
	 <option <?php echo $sele;?> value="<?php echo $r['id']; ?>"> <?php echo $r['name']; ?></option>  
<?php } ?>
 </select>