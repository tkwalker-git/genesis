<?php include_once('admin/database.php'); 

if($_POST) {

 $centerid=$_POST['centerid']; 
 $abc=explode('_',$centerid);
 $val1=$abc[0];
 $val2=$abc[1];
 
  $get_dts = mysql_query("update patient_comments set status='".$val2."' where id='".$val1."'");
}
?>

