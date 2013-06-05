<?php
$mssql_dmy="mdy";

function db_connect()
{
	global $host,$user,$pwd,$dbname,$mssql_dmy,$cCodepage;
    $connstr="PROVIDER=SQLOLEDB;SERVER=".$host.";UID=".$user.";PWD=".$pwd.";DATABASE=".$dbname;
	try {
	$conn = new COM("ADODB.Connection",NULL,$cCodepage);
	$conn->Open($connstr);
	$rs=$conn->Execute("select convert(datetime,'2000-11-22',121)");
	$str=$rs->Fields[0]->Value;
	$y=strpos($str,"2000");
	$m=strpos($str,"11");
	$d=strpos($str,"22");
	if($y<$m && $m<$d)
		$mssql_dmy="ymd";
	if($d<$m && $m<$y)
		$mssql_dmy="dmy";
	} catch(com_exception $e)
	{
		trigger_error($e->getMessage(),E_USER_ERROR);
	}
	return $conn;
}

function db_close($conn)
{
	$conn->Close();
}

function db_query($qstring,$conn)
{
	global $strLastSQL,$dDebug;
	if ($dDebug===true)
		echo $qstring."<br>";
	$strLastSQL=$qstring;
	try{
	return $conn->Execute($qstring);
	} catch(com_exception $e)
	{
		trigger_error($e->getMessage(),E_USER_ERROR);
	}

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
	if($page==1)
		return;
	if($qhandle->EOF())
		return;
   $qhandle->Move($pagesize*($page-1));
}

function db_fetch_array($rs, $assoc=1)
{
	global $mssql_dmy;
	if( $rs->EOF() )
           return false;
	try {
	$ret=array();
	for( $i = 0; $i < db_numfields($rs); $i++ )
	{
		if(IsBinaryType($rs->Fields[$i]->Type) && $rs->Fields[$i]->Type!=128)
		{
			$str="";
			if($rs->Fields[$i]->ActualSize)
			{
				$val=$rs->Fields[$i]->GetChunk($rs->Fields[$i]->ActualSize);
				$str=str_pad("",count($val));
				$j=0;
				foreach($val as $byte)
					$str[$j++]=chr($byte);
			}
			if($assoc)
				$ret[$rs->Fields[$i]->Name] = $str;
			else
				$ret[$i] = $str;
		}
		else
		{
			$value = $rs->Fields[$i]->Value;
			if(is_null($value))
			{
				$val=NULL;
			}
			else
			{
				if(isdatefieldtype($rs->Fields[$i]->Type))
					$value=localdatetime2db((string)$rs->Fields[$i]->Value,$mssql_dmy);
				if(IsNumberType($rs->Fields[$i]->Type))
					$val=floatval($value);
				else
					$val=strval($value);
			}
			if($assoc)
				$ret[$rs->Fields[$i]->Name] = $val;
			else
				$ret[$i] = $val;
		}
	}
	$rs->MoveNext();
	} catch(com_exception $e)
	{
		trigger_error($e->getMessage(),E_USER_ERROR);
	}

	return $ret;
}

function db_fetch_numarray($qhandle)
{
	return db_fetch_array($qhandle,0);
}

function db_error()
{
	global $conn;
	return $conn->Errors[$conn->Errors->Count-1]->Description;
}



function db_numfields($lhandle)
{
	return $lhandle->Fields->Count;
}

function db_fieldname($lhandle,$fnumber)
{
	return $lhandle->Fields($fnumber)->Name;
}

function db_insertid($qhandle)
{
$strSQL = "select @@IDENTITY as indent";
$rs = db_query($strSQL,$qhandle);
$row = db_fetch_array($rs);
return $row["indent"];
}

?>