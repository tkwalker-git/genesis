<?php

//	field labels
$fieldLabelswebreport_users = array();
$fieldLabelswebreport_users["English"]=array();
$fieldLabelswebreport_users["English"]["id"] = "Id";
$fieldLabelswebreport_users["English"]["username"] = "Username";
$fieldLabelswebreport_users["English"]["password"] = "Password";
$fieldLabelswebreport_users["English"]["email"] = "Email";


$tdatawebreport_users=array();
	$tdatawebreport_users[".NumberOfChars"]=80; 
	$tdatawebreport_users[".ShortName"]="webreport_users";
	$tdatawebreport_users[".OwnerID"]="";
	$tdatawebreport_users[".OriginalTable"]="webreport_users";
	$tdatawebreport_users[".NCSearch"]=true;
	

$tdatawebreport_users[".shortTableName"] = "webreport_users";
$tdatawebreport_users[".dataSourceTable"] = "webreport_users";
$tdatawebreport_users[".nSecOptions"] = 0;
$tdatawebreport_users[".nLoginMethod"] = 1;
$tdatawebreport_users[".recsPerRowList"] = 1;	
$tdatawebreport_users[".tableGroupBy"] = "0";
$tdatawebreport_users[".dbType"] = 3;
$tdatawebreport_users[".mainTableOwnerID"] = "";
$tdatawebreport_users[".moveNext"] = 1;

$tdatawebreport_users[".listAjax"] = false;

	$tdatawebreport_users[".audit"] = false;

	$tdatawebreport_users[".locking"] = false;
	
$tdatawebreport_users[".listIcons"] = true;
$tdatawebreport_users[".edit"] = true;
$tdatawebreport_users[".inlineEdit"] = true;
$tdatawebreport_users[".view"] = true;

$tdatawebreport_users[".exportTo"] = true;

$tdatawebreport_users[".printFriendly"] = true;

$tdatawebreport_users[".delete"] = true;

$tdatawebreport_users[".showSimpleSearchOptions"] = false;

$tdatawebreport_users[".showSearchPanel"] = true;


$tdatawebreport_users[".isUseAjaxSuggest"] = true;

$tdatawebreport_users[".rowHighlite"] = true;


// button handlers file names

// start on load js handlers








// end on load js handlers



$tdatawebreport_users[".arrKeyFields"][] = "id";

// use datepicker for search panel
$tdatawebreport_users[".isUseCalendarForSearch"] = false;

// use timepicker for search panel
$tdatawebreport_users[".isUseTimeForSearch"] = false;





$tdatawebreport_users[".isUseInlineAdd"] = true;

$tdatawebreport_users[".isUseInlineEdit"] = true;
$tdatawebreport_users[".isUseInlineJs"] = $tdatawebreport_users[".isUseInlineAdd"] || $tdatawebreport_users[".isUseInlineEdit"];

$tdatawebreport_users[".allSearchFields"] = array();

$tdatawebreport_users[".globSearchFields"][] = "id";
// do in this way, because combine functions array_unique and array_merge returns array with keys like 1,2, 4 etc
if (!in_array("id", $tdatawebreport_users[".allSearchFields"]))
{
	$tdatawebreport_users[".allSearchFields"][] = "id";	
}
$tdatawebreport_users[".globSearchFields"][] = "username";
// do in this way, because combine functions array_unique and array_merge returns array with keys like 1,2, 4 etc
if (!in_array("username", $tdatawebreport_users[".allSearchFields"]))
{
	$tdatawebreport_users[".allSearchFields"][] = "username";	
}
$tdatawebreport_users[".globSearchFields"][] = "password";
// do in this way, because combine functions array_unique and array_merge returns array with keys like 1,2, 4 etc
if (!in_array("password", $tdatawebreport_users[".allSearchFields"]))
{
	$tdatawebreport_users[".allSearchFields"][] = "password";	
}
$tdatawebreport_users[".globSearchFields"][] = "email";
// do in this way, because combine functions array_unique and array_merge returns array with keys like 1,2, 4 etc
if (!in_array("email", $tdatawebreport_users[".allSearchFields"]))
{
	$tdatawebreport_users[".allSearchFields"][] = "email";	
}



	

$tdatawebreport_users[".isDisplayLoading"] = true;

$tdatawebreport_users[".isResizeColumns"] = false;


$tdatawebreport_users[".createLoginPage"] = true;


 	




$tdatawebreport_users[".pageSize"] = 20;

$gstrOrderBy = "";
if(strlen($gstrOrderBy) && strtolower(substr($gstrOrderBy,0,8))!="order by")
	$gstrOrderBy = "order by ".$gstrOrderBy;
$tdatawebreport_users[".strOrderBy"] = $gstrOrderBy;
	
$tdatawebreport_users[".orderindexes"] = array();

$tdatawebreport_users[".sqlHead"] = "SELECT id,   username,   password,   email";

$tdatawebreport_users[".sqlFrom"] = "FROM webreport_users";

$tdatawebreport_users[".sqlWhereExpr"] = "";

$tdatawebreport_users[".sqlTail"] = "";



	$tableKeys=array();
	$tableKeys[]="id";
	$tdatawebreport_users[".Keys"]=$tableKeys;

	
//	id
	$fdata = array();
	$fdata["strName"] = "id";
	$fdata["ownerTable"] = "webreport_users";
		$fdata["Label"]="Id"; 
			$fdata["FieldType"]= 3;
		$fdata["AutoInc"]=true;
			$fdata["UseiBox"] = false;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	

		
			$fdata["NeedEncode"]=true;
	
	$fdata["GoodName"]= "id";
		$fdata["FullName"]= "id";
						$fdata["Index"]= 1;
	
			$fdata["EditParams"]="";
			 $fdata["bListPage"]=true; 
				$fdata["FieldPermissions"]=true;
							$fdata["ListPage"]=true;
			$tdatawebreport_users["id"]=$fdata;
	
//	username
	$fdata = array();
	$fdata["strName"] = "username";
	$fdata["ownerTable"] = "webreport_users";
		$fdata["Label"]="Username"; 
			$fdata["FieldType"]= 202;
				$fdata["UseiBox"] = false;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	

		
			$fdata["NeedEncode"]=true;
	
	$fdata["GoodName"]= "username";
		$fdata["FullName"]= "username";
						$fdata["Index"]= 2;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=200";
		 $fdata["bListPage"]=true; 
				$fdata["FieldPermissions"]=true;
							$fdata["ListPage"]=true;
			$tdatawebreport_users["username"]=$fdata;
	
//	password
	$fdata = array();
	$fdata["strName"] = "password";
	$fdata["ownerTable"] = "webreport_users";
		$fdata["Label"]="Password"; 
			$fdata["FieldType"]= 202;
				$fdata["UseiBox"] = false;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	

		
			$fdata["NeedEncode"]=true;
	
	$fdata["GoodName"]= "password";
		$fdata["FullName"]= "password";
						$fdata["Index"]= 3;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=200";
		 $fdata["bListPage"]=true; 
				$fdata["FieldPermissions"]=true;
							$fdata["ListPage"]=true;
			$tdatawebreport_users["password"]=$fdata;
	
//	email
	$fdata = array();
	$fdata["strName"] = "email";
	$fdata["ownerTable"] = "webreport_users";
		$fdata["Label"]="Email"; 
			$fdata["FieldType"]= 202;
				$fdata["UseiBox"] = false;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	

		
			$fdata["NeedEncode"]=true;
	
	$fdata["GoodName"]= "email";
		$fdata["FullName"]= "email";
						$fdata["Index"]= 4;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=200";
		 $fdata["bListPage"]=true; 
				$fdata["FieldPermissions"]=true;
							$fdata["ListPage"]=true;
			$tdatawebreport_users["email"]=$fdata;

	
$tables_data["webreport_users"]=&$tdatawebreport_users;
$field_labels["webreport_users"] = &$fieldLabelswebreport_users;

// -----------------start  prepare master-details data arrays ------------------------------//
// tables which are detail tables for current table (master)
$detailsTablesData["webreport_users"] = array();

	
// tables which are master tables for current table (detail)
$masterTablesData["webreport_users"] = array();

// -----------------end  prepare master-details data arrays ------------------------------//

require_once(getabspath("classes/sql.php"));










$proto0=array();
$proto0["m_strHead"] = "SELECT";
$proto0["m_strFieldList"] = "id,   username,   password,   email";
$proto0["m_strFrom"] = "FROM webreport_users";
$proto0["m_strWhere"] = "";
$proto0["m_strOrderBy"] = "";
$proto0["m_strTail"] = "";
$proto1=array();
$proto1["m_sql"] = "";
$proto1["m_uniontype"] = "SQLL_UNKNOWN";
	$obj = new SQLNonParsed(array(
	"m_sql" => ""
));

$proto1["m_column"]=$obj;
$proto1["m_contained"] = array();
$proto1["m_strCase"] = "";
$proto1["m_havingmode"] = "0";
$proto1["m_inBrackets"] = "0";
$proto1["m_useAlias"] = "0";
$obj = new SQLLogicalExpr($proto1);

$proto0["m_where"] = $obj;
$proto3=array();
$proto3["m_sql"] = "";
$proto3["m_uniontype"] = "SQLL_UNKNOWN";
	$obj = new SQLNonParsed(array(
	"m_sql" => ""
));

$proto3["m_column"]=$obj;
$proto3["m_contained"] = array();
$proto3["m_strCase"] = "";
$proto3["m_havingmode"] = "0";
$proto3["m_inBrackets"] = "0";
$proto3["m_useAlias"] = "0";
$obj = new SQLLogicalExpr($proto3);

$proto0["m_having"] = $obj;
$proto0["m_fieldlist"] = array();
						$proto5=array();
			$obj = new SQLField(array(
	"m_strName" => "id",
	"m_strTable" => "webreport_users"
));

$proto5["m_expr"]=$obj;
$proto5["m_alias"] = "";
$obj = new SQLFieldListItem($proto5);

$proto0["m_fieldlist"][]=$obj;
						$proto7=array();
			$obj = new SQLField(array(
	"m_strName" => "username",
	"m_strTable" => "webreport_users"
));

$proto7["m_expr"]=$obj;
$proto7["m_alias"] = "";
$obj = new SQLFieldListItem($proto7);

$proto0["m_fieldlist"][]=$obj;
						$proto9=array();
			$obj = new SQLField(array(
	"m_strName" => "password",
	"m_strTable" => "webreport_users"
));

$proto9["m_expr"]=$obj;
$proto9["m_alias"] = "";
$obj = new SQLFieldListItem($proto9);

$proto0["m_fieldlist"][]=$obj;
						$proto11=array();
			$obj = new SQLField(array(
	"m_strName" => "email",
	"m_strTable" => "webreport_users"
));

$proto11["m_expr"]=$obj;
$proto11["m_alias"] = "";
$obj = new SQLFieldListItem($proto11);

$proto0["m_fieldlist"][]=$obj;
$proto0["m_fromlist"] = array();
												$proto13=array();
$proto13["m_link"] = "SQLL_MAIN";
			$proto14=array();
$proto14["m_strName"] = "webreport_users";
$proto14["m_columns"] = array();
$proto14["m_columns"][] = "id";
$proto14["m_columns"][] = "username";
$proto14["m_columns"][] = "password";
$proto14["m_columns"][] = "email";
$obj = new SQLTable($proto14);

$proto13["m_table"] = $obj;
$proto13["m_alias"] = "";
$proto15=array();
$proto15["m_sql"] = "";
$proto15["m_uniontype"] = "SQLL_UNKNOWN";
	$obj = new SQLNonParsed(array(
	"m_sql" => ""
));

$proto15["m_column"]=$obj;
$proto15["m_contained"] = array();
$proto15["m_strCase"] = "";
$proto15["m_havingmode"] = "0";
$proto15["m_inBrackets"] = "0";
$proto15["m_useAlias"] = "0";
$obj = new SQLLogicalExpr($proto15);

$proto13["m_joinon"] = $obj;
$obj = new SQLFromListItem($proto13);

$proto0["m_fromlist"][]=$obj;
$proto0["m_groupby"] = array();
$proto0["m_orderby"] = array();
$obj = new SQLQuery($proto0);

$queryData_webreport_users = $obj;
$tdatawebreport_users[".sqlquery"] = $queryData_webreport_users;



?>
