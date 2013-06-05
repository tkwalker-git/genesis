<?php 
require_once('admin/database.php');
$sql = mysql_query("delete from blood_gluco where id ='".$_GET['id']."'");
if($sql)
	echo "1";
else
	echo  "0";
?>