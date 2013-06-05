<?php 
function CreateSubscription($fname,$lname,$email) 
{

	$data = '<?xml version="1.0" encoding="UTF-8"?>
        <subscription>
          <product_id>1085863</product_id>
          <customer_attributes>
            <first_name>'. $fname .'</first_name>
            <last_name>'. $lname .'</last_name>
            <email>'. $email .'</email>
          </customer_attributes>
          <credit_card_attributes>
            <full_number>1</full_number>
            <expiration_month>10</expiration_month>
            <expiration_year>2020</expiration_year>
          </credit_card_attributes>
        </subscription>';
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://big-chillin.chargify.com/subscriptions.xml");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/xml',
		'Accept: application/xml'
	));         	

	curl_setopt($ch, CURLOPT_USERPWD, 'CxqmiGCkB8SQAAk77t-Y:x' );

	$method = 'POST';
	
	if($method == 'POST')
	{
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	else if ($method == 'PUT')
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	else if($method != 'GET')
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	}

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);

	$result = new StdClass();
	$result->response = curl_exec($ch);
	$result->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$result->meta = curl_getinfo($ch);
	
	if ( $result->code > 0 ) {
		
		$responseXML 	= $result->response;
		$xmlparser		= new xmlparser();
		$data			= $xmlparser->GetXMLTree($responseXML);
		$resp			= $data['SUBSCRIPTION'][0]['CUSTOMER'][0]['ID'][0]['VALUE'];
		
	} else {
		$resp = -1;
	}

	curl_close($ch);
	
	
	
	return $resp;
}	

echo CreateSubscription("ali","asad","ilyasbash@hotmail.com");

?>