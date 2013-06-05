<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1); 
require_once('admin/database.php');
require_once('site_functions.php');

if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0) {
	echo "<script>window.location.href='user_login.php';</script>";
}

if ( trim($_GET['NoviFormName']) != ''   ) 
	$noviFormName = $_GET['NoviFormName'];

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){
$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}else {
$member_full_name = attribValue('patients', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}
$meta_title	= 'Survey';
include_once('includes/header.php');


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
  
   mysql_query("DELETE FROM `mnoviforms_response_detail` WHERE `response_id`='". $post ."'");
   
  for($ci=0;$ci<count($data0);$ci++)
  {
	$sql = "INSERT INTO `mnoviforms_response_detail` (`Id`, `response_id`, `Key`, `Value`) VALUES (NULL, '".$post."', '".$data0[$ci]."', '".$data1[$ci]."')";
	mysql_query($sql);
  }
  //$sql = "INSERT INTO `mnoviforms_response_detail` (`Id`, `Responce Id`, `Key`, `Value`) VALUES (NULL, '".$post."', '".$data[0]."', '".$data[1]."')";
  //mysql_query($sql);
}

$sql = "select * from mnoviforms where `NoviFormName`='". $noviFormName ."'";
$res = mysql_query($sql);
$form_id = null;
if($res)
{
	if(mysql_num_rows($res)>0)
	{
		$row= mysql_fetch_array($res);
		$form_id = $row['ID'];
		$FormName = $row['FormName'];
	}
}
?>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">

<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="clr"></div>
    <div class="gredBox">
      <?php include('dashboard_menu_tk.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:7px;">
			<div style="padding-bottom:50px; overflow:hidden;">
				<?php
				if(isset($_GET['sid']) && $_GET['sid'] !="")
				{
					$sql = "SELECT * FROM `mnoviforms_response` WHERE `PangiaId`='".$member_id."' and `mnoviforms_id` = '".$form_id."'";
					$res = mysql_query($sql);
					if($res)
					{
						if(mysql_num_rows($res)>0)
						{
							$sql = "UPDATE `mnoviforms_response` SET `response_id`='".$_GET['sid']."',`date_completed`='".date("Y-m-d H:i:s")."' WHERE `PangiaId` =".$member_id." AND `mnoviforms_id` =".$form_id;
							$res = mysql_query($sql);
						}
						else
						{
							$sql = "INSERT INTO `mnoviforms_response` (`ID` ,`mnoviforms_id`,`PangiaId` ,`patient_id` ,`response_id` ,`date_started` ,`date_completed`) VALUES (NULL , '".$form_id."', '".$member_id."', '".$member_id."', '".$_GET['sid']."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."');";
							$res = mysql_query($sql);
						}
						
						
							login_ws("admin","Meghal123");
							getrespondent_ws($_GET['sid']);
?>
Please Wait ...
<script type='text/javascript'>
window.location.href = 'https://restorationhealth.yourhealthsupport.com/dashboard.php';
</script>
<?php					
					}
					else
					{
						echo 'System error, Please reload page';
					}
				}
				else
				{
					$sql = "select * from mnoviforms_response where PangiaId=".$member_id." and `mnoviforms_id`=".$form_id;
					$res = mysql_query($sql);
					$show_flg=true;
					if($res)
					{
						if(mysql_num_rows($res)>0)
						{
							$row= mysql_fetch_array($res);
							if($row['response_id']!='')
							$show_flg = true;
						}
						else
						{
							$sql = "INSERT INTO `mnoviforms_response` (`ID` ,`mnoviforms_id`,`PangiaId` ,`patient_id` ,`response_id` ,`date_started` ,`date_completed`) VALUES (NULL , '".$form_id."', '".$member_id."', '".$member_id."', NULL , '".date("Y-m-d H:i:s")."', NULL);";
							$res = mysql_query($sql);
							
						}
						
					}
					if($show_flg)
					{
				?>
						<iframe src="https://pangeaifa.com/NoviSurvey/n/<?php echo $noviFormName; ?>.aspx?PangiaId=<?php echo $member_id; ?>&FormName=<?php echo $noviFormName; ?>" frameborder=0 width="900px" height="10530px" scrolling = "auto" ></iframe>
							
				
				<?php
					}
					else
					{
						echo 'You have already completed this survey.  Please visit your Dashboard to take a new survey';
						
						
					}
				}
				?>
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>