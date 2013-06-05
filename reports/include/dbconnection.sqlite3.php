<?php


function db_connect() 
{
    global $dbname;
	$conn = new SQLite3($dbname);
	if (!$conn) 
	{
	  trigger_error($conn->lastErrorMsg(), E_USER_ERROR);
	}
	return $conn;
}

function db_close($conn)
{
  return $conn->close();
}

function db_query($qstring,$conn) 
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	if(!($ret=$conn->query($qstring)))
	{
	  trigger_error($conn->lastErrorMsg(), E_USER_ERROR);
	}
	return $ret;
	
}

function db_exec($qstring,$conn)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	return $conn->exec($qstring);
}

function db_pageseek($qhandle,$pagesize,$page)
{
	db_dataseek($qhandle,($page-1)*$pagesize);
}

function db_dataseek($qhandle,$row)
{
    for($i=0;$i<$row;$i++)
		$qhandle->fetchArray();
}

function db_fetch_array($qhandle) {
	return $qhandle->fetchArray($mode=SQLITE3_ASSOC);
}

function db_fetch_numarray($qhandle) {
	return $qhandle->fetchArray($mode=SQLITE3_NUM);
}


function db_numfields(&$lhandle) {
	return $lhandle->numColumns();
}

function db_fieldname(&$lhandle,$fnumber) 
{
	return $lhandle->columnName($fnumber);
}

function db_error() 
{
	global $conn;
	return @$conn->lastErrorMsg();
}

?>