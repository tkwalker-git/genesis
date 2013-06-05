<?php

$version = explode('.', PHP_VERSION);
if($version[0]*10+$version[1]<53)
	set_magic_quotes_runtime(0);

if(!file_exists("config.php"))
	message_error_install("Config error","Can not find file config.php","");

include("config.php");	

if(@$_SERVER["REQUEST_URI"])
{
	$pinfo=pathinfo($_SERVER["REQUEST_URI"]);
	$dirname = @$pinfo["dirname"];
	$dir = explode("/",$dirname);
	$dirname="";
	foreach($dir as $subdir)
	{
		if($subdir!="")
			$dirname.="/".rawurlencode($subdir);
	}
	if($dirname!="")
	{
//		@session_set_cookie_params(0,$dirname."/");
	}
}
@session_cache_limiter("none");
@session_start();

error_reporting(E_ALL ^ E_NOTICE);

/// include php specific code
include("phpfunctions.php");

if($config["databaseType"]!="mysql" && $config["databaseType"]!="access" && $config["databaseType"]!="mssql" && $config["databaseType"]!="postgre" && $config["databaseType"]!="oracle" && $config["databaseType"]!="sqlite" && $config["databaseType"]!="firebird")
		message_error_install("Config error","Database type is not defined!","Set database type in config.php.");

if(!isset($config["locale"]))
	$config["locale"]=1033;
if(!file_exists("locales/l".$config["locale"].".php"))
	message_error_install("Config error","Locale is not defined!","Set locale in config.php.");
		
if($config["databaseType"]=="mysql")
{
	$host=$config["mysql"]["host"];
	$user=$config["mysql"]["username"];
	$pwd=$config["mysql"]["password"];
	$port=$config["mysql"]["port"];
	$sys_dbname=$config["mysql"]["dbname"];
	$config["dbType"]=0;
	include("dbconnection.my.php");
	include("dbfunctions.my.php");
	include("dbinfo.my.php");
}
elseif($config["databaseType"]=="mssql")
{
	$host=$config["mssql"]["host"];
	$user=$config["mssql"]["username"];
	$pwd=$config["mssql"]["password"];
	$config["dbType"]=2;
	$dbname=$config["mssql"]["dbname"];
	include("dbconnection.mssql.php");
	include("dbfunctions.mssql.php");
	include("dbinfo.mssql.php");
}
elseif($config["databaseType"]=="oracle")
{
	$user=$config["oracle"]["username"];
	$pwd=$config["oracle"]["password"];
	$sid=$config["oracle"]["servername"];
	$config["dbType"]=1;
	include("dbconnection.ora.php");
	include("dbfunctions.ora.php");
	include("dbinfo.ora.php");
}
elseif($config["databaseType"]=="postgre")
{
	$host=$config["postgre"]["host"];
	$user=$config["postgre"]["username"];
	$password=$config["postgre"]["password"];
	$options=$config["postgre"]["options"];
	$dbname=$config["postgre"]["dbname"];
	$config["dbType"]=4;
	$connstr=	"host='".pg_escape_string($host).
				"' user='".pg_escape_string($user).
				"' password='".pg_escape_string($password).
				"' dbname='".pg_escape_string($dbname).
				"' ".$options;
	include("dbconnection.pg.php");
	include("dbfunctions.pg.php");
	include("dbinfo.pg.php");
}
elseif($config["databaseType"]=="access")
{
	$ODBCString = $config["access"]["odbc_string"];
	$config["dbType"]=3;
	include("dbconnection.odbc.php");
	include("dbfunctions.odbc.php");
	include("dbinfo.odbc.php");
}
elseif($config["databaseType"]=="sqlite")
{
	$dbname = $config["sqlite"]["dbname"];
	$config["dbType"]=-1;
	include("dbconnection.sqlite3.php");
	include("dbfunctions.sqlite3.php");
	include("dbinfo.sqlite3.php");
}
elseif($config["databaseType"]=="firebird")
{
	$ODBCString = $config["firebird"]["odbc_string"];
	$config["dbType"]=-1;
	include("dbconnection.odbc.php");
	include("dbfunctions.odbc.php");
	include("dbinfo.odbc.php");
}

$cCharset = "utf-8";

$gLoadSearchControls = 30;

$projectPath = '';

include("locales/l".$config["locale"].".php");
include("locale.php");
include("events.php");
include("commonfunctions.php");
include("appsettings.php");

if($config["databaseType"]=="mysql")
{
	$strLeftWrapper="`";
	$strRightWrapper="`";	
	$nDBType=0;
}
elseif($config["databaseType"]=="mssql")
{
	$strLeftWrapper="[";
	$strRightWrapper="]";
	$nDBType=2;
}
elseif($config["databaseType"]=="oracle")
{
	$strLeftWrapper="\"";
	$strRightWrapper="\"";	
	$nDBType=1;
}
elseif($config["databaseType"]=="postgre")
{
	$strLeftWrapper="\"";
	$strRightWrapper="\"";
	$nDBType=4;
}
elseif($config["databaseType"]=="access")
{
	$strLeftWrapper="[";
	$strRightWrapper="]";
	$nDBType=3;
}
elseif($config["databaseType"]=="sqlite")
{
	$strLeftWrapper="\"";
	$strRightWrapper="\"";
	$nDBType=-1;
}
elseif($config["databaseType"]=="firebird")
{
	$strLeftWrapper="\"";
	$strRightWrapper="\"";
	$nDBType=-1;
}

header("Content-Type: text/html; charset=".$cCharset);

set_error_handler("error_handler");

$WRAdminPagePassword = "";
$wr_is_standalone=true;
$wr_version=1.3;
// json support
if($cCharset == "utf-8")
	$useUTF8 = true;
else
	$useUTF8 = false;

if(!function_exists('json_encode') || !$useUTF8)
{
	include(getabspath("classes/json.php"));
	$GLOBALS['JSON_OBJECT'] = new Services_JSON(SERVICES_JSON_LOOSE_TYPE, $useUTF8);
               
    function my_json_encode($value){
    	return $GLOBALS['JSON_OBJECT']->encode($value);
    }
   
    function my_json_decode($value){
        return $GLOBALS['JSON_OBJECT']->decode($value);
	}
}
else
{
	function my_json_encode($value){
    	return json_encode($value);
    }
   
    function my_json_decode($value){
        return json_decode($value,true);
	}
}
function connect_error_handler($errno, $errstr, $errfile, $errline)
{

	global $strLastSQL;
	if ($errno==2048)
		return 0;	
	if($errno==8192)
	{
		if($errstr=="Assigning the return value of new by reference is deprecated")
			return 0;
		if(strpos($errstr,"set_magic_quotes_runtime"))
			return 0;
	}

	if($errno==2 && strpos($errstr,"has been disabled for security reasons"))
		return 0;
	if($errno==2 && strpos($errstr,"Data is not in a recognized format"))
		return 0;
	if($errno==8 && !strncmp($errstr,"Undefined index",15))
		return 0;
	if(strpos($errstr,"It is not safe to rely on the system's timezone settings."))
		return 0;	
	if(strpos($errstr,"fopen(")===0)
		return 0;
	
	echo "
		<html>
		<head>
		<link REL=\"stylesheet\" href=\"include/style.css\" type=\"text/css\">
		<!--[if IE]>
		<link REL=\"stylesheet\" href=\"include/styleIE.css\" type=\"text/css\">
		<![endif]-->
		</head>
		<body>
		<table cellpadding=0 cellspacing=0 border=0 width=400px align=center style=\"margin-top:100px;\" width=400px>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td class=headerlistup_left width=7px valign=middle align=center height=31></td>
						<td class=upeditmenu_gif valign=middle align=center height=31>Config error</td>
						<td class=headerlistup_right width=7px valign=middle align=center height=31></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100% class=\"main_table_border2\">
					<tr>
						<td>
							<table cellpadding=10 cellspacing=0 border=0 width=100% class=\"main_table_border\">
								<tr>
									<td class=editshade_b style=\"border:0px;color:black;font-weight : normal;\" align=center>
										Can not connect to database
									</td>
								</tr>
								<tr>
									<td class=editshade_b style=\"border:0px;color:black;font-weight : normal;\" align=center>
										Verify connection settings in config.php.
									</td>
								</tr>
								<tr><td class=main_table_border_P align=center style=\"padding-bottom:0px\">
									<a class=\"tablelinks\" href=\"readme.txt\">Installation instructions</a>
								</td></tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td class=headerlistdown_left width=8px valign=middle align=center height=15></td>
						<td class=downeditmenu valign=middle align=center height=15>&nbsp;</td>
						<td class=headerlistdown_right width=8px valign=middle align=center height=15></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</body>
	</html>";
	die();
}
function message_error_install($header,$str1,$str2)
{
	echo "
	<html>
	<head>
	<link REL=\"stylesheet\" href=\"include/style.css\" type=\"text/css\">
	<!--[if IE]>
	<link REL=\"stylesheet\" href=\"include/styleIE.css\" type=\"text/css\">
	<![endif]-->
	</head>
	<body>
		<table cellpadding=0 cellspacing=0 border=0 width=400px align=center style=\"margin-top:100px;\" width=400px>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td class=headerlistup_left width=7px valign=middle align=center height=31></td>
						<td class=upeditmenu_gif valign=middle align=center height=31>".$header."</td>
						<td class=headerlistup_right width=7px valign=middle align=center height=31></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100% class=\"main_table_border2\">
					<tr>
						<td>
							<table cellpadding=10 cellspacing=0 border=0 width=100% class=\"main_table_border\">
								<tr>
									<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\" align=center>
										".$str1."
									</td>
								</tr>";
if($str2)
	echo "						<tr>
									<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\" align=center>
										".$str2."
									</td>
								</tr>";					
								
echo "						<tr><td class=main_table_border_P align=center style=\"padding-bottom:0px\">";
if($header!="Installation")
	echo "						<a href=\"readme.txt\" class=\"tablelinks\">Installation instructions</a>";
else
	echo "						<a href=\"login.php\" class=\"tablelinks\">Login</a>";
echo "						</td></tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td class=headerlistdown_left width=8px valign=middle align=center height=15></td>
						<td class=downeditmenu valign=middle align=center height=15>&nbsp;</td>
						<td class=headerlistdown_right width=8px valign=middle align=center height=15></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</body>
	</html>";
	die();
}
?>