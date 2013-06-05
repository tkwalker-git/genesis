<?php


function db_connect() 
{
    global $ODBCString,$config;
	$uid="";
	$pwd="";
	if($config["databaseType"]=="firebird")
	{
		$uid=$config["firebird"]["uid"];
		$pwd=$config["firebird"]["pwd"];
	}
	$conn = odbc_connect($ODBCString,$uid,$pwd);
	if (!$conn) 
	{
	  trigger_error(db_error(), E_USER_ERROR);
	}
	return $conn;
}

function db_close($conn)
{
  return odbc_close($conn);
}

function db_query($qstring,$conn) 
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	$count=0;
	if(!($rs=odbc_exec($conn,$qstring)))
	  trigger_error(odbc_error(), E_USER_ERROR);
	odbc_binmode($rs,ODBC_BINMODE_RETURN);
	odbc_longreadlen($rs,1024*1024);
	$ret=array($count,$rs);
	return $ret;
	
}

function db_query_direct($qstring,$conn,$rowcount)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	if(!($rs=odbc_exec($conn,$qstring)))
	  trigger_error(odbc_error(), E_USER_ERROR);
	odbc_binmode($rs,ODBC_BINMODE_RETURN);
	odbc_longreadlen($rs,1024*1024);
	$ret=array($rowcount,$rs);
	return $ret;
}

function db_exec($qstring,$conn)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	return odbc_exec($conn,$qstring);
}

function db_pageseek(&$qhandle,$pagesize,$page)
{
	db_dataseek($qhandle,($page-1)*$pagesize);
}

function db_dataseek(&$qhandle,$row)
{
   $i=0;
   while($i<$row)
   {
   		odbc_fetch_row($qhandle[1]);
		$i++;
   }
}

function db_fetch_array(&$qhandle) {
	return odbc_fetch_array($qhandle[1]);
}

function db_fetch_numarray(&$qhandle) {
	$row=array();
	odbc_fetch_into($qhandle[1],$row);
	return $row;
}
	
function db_error() {
	return @odbc_errormsg();
}

function db_numfields(&$lhandle) {
	return @odbc_num_fields($lhandle[1]);
}

function db_fieldname(&$lhandle,$fnumber) 
{
	return @odbc_field_name($lhandle[1],$fnumber+1);
}

?>