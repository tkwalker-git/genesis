<?php 

/*************************************  login function   **********************************************/

function login(){
$aParametres = array("userName" => "kunaltest",
                      "password" => "123456?",
					  "DeviceID" => "1"
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->login( array("login"=> $aParametres));
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
/*************************************  add patient function   **********************************************/

function addpatient($fname,$lname,$sex,$dob,$address,$city,$state,$zip,$phone,$email,$username,$password){	
$aParametres = array("FirstName" => "$fname",
                      "LastName" => "$lname",
					  "Sex" => "$sex",
					  "DOB" => "$dob",
					  "AddressLine1" => "$address",
					  "City" => "$city",
					  "State" => "$state",
					  "Zip" => "$zip",
					  "Phone" => "$phone",
					  "Email" => "$email",
					  "UserName" => "$username",
					  "Password" => "$password"
                     );
					 
					 print_r($aParametres);
					 exit();
					 

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->AddPatient( array("parameter"=> $aParametres));
			//var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        }
} 

/*************************************  addappointmentrequest  function   **********************************************/

function addappointmentrequest(){	

$aParametres = array("sessionId" => "",
						"patientID" => "",
						"requestTime" => "",
						"DoctorId" => "",
						"reason" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->AddAppointmentRequest(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}

/*************************************  addclinicdoctor  function   **********************************************/
function addclinicdoctor(){	
$aParametres = array("ClinicID"  =>  "",        
						"ClinicName"  =>  "bluecom",
						"ClinicAddress1"  =>  "kohinooor",
						"ClinicAddress2"  =>  "fsd",
						"ClinicCity"  =>  "faisalabad",
						"ClinicState"  =>  "Dallas",
						"ClinicZip"  =>  "75120",
						"ClinicPhone"  =>  "123-456-789",
						"ClinicFAX1"  =>  "",
						"ClinicFAX2"  =>  "",
						"ClinicWebsite"  =>  "",
						"DoctorsFirstName"  =>  "muhammad",
						"DoctorsLastName"  =>  "ilyas",
						"DoctorsEmail"  =>  "ilyasbasheer@gmail.com",
						"DoctorsSex"  =>  "Male",
						"DoctorsDOB"  =>  "1980-06-22",
						"DoctorsUserName"  =>  "ilyasbasheer",
						"DoctorsPassword"  =>  "123456"
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->AddClinicDoctor( array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
/*************************************  allergies  function   **********************************************/
function allergies(){	
	
$aParametres = array("sessionID" => "",
						"locatioonGUID" => "",
						"patientMRN" => "",
						"visitID" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->allergies(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}

/*************************************  allLabs  function   **********************************************/
function alllabs(){	

$aParametres = array("sessionID" => "",
						"locationGUID" => "",
						"patientMRN" => "",
						"visitID" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->allLabs(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}

/*************************************  addappointmentrequest  function   **********************************************/
function getallencounters(){		
			

$aParametres = array("sessionID" => "",
						"patientMRN" => "",
						"maxDate" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->getAllEncounters(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}

/*************************************  GetAppointmentCount  function   **********************************************/

function getappointmentcount(){			
			


$aParametres = array("sessionID" => "",
						"fromDate" => "",
						"toDate" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->GetAppointmentCount(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}

/*************************************  getappointmentrequest  function   **********************************************/
function getappointmentrequest(){	

$aParametres = array("sessionId" => "",
            			"patientID" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->GetAppointmentRequest(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
/*************************************  getEncounter  function   **********************************************/

function getencounter(){	

$aParametres = array("sessionID" => "123-456-789",
           			 "encounterID" => "12"
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->getEncounter( array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 		
}
/*************************************  getEncounterDetail function   **********************************************/

function getencounterdetail(){	

$aParametres = array("sessionID" => "",
            			"encounterID" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->getEncounterDetail(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}


/************************************* getPatientInfo function**********************************************/

function getpatientinfo(){	

$aParametres = array("sessionID" => "",
           			 "patientMRN" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->getPatientInfo(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        }
} 

/************************************* getschedule function**********************************************/

function getschedule(){		

$aParametres = array("sessionID" => "",
					"fromDate" => "",
					"toDate" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->getSchedule(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        
/************************************* lab function**********************************************/

function lab(){	
$aParametres = array("sessionID" => "",
						"labGUID" => "",
						"patientMRN" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->Lab(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
/************************************* medication function**********************************************/
function medication(){	

$aParametres = array("sessionID" => "",
						"locationGUID" => "",
						"patientMRN" => "",
						"visitID" => "",
						"update" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->medication(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
/************************************* saveencounter function**********************************************/
saveencounterfunction saveencounter(){					

$aParametres = array("sessionID" => "",
						"encounterID" => "",
						"encounterData" => "cid:325620957753"
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->saveEncounter(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
/************************************* updateclinic function**********************************************/

function updateclinic(){	

$aParametres = array("ClinicID" => "",
					"ClinicName" => "",
					"ClinicAddress1" => "",            
					"ClinicAddress2" => "",
					"ClinicCity" => "",
					"ClinicState" => "",
					"ClinicZip" => "",            
					"ClinicPhone" => "",            
					"ClinicFAX1" => "",            
					"ClinicFAX2" => "",            
					"ClinicWebsite" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->UpdateClinic(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}

/************************************* updatedoctor function**********************************************/
function updatedoctor(){	
	

$aParametres = array("ClinicID" => "",
					"DoctorID" => "",
					"DoctorsFirstName" => "",
					"DoctorsLastName" => "",
					"DoctorsEmail" => "",            
					"DoctorsSex" => "",            
					"DoctorsDOB" => "",
					"DoctorsUserName" => "",
					"DoctorsPassword" => ""
                     );

try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->UpdateDoctor(array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
/************************************* updatepatient function**********************************************/


function updatepatient(){	

$aParametres = array("ClinicID" => "",
						"PatientID" => "",
						"FirstName" => "",
						"LastName" => "",
						"Sex" => "",
						"DOB" => "",
						"AddressLine1" => "",            
						"AddressLine2" => "",
						"City" => "",
						"State" => "",
						"Zip" => "",
						"Phone" => "",
						"Email" => "",
						"UserName" => "",
						"Password" => ""
                     );

		try {
            $options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
			
            $client = new SoapClient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', $options);
			//var_dump($client->__getFunctions());

            $results = $client->UpdatePatient(array("parameter"=> $aParametres));
			//var_dump($client);
			var_dump($results);			
       		 } 
		catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
		
}
?>