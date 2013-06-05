<?php
function includeMSSQLFile()
{
	// microsoft driver for MSSQL 2005/2008
	if (extension_loaded("sqlsrv")===true)
		return "dbconnection.mssql.sqlsrv.php";
	elseif (strtoupper(substr(PHP_OS,0,3))=="WIN" && substr(PHP_VERSION,0,1)>'4')
		return "dbconnection.mssql.win.php";
	else
		return "dbconnection.mssql.unix.php";
}

include(includeMSSQLFile());
?>