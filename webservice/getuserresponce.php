<?php
session_start();
function login($user,$pass)
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
		
			}
	else
	{
		unset($_SESSION['NoviSurveySessionCookie']);
		session_write_close();
		
	}
	
} 
function getrespondent($post)
{
	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/SurveyWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
  );
 
  
  $client->__setCookie("NoviSurveySessionCookie",$_SESSION['NoviSurveySessionCookie']);
  $tmp = $client->GetSurveyResponsesByIds(array("surveyResponseIds" => array($post['id']),"separator" => "Comma","headerSeparator" => "|","condenseValues" => True,"encodedHeaderFormat" => True,"tagForOptionsSelected" => "1","tagForOptionsNotSelected" => "0","tagForNas" => "NA","includePersonData" => True,"includeParameterData"=>True,"includeScoreData"=>True,"includeResponseData"=>True,"includePartialPageData"=>True));
 /*
 print "<pre>\n";
print "Request: \n".htmlspecialchars($client->__getLastRequest()) ."\n";
print "Response: \n".htmlspecialchars($client->__getLastResponse())."\n";
print "</pre>";
*/
//print_r($tmp);


 session_destroy();
 //exit;
 
 echo '<pre>';
	if($tmp->GetSurveyResponsesByIdsResult->Success==1)
	{
		
		$resstr=$tmp->GetSurveyResponsesByIdsResult->Data->string;
		echo $resstr;
		echo "\n";
		$data = explode("\n",$resstr);
		print_r($data);
	}
	else
	{
		//return null;
	}
  exit;
  
}
if(isset($_POST['username']) && isset($_POST['password']))
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
	
}
?>

<?php 
if(isset($_SESSION['NoviSurveySessionCookie']) && $_SESSION['NoviSurveySessionCookie'] !="")
{
?>
Get Responce By Id <br/>
<Form action="" method="POST" name="getresponce">
	<table>
		<tr>
			<td>Responce Id : </td>
			<td><input type="text" name="id" value="1"/></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit"/></td>
		</tr>
	</table>
</form>
<?php
}
else
{
?>
Login Wih Webservice User (admin) <br/>
<Form action="" method="POST" name="loginfrm">
	<table>
		<tr>
			<td>User Name : </td>
			<td><input type="text" name="username" value="admin"/></td>
		</tr>
		
		<tr>
			<td>Password : </td>
			<td><input type="password" name="password" value="Meghal123"/></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit"/></td>
		</tr>
	</table>
</form>
<?php
}
?>
