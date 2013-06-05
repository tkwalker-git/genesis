<?php
function db_addslashes($str)
{
	$ora_maxstring=4000;
	if(strlen($str)<$ora_maxstring)
		return str_replace("'","''",$str);
//	split ret to 4000-len substrings
	$i=0;
	$out="'";
	while($i<strlen($str))
	{
		$out.="||to_clob('".str_replace("'","''",substr($str,$i,$ora_maxstring))."')";
		$i+=$ora_maxstring;
	}
	$out.="||'";
	return $out;
	
}

function db_addslashesbinary($str)
{
	return $str;
}

function db_stripslashesbinary($str)
{
	return $str;
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
	return "'".$val."'";
}

?>