<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

 if (isset($_GET['cat']) && $_GET['cat'] != '' ) {
  		$cat = $_GET['cat'];
		$subcat_q = "SELECT * FROM `disease_subcategory` WHERE cat_id = '". $cat ."' ORDER BY id ASC";
		$res = mysql_query($subcat_q) ;
 }
 ?>  
 
<select name="disease_subcategory" class="selectBig" onChange="dynamic_Select('ajax/disease_conditions.php', this.value, '', 'conditions' );" >
	  <option value=""><?php if (isset($_GET['class'])){ echo "-- Sub Category --"; } else{ echo ''; } ?></option> 
	  <?php 
	  	while( $r = mysql_fetch_assoc($res) ){  
	  		if ( $r['id'] == $_GET['subcat'] )
				$sele = 'selected="selected"';
			else
				$sele = '';	
	  ?>
	 <option <?php echo $sele;?> value="<?php echo $r['id']; ?>"><?php echo $r['sub_cat_name']; ?></option>  
<?php } ?>
 </select>