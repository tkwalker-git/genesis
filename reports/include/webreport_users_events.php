<?php










// Before record added
function BeforeAdd(&$values,&$message,$inline)
{

$values["password"]=md5($values["password"]);

return true;
;
} // function BeforeAdd
$arrEventTables["BeforeAdd"]="webreport_users";



















































// Before record updated
function BeforeEdit(&$values,$where,&$oldvalues,&$keys,&$message,$inline)
{

if($oldvalues["password"]!=$values["password"])
	$values["password"]=md5($values["password"]);

return true;
;
} // function BeforeEdit
$arrEventTables["BeforeEdit"]="webreport_users";































?>