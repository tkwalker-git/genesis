<?php
$strTableName="webreport_users";
$_SESSION["OwnerID"] = $_SESSION["_".$strTableName."_OwnerID"];

$strOriginalTableName="webreport_users";

$gstrOrderBy="";
if(strlen($gstrOrderBy) && strtolower(substr($gstrOrderBy,0,8))!="order by")
	$gstrOrderBy="order by ".$gstrOrderBy;

$g_orderindexes=array();
$gsqlHead="SELECT id,   username,   password,   email";
$gsqlFrom="FROM webreport_users";
$gsqlWhereExpr="";
$gsqlTail="";

include(getabspath("include/webreport_users_settings_postgre.php"));

// alias for 'SQLQuery' object
$gQuery = &$queryData_webreport_users;
include(getabspath("include/webreport_users_events.php"));

$reportCaseSensitiveGroupFields = false;

$gstrSQL = gSQLWhere("");


?>