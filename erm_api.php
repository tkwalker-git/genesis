<?php
 
/*require_once('lib/nusoap.php');
 
$url = "https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL";
 
$client = new nusoap_client($url);
 
$err = $client->getError();
 
if ($err) {
    echo '<p><b>Error: ' . $err . '</b></p>';
}
 
$args = array("userName" => "kunaltest",
                      "password" => "123456?",
       "DeviceID" => "1"
                     );
 
$return = $client->call('pollServer', array($args));
 
echo "<p>Value returned from the server is: " . $return . "</p>";*/
 
 
?>

<?php
/*// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the client instance
$client = new soapclient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL');
// Check for an error
$err = $client->getError();
if ($err) {
	// Display the error
	echo '<p><b>Constructor error: ' . $err . '</b></p>';
	// At this point, you know the call that follows will fail
}
// Call the SOAP method
 $result = array("userName" => "kunaltest",
                      "password" => "123456?",
       "DeviceID" => "1"
                     );
//$result = $client->call('hello', array('name' => 'Scott'));
// Check for a fault
if ($client->fault) {
	echo '<p><b>Fault: ';
	print_r($result);
	echo '</b></p>';
} else {
	// Check for errors
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<p><b>Error: ' . $err . '</b></p>';
	} else {
		// Display the result
		//print_r($result);
	}
}
// Display the request and response
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
// Display the debug messages
echo '<h2>Debug</h2>';
echo '<pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';*/
?>



<?php
/*// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the client instance
$client = new soapclient('https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL', true);
// Check for an error
$err = $client->getError();
if ($err) {
    // Display the error
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    // At this point, you know the call that follows will fail
}
// Create the proxy
$proxy = $client->getProxy();
// Call the SOAP method
$person = array("userName" => "kunaltest",
                      "password" => "123456?",
      				  "DeviceID" => "1"
                     );
//$person = array('firstname' => 'Willi', 'age' => 22, 'gender' => 'male');
$result = $proxy->hello($person);
// Check for a fault
if ($proxy->fault) {
    echo '<h2>Fault</h2><pre>';
    print_r($result);
    echo '</pre>';
} else {
    // Check for errors
    $err = $proxy->getError();
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
// Display the request and response
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($proxy->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($proxy->response, ENT_QUOTES) . '</pre>';
// Display the debug messages
echo '<h2>Debug</h2>';
echo '<pre>' . htmlspecialchars($proxy->debug_str, ENT_QUOTES) . '</pre>';*/
?>

<?php 

require_once('lib/nusoap.php');


	
	$request = array('Request' => array(
					'userName' => 'kunaltest',
                    'password' => '123456?',
      				'DeviceID' => '1',					
					'Requests' => array(
							'SourceRequest' => array(
									'Source' => 'Web',
									'Offset' => 0,
									'Count' => 50,
									'ResultFields' => 'All'))));

	$soapClient = new soapclient("https://ehmclinicals.com/WebApplication6/PangeaWS?WSDL", false);
	$result = $soapClient->call("Search", $request);
	print_r($result);
?>