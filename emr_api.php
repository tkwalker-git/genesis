<?php

require_once 'nusoap/lib-old/nusoap.php';

$client	= new nusoap_client("https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL", true);

$SOAPAction="loginRequest";

$aParametres = array("userName" => "kunaltest",
                      "password" => "123456?",
					  "DeviceID" => "1"
                     );
                    
$request=$client->call('login', $aParametres, $SOAPAction);

if ($client->fault) {
	echo '<h2>Fault</h2><pre>';
	print_r($result);
	echo '</pre>';
} else {
	// Check for errors
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
	} else {
		// Display the result
		echo '<h2>Result</h2><pre>';
		print_r($result);
		echo '</pre>';
	}
}
echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';

print_r($request);

?>