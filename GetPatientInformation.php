<?php
// **************** INFORMATION HEADER ****************
/*
Test: 
http://50.63.174.147/GamiGen/Pangea/GetPatientInformation.php?UserID=1&DoctorsUsername=kunaltest&Hash=Nada

Test Debug: 
http://50.63.174.147/GamiGen/Pangea/GetPatientInformation.php?UserID=1&DoctorsUsername=kunaltest&Hash=Nada&Debug=true

!!! Expected Parameters !!!
GetPatientInformation - Get Sections, Risks and Top 5 for a particular patient
UserID (integer, the patient ID in our database)
Hash (security hash of doctor's userid and password)

!!! Test Queries !!!

<-- This is the SECTION query -->
select 
msections.SectionName
, findings.DamagePercentage 
from findings 
inner join msections 
on findings.SectionID = msections.ID 
inner Join disease_category 
on msections.SystemID = disease_category.ID 
where 
QuestionnaireID = 
(
	select ID 
	from questionnaires 
	where UserID = 1 
	and QuestionnaireDate = 
	(
		Select MAX(QuestionnaireDate) 
		from questionnaires 
		where UserID = 1
	)
)
and disease_category.Cat_Name = 'Systems' 
order by DamagePercentage desc
LIMIT 5;


<-- This is the RISKS query -->
select 
msections.SectionName
, findings.DamagePercentage 
from findings 
inner join msections 
on findings.SectionID = msections.ID 
inner Join disease_category 
on msections.SystemID = disease_category.ID 
where 
QuestionnaireID = 
(
	select ID 
	from questionnaires 
	where UserID = 1 
	and QuestionnaireDate = 
	(
		Select MAX(QuestionnaireDate) 
		from questionnaires 
		where UserID = 1
	)
)
and categories.CategoryName <> 'Systems' 
order by DamagePercentage desc
LIMIT 5;


<-- This is the Top5 query -->
select * from Users_Profiles where UserId = 1;

*/
// ****************************************************


// **************** GET INCOMING PARAMETERS ****************
if( isset($_GET["Debug"]))
{
	$debug = $_GET["Debug"];
}

if( !isset($_GET["PatientID"]))
{
	header('HTTP/1.0 400 Missing Required Parameter (PatientID)');
	echo("HTTP/1.0 400 Missing Required Parameter (PatientID)");
	exit();
}
else
{
	$patientID = $_GET["PatientID"];
	if($debug) echo "PatientID = " . $patientID . PHP_EOL;
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

// Be sure to set the header for text!
header('Content-Type: text/soap+xml');

// **************** PARSE / MASSAGE INCOMING DATA ****************



// **************** DATABASE INIT ****************
include 'dbConnSettings.php';
if($debug) echo("Connected Successfully to MySQL" . PHP_EOL);


// **************** SECTIONS QUERY ****************
$sql = "SELECT 
mdet.Key
, mdet.Value
FROM mnoviforms_response_detail mdet
WHERE mdet.response_id = 
(
SELECT response_id
FROM mnoviforms_response m
	INNER JOIN patients p
		ON m.patient_id = p.id
WHERE p.ID = " . $patientID . "
ORDER BY m.date_completed desc
LIMIT 1
)
AND mdet.Key LIKE '%(%)%';";
$sectionsResult = mysql_query($sql);
if($debug) echo("SQL Statement #1: " . $sql . PHP_EOL);


// **************** RISKS QUERY ****************
$sql = "SELECT 
mdet.Key
, mdet.Value
FROM mnoviforms_response_detail mdet
WHERE mdet.response_id = 
(
SELECT response_id
FROM mnoviforms_response m
	INNER JOIN patients p
		ON m.patient_id = p.id
WHERE p.ID = " . $patientID . "
ORDER BY m.date_completed desc
LIMIT 1
)
AND mdet.Key NOT LIKE '%System%'
AND mdet.Key LIKE '%(%)';
";
$risksResult = mysql_query($sql);
if($debug) echo("SQL Statement #2: " . $sql . PHP_EOL);


// **************** TOP5 QUERY ****************
$sql = "select * from patients where PatientID = " . $patientID;
//$usersProfile = mysql_query($sql);
if($debug) echo("SQL Statement #3: " . $sql . PHP_EOL);

// **************** QUERY RESULTS ****************

/*
<Top5>
<Category CategoryName ="Sections">
<row SectionName="Hepatic - Gallbladder" DamagePercentage="39.58" />
<row SectionName="Cardiovascular System" DamagePercentage="35.55" />
<row SectionName="Nervous System" DamagePercentage="33.33" />
<row SectionName="Adrenal" DamagePercentage="31.94" />
<row SectionName="Gastrointestinal System" DamagePercentage="31.37" />
</Category>
<Category CategoryName ="Risks">
<row SectionName="Medication Use" DamagePercentage="77.77" />
<row SectionName="Heavy Metal Exposure" DamagePercentage="58.62" />
<row SectionName="Biotoxicity Exposure" DamagePercentage="51.28" />
<row SectionName="Supplement Use" DamagePercentage="50.00" />
<row SectionName="Sleep" DamagePercentage="50.00" />
</Category>
<Users_Profiles ID="45" UserID="B0096C50-BE6C-45CE-A9DA-A0CB89C5A445" ClinicID="18" LastName="flintstone" FirstName="fred" DOB="1973-01-17T00:00:00" Sex="Male" Address="123 bedrock ave" City="orlando" State="FL" Zip="12359" Phone="123-456-7890" CreatedBy="System" />
</Top5>
*/

$output = "<Top5>";

// SECTIONS Output
$count = mysql_num_rows($sectionsResult);
if($debug) echo("Total Sections: " . $count . PHP_EOL);
$output .= "<Category CategoryName =\"Sections\">";
if ($count > 0)
{
	// Be sure to set the header for text!
	//header('Content-Type: text/soap+xml');
	
	for($i=0; $i < $count; $i++)
	{
		// echo the results in the proper format
		$sectionName=mysql_result($sectionsResult,$i,"Key");
		$damagePercentage=mysql_result($sectionsResult,$i,"Value");
		$output .= "<row SectionName=\"$sectionName\" DamagePercentage=\"$damagePercentage\" />";
	}
}
$output .= "</Category>";

// RISKS Output
$count = mysql_num_rows($risksResult);
if($debug) echo("Total Sections: " . $count . PHP_EOL);
$output .= "<Category CategoryName =\"Risks\">";
if ($count > 0)
{
	// Be sure to set the header for text!
	//header('Content-Type: text/soap+xml');
	
	for($i=0; $i < $count; $i++)
	{
		// echo the results in the proper format
		$sectionName=mysql_result($risksResult,$i,"Key");
		$damagePercentage=mysql_result($risksResult,$i,"Value");
		$output .= "<row SectionName=\"$sectionName\" DamagePercentage=\"$damagePercentage\" />";
	}
}
$output .= "</Category>";

// USER PROFILE Output
/* Query is not ready, therefore we will send false info shown at the end of this block comment
$count = mysql_num_rows($usersProfile);
if($debug) echo("Total Sections: " . $count . PHP_EOL);
$output .= "<Category CategoryName =\"Risks\">";
if ($count > 0)
{
	// Be sure to set the header for text!
	header('Content-Type: text/soap+xml');
	
	// Get each column, store into a variable
	$sectionName=mysql_result($sectionsResult,$i,"SectionName");
	
	output .= "<Users_Profiles ID=\"45\" UserID=\"B0096C50-BE6C-45CE-A9DA-A0CB89C5A445\" ClinicID=\"18\" LastName=\"flintstone\" FirstName=\"fred\" DOB=\"1973-01-17T00:00:00\" Sex=\"Male\" Address=\"123 bedrock ave\" City=\"orlando\" State=\"FL\" Zip=\"12359\" Phone=\"123-456-7890\" CreatedBy=\"System\" />";
}
*/

$output .= "<Users_Profiles ID=\"45\" UserID=\"B0096C50-BE6C-45CE-A9DA-A0CB89C5A445\" ClinicID=\"18\" LastName=\"flintstone\" FirstName=\"fred\" DOB=\"1973-01-17T00:00:00\" Sex=\"Male\" Address=\"123 bedrock ave\" City=\"orlando\" State=\"FL\" Zip=\"12359\" Phone=\"123-456-7890\" CreatedBy=\"System\" />";

$output .= "</Top5>";
echo $output;
?>