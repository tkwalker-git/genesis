<?php


function addclinicdoctor($clinicid,$clinicname,$clinicaddress1,$clinicaddress2,$city,$state,$zip,$phone,$fax1,$fax1,$clinicweb,$doctorfirstname,$doctorlastname,$doctoremail,$doctorsex,$doctordob,$doctorusername,$doctorpass){	
$aParametres = array("ClinicID"  =>  $clinicid,        
						"ClinicName"  =>  $clinicname,
						"ClinicAddress1"  =>  $clinicaddress1,
						"ClinicAddress2"  =>  $clinicaddress2,
						"ClinicCity"  =>  $city,
						"ClinicState"  =>  $state,
						"ClinicZip"  =>  $zip,
						"ClinicPhone"  =>  $phone,
						"ClinicFAX1"  =>  $fax1,
						"ClinicFAX2"  =>  $fax2,
						"ClinicWebsite"  =>  $clinicweb,
						"DoctorsFirstName"  =>  $doctorfirstname,
						"DoctorsLastName"  =>  $doctorlastname,
						"DoctorsEmail"  =>  $doctoremail,
						"DoctorsSex"  =>  $doctorsex,
						"DoctorsDOB"  =>  $doctordob,
						"DoctorsUserName"  =>  $doctorusername,
						"DoctorsPassword"  =>  $doctorpass
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
			//var_dump($client);
			//var_dump($results);
			
			echo $reference = $results->return->DoctorID;
			
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
}
addclinicdoctor('pangeafinal2','testa','','','','','','','','','','muhammad','ilyasa','aliaas@gmail.com','Male','1980-06-06','alaias','1233456');
?>