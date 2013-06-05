<?php 
session_start();

function registeruser($posta,$organisationid,$firstname,$lastname,$username,$email,$password)
{
	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/AdminWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
  );
 
  
  $client->__setCookie("NoviSurveySessionCookie",$posta);
  $tmp = $client->CreateUser(array("organizationId" => $organisationid,"firstName" => $firstname,"lastName" => $lastname,"userName" => $username,"email" => $email,"passwordHash" => $password));
 
  if($tmp->CreateUserResult!="")
  {
	return $tmp->CreateUserResult;
  }
  else
  {
	return null;
  }
  
}


function get_novi_id($user,$pass,$organisationid,$firstname,$lastname,$username,$email,$password)
{
	
	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/AdminWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
	);
	
	$check =  $client->Authenticate(array("userName"=>$user,"passwordOrHash"=>$pass))->AuthenticateResult;
	
	if($check==1)
	{
		$tmp = $client->_cookies;
	 	$_SESSION['NoviSurveySessionCookie'] = $tmp['NoviSurveySessionCookie'][0];
		session_write_close();
	   $nuid = registeruser($_SESSION['NoviSurveySessionCookie'],$organisationid,$firstname,$lastname,$username,$email,$password);
		
			}
	else
	{
		unset($_SESSION['NoviSurveySessionCookie']);
		session_write_close();
		
		$nuid	=	"nologin";		
	}
	
	return $nuid;
} 

 echo $resu = get_novi_id('admin','Meghal123',1,'Nirav','Detroja','niravdetroja','niravdetroja@gmail.com','nirav');


?>