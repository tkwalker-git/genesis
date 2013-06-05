<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

$image_name = urldecode($_GET['image_url']);
$image_name = str_replace(' ','%20',$image_name);

list($viw, $vih, $type33, $attr33) = getimagesize($image_name);
list($viw, $vih) = getPropSize($viw, $vih, 474,400);

?>
<div style="cursor: pointer; margin-right: -10px; position: absolute; right: 0;" title="Close"><img onclick="hideOverlayer();"
src="<?php echo IMAGE_PATH;?>fileclose.png"></div>
<div class="locationBox">

		<img id="img" src="<?php echo $image_name; ?>" width="<?php echo $viw; ?>" height="<?php echo $vih; ?>" />
</div>