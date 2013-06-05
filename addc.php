<?php 

function CreateSubscription($fname,$lname,$email,$product_id,$card_number,$exp_month,$exp_year) 
{

	$data = '<?xml version="1.0" encoding="UTF-8"?>
        <subscription>
          <product_id>'.$product_id.'</product_id>
          <customer_attributes>
            <first_name>'. $fname .'</first_name>
            <last_name>'. $lname .'</last_name>
            <email>'. $email .'</email>
          </customer_attributes>
          <credit_card_attributes>
            <full_number>'.$card_number.'</full_number>
            <expiration_month>'.$exp_month.'</expiration_month>
            <expiration_year>'.$exp_year.'</expiration_year>
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
		print_r($data);
		$resp			= $data['SUBSCRIPTION'][0]['CUSTOMER'][0]['ID'][0]['VALUE'];
		
	} else {
		$resp = -1;
	}

	curl_close($ch);
	
	
	
	return $resp;
}	




 $bc_doctor_first_name='muhammad';
 $bc_doctor_last_name='basheer';
 $bc_doctor_email='ilyasbasheer@gmail.com';
 $bc_clinic_product=1085863;
echo $bc_card_number=1;
 $bc_month=10;
 $bc_year=2014;

echo $genSysCustomerID = CreateSubscription($bc_doctor_first_name,$bc_doctor_last_name,$bc_doctor_email,$bc_clinic_product,$bc_card_number,$bc_month,$bc_year);

?>