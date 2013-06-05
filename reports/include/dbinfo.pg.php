<?php
function db_gettablelist()
{
	global $conn;
	$ret=array();
	$strSQL="select schemaname||'.'||tablename as name from pg_tables where schemaname not in ('pg_catalog','information_schema')
                 union all
                 select schemaname||'.'||viewname as name from pg_views where schemaname not in ('pg_catalog','information_schema')";
	$rs=db_query($strSQL,$conn);
	while($data=db_fetch_array($rs))
		$ret[]=$data["name"];
	return $ret;
}
function db_getfieldslist($strSQL)
{
	global $conn;
	$res=array();
	$rs=db_query($strSQL,$conn);
	for($i=0;$i<db_numfields($rs);$i++)
	{
		$stype=pg_field_type($rs,$i);
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
		case (strtoupper($stype)=="INT4" || strtoupper($stype)=="INT2" || strtoupper($stype)=="INT8"):
			$ntype=3;
			break;
		case (strtoupper($stype)=="FLOAT4" || strtoupper($stype)=="FLOAT8"):
			$ntype=5;
			break;
		case (strtoupper($stype)=="NUMERIC" || strtoupper($stype)=="MONEY"):
			$ntype=14;
			break;
		case (strtoupper($stype)=="ABSTIME" || strtoupper($stype)=="TIMESTAMP" || strtoupper($stype)=="TIMESTAMPTZ"):
			$ntype=135;
			break;
		case (strtoupper($stype)=="TIME" || strtoupper($stype)=="TIMETZ"):
			$ntype=134;
			break;
		case "BYTEA":
			$ntype=13;
			break;
		case "CHAR":
			$ntype=129;
			break;
		case "VARCHAR":
			$ntype=200;
			break;
		case "DATE":
			$ntype=7;
			break;
		case "TEXT":
			$ntype=201;
			break;
	}
	return $ntype;
}
?>