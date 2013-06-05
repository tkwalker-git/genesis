<?php include_once('admin/database.php'); 

if($_POST) {
$centerid=$_POST['centerid'];
 $res = mysql_query("select * from `supplement` where `id`='". $centerid ."'");
 if ( $ro = mysql_num_rows( $res )){  
  $ro= mysql_fetch_array($res); 
  $instructions_s = strip_tags($ro['comment']);
  echo $ro['suppliers']."|".$ro['cost']."|".$ro['without_food']."|".$ro['dosage']."|".$ro['breakfast']."|".$ro['snack1']."|".$ro['lunch']."|".$ro['snack2']."|".$ro['dinner']."|".$ro['before_bed']."|".$instructions_s."|".$ro['retail_price'];
 }
}

?>

