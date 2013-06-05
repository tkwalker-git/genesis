<?php

function db_connect()
{
    global $user,$pwd,$sid;
	$conn=ociplogon($user,$pwd,$sid);
	if(!$conn)
		trigger_error(db_error(),E_USER_ERROR);
	$stmt=ociparse($conn,"alter session set nls_date_format='YYYY-MM-DD HH24:MI:SS'");
	ociexecute($stmt);
	ocifreestatement($stmt);
	return $conn;
}

function db_close($conn)
{
	return @ocilogoff($conn);
}

function db_query($qstring,$conn)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	$rowcount=0;
	$stmt=ociparse($conn,$qstring);
	$stmt_type=ocistatementtype($stmt);
	if(!ociexecute($stmt))
		trigger_error(db_error(), E_USER_ERROR);
	return array($stmt,$rowcount);
}

function db_query_direct($qstring,$conn,$rowcount)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	$stmt=ociparse($conn,$qstring);
	$stmt_type=ocistatementtype($stmt);
	if(!ociexecute($stmt))
		trigger_error(db_error(), E_USER_ERROR);
	return array($stmt,$rowcount);
}


function db_exec($qstring,$conn)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	$stmt=ociparse($conn,$qstring);
	$stmt_type=ocistatementtype($stmt);
	if(!ociexecute($stmt))
	{
		trigger_error(db_error(), E_USER_ERROR);
		return 0;
	}
	else
		return 1;
}

function db_pageseek($qhandle,$pagesize,$page)
{
	db_dataseek($qhandle,($page-1)*$pagesize);
}

function db_dataseek(&$qhandle,$row)
{
	for($i=0;$i<$row;$i++)
		myoci_fetch_array($qhandle[0],OCI_NUM+OCI_RETURN_NULLS);
}


function db_fetch_array($qhandle)
{
	return myoci_fetch_array($qhandle[0],OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS);
}

function db_fetch_numarray($qhandle)
{
	return myoci_fetch_array($qhandle[0],OCI_NUM+OCI_RETURN_NULLS+OCI_RETURN_LOBS);
}

function db_error()
{
	$arr=ocierror();
	if(count($arr)>1)
		return $arr["code"]." - ".$arr["message"];
}



function myoci_fetch_array($qhandle,$flags)
{
	if(function_exists("oci_fetch_array"))
		return oci_fetch_array($qhandle,$flags);
	$data=array();
	if(ocifetchinto($qhandle,$data,$flags))
		return $data;
	return false;
}

function db_numfields($lhandle)
{
	return OCINumCols($lhandle[0]);
}

function db_fieldname($lhandle,$fnumber)
{
	return OCIColumnName($lhandle[0], $fnumber+1);
}



?>