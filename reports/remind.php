<?php
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");


include("include/dbcommon.php");
include('classes/runnerpage.php');


$cEmailField = "email";
$reminded=false;
$strSearchBy="username";

include('include/xtempl.php');
$xt = new Xtempl();

$id = postvalue("id")!=="" ? postvalue("id") : 1;

$strTableName = 'remind';

//array of params for classes
$params = array("id" =>$id);
$params['xt'] = &$xt;
$pageObject = new RunnerPage($params);

// button handlers file names
$buttonHandlers = array();
$pageObject->addButtonHandlers($buttonHandlers);


// add onload event
	
	
$isUseCaptcha = false;
$isCaptchaOk=1;



$strUsername="";
$strEmail="";
$strMessage="";






if (@$_POST["btnSubmit"] == "Remind")
{
	//	Before Process event
	if(function_exists("BeforeProcessRemind"))
		BeforeProcessRemind($conn);
	
    $strSearchBy = postvalue("searchby");
	$strUsername = postvalue("username");
	$strEmail = postvalue("email");
		
	if(!$isUseCaptcha || ($isUseCaptcha && $isCaptchaOk==1))
	{	
		$tosearch=false;
		if($strSearchBy!="email")
		{
			$value=$strUsername;
			if((string)$value!="")
				$tosearch=true;
			if(NeedQuotes($cUserNameFieldType))
				$value="'".db_addslashes($value)."'";
			else
				$value=(0+$value);
			$sWhere=AddFieldWrappers($cUserNameField)."=".$value;
		}
		else
		{
			$value=$strEmail;
			if((string)$value!="")
				$tosearch=true;
			if(NeedQuotes($cEmailFieldType))
				$value="'".db_addslashes($value)."'";
			else
				$value=(0+$value);
			$sWhere=AddFieldWrappers($cEmailField)."=".$value;
		}
		
		if($tosearch && function_exists("BeforeRemindPassword"))
			$tosearch = BeforeRemindPassword($strUsername,$strEmail);
		
		if($tosearch)
		{
			$strSQL="select ".AddFieldWrappers($cUserNameField).",".AddFieldWrappers($cPasswordField).",".AddFieldWrappers($cEmailField)." from ".AddTableWrappers("webreport_users")." where ".$sWhere;
			$rs=db_query($strSQL,$conn);
			if($data=db_fetch_numarray($rs))
			{
				$password=$data[1];
		//	generate 6 chars length password
				$password="";
				for($i=0;$i<10;$i++)
				{
					$j=rand(0,35);
					if($j<26)
						$password.=chr(ord('a')+$j);
					else
						$password.=chr(ord('0')-26+$j);
				}
				db_exec("update ".AddTableWrappers("webreport_users")." set ".AddFieldWrappers($cPasswordField)."='".md5($password)."' where ".$sWhere,$conn);
				$url = GetSiteUrl();
				$url.=$_SERVER["SCRIPT_NAME"];
				$message="Password reminder"."\r\n";
				$message.="You asked to remind your username and password at"." ".$url."\r\n";
				$message.="Username".": ".$data[0]."\r\n";
				$message.="Password".": ".$password."\r\n";
				runner_mail(array('to' => $data[2], 'subject' => "Password reminder", 'body' => $message));
				$reminded=true;
				if(function_exists("AfterRemindPassword"))
					AfterRemindPassword($strUsername,$strEmail);
				$loginlink_attrs="href=\"login.php";
				if($strSearchBy!="email")
					$loginlink_attrs.="?username=".rawurlencode($strUsername);
				$loginlink_attrs.="\"";
				$xt->assign("loginlink_attrs",$loginlink_attrs);
				$xt->assign("body",true);
				$_SESSION[$strTableName."_count_captcha"]=$_SESSION[$strTableName."_count_captcha"]+1;
				$xt->display("remind_success.htm");
				return;
			}
		}
		
		if(!$reminded)
		{
			if($strSearchBy!="email")
				$strMessage="User"." <i>".$strUsername."</i> "."is not registered.";
			else
				$strMessage="This email doesn't exist in our database";
		}

	}

}
$emailradio_attrs="onclick=\"document.forms.form1.searchby.value='email'; UpdateControls();\"";
$usernameradio_attrs="onclick=\"document.forms.form1.searchby.value='username'; UpdateControls();\"";
if($strSearchBy=="username")
{
	$usernameradio_attrs.=" checked";
	$search_disabled = "email";
}
else
{
	$emailradio_attrs.=" checked";
	$search_disabled = "username";
}
$xt->assign("emailradio_attrs",$emailradio_attrs);
$xt->assign("usernameradio_attrs",$usernameradio_attrs);

$xt->assign("username_label",true);
$xt->assign("email_label",true);
$is508=isEnableSection508();
if($is508)
{
	$xt->assign_section("username_label","<label for=\"username\">","</label>");
	$xt->assign_section("email_label","<label for=\"email\">","</label>");
}
$xt->assign("username_attrs",($is508==true ? "id=\"username\" " : "")."value=\"".htmlspecialchars($strUsername)."\"");
$xt->assign("email_attrs",($is508==true ? "id=\"email\" " : "")."value=\"".htmlspecialchars($strEmail)."\"");

if(@$strMessage)
{
 	$xt->assign("message",@$strMessage);
	$xt->assign("message_block",true);
	if($isCaptchaOk==1) 
		$_SESSION[$strTableName."_count_captcha"]=$_SESSION[$strTableName."_count_captcha"]+1;
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


$pageObject->body["begin"] .="<script language = JavaScript>
function OnKeyDown()
{
	e = window.event;
	if (e.keyCode == 13)
	{
		e.cancel = true;
		document.forms[0].submit();
	}	
}

function UpdateControls()
{
	if (document.forms.form1.searchby.value==\"username\")
	{
		document.forms.form1.username.style.backgroundColor='white';
		document.forms.form1.email.style.backgroundColor='gainsboro';
		document.forms.form1.username.disabled=false; 
		document.forms.form1.email.disabled=true;
	}
	else
	{
		document.forms.form1.username.style.backgroundColor='gainsboro';
		document.forms.form1.email.style.backgroundColor='white';
		document.forms.form1.username.disabled=true; 
		document.forms.form1.email.disabled=false;
	}
}
</script>
<form method=post action=\"remind.php\" id=form1 name=form1>
<input type=hidden name=btnSubmit value=\"Remind\">
<input type=\"Hidden\" name=\"searchby\" value=\"".$strSearchBy."\">";
$pageObject->body["end"] .= "</form>
	<script language=\"JavaScript\">
	document.forms.form1.".$search_disabled.".disabled=true;
	UpdateControls();
	".$pageObject->PrepareJS()."
	</script>";



$xt->assignbyref("body",$pageObject->body);



$templatefile="remind.htm";
if(function_exists("BeforeShowRemindPwd"))
	BeforeShowRemindPwd($xt,$templatefile);

$xt->display($templatefile);
