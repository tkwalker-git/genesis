var element = false,
	elementhelp,
	fieldname,
	groupname,
	numChange = 0,
	styleChange = '<attr value="arr">',
	target = 'all';

function getStyleObject(type, glob){
	
    var styleChangeLocal = '<attr value="params">';
    if (type == 'cell'){

        el = $(element);
        var fn=0,gn=0;
        if($(element).attr('fieldname')!=undefined)
            fn=$(element).attr('fieldname');
        if($(element).attr('groupname')!=undefined)
            gn=$(element).attr('groupname');
        
        styleChangeLocal += '<attr value="fieldName">' + fn + '</attr>';
        styleChangeLocal += '<attr value="groupName">' + gn + '</attr>';
        if ($(element).attr('uniqe') != '' && $(element).attr('uniqe') != undefined) styleChangeLocal += '<attr value="uniq">' + $(element).attr('uniqe') + '</attr>';
        else styleChangeLocal += '<attr value="uniq">0</attr>';
    }
    else if (type == 'field'){
        el = $('[fieldname=' + $(element).attr('fieldname') + ']');
        styleChangeLocal += '<attr value="fieldName">' + $(element).attr('fieldname') + '</attr>';
        styleChangeLocal += '<attr value="groupName">0</attr>';
        styleChangeLocal += '<attr value="uniq">0</attr>';
    }
    else if (type == 'group'){
        el = $('[groupname=' + $(element).attr('groupname') + ']');
        styleChangeLocal += '<attr value="groupName">' + $(element).attr('groupname') + '</attr>';
        styleChangeLocal += '<attr value="fieldName">0</attr>';
        styleChangeLocal += '<attr value="uniq">0</attr>';
    }
    else if (type == 'table'){
        el = $(element);
        styleChangeLocal += '<attr value="groupName">0</attr>';
        styleChangeLocal += '<attr value="fieldName">0</attr>';
        styleChangeLocal += '<attr value="uniq">0</attr>';
    }
		
    var str = new Array,
		str_type = new Array,
		t = 0;
		
    if (el.css('font') && ((glob == "all") || (glob == "font"))){ str[t] = 'font: ' + el.css('font') + '; '; str_type[t] = "font"; t++;}
    if (el.css('fontStyle') && ((glob == "all") || (glob == "fontStyle") || (glob == "font"))){ str[t] = 'font-style: ' + el.css('fontStyle') + '; '; str_type[t] = "fontStyle"; t++;}
    if (el.css('fontWeight') && ((glob == "all") || (glob == "fontWeight")) || (glob == "font")){ str[t] = 'font-weight: ' + el.css('fontWeight') + '; '; str_type[t] = "fontWeight"; t++;}
    if (el.css('color') && ((glob == "all") || (glob == "color"))){ str[t] = 'color: ' + el.css('color') + '; '; str_type[t] = "color"; t++;}
	if (el.css('background') && ((glob == "all") || (glob == "background") || (glob == "backgroundColor"))){ str[t] = 'background: ' + el.css('background') + '; '; str_type[t] = "background"; t++;}
	if (el.css('backgroundColor') && ((glob == "all") || (glob == "background") || (glob == "backgroundColor"))){ str[t] = 'background-color: ' + el.css('backgroundColor') + '; '; str_type[t] = "backgroundColor"; t++;}
    if (el.css('fontFamily') && ((glob == "all") || (glob == "fontFamily"))){ str[t] = 'font-family: ' + el.css('fontFamily') + '; '; str_type[t] = "fontFamily"; t++;}
    if (el.css('fontSize') && ((glob == "all") || (glob == "fontSize"))){ str[t] = 'font-size: ' + el.css('fontSize') + '; '; str_type[t] = "fontSize"; t++;}
    if (el.css('textAlign') && ((glob == "all") || (glob == "textAlign"))){ str[t] = 'text-align: ' + el.css('textAlign') + '; '; str_type[t] = "textAlign"; t++;}
    if (el.css('padding') && ((glob == "all") || (glob == "padding"))){ str[t] = 'padding: ' + el.css('padding') + '; '; str_type[t] = "padding"; t++;}
		
    for (var n in str) {
        styleChange +='<attr value="'+numChange+'">';
        styleChange += styleChangeLocal;
        styleChange += '<attr value="styleStr">' + str[n] + '</attr>';
        styleChange += '<attr value="styleType">' + str_type[n] + '</attr>';
        styleChange += '</attr>'
        styleChange += '<attr value="type">' + type + '</attr>';
        styleChange += '</attr>'
			
        numChange++; n++;
    }
}
	
function replace_string(txt,cut_str,paste_str){
    var f=0;
    var ht='';
    ht = ht + txt;
    f=ht.indexOf(cut_str);
    while (f!=-1){
        f=ht.indexOf(cut_str);
        if (f>0){
            ht = ht.substr(0,f) + paste_str + ht.substr(f+cut_str.length);
        };
    };
    return ht;
};
	
function cleanaligntable(){
    $('#aligntable td').each(function (){
        $(this).css('border','1px solid black').css('margin',2);
    });
}

function applyToSomething(targetCss,targetField){

	var targetToSomeField = '';
    if (targetField == 'field') {
        targetToSomeField = '[fieldname=' + fieldname + ']';
    } else if (targetField == 'group') {
        targetToSomeField = '[groupname=' + groupname + ']';
    } else if (targetField == 'table') {
        targetToSomeField = '#legend td';
    }

    if (targetCss == 'all'){
        $(targetToSomeField).css('textAlign', $(element).css('textAlign'));
        $(targetToSomeField).css('fontWeight', $(element).css('fontWeight'));
        $(targetToSomeField).css('fontStyle', $(element).css('fontStyle'));
        var str = $(element).css('paddingTop') + ' '
            + $(element).css('paddingRight') + ' '
            + $(element).css('paddingBottom') + ' '
            + $(element).css('paddingLeft') + ' ';
        $(targetToSomeField).css('padding', str);
        $(targetToSomeField).css('color', $(element).css('color'));
        $(targetToSomeField).css('fontSize', $(element).css('fontSize'));
        $(targetToSomeField).css('fontFamily', $(element).css('fontFamily'));
        $(targetToSomeField).css('background', $(element).css('background'));
    } else if (targetCss == 'font'){
        $(targetToSomeField).css('fontWeight', $(element).css('fontWeight'));
        $(targetToSomeField).css('fontStyle', $(element).css('fontStyle'));
    } else if (targetCss == 'padding'){
        var str = $(element).css('paddingTop') + ' '
            + $(element).css('paddingRight') + ' '
            + $(element).css('paddingBottom') + ' '
            + $(element).css('paddingLeft') + ' ';
        $(targetToSomeField).css('padding', str);
    }
    else { 
        $(targetToSomeField).css(targetCss, $(element).css(targetCss));// - к группе
    }
    getStyleObject(targetField,targetCss);
}

function fun(type){
    
	element = type;
    groupname = $(element).attr("groupname");
    fieldname = $(element).attr("fieldname");

    cleanaligntable();

    var str;
    if (($(element).css('textAlign') == '') || ($(element).css('textAlign') == 'start')  || ($(element).css('textAlign') == 'auto')) {
        str = 'left';
    } else { 
        str = $(element).css('textAlign');
    }

    $('#' + str).css('border','3px solid black').css('margin',2);
	$("#fontfamily").html("default");
	if ($(element).css("fontFamily")) {
		$("#fontfamilyselect table td").each(function(){
			if ($(element).css("fontFamily") == $(this).html())
				$("#fontfamily").html($(this).html());
		});
	}
	$("#fontsize").html("default");
    if ($(element).css("fontSize")) {
		$("#fontsizeselect table td").each(function(){
			if ($(element).css("fontSize") == $(this).html())
				$("#fontsize").html($(this).html());
		});
	}
	$("#padding").html("default");
    if ($(element).css("paddingLeft")) {
		$("#paddingselect table td").each(function(){
			if ($(element).css("paddingLeft") == $(this).html())
				$("#padding").html($(this).html());
		});
	}

    if ($(element).css('fontStyle') == 'italic') {
        $("#italic").attr('checked','checked');
    } else {
        $("#italic").removeAttr('checked'); 
    }
    if ($(element).css('fontWeight') == 'bold' || $(element).css('fontWeight')=='700') {
        $("#bold").attr('checked','checked');
    } else {
        $("#bold").removeAttr('checked');
    }

    $('#picker1').css('backgroundColor', $(element).css('color')).html(''); 
    $('#picker2').css('backgroundColor', $(element).css('backgroundColor')).html('');
    $("#params").show();
	
}

function changeact(act,value,td){

	if (act == 'font') {
        value = replace_string(value,',',' ') + ' ' + $(element).css('fontSize') + ' sans-serif';
    }else if (act == 'textAlign'){ 
        cleanaligntable();
        $(td).css('border','3px solid black');
    }
    $(element).css(act,value);
    getStyleObject("cell", act);
}

var tdborder,
	group = new Array(
		'', 
		'Group 1 Header', 'Group 1 Count', 'Group 1 Min', 'Group 1 Max', 'Group 1 Sum', 'Group 1 Avg', 
		'Group 2 Header', 'Group 2 Count', 'Group 2 Min', 'Group 2 Max', 'Group 2 Sum', 'Group 2 Avg', 
		'Group 3 Header', 'Group 3 Count', 'Group 3 Min', 'Group 3 Max', 'Group 3 Sum', 'Group 3 Avg',
		'Group 4 Header', 'Group 4 Count', 'Group 4 Min', 'Group 4 Max', 'Group 4 Sum', 'Group 4 Avg', 
		'Table Data', 
		'Page Summary', 'Page Summary Min', 'Page Summary Max', 'Page Summary Sum', 'Page Summary Avg', 
		'Group Summary', 'Group Summary Min', 'Group Summary Max', 'Group Summary Sum', 'Group Summary Avg',
		'Group Summary 1', 'Group Summary 2', 'Group Summary 3', 'Group Summary 4', 'Field Header'
	);
	
$(document).ready(function(){

	$(".selectstyle")
		.mouseover(
			function (){
				$(this).css('backgroundColor','rgb(146, 190, 235)').parent().css('backgroundColor','rgb(146, 190, 235)');
			})
		.mouseout( 
			function (){
				$(this).css('backgroundColor','rgb(244, 247, 251)').parent().css('backgroundColor','rgb(244, 247, 251)');
			});

/* font family
 *------------------------------------------------------------------*/
	var timeoutfamily	 = 300,
		closetimerfamily = 0;
	$(".selectorfamily, #fontfamily").click(function(){
		$("#fontfamilyselect").show();
	}).mouseout(function(){
		closetimerfamily = window.setTimeout("$(\"#fontfamilyselect\").hide();", timeoutfamily);
	});
	$("#fontfamilyselect table").mouseout(function(){
		closetimerfamily = window.setTimeout("$(\"#fontfamilyselect\").hide();", timeoutfamily);
	}).mouseover(function(){
		if(closetimerfamily) {
			window.clearTimeout(closetimerfamily);
			closetimerfamily = null;
		}
	});
	$("#fontfamilyselect table td").click(function(){
		if ($(this).html() != 'define') 
			changeact('fontFamily',$(this).html());
		else changeact('fontFamily','');
		$("#fontfamily").html($(this).html());
		$("#fontfamilyselect").hide();
	});
/*-------------------------------------------------------*/

/* font size
 *------------------------------------------------------------------*/
	var timeoutsize	 = 300,
		closetimersize = 0;
	$(".selectorfontsize,#fontsize").click(function(){
		$("#fontsizeselect").show();
	}).mouseout(function(){
		closetimersize = window.setTimeout("$(\"#fontsizeselect\").hide();", timeoutsize);
	});
	$("#fontsizeselect table").mouseout(function(){
		closetimersize = window.setTimeout("$(\"#fontsizeselect\").hide();", timeoutsize);
	}).mouseover(function(){
		if(closetimersize) {
			window.clearTimeout(closetimersize);
			closetimersize= null;
		}
	});
	$("#fontsizeselect table td").click(function(){
		if ($(this).html() != 'define') 
			changeact('fontSize',$(this).html());
		else changeact('fontSize','');
		$("#fontsize").html($(this).html());
		$("#fontsizeselect").hide();
	});
/*-------------------------------------------------------*/

/* padding
 *------------------------------------------------------------------*/
	var timeoutpadding	 = 300,
		closetimerpadding = 0;
	$(".selectorpadding,#padding").click(function(){
		$("#paddingselect").show();
	}).mouseout(function(){
		closetimerpadding = window.setTimeout("$(\"#paddingselect\").hide();", timeoutpadding);
	});
	$("#paddingselect table").mouseout(function(){
		closetimerpadding = window.setTimeout("$(\"#paddingselect\").hide();", timeoutpadding);
	}).mouseover(function(){
		if(closetimerpadding) {
			window.clearTimeout(closetimerpadding);
			closetimerpadding= null;
		}
	});
	$("#paddingselect table td").click(function(){
		if ($(this).html() != 'define') 
			changeact('padding',$(this).html());
		else changeact('padding','');
		$("#padding").html($(this).html());
		$("#paddingselect").hide();
	});
/*-------------------------------------------------------*/
	
	$("#default").click(function(){
		$.cookie('position_top',$("#example").css("top"),{'expires': 10000});	
		$.cookie('position_left',$("#example").css("left"),{'expires': 10000});	
		$.ajax({
			type: "POST",
			url: "save-style.php",
			data: {
				repname: $("#location").attr('src'),
				str_xml: 'del_all'
			},
			success: function(msg){
				window.location.reload();
			}
		});
	});

	
	$('#legend td').click(function(){
        if (tdborder){
			$('div',tdborder).css('border','2px solid '+$(tdborder).css("backgroundColor"));
		}
        tdborder = this;
		$('div',tdborder).css('border','2px solid red');
    });     
    
    $("#example").hide();

    $("#colorPickerVtd td").each(function(){
        $(this).css("border","1px solid " + $(this).css("backgroundColor"));
    })
		.css("cursor","pointer");
	$(".ColorPickerDivSample").css("cursor","pointer");

    $("#headerpanel").css("textAlign",'center');
    $("#middlepanel").css("textAlign",'center');
    $("#headerpanel td").css("textAlign",'center');

    $("#legend td img, .legend td img").hide();
    $("#legend td, .legend td").click(function (){fun(this);})
    .each(function (){
        var str = $(this).html();
		$(this).html('<div style="border: 2px solid '+$(this).css("backgroundColor")+'">'+$(this).html()+'</div>');
		if (($(this).attr('groupname') == 1) || ($(this).attr('groupname') == 7) || ($(this).attr('groupname') == 13) || ($(this).attr('groupname') == 19) || ($(this).attr('groupname') == 26) || ($(this).attr('groupname') == 31) || ($(this).attr('groupname') == 36) || ($(this).attr('groupname') == 37) || ($(this).attr('groupname') == 38) || ($(this).attr('groupname') == 39)){
			$('div',this).html(group[$(this).attr('groupname')]);
		}
		else if (($(this).attr('groupname') != 40 && $('div',this).html()!="&nbsp;")){
			$('div',this).html('[text]');
		}
		//$(this).html('<div style="border: 2px solid '+$(this).css("backgroundColor")+'">'+group[$(this).attr('groupname')]+'</div>');
		//if ($(this).attr('groupname') != 40)
            //$(this).html('<div style="border: 2px solid '+$(this).css("backgroundColor")+'">[text]</div>');		
    });

    $("#example").draggable({handle: "#wnd_header"})
		.css("top", $(window).height() - $("#example").height() - 150)
		.css("left", $(window).width()/2 - $("#example").width()/2)		
		.parent().parent()
		.css("opacity",'0.9')
		.css("border","1px solid black");
	if ($.cookie('position_top')) {
		$("#example").css('top',$.cookie('position_top'));
		$.cookie('position_top',null);
	}
	if ($.cookie('position_left')) {
		$("#example").css('left',$.cookie('position_left'));
		$.cookie('position_top',null);
	}
    $("#legend td:first").click();

    $("#groupSelectField").hover(
		function (){
			$('[fieldname=' + fieldname + ']').each(function(){
				$('div',this).css('border','2px solid red');
			});
		}, 
		function (){
			$('[fieldname=' + fieldname + ']').each(function(){
				$('div',this).css('border','2px solid '+$(this).css("backgroundColor"));
			});
			$('div',element).css('border','2px solid red');
		}
	);
    $("#groupSelectGroup").hover(
		function (){
			$('[groupname=' + groupname + ']').each(function(){
				$('div',this).css('border','2px solid red');
			});
		}, 
		function (){
			$('[groupname=' + groupname + ']').each(function(){
				$('div',this).css('border','2px solid '+$(this).css("backgroundColor"));
			});
			$('div',element).css('border','2px solid red');
		}
	);

    $("#bold").click(function(){
        if ($("#bold").attr("checked")) $(element).css('fontWeight','bold');
        else $(element).css('fontWeight','normal');
        getStyleObject('cell', 'fontWeight');
    });

    $("#italic").click(function(){
        if ($("#italic").attr("checked")) $(element).css('fontStyle','italic');
        else $(element).css('fontStyle','normal');
        getStyleObject('cell', 'fontStyle');
    });

    $("#load").hide();
    $("#legend, #example, .legend").show();

    $("#legend td, .legend td").click(function(){
        $("#example").parent().parent().css('display','block');
        $(this).toggleClass("active"); return false;
    });

var timeoutpicker	 = 300,
	closetimerpicker = 0,
	div_id=0;
	
/* Color picker
 *------------------------------------------------------------------*/	
	
	$(".selector,#picker2,#picker1").click(function(){
		
		if(closetimerpicker) {
			window.clearTimeout(closetimerpicker);
			closetimerpicker = null;
		}
		
		$(".ColorPickerDivSample").each(function(){
			this.active = "no";
		});
		
		div_id=$(this).parents("tr").eq(0).find("div").get(0).id;
		
		$(this).parents("tr").eq(0).find("div").get(0).active = "yes";
		
		var top = $('#pick').position().top + $('#example').position().top;
		var left = $(this).position().left + $('#example').position().left;
		if ((top + 210) > $(window).height()){
			top = $(window).height() - $("#colorPickerVtd").height();
		}
		
		if ((left + 210) > $(window).width()){
			left = $(window).width() - $("#colorPickerVtd").width();
		}
		bc=$(this).parents("tr").find("div.ColorPickerDivSample").css("background-color");
		$("#colorPickerVtd").css("top", top+8);
		$("#colorPickerVtd").css("left", left+8);
		$("#colorPickerVtd").show();
		$("#colorPickerVtd td").each(function(){
			$(this).css("border", "1px solid "+$(this).css("background-color"));
			if(bc==$(this).css("background-color"))
				$(this).css("border", "1px dotted #fff");
		});
	});
		
	$("#colorPickerVtd").mouseover(function(){
		if(closetimerpicker) {
			window.clearTimeout(closetimerpicker);
			closetimerpicker = null;
		}
	}).mouseout(function(){
		closetimerpicker = window.setTimeout("$(\"#colorPickerVtd\").hide();", timeoutpicker);
	});
	
	$("#colorPickerVtd td").mouseover(function(){
		if(closetimerpicker) {
			window.clearTimeout(closetimerpicker);
			closetimerpicker = null;
		}
		$(this).css("border", "1px dashed white");
		if ($(this).attr('id')!="nocolor"){
			$(".ColorPickerDivSample[active=yes]").css("backgroundColor",$(this).css("backgroundColor")).html('');
		}
		else{
			$(".ColorPickerDivSample[active=yes]").css("backgroundColor",'').html('No color');
		}
	});

	$("#colorPickerVtd td").mouseout(function(){
		$(this).css("border", "1px solid " + $(this).css("backgroundColor"));
	});

	$("#colorPickerVtd td").click(function(){
		if ( this.name == "none" ) {
			$("#"+div_id)[0].color1 = "";
			$("#"+div_id)[0].color2 = "";
			changeact($(".ColorPickerDivSample[active=yes]").attr('act'),'');
		} else {
			$("#"+div_id)[0].color1 = $(this).css("backgroundColor");
			if ($(this).attr('id')!="nocolor"){
				//$(".ColorPickerDivSample[active=yes]").css("backgroundColor",$(this).css("backgroundColor")).html('');
				$("#"+div_id).css("backgroundColor",$(this).css("backgroundColor")).html('');
				changeact($("#"+div_id).attr('act'),$(this).css("backgroundColor"));
			}
			else{
				$("#"+div_id).css("backgroundColor",'').html('No color');
				changeact($("#"+div_id).attr('act'),'none');
			}
		}
		$("#colorPickerVtd").hide();
	});
	
	$(".apply-style").change(function(){
		applyToSomething($(this).attr("id"), $(this).val());
	});	
	
var timeoutselector	= 400,
	closetimerselector	= 0;
/* Group Selector
 *------------------------------------------------------------------*/


	$(".selectorApply").parent().parent().click(function(){
		if(closetimerselector) {
			window.clearTimeout(closetimerselector);
			closetimerselector = null;
		}
		var top = $(this).position().top + $('#example').position().top+$(this).parent().parent().height();
		var left = $(this).position().left + $('#example').position().left;
		if ((top + 80) > $(window).height()){
			top = top-66;
		}		
		if ((left + 200) > $(window).width()){
			left = $(window).width() - $("#groupSelectorDiv").width();
		}
		$("#groupSelectorDiv").css("top", top+7);
		$("#groupSelectorDiv").css("left", left+12);
		$("#groupSelectorDiv").css("width",$(this).parent().parent().width());
		$("#groupSelectorDiv").show();
		target = $(".selectorApply",this).attr("act");
	});
	$(".selectorApply").parent().parent().mouseout(function(){
		closetimerselector = window.setTimeout("$(\"#groupSelectorDiv\").hide();", timeoutselector);
	});
	$("#groupSelectorDiv td").mouseover(function(){
		if(closetimerselector) {
			window.clearTimeout(closetimerselector);
			closetimerselector = null;
		}
	});
		
	$("#groupSelectorDiv td").mouseout(function(){
		closetimerselector = window.setTimeout("$(\"#groupSelectorDiv\").hide();", timeoutselector);
	});
	
	$(".groupSelect").click(function(){
		applyToSomething(target,$(this).attr("value"));
		$("#groupSelectorDiv").hide();
	}).hover(
		function (){
			$(this).css('backgroundColor','rgb(146, 190, 235)').parent().parent().css('backgroundColor','rgb(146, 190, 235)');
		}, 
		function (){
			$(this).css('backgroundColor','rgb(244, 247, 251)').parent().parent().css('backgroundColor','rgb(244, 247, 251)');
		}
	);
	$(".selectorApply").parent().parent().find("td:first").mouseover(function(){
		if(closetimerselector) {
			window.clearTimeout(closetimerselector);
			closetimerselector = null;
		}
	});

/* Navigation Buttons
 *------------------------------------------------------------------*/
var timeout	= 200,
	closetimer	= 0;
	
	$("#alert").dialog({
		title: "Message",
		draggable: false,
		resizable: false,
		bgiframe: true,
		autoOpen: false,
		modal: true,
		buttons: {
			Ok: function() {
				$(this).dialog("close");
			}
		}
	});
	
	$("#row7")
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
 
	$("#jumpto").mouseover(function(){
		if(closetimer) {
			window.clearTimeout(closetimer);
			closetimer = null;
		}
		if ($("#jumpto").offset().top + $("#jumpto").height() + $("#menujump").height() + $(window).scrollTop() > $(window).height()) {
			$("#menujump").css("top", ($(this).offset().top - $("#menujump").height()) + "px");		
			$("#menujump").css("left", ($(this).offset().left) + "px");					
		} else {
			$("#menujump").css("top", ($(this).offset().top + $(this).height()) + "px");		
			$("#menujump").css("left", ($(this).offset().left) + "px");			
		}
		$("#menujump").show();
	});

	$("#jumpto").mouseout(function(){
		closetimer = window.setTimeout("$(\"#menujump\").hide();", timeout);
	});
	
	$("#menujump td").mouseover(function(){
		if(closetimer) {
			window.clearTimeout(closetimer);
			closetimer = null;
		}
	});
		
	$("#menujump td").mouseout(function(){
		closetimer = window.setTimeout("$(\"#menujump\").hide();", timeout);
	});
	
	$(document.body).click(function(){
		$("#menujump").hide();
	});
	
	$("#nextbtn, #backbtn, td[id^=row], #savebtn, #saveasbtn, #previewbtn").click(function(){
		var URL = "webreport.php";
		if( this.id == "nextbtn" )
			URL = "webreport8.php";
		if( this.id == "backbtn" )
			URL = "webreport6.php";
		if( this.id == "saveasbtn" )
			URL = "webreport8.php?saveas=1";
		if( this.id.substr(0,3)=="row" && this.id != "row7" )
			URL = "webreport" + this.id.replace("row", "") + ".php";
		if( this.id == "row10" )
			URL = "webreport.php";
		if( this.id == "row11" )
			URL = defURL;

		if( this.id == "savebtn" || this.id == "previewbtn") {
			id=this.id;
			$.ajax({
				type: "POST",
				url: "save-state.php",
				data: {
					save: 1,
					name: "style_editor",
					web: "webreports",
					str_xml: '{ "style_editor" : { "status" : "success" } }',
					rnd: (new Date().getTime())
				},
				success: function(msg){
					if ( msg == "OK" ) {
						$.ajax({
							type: "POST",
							url: "save-style.php",
							data: {
								repname: $("#location").attr('src'),
								str_xml: styleChange + "</attr>"
							},
							success: function(msg){
								if(id=="savebtn")
								{
									styleChange = new String('<attr value="arr">');
									$("#alert").html("<p>Report Saved</p>").dialog("option", "close", function(){
										window.location = "webreport.php";
									}).dialog("open");
								}
								else
									$("#preview").click();
							},
							error: function() {
								$("#alert").html("<p>Some problems appear during saving</p>").dialog("open");
							}
						});						
					} else {
						$("#alert").html("<p>"+msg+"</p>").dialog("open");
					}
				}
			});			
			styleChangeLocal = '<attr value="arr">';
		}
	
		if(this.id != "row7" && this.id !="savebtn" && this.id !="previewbtn") {
			$.ajax({
				type: "POST",
				url: "save-style.php",
				data: {
					repname: $("#location").attr('src'),
					str_xml: styleChange + "</attr>"
				},
				success: function(msg){
					styleChange = new String('<attr value="arr">');
					window.location = URL;
				},
				error: function() {
					$("#alert").html("<p>Some problems appear. Try again later</p>").dialog("open");
				}
			});			
		}
	});	
});