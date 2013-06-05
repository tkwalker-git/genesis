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
function getrespondent($id,$sd,$ed)
{
	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/SurveyWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
  );
 
  
  $client->__setCookie("NoviSurveySessionCookie",$_SESSION['NoviSurveySessionCookie']);
  $tmp = $client->GetSurveyRespondents(array("surveyDeploymentId" => $id, "startDate" => $sd,"endDate" => $ed));
 
	if($tmp->GetSurveyRespondentsResult->Success==1)
	{
		return $tmp->GetSurveyRespondentsResult->Data->NoviPerson;
	}
	else
	{
		return null;
	}
  
  
}
if(isset($_POST['username']) && isset($_POST['password']))
{
	login($_POST['username'],$_POST['password']);
}
if(isset($_POST['id']) && isset($_POST['sd']) && isset($_POST['ed']))
{
	$data = getrespondent($_POST['id'],$_POST['sd'],$_POST['ed']); 
	//print_r($data);
	if($data !=null)
	{
		
		echo '<table><tr><td>Id</td><td>FirstName</td><td>LastName</td><td>Email</td></tr>';
		foreach($data as $d)
		echo "<tr><td>".$d->ID."</td><td>".$d->FirstName."</td><td>".$d->LastName."</td><td>".$d->Email."</td></tr>";
		echo '</table>';
		session_destroy();
		exit;
	}
	
}
?>

<?php 
if(isset($_SESSION['NoviSurveySessionCookie']) && $_SESSION['NoviSurveySessionCookie'] !="")
{
?>
<Form action="" method="POST" name="getresponce">
	<table>
		<tr>
			<td>Id : </td>
			<td><input type="text" name="id" value="zzz4q"/></td>
		</tr>
		<tr>
			<td>Start Date : </td>
			<td><input type="text" name="sd" value="2010-10-02T00:00:00"/></td>
		</tr>
		<tr>
			<td>End Date : </td>
			<td><input type="text" name="ed" value="2012-11-20T00:00:00"/></td>
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
