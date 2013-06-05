<?php

if(!file_exists("config.php"))
	not_ex_config();

include("config.php");

if(!isset($_REQUEST["step"]))
{
	session_unset();
	include("include/dbcommon.php");
	
	if($config["databaseType"]!="mysql" && $config["databaseType"]!="access" && $config["databaseType"]!="mssql" && $config["databaseType"]!="postgre" && $config["databaseType"]!="oracle" && $config["databaseType"]!="sqlite" && $config["databaseType"]!="firebird")
		message_error_install("Config error","Database type is not defined!","Set database type in config.php.");

	if($config["charset"]=="")
		$config["charset"]="utf-8";

	$strLastSQL="";
	$dDebug=false;

	
	$conn=db_connect();
	
	$arr_table=db_gettablelist();
	if($config["databaseType"]=="oracle" || $config["databaseType"]=="postgre" || $config["databaseType"]=="mssql")
	{
		foreach($arr_table as $ind=>$table)
		{
			$pos=strpos($table,".");
			$arr_table[$ind]=substr($table,$pos+1);
		}
	}
	$first_install=true;
	foreach($arr_table as $ind=>$table)
	{
		if(strtoupper("webreport_settings")==strtoupper($table))
		{
			$rs=db_query("select ".AddFieldWrappers("version")." from ".AddTableWrappers("webreport_settings"),$conn);
			if($data=db_fetch_numarray($rs))
				$version=floatval($data[0]);
			else
				$version=1;
			$first_install=false;
			break;
		}
	}
	if(!$first_install)
	{
		if($version<$wr_version)
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
			<form name=frmAdmin method=post action=\"install.php?step=upgrade\">
			<table cellpadding=0 cellspacing=0 border=0 width=400px align=center style=\"margin-top:100px;\" width=400px>
				<tr>
					<td>
						<table cellpadding=0 cellspacing=0 border=0 width=100%>
							<tr>
								<td class=headerlistup_left width=7px valign=middle align=center height=31></td>
								<td class=upeditmenu_gif valign=middle align=center height=31>Upgrade</td>
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
											<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;height:40px\">
												<p>Your version of ReportsMaestro is ".$version.".</p>
											</td>
										</tr>
									<tr><td class=main_table_border_P align=center style=\"padding-bottom:0px;\">
										<spam class=buttonborder><input type=submit class=buttonM value=\"Upgrade to version ".$wr_version."\" style=\"width:150px\"></span>
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
			</form>
			</body>
			</html>";
		}
		else
			message_error_install("Installation","You have the latest version of ReportsMaestro (v".$version.").","No need to upgrade.");

	}
	else
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
		<form name=frmAdmin method=post action=\"install.php?step=1\">
		<table cellpadding=0 cellspacing=0 border=0 width=400px align=center style=\"margin-top:100px;\" width=400px>
			<tr>
				<td>
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td class=headerlistup_left width=7px valign=middle align=center height=31></td>
							<td class=upeditmenu_gif valign=middle align=center height=31>Installation</td>
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
										<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\">
											Database connection OK!
										</td>
									</tr>
									<tr>
										<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\">
											&nbsp;
										</td>
									</tr>

									<tr>
										<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\">
											Administrator login: admin
										</td>
									</tr>
									<tr>
										<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\">
											Enter administrator password:&nbsp;&nbsp;&nbsp;<input type=text id=admpass name=admpass size=20>
										</td>
									</tr>
								<tr><td class=main_table_border_P align=center style=\"padding-bottom:0px;\">
									<spam class=buttonborder><input type=submit class=buttonM value=\"Install\" style=\"width:100px\"></span>
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
		</form>
		</body>
		</html>";
	}
}
elseif($_REQUEST["step"]=="1")
{
	include("include/dbcommon.php");
	$conn=db_connect();
	$admpass="";
	$admpass=postvalue("admpass");

	create_table("webreport_admin");
	create_table("webreport_style");
	create_table("webreport_settings");
	create_table("webreports");
	create_table("webreport_users");
	create_table("webreport_sql");
	db_exec("insert into ".AddTableWrappers("webreport_users")." (".AddFieldWrappers("username").",".AddFieldWrappers("password").",".AddFieldWrappers("email").") values ('admin','".md5($admpass)."','')",$conn);

	echo "
	<html>
	<head>
	<link REL=\"stylesheet\" href=\"include/style.css\" type=\"text/css\">
	<!--[if IE]>
	<link REL=\"stylesheet\" href=\"include/styleIE.css\" type=\"text/css\">
	<![endif]-->
	</head>
	<body>
	<form name=frmAdmin method=post action=\"login.php\">
	<input type=hidden name=btnSubmit value=\"Login\">
	<input type=hidden name=username value=\"admin\">
	<input type=hidden name=password value=\"".$admpass."\">
	<table cellpadding=0 cellspacing=0 border=0 width=400px align=center style=\"margin-top:100px;\" width=400px>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td class=headerlistup_left width=7px valign=middle align=center height=31></td>
						<td class=upeditmenu_gif valign=middle align=center height=31>Success!</td>
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
									<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\">
										For security reasons delete or rename install.php file.
									</td>
								</tr>
							<tr>
								<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\" align=center>
								Login as <a href=\"#\" class=tablelinks onclick='document.forms.frmAdmin.submit();'>admin</a>
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
	</form>
	</body>
	</html>";
}
elseif($_REQUEST["step"]=="upgrade")
{
	include("include/dbcommon.php");
	$conn=db_connect();
	$rs=db_query("select ".AddFieldWrappers("version")." from ".AddTableWrappers("webreport_settings"),$conn);
	if($data=db_fetch_numarray($rs))
		$version=floatval($data[0]);
	else
		$version=1;
	Upgrade($version);
	db_exec("update ".AddTableWrappers("webreport_settings")." set ".AddFieldWrappers("version")."='".$wr_version."'",$conn);
	echo "
	<html>
	<head>
	<link REL=\"stylesheet\" href=\"include/style.css\" type=\"text/css\">
	<!--[if IE]>
	<link REL=\"stylesheet\" href=\"include/styleIE.css\" type=\"text/css\">
	<![endif]-->
	</head>
	<body>
	<input type=hidden name=btnSubmit value=\"Login\">
	<table cellpadding=0 cellspacing=0 border=0 width=400px align=center style=\"margin-top:100px;\" width=400px>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td class=headerlistup_left width=7px valign=middle align=center height=31></td>
						<td class=upeditmenu_gif valign=middle align=center height=31>Success!</td>
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
									<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\">
										Your current version of ReportsMaestro is ".$wr_version.".
									</td>
								</tr>
							<tr>
								<td class=editshade_b style=\"border:0px;color:black;font-weight:normal;\" align=center>
								<a href=\"#\" class=tablelinks onclick='window.location.href=\"login.php\";return false;'>Login</a>
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
}

function create_table($dbname)
{
	global $config,$conn;
	$arr_table=db_gettablelist();
	if($config["databaseType"]=="oracle" || $config["databaseType"]=="postgre" || $config["databaseType"]=="mssql")
	{
		foreach($arr_table as $ind=>$table)
		{
			$pos=strpos($table,".");
			$arr_table[$ind]=substr($table,$pos+1);
		}
	}
	foreach($arr_table as $ind=>$table)
	{
		if(strtoupper($dbname)==strtoupper($table))
			return false;
	}
	$handle = fopen("script/".$config["databaseType"]."/".$dbname.".sql", "r");
	if(!$handle)
		die("File 'script/".$config["databaseType"]."/".$dbname.".sql' don't exist !");

	$contents = fread($handle, filesize("script/".$config["databaseType"]."/".$dbname.".sql"));
	fclose($handle);
	$pos=0;
	$arr=array();
	$arr=explode("-- \$next",$contents);
	foreach($arr as $value)
	{
		if($value)
		{
			while($pos!==false)
			{
				$pos=strpos($value,"--",$pos);
				if($pos!==false)
				{
					$posend=strpos($value,"\r\n",$pos);
					if(!$posend)
		                              $posend=strpos($value,"\n",$pos);
					$value=substr($value,0,$pos).substr($value,$posend);
				}
			}
			if($config["databaseType"]=="oracle")
				$value = str_replace("\r","",$value);
			db_exec($value,$conn);
		}
	}
}
function Upgrade($user_ver)
{

	if($user_ver<=1)
		create_table("webreport_sql");
/*	if($user_ver<=1.3)
		create_table("next_sql");
	if($user_ver<=1.4)
		create_table("next2_sql");*/
}
function not_ex_config()
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
										Can not find file config.php
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
?>

<script>
	if(document.getElementById("admpass"))
		document.getElementById("admpass").focus();
</script>