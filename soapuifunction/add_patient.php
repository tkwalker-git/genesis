<?php
include_once('admin/xmlparser.php');
//$client = new SoapClient("https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL", array('soap_version'   => SOAP_1_2));
//5c4b6484-bcb0-4e1b-a8e0-2c60a607c333
function addpatient(){	

			


$aParametres = array("ClinicID" => "pangeafinal2",
						"FirstName" => "Ali",
						"LastName" => "Usman",
						"Sex" => "Male",
						"DOB" => "2000-01-01",
						"AddressLine1" => "Test Address"					
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

            $results = $client->AddPatient( array("parameter"=> $aParametres));
			var_dump($client);
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        }
} 
?>