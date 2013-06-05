<?php


function db_connect() {
    global $connstr;
	$conn=pg_connect($connstr);
	if(!$conn)
		trigger_error("Unable to connect",E_USER_ERROR);
	return $conn;
}

function db_close($conn)
{
  return pg_close($conn);
}

function db_query($qstring,$conn) {
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;

	if(version_compare(phpversion(),"4.2.0")>=0)
		$ret=pg_query($conn,$qstring);
	else
		$ret=pg_exec($conn,$qstring);
	if(!$ret)
	{
	  trigger_error(db_error(), E_USER_ERROR);
	}
	return $ret;
	
}

function db_exec($qstring,$conn)
{
	global $dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	return db_query($qstring,$conn);
}

function db_pageseek($qhandle,$pagesize,$page)
{
	db_dataseek($qhandle,($page-1)*$pagesize);
}

function db_dataseek($qhandle,$row)
{
   pg_result_seek($qhandle,$row);
}

function db_fetch_array($qhandle) {
	$ret=pg_fetch_array($qhandle);
//	remove numeric indexes
	if(!$ret)
		return false;
	foreach($ret as $key=>$value)
		if(is_int($key))
			unset($ret[$key]);
	return $ret;
}

function db_fetch_numarray($qhandle) {
	return @pg_fetch_row($qhandle);
}
	

function db_error() {
	global $conn;
	if(version_compare(phpversion(),"4.2.0")>=0)
		return @pg_last_error($conn);
	else
		return "PostgreSQL error happened";
		
}




function db_numfields($lhandle) {
	return @pg_num_fields($lhandle);
}

function db_fieldname($lhandle,$fnumber) {
           return @pg_field_name($lhandle,$fnumber);
}



?>