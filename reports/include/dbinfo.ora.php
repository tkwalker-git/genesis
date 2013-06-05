<?php
function db_gettablelist()
{
	global $conn;
	$ret=array();
	$strSQL="select owner||'.'||table_name as name,'TABLE' as type from all_tables where owner not like '%SYS%'
                 union all
                 select owner||'.'||view_name as name,'VIEW' from all_views where owner not like '%SYS%'";
	$rs=db_query($strSQL,$conn);
	while($data=db_fetch_numarray($rs))
		$ret[]=$data[0];
	return $ret;
}
function db_getfieldslist($strSQL)
{
	global $conn;
	$res=array();
	$rs=db_query($strSQL,$conn);
	for($i=0;$i<db_numfields($rs);$i++)
	{
		$stype=oci_field_type($rs[0],$i+1);
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
		case "INTEGER":
			$ntype=3;
			break;
		case (strtoupper($stype)=="SMALLINT" || strtoupper($stype)=="OCTET"):
			$ntype=2;
			break;
		case "NUMBER":
			$ntype=14;
			break;
		case (strtoupper($stype)=="REAL" || strtoupper($stype)=="DOUBLE" || strtoupper($stype)=="FLOAT"):
			$ntype=5;
			break;
		case (strtoupper($stype)=="BLOB" || strtoupper($stype)=="BFILE") :
			$ntype=128;
			break;
		case "CHAR":
			$ntype=129;
			break;
		case (strtoupper($stype)=="VARCHAR" || strtoupper($stype)=="VARCHAR2"):
			$ntype=200;
			break;
		case "DATE":
			$ntype=135;
			break;
		case "CLOB":
			$ntype=201;
			break;
	}
	return $ntype;
}
?>