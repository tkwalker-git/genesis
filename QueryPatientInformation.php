<?php
// **************** INFORMATION HEADER ****************
/*
Test: 
http://50.63.174.147/GamiGen/Pangea/QueryPatientInformation.php?ClinicIC=4&PatientName=J&Hash=Nada

Test Debug: 
http://50.63.174.147/GamiGen/Pangea/QueryPatientInformation.php?ClinicIC=4&PatientName=J&Hash=Nada&Debug=true

!!! Expected Parameters !!!
QueryPatientInformation - Get lists of matching patients
ClinicID (integer)
PatientName (last,first format)
Hash (security hash of doctor's userid and password) Test Queries

!!! Test Queries !!!
select id 
from patients 
where FirstName like '%m%'
and ClinicID = 4
union
select id 
from users 
where LastName like '%m%'
and ClinicID = 4;

select 
users.id, LastName, FirstName, DOB, Sex, clinic.clinicname as ClinicName 
from users 
inner join clinic on users.clinicid = clinic.id
where users.id in (11,27,1);
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

if( !isset($_GET["PatientName"]))
{
	header('HTTP/1.0 400 Missing Required Parameter (PatientName)');
	echo("HTTP/1.0 400 Missing Required Parameter (PatientName)");
	exit();
}
else
{
	$patientName = $_GET["PatientName"];
	if($debug) echo "PatientName = " . $patientName . PHP_EOL;
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
// Split words using blank space as delimiter
$splitNames = explode(" ", $patientName);

// Build SQL statement for all of the names
$firstNameSql = " FirstName like ";
$lastNameSql = " LastName like ";
for($i = 0; $i < count($splitNames); $i++)
{
	if($i < count($splitNames)-1)
	{
		$firstNameSql .= "'%" . $splitNames[$i] . "%' OR ";
		$lastNameSql .= "'%" . $splitNames[$i] . "%' OR ";
	}
	else
	{
		$firstNameSql .= "'%" . $splitNames[$i] . "%'";
		$lastNameSql .= "'%" . $splitNames[$i] . "%'";
	}
}


// **************** DATABASE INIT ****************
include 'dbConnSettings.php';
if($debug) echo("Connected Successfully to MySQL" . PHP_EOL);


// **************** QUERY ****************
$sql = "select id from patients where " . $firstNameSql . " and ClinicID = " . $clinicId .
" union select id from patients where " . $lastNameSql . " and ClinicID = " . $clinicId;
if($debug) echo("SQL Statement #1: " . $sql . PHP_EOL);
$prelimResult = mysql_query($sql);

// Change array result to comma delimited string
$count = mysql_num_rows($prelimResult);
if($debug) echo("Total Matching Users: " . $count . PHP_EOL);

$delim = "";
$counter = 0;
if($debug) echo("Start Delimited String" . PHP_EOL);

while($row = mysql_fetch_array($prelimResult, MYSQL_BOTH))
{
	if($counter < $count-1)
	{
		$delim .= $row[0] . ',';
		if($debug) echo("Record = " . $row[0] . PHP_EOL);
	}
	else
	{
		$delim .= $row[0];
		if($debug) echo("Record = " . $row[0] . PHP_EOL);
	}
	$counter++;
}
if($debug) echo("End Delimited String" . PHP_EOL . PHP_EOL);

$sql = "select patients.ID, LastName, FirstName, DOB, Sex, clinic.ClinicName as ClinicName from patients left outer join clinic on patients.ClinicID = clinic.ID where patients.ID in (" . $delim . ")";
if($debug) echo("SQL Statement #2: " . $sql . PHP_EOL);
$result = mysql_query($sql);


// **************** QUERY RESULTS ****************
$count = mysql_num_rows($result);
if($debug) echo("Total Matching Users: " . $count . PHP_EOL);
if ($count > 0)
{
	// Be sure to set the header for text!
	header('Content-Type: text/soap+xml');
	
	/* Version 2 - header elements are retired
	$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	$output .= "<soap:Envelope xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">";
	$output .= "<soap:Body>";
	
	// Elements for this service
	$output .= "<QueryPatientInformationResponse xmlns=\"https://www.PangeaIFA.com/\">";
	$output .= "<QueryPatientInformationResult>";
	*/
	
	// Version 2 - Start with the table we're building
	$output = "<Patients>";
	
	for($i=0; $i < $count; $i++)
	{
		// echo the results in the proper format
		$UserID=mysql_result($result,$i,"ID");
		$LastName=mysql_result($result,$i,"LastName");
		$FirstName=mysql_result($result,$i,"FirstName");
		$DOB=mysql_result($result,$i,"DOB");
		$Sex=mysql_result($result,$i,"Sex");
		$ClinicName=mysql_result($result,$i,"ClinicName");
		
		// Output this row in proper format
		// XML Output, retired
		$output .= "<row UserID=\"$UserID\" LastName=\"$LastName\" FirstName=\"$FirstName\" DOB=\"$DOB\" Sex=\"$Sex\" ClinicName=\"$ClinicName\" />";
		
		// TXT Output
		//printf("$UserID,$LastName,$FirstName,$DOB,$Sex,$ClinicName");
	}
	
	// Wrap up all the remaining XML elements
	$output .= "</Patients>";
	
	/* Version 2 - header elements are retired
	$output .= "</QueryPatientInformationResult>";
	$output .= "</QueryPatientInformationResponse>";
	$output .= "</soap:Body>";
	$output .= "</soap:Envelope>";
	*/
	
	echo $output;
}
?>