<?php include_once('../admin/database.php'); 

$value=$_REQUEST['value'];
$sql=mysql_query("select * from clinic where id='$value'");
while($gclinic=mysql_fetch_array($sq)){
$cname=$gclinic['ClinicName'];
$cadd1=$gclinic['Address1'];
$cadd2=$gclinic['Address2'];
$ccity=$gclinic['City'];
$cstate=$gclinic['State'];
$czip=$gclinic['Zip'];
$cphone1=$gclinic['Phone1'];
$cphone2=$gclinic['Phone2'];
$cfax1=$gclinic['Fax1'];
$cfax2=$gclinic['Fax2'];
$cweb=$gclinic['Website'];
}

?>