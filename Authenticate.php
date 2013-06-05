<?php
// **************** INFORMATION HEADER ****************
/*
Test: 
http://50.63.174.147/GamiGen/Pangea/Authenticate.php?DoctorsUsername=Bill&Hash=1234

Test Debug: 
http://50.63.174.147/GamiGen/Pangea/Authenticate.php?DoctorsUsername=Bill&Hash=1234&Debug=true

!!! Expected Parameters !!!
DoctorsUsername (The Doctor's username for the EventGrabber site)
Hash (security hash of doctor's userid and password) Test Queries

!!! Test Queries !!!
select email from users where username = 'bill' and password = '1234';

*/
// ****************************************************


// **************** GET INCOMING PARAMETERS ****************
if( isset($_GET["Debug"]))
{
	echo "Debug set";
	$debug = $_GET["Debug"];
}

if( !isset($_GET["ClinicID"]))
{
	header('HTTP/1.0 400 Missing Required Parameter (ClinicID)');
	echo("HTTP/1.0 400 Missing Required Parameter (ClinicID)");
	exit();
}
else
{
	$clinicid = $_GET["ClinicID"];
	if($debug) echo "ClinicID = " . $clinicid . PHP_EOL;
}

if( !isset($_GET["DoctorsUsername"]))
{
	header('HTTP/1.0 400 Missing Required Parameter (DoctorsUsername)');
	echo("HTTP/1.0 400 Missing Required Parameter (DoctorsUsername)");
	exit();
}
else
{
	$doctorsUsername = $_GET["DoctorsUsername"];
	if($debug) echo "DoctorsUsername = " . $doctorsUsername . PHP_EOL;
}

if( !isset($_GET["Hash"]))
{
	header('HTTP/1.0 400 Missing Required Parameter (Hash)');
	echo("HTTP/1.0 400 Missing Required Parameter (Hash)");
	exit();
}
else
{
	$hash = $_GET["Hash"];
	if($debug) echo "Hash = " . $hash . PHP_EOL;
}

// **************** PARSE / MASSAGE INCOMING DATA ****************


// **************** DATABASE INIT ****************
include 'dbConnSettings.php';
if($debug) echo("Connected Successfully to MySQL" . PHP_EOL);


// **************** QUERY ****************
$sql = "select email, id from users where ( email = '" . $doctorsUsername . "' or username = '" . $doctorsUsername . "' ) and password = '" . $hash . "' and clinicid = '" . $clinicid . "';";
if($debug) echo("SQL Statement #1: " . $sql . PHP_EOL);
$result = mysql_query($sql);


// **************** QUERY RESULTS ****************
$count = mysql_num_rows($result);
if($debug) echo("Total Matching Users: " . $count . PHP_EOL);
if ($count > 0)
{
	// Be sure to set the header for text!
	header('Content-Type: text/txt');
	
	for($i=0; $i < $count; $i++)
	{
		// echo the results in the proper format
		$email=mysql_result($result,$i,"email");
		$id=mysql_result($result,$i,"id");
		
		// Output this row in proper format
		// XML Output, retired
		$output .= $email . ',' . $id . ';';
	}
	
	echo $output;
}
else
{
	echo "Invalid Login";
}
?>