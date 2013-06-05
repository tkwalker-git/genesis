<?php
require_once("database.php");
$id = $_GET['id'];
$table = $_GET['table'];
$link  = $_GET['link'];

if( $table == 'images' ) {
	
	$q = mysql_query("select image from images where id='$id' limit 1") or die(mysql_error());
	$r = mysql_fetch_assoc($q);
	$img = stripcslashes($r['image']);
	
	unlink("../images/gallery/ico_".$img);
	unlink("../images/gallery/th_".$img);
	unlink("../images/gallery/".$img);
}

if( $table == 'event_ticket'){
	mysql_query("delete from `event_ticket_price` where `ticket_id`='$id'");

}
mysql_query("delete from $table where id='$id' limit 1");
if($table=='products'){
$res = mysql_query("select * from `products_images` where `product_id`='$id'");
while($row = mysql_fetch_array($res)){
$img = $row['image'];
unlink("../images/products/".$img);
unlink("../images/products/th_".$img);
}
mysql_query("delete from `products_images` where `product_id`='$id'");
}
header("Location: list.php?link=$link&msg=Successfully+deleted");
?>