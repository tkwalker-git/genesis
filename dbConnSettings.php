<?php
/* PANGEA PROD SSL SETTINGS */
$srv = "localhost";
$usr = "brett";
$db = "zadmin_pangea";
$pwd = "ysumaqa3u";

/* PANGEA.EventGrabber.com SETTINGS */
/*
$srv = "localhost";
$usr = "eventgra_pangea";
$db = "eventgra_pangea";
$pwd = "4~SVRKd#xaJ!";
*/

/* TEST SETTINGS */
/*
$srv = "localhost";
$usr = "root";
$db = "Pangea";
$pwd = "BCiagg@1";
*/

$con = mysql_connect($srv, $usr, $pwd);
if(!$con)
{
	header('Content-Type: text/txt');
	/*header('HTTP/1.0 400 Database Connection Failed');*/
	echo('HTTP/1.0 400 Database Connection Failed: ' . mysql_error());
	exit();
}
mysql_select_db($db, $con);
?>