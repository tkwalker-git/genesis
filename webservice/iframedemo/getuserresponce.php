<?php
session_start();
function login()
{
	
	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/AdminWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
	);
	
	$check =  $client->Authenticate(array("userName"=>'admin',"passwordOrHash"=>'Meghal123'))->AuthenticateResult;
	if($check==1)
	{
		$tmp = $client->_cookies;
		$_SESSION['NoviSurveySessionCookie'] = $tmp['NoviSurveySessionCookie'][0];
		//session_write_close();
	}
	else
	{
		unset($_SESSION['NoviSurveySessionCookie']);
		//session_write_close();
	}
} 

function getrespondent()
{
	//$sid = echo "<script language='javascript'> QuseryStringValue('sid'); </script>";
	$sid = $_GET['sid'];
	//print $sid."SID";
	//exit;
	login();
	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/SurveyWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
	);
 
	//print $client;
	$client->__setCookie("NoviSurveySessionCookie",$_SESSION['NoviSurveySessionCookie']);
	$tmp = $client->GetSurveyResponsesByIds(array("surveyResponseIds" => array($sid),"separator" => "Comma","headerSeparator" => "|","condenseValues" => True,"encodedHeaderFormat" => True,"tagForOptionsSelected" => "1","tagForOptionsNotSelected" => "0","tagForNas" => "NA","includePersonData" => True,"includeParameterData"=>True,"includeScoreData"=>True,"includeResponseData"=>True,"includePartialPageData"=>True));
 
	print "<pre>\n";
	print "Request: \n".htmlspecialchars($client->__getLastRequest()) ."\n";
	print "Response: \n".htmlspecialchars($client->__getLastResponse())."\n";
	print "</pre>";

	session_destroy();
	exit;
	 /*
		if($tmp->GetSurveyRespondentsResult->Success==1)
		{
			return $tmp->GetSurveyRespondentsResult->Data->NoviPerson;
		}
		else
		{
			return null;
		}
	  */
  
}
/*if(isset($_POST['username']) && isset($_POST['password']))
{
	login($_POST['username'],$_POST['password']);
}
if(isset($_POST['id']))
{
	$data = getrespondent($_POST); 
	if($data !=null)
	{
		session_destroy();
		exit;
	}
	
}*/

?>

<form action="" Method="POST" name="getrespondent">
	<?php 
		getrespondent();
	?> 
</form>



