<?php

@ini_set("mssql.datetimeconvert","0");

function db_connect()
{
    global $host,$user,$pwd,$dbname;
	$conn=mssql_connect($host,$user,$pwd);
	if(!$conn)
		trigger_error(db_error(),E_USER_ERROR);
	mssql_select_db($dbname,$conn);
	return $conn;
}

function db_close($conn)
{
	return mssql_close($conn);
}

function db_query($qstring,$conn)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	if(!($ret=mssql_query($qstring,$conn)))
	{
	  trigger_error(db_error(), E_USER_ERROR);
	}
	return $ret;
	
}

function db_exec($qstring,$conn)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	return mssql_query($qstring,$conn);

}

function db_pageseek($qhandle,$pagesize,$page)
{
	db_dataseek($qhandle,($page-1)*$pagesize);
}

function db_dataseek($qhandle,$row)
{
   if($row>0)
	   mssql_data_seek($qhandle,$row);
}

function db_fetch_array($qhandle)
{
	return @mssql_fetch_array($qhandle,MSSQL_ASSOC);
}

function db_fetch_numarray($qhandle)
{
	return @mssql_fetch_array($qhandle,MSSQL_NUM);
}

function db_error()
{
	return @mssql_get_last_message();
}


function db_numfields($lhandle)
{
	return @mssql_num_fields($lhandle);
}

function db_fieldname($lhandle,$fnumber)
{
	return @mssql_field_name($lhandle,$fnumber);
}



?>