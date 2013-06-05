<?
	@session_start();
	
	function authorize_process($customer){
		
		$exp_date    = $customer['card_expiry_month'].$customer['card_expiry_year'];

		$auth_net_tran_key 	=  attribValue("merchant_settings", "secret_key", "where id='1' limit 1");
		$auth_net_login_id 	=  attribValue("merchant_settings", "api_login", "where id='1' limit 1");
		$mode				=  attribValue("merchant_settings", "mode", "where id='1' limit 1");
		
		if ($mode == 'Test')
			$auth_net_url	= "https://test.authorize.net/gateway/transact.dll"; // test mode
		else	
			$auth_net_url	= "https://secure.authorize.net/gateway/transact.dll"; // live mode
		
		/*
		5Gfz58Ws2C7G3rB7
		57CGFCW8bkDs
		$auth_net_login_id			= "6zz6m5N4Et"; 
		$auth_net_tran_key			= "9V9wUv6Yd92t27t5";
		$auth_net_url				= "https://test.authorize.net/gateway/transact.dll"; // test mode
		$auth_net_url			  	= "https://secure.authorize.net/gateway/transact.dll"; // live mode
		*/
		
		$authnet_values	= array(
			"x_login" => $auth_net_login_id,
			"x_version" => "3.1",
			"x_delim_char" => "|",
			"x_delim_data" => "TRUE",
			"x_url" => "FALSE",
			"x_type" => "AUTH_CAPTURE",
			"x_method" => "CC",
	 		"x_tran_key" => $auth_net_tran_key,
	 		"x_relay_response" => "FALSE",
			"x_card_num" => $customer['card_number'],
			"x_card_code" => $customer['card_code'],
			"x_exp_date" => $exp_date,
	//		"x_description" => "UnitedDatabase.net",
			"x_description" => "EventGrabber.com",
			"x_amount" => $customer['totalAmount'],
			//"x_amount" => number_format(100, 2),
			"x_first_name" => $customer['first_name'],
			"x_last_name" => $customer['last_name'],
			"x_address" => $customer['address'],
			"x_city" => $customer['city'],
			"x_state" => $customer['state'],
			"x_zip" => $customer['zip'],
			"x_country" => $customer['country'],
			"x_Ship_To_First_Name" => $customer['first_name'],
			"x_Ship_To_Last_Name" => $customer['last_name'],
			"x_Ship_To_Address" => $customer['address'],
			"x_Ship_To_City" => $customer['city'],
			"x_Ship_To_State" => $customer['state'],
			"x_Ship_To_Zip" => $customer['zip'],
			"x_Ship_To_Country" => $customer['country'],
		);
			
		$fields = "";
		foreach($authnet_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
		
		$ch = curl_init($auth_net_url); 
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); // use HTTP POST to send form data
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // uncomment this line if you get no gateway response. ###
		$resp = curl_exec($ch); //execute post and get results
		curl_close ($ch);
			
	return $resp;
}	

	function authorize_isProcessed($resp){
		
		$response = array();

		$tok = strtok($resp,"|");
		while(!($tok === FALSE)){
		   $response[] = $tok;
		   $tok = strtok("|");
		}
		
		if ($response[0] == 1) {
		    // $response[0] == 2 -> Declined
			// $response[0] == 3 -> Error
			// $response[3] == 2 -> Reason Text
			// $response[38] == A,B,N,P,S ... -> Card Code Response  [info available at the following url]
			// http://www.authorize.net/support/Merchant/Integration_Settings/Standard_Transaction_Security_Settings.htm
			
			return array(true, $response);
		}
		
		return array(false, $response);
	} 
?>
