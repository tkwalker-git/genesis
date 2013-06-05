<?
require_once('admin/database.php');
require_once('site_functions.php');

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}
//d(base64_decode($_REQUEST['referer']),1);
if ($_REQUEST['signed_request']) {
  $response = parse_signed_request($_REQUEST['signed_request'],FACEBOOK_SECRET2);
  	//d($response,1);
	if ( isset($response['registration'])) {
		$name 			= DBin($response['registration']['name']);
		$email 			= DBin($response['registration']['email']);
		$comments 		= DBin($response['registration']['comments']);
	   
		$event_id 		= DBin($response['registration']['event_id']);

		$event_check    = "select  from event_rsvp where email = '$email' AND event_id='$event_id'";
	
		$event_exist = mysql_query($event_check);
		if(!$event_exi = mysql_fetch_assoc($event_exist)){
           $sql  = "INSERT INTO event_rsvp (event_id,name, email, comments,created_date)
					VALUES ('$event_id', '$name', '$email','$comments','".date("Y-m-d H:i:s")."')";
            mysql_query($sql);
        }else{
           $sql  = "UPDATE event_rsvp SET comments='$comments'";
            mysql_query($sql);
        }
        
		
	}
}

header("Location: ".base64_decode($_REQUEST['referer']));exit;
?>