<?php
// **************** INFORMATION HEADER ****************
/*
Test: 
http://50.63.174.147/GamiGen/Pangea/QueryGroupInformation.php?ClinicID=4&GroupDate=&GroupName=&DoctorsUsername=kunaltest&Hash=123456?

Test Debug: 
http://50.63.174.147/GamiGen/Pangea/QueryGroupInformation.php?ClinicID=4&GroupDate=&GroupName=&DoctorsUsername=kunaltest&Hash=123456?&Debug=true

!!! Expected Parameters !!!
QueryGroupInformation - Get lists of matching groups of patients
ClinicID
GroupDate
GroupName
DoctorsUsername
Hash (encrypted password for the DoctorsUsername

!!! Test Queries !!!

*/
// ****************************************************


// **************** GET INCOMING PARAMETERS ****************
if( isset($_GET["Debug"]))
{
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
	$clinicId = $_GET["ClinicID"];
	if($debug) echo "ClinicID = " . $clinicId . PHP_EOL;
}

if( isset($_GET["GroupDate"]))
{
	$groupDate = $_GET["GroupDate"];
	list($month, $day, $year) = split('/', $groupDate);  
	if($debug) echo "GroupDate = " . $groupDate . PHP_EOL;
}

if( isset($_GET["GroupName"]))
{
	$groupName = $_GET["GroupName"];
	if($debug) echo "GroupName = " . $groupName . PHP_EOL;
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
header('Content-Type: text/soap+xml');
$sql = "select ID,GroupName,GroupDate from groups where ClinicID = " . $clinicId;

if(strlen($groupDate) > 0)
{
	if($debug) echo $groupDate . PHP_EOL;
	$time = mktime(0,0,0,$month,$day,$year);

	try
	{
		$beginDate = date("Y-m-d H:i:s", $time);
		if($debug) echo $beginDate . PHP_EOL;

        $thePHPDate = getdate($time); // Covert to Array    
        $thePHPDate['mday'] = $thePHPDate['mday']+1; // Add to Day   
        $time = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']);
		$endDate = date("Y-m-d H:i:s", $time);
		if($debug) echo $endDate . PHP_EOL;
	
		$sql .= " and (GroupDate >= '" . $beginDate . "' and GroupDate <= '" . $endDate . "') ";
		if($debug) echo $sql . PHP_EOL;
	}
	catch(Exception $e) 
	{
		echo "Exception - " . $e->getMessage() . PHP_EOL;
	}
}

if(strlen($groupName) > 0)
{
	$sql .= " and GroupName like '%" . $groupName . "%' ";
}
if($debug) echo "TSQL = " . $sql . PHP_EOL;

// **************** DATABASE INIT ****************
include 'dbConnSettings.php';
if($debug) echo("Connected Successfully to MySQL" . PHP_EOL);


// **************** QUERY ****************
if($debug) echo("SQL Statement #1: " . $sql . PHP_EOL);
$result = mysql_query($sql);

// **************** QUERY RESULTS ****************
$count = mysql_num_rows($result);
if($debug) echo("Total Matching Users: " . $count . PHP_EOL);

if ($count > 0)
{
	// Be sure to set the header for text!
	header('Content-Type: text/soap+xml');
	
	// Version 2 - Start with the table we're building
	$output = "<Groups>";
	
	for($i=0; $i < $count; $i++)
	{
		// echo the results in the proper format
		$clinicID=mysql_result($result,$i,"ID");
		$groupName=mysql_result($result,$i,"GroupName");
		$groupDate=mysql_result($result,$i,"GroupDate");
		
		// Output this row in proper format
		// XML Output, retired
		$output .= "<row ID=\"$clinicID\" GroupName=\"$groupName\" GroupDate=\"$groupDate\" />";
		
		// TXT Output
		//printf("$UserID,$LastName,$FirstName,$DOB,$Sex,$ClinicName");
	}
	
	// Wrap up all the remaining XML elements
	$output .= "</Groups>";
	
	echo $output;
}
?>