<?php
include_once('admin/database.php');
include_once('site_functions.php');
$logout_type = $_SESSION['usertype'];
$_SESSION['logedin'] = false;
$_SESSION['LOGGEDIN_MEMBER_ID'] = false;
$_SESSION['LOGGEDIN_MEMBER_TYPE'] = false;
$_SESSION['usertype'] = false;



session_destroy();



//if($logout_type=='patient'){
header("Location:index.php");
//exit;
//}
/*else {
header("Location:login.php");
exit;
}*/



?>