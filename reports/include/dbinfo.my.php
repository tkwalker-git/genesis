<?php
function db_gettablelist()
{
	global $conn;
	$ret=array();
	$strSQL="select DATABASE() as dbname";
	$rs=db_query($strSQL,$conn);
	$data=db_fetch_array($rs);
	if(!$data)
		return $ret;
	$dbname=$data["dbname"];
	if(mysql_get_server_info()>=5)
	{
		$strSQL="SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."'";
		$rs=db_query($strSQL,$conn);
		while($data=db_fetch_array($rs))
			$ret[]=$data["TABLE_NAME"];

		$strSQL="SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = '".$dbname."'";
		$rs=db_query($strSQL,$conn);
		while($data=db_fetch_array($rs))
			$ret[]=$data["TABLE_NAME"];
		sort($ret);
	}
	else
	{
		$strSQL="SHOW tables";
		$rs=db_query($strSQL,$conn);
		while($data=db_fetch_numarray($rs))
			$ret[]=$data[0];
	}
	
	return $ret;
}
function db_getfieldslist($strSQL)
{
	global $conn;
	$res=array();
	$rs=db_query($strSQL,$conn);
	for($i=0;$i<db_numfields($rs);$i++)
	{
		$stype=mysql_field_type($rs,$i);
		if($stype=="blob")
		{
			$flags=mysql_fieldflags($rs,$i);
			if(strpos($flags,"binary")===false)
				$stype="text";
		}

		$ntype=db_fieldtypenum($stype);
		$arr=mysql_fetch_field($rs,$i);
		$res[$i]=array("fieldname"=>db_fieldname($rs,$i),"type"=>$ntype,"not_null"=>0);
	}
	return $res;
}
function db_fieldtypenum($stype)
{
	$ntype="";
	switch(strtoupper($stype))
	{
		case "STRING":
			$ntype=200;
			break;
		case "INT":
			$ntype=3;
			break;
		case "REAL":
			$ntype=5;
			break;
		case "TIMESTAMP":
			$ntype=135;
			break;
		case "YEAR":
			$ntype=3;
			break;
		case "DATE":
			$ntype=7;
			break;
		case "TIME":
			$ntype=134;
			break;
		case "DATETIME":
			$ntype=135;
			break;
		case "BLOB":
			$ntype=128;
			break;
		case "TEXT":
			$ntype=201;
			break;
	}
	return $ntype;
}

?>