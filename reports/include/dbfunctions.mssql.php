<?php
function db_addslashes($str)
{
	return str_replace("'","''",$str);
}

function db_stripslashes($str)
{
	return str_replace("''","'",$str);
}

function db_addslashesbinary($str)
{
	return "0x".bin2hex($str);
}

function db_stripslashesbinary($str)
{
//	try to remove ole header for BMP pictures
	$pos = strpos($str,".Picture");
	if($pos===false || $pos>300)
		return $str;
	$pos1=strpos($str,"BM",$pos);
	if($pos1===false || $pos1>300)
		return $str;
	return substr($str,$pos1);
}

// adds wrappers to field name if required
function AddFieldWrappers($strName)
{
	global $strLeftWrapper,$strRightWrapper;
	if(substr($strName,0,1)==$strLeftWrapper)
		return $strName;
	return $strLeftWrapper.$strName.$strRightWrapper;
}

function AddTableWrappers($strName)
{
	global $strLeftWrapper,$strRightWrapper;
	if(substr($strName,0,1)==$strLeftWrapper)
		return $strName;
	$arr=explode(".",$strName);
	$ret=$strLeftWrapper.$arr[0].$strRightWrapper;
	if(count($arr)>1)
		$ret.=".".$strLeftWrapper.$arr[1].$strRightWrapper;
	return $ret;
}

// removes wrappers from field name if required
function RemoveFieldWrappers($strName)
{
	global $strLeftWrapper,$strRightWrapper;
	if(substr($strName,0,1)==$strLeftWrapper)
		return substr($strName,1,strlen($strName)-2);
	return $strName;
}

function RemoveTableWrappers($strName)
{
	global $strLeftWrapper,$strRightWrapper;
	if(substr($strName,0,1)!=$strLeftWrapper)
		return $strName;
	$arr=explode(".",$strName);
	$ret=substr($arr[0],1,strlen($arr[0])-2);
	if(count($arr)>1)
		$ret.=".".substr($arr[1],1,strlen($arr[1])-2);
	return $ret;
}

function db_upper($dbval)
{
	return "upper(".$dbval.")";
}

function db_datequotes($val)
{
	return "convert(datetime,'".$val."',120)";
}
?>