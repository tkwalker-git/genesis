<?php
function db_addslashes($str)
{
	return str_replace("'","''",$str);
}

function db_addslashesbinary($str)
{
	if(!strlen($str))
		return "''";
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
	return AddFieldWrappers($strName);
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
	return RemoveFieldWrappers($strName);
}

function db_upper($dbval)
{
	return "ucase(".$dbval.")";
}

function db_datequotes($val)
{
	return "#".$val."#";
}

?>