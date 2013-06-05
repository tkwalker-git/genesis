<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");

add_nocache_headers();

set_magic_quotes_runtime(0);

include("include/reportfunctions.php");

$strTableName="";

$conn=db_connect();

$xml_array=array();

$sessPrefix = "";

if (postvalue("rname")) 
{
	$xml_array = getReportArray(postvalue("rname"));
	if(!is_wr_project())
	{
		$sessPrefix = "webreport".postvalue("rname");
	}
} 
elseif (postvalue("cname")) 
{
	$xml_array = getChartArray(postvalue("cname"));
	$sessPrefix = "webchart".postvalue("cname");
}

	if(!@$_SESSION["UserID"]) {					
		$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
		header("Location: login.php?message=expired");
		return;
	} elseif ( $xml_array['settings']['status'] == "private" && $xml_array['owner'] != @$_SESSION["UserID"] ) {
		echo "<p>You don't have permissions to view this report</p>";
		exit;
	}

if (strpos($xml_array['table_relations']['join_tables'], ",") !== false) {
	$arr_join_tables = array_slice(explode(",", $xml_array['table_relations']['join_tables']), 0, -1);	
}
$arr_join_tables[] = $xml_array['tables'][0];

if(is_wr_project())
{
	foreach ($arr_join_tables as $tbl) 
	{
		include("include/" . GetTableURL($tbl) . "_settings.php");	
	}
}

if (postvalue("rname")) {
	$inspect_fields = $xml_array["totals"];
} elseif (postvalue("cname")) {
	//$inspect_fields = $xml_array["parameters"];
	$inspect_fields = array();
	for ($i=0; $i < count($arr_join_tables); $i++ ) {
		$t = $arr_join_tables[$i];
		$arr_fields = WRGetNBFieldsList($t);
		for ($j=0; $j < count($arr_fields); $j++) {
			$inspect_fields[] = array("table" => $t, "name" => $arr_fields[$j], "show" => "true");
		}
	}	
}

$where = &$_SESSION;
//}
$pageName = "dsearch.php";

//Basic includes js files
$includes="";
//javascript code
$jscode="";

include('include/xtempl.php');
$xt = new Xtempl();

//	Before Process event
if(tableEventExists("BeforeProcessSearch",$strTableName)) 
{
	$eventObj =&getEventObject($strTableName);
	$eventObj->BeforeProcessSearch($conn);
}

$includes = <<<JS
<script type="text/javascript" src="include/js/jquery.min.js"></script>
<script type="text/javascript" src="include/jsfunctions.js"></script>
<script type="text/javascript" src="include/customlabels.js"></script>
<script type="text/javascript" src="include/calendar.js"></script>
JS;

$jscode.="TEXT_MONTH_JAN='".jsreplace("January")."';\r\n";
$jscode.="TEXT_MONTH_FEB='".jsreplace("February")."';\r\n";
$jscode.="TEXT_MONTH_MAR='".jsreplace("March")."';\r\n";
$jscode.="TEXT_MONTH_APR='".jsreplace("April")."';\r\n";
$jscode.="TEXT_MONTH_MAY='".jsreplace("May")."';\r\n";
$jscode.="TEXT_MONTH_JUN='".jsreplace("June")."';\r\n";
$jscode.="TEXT_MONTH_JUL='".jsreplace("July")."';\r\n";
$jscode.="TEXT_MONTH_AUG='".jsreplace("August")."';\r\n";
$jscode.="TEXT_MONTH_SEP='".jsreplace("September")."';\r\n";
$jscode.="TEXT_MONTH_OCT='".jsreplace("October")."';\r\n";
$jscode.="TEXT_MONTH_NOV='".jsreplace("November")."';\r\n";
$jscode.="TEXT_MONTH_DEC='".jsreplace("December")."';\r\n";

$jscode.="TEXT_DAY_SU='".jsreplace("Su")."';\r\n";
$jscode.="TEXT_DAY_MO='".jsreplace("Mo")."';\r\n";
$jscode.="TEXT_DAY_TU='".jsreplace("Tu")."';\r\n";
$jscode.="TEXT_DAY_WE='".jsreplace("We")."';\r\n";
$jscode.="TEXT_DAY_TH='".jsreplace("Th")."';\r\n";
$jscode.="TEXT_DAY_FR='".jsreplace("Fr")."';\r\n";
$jscode.="TEXT_DAY_SA='".jsreplace("Sa")."';\r\n";

$jscode.="TEXT_TODAY='".jsreplace("today")."';\r\n";
$jscode .= '
locale_dateformat = "'.$locale_info["LOCALE_IDATE"].'";
locale_datedelimiter = "'.$locale_info["LOCALE_SDATE"].'";
bLoading = false;
TEXT_PLEASE_SELECT = "'.jsreplace("Please select").'"; 
detect = navigator.userAgent.toLowerCase();
checkIt = function(string)
{
	place = detect.indexOf(string) + 1;
	thestring = string;
	return place;
}
ShowHideControls = function ()
{';
foreach ( $inspect_fields as $fld ) {
	if(is_wr_db())
		$fld_type=WRGetFieldType($fld['table'].".".$fld['name']);
	elseif(is_wr_custom())
		$fld_type=WRCustomGetFieldType($fld['table'],$fld['name']);
	else
		$fld_type=GetFieldType($fld['name'],$fld['table']);
	if ( !IsBinaryType($fld_type)) {
		if(!is_wr_custom())
			$jscode .=	'
				document.getElementById("second_'.GoodFieldName($fld['table'])."_".GoodFieldName($fld['name']).'").style.display =  
					document.forms.editform.elements["asearchopt_'.GoodFieldName($fld['table'])."_".GoodFieldName($fld['name']).'_1"].value == "Between" ? "" : "none";
			';
		else
			$jscode .=	'
				document.getElementById("second_'.GoodFieldName($fld['name']).'").style.display =  
					document.forms.editform.elements["asearchopt_'.GoodFieldName($fld['name']).'_1"].value == "Between" ? "" : "none";
			';
	}
}

$jscode .= '
	return false;
}
ResetControls = function() {
	var i;
	e = document.forms[0].elements; 
	for (i=0;i<e.length;i++) 
	{
		if (e[i].name!="type" && e[i].className!="button" && e[i].type!="hidden")
		{
			if(e[i].type=="select-one")
				e[i].selectedIndex=0;
			else if(e[i].type=="select-multiple")
			{
				var j;
				for(j=0;j<e[i].options.length;j++)
					e[i].options[j].selected=false;
			}
			else if(e[i].type=="checkbox" || e[i].type=="radio")
				e[i].checked=false;
			else 
				e[i].value = ""; 
		}
		else if(e[i].name.substr(0,6)=="value_" && e[i].type=="hidden")
			e[i].value = ""; 
	}
	ShowHideControls();	
	return false;
};
OnKeyDown = function(e){
	if(!e) e = window.event; 
	if (e.keyCode == 13){
		e.cancel = true;
		document.forms[0].submit();
	}
};';

if(is_wr_custom())
	$jscode .= '
	$(document).ready(function(){
		$(".text").hide();
		});
	';

$all_checkbox = 'value="and"';
$any_checkbox = 'value="or"';

if(@$where[$sessPrefix."_asearchtype"]=="or") {
	$any_checkbox.=" checked";
} else {
	$all_checkbox.=" checked";
}
$xt->assign("any_checkbox", $any_checkbox);
$xt->assign("all_checkbox", $all_checkbox);

$editformats=array();

if(!is_array($where[$sessPrefix."_asearchopt"]))
	$where[$sessPrefix."_asearchopt"]=array();
if(!is_array($where[$sessPrefix."_asearchnot"]))
	$where[$sessPrefix."_asearchnot"]=array();
if(!is_array($where[$sessPrefix."_asearchfor"]))
	$where[$sessPrefix."_asearchfor"]=array();
if(!is_array($where[$sessPrefix."_asearchfor2"]))
	$where[$sessPrefix."_asearchfor2"]=array();

/////////////////////////////////////////////////////
$aGroupFields=array();
$ngFieldNames=array();
$arr_page_order_fields=array();
$arr_page_order_fields["data"]=array();
$arr_not_group_fields=array();

for ( $i=0; $i < count($xml_array['group_fields'])-1; $i++ ) {
	$aGroupFields[] = $xml_array['tables'][0]."_".$xml_array['group_fields'][$i]['name'];
}


$aTotFields=array();


foreach ( $inspect_fields as $fld ) {
	if(is_wr_db())
		$fld_type=WRGetFieldType($fld['table'].".".$fld['name']);
	elseif(is_wr_custom())
		$fld_type=WRCustomGetFieldType($fld['table'],$fld['name']);
	else
		$fld_type=GetFieldType($fld['name'],$fld['table']);	
	
	if ( !IsBinaryType($fld_type)) {
		$aTotFields[] = $fld['table']."_".$fld['name'];
	}
}

$ngFieldNames = array_diff( $aTotFields, $aGroupFields );


$arr_alias = array();
foreach ( $aGroupFields as $gr_name  ) {
	foreach ( $inspect_fields as $fld ) {
		if ( $gr_name == $fld['table']."_".$fld['name'] && !in_array($fld['table']."_".$fld['name'], $arr_alias)) {
			$arr_alias[] = $fld['table']."_".$fld['name'];
			$arr_page_order_fields["data"][] = $fld;
		}
	}
}


foreach ( $inspect_fields as $fld ) {
	if ( in_array( $fld['table']."_".$fld['name'], $ngFieldNames ) ) {
		$arr_page_order_fields["data"][] = $fld;
		if ($fld["show"] == "true") {
			$arr_not_group_fields[] = $fld;
		}
	}
}

$arr_alias = array();
$arr_unset = array();
foreach ($arr_page_order_fields["data"] as $key => $value){
	$ftable = $arr_page_order_fields["data"][$key]["table"];
	$gtable= GoodFieldName($ftable);
	$fname = $arr_page_order_fields["data"][$key]["name"];

	if(is_wr_db())
	{
		$table_field=$ftable.".".$fname;
		$gname = $gtable."_".GoodFieldName($fname);
	}
	else
	{
		$table_field=$fname;
		$gname = GoodFieldName($fname);
	}
	// remove duplicate search parameters
	if (array_key_exists($gname, $arr_alias) || GetGenericEditFormat($ftable, $fname) == EDIT_FORMAT_DATABASE_IMAGE) {
		$arr_unset[]=$key;
		continue;
	} else {
		$arr_alias[$fname] = 1;	
	}
	//
	$opt = "";
	$not = false;
	//	edit format
	if (GetGenericEditFormat($ftable, $fname) == EDIT_FORMAT_TEXT_AREA
		|| GetGenericEditFormat($ftable, $fname) == EDIT_FORMAT_PASSWORD
		|| GetGenericEditFormat($ftable, $fname) == EDIT_FORMAT_HIDDEN
		|| GetGenericEditFormat($ftable, $fname) == EDIT_FORMAT_READONLY
		|| GetGenericEditFormat($ftable, $fname) == EDIT_FORMAT_FILE)
	{
		$editformats[$table_field]=EDIT_FORMAT_TEXT_FIELD;
	} else {
		$editformats[$table_field]=GetGenericEditFormat($ftable, $fname);
	}

	$editcontrol=array();
	$editcontrol["params"] = array();
	if(@$where[$sessPrefix."_search"]==2)
	{
		$opt = @$where[$sessPrefix."_asearchopt"][$table_field];
		$not = @$where[$sessPrefix."_asearchnot"][$table_field];
		$editcontrol["params"]["value"] = @$where[$sessPrefix."_asearchfor"][$table_field];
	}
	$editcontrol["func"]="xt_buildeditcontrol";
	$editcontrol["params"]["field"]=$table_field;
	$editcontrol["params"]["mode"]="search";
	$editcontrol["params"]["format"]=$editformats[$table_field];
	$editcontrol["params"]["id"]=1;
	$arr_page_order_fields["data"][$key]["editcontrol"] = $editcontrol;
	
	$editcontrol1 = $editcontrol;
	$editcontrol1["params"]["second"]=true;
	$editcontrol1["params"]["fieldNum"]=1;
	if(@$where[$sessPrefix."_search"]==2) {
		$editcontrol1["params"]["value"] = @$where[$sessPrefix."_asearchfor2"][$table_field];
	}
	$arr_page_order_fields["data"][$key]["editcontrol1"] = $editcontrol1;
	if(is_wr_db())
		$arr_page_order_fields["data"][$key]["fieldblock"] = '
			<input type="hidden" name="asearchfield[]" value="'.htmlspecialchars($ftable).".".htmlspecialchars($fname).'">
			<input type="hidden" name="asearchtable[]" value="'.htmlspecialchars($ftable).'">
		';
	else
		$arr_page_order_fields["data"][$key]["fieldblock"] = '
			<input type="hidden" name="asearchfield[]" value="'.htmlspecialchars($fname).'">
			<input type="hidden" name="asearchtable[]" value="'.htmlspecialchars($ftable).'">
		';
	
	$arr_page_order_fields["data"][$key]["notbox"] = ($not) ? 'name="not_'.$gname.'_1" checked' : 'name="not_'.$gname.'_1"';	
	$arr_page_order_fields["data"][$key]["second_id"] = "id=\"second_".$gname."\"";

	$arr_options = GetAdvSearchOptions($ftable, $fname);
	$options = "";	
	foreach ($arr_options as $arr_opt) {
		$options .= '<option value="'.$arr_opt["type"].'" '.(($opt==$arr_opt["type"])?"selected":"").'>'.$arr_opt["label"].'</option>';
	}

	$searchtype = '<select id="SearchOption" name="asearchopt_'.$gname.'_1" size="1" onchange="return ShowHideControls();">';
	$searchtype .= $options;
	$searchtype .= '</select>';
	
	$arr_page_order_fields["data"][$key]["searchtype"] = $searchtype;

}
foreach($arr_unset as $idx=>$val)
{
	unset($arr_page_order_fields["data"][$val]);
}

$xt->assignbyref("page_order_fields", $arr_page_order_fields);

$body=array();
$body["begin"]=$includes;
$jscode.="ShowHideControls();";
$body["end"]='<script type="text/javascript">'.$jscode.'</script>';
$xt->assignbyref("body",$body);

$contents_block=array();
$contents_block["begin"]='<form method="POST" ';
if(postvalue('rname'))
{
	$contents_block["begin"].='action="dreport.php?rname='.htmlspecialchars(rawurlencode(postvalue("rname"))).'" ';
}	
elseif (postvalue('cname'))
{
	$contents_block["begin"].='action="dchart.php?cname='.htmlspecialchars(rawurlencode(postvalue("cname"))).'" ';
}	
$contents_block["begin"] .= 'name="editform"><input type="hidden" id="a" name="a" value="advsearch">';
$contents_block["end"] = '</form>';
$xt->assignbyref("contents_block",$contents_block);

$xt->assign("searchbutton_attrs", 'name="SearchButton" onclick="document.forms.editform.submit();"');
$xt->assign("resetbutton_attrs", 'onclick="return ResetControls();"');
$xt->assign("backbutton_attrs", "onclick=\"document.forms.editform.a.value='return'; document.forms.editform.submit();\"");

$xt->assign("conditions_block",true);
$xt->assign("search_button",true);
$xt->assign("reset_button",true);
$xt->assign("back_button",true);

$xt->assign("dynamic", "true");
if (postvalue('rname')) {
	$xt->assign("rname", postvalue('rname'));
}
if (postvalue('cname')) {
	$xt->assign("cname", postvalue('cname'));
}
	
$templatefile = "dsearch.htm";
if(tableEventExists("BeforeShowSearch",$strTableName)) 
{
	$eventObj =&getEventObject($strTableName);
	$eventObj->BeforeShowSearch($xt,$templatefile);
}

$xt->assign( "reportTitle", htmlspecialchars( $xml_array['settings']['title'] ) );
$xt->display($templatefile);
?>