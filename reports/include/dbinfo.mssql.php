<?php
function db_gettablelist()
{
	global $conn;
	$ret=array();
	$strSQL="sp_tables";
	$rs=db_query($strSQL,$conn);
	while($data=db_fetch_array($rs))
		if(strtoupper($data["TABLE_OWNER"])!="SYS" && strtoupper($data["TABLE_OWNER"])!="INFORMATION_SCHEMA")
			$ret[]=$data["TABLE_OWNER"].".".$data["TABLE_NAME"];
	return $ret;
}

function db_getfieldslist($strSQL)
{
	global $conn;
	$res=array();
	$rs=db_query($strSQL,$conn);
	for($i=0;$i<db_numfields($rs);$i++)
	{
		$ntype=$rs->Fields[$i]->Type;
		$res[$i]=array("fieldname"=>db_fieldname($rs,$i),"type"=>$ntype,"not_null"=>0);
	}
	return $res;
}
?>