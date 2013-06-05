<?php include_once('admin/database.php'); 

if($_POST) {
 $centerid=$_POST['centerid'];
 $sql = mysql_query("select * from clinic where id='$centerid' ");
 if ( $roo = mysql_num_rows( $sql )){  
  //$suburb=mysql_query("select suburb from suburbs where postcode='".$ro['postcode']."'");
  $ro= mysql_fetch_array($sql); 
  echo $ro['clinicname']."|".$ro['address1']."|".$ro['address2']."|".$ro['city']."|".$ro['state']."|".$ro['zip']."|".$ro['phone1']."|".$ro['phone2']."|".$ro['fax1']."|".$ro['fax2']."|".$ro['website'];
 }
}

?>

