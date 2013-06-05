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

if (is_wr_project()) 
	include("include/" . GetTableURL( $_SESSION['webreports']['tables'][0] ) . "_variables.php");

$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();

$arr_tables = getReportTablesList();
$group_fields=array();

foreach($arr_tables as $t)
{
	$tfields=WRGetNBFieldsList($t);
	foreach($tfields as $f)
	{
		if(is_wr_db())
			$group_fields[]=$t.".".$f;
		else
			$group_fields[]=$f;
	}
}
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

$sGroupFields = "";
$types = "";

$h_includes = "";
$b_includes = "";

$h_includes .= '
	<link rel="stylesheet" href="include/css/stylesheet.css" type="text/css">
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
	</script>
'."\r\n";

$xt->assign("h_includes", $h_includes);

$b_includes .= '<script type="text/javascript">'."\r\n";
$b_includes .= 'fld_types = new Array();'."\r\n";

if(is_wr_custom())
{
	$fields_type=array();
	$fields_type=WRGetAllCustomFieldType();
}

foreach ($group_fields as $fld) {
	if(!is_wr_custom())
		$type = WRGetFieldType($fld);
	else
		$type = $fields_type[$fld];

	if ( IsNumberType( $type ) ) {
		$b_includes .= "fld_types['" . jsreplace($fld) . "'] = \"number\";"."\r\n";
	} elseif ( IsCharType( $type ) ) {
		$b_includes .= "fld_types['" . jsreplace($fld) . "'] = \"string\";"."\r\n";
	} elseif ( IsDateFieldType( $type ) ) {
		$b_includes .= "fld_types['" . jsreplace($fld) . "'] = \"date\";"."\r\n";
	}
}

if (is_wr_db()) {
	$b_includes .= '
	var NEXT_PAGE_URL = "webreport4.php",
		PREV_PAGE_URL = "webreport2.php";
	'."\r\n";
} else {
	$b_includes .= '
	var NEXT_PAGE_URL = "webreport4.php",
		PREV_PAGE_URL = "webreport0.php";
	'."\r\n";	
}

$b_includes .= '
var timeout	= 200,
	closetimerpicker	= 0,
	closetimer=0,
	timeoutpicker	 = 300,
	relation_stack = [],
	int_types = new Array(
		[0, "Normal"],
		[10, "10s"],
		[50, "50s"],
		[100, "100s"],
		[500, "500s"],
		[1000, "1000s"]
	),
	str_types = new Array(
		[0, "Normal"],
		[1, "1st Letter"],
		[2, "2 Initial Letters"],
		[3, "3 Initial Letters"],
		[4, "4 Initial Letters"],
		[5, "5 Initial Letters"]
	),
	date_types = new Array(
		[0, "Normal"],
		[1, "Year"],
		[2, "Quarter"],
		[3, "Month"],
		[4, "Week"],
		[5, "Day"],
		[6, "Hour"],
		[7, "Minute"]
	);

$(document).ready(function(){
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
		var output = {};
		output.group_fields = [];
		k=0;
		$("tbody > tr.grline", "#tgf").each(function(i){
			val_field=$("select[id^=field]",this).val();
			flag=true;
			$("tbody > tr.grline", "#tgf").each(function(j){
				if(j<i && val_field==$("select[id^=field]",this).val())
					flag=false;
			});
			if ( $("select[id^=field]",this)[0].value != "" && flag) {
				output.group_fields[k] = {
					name        : $("select[id^=field]",this).val(),
					int_type    : $("select[id^=type]",this).val(),
					ss          : $("input[id^=ss]",this).attr("checked").toString(),
					group_order : $("input[id^=go]",this).val(),
					color1      : $("div[id^=picker]",this).attr("color1"),
					color2      : $("div[id^=picker]",this).attr("color2")
				};
				k++;
			}
		});
		output.group_fields.push({
			name : "Summary",
			sps  : $("#sps").attr("checked").toString(),
			sds  : $("#sds").attr("checked").toString(),
			sgs  : $("#sgs").attr("checked").toString()
		});
		
		return JSON.stringify(output);
	}	';
	
$b_includes .= colorPickerMouse();

$b_includes .= '
	$("select[id^=field]").change(function(){
	  for(var i=Number(this.id.replace("field",""));i<4;i++)
	  {
		if($("select[id=field" + this.id.replace("field","") + "]")[0].selectedIndex==0 && this.id.replace("field","")>0)
		{
			$("select[id=field" + (i+1) + "]")[0].disabled=true;
			$("select[id=type" + (i+1) + "]")[0].disabled=true;
			$("select[id=field" + (i+1) + "]")[0].selectedIndex=0;
			$("select[id=type" + (i+1) + "]").html("");
			$("input[id=ss" + (i+1) + "]")[0].disabled = true;
			
			$("div[id=picker" + (i + 1) + "]").css("cursor","default");
			$("div[id=picker" + (i + 1) + "]").parent().next("td").find("img").css("cursor","default");
		}
		else
		{
			if(this.id.replace("field","")<4){
			$("select[id=field" + (Number(this.id.replace("field","")) + 1) + "]")[0].disabled=false;
			$("select[id=type" + (Number(this.id.replace("field","")) + 1) + "]")[0].disabled=false;
			$("input[id=ss" + (Number(this.id.replace("field","")) + 1) + "]")[0].disabled = false;

			$("div[id=picker" + (Number(this.id.replace("field","")) + 1) + "]").css("cursor","pointer");
			$("div[id=picker" + (Number(this.id.replace("field","")) + 1) + "]").parent().next("td").find("img").css("cursor","pointer");
			}
		}
		if($("select[id=field" + this.id.replace("field","") + "]")[0].selectedIndex==0)
		{
			$("div[id=picker" + (i+1) + "]").css("background-color","white");
			$("div[id=picker" + i + "]").css("background-color","white");
		}
	  }
  
		$("select[id=type" + this.id.replace("field","") + "]").html("");
		if ( this.value == "" ) {
			$("input[id=ss" + this.id.replace("field","") + "]")[0].checked = false;
		}

		switch ( fld_types[this.value] ) {
			case "number":
				for (var i=0; i < int_types.length; i++) {
					$("select[id=type" + this.id.replace("field","") + "]").append(\'<option value="\' + int_types[i][0] + \'">\' + int_types[i][1] + \'</option>\');
				}
				$("select[id=type" + this.id.replace("field","") + "]")[0].selectedIndex = 0;
				break;
			case "string":
				for (var i=0; i < str_types.length; i++) {
					$("select[id=type" + this.id.replace("field","") + "]").append("<option value=\"" + str_types[i][0] + "\">" + str_types[i][1] +"</option>");
				}
				$("select[id=type" + this.id.replace("field","") + "]")[0].selectedIndex = 0;
				break;
			case "date":
				for (var i=0; i < date_types.length; i++) {
					$("select[id=type" + this.id.replace("field","") + "]").append(\'<option value="\' + date_types[i][0] + \'">\' + date_types[i][1] + \'</option>\');
				}
				$("select[id=type" + this.id.replace("field","") + "]")[0].selectedIndex = 0;
				break;
			default:
				break;
		}
	});	
	$("#row3")
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

if (count(GetUserGroups()) < 2
	|| isset( $_SESSION['webreports']['settings']['status'] ) && $_SESSION['webreports']['settings']['status'] == "private" ) {
	$b_includes .= '$("td[id=row9]").hide();'."\r\n";
}
if (is_wr_project() || is_wr_custom()) {
	$b_includes .= '$("td[id=row1], td[id=row2]").hide();'."\r\n";
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
			URL = NEXT_PAGE_URL;
		if( this.id == "backbtn" )
			URL = PREV_PAGE_URL;
		if( this.id == "saveasbtn" )
			URL = "webreport8.php?saveas=1";			
		if( this.id.substr(0,3)=="row" && this.id != "row3" )
			URL = "webreport" + this.id.replace("row", "") + ".php";
		if( this.id == "row10" )
			URL = "webreport.php";
		if( this.id == "row11" )
			URL = "menu.php";
		if ( this.id == "row7" )
			URL = "dreport.php?edit=style&rname='.@$_SESSION['webreports']['settings']['name'].'";
			
		var output = collect_input_data();
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
					name: "group_fields",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						if(id=="savebtn")
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

		thisid=this.id;
		if(this.id != "row3" && this.id !="savebtn" && this.id !="previewbtn") {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					name: "group_fields",
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

foreach ($group_fields as $fld) {
   $sGroupFields .= "<option value=\"" . htmlspecialchars($fld) . "\">" . $fld . "</option>"."\r\n";
}

for ( $i=0; $i < 4; $i++ ) {
   $aGroupFields[] = $sGroupFields;
   $aTypes[] = "";
   $aChecked[] = "";
   $aColor1[] = "";
   $aColor2[] = "";
}

$arr = $_SESSION['webreports']['group_fields'];
if ( !empty( $arr ) ){

	for ( $i=0; $i < count($arr)-1; $i++ ) {
		$sGroupFields = "";
		foreach ($group_fields as $fld) {
			$selected = ( $fld == $arr[$i]["name"] ) ? "selected" : "";
			$sGroupFields .= "<option " . $selected . " value=\"" . htmlspecialchars($fld) . "\">" . $fld . "</option>"."\r\n";
		}
		$aGroupFields[$i] = $sGroupFields;
		$aTypes[$i] = $arr[$i]["int_type"];
		$aChecked[$i] = ( $arr[$i]["ss"] == "true" ) ? "checked" : "";
		$aColor1[$i] = $arr[$i]["color1"];
		$aColor2[$i] = $arr[$i]["color2"];
	}

	$akeys = array_keys($arr);
	$summIdx = $akeys[count($akeys)-1];
	$xt->assign("sps", ( $arr[$summIdx]["sps"] == "true" ) ? "checked" : "");
	$xt->assign("sds", ( $arr[$summIdx]["sds"] == "true" ) ? "checked" : "");
	$xt->assign("sgs", ( $arr[$summIdx]["sgs"] == "true" ) ? "checked" : "");
} else {
	$xt->assign("sps", "");
	$xt->assign("sds", "checked");
	$xt->assign("sgs", "");
}

for ( $i=0; $i < count($aGroupFields); $i++ ) {
	$xt->assign("groupFields" . ($i+1), $aGroupFields[$i]);
	$xt->assign("schecked" . ($i+1), $aChecked[$i]);
}

$b_includes .= '<script type="text/javascript">
	$(document).ready(function(){
		$("select[id^=field]").change();'."\r\n";

for ( $i=0; $i < count($aTypes); $i++ ) {
	$b_includes .= '$("select[id=type' . ($i+1) . ']").children().each(function(i){
		if ( $(this).attr("value") == "' . $aTypes[$i] . '" ) {
			setTimeout("$(\'select[id=type' . ($i+1) . ']\')[0].selectedIndex = " + i + ";",500);
		}
	});'."\r\n";
}

for ( $i=0; $i < count($aColor1); $i++ ) {
	if ( $aColor1[$i] != "" ) {
		$b_includes .= '$("div[id=picker' . ($i+1) . ']").css("background-color","#' . $aColor1[$i] . '");'."\r\n";
	}
	$b_includes .= '$("div[id=picker' . ($i+1) . ']")[0].color1 = "' . $aColor1[$i] . '";' . "\r\n";
	$b_includes .= '$("div[id=picker' . ($i+1) . ']")[0].color2 = "' . $aColor2[$i] . '";' . "\r\n";
	$b_includes .= 'if( $("#field'.($i+1).'").attr("disabled") ){' . "\r\n";
	$b_includes .= '$("div[id=picker' . ($i+1) . ']").css("cursor","default");' . "\r\n";
	$b_includes .= '$("div[id=picker' . ($i+1) . ']").parent().next("td").find("img").css("cursor","default");' . "\r\n";
	$b_includes .= '}' . "\r\n";

}

$b_includes .= '});
</script>';
$xt->assign("report_name_preview",$_SESSION['webreports']['settings']['name']);
$xt->assign("b_includes", $b_includes);

$templatefile = "webreport3.htm";
$xt->display($templatefile);
?>