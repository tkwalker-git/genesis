<?php
function db_gettablelist()
{
	global $conn;
	$ret=array();
	$strSQL="SELECT tbl_name FROM sqlite_master WHERE type='table' ORDER BY name";
	$rs=db_query($strSQL,$conn);
	while($data=db_fetch_array($rs))
		$ret[]=$data["tbl_name"];
	return $ret;
}
function db_getfieldslist($strSQL)
{
	global $conn;
	$res=array();
	$rs=db_query($strSQL,$conn);
	for($i=0;$i<db_numfields($rs);$i++)
	{
		$res[$i]=array("fieldname"=>db_fieldname($rs,$i),"type"=>202,"is_nullable"=>0);
	}
	return $res;
}
?>