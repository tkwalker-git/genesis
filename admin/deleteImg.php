<?	
require_once("database.php");
$id		= $_GET['id'];
$tbl	= $_GET['tbl'];
$fld	= $_GET['fld'];
$img	= $_GET['img'];
$dir	= $_GET['dir'];

if($tbl == 'homes_images' || $tbl == 'products_images' || $tbl =='event_gallery_images' || $tbl == 'annual_images')
	mysql_query("delete from $tbl where id='$id' limit 1");
else
	mysql_query("update $tbl set $fld='' where id='$id' limit 1");

@unlink($dir . $img);
@unlink($dir . "th_" . $img);
@unlink($dir . "ico_" . $img);
@unlink($dir . "sub_" . $img);

?>

<?	
require_once("database.php");
$id		= $_GET['id'];
$tbl	= $_GET['tbl'];
$fld	= $_GET['fld'];
$img	= $_GET['img'];
$dir	= $_GET['dir'];

if($tbl == 'homes_images' || $tbl == 'products_images' || $tbl =='event_gallery_images' || $tbl == 'annual_images')
	mysql_query("delete from $tbl where id='$id' limit 1");
else
	mysql_query("update $tbl set $fld='' where id='$id' limit 1");


@unlink($dir . $img);
@unlink($dir . "th_" . $img);
@unlink($dir . "ico_" . $img);
@unlink($dir . "sub_" . $img);

?>