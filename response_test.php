<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1); 
require_once('admin/database.php');
require_once('site_functions.php');


function csv2array($input,$delimiter=',',$enclosure='"',$escape='\\'){
    $fields=explode($enclosure.$delimiter.$enclosure,substr($input,1,-1));
    foreach ($fields as $key=>$value)
        $fields[$key]=str_replace($escape.$enclosure,$enclosure,$value);
    return($fields);
} 
function login_ws($user,$pass)
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
		
		
	}
	else
	{
		unset($_SESSION['NoviSurveySessionCookie']);
		
		
	}
	
} 

function getrespondent_ws($post)
{


	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/SurveyWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
  );
 
  
  $client->__setCookie("NoviSurveySessionCookie",$_SESSION['NoviSurveySessionCookie']);
  $tmp = $client->GetSurveyResponsesByIds(array("surveyResponseIds" => array($post),"separator" => "Comma","headerSeparator" => "|","condenseValues" => True,"encodedHeaderFormat" => True,"tagForOptionsSelected" => "1","tagForOptionsNotSelected" => "0","tagForNas" => "NA","includePersonData" => True,"includeParameterData"=>True,"includeScoreData"=>True,"includeResponseData"=>True,"includePartialPageData"=>True));

  $resstr=$tmp->GetSurveyResponsesByIdsResult->Data->string;
  $data = explode("\n",$resstr);
  
  $data0 = csv2array($data[0]);
  $data1 = csv2array($data[1]);
  
  print_r($data0);
  echo '<hr>';
  print_r($data1);
  
}

login_ws("admin","Meghal123");
getrespondent_ws('6583c2b8260d4f57b549beee1b002415');

?>