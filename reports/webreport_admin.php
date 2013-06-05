<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

include("include/reportfunctions.php");

if(!isWRAdmin())
{
	$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
	header("Location: webreport.php");
	return;
}

$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();

$tables_admin_db = WRGetTableListAdmin("db");
$tables_admin_project = WRGetTableListAdmin("project");
$tables_admin_custom = WRGetTableListAdmin("custom");

$arr_tables_db = DBGetTablesList();
$arr_tables_project = GetTablesListReport();
$arr_tables_custom = GetTablesListCustom();

$groups = array();
if(!$wr_is_standalone)
	$arr_UserGroups = GetUserGroups();
else
{
	$arr_UserGroups = array();
	foreach(GetUserGroups() as $idx=>$value)
		if($value[0]!="Guest")
			$arr_UserGroups[]=$value;
}

$group_list="";
$groupSelected="";

$wr_user=postvalue("username");

if($wr_is_standalone)
{
	if(postvalue("editid1"))
	{
		$rs=db_query("select ".AddFieldWrappers("username")." from ".AddTableWrappers("webreport_users")." where ".AddFieldWrappers("id")."=".postvalue("editid1"),$conn);
		$data=db_fetch_numarray($rs);
			if($data)
				$wr_user=$data[0];
	}
}

if(count($arr_UserGroups))
{
	usort($arr_UserGroups,"sortUserGroup");
	$groups=$arr_UserGroups;
	$i=0;

	if(!$wr_is_standalone)
		$xt->assign("group_header","User Groups");
	else
		$xt->assign("group_header","Users");
	$group_list="<select name=select_group_list id=select_group_list size=2 style='width:150px;";
	$group_list.="'>";
	foreach($arr_UserGroups as $val)
	{
		$sel="";
		if($wr_user=="" && $i==0 || $wr_user==$val[0])
		{
			$sel=" selected";
			$groupSelected=$val[0];
		}
		$group_list.="<option value=\"".$i."\"".$sel.">".htmlspecialchars($val[1])."</option>";
		$i++;
	}
	$group_list.="</select>";
}
else
{
	$groups[]=array(0=>"",1=>"");
	$group_list.="<select name=select_group_list id=select_group_list size=0 style='display:none'><option value=\"0\" selected>0</option></select>";
}


$table_list="";
$i=0;

foreach($groups as $group_name)
{
	$table_list.="<span id=\"group_db_".$i."\" ";
	if($groupSelected!=$group_name[0])
		$table_list.="style='display:none;' ";
	$table_list.=">\n";
	foreach($arr_tables_db as $ind=>$table)
	{
		$chbox="";
		foreach($tables_admin_db as $val)
			if($table==$val["tablename"] && $group_name[0]==$val["group"])
				$chbox=" checked";
			
		$table_list.="<input type=\"checkbox\" dbname=\"db\" groupname=\"".htmlspecialchars($group_name[0])."\" id=\"adm_tables_".$ind."_".$i."_db\" value=\"".htmlspecialchars($table)."\" ".$chbox.">&nbsp;&nbsp;".$table."<br>\n";
	}
	$table_list.="</span>\n";
	
	$table_list.="<span id=\"group_project_".$i."\" ";
	$table_list.="style='display:none;' ";
	$table_list.=">\n";
	foreach($arr_tables_project as $ind=>$table)
	{
		$chbox="";
		foreach($tables_admin_project as $val)
			if($table==$val["tablename"] && $group_name[0]==$val["group"])
				$chbox=" checked";
			
		$table_list.="<input type=\"checkbox\" dbname=\"project\" groupname=\"".htmlspecialchars($group_name[0])."\" id=\"adm_tables_".$ind."_".$i."_project\" value=\"".htmlspecialchars($table)."\" ".$chbox.">&nbsp;&nbsp;".$table."<br>\n";
	}
	$table_list.="</span>\n";
	
	$table_list.="<span id=\"group_custom_".$i."\" ";
	$table_list.="style='display:none;' ";
	$table_list.=">\n";
	foreach($arr_tables_custom as $ind=>$table)
	{
		$chbox="";
		foreach($tables_admin_custom as $val)
			if($table==$val["tablename"] && $group_name[0]==$val["group"])
				$chbox=" checked";
			$custom_bold1="";
			$custom_bold2="";
			if(postvalue("queryname")==$table)
			{
				$custom_bold1="<b>";
				$custom_bold2="</b>";
			}
		$table_list.="<input type=\"checkbox\" dbname=\"custom\" groupname=\"".htmlspecialchars($group_name[0])."\" id=\"adm_tables_".$ind."_".$i."_custom\" value=\"".htmlspecialchars($table)."\" ".$chbox.">&nbsp;&nbsp;".$custom_bold1.$table.$custom_bold2."<br>\n";
	}
	$table_list.="</span>\n";
	$i++;
}

$b_includes = "";
$h_includes = "";

$h_includes .= '
	<link rel="stylesheet" href="include/css/jquery-ui.css" type="text/css">
	<link rel="stylesheet" href="include/css/stylesheet.css" type="text/css">
	<link rel="stylesheet" href="include/css/dstyle.css" type="text/css">
	<link rel="stylesheet" href="include/style.css" type="text/css">
	
	<script type="text/javascript" src="include/js/jquery.min.js"></script>
	<script type="text/javascript" src="include/js/jquery.dimensions.pack.js"></script>
    <script type="text/javascript" src="include/js/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/json.js"></script>
	<script type="text/javascript" src="include/jsfunctions.js"></script>
'."\r\n";

$xt->assign("h_includes", $h_includes);

$b_includes .= '
<style type="text/css">
	#my_tabs .ui-corner-top 
	{
		-moz-border-radius-topleft:3px;
		-moz-border-radius-topright:3px;
	}
	#my_tabs .ui-tabs, #my_tabs .ui-tabs-nav, #my_tabs li 
	{
		float:left;
		left:0px;
		margin:0 0.2em -1px 0;
		position:relative;
	}
	#my_tabs .ui-state-default
	{
		padding-left:15;
		padding-right:15;
		padding-left:15;
		padding-right:15;
		border-bottom-width:0 !important;
		border-bottom: none;
		border: 1px solid white;
		background:url("include/img/but_middle_2.gif") repeat-x scroll 50% 50% #E6E6E6;
		font-weight:normal;
		outline:medium none;
	}
	#my_tabs .ui-tabs-selected
	{
		padding-left:15;
		padding-right:15;
		padding-left:15;
		padding-right:15;
		border: 1px solid white;
		border-bottom: none;
		background:url("include/img/but_middle_1.gif") repeat-x scroll 50% 50% #E6E6E6;
		font-weight:normal;
		outline:medium none;
	}
	#my_tabs .ui-widget,#my_tabs .ui-widget-content,#my_tabs .ui-corner-all 
	{
		background:none;
		color:black;
		border:0px solid black;
	}
</style>

<script type="text/javascript">'."\r\n";
  
$b_includes .= '
$(document).ready(function(){';

if($wr_is_standalone)
	$b_includes .= '$("#li_project").hide();';

$b_includes .= alertDialog();
$b_includes .= '
	li_selected="db";
	$(function() {
		$("#radio_select_table").tabs();
	});
	
	$("#backbtn").click(function(){
		window.location = "webreport.php";
		return;
	});
	$("#select_group_list").click(function(){
		db_type=li_selected;
		$("span[id^=group_"+db_type+"]").css("display","none");
		gr=$("select[@id=select_group_list] option:selected").val();
		$("#group_"+db_type+"_"+gr).css("display","");
		check_all_box();
	});
	
	$("#saveexitbtn").click(function(){
		i=0;
		output = {};
		$("input[id^=adm_tables]").each(function()
		{
			if(this.checked)
			{
				output[i] = {};
				output[i]["table"]=this.value;
				output[i]["group"]=$(this).attr("groupname");
				output[i]["db_type"]=$(this).attr("dbname");
				i++;
			}
		}
		);
		output=JSON.stringify(output);
		$.ajax({
			type: "POST",
			url: "save-admin.php",
			data: {
				name: "admin_table",
				output: output,
				rnd: (new Date().getTime())
			},
			success: function(msg)
			{
				if ( msg == "OK" ) 
				{
					window.location = "webreport.php";
					return false;
				}
			}
		});
	});
	
	$("#ch_all").click(function(){
		ngroup=$("select[@id=select_group_list] option:selected").val();
		if($(this).attr("checked"))
			$("input[id$=_"+ngroup+"_"+li_selected+"]").attr("checked",true);
		else
			$("input[id$=_"+ngroup+"_"+li_selected+"]").attr("checked",false);
	});
	
	$("input[id^=adm_tables_]").click(function(){
		check_all_box();
	});
	
	$("#radio_db,#radio_project,#radio_custom").click(function(){
		document.getElementById("select_group_list").style.height="auto";
		gr=$("select[@id=select_group_list] option:selected").val();
		if($(this).attr("id")=="radio_db")
		{
			$("span[id^=group_db_"+gr+"]").css("display","");
			$("span[id^=group_project]").css("display","none");
			$("span[id^=group_custom]").css("display","none");
			h_select=document.getElementById("group_db_"+gr).offsetHeight;
			h_group=document.getElementById("select_group_list").offsetHeight;
			if(h_group<=h_select)
				document.getElementById("select_group_list").style.height=h_select;
			else
				document.getElementById("select_group_list").style.height=h_group;
			li_selected="db";
		}
		else if($(this).attr("id")=="radio_project")
		{
			$("span[id^=group_db]").css("display","none");
			$("span[id^=group_custom]").css("display","none");
			$("span[id^=group_project_"+gr+"]").css("display","");
			h_select=document.getElementById("group_project_"+gr).offsetHeight;
			h_group=document.getElementById("select_group_list").offsetHeight;
			if(h_group<=h_select)
				document.getElementById("select_group_list").style.height=h_select;
			else
				document.getElementById("select_group_list").style.height=h_group;
			li_selected="project";
		}
		else
		{
			$("span[id^=group_db]").css("display","none");
			$("span[id^=group_project]").css("display","none");
			$("span[id^=group_custom_"+gr+"]").css("display","");
			h_select=document.getElementById("group_custom_"+gr).offsetHeight;
			h_group=document.getElementById("select_group_list").offsetHeight;
			if(h_group<=h_select)
				document.getElementById("select_group_list").style.height=h_select;
			else
				document.getElementById("select_group_list").style.height=h_group;
			li_selected="custom";
		}
		check_all_box();
	});
	
	function check_all_box(){
		check_all=true;
		ngroup=$("select[@id=select_group_list] option:selected").val();
		$("input[id$=_"+ngroup+"_"+li_selected+"]").each(function(i){
			if(!$(this).attr("checked"))
				check_all=false;
		});
		$("#ch_all").attr("checked",check_all);
	}
	check_all_box();
	';
	
	if(!postvalue("username"))
		$b_includes.= '$("#radio_db").click();';
	else
		$b_includes.= '$("#radio_custom").click();';
		
$b_includes.= '
	h_select=document.getElementById("group_db_"+$("#select_group_list").get(0).selectedIndex).offsetHeight;
	h_group=document.getElementById("select_group_list").offsetHeight;
	if(h_group<=h_select)
		document.getElementById("select_group_list").style.height=h_select;';
	if(!count($arr_tables_db))
		$b_includes.= '$("#li_db").hide();';
	if(!count($arr_tables_project))
		$b_includes.= '$("#li_project").hide();';
	if(!count($arr_tables_custom))
		$b_includes.= '$("#li_custom").hide();';
$b_includes.= '
});
</script>'."\r\n";


$xt->assign("b_includes", $b_includes);



if (@$_SESSION['webreports']['settings']['title'] != "") {
	$xt->assign("report_title",", Title: ".@$_SESSION['webreports']['settings']['title']);
} else {
	$xt->assign("report_title","");
}

$xt->assign("table_list",$table_list);
$xt->assign("group_list",$group_list);
$templatefile = "webreport_admin.htm";
$xt->display($templatefile);
?>
