<?php 
require_once('admin/database.php');
$sql = mysql_query("delete from pt_inr where id ='".$_GET['id']."'");
if($sql)
	echo "1";
else
	echo  "0";
?>