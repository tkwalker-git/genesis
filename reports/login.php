<?php
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");


include("include/dbcommon.php");


add_nocache_headers();

if($wr_is_standalone)
{
	$arr_table=db_gettablelist();
	$first_install=true;
	foreach($arr_table as $ind=>$table)
	{
		if(strpos(strtoupper($table),strtoupper("webreport_settings"))!==false)
		{
			$first_install=false;
			break;
		}
	}
	if($first_install)
		header("Location: install.php");
}

$auditObj = GetAuditObject();

if(@$_POST["a"]=="logout" || @$_GET["a"]=="logout")
{
	if($auditObj)
		$auditObj->LogLogout();

	session_unset();
	setcookie("username","",time()-365*1440*60);
	setcookie("password","",time()-365*1440*60);
	header("Location: login.php");
	exit();
}

include('include/xtempl.php');
include('classes/runnerpage.php');
$xt = new Xtempl();

$id = postvalue("id") != "" ? postvalue("id") : 1;

//array of params for classes
$params = array("id" =>$id);
$params['xt'] = &$xt;
$pageObject = new RunnerPage($params);

// proccess captcha
$isCaptchaOk=1;
$useCaptcha = false;


// end proccess captcha

// button handlers file names
$buttonHandlers = array();
$pageObject->addButtonHandlers($buttonHandlers);

// add onload event




//	Before Process event
if(function_exists("BeforeProcessLogin"))
	BeforeProcessLogin($conn);


$myurl=@$_SESSION["MyURL"];
unset($_SESSION["MyURL"]);



$message="";

$pUsername=postvalue("username");
$pPassword=postvalue("password");

$is508=isEnableSection508();

$rememberbox_checked="";
$rememberbox_attrs = ($is508==true ? "id=\"remember_password\" " : "")."name=\"remember_password\" value=\"1\"";
if(@$_COOKIE["username"] || @$_COOKIE["password"])
	$rememberbox_checked=" checked";

$logacc=true;

	if($auditObj)
	{
		if($auditObj->LoginAccess())
		{
			$logacc=false;
			$message=mysprintf("Access denied for %s minutes",array($auditObj->LoginAccess()));
		}
	}

if (@$_POST["btnSubmit"] == "Login" && $logacc)
{
	if(@$_POST["remember_password"] == 1)
	{
		setcookie("username",$pUsername,time()+365*1440*60);
		setcookie("password",$pPassword,time()+365*1440*60);
		$rememberbox_checked=" checked";
	}
	else
	{
		setcookie("username","",time()-365*1440*60);
		setcookie("password","",time()-365*1440*60);
		$rememberbox_checked="";
	}
//   	 username and password are stored in the database
	$strUsername = (string)$pUsername;
	$strPassword = (string)$pPassword;
	$strPassword=md5($strPassword);
	$sUsername=$strUsername;
	$sPassword=$strPassword;
		
	if(NeedQuotes($cUserNameFieldType))
		$strUsername="'".db_addslashes($strUsername)."'";
	else
		$strUsername=(0+$strUsername);
	if(NeedQuotes($cPasswordFieldType))
		$strPassword="'".db_addslashes($strPassword)."'";
	else
		$strPassword=(0+$strPassword);
		$strSQL = "select * from ".AddTableWrappers("webreport_users")." where ".AddFieldWrappers($cUserNameField).
		"=".$strUsername." and ".AddFieldWrappers($cPasswordField).
		"=".$strPassword;
	$retval=true;
   	$logged=false;
	$data=array();
	if(function_exists("BeforeLogin"))
		$retval=BeforeLogin($pUsername,$pPassword,$message);
	if($retval)
	{
		$rs=db_query($strSQL,$conn);
	 	$data=db_fetch_array($rs);
		if($data)
			if(@$data[$cUserNameField]==$sUsername && @$data[$cPasswordField]==$sPassword)
				$logged=true;
	}
	
	if($useCaptcha && $isCaptchaOk)
		$_SESSION["login_count_captcha"] = $_SESSION["login_count_captcha"]+1;
	
	if($logged && $isCaptchaOk)
	{
		$_SESSION["UserID"] = $pUsername;
   		$_SESSION["AccessLevel"] = ACCESS_LEVEL_USER;

		$_SESSION["GroupID"] = $data["username"];
		



					$_SESSION["OwnerID"] = $data["id"];
	$_SESSION["_webreport_users_OwnerID"] = $data["id"];
		if($auditObj)
		{
			$auditObj->LogLogin();
			$auditObj->LoginSuccessful();
		}

		if(function_exists("AfterSuccessfulLogin"))
			AfterSuccessfulLogin($pUsername,$pPassword,$data);
if($wr_is_standalone)
{
	$rs=db_query("select count(id) from webreport_admin",$conn);
	$data=db_fetch_numarray($rs);
	if($data[0]==0)
	{
		header("Location: webreport_admin.php");
		return;
	}
}
		if($myurl)
			header("Location: ".$myurl);
		else
			header("Location: menu.php");
		return;
   	}
	else
	{
			if($auditObj)
			{
				$auditObj->LogLoginFailed($pUsername);
				$auditObj->LoginUnsuccessful($pUsername);
			}

			if(function_exists("AfterUnsuccessfulLogin"))
				AfterUnsuccessfulLogin($pUsername,$pPassword,$message);
			if($message=="")
				$message = "Invalid Login";
	}
}

$xt->assign("rememberbox_attrs",$rememberbox_attrs.$rememberbox_checked);


	$xt->assign("guestlink_block",true);

	
$_SESSION["MyURL"]=$myurl;
if($myurl)
	$xt->assign("guestlink_attrs","href=\"".$myurl."\"");
else
	$xt->assign("guestlink_attrs","href=\"menu.php\"");
	
if(postvalue("username"))
	$xt->assign("username_attrs",($is508==true ? "id=\"username\" " : "")."value=\"".htmlspecialchars($pUsername)."\"");
else
	$xt->assign("username_attrs",($is508==true ? "id=\"username\" " : "")."value=\"".htmlspecialchars(refine(@$_COOKIE["username"]))."\"");


$password_attrs="onkeydown=\"e=event; if(!e) e = window.event; if (e.keyCode != 13) return; e.cancel = true; e.cancelBubble=true; document.forms[0].submit(); return false;\"";
if(postvalue("password"))
	$password_attrs.=($is508==true ? " id=\"password\"": "")." value=\"".htmlspecialchars($pPassword)."\"";
else
	$password_attrs.=($is508==true ? " id=\"password\"": "")." value=\"".htmlspecialchars(refine(@$_COOKIE["password"]))."\"";
$xt->assign("password_attrs",$password_attrs);

if(@$_GET["message"]=="expired")
	$message = "Your session has expired. Please login again.";


if($message)
{
	$xt->assign("message_block",true);
	$xt->assign("message",$message);
}


$pageObject->body["begin"] .= "<script type=\"text/javascript\" src=\"include/jquery.js\"></script>".
"<script type=\"text/javascript\" src=\"include/jsfunctions.js\"></script>";

if ($pageObject->debugJSMode === true)
{

	$pageObject->body["begin"] .= "<script type=\"text/javascript\" src=\"include/runnerJS/Runner.js\"></script>".
		"<script type=\"text/javascript\" src=\"include/runnerJS/Util.js\"></script>";
}
else
{
	$pageObject->body["begin"] .= "<script type=\"text/javascript\" src=\"include/runnerJS/RunnerBase.js\"></script>";
}
$pageObject->body["begin"] .= "<form method=post action=\"login.php\" id=form1 name=form1>
		<input type=hidden name=btnSubmit value=\"Login\">";
$pageObject->body["end"] .= "</form>
<script>
function elementVisible(jselement)
{ 
	do
	{
		if (jselement.style.display.toUpperCase() == 'NONE')
			return false;
		jselement=jselement.parentNode; 
	}
	while (jselement.tagName.toUpperCase() != 'BODY'); 
	return true;
}
if(elementVisible(document.forms[0].elements['username']))
	document.forms[0].elements['username'].focus();
</script>";






$pageObject->body["end"] .= "<script>".$pageObject->PrepareJS()."</script>";
$xt->assignbyref("body",$pageObject->body);


$xt->assign("username_label",true);
$xt->assign("password_label",true);
$xt->assign("remember_password_label",true);
if(isEnableSection508())
{
	$xt->assign_section("username_label","<label for=\"username\">","</label>");
	$xt->assign_section("password_label","<label for=\"password\">","</label>");
	$xt->assign_section("remember_password_label","<label for=\"remember_password\">","</label>");
}

$templatefile="login.htm";
if(function_exists("BeforeShowLogin"))
	BeforeShowLogin($xt,$templatefile);

$xt->display($templatefile);
?>