<?php

	require_once('../admin/database.php');
	require_once('../site_functions.php');

	$sub_cat	= $_POST['cat'];
	
	if($sub_cat == '')
		$sub_cat	= $_GET['cat'];

	$qry	= "select * from `findings_category` where `disease_subcategory_id`='". $sub_cat ."'";
	$res	= mysql_query($qry);
	$i		= 0;
	while($row = mysql_fetch_array($res)){
	$i++;
	?>
    <div style="float:left; width:50%">
		<input type="checkbox" name="conditions[]" <?php if (is_array($bc_conditions) && in_array($row['id'],$bc_conditions)){ echo 'checked="checked"'; } ?> value="<?php echo $row['id']; ?>" /> <?php echo $row['finding_name']; ?>
    </div>
	<?php } ?>