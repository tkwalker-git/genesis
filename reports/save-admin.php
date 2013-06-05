<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
include("include/reportfunctions.php");

if(postvalue("name")=="password")
{
	if(postvalue("password")==$WRAdminPagePassword)
	{
		$_SESSION["WRAdmin"]=true;
		echo "OK";
	}
	else
	{
		unset($_SESSION["WRAdmin"]);
		echo "ERROR";
	}
	exit();
}

if(!isWRAdmin())
{
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: webreport.php?message=expired");
	return;
}

if(postvalue("name")=="deletesql")
{
	if(postvalue("idsql"))
		db_exec("delete from ".AddTableWrappers("webreport_sql")."  where ".AddFieldWrappers("id")."=".db_addslashes(postvalue("idsql")),$conn);
	echo "OK";
	exit();
}

if(postvalue("name")=="sqledit")
{
	$sqlcontent=postvalue("sqlcontent");
	$_SESSION["object_sql"]=$sqlcontent;
	$rs=db_query_safe($sqlcontent,$conn,$errstr);
	if(!$rs)
	{
		echo $errstr;
		exit();
	}
	if($_SESSION["idSQL"])
	{
		db_exec("update ".AddTableWrappers("webreport_sql")." set ".AddFieldWrappers("sqlname")."='".db_addslashes(postvalue("namesql"))."',".AddFieldWrappers("sqlcontent")."='".db_addslashes($sqlcontent)."' where ".AddFieldWrappers("id")."=".db_addslashes($_SESSION["idSQL"]),$conn);
		db_exec("update webreport_admin set tablename='".db_addslashes(postvalue("namesql"))."' where tablename='".db_addslashes($_SESSION["nameSQL"])."'",$conn);
		$_SESSION["nameSQL"]=postvalue("namesql");
	}
	else
	{
		$sname=postvalue("namesql");
		$prefix=0;
		while(true)
		{
			if($prefix>0)
				$sname=postvalue("namesql")."_".$prefix;
			$rs=db_query("select count(*) from ".AddTableWrappers("webreport_sql")." where ".AddFieldWrappers("sqlname")."='".db_addslashes($sname)."'",$conn);
			$data = db_fetch_numarray($rs);
			if($data[0]>0)
				$prefix++;
			else
				break;
		}
		db_exec("insert into ".AddTableWrappers("webreport_sql")." (".AddFieldWrappers("sqlname").",".AddFieldWrappers("sqlcontent").") values ('".db_addslashes($sname)."','".db_addslashes($sqlcontent)."')",$conn);
		db_exec("insert into webreport_admin (tablename,db_type,group_name) values ('".db_addslashes($sname)."','custom','".$_SESSION["UserID"]."')",$conn);
		$rs=db_query("select ".AddFieldWrappers("id")." from ".AddTableWrappers("webreport_sql")." where ".AddFieldWrappers("sqlname")."='".db_addslashes($sname)."'",$conn);
		$data = db_fetch_numarray($rs);
		$_SESSION["idSQL"]=$data[0];
	}
	echo "OK";
	exit();
}
if(postvalue("name")=="viewsql")
{

	$arr=array();
	$arr=array(0,"",postvalue("output"));
	$customSQL=$arr[2];
	$_SESSION["customSQL"]=$customSQL;
	$_SESSION["idSQL"]=$arr[0];
	$_SESSION["nameSQL"]=$arr[1];
	$_SESSION["object_sql"]=$customSQL;
	echo $customSQL;
	exit();
}
if(postvalue("name")=="getcustomsql")
{
	$arr=array();
	$arr=WRgetCurrentCustomSQL(postvalue("output"));
	$customSQL=$arr[2];
	$_SESSION["customSQL"]=$customSQL;
	$_SESSION["idSQL"]=$arr[0];
	$_SESSION["nameSQL"]=$arr[1];
	$_SESSION["object_sql"]=$customSQL;
	echo $customSQL;
	exit();
}

$arr = my_json_decode(postvalue("output"));
db_exec("delete from webreport_admin",$conn);
foreach($arr as $val)
{
	db_exec("insert into webreport_admin (tablename,db_type,group_name) values ('".db_addslashes($val["table"])."','".$val["db_type"]."','".db_addslashes($val["group"])."')",$conn);
}
echo "OK";
?>