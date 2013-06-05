<?php 
error_reporting(E_ALL);
require_once('admin/database.php');
require_once('site_functions.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='dashboard.php';</script>";
$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){
$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}else {
$member_full_name = attribValue('patients', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}








function login($user,$pass,$memberid)
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
		$data = $client->GetLoginValidationToken();
		$token = $data->GetLoginValidationTokenResult;
		$token = str_replace(" ","+",$token);
		$tmpurl="https://pangeaifa.com/NoviSurvey/n/pangeaquestionnaire.aspx?PangiaId=".$memberid;
		$tmpurl=urlencode($tmpurl);
		$url = "https://pangeaifa.com/NoviSurvey/Login.aspx?ui=admin&vt=".$token."&ReturnUrl=".$tmpurl;
				
	}
	else
	{
		$url="";
	}
	
	return $url;
	
}

$data = login("admin","Meghal123",$member_id);
if($data!="")
{
	//echo $data;
	echo '<script> window.location="'.$data.'"</script>';
	
}
else
{
	echo '			<script>		alert("Please Reload Page");	</script>';

}


?>
