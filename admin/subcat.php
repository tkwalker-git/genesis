<?php
require_once('database.php');
//include_once(CLASSES."database_class.php");
 //$obj = new DATABASE();
$cat_id =  $_REQUEST['catid'];
$subcatid = $_REQUEST['subcatid'];
$totalRows = 0;
if($cat_id > 0)
{

	$sqlParent = "SELECT name,id FROM sub_categories where categoryid='".$cat_id."'";
	$resParent = mysql_query($sqlParent);
	$totalRows= mysql_num_rows($resParent);
}
?>

<select id="subcat" name="subcat" class="evSel" onchange='showSubCategoryVal();'>
<option value="">Select Sub Category</option>
<?php 
if($totalRows > 0)
{
	while($rowParent = mysql_fetch_array($resParent))
	{	
	?>
	<option value="<?=$rowParent['id']?>" <?php if($rowParent['id']==$subcatid){?>selected <?php } ?>><?=$rowParent['name']?></option>
	<?php } ?>
		



<?php }?>
	</select>