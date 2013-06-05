var debug=false;
var removeflyframe;
function DisplayPage(event,page,parId,control,field,tablename,category)
{
	if(page.indexOf("_add.")>0 || page.indexOf("_list.")>0)
	{
		var ctrl = Runner.controls.ControlManager.getAt(tablename, parId, field, 0);
		if (ctrl){
			editMode = ctrl.mode;
		}else{
			editMode = -1;
		}
	}
	flyid++;
	var id = flyid;
	var firstTime = 1;
	var x,y;
	if($.browser.msie)
	{
		y = event.y;
		x = event.x;
	}
	else
	{
		y = event.clientY;
		x = event.clientX;
	}
	var params;
	var pagetype;
	if(page.indexOf("_add.")>0)
	{
		params={
			editType:"onthefly",
			id:id,
			rndval: Math.random(),
			editType: "onthefly",
			control: control,
			field: field,
			table: tablename,
			category: category
		};
		pagetype="add";
		firstTime = 0;
	}
	else if(page.indexOf("_list.")>0)
	{
		params={
			id:id,
			parId:parId,
			rndval: Math.random(),
			mode: "lookup",
			control: control,
			field: field,
			table: tablename,
			category: category,
			firsttime: firstTime,
			editMode: editMode
		};
		pagetype="list";
	}
	else if(page.indexOf("fulltext.")==0)
	{
		params={
			id:id,
			rndval: Math.random()
		};
		if(page.indexOf("?")==-1)
			page = page + '?' + field;
		else
			page = page + '&' + field;
		pagetype="fulltext";
		firstTime = 0;
	}
	else
		return false;
		
	$.get(page,	params,
		function(xml)
		{
			var pos = pos1 = -1;
			if(pagetype!="fulltext")
			{
				var pos = xml.indexOf("<jscode>");
				var pos1 = xml.indexOf("</jscode>");
			}
			var js = "";
			if(pos>=0 && pos1>=0)
			{
				js = slashdecode(xml.substr(pos+8,pos1-pos-8));
				xml = xml.substr(pos1+9);
			}
			if(debug)
			{
				$(document.body).append("<textarea id=htm"+id+" cols=50 rows=10></textarea>");
				$("#htm"+id).text(xml);
			}
			DisplayFlyDiv(xml,js,id,control,x,y,pagetype,field,parId,"","",firstTime);
		}
	);
	
	return flyid;
	
}

function RemoveFlyDiv(id, dontremoveiframe,type)
{
	$("#fly"+id).remove();	

	if(!dontremoveiframe)
	{
		var removeflyframe = $("#flyframe" + id)[0];
		if (removeflyframe){
			setTimeout(function(){
				$(removeflyframe).remove();
			}, 1);
		}		
	}
		
	if(IsIE6())	
		$("#fli"+id).remove();
	$("#shadow"+id).remove();
}
 
/**
 * Returns object which contains height, width ,x ,y
 * @param {int} id
 * @param {int} x
 * @param {int} y
 * @return {obj}
 */
function getFlyDivSizeAndCoors(id, x, y)
{
	var w, h, flydiv=$("#fly"+id)[0];
	
	if($.browser.msie)
		w = 'width:55%;';
	w = flydiv.offsetWidth;
	var h = flydiv.offsetHeight;
	var oW = document.body.offsetWidth*.55;
	var sH = screen.height*0.5;
	
	if(w > oW) 
		w = oW;
	if(h > sH) 
		h = sH;
	var flycontents = $("#flycontents"+id)[0];
	var Wfcon = flycontents.offsetWidth;
	var Hfcon = flycontents.offsetHeight;
	if((w > Wfcon || w < Wfcon) && Wfcon <= oW) 
		w = Wfcon;
	if((h > (Hfcon+35) || h < (Hfcon+35)) && Hfcon <= sH) 
		h = (Hfcon+35);
	
	x += document.body.scrollLeft;
	y += document.body.scrollTop;
	if(document.body.scrollLeft + document.body.clientWidth < x+w)
		x = document.body.scrollLeft + document.body.clientWidth - w-20;
	if(x < document.body.scrollLeft)
		x = document.body.scrollLeft+20;
	if(document.body.scrollTop + document.body.clientHeight < y+flydiv.offsetHeight)
		y = document.body.scrollTop + document.body.clientHeight - flydiv.offsetHeight-20;
	if(y < document.body.scrollTop)
		y = document.body.scrollTop+20;
	
	x = x + ''; y = y + ''; w = w + ''; h = h + '';
	if(x.indexOf('px')!==-1)
	{
		x = x.substring(0, x.length-2);
		x = parseInt(x);
	}
	if(y.indexOf('px')!==-1)
	{
		y = y.substring(0, y.length-2);
		y = parseInt(y);
	}
	if(w.indexOf('px')!==-1)
	{
		w = w.substring(0, w.length-2);
		w = parseInt(w);
	}
	if(h.indexOf('px')!==-1)
	{
		h = h.substring(0, h.length-2);
		h = parseInt(h);
	}
	return{
		x: x ? x : 0,
		y: y ? y : 0,
		width: w < 400 ? 400 : w,
		height: h < 250 ? 250 : h
	};
}
/**
 * Set position of fly div. Also make div resizable
 * @param {obj} paramObj
 * @param {int} id
 */
function setFlyDivDimAndCoors(paramObj, id)
{
	var x = parseInt(paramObj.x), 
		y = parseInt(paramObj.y), 
		w = parseInt(paramObj.width), 
		h = parseInt(paramObj.height), 
		flydiv=$("#fly"+id)[0], 
		flycontents = $("#flycontents"+id)[0];
	
	if(IsIE6())
	{
		var frameW = w+8;
		var frameH = h+8;
		var flyframe = document.getElementById("fli"+id);
		$(flyframe).css("left","" + (x) + "px");
		$(flyframe).css("top",""+(y)+"px");
		$(flyframe).css("width", frameW + "px");
		$(flyframe).css("height", frameH+"px");
		$(flyframe).show();
	}
	
	var flycontainer = document.getElementById("flycontainer"+id); 
	$(document.body).append($(flydiv));
	
	if (flycontainer)
		document.body.removeChild(flycontainer);	
		
	$(flydiv).css("position", "absolute");
	$(flydiv).css("left","" + (x) + "px");
	$(flydiv).css("top",""+(y)+"px");
	$(flydiv).css("width","" + (w) + "px");
	$(flydiv).css("height",""+(h)+"px");
	
	if(IsIE6())
	{
		$(flycontents).css("width",""+(w-9)+"px");
		$(flycontents).css("height",""+(h-29)+"px");
	}
	else{
			$(flycontents).css("width",""+(w-28)+"px");
			$(flycontents).css("height",""+(h-38)+"px");
		}
	
	flydivonclick(flydiv,id);
	
	if($.browser.mozilla)
	{
		var clientHeight = window.innerHeight;
		var clientWidth = window.innerWidth;
	}
	else{
			var clientHeight = document.body.clientHeight;
			var clientWidth = document.body.clientWidth;
		}
	
	$("#fly"+id).resizable({
		handles: 's,e,w,se,sw',
		maxHeight: clientHeight,
		maxWidth: clientWidth,
		minWidth: 100,
		minHeight: 100,
		resize: function(e, ui)	{
			$(shadow).css("left",ui.instance.position.left+8);
			$(shadow).css("top",ui.instance.position.top+8);
			$(shadow).css("width",ui.instance.size.width);
			$(shadow).css("height",ui.instance.size.height);
			if(IsIE6())
			{
				$(flyframe).css("left",ui.instance.position.left+2);
				$(flyframe).css("top",ui.instance.position.top+2);
				$(flyframe).css("width",ui.instance.size.width+6);
				$(flyframe).css("height",ui.instance.size.height+6);
				w = ui.instance.size.width-9;
				h = ui.instance.size.height-29;
			}
			else{
					w = ui.instance.size.width-28;
					h = ui.instance.size.height-38;
				}
			$(flycontents).css("width",""+w+"px");
			$(flycontents).css("height",""+h+"px");	 
		}
	});
	var shadowX = x+8;
	var shadowY = y+8;
	
	var shadow = $("#shadow"+id);
	shadow.css("left", shadowX+"px");
	shadow.css("top",shadowY+"px");
	shadow.css("width",w+"px");
	shadow.css("height",h+"px");
	shadow.show();
}
/**
 * 
 * @param {string} html
 * @param {string} js
 * @param {} id
 * @param {} control
 * @param {} x
 * @param {} y
 * @param {} pagetype
 * @param {} field
 * @param {} parId
 * @param {string} oncloseHandlerCode
 * @param {object} cfgObj
 * @param {object} cfgObj.headerObj:
 {
	 * @param {string} cfgObj.title
	 * @param {object} cfgObj.closeButton
	 * @param {string} cfgObj.closeButton.style
	 * @param {string} cfgObj.closeButton.handler
	 * @param {string} cfgObj.closeButton.src
	 * @param {array} cfgObj.buttons
	 * @param {object} cfgObj.buttons[i]
	 * @param {string} cfgObj.buttons[i].style
	 * @param {string} cfgObj.buttons[i].handler
	 * @param {string} cfgObj.buttons[i].src
 }
 * 
 * @return {object}
 */
function DisplayFlyDiv(html,js,id,control,x,y,pagetype,field,parId, oncloseHandlerCode, cfgObj, firstTime)
{
	cfgObj = cfgObj ? cfgObj : {};
	
	window["postloadstep"+(id ? "_"+id : "")+"_worked"]=false;
	
	var w='width:inherit;';
	
	if(IsIE6())
		$(document.body).append("<iframe src=\"javascript:false;\" id='fli"+id+"' frameborder=\"0\" vspace=\"0\" hspace=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" style='background:white; position:absolute;display:none;opacity:0;filter:alpha(opacity=0);' > </iframe>");
	
	$(document.body).append("<div id='flycontainer"+id+"' style='position:absolute;'>"
		+"<div align=center pagetype='"+pagetype+"' id='fly"+id+"' style='"+w+"border:solid #434343 1px; background:white; background-repeat:no-repeat;overflow:hidden;'  control='"+control+"'  onmousedown='flydivonclick(this,"+id+");'></div></div>");
	var title="", additionalButtonsHtml = "";
	
	var closeButtonHtml = "<img src='images/cross.gif' style='cursor:pointer;' onclick=\""+(oncloseHandlerCode ? oncloseHandlerCode : '')+" RemoveFlyDiv('"+id+"');\">";
	// proccess title object, which may contain title txt, buttons, and close button
	if (cfgObj.headerObj)
	{		
		title = cfgObj.headerObj.title;
		// proccess close button if it passed
		if (cfgObj.headerObj.closeButton)
		{
			closeButtonHtml = "<img src=\""+(cfgObj.headerObj.closeButton.src ? cfgObj.headerObj.closeButton.src : 'images/cross.gif')+"\"";			
			closeButtonHtml += (cfgObj.headerObj.closeButton.style ? 'style="'+cfgObj.headerObj.closeButton.style+'"' : 'style="cursor:pointer;"');			
			oncloseHandlerCode = cfgObj.headerObj.closeButton.handler || oncloseHandlerCode;			
			closeButtonHtml += "onclick=\""+(oncloseHandlerCode ? oncloseHandlerCode : '')+" RemoveFlyDiv('"+id+"');\"";
			closeButtonHtml += ">";
		}
		else if (cfgObj.headerObj.closeButton === false)
			closeButtonHtml = '';
		
		// proccess addtional buttons if they passed
		if (cfgObj.headerObj.buttons)
		{
			for(var i=0;i<cfgObj.headerObj.buttons.length; i++)
			{
				var buttonObj = cfgObj.headerObj.buttons[i];
				additionalButtonsHtml = "<img src=\""+buttonObj.src+"\"";				
				additionalButtonsHtml += (buttonObj.alt ? 'alt="'+buttonObj.alt+'"' : '') ;
				additionalButtonsHtml += (buttonObj.title ? 'title="'+buttonObj.title+'"' : '') ;
				additionalButtonsHtml += (buttonObj.style ? 'style="'+buttonObj.style+'"' : 'style="cursor:pointer;"') ;
				additionalButtonsHtml += (buttonObj.handler ? 'onclick="'+buttonObj.handler+'"' : "");
				additionalButtonsHtml += ">";
			}			
		}
	}
			
	var titlebar="<div id=display_fly"+id+" onmousedown='fly_mousedown_func(event,this.parentNode,"+id+")' class='blackshade' style='padding:5px 10px;border-bottom:solid black 1px;text-align:right;cursor:move;'><span style='float:left;'> "+title+"</span>"+additionalButtonsHtml+closeButtonHtml+"</div>";
	var container="<div id='flycontents"+id+"' style='padding: 0px 10px 10px 10px; margin:0 4px 4px 4px;overflow:auto;text-align:left;'>";
	var htm=titlebar+container+"</div>";
	$("#fly"+id).html(htm);
	// for empty html, create it with 0 coors
	if(firstTime && js.length)
		runLoading(id,$("#flycontents"+id),1);
	var flyDinDimAndCoors = {x: x, y: y, width: 0, height: 0};
	var loadedContent = $("#loaded_content"+id), flycontents = "";
	if($(loadedContent).length)
	{
		$(loadedContent).empty()
		$(loadedContent).html(html);
		var left = $(loadedContent).css('left');
		var pos = $(loadedContent).css('position');
		if(left!='0px' && pos!='static')
		{
			$(loadedContent).css('position','static');
			$(loadedContent).css('left','0px');
		}
		if (html.length)
			flyDinDimAndCoors = getFlyDivSizeAndCoors(id, x, y);
		if(left!='0px' && pos!='static')
		{
			$(loadedContent).css('position','absolute');
			$(loadedContent).css('left','-10000px');
		}
		flycontents = $(loadedContent)[0];
	}
	else{
			$("#flycontents"+id).empty()
			$("#flycontents"+id).html(html);
			flycontents = $("#flycontents"+id)[0];
			if (html.length)
				flyDinDimAndCoors = getFlyDivSizeAndCoors(id, x, y);
		}
		
	var flydiv=$("#fly"+id)[0];
	$(flydiv).css("top","-10000px");
	
	var color = (cfgObj.border && cfgObj.border.color ? cfgObj.border.color : '') || $("#display_fly"+id).css("background-color");
	$("#display_fly"+id).css("background-color", color);
		
	// add styles and shadow div
	$(document.body).append("<div id='shadow"+id+"' style='position:absolute;display:none;background:#ccc;border:none;opacity:0.4;filter:alpha(opacity=40);left:"+(flyDinDimAndCoors.x+8)+";top:"+(flyDinDimAndCoors.y+8)+";width:"+flyDinDimAndCoors.width+";height:"+flyDinDimAndCoors.height+";'>\r\n"
								+"<table width=100% height=100% border=0 cellpadding=0 cellspacing=0 style='background:#666;'>\r\n"
								+" <tr>\r\n"
								+"  <td width='6' height='6' nowrap='nowrap' style='background:url(images/shadow_up_left.gif) top left no-repeat;'></td>\r\n"
								+"  <td style='background:url(images/shadow_up.gif) top right repeat-x;'></td>\r\n"
								+"	<td width='6' nowrap='nowrap' style='background:url(images/shadow_up_right.gif) top right no-repeat;'></td>\r\n"
								+" </tr>\r\n"
								+" <tr>\r\n"
								+"  <td width='6' nowrap='nowrap' style='background:url(images/shadow_left.gif) top left repeat-y;'>&nbsp</td>\r\n"
								+"  <td>&nbsp</td>\r\n"
								+"  <td width='6' nowrap='nowrap' style='background:url(images/shadow_right.gif) top right repeat-y;'>&nbsp</td>\r\n"
								+" </tr>\r\n"
								+" <tr>\r\n"
								+"  <td width='6' height='6' nowrap='nowrap' style='background:url(images/shadow_down_left.gif) left bottom no-repeat;'></td>\r\n"
								+"  <td height='6' style='background:url(images/shadow_down.gif) left bottom repeat-x;'></td>"
								+"  <td width='6' height='6' nowrap='nowrap' style='background:url(images/shadow_down_right.gif) right bottom no-repeat;'></td>\r\n"
								+" </tr>\r\n"
								+"</table>\r\n");								
	var shadow = $("#shadow"+id)[0];
	var style=$("#style")[0];
	if(!style)
	{
		$(document.body).append("<div id='style'></div>\r\n<style>\r\n"
								+".ui-resizable { position: relative; }\r\n"
								+".ui-resizable-handle { position: absolute; display: none; font-size: 0.1px; }\r\n"
								+".ui-resizable .ui-resizable-handle { display: block; }\r\n"
								+"body .ui-resizable-disabled .ui-resizable-handle { display: none; }\r\n"
								+"body .ui-resizable-autohide .ui-resizable-handle { display: none; }\r\n"
								+".ui-resizable-s { cursor: s-resize; height: 4px; width: 100%; bottom: 0px; left: 0px; background: "+color+" repeat-x scroll left top;border-top:1px solid #434343;}\r\n"
								+".ui-resizable-e { cursor: e-resize; width:4px; right: 0px; top: 23px; height:100%; background: "+color+" repeat-y scroll left top ; border-left:1px solid #434343;}\r\n"
								+".ui-resizable-w { cursor: w-resize; width:4px; left: 0px; top: 23px; height:100%; background: "+color+" repeat-y scroll right top; border-right:1px solid #434343;}\r\n"
								+".ui-resizable-se { cursor: se-resize; width: 4px; height: 4px; right: 0px; bottom: 0px; background: "+color+" no-repeat left top; border-left:1px solid "+color+";}\r\n"
								+".ui-resizable-sw { cursor: sw-resize; width: 4px; height: 4px; left: 0px; bottom: 0px;background: "+color+" no-repeat right top; border-right:1px solid "+color+";}\r\n"
								+"</style>\r\n");
	}
		
	if (html.length)
		setFlyDivDimAndCoors(flyDinDimAndCoors, id);
		
	var flyframe = document.getElementById("fli"+id);
	
	if (control){
		var io = createAddIframe(id,control,field,parId);	
	}
	
	var form=$("form[@name=editform"+id+"]")[0];
	if(js.length)
	{
		if(debug)
		{
			$(document.body).append("<textarea id=txt"+id+" cols=50 rows=10> </textarea>");
			$("#txt"+id).text(js);
		}
		eval(js);
	}
	
	return $(flycontents);
}

function createAddIframe(id,control,field,parId)
{
	//create frame
	var frameId = 'flyframe' + id;
//	iframe already exists - reset load counter only
	if($('#'+frameId).length)
	{
		delete $('#'+frameId).loadCount;
//		delete window.frames[frameId].loadCount;
		return;
	}
	if(window.ActiveXObject)
	{
		var iframetxt='<iframe src="javascript:false;" style="position:absolute;opacity:0;filter:alpha(opacity=0);"'+ 
		'onload="if (typeof this.loadCount == \'undefined\'){this.loadCount = 0;return;} var ioDocument = window.frames[\''+frameId+'\'].document;'+
		'ProcessReturn(ioDocument,\''+control+'\','+id+',\''+field+'\','+parId+');"'+
		'id="' + frameId + '" name="' + frameId + '" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no"/>';
		var io = document.createElement(iframetxt);
	}
	else {
		var io = document.createElement('iframe');
		io.id = frameId;
		io.name = frameId;
		io.src = 'javascript:false;';
		$(io).load(function()
		{
			if (typeof this.loadCount == 'undefined') 
			{
				this.loadCount = 0;
				return;
			}
			var ioDocument = $("#"+frameId).get(0).contentDocument;
			ProcessReturn(ioDocument,control,id,field,parId);
		});
	}
	io.style.position = 'absolute';
	io.style.top = '-1000px';
	io.style.left = '-1000px';
	document.body.appendChild(io);
	return io;
}

function ProcessReturn(doc,control,id,field,parId)
{
	if(debug)
	{
		$(document.body).append("<textarea id=err"+id+" cols=50 rows=10></textarea>");
		$("#err"+id).text(doc.body.innerHTML);
	}
	var pagetype=$("#fly"+id).attr("pagetype");
	var txt;
	if($("#data",doc).length)
		txt = $("#data",doc).text();
	else
		txt="error"+doc.body.innerHTML;
		
	if(txt.substr(0,5)=='added')
	{
		txt=txt.substr(5);
		var blocks=txt.split("\n");
		$.each(blocks,function(i,n){
			blocks[i] = slashdecode(n);
		});

		var fields=blocks[0].split("\n");
		$.each(fields,function(i,n){
			fields[i] = slashdecode(n);
			});
		var tName = window['tName'+parId];
		var Cntrl = Runner.controls.ControlManager.getAt(tName, parId, field);	
		var cntrlType = Cntrl.getControlType();
		if(cntrlType=='select')
		{
			Cntrl.addOption(fields[1],fields[0]);
			Cntrl.setValue([fields[0]],true);
		}
		else{
				Cntrl.setValue(fields[1],fields[0],true);
			}
		RemoveFlyDiv(id,false,'save');
		
	}
	else if(txt.substr(0,5)=='decli')
	{
		txt = txt.substr(5);
		var y = document.getElementById("fly"+id).offsetTop;
		var x = document.getElementById("fly"+id).offsetLeft;
		$("#data",doc).remove();
		RemoveFlyDiv(id,true);
		DisplayFlyDiv($("#html",doc).text(),txt,id,control,x,y,pagetype,"","","","",0);
	}
	else
	{
		txt = txt.substr(5);
		var y = document.getElementById("fly"+id).offsetTop;
		var x = document.getElementById("fly"+id).offsetLeft;
		RemoveFlyDiv(id,true);
		DisplayFlyDiv(txt,"",id,control,x,y,pagetype,"","","","",0);
	}
}

function flydivonclick(div,id)
{
	var shadow=$("#shadow"+id)[0];
	if($.browser.msie)
	{
		div.style.zIndex=++zindex_max;
		shadow.style.zIndex=zindex_max;
		if(IsIE6())
		{
			var fli=$("#fli"+id)[0];
			fli.style.zIndex=zindex_max;
		}		
	}
	else{
			$(div).css("z-index",++zindex_max);
			$(shadow).css("z-index",zindex_max);
		}
}

/**
  * Was mouse down on fly div
  * @var boolean
  */
var fly_mousedown = false;
/**
  * Disparity between offsetLeft by div and offsetLeft by click on div
  * @var integer
  */
var fly_offsetx;
/**
  * Disparity between offsetTop by div and offsetTop by click on div
  * @var integer
  */
var fly_offsety;
/**
  * Div element which was moving
  * @var object
  */
var fly_movingdiv;
/**
  * Init events for moving fly div
  * @var boolean
  */
var fly_initmove = false;
var eYNegSign = false;
/**
  * Calculation of coordinates for moving fly div
  * @param {object} e - event
  * @param {object} div - element fly div
  * @param {integer} id - id of page
  */
function fly_mousedown_func(e,div,id)
{
	if(!e)
		e = window.event;
	
	//Init coordinates foe show fly div
	if(!fly_initmove)
	{
		document.body.onmousemove = function(e)
		{
			var x = 0, y = 0;
			if($.browser.msie)
			{
				e = window.event
				x = e.x;
				y = e.y;
			}
			else{
					x = e.clientX;
					y = e.clientY;
				}
				
			var shadow = $("#shadow"+fly_movingdiv.id.substr(3))[0];
			var scrX = document.body.scrollLeft;
			var scrY = document.body.scrollTop;
			if(fly_mousedown)
			{
				var top = 0, sTop = 8;
			
				if(div.offsetTop > 0 || fly_movingdiv.offsetTop > 0)
				{
					if(y > 0 && !eYNegSign)
						top = y - fly_offsety;
					else if(scrY > 0)
					{
						top = scrY;
						eYNegSign = true;
					}
					else if(eYNegSign)
						top = y;
				
					sTop = top + sTop;
				}
				else if(y > 0)
				{
					top = y;
					sTop = top + sTop;
				}
			
				if($.browser.msie)
				{
					fly_movingdiv.style.left = ""+(x - fly_offsetx)+"px";
					fly_movingdiv.style.top = "" + top + "px";
					shadow.style.left = ""+(x - fly_offsetx + 8)+"px";
					shadow.style.top = "" + sTop + "px";	
				}
				else{
						fly_movingdiv.style.left = (x - fly_offsetx);
						fly_movingdiv.style.top = top;
						$(shadow).css("left",(x - fly_offsetx + 8));
						$(shadow).css("top", sTop);	
					}
					
				if(IsIE6())
				{
					var flyframe = document.getElementById("fli" + fly_movingdiv.id.substr(3));
					flyframe.style.left = "" + (x - fly_offsetx) + "px";
					flyframe.style.top = "" + top + "px";
				}
			
			}
			if(document.body.oldmousemove!=null)
				document.body.oldmousemove();
		}
		
			
		document.body.onmouseup = function()
		{
			fly_mousedown = false;
			eYNegSign = false;
		}
		
		fly_initmove = true;
	}

	fly_mousedown = true;
	if($.browser.msie)
	{
		fly_offsetx = e.x - div.offsetLeft;
		if(div.offsetTop!=0 && div.offsetTop > 0)
		   fly_offsety = e.y - div.offsetTop;
		else if(e.clientY!=0 && e.y > 0)
		        fly_offsety = e.y;
		else fly_offsety = 0;
	}
	else
	{ 
		fly_offsetx = e.clientX - div.offsetLeft;
		
		if(div.offsetTop!=0 && div.offsetTop > 0)
		   fly_offsety = e.clientY - div.offsetTop;
		
		else if(e.clientY!=0 && e.clientY > 0)
		        fly_offsety = e.clientY;
		
		else fly_offsety = 0;
	}
	fly_movingdiv = div;	
}

var onthefly_included=true;
