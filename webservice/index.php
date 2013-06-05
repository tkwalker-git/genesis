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
function registeruser($post)
{
	$client = new SoapClient("https://pangeaifa.com/novisurvey/ws/AdminWebService.asmx?WSDL",
    array(
      "trace"      => 1,		// enable trace to view what is happening
      "exceptions" => 0,		// disable exceptions
      "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
  );
 
  
  $client->__setCookie("NoviSurveySessionCookie",$_SESSION['NoviSurveySessionCookie']);
  $tmp = $client->CreateUser(array("organizationId" => $post['id'],"firstName" => $post['firstName'],"lastName" => $post['lastName'],"userName" => $post['userName'],"email" => $post['email'],"passwordHash" => $post['password']));
 
  if($tmp->CreateUserResult!="")
  {
	return $tmp->CreateUserResult;
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
if(isset($_POST['id']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['userName']) && isset($_POST['email']) && isset($_POST['password']))
{
	$data = registeruser($_POST); 
	if($data !=null)
	{
		echo "User Id : ".$data.'<br/>';
		echo "<a target='_blank' href='https://pangeaifa.com/NoviSurvey/TakeSurvey.aspx?s=zzz4q'>Click Here For Survey</a><br/>";
		echo "<a target='_blank' href='getuserresponce.php'>Click Here To Get User Response</a>";
		session_destroy();
		exit;
	}
	
}
?>

<?php 
if(isset($_SESSION['NoviSurveySessionCookie']) && $_SESSION['NoviSurveySessionCookie'] !="")
{
?>
Create User <br/>
<Form action="" method="POST" >
	<table>
		<tr>
			<td>organizationId : </td>
			<td><input type="text" name="id" value="1"/></td>
		</tr>
		<tr>
			<td>firstName : </td>
			<td><input type="text" name="firstName" value="Dipal"/></td>
		</tr>
		<tr>
			<td>lastName : </td>
			<td><input type="text" name="lastName" value="Parikh"/></td>
		</tr>
		<tr>
			<td>userName : </td>
			<td><input type="text" name="userName" value="niravdetroja"/></td>
		</tr>
		<tr>
			<td>email : </td>
			<td><input type="text" name="email" value="parikhdipal@gmail.com"/></td>
		</tr>
		<tr>
			<td>Password : </td>
			<td><input type="password" name="password" value="nirav"/></td>
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
Login with Webservice User (Admin) <br/>
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
