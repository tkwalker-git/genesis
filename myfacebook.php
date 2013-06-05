<?php 
//echo "+++++++";
require_once('admin/database.php');
//include_once(CLASSES."user_class.php");
include_once('facebook.php');
//$_SESSION['fblogedin']='';
if(isset($_SESSION['usertype']) && $_SESSION['usertype'] != ''){
		$usertp = $_SESSION['usertype'];
//		echo "usertype".$usertp;
}else{
	$usertp = '1';
}


// original
//define('FACEBOOK_APP_ID','147946741892206');
//define('FACEBOOK_SECRET','35d0ba775757c080586fddd6cdeb580a');

//qs-dev
//define('FACEBOOK_APP_ID','146656285391173');
//define('FACEBOOK_SECRET','05390a822d46fe05ecdc22e7e3f30cf3');
//end qs-dev

//define('FACEBOOK_APP_ID', '152591101427568');
//define('FACEBOOK_SECRET', '6df6bc05ce0e80458b345ccdc3487225');

function get_facebook_cookie($app_id, $application_secret) {
  $args = array();
 parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  ksort($args);
  $payload = '';
  foreach ($args as $key => $value) {
    if ($key != 'sig') {
      $payload .= $key . '=' . $value;
    }
  }
  if (md5($payload . $application_secret) != $args['sig']) {
    return null;
  }
  return $args;
}

$cookie = get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET);
$url="https://graph.facebook.com/me?access_token=" .$cookie['access_token']."";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $xml_resp =curl_exec($ch);
    curl_close($ch);
    $userfacebook = json_decode($xml_resp);
	
	//print_r($_COOKIE);
	
//	echo "<pre>";
//	print_r( $userfacebook);
//	echo "</pre>";
//	die;
	if($userfacebook->id!='')
	{
		if($userfacebook->gender=='male')
		{
			$Gender='m';
		}
		else
		{
			$Gender='f';
		}
	//$obj = new USER();

	 $password = "";				
				$possible = "0123456789bcdfghjkmnpqrstvwxyz";// define possible characters				
				$i = 0; // set up a counter
				// add random characters to $password until $length is reached
				while ($i < 6) { 

					// pick a random character from the possible ones
					$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
	
					// we don't want this character if it's already in the password
					if (!strstr($password, $char)) { 
					$password .= $char;
					$i++;
				}
			
	  }			
	
	$sqlLogin = "select * from users where email='".$userfacebook->email."'  and facebookid='".$userfacebook->id."'";
	$reslogin = mysql_query($sqlLogin);
	$totalRows = mysql_num_rows($reslogin);
	$alreadyLogin = mysql_fetch_array($reslogin);
	if($totalRows=='0')
		{
		
		$usrnm = strtolower(str_replace(" ","_",trim($userfacebook->first_name)));
		
	$sql="insert into  users SET firstname='".$userfacebook->name."',email='".$userfacebook->email."',username='".$usrnm."',password='".$password."',varifaction_code='',email_verify='1',enabled='1',usertype='$usertp',createddate=now(),facebookid='".$userfacebook->id."'";
	 $reslogin= mysql_query($sql);


 $sqlId="select * from users order by id desc limit 0,1";
 $resid= mysql_query($sqlId);
 $rowAutid = mysql_fetch_array($resid);
 $autoId=$rowAutid['id'];
if($reslogin!='')
			{
				$_SESSION['LOGGEDIN_MEMBER_ID']=$autoId;
				$_SESSION['FID']=$userfacebook->id;
				$_SESSION['MEMBERNAME']=$rowLogin['username'];
				$_SESSION['EMAIL']=$rowLogin['email'];
				$_SESSION['LOGGEDIN_MEMBER_TYPE']=$rowLogin['usertype'];
				$_SESSION['fblogedin']='1';

				//$arrUser = array();
				$fname = $userfacebook->first_name;
				$email = $userfacebook->email;
				//$arrUser['email'] ='neeraj_pandey@seologistics.com';
				$usename = $userfacebook->first_name;
				$password = $password;
				//$obj->facebookpassword($arrUser);
				 //$redirect_url = $site_url."myevent.php";
	             //header("Location: ".$redirect_url."");
					/*echo '<script>window.location="index.php";</script>';*/
				
			}

		
		}
		else
		{
			    $_SESSION['LOGGEDIN_MEMBER_ID']=$alreadyLogin['id'];
				$_SESSION['FID']=$alreadyLogin['facebookid'];
				$_SESSION['MEMBERNAME']=$alreadyLogin['username'];
				$_SESSION['EMAIL']=$alreadyLogin['email'];
				$_SESSION['LOGGEDIN_MEMBER_TYPE']=$alreadyLogin['usertype'];
				$_SESSION['fblogedin']='1';
				//$redirect_url = $site_url."myevent.php";
	            //header("Location: ".$redirect_url."");
				/*echo '<script>window.location="index.php";</script>';*/

		}
		
	}
	
	
	
$facebook = new Facebook(array(
  'appId'  => FACEBOOK_APP_ID,
  'secret' => FACEBOOK_SECRET,
  'cookie' => true, // enable optional cookie support
));

if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// login or logout url will be needed depending on current user state.
if ($me) {
 $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}



?>
	<div id="fb-root"></div>
	
	<script src="http://connect.facebook.net/en_US/all.js"></script>

	<?php if(isset($_SESSION['fblogedin']) && $_SESSION['fblogedin']==1){ ?>
			
			<script>
				FB.init({appId: '<?= FACEBOOK_APP_ID ?>', status: true, cookie: true, xfbml: true });
				FB.Event.subscribe('auth.login', function(response) { login(); });
				FB.Event.subscribe('auth.logout', function(response) { logout(); });
				function logout(){ document.location.href = "<?php echo ABSOLUTE_PATH;?>logout.php"; }
    			function login(){ document.location.href = "<?php echo ABSOLUTE_PATH;?>category/live-entertainment.html"; }
		   </script>   
		   <fb:login-button autologoutlink="true"  width="100" background="white" length="short" label="Logout" perms="email,user_birthday">
			Facebook Logout
		   </fb:login-button>
	<?php }?>	   


     
  <!-- <div id="fb-root"></div>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
      FB.init({appId: '<?= FACEBOOK_APP_ID ?>', status: true,
               cookie: true, xfbml: true});
      FB.Event.subscribe('auth.login', function(response) {
	   login();
        //window.location.reload();
      });
	  FB.Event.subscribe('auth.logout', function(response) {

                    // do something with response

                                                            logout();

                    //window.location.reload();

               });

			function logout(){

			document.location.href = "<?php //echo ABSOLUTE_PATH;?>logout.php";

			}
			function login(){

				document.location.href = "<?php //echo ABSOLUTE_PATH;?>category/live-entertainment.html";

			}
      
</script> --> 

   
      <!-- <a href="<?php echo $loginUrl; ?>">
        <img src="http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif">
      </a> -->
	  <?php if(isset($_SESSION['fblogedin']) && $_SESSION['fblogedin']==1){ ?>
	 
	  <!--	<fb:login-button autologoutlink="true"  width="100" background="white" length="short" label="Logout" perms="email,user_birthday"><span>Facebook</span><span>&nbsp;</span><span>Logout</span><span class="fbr" style="margin-top: -12px !important;"></span></fb:login-button> -->
	  <?php }else { ?>
	<!-- <fb:login-button autologoutlink="true"  width="100" background="white" length="short" label="Logout" perms="email,user_birthday"><span>Connect</span><span style="font-weight:normal !important"> With </span><span>Facebook</span><span class="fbr"></span></fb:login-button> -->
		 
		<?php } ?>