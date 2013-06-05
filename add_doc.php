<?php

//$client = new SoapClient("https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL", array('soap_version'   => SOAP_1_2));
//5c4b6484-bcb0-4e1b-a8e0-2c60a607c333

$aParametres = array("ClinicName" => "blucom",
                      "ClinicAddress1" => "kohinoor",
					  "ClinicCity" => "Dallas",
					  "ClinicState" => "TX",
					  "ClinicZip" => "75120",
					  "ClinicPhone" => "123-665-9887",
					  "DoctorsFirstName" => "ilyas",
					  "DoctorsLastName" => "basheer",
					  "DoctorsEmail" => "sales@bluecomp.net",
					  "DoctorsSex" => "sales@bluecomp.net",
					  "DoctorsDOB" => "2000-01-01",
					  "DoctorsUserName" => "ilyasbasheer",
					  "DoctorsPassword" => "123478"
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
			var_dump($results);
        } catch (Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        } 
?>