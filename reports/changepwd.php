<?php
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");


include("include/dbcommon.php");

if(!@$_SESSION["UserID"] || @$_SESSION["UserID"]=="<Guest>")
{ 
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: login.php?message=expired"); 
	return;
}

$message="";
$go=1;

include('include/xtempl.php');
include('classes/runnerpage.php');
$xt = new Xtempl();

$id = postvalue("id") != "" ? postvalue("id") : 1;
//array of params for classes
$params = array("pageType" => PAGE_EDIT, "id" =>$id);
$params['xt'] = &$xt;
$pageObject = new RunnerPage($params);

// add onload event

$auditObj = GetAuditObject("webreport_users");

//	Before Process event
if(function_exists("BeforeProcessChangePwd"))
	BeforeProcessChangePwd($conn);

if (@$_POST["btnSubmit"] == "Submit")
{	
	$go = postvalue("go")+1;
	$xt->assign("backlink_attrs","href=\"javascript:history.go(-".$go.")\"");
	$opass = postvalue("opass");
	$newpass = postvalue("newpass");
	$newpassraw=$newpass;
	$opass = md5($opass);
	$newpass = md5($newpass);
	
	$value = @$_SESSION["UserID"];
	if(NeedQuotes($cUserNameFieldType))
		$value="'".db_addslashes($value)."'";
	else
		$value=(0+$value);
	$passvalue = $newpass;
	if(NeedQuotes($cPasswordFieldType))
		$passvalue="'".db_addslashes($passvalue)."'";
	else
		$passvalue=(0+$passvalue);


    	$sWhere = " where ".AddFieldWrappers($cUserNameField)."=".$value;
		$strSQL = "select * from ".AddTableWrappers($cLoginTable).$sWhere;
		$rstemp=db_query($strSQL,$conn);

		if($row=db_fetch_array($rstemp))
		{
			if($opass == $row[$cPasswordField])
			{
				$retval=true;
				if(function_exists("BeforeChangePassword"))
					$retval=BeforeChangePassword(postvalue("opass"), postvalue("newpass"));
				if($retval)
				{
					$strSQL= "update ".AddTableWrappers($cLoginTable)." set ".AddFieldWrappers($cPasswordField)."=".$passvalue.$sWhere;
					db_exec($strSQL,$conn);
					if($auditObj)
						$auditObj->ChPassword();
					if(function_exists("AfterChangePassword"))
						AfterChangePassword(postvalue("opass"), postvalue("newpass"));
					$xt->assign("body",true);
					$xt->display("changepwd_success.htm");
					return;
				}
			}
			else
				$message = "Invalid password";
	}
}
else $xt->assign("backlink_attrs","href=\"javascript:history.go(-1)\"");
	
if($message)
{
	$xt->assign("message",$message);
	$xt->assign("message_block",true);
}


$includes="";
$includes.="<script language=\"JavaScript\" src=\"include/jquery.js\"></script>\r\n";
$includes.="<script language=\"JavaScript\" src=\"include/jsfunctions.js\"></script>\r\n";
if ($pageObject->debugJSMode === true)
{
	$includes.="<script language=\"JavaScript\" src=\"include/runnerJS/Runner.js\"></script>\r\n";
	$includes.="<script language=\"JavaScript\" src=\"include/runnerJS/RunnerEvent.js\"></script>\r\n";
	$includes.= "<script type=\"text/javascript\" src=\"include/runnerJS/Util.js\"></script>";	
}
else
{
	$includes.="<script language=\"JavaScript\" src=\"include/runnerJS/RunnerBase.js\"></script>\r\n";
}	


$pageObject->body["begin"] .= $includes."<script>".$pageObject->PrepareJS()."</script>"."<script language=\"JavaScript\">
function validate()
{

	
	if (document.forms.form1.cpass.value!=document.forms.form1.newpass.value)
	{	
		alert('".jsreplace("Passwords do not match. Re-enter password").
		"');
		document.forms.form1.newpass.value='';
		document.forms.form1.cpass.value='';
		document.forms.form1.newpass.focus();
		return false;
	}
	return true;
}
</script>
 <form method=\"POST\" action=\"changepwd.php\" id=form1 name=form1 onsubmit=\"return validate();\">
<input type=hidden name=btnSubmit value=\"Submit\">
<input type=hidden name=go value=\"".$go."\">";
$pageObject->body["end"] .="</form>";
$xt->assignbyref("body",$pageObject->body);

$templatefile="changepwd.htm";
if(function_exists("BeforeShowChangePwd"))
	BeforeShowChangePwd($xt,$templatefile);

$xt->display($templatefile);
?>