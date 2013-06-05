<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

include("include/reportfunctions.php");

	if(!@$_SESSION["UserID"])
	{
		$_SESSION["MyURL"]="webreport.php";
		header("Location: login.php?message=expired");
		return;
	}

$conn=db_connect();
$arr_tables = DBGetTablesListByGroup("db");

include('include/xtempl.php');
$xt = new Xtempl();

if (@$_SESSION['webreports']['settings']['title'] != "") {
	$title=@$_SESSION['webreports']['settings']['title'];
	if(strlen($title)>25)
		$title=substr($title,25)."...";
	$xt->assign("report_title",", Title: ".$title);
} else {
	$xt->assign("report_title","");
}
if (@$_SESSION['webreports']['tables'][0] != "") {
	$stable=@$_SESSION['webreports']['tables'][0];
	if(strlen($stable)>25)
		$stable=substr($stable,25)."...";

	$xt->assign("report_table",", Table: ".$stable);
} else {
	$xt->assign("report_table","");
}

$xt->assign("b_is_report_save",($_SESSION['webreports']['tmp_active'] != "x"));	
$xt->assign("b_is_report_name",($_SESSION['webreports']['settings']['name'] != ""));
$xt->assign("report_name",$_SESSION['webreports']['settings']['name']);

$h_includes = "";
$b_includes = "";

$h_includes .= '
	<link rel="stylesheet" href="include/css/dstyle.css" type="text/css">
	<link rel="stylesheet" href="include/style.css" type="text/css">
	<link rel="stylesheet" href="include/css/jquery.fancybox.css" type="text/css" media="screen">
	<link rel="stylesheet" href="include/css/jquery-ui.css" type="text/css" media="screen">
	
	<script type="text/javascript" src="include/js/jquery.min.js"></script>
	<script type="text/javascript" src="include/js/jquery.dimensions.pack.js"></script>
	<script type="text/javascript" src="include/js/jquery.easing.js"></script>
    <script type="text/javascript" src="include/js/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="include/js/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/json.js"></script>
	<script type="text/javascript" src="include/jsfunctions.js"></script>
'."\r\n";

$xt->assign("h_includes", $h_includes);

$table_selected = @$_SESSION['webreports']['tables'][0];
$xt->assign("table_selected", htmlspecialchars($table_selected));

$fields_table_selected = "";
$arr_fields = WRGetFieldsList($table_selected);
foreach ($arr_fields as $fld_name) {
	$fields_table_selected .= $fld_name.", ";
}
$fields_table_selected = substr($fields_table_selected,0,strlen($fields_table_selected)-2);
$xt->assign("fields_table_selected", $fields_table_selected);

$arr_rel = $_SESSION['webreports']['table_relations'];
if ( !empty( $arr_rel ) )
	$arr_relations = array_slice(explode("@END@", $arr_rel["relations"]), 0, -1);


$tables = "";
$b_includes .= "
<script type='text/javascript'>
var left_wrapper = '".$strLeftWrapper."';
var right_wrapper = '".$strRightWrapper."';
var arr_tables_fields = new Array();"."\n";
for ($i=0; $i < count($arr_tables); $i++ ) {
	$t = $arr_tables[$i];
	
	if($t!=$table_selected)
	{
		$flag=0;
		if (!empty($arr_rel))
		{
			foreach ($arr_relations as $rel) 
			{
				$arr_parts = explode("@SEP@", $rel);
				if($arr_parts[1]==$t)
					$flag=1;
			}
		}
		if($flag==0)
			$tables .= '<option value="'.htmlspecialchars($t).'">'.$t.'</option>';
	
	}
	$arr_fields = array();
	$arr_fields = WRGetNBFieldsList($t);
	$b_includes .= "arr_tables_fields['".jsreplace($t)."'] = new Array();"."\n";
	for ($j=0; $j < count($arr_fields); $j++) {
		$b_includes .= "arr_tables_fields['".jsreplace($t)."'][".$j."] = '".jsreplace($arr_fields[$j])."';"."\n";
	}
}
$b_includes .= '</script>';
$xt->assign("tables", $tables);

$b_includes .= '
<script type="text/javascript">
var timeout	= 200,
	closetimer	= 0,
	relation_stack = [],
	table_stack = [],
	rel = [];

$(document).ready(function(){
	
	$("a#sql_query").fancybox({
		"hideOnContentClick": false,
		"frameWidth" : 800,
		"frameHeight" : 550,
		"overlayShow": true,
		"hideOnContentClick" : true,
		"easingIn" : "easeOutBack",
		"easingOut" : "easeInBack"
	});
	if($.browser.msie)
		$("a#preview").fancybox({
			"hideOnContentClick": false,
			"frameWidth" : 890,
			"frameHeight" : 730,
			"overlayShow": true,
			"hideOnContentClick" : true,
			"easingIn" : "easeOutBack",
			"easingOut" : "easeInBack"
		});
	else
		$("a#preview").fancybox({
			"hideOnContentClick": false,
			"frameWidth" : 820,
			"frameHeight" : 660,
			"overlayShow": true,
			"hideOnContentClick" : true,
			"easingIn" : "easeOutBack",
			"easingOut" : "easeInBack"
		});
	';
	$b_includes .= alertDialog();
	$b_includes .= '
	function collect_input_data() {
		var s = "", t = "", r=[];
		
		for (i in relation_stack) {
			if (relation_stack[i] != undefined) {
				s += i + "@SEP@" + relation_stack[i] + "@END@";
			}
		}
		for (i in table_stack) {
			if (table_stack[i] != undefined) {
				t += i + ",";
			}
		}		
		
		$("#rel_list").children().each(function(){
			r[r.length]=this.rel;
		});
		
		var output = {
			table_relations : {
				left_table  : $("#left_tables").val(),
				right_table : $("#right_tables").val(),
				left_field  : $("#left_fields_1").val(),
				right_field : $("#right_fields_1").val(),
				join_type   : $("#join_select").val(),
				relations   : s,
				join_tables : t,
				relat: r
			}
		};
	
		return JSON.stringify(output);
	}	
	
	$("#sqlbtn").click(function(){
		
		var output = collect_input_data();
		
		$.ajax({
			type: "POST",
			url: "save-state.php",
			data: {
				name: "table_relations",
				web: "webreports",
				str_xml: output,
				rnd: (new Date().getTime())
			},
			success: function(msg){
				if ( msg == "OK" ) {
					$("#sql_query").click();
				} else {
					$("#alert").html("<p>"+msg+"</p>").dialog("open");
				}
			}
		});
	});
		
	$("#addrel_btn").click(function(){
		var rel_txt,
			tbl_left = $("#left_tables").val(),
			tbl_right = $("#right_tables").val();
		
		if ( tbl_left == -1 ) {
			$("#alert").html("<p>Select left table</p>").dialog("open");
			return;
		}
		if ( tbl_right == -1 ) {
			$("#alert").html("<p>Select right table</p>").dialog("open");
			return;
		}
		// add check if field is selected
		
		rel_txt = $("#join_select").val() + left_wrapper + tbl_right + right_wrapper + " ON ";
		$(".link_fields").each(function(){
			if ($("select[id^=left]",this).val() != null && $("select[id^=left]",this).val() != "-1"
				&& $("select[id^=right]",this).val() != null && $("select[id^=right]",this).val() != "-1")
			{
				rel_txt += left_wrapper + tbl_left + right_wrapper + ".";
				rel_txt += left_wrapper + $("select[id^=left]",this).val() + right_wrapper + " = ";
				rel_txt += left_wrapper + tbl_right + right_wrapper + ".";
				rel_txt += left_wrapper + $("select[id^=right]",this).val() + right_wrapper;
				rel_txt += " AND "
			}
		});
		// if ( rel_txt != $("#join_select").val() ) {
		rel_txt = rel_txt.substr(0,rel_txt.length-5)
		// }
	
		if (relation_stack[rel_txt] != undefined) {
			$("#alert").html("<p>The relation with selected parameters already exists</p>").dialog("open");
			return;
		}		
		
		rel={"left_table":tbl_left,
				 "right_table":tbl_right,
				 "left_fields":[],
				 "right_fields":[],
				 "rel_type":$("#join_select").val()};
		
		$(".link_fields").each(function(){
			if ($("select[id^=left]",this).val() != null && $("select[id^=left]",this).val() != "-1"
					&& $("select[id^=right]",this).val() != null && $("select[id^=right]",this).val() != "-1")
			{
				rel.left_fields[rel.left_fields.length]=$("select[id^=left]",this).val();
				rel.right_fields[rel.right_fields.length]=$("select[id^=right]",this).val();
			}
		});
		
		relation_stack[rel_txt] = tbl_right;
		

		option = new Option(rel_txt,tbl_right);
		option.rel=rel;
		var objSel=document.getElementById("rel_list");
		objSel.options[objSel.length]=option;


		if (table_stack[tbl_right] == undefined) {
			table_stack[tbl_right] = 1;
			$("#left_tables").append("<option value=\""+tbl_right+"\">"+tbl_right+"</option>");			
			$("#right_tables option:selected").remove();
			$("select[id^=right_fields_]").empty();
		} else {
			table_stack[tbl_right] = table_stack[tbl_right] + 1;
		}
	});
	
	$("#remrel_btn").click(function(){
		var rel, val, db, 
		tbl_left = $("#left_tables").val(),
		tbl_right = $("#right_tables").val();
		if ($("#rel_list").children(":selected").length > 0) {
			rel = $("#rel_list").children(":selected");
			val = $(rel).text();
			db = $(rel).val();
			if(db!="")
			{
				$("#right_tables").append("<option value=\""+db+"\">"+db+"</option>");			
				$("#left_tables option[value="+db+"]").remove();

			}
			if (table_stack[relation_stack[val]] == 1) {
				table_stack[relation_stack[val]] = undefined;
			} else {
				table_stack[relation_stack[val]] = table_stack[relation_stack[val]] - 1;
			}
			relation_stack[val] = undefined;
			$(rel).remove();
		} else {
			$("#alert").html("<p>Select relation you want to remove</p>").dialog("open");
			return;
		}
	});
	
	$(".table_fields").change(function(){
		var s = "",
			t = $(this).val(),
			id = this.id.replace("_tables","");
			
		if (t == "-1") {
			$("#"+id+"_fields_1").html("");
			return;
		}
		var theSel=document.getElementById(id+"_fields_1");
		$("#"+id+"_fields_1").empty();
		for (var i=0; i < arr_tables_fields[t].length; i++) {
			theSel.options[theSel.length] = new Option(arr_tables_fields[t][i], arr_tables_fields[t][i]);
		}
		$("#"+id+"_fields_1").get(0).disabled = false;
	});
	
	$(".fld_names").change(function(){
		live_change(this);
	});	
	
	$("#row1")
		.css("cursor", "default")
		.css("font-weight", "bold");

	$("td[id^=row]").mouseover(function(){
		for(var i=0; i<=11; i++) {
			if(i == this.id.replace("row", "")) {
				$("td[id=row" + i + "]").css("background-color","#92BEEB");
			}
			else {
				$("td[id=row" + i + "]").css("background-color","#F4F7FB");
			}
		}
	});
 
'."\r\n";

$b_includes .= JumpTo();

if (count(GetUserGroups()) < 2 || isset( $_SESSION['webreports']['settings']['status'] )
	&& $_SESSION['webreports']['settings']['status'] == "private" ) {
	$b_includes .= '$("td[id=row9]").hide();'."\r\n";
}
if($wr_is_standalone)
	$b_includes .= '$("td[id=row11]").hide();'."\n";

if ($_SESSION['webreports']['settings']['title'] == "") {
	$b_includes .= '
		for (var i=2; i<=9; i++){
			$("td[id=row" + i + "]").hide();
		};
	'."\r\n";
}
    
$b_includes .= '
	$("#nextbtn, #backbtn, td[id^=row], #savebtn, #saveasbtn, #previewbtn").click(function(){
		var URL = "webreport.php";
		if( this.id == "nextbtn" )
			URL = "webreport2.php";
		if( this.id == "backbtn" )
			URL = "webreport0.php";
		if( this.id == "saveasbtn" )
			URL = "webreport8.php?saveas=1";			
		if( this.id.substr(0,3)=="row" && this.id != "row1" )
			URL = "webreport" + this.id.replace("row", "") + ".php";
		if( this.id == "row10" )
			URL = "webreport.php";
		if( this.id == "row11" )
			URL = "menu.php";
		if ( this.id == "row7" )
			URL = "dreport.php?edit=style&rname='.@$_SESSION['webreports']['settings']['name'].'";			
	
		var output = collect_input_data();
		thisid=this.id;
		
		var_save=0;
		if( this.id == "savebtn")
			var_save=1;
		if( this.id == "savebtn" || this.id == "previewbtn" ) {
			id=this.id;
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					save: var_save,
					web: "webreports",
					name: "table_relations",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						if( id == "savebtn" )
						{
							$("#alert")
								.html("<p>Report Saved</p>")
								.dialog("option", "close", function(){
									window.location = "webreport.php";
								})
								.dialog("open");
						}
						else
							$("#preview").click();
					} else {
						$("#alert").html("<p>Some problems appear during saving</p>").dialog("open");
					}
				},
				error: function() {
					$("#alert").html("<p>Some problems appear during saving</p>").dialog("open");
				}
			});
		}
	
		if(this.id != "row1" && this.id !="savebtn" && this.id !="previewbtn") {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					name: "table_relations",
					web: "webreports",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						window.location = URL;
					} else {
						$("#alert").html("<p>"+msg+"</p>").dialog("open");
						if( thisid == "row10" || thisid == "row11" )
							window.location=URL;	

					}
				}
			});
		}
	});
});
</script>'."\r\n";

$arr = $_SESSION['webreports']['table_relations'];
	
$b_includes .= "<script type='text/javascript'>
	$(document).ready(function(){";
if ( !empty( $arr ) )
{
	$arr_relations = array_slice(explode("@END@", $arr["relations"]), 0, -1);
	$b_includes .="
			$('#left_tables').val('".jsreplace($arr["left_table"])."');
			if ($('#left_tables').val() != '-1') 
			{
				$('#left_tables').change();				
			}
			$('#right_tables').val('".jsreplace($arr["right_table"])."');
			if ($('#right_tables').val() != '-1') 
			{
				$('#right_tables').change();				
			}			
			$('#left_fields_1').val('".jsreplace($arr["left_field"])."');
			$('#right_fields_1').val('".jsreplace($arr["right_field"])."');
			$('#right_fields_1').change();
			$('#join_select').val('".jsreplace($arr["join_type"])."');"."\r\n";
			$c=0;
			if(!is_array($arr["relat"]))
				$arr_relations=array();
			foreach ($arr_relations as $rel) 
			{
				$arr_parts = explode("@SEP@", $rel);
				
				$b_includes .= "
				relation_stack['".jsreplace($arr_parts[0])."'] = '".jsreplace($arr_parts[1])."';";
			
				foreach($arr["relat"] as $key=>$value)
				{
					if($key==$c)
					{
						$b_includes .= "var rel={'left_table':'".$value["left_table"]."',
										'right_table':'".$value["right_table"]."',
										'left_fields':[],
										'right_fields':[],
										'rel_type':'".$value["rel_type"]."'};"."\r\n";
						foreach($value["left_fields"] as $k=>$val)
							$b_includes .= "rel.left_fields[".$k."]='".jsreplace($val)."';";
						foreach($value["right_fields"] as $k=>$val)
							$b_includes .= "rel.right_fields[".$k."]='".jsreplace($val)."';";
					}	
				}
				
				$b_includes .= "option = new Option('".jsreplace($arr_parts[0])."','".jsreplace($arr_parts[1])."');
				option.rel=rel;
				var objSel=document.getElementById('rel_list');
				objSel.options[objSel.length]=option;
				
				if (table_stack['".jsreplace($arr_parts[1])."'] == undefined) 
				{
					table_stack['".jsreplace($arr_parts[1])."'] = 1;
					$('#left_tables').append(\"<option value='".jsreplace($arr_parts[1])."'>".jsreplace($arr_parts[1])."</option>\");
				} 
				else 
				{
					table_stack['".jsreplace($arr_parts[1])."'] = table_stack['".jsreplace($arr_parts[1])."'] + 1;
				}		
				"."\r\n";	
				$c++;
			}
}
else
{
	$b_includes .='$("#left_tables").val($("#left_tables")[0].options[1].value);
		$("#left_tables").change();';
}
$b_includes .= '});

function live_change(th)
{
		var id = th.id.substr(th.id.length-1),
			tr  = $(th).parent().parent(),
			new_id = 0,
			new_tr = "";

		if ( $("td > select",tr).eq(0).val() != null && $("td > select",tr).eq(0).val() != "-1"
			&& $("td > select",tr).eq(1).val() != null && $("td > select",tr).eq(1).val() != "-1") {
			new_id = parseInt(id)+1;
			if ($("#left_fields_"+new_id).length > 0) {
				return;
			}
			new_tr += "<tr class=\"link_fields\">";
			new_tr += "<td/>";
			new_tr += "<td><select class=\"fld_names\" id=\"left_fields_"+new_id+"\" name=\"left_fields_"+new_id+"\" style=\"width:150px\" onchange=\"live_change(this);\">";
			if (new_id == 2) {
				new_tr += "<option value=\"-1\"></option>";
			}
			new_tr += $("td > select",tr).eq(0).html();
			new_tr += "</select></td>";
			new_tr += "<td/>";
			new_tr += "<td><select class=\"fld_names\" id=\"right_fields_"+new_id+"\" name=\"right_fields_"+new_id+"\" style=\"width:150px\" onchange=\"live_change(this);\">";
			if (new_id == 2) {
				new_tr += "<option value=\"-1\"></option>";
			}
			new_tr += $("td > select",tr).eq(1).html();
			new_tr += "</select></td>";
			new_tr += "</tr>";
			$(new_tr).insertAfter(tr);
			$("#left_fields_"+new_id).get(0).selectedIndex=0;
			$("#right_fields_"+new_id).get(0).selectedIndex=0;
		}
}

</script>'."\r\n";
$xt->assign("report_name_preview",$_SESSION['webreports']['settings']['name']);
$xt->assign("b_includes", $b_includes);

$templatefile = "webreport1.htm";
$xt->display($templatefile);
?>