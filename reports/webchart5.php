<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
include("include/dbcommon.php");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

include("include/reportfunctions.php");
if(is_wr_project())
	include("include/" . GetTableURL( $_SESSION['webcharts']['tables'][0] ) . "_variables.php");

	if(!@$_SESSION["UserID"])
	{
		$_SESSION["MyURL"]=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
		header("Location: login.php?message=expired");
		return;
	}

$conn=db_connect();

include('include/xtempl.php');
$xt = new Xtempl();

if (@$_SESSION['webcharts']['settings']['title'] != "") {
	$title=@$_SESSION['webcharts']['settings']['title'];
	if(strlen($title)>25)
		$title=substr($title,25)."...";
	$xt->assign("chart_title",", Title: ".$title);
} else {
	$xt->assign("chart_title","");
}
if (@$_SESSION['webcharts']['tables'][0] != "") {
	$stable=@$_SESSION['webcharts']['tables'][0];
	if(strlen($stable)>25)
		$stable=substr($stable,25)."...";

	$xt->assign("chart_table",", Table: ".$stable);
} else {
	$xt->assign("chart_table","");
}
$xt->assign("b_is_chart_saveas",($_SESSION['webcharts']['tmp_active'] != "x"));	
$xt->assign("b_is_chart_save",($_SESSION['webcharts']['settings']['name'] != ""));
$xt->assign("chart_name",$_SESSION['webcharts']['settings']['name']);

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
	<script type="text/javascript" src="include/jsfunctions.js"></script>

'."\r\n";

$xt->assign("h_includes", $h_includes);

$b_includes .= '
<script type="text/javascript">
var timeout	= 200,
	closetimer	= 0,
	closetimerpicker = 0,
	timeoutpicker = 300,
	div_id=0;

$(document).ready(function(){
	';
	$b_includes .= alertDialog();
	$b_includes .= '
	
	function collect_input_data() {
		var output = {
			appearance : {
				slegend : $("input[id=slegend]").attr("checked").toString(),
				sgrid : $("input[id=sgrid]").attr("checked").toString(),
				sname : $("input[id=sname]").attr("checked").toString(),
				sval : $("input[id=sval]").attr("checked").toString(),
				sanim : $("input[id=sanim]").attr("checked").toString(),
				scur : $("input[id=scur]").attr("checked").toString(),
				sstacked : $("input[id=sstacked]").attr("checked").toString(),
				saxes : $("input[id=saxes]").attr("checked").toString(),
				slog : $("input[id=slog]").attr("checked").toString(),
				dec : $("input[id=dec]").val(),
				head : $("input[id=head]").val(),
				foot : $("input[id=foot]").val(),
				linestyle : $("select[id=linestyle]").val(),
				gaugestyle : $("select[id=gaugestyle]").val(),
				accumulstyle : $("select[id=accumulstyle]").val(),
				aqua : $("select[id=aqua]").val(),
				cview : $("select[id=cview]").val(),
				accumulinvert : $("input[id=accumulinvert]").attr("checked").toString(),
				is3d : $("input[id=is3d]").attr("checked").toString(),
				isstacked : $("input[id=isstacked]").attr("checked").toString(),
				autoupdate : $("input[id=autoupdate]").attr("checked").toString(),
				cscroll : $("input[id=cscroll]").attr("checked").toString(),
				update_interval : $("input[id=update_interval]").val(),
				maxbarscroll : $("input[id=maxbarscroll]").val()
			}
		};
		for ( var i = 5; i <= 14; i++ ) {
			output.appearance["color"+i+"1"] = $("div[id=picker"+i+"]").attr("color1");
			output.appearance["color"+i+"2"] = $("div[id=picker"+i+"]").attr("color2");
		}		
		
		return JSON.stringify(output);		
	}

	$("#row5")
		.css("cursor", "default")
		.css("font-weight", "bold");

	$("td[id^=row]").mouseover(function(){
		for(var i=0; i<=9; i++) {
			if(i == this.id.replace("row", "")) {
				$("td[id=row" + i + "]").css("background-color","#92BEEB");
			}
			else {
				$("td[id=row" + i + "]").css("background-color","#F4F7FB");
			}
		}
	});
	
	$.each([5,6,7,8,9,10,11,12,13,14],function(i,n){
		$("#picker"+n).css("background-color", "#FFFFFF");
		$("#picker"+n)[0].color1 = "";
		$("#picker"+n)[0].color2 = "";                      
	});
	
';
	
$b_includes .= colorPickerMouse();

$b_includes .= JumpTo();

if (count(GetUserGroups()) < 2 || isset( $_SESSION['webcharts']['settings']['status'] )
	&& $_SESSION['webcharts']['settings']['status'] == "private" ) {
	$b_includes .= '$("td[id=row7]").hide();'."\r\n";
}
if (is_wr_project() || is_wr_custom()) {
	$b_includes .= '$("td[id=row1], td[id=row2]").hide();'."\r\n";
}
if($wr_is_standalone)
	$b_includes .= '$("td[id=row9]").hide();'."\n";
	
if ($_SESSION['webcharts']['settings']['title'] == "") {
	$b_includes .= '
		for (var i=2; i<=7; i++){
			$("td[id=row" + i + "]").hide();
		};
	'."\r\n";
}
    
$b_includes .= '
	var activeXDetectRules = [
            {"name":"ShockwaveFlash.ShockwaveFlash.7"},
            {"name":"ShockwaveFlash.ShockwaveFlash.6"},
            {"name":"ShockwaveFlash.ShockwaveFlash"}
    ];
	var getActiveXObject = function(name){
        var obj = -1;
        try{
            obj = new ActiveXObject(name);
        }catch(err){
            obj = {activeXError:true};
        }
        return obj;
    };
	if(navigator.plugins && navigator.plugins.length>0)
	{
		var type = "application/x-shockwave-flash";
		var mimeTypes = navigator.mimeTypes;
		if(!mimeTypes || !mimeTypes[type] || !mimeTypes[type].enabledPlugin || !mimeTypes[type].enabledPlugin.description)
		{
			$("#previewbtn").parent("span").hide();
			$("#previewbtn").hide();
		}
	}
	else if(navigator.appVersion.indexOf("Mac")==-1 && window.execScript)
	{
		var isFlash = false;
		for(var i=0; i<activeXDetectRules.length; i++){
                var obj = getActiveXObject(activeXDetectRules[i].name);
                if(!obj.activeXError){
					isFlash = true;
				}
			}
		if(!isFlash){
			$("#previewbtn").parent("span").hide();
			$("#previewbtn").hide();
		}
	}		
	$("#nextbtn, #backbtn, td[id^=row], #savebtn, #saveasbtn, #previewbtn").click(function(){
		var URL = "webreport.php";
		if( this.id == "nextbtn" )
			URL = "webchart6.php";
		if( this.id == "backbtn" )
			URL = "webchart4.php";
		if( this.id == "saveasbtn" )
			URL = "webchart6.php?saveas=1";			
		if( this.id.substr(0,3)=="row" && this.id != "row5" )
			URL = "webchart" + this.id.replace("row", "") + ".php";
		if( this.id == "row8" )
			URL = "webreport.php";
		if( this.id == "row9" )
			URL = "menu.php";
		
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
					web: "webcharts",
					name: "appearance",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						if(id == "savebtn")
						{
							$("#alert")
								.html("<p>Chart Saved</p>")
								.dialog("option", "close", function(){
									window.location = "webreport.php";
								})
								.dialog("open");
						}
						else
						{
							$("#preview").click();
						}
					} else {
						if(this.id == "savebtn")
							$("#alert").html("<p>Some problems appear during saving</p>").dialog("open");
						else
							$("#alert").html("<p>Some problems appear during preview</p>").dialog("open");
					}
				},
				error: function() {
					if(this.id == "savebtn")
						$("#alert").html("<p>Some problems appear during saving</p>").dialog("open");
					else
						$("#alert").html("<p>Some problems appear during preview</p>").dialog("open");
				}
			});
		}
		thisid=this.id;
		if(this.id != "row5" && this.id !="savebtn" && this.id !="previewbtn") {
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					name: "appearance",
					web: "webcharts",
					str_xml: output,
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						window.location = URL;
					} else {
						$("#alert").html("<p>"+msg+"</p>").dialog("open");
						if( thisid == "row8" || thisid == "row9" )
							window.location=URL;
					}
				}
			});
		}
	});
});
</script>'."\r\n";

$aColor = array();
for ( $i = 1; $i <= 14; $i++ ) {
	$aColor["color".$i."1"] = "";
	$aColor["color".$i."2"] = "";
}

$slegend_checked = "checked";
$sname_checked = "checked";
$sval_checked = "checked";
$sanim_checked = "checked";
$sgrid_checked = "";
$scur_checked = "";
$sstacked_checked = "";
$saxes_checked = "";
$slog_checked = "";
$isstacked_checked = "";
$is3d_checked = "";
$autoupdate_checked = "";
$update_interval_val = 5;
$cscroll_checked = "checked";
$maxbarscroll_val = 10;
$dec_val = 0;
$linestyle_val=0;
$gaugestyle_val=0;
$cview_val = 0;
$head_val = "New Chart ".CheckLastID('chart');
$foot_val = "New Chart ".CheckLastID('chart');

$arr = $_SESSION['webcharts']['appearance'];
if ( !empty( $arr ) ){
	for ( $i = 1; $i <= 14; $i++ ) {
		$aColor["color".$i."1"] = $arr["color".$i."1"];
		$aColor["color".$i."2"] = $arr["color".$i."1"];
	}
	
	$slegend_checked = ( $arr["slegend"] == "true" ) ? "checked" :  "";
	$sgrid_checked = ( $arr["sgrid"] == "true" ) ? "checked" :  "";
	$sname_checked = ( $arr["sname"] == "true" ) ? "checked" :  "";
	$sval_checked = ( $arr["sval"] == "true" ) ? "checked" :  "";
	$sanim_checked = ( $arr["sanim"] == "true" ) ? "checked" :  "";
	$scur_checked = ( $arr["scur"] == "true" ) ? "checked" :  "";
	$sstacked_checked = ( $arr["sstacked"] == "true" ) ? "checked" :  "";
	$saxes_checked = ( $arr["saxes"] == "true" ) ? "checked" :  "";
	$slog_checked = ( $arr["slog"] == "true" ) ? "checked" :  "";
	$is3d_checked = ( $arr["is3d"] == "true" ) ? "checked" :  "";
	$isstacked_checked = ( $arr["isstacked"] == "true" ) ? "checked" :  "";
	$autoupdate_checked = ( $arr["autoupdate"] == "true" ) ? "checked" :  "";
	$cscroll_checked = ( $arr["cscroll"] == "true" ) ? "checked" :  "";
	$accumulinvert_checked = ( $arr["accumulinvert"] == "true" ) ? "checked" :  "";
	$accumulstyle_val = $arr["accumulstyle"];
	$dec_val = $arr["dec"];
	$aqua_val = $arr["aqua"];
	$linestyle_val = $arr["linestyle"];
	$gaugestyle_val = $arr["gaugestyle"];
	$cview_val = $arr["cview"];
	$head_val = $arr["head"];
	$foot_val = $arr["foot"];
	$update_interval_val = $arr["update_interval"];
	$maxbarscroll_val = $arr["maxbarscroll"];
}

$xt->assign("slegend_checked", $slegend_checked);
$xt->assign("sgrid_checked", $sgrid_checked);
$xt->assign("sname_checked", $sname_checked);
$xt->assign("sval_checked", $sval_checked);
$xt->assign("sanim_checked", $sanim_checked);
$xt->assign("scur_checked", $scur_checked);
$xt->assign("sstacked_checked", $sstacked_checked);
$xt->assign("saxes_checked", $saxes_checked);
$xt->assign("slog_checked", $slog_checked);
$xt->assign("isstacked_checked", $isstacked_checked);
$xt->assign("is3d_checked", $is3d_checked);
$xt->assign("autoupdate_checked", $autoupdate_checked);
$xt->assign("cscroll_checked", $cscroll_checked);
$xt->assign("accumulinvert_checked", $accumulinvert_checked);
$xt->assign("dec_val", $dec_val);
$xt->assign("head_val", $head_val);
$xt->assign("foot_val", $foot_val);
$xt->assign("maxbarscroll_val", $maxbarscroll_val);
$xt->assign("update_interval_val", $update_interval_val);

$b_includes .= '<script type="text/javascript">
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
          $("select[id=aqua]").find("option").each(function(i){
            if ( this.value == "'.$aqua_val.'" ) {
              $("select[id=aqua]")[0].selectedIndex = i;
              return;
            }
          });
          $("select[id=cview]").find("option").each(function(i){
            if ( this.value == "'.$cview_val.'" ) {
              $("select[id=cview]")[0].selectedIndex = i;
              return;
            }
          });
		  $("select[id=linestyle]").find("option").each(function(i){
            if ( this.value == "'.$linestyle_val.'" ) {
              $("select[id=linestyle]")[0].selectedIndex = i;
              return;
            }
          });
		  $("select[id=gaugestyle]").find("option").each(function(i){
            if ( this.value == "'.$gaugestyle_val.'" ) {
              $("select[id=gaugestyle]")[0].selectedIndex = i;
              return;
            }
          });
		  $("select[id=accumulstyle]").find("option").each(function(i){
            if ( this.value == "'.$accumulstyle_val.'" ) {
              $("select[id=accumulstyle]")[0].selectedIndex = i;
              return;
            }
          });
		  $("#autoupdate").click(function()
		  {
			$("#sanim").attr("checked","");
		  });
		  $("#sanim").click(function()
		  {
			$("#autoupdate").attr("checked","");
		  });
		  $("#isstacked").click(function()
		  {
			$("#slog").attr("checked","");
		  });		  
		  $("#slog").click(function()
		  {
			$("#isstacked").attr("checked","");
		  });	
		  
'."\r\n";

for ( $i = 5; $i <= 14; $i++ ) {
	if ( $aColor["color".$i."1"] != "" ) {
            $b_includes .= '$("div[id=picker' . $i . ']").css("background-color","#' . $aColor["color".$i."1"] . '");'."\r\n";
            $b_includes .= '$("div[id=picker' . $i . ']")[0].color1 = "' . $aColor["color".$i."1"] . '";' . "\r\n";
            $b_includes .= '$("div[id=picker' . $i . ']")[0].color2 = "' . $aColor["color".$i."2"] . '";' . "\r\n";
	}	
}

$b_includes .= '});
</script>';

$xt->assign("b_includes", $b_includes);

$xt->assign("stacked", '<td id=td_stacked style="visibility:hidden;">');

if ($_SESSION['webcharts']['chart_type']['type']=="2d_column" || $_SESSION['webcharts']['chart_type']['type']=="2d_bar" || $_SESSION['webcharts']['chart_type']['type']=="bubble" || $_SESSION['webcharts']['chart_type']['type']=="2d_pie" || $_SESSION['webcharts']['chart_type']['type']=="2d_doughnut") 
	$xt->assign("chart_is_3d","<td>");
else 
	$xt->assign("chart_is_3d", '<td style="visibility:hidden;">');	

if ($_SESSION['webcharts']['chart_type']['type']=="2d_column" || $_SESSION['webcharts']['chart_type']['type']=="2d_bar" || $_SESSION['webcharts']['chart_type']['type']=="area") 
	$xt->assign("chart_is_stacked","<td>");
else 
	$xt->assign("chart_is_stacked", '<td style="visibility:hidden;">');	

if ( preg_match( "/bar/i", $_SESSION['webcharts']['chart_type']['type'] ) ) {
	$xt->assign("aqua", "<td>");
    $xt->assign("cview", "<td>");
} else {
	$xt->assign("aqua", '<td style="visibility:hidden;">');
    $xt->assign("cview", '<td style="visibility:hidden;">');	
}
if ( preg_match( "/line/i", $_SESSION['webcharts']['chart_type']['type'] ) ) {
	$xt->assign("line", "<td>");
} else {
	$xt->assign("line", '<td style="visibility:hidden;">');
}
if ( preg_match( "/funnel/i", $_SESSION['webcharts']['chart_type']['type'] ) ) {
	$xt->assign("funnel", "<td colspan=2>");
	$xt->assign("tr_funnel", "");
} else {
	$xt->assign("funnel", '<td colspan=2 style="visibility:hidden;">');
	$xt->assign("tr_funnel", "style=\"display:none\"");
}
if ( preg_match( "/gauge/i", $_SESSION['webcharts']['chart_type']['type'] ) ) {
	$xt->assign("gauge", "<td colspan=2>");
} else {
	$xt->assign("gauge", '<td colspan=2 style="visibility:hidden;">');
}
$xt->assign("chart_name_preview",$_SESSION['webcharts']['settings']['name']);
$table_name = @$_SESSION['webcharts']['tables'][0];
$xt->assign("table_name", $table_name);

$templatefile = "webchart5.htm";
$xt->display($templatefile);

?>