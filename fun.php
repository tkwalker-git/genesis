<?php
include_once('admin/database.php'); 
include_once('site_functions.php');








$user = "admin";
$pass = "Meghal123";

$username = "aliass";
$password = "123456";
$email = "faisal@gmail.com";
$organisationid = 1;
$firstname = "ali";
$lastname = "basheer";

$clinicid = "pangeafinal2";
$fname = "ali";
$lname = "basheer";
$sex = "Male";
$dob = "2012-06-16";
$address = "lahore";
$city = "lahore";
$state = "alabama";
$zip = "75120";
$phone = "123-456-78";





echo get_novi_id($user,$pass,$organisationid,$firstname,$lastname,$username,$email,$password);
echo "<br />";
echo addpatient($clinicid,$fname,$lname,$sex,$dob,$address,$city,$state,$zip,$phone,$email,$username,$password);



?>