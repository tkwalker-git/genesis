<?php

function db_gettablelist()
{
	global $conn;
	$ret=array();
	$rs = odbc_tables($conn);
	while(odbc_fetch_row($rs))
		if(odbc_result($rs,"TABLE_TYPE")=="TABLE" || odbc_result($rs,"TABLE_TYPE")=="VIEW")
			$ret[]=odbc_result($rs,"TABLE_NAME");
	return $ret;
}
function db_getfieldslist($strSQL)
{
	global $conn;
	$res=array();
	$rs=db_query($strSQL,$conn);
	for($i=0;$i<db_numfields($rs);$i++)
	{
		$stype=odbc_field_type($rs[1],$i+1);
		$ntype=db_fieldtypenum($stype);
		$res[$i]=array("fieldname"=>db_fieldname($rs,$i),"type"=>$ntype,"is_nullable"=>0);
	}
	return $res;
}
function db_fieldtypenum($stype)
{
	$ntype="";
	switch(strtoupper($stype))
	{
		case "COUNTER":
			$ntype=3;
			break;
		case "VARCHAR":
			$ntype=202;
			break;
		case "LONGCHAR":
			$ntype=203;
			break;
		case "INTEGER":
			$ntype=3;
			break;
		case "BYTE":
			$ntype=17;
			break;
		case "SMALLINT":
			$ntype=2;
			break;
		case "REAL":
			$ntype=4;
			break;
		case "DOUNLE":
			$ntype=5;
			break;
		case "GUID":
			$ntype=72;
			break;
		case "DECIMAL":
			$ntype=131;
			break;
		case "DATETIME":
			$ntype=7;
			break;
		case "CURRENCY":
			$ntype=6;
			break;
		case "BIT":
			$ntype=11;
			break;
		case "LONGBINARY":
			$ntype=205;
			break;
		case "DOUBLE":
			$ntype=5;
			break;
		case "BYTE":
			$ntype=11;
			break;			
	}
	return $ntype;
}
?>