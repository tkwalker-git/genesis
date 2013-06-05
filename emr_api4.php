<?php
//include_once("nusoap/lib-old/nusoap.php");
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
?>