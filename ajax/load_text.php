<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	$table	= $_GET['table'];
	$field = $_GET['field'];
	$id	= $_GET['id'];


	$res = mysql_query("select * from `$table` where `id`='$id'");
	while($row = mysql_fetch_array($res)){
		$text = DBout($row[$field]);
	}
?>
<style>
.roundedCorner
{
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	-khtml-border-radius: 10px;
	behavior:url(/border-radius.htc);
	border-radius: 10px;
	background-color:#FEFEFE;
}
</style>
<div style="margin:auto; width:400px">
<div style="cursor: pointer; margin-right: -10px; margin-top:-10px; position: absolute; right: 0;" title="Close"><img onclick="hideOverlayer();"
src="<?php echo IMAGE_PATH;?>fileclose.png"></div>
<div class="roundedCorner" style="background:#FFFFFF; padding:15px; text-align:left; min-height:250px; max-height:515px; overflow:auto">
	<?php echo $text; ?>
</div>
</div>