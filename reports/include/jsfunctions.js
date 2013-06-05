var jsfunctions_included=true;
var flyid=1;
var cookieRoot = "";


// ----- start error handling functions ------
window.onerror = function (errMsg, url, lineNum){
	
	if (!window.debugMode){
		window.onerror = function(errMsg, url, lineNum){
			stopLoadingForError();
			return true;
		}		
	}else{		
		var errArea = document.createElement("textarea");	
		$(errArea).appendTo("body").attr("cols", "60").attr("rows", "15");
		
		window.onerror = function(errMsg, url, lineNum){
			stopLoadingForError();
			var errTxt = "ErrMsg: " + errMsg + ".\nURL = " + url + ";\nLineNum: " + lineNum + ";";	
			if ($(errArea).text().length){
				errTxt = $(errArea).text() + "\r\n-------------------------------\r\n" + errTxt;
			}
			$(errArea).text(errTxt);		
			return true;
		}	
	}
	window.onerror(errMsg, url, lineNum);
	return true;
}


function getElementsByName_iefix(tag, name) 
{    
	var elem = document.getElementsByTagName(tag);
	var arr = new Array();
	for(i = 0,iarr = 0; i < elem.length; i++){
		att = elem[i].getAttribute("name");
		if(att == name){
			arr[iarr] = elem[i];
			iarr++;
		}
	}
	return arr;
}

/**
 * Stop showing loading if happend error on page 
 */
function stopLoadingForError()
{
	var lDivs, lConts;
	if(navigator.appName == "Microsoft Internet Explorer"){
		lDivs = getElementsByName_iefix("div","loadingDiv");
		lConts = getElementsByName_iefix("div","loadedContent");
	}else{	
		lDivs = document.getElementsByName("loadingDiv");
		lConts = document.getElementsByName("loadedContent");
	}
	for(i=0;i<lDivs.length;i++)
		lDivs[i].parentNode.removeChild(lDivs[i]);
		
	for(j=0;j<lConts.length;j++)	
	{
		if($(lConts[j]).css("position")!="static")
		{
			$(lConts[j]).css("position","static");
			$(lConts[j]).css("left","0px");
		}	
	}
}

// ----- end error handling functions ------

/**
 * Remove class buttonbotder for chrome 
 * because it doesn't correct show outline style
function setButtonBorderStyle()
{
	if($.browser.safari)
		$("span.buttonborder").removeClass("buttonborder");
}
*/

/**
 * Set Tag Name
 */
$.fn.tagName = function() 
{
    return this.get(0).tagName.toLowerCase();;
}
/**
 * Cross browser element left absolute coordinate
 * @param {DOM element} eElement
 */
function DL_GetElementLeft(eElement){
	
   if (!eElement && this)                    // if argument is invalid
   {                                         // (not specified, is null or is 0)
      eElement = this;
	                        // and function is a method
   }
	
   var DL_bIE = document.all ? true : false; // initialize var to identify IE

   var nLeftPos = eElement.offsetLeft;       // initialize var to store calculations
   var eParElement = eElement.offsetParent;  // identify first offset parent element

   while (eParElement != null)
   {                                         // move up through element hierarchy
      if(DL_bIE){
         if(eParElement.tagName == "TD")     // if parent a table cell, then...
         {
            nLeftPos += eParElement.clientLeft; // append cell border width to calcs
         }
      }

      nLeftPos += eParElement.offsetLeft;    // append left offset of parent
      eParElement = eParElement.offsetParent; // and move up the element hierarchy

   }                                         // until no more offset parents exist
   return nLeftPos;                          // return the number calculated
}
/**
 * Cross browser element top absolute coordinate
 * @param {DOM element} eElement
 */
function DL_GetElementTop(eElement)
{
	
   if (!eElement && this)                    // if argument is invalid
   {                                         // (not specified, is null or is 0)
      eElement = this;                         // and function is a method
   }                                // identify the element as the method owner

   var DL_bIE = document.all ? true : false; // initialize var to identify IE

   var nTopPos = eElement.offsetTop;       // initialize var to store calculations
   var eParElement = eElement.offsetParent;  // identify first offset parent element

   while (eParElement != null)
   {                                         // move up through element hierarchy
      if(DL_bIE)
      {
         if(eParElement.tagName == "TD")     // if parent a table cell, then...
         {
            nTopPos += eParElement.clientTop; // append cell border width to calcs
         }
      }

      nTopPos += eParElement.offsetTop;    // append top offset of parent
      eParElement = eParElement.offsetParent; // and move up the element hierarchy

   }                                         // until no more offset parents exist
   return nTopPos;                          // return the number calculated
}

/**
 * Get client viewport dimensions
 */
function getWindowDimensions() 
{
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) 
  {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  return [ myWidth, myHeight ];
}

/**
 * Gets cross browser window scrolling
 */
function getScrollXY() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}

function findCookieRoot()
{
	var cutFrom = document.location['pathname'].indexOf('/', 1);
	cookieRoot = document.location['pathname'].substr(0,(cutFrom+1));
}

// add opened menu id to cookie
function addOpenMenuItemIdToCookie(menuItemId)
{
	var openMenuItemIds = get_cookie('openMenuItemIds');
	if (openMenuItemIds)
	{
		if (openMenuItemIds.indexOf(menuItemId) == -1)
			openMenuItemIds += ";"+menuItemId;	
				
	}
	else
		openMenuItemIds = menuItemId;
	set_cookie('openMenuItemIds', openMenuItemIds, '', cookieRoot, '', '' );
	toggleExpandCollapse();
}
// remove opened menu id from cookie
function removeOpenMenuFromCookie(menuItemId)
{
	var openMenuItemIds = get_cookie('openMenuItemIds');
	if (openMenuItemIds)
	{
		openMenuItemIds = openMenuItemIds.replace((";"+menuItemId), "");
		openMenuItemIds = openMenuItemIds.replace(menuItemId, "");
		if(openMenuItemIds.indexOf(';')==0)
			openMenuItemIds = openMenuItemIds.substr(1,openMenuItemIds.length);
		set_cookie('openMenuItemIds', openMenuItemIds, '', cookieRoot, '', '' );
	}
	setTimeout("toggleExpandCollapse()",500);
}

// expand/collapse button control
function toggleExpandCollapse()
{
	var visibleLength = $("#mainmenu_block").find("ul[@id^=u]:visible").length,
	hiddenLength = $("#mainmenu_block").find("ul[@id^=u]:hidden").length;
	if (visibleLength == 0 && hiddenLength > 0 && $("img.pmimg").attr("src")=="include/img/plus.gif")
	{
		$('a.plus_minus').empty();
		$('img.pmimg').attr('src','include/img/plus.gif');
		$('a.plus_minus').append('<img src=\"include/img/plus.gif\" border=0> &nbsp;&nbsp;'+TEXT_EXPAND_ALL);	
	}
	else if (visibleLength != 0 && hiddenLength == 0 && $("img.pmimg").attr("src")=="include/img/minus.gif")
	{
		$('a.plus_minus').empty();
		$('img.pmimg').attr('src','include/img/minus.gif');
		$('a.plus_minus').append('<img src=\"include/img/minus.gif\" border=0> &nbsp;&nbsp;'+TEXT_COLLAPSE_ALL);
	}
}

// open menus on page load
function openMenuItemsOnLoad()
{
	findCookieRoot();
	var openMenuItemIds = get_cookie('openMenuItemIds');
	if (openMenuItemIds) 
	{
		var itemForOpenArr = openMenuItemIds.split(";");
		for (var i = 0; i < itemForOpenArr.length; i++) 
		{
			var itemId = itemForOpenArr[i];
			// show sometimes conficts with slideDown in Vmenu2()
			$("#"+itemId).slideDown(1);
			var par = $("#"+itemId).parent();
			var parc = $('a.current').parent();
			if($('a.current',par).length && $(par).attr('id')!=$(parc).attr('id'))
				$(par).find('a:first').css('color',window.colorlink);
			$(par).find('img:first').attr("src", "include/img/minus.gif");		
		}
	}
	toggleExpandCollapse();
}

//	For vertical menu on page
function Vmenu1(rOrder)
{
	window.Max=0; 
	$('.Vmenu1 ul li').hover(
	function()
	{
		flag = 1;
		var left = 1, top = 2, vscroll = 0;
		if($(this).attr('class')!='hr')
			$(this).addClass('menu_active');
		ul = $(this).find('ul:first:has(li)');
		if($(ul).length)
		{
			$(ul).css('display','block');
			if(rOrder=='RTL' && $.browser.msie)
			{
				$(ul).find('li[@class=hr][@parent='+$(ul).attr('id')+']').each(function()
				{
					$(this).hide();
				});
			}
			ulW = $(ul)[0].offsetWidth;
			if($(this).attr('view')=='topitem')
			{
				el = getAbsolutePosition(this,1);
				if(rOrder=='RTL')
					window.Max = el.l - ulW;
				else
					window.Max = el.l + el.w;
				if(!$.browser.msie)
				{
					if(rOrder=='RTL')
						window.Max = window.Max + 10;
					else
						window.Max = window.Max - 10;
				}
				window.first = 0;
			}
			if(!$.browser.msie && ($(ul)[0].offsetHeight + el.t) > document.body.clientHeight)
				vscroll = 10;
			if(!window.first)
			{
				$(ul).css('left',''+(window.Max - vscroll)+'px');	
				$(ul).css('top',''+(el.t)+'px');
			}
			else{
					$(ul).css('top',''+$(this)[0].offsetTop+'px');
					if(rOrder=='RTL')
						$(ul).css('right',''+($(this)[0].offsetWidth - vscroll)+'px');
					else
						$(ul).css('left',''+($(this)[0].offsetWidth - vscroll)+'px');
				}
			if(rOrder=='RTL')
			{
				left = -2;
				if($.browser.msie)
				{
					$(ul).find('li[@class=hr][@parent='+$(ul).attr('id')+']').each(function()
					{
						$(this).css('width',''+ulW+'px');
						$(this).show();
					});
					if(!window.first)
					{
						var idPar = $('div.Vmenu1').parent().parent().attr('id');
						if(!idPar || idPar.indexOf('left_block')==-1)
						{
							left = -21;
							top = 0;
						}
					}	
				}
			}
			window.first=1;
			$(ul).dropShadow({left: left, top: top, blur: 2, opacity: 0.4})
			setTimeout('if(flag) $(ul).redrawShadow()',150);
			if(IsIE6())
				addiframe($(this));
		}
	},
	function()
	{            
		flag=0;
		if($(this).attr('class')!='hr')
			$(this).removeClass('menu_active'); 
		ul = $(this).find('ul:first:has(li)');
		if($(ul).length)
		{
			$(ul).css('display','none'); 
			$(ul).removeShadow();
			var id = $(this).attr('id');
			id = id.substr(2);
			if($('#ul'+id).length)
				$("#frame_menu"+id).remove();
		}
	});    
	$('.Vmenu1 li:has(ul:has(li))').find('a:first').each(function()
	{
		if(!$('b',this).length)
		{
			$(this).after('<b class="bmenu bmenu_simple">&nbsp;&raquo;</b>');
			$("b.bmenu_simple").css("color",$(this).css("color"));
		}	
	});
	$('.Vmenu1 li[@view=topitem]:has(ul:has(a.current))').find("b:first").append("&nbsp;<b class='bmenu_current'>"+$("a.current").attr('title')+"</b>");
	$("b.bmenu_current").css("color",$("a.current").css("color"));
	$('.Vmenu1 ul li ul').addClass('submenu');
}
//	For vertical tree-like menu on page
function Vmenu2()
{
	window.colorlink = $("a.tablelinkssearch").css("color");
	$('.Vmenu2 ul li a').hover(
	function()
	{
		if($(this).parent().attr('class')!='hr')
			$(this).addClass('menu_active');
	},
	function()
	{
		if($(this).parent().attr('class')!='hr')
			$(this).removeClass('menu_active'); 
	}); 
	$('.Vmenu2 ul li ul').hide();
	$('li[@view=topitem]:has(ul:has(a.current))').find('a:first').css('color',$("a.current").css("color"));
	$('.Vmenu2 ul li:has(ul) span').bind('click',function()
	{
		var par = $(this).parent();
		var parc = $('a.current').parent();
		$(par).find('ul:first').slideToggle(); 
		if ($(this).find('img.pmimg').attr('src') == 'include/img/plus.gif') 
		{
			// add to cookie opened menu
			addOpenMenuItemIdToCookie($(par).find('ul:first').attr("id"));
			$(this).find('img.pmimg').attr('src', 'include/img/minus.gif');
			if($('a.current',par).length && $(par).attr('id')!=$(parc).attr('id'))
				$(par).find('a:first').css('color',window.colorlink);
			$(par).find('ul:has(a.current)').each(function(parc)
			{
				$(this).parent().find('img.pmimg:first').attr('src','include/img/minus.gif');
				if($('a.current',this).length && $(this).parent().attr('id')!=$(parc).attr('id'))
					$(this).parent().find('a:first').css('color',window.colorlink);
				addOpenMenuItemIdToCookie($(this).attr("id"));
				$(this).slideDown();
			});
		}
		else{
				// remove from cookie opened menu
				removeOpenMenuFromCookie($(par).find('ul:first').attr("id"));
				// remove all children from cookie
				$(par).find('ul').each(function()
				{
					removeOpenMenuFromCookie($(this).attr("id"));
				});
				$(this).find('img.pmimg').attr('src', 'include/img/plus.gif');
				if($('a.current',par).length && $(par).attr('id')!=$(parc).attr('id'))
					$(par).find('a:first').css('color',$("a.current").css("color"));
			}
		return false;
	});
	if($('.Vmenu2 li[@view=topitem]:has(ul)').length && $('.Vmenu2 ul:first').length)
	{
		$('.Vmenu2_links').css('display','block');
		$('a.plus_minus').click(function()
		{
		   if(flag)
		   {
				$(this).parent().parent().find('ul li ul').slideUp('slow');
				$('a.plus_minus').empty();
				$('img.pmimg').attr('src','include/img/plus.gif');
				$('a.plus_minus').append('<img src=\"include/img/plus.gif\" border=0> &nbsp;&nbsp;'+TEXT_EXPAND_ALL);
				flag = 0;
				// on collapse all, remove all ids from cookie
				delete_cookie('openMenuItemIds', cookieRoot, '');
				$(this).parent().parent().find('li:has(ul:has(a.current))').each(function()
				{
					$(this).find('a:first').css('color',$("a.current").css("color"));
				});
			}
		   else{
					$(this).parent().parent().find('ul li ul').slideDown('slow');
					$('a.plus_minus').empty();
					$('img.pmimg').attr('src','include/img/minus.gif');
					$('a.plus_minus').append('<img src=\"include/img/minus.gif\" border=0> &nbsp;&nbsp;'+TEXT_COLLAPSE_ALL);
					flag = 1;
					// on expand all add all ids to cookie
					$(this).parent().parent().find('ul li ul').each(function()
					{									
						addOpenMenuItemIdToCookie($(this).attr("id"));	
					});
					$(this).parent().parent().find('li:has(ul:has(a.current))').each(function()
					{
						$(this).find('a:first').css('color',window.colorlink);
					});	
				}
			return false; 
		});
	}	
	if($.browser.msie)
		$('.Vmenu2 ul li').css('padding','5px 0');	
}
//	For gorizonal menu on page	
function Gmenu(rOrder)
{
	window.Max = 0; 
	window.UlMax = 0;
	window.LiMax = 0;
	window.first = 0;
	$('.Gmenu ul li').hover(
	function()
	{
		flag = left = 1;
		if(!$(this).find('.main_item').length && $(this).attr('class')!='hr')
			$(this).addClass('menu_active');
		ul = $(this).find('ul:first:has(li)');
		if($(ul).length)
		{
			window.UlMax=0;
			if($(this).attr('view')=='topitem')
			{
				el = getAbsolutePosition(this,1);
				window.LiMax = el.w;
				window.first = 0;
			}
			$(ul).css('display','block');
			$(ul).find('li[@parent='+($(ul).attr('id'))+'] a').each(function()
			{
				if(window.UlMax < $(this)[0].offsetWidth)
					window.UlMax = $(this)[0].offsetWidth
			});
			if(!window.first && window.UlMax < window.LiMax)
				window.Max = window.LiMax;
			else
				window.Max = window.UlMax;
			window.Max = window.Max + 20;	
			$(ul).css('width',''+window.Max+'px');
			if(!window.first)
			{
				if(rOrder=='RTL')
					$(ul).css('left',''+(el.l + (el.w - $(ul)[0].offsetWidth))+'px');
				else
					$(ul).css('left',''+(el.l)+'px');
				$(ul).css('top',''+(el.t + el.h - 1)+'px');
			}
			else
				$(ul).css('top',''+$(this)[0].offsetTop+'px');
			$(ul).find('li[@parent='+($(ul).attr('id'))+']').each(function()
			{
				$(this).css('width',''+window.Max+'px');
				if(rOrder=='RTL')
					$(this).find('ul:first').css('right',''+window.Max+'px');
				else
					$(this).find('ul:first').css('left',''+window.Max+'px');
			});
			if(rOrder=='RTL')
			{
				if($.browser.msie && !window.first)
					left = -21;
				else
					left = -2;
			}
			window.first = 1;
			$(ul).dropShadow({left: left, top: 2, blur: 2, opacity: 0.4});
			setTimeout('if(flag) $(ul).redrawShadow()',150);
			if(IsIE6())
				addiframe($(this));
		}	
	},
	function() 
	{
		flag=0;
		if(!$(this).find('.main_item').length && $(this).attr('class')!='hr')
			$(this).removeClass('menu_active'); 
		$(this).find('ul:first').css('display','none'); 
		$(this).find('ul:first').removeShadow();
		var id = $(this).attr('id');
		id = id.substr(2);
		if($('#ul'+id).length)
			$("#frame_menu"+id).remove();
	});	
	$('.Gmenu li:has(ul:has(li))').find('a:first').each(function()
	{
		if(!$('b',this).length)
		{	
			$(this).after('<b class="bmenu bmenu_simple">&nbsp;&raquo;</b>');
			$("b.bmenu_simple").css("color",$(this).css("color"));
		}
	});
	$('.Gmenu li[@view=topitem]:has(ul:has(a.current))').find("b.bmenu:first").append("&nbsp;<b class='bmenu_current'>"+$("a.current").attr('title')+"</b>");
	$("b.bmenu_current").css("color",$("a.current").css("color"));
	var top=0;
	if($(".Madrid").length)
	{
		if(IsIE6())
		{
			$('.Madrid b.xtop').each(function()
			{
				$(this).attr("style","height:1px;width:"+($(this).parent()[0].offsetWidth)+"px");
			});
			$('.Gmenu').parent().append('&nbsp;');
		}
	}
	else if($(".Paris").length)
	{
		$('.Gmenu').parent().css('height','1px');
		if($.browser.msie)
			$('.Gmenu').parent().append('&nbsp;');
	}
	else if($('#menu_block>div.Gmenu').length)
	{
		var h=0, w=0;
		var mtW = $('div.Gmenu')[0].offsetWidth;
		$('div.Gmenu li[@view=topitem]').each(function()
		{
			w += $(this)[0].offsetWidth;
			if(w>mtW)
			{
				h += $(this)[0].offsetHeight;
				w=$(this)[0].offsetWidth;
			}
		});
		$('#menu_block').css('height',''+(h+19)+'px');
	}	
	$('.Gmenu ul li ul li ul').css('top','0px');
}

//for add iframe for menu for IE6
function addiframe(elem)
{
	var id = $(elem).attr('id');
	id = id.substr(2);
	if($('#ul'+id).length)
	{
		var w = $('#ul'+id)[0].offsetWidth;
		var h = $('#ul'+id)[0].offsetHeight;
		var l = $('#ul'+id)[0].offsetLeft;
		var t = $('#ul'+id)[0].offsetTop;
		$(elem).append('<iframe src="javascript:false;" id="frame_menu'+id+'" frameborder="1" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no" style="left:'+l+'px;top:'+t+'px;width:'+w+'px;height:'+h+'px;background:white;position:absolute;display:block;opacity:0;filter:alpha(opacity=0);" > </iframe>');
	}		
}

function IsIE6()
{
	var browserName=navigator.appName;
	var browserVer=parseInt(navigator.appVersion); 
	if (browserName=="Microsoft Internet Explorer" && browserVer<7)
		return true;
	return false;
}

//for correct height of list relative to left block
function correctListHeight(mode)
{	
	/*var layout = $('#height1001').attr('layout');
	var lblock, rblock;
	if(layout=='London')
	{
		rblock = $('#right_block1')[0];
		if(mode=='inline')
			lblock = $('#grid_block1')[0];
		else
			lblock = $('#left_block1')[0];
	}
	else if(layout=='Rome')
	{
	
	}
	else
		return;
	
	if(lblock && rblock)
	{
		var lefth = lblock.offsetHeight;
		var righth = rblock.offsetHeight;
		if((lefth)>(righth+50))
		{
			$('#right_block').css('height',''+(lefth)+'px');
			//$('div.list_table1').css('height',''+(lefth-3)+'px');
			//$('div.list_table').css('height',''+(lefth-6)+'px');
		}
	}	*/
}

//get coordinat of element 
function getAbsolutePosition(el,i) 
{
	// for first call
	i = (i === 'undefined') ? i=1 : i;
	
	var r = {l: el.offsetLeft, t: el.offsetTop, w: el.offsetWidth, h: el.offsetHeight };
	//Get borders
	var bl = parseInt(jQuery.css(el, 'borderLeftWidth')) || 0;
	var bt = parseInt(jQuery.css(el, 'borderTopWidth'))  || 0;
	var tagName = $(el).tagName();
	//All browses don't add table's cellpadding
	if(tagName=='table')
	{
		if($(el).attr('cellpadding'))
		{
			r.l -= $(el).attr('cellpadding');
			r.t -= $(el).attr('cellpadding');
		}
	}	
	// Safari do not add the border for the element
	if(jQuery.browser.safari) 
	{
		r.l += bl;
		r.t += bt;
	}
	// Mozilla and IE don't add the border if 'td' has it
	else if (jQuery.browser.mozilla || jQuery.browser.msie) 
	{
		if(tagName=='td')
		{
			r.t += bt;
			r.l += bl;
		}
		// Mozilla removes the border if the parent has overflow property other than visible
		if (jQuery.browser.mozilla && i>1 && jQuery.css(el, 'overflow') != 'visible') 
		{
			r.l += bl;
			r.t += bt;
		}
	}
	if (el.offsetParent && $(el).css('position')!='absolute')
	{
		if($(el.offsetParent).css('position')!='absolute')
		{
			if (tagName == 'body' || tagName == 'html')
			{
				//Delete padding
				r.l -= parseInt(jQuery.css(el, 'paddingLeft')) || 0;
				r.t -= parseInt(jQuery.css(el, 'paddingTop'))  || 0;
			}
			else
				var tmp = getAbsolutePosition(el.offsetParent,i++);
			r.l += tmp.l;
			r.t += tmp.t;
		}
	}
	return r;
}



//	To set my added function on event \"onChange\" on the first place
jQuery.fn.bindFirst = function(evt, fn)
{
	var events = $(this).data('events');
	var handlers = [];
	for(var type in events) 
	{
		if(type == evt) 
		{
			for(var guid in events[type])
				handlers.push(events[type][guid]);
			$(this).unbind(evt);
			$(this).bind(evt,function()
			{
				fn();
				for(var i = 0; i < handlers.length; i++)
					handlers[i]();
			});
			break;
		}
	}
	return $(this);
}
//	For multiple sorting 
function sort(e,url,useAjax,id)
{
	var ctrlPressed = 0;
	if(parseInt(navigator.appVersion) > 3)
	{
		if (navigator.appName == "Netscape")
		{
			var ua = navigator.userAgent;
			var isFirefox = (ua != null && (ua.indexOf("Firefox/") != -1 || ua.indexOf("Chrome/") != -1));
			if ((!isFirefox && getNNVersionNumber() >= 6) || isFirefox) 
				ctrlPressed = e.ctrlKey;
			else 
				ctrlPressed = ((e.modifiers+32).toString(2).substring(3,6).charAt(1)=="1");
		}
		else ctrlPressed = event.ctrlKey;
		if (ctrlPressed) 
		{
			url = url + '&ctrl=1';
			if(useAjax){	
				window.frames['flyframe' + id].location = url; 
				runLoading(id,getParentTableObj(id),4);
			}else{
				var newPage = "<scr" + "ipt language=\"JavaScript\">setTimeout(\'window.location.href=\"" + url + "&ctrl=1\"\', 1);</scr" + "ipt>";
				document.write(newPage);
				document.close();} 
			return false;
		}
	}
	return true;
}

var flag_hint=0;
var hspan = false;
//	To add hints for multiple sorting
function addspan(e)
{
	if($.browser.msie)
	{
		y_ = e.y;
		x_ = e.x;
	}
	else
	{
		y_ = e.clientY;
		x_ = e.clientX;
	}
	flag_hint=1;	
	hspan = $(document.body).find("span.hover_span")[0];
	timespan(x_,y_);
}	
//	To  show hints it may take some time
function timespan(x_,y_)
{
	if(!hspan) 
	{
		$(document.body).append('<span class=\'hover_span\' style=\'position:absolute;display:none;font-size:9px;border:solid 1px #747474;background-color:#ffffe1;color:#000;white-space:pre;padding:2px;width:170;z-Index:1000;\'><b>'+window.TEXT_CTRL_CLICK+'</b></span>');
		hspan = $("span.hover_span")[0];
	}
	if ($.browser.mozilla)
	{
		clientHeight=window.innerHeight;
		clientWidth=window.innerWidth;
	}
	else{
			clientHeight=document.body.clientHeight;
			clientWidth=document.body.clientWidth;
		}
	left=false;
	right=false;
	w_ = hspan.offsetWidth;
	h_ = hspan.offsetHeight; 
	if(x_ + w_ > clientWidth)
	{
		x_ = clientWidth - w_;
		left=true;
	}
	if(y_ + h_ > clientHeight)
	{
		y_ = clientHeight - h_;
		right=true;
	}
	if(left&&right)
		y_ = clientHeight - h_;
	x_ = x_ + document.body.scrollLeft;
	y_ = y_ + document.body.scrollTop + 20;
	$(hspan).css("left", "" + x_ + "px");
	$(hspan).css("top", "" + y_ + "px");
	if(flag_hint) $(hspan).css("display", "inline");
}
//	To del hints for multiple sorting
function delspan()
{
	if(hspan)
	{
		$(hspan).css("display","none");
		flag_hint=0;
	}	
}
//	To moving hints for multiple sorting
function movespan(e)
{	
	if(hspan)
	{
		if( hspan.style.display != "inline" )
			return false;
		hspan.style.left = ( e.clientX || e.x ) + document.body.scrollLeft;
		hspan.style.top  = ( e.clientY || e.y ) + 20 + document.body.scrollTop;
	}
}


function RunSearch(pid)
{
	var form,id='';
	if(pid){
		id=pid;
		form=document.forms['frmSearch'+id];
	}else{
		form=document.forms.frmSearch;
	}
	form.a.value = 'search'; 
	form.SearchFor.value = document.getElementById('ctlSearchFor'+id).value; 
	// value from field for search dropdown
	if(document.getElementById('ctlSearchField'+id)!=undefined){
		form.SearchField.value = $('#ctlSearchField'+id).val();
	// if not defined, choose anyfield
	}else{
		form.SearchField.value = '';
	}
	if(document.getElementById('ctlSearchOption'+id)!=undefined){
		form.SearchOption.value = $('#ctlSearchOption'+id).val(); 
	}else{
		form.SearchOption.value = "Contains";
	}
	form.submit();
}


function GetGotoPageUrlString (nPageNumber,sUrlText)
{
	return "<a href='JavaScript:GotoPage(" + nPageNumber + ");' style='TEXT-DECORATION: none;'>" + sUrlText 
	+ "</a>";
}

function WritePagination(mypage, maxpages, id)
{
	var paginationHTML = "";
	
	if (maxpages > 1 && mypage <= maxpages)
	{
		paginationHTML += "<table rows='1' cols='1' align='center' width='auto' border='0'>"; 
		paginationHTML += "<tr valign='center'><td align='center'>";
 
		var counterstart = mypage - 9; 
		if (mypage%10) 
			counterstart = mypage - (mypage%10) + 1; 
 
		var counterend = counterstart + 9; 
		if (counterend > maxpages) 
			counterend = maxpages; 
 
		if (counterstart != 1)
			paginationHTML += GetGotoPageUrlString(1,TEXT_FIRST)+"&nbsp;:&nbsp;"+GetGotoPageUrlString(counterstart - 1,TEXT_PREVIOUS)+"&nbsp;";
 
		paginationHTML += "<b>[</b>";
		
		var pad="";
		var counter	= counterstart;
		for(;counter<=counterend;counter++)
		{
			if (counter != mypage) 
				paginationHTML += "&nbsp;" + GetGotoPageUrlString(counter,pad + counter);
			else 
				paginationHTML += "&nbsp;<b>" + pad + counter + "</b>";		
		}
		paginationHTML += "&nbsp;<b>]</b>";
		
		if (counterend != maxpages) 
			paginationHTML += "&nbsp;" + GetGotoPageUrlString (counterend + 1,TEXT_NEXT) + "&nbsp;:&nbsp;" + GetGotoPageUrlString(maxpages,TEXT_LAST);
						
		paginationHTML += "</td></tr></table>";
		$('#pagination'+id).html(paginationHTML);
	}
}


    var rowWithMouse = null;

    function gGetElementById(s) {
      var o = (document.getElementById ? document.getElementById(s) : document.all[s]);
      return o == null ? false : o;
    }

    function rowUpdateBg(row, myId) 
    {
        row.className = (row == rowWithMouse) ? 'rowhighlited' : ( (myId&1) ? '' : 'shade' );
    }

    function rowRollover(myId, isInRow) {
      // myId is our own integer id, not the DOM id
      // isInRow is 1 for onmouseover, 0 for onmouseout
      var row = document.getElementById('tr_' + myId);
      rowWithMouse = (isInRow) ? row : null;
      rowUpdateBg(row, myId);
    }



function BuildSecondDropDown(arr, SecondField, FirstValue)
{
	document.forms.editform.elements[SecondField].selectedIndex=0;

	document.forms.editform.elements[SecondField].options[0]=new Option(TEXT_PLEASE_SELECT,'');

	var i=1;
	for(ctr=0;ctr<arr.length;ctr+=3)
	{
		if (FirstValue.toLowerCase() == arr[ctr+2].toLowerCase())
		{
			document.forms.editform.elements[SecondField].options[i]=new Option(arr[ctr+1],arr[ctr]);
			i++;
		}
	}
	document.forms.editform.elements[SecondField].length=i;
	if(i<3 && i>1 && !bLoading)
		document.forms.editform.elements[SecondField].selectedIndex=1;
	else
		document.forms.editform.elements[SecondField].selectedIndex=0;
}

function SetSelection(FirstField, SecondField, FirstValue, SecondValue, arr)
{
	var ctr;

	BuildSecondDropDown(arr, SecondField, FirstValue);	 
	if(SecondValue=="" && document.forms.editform.elements[SecondField].length<3)
		return;
	for (ctr=0; ctr<document.forms.editform.elements[SecondField].length; ctr++)
	 if (document.forms.editform.elements[SecondField].options[ctr].value.toLowerCase() == SecondValue.toLowerCase() )
	 	 {
		  document.forms.editform.elements[SecondField].selectedIndex = ctr;
		  break;
		 }
}
function padDateValue(value,threedigits)
{
	if(!threedigits)
	{
		if(value>9)
			return ''+value;
		return '0'+value;
	}
	if(value>9)
	{
		if(value>99)
			return ''+value;
		return '0'+value;
	}
	return '00'+value;
}

function getTimestamp()
{
	var ts = "";
	var now = new Date();
	ts += now.getFullYear();
	ts+=padDateValue(now.getMonth()+1,false);
	ts+=padDateValue(now.getDate(),false)+'-';
	ts+=padDateValue(now.getHours(),false);
	ts+=padDateValue(now.getMinutes(),false);
	ts+=padDateValue(now.getSeconds(),false);
	return ts;
}

function addTimestamp(filename)
{
	var wpos=filename.lastIndexOf('.');
	if(wpos<0)
		return filename+'-'+getTimestamp();
	return filename.substring(0,wpos)+'-'+getTimestamp()+filename.substring(wpos);
}

function create_option( theselectobj, thetext, thevalue ) 
{
theselectobj.options[theselectobj.options.length]= new Option(thetext,thevalue);
}

function SetToFirstControl(name)
{
try {
	if(name)
	{
	    var form=document.forms[name];
		for(i=0; i < form.elements.length; i++)
		{
			if (form.elements[i].type == "hidden" || form.elements[i].disabled)
		  		continue;
	   	    form.elements[i].focus();
			break;
		}
		return;
	}
	var bFound = false;
	for (f=0; !bFound && f<document.forms.length; f++)
	{
	    var form=document.forms[f];
	    for(i=0; i < form.elements.length; i++)
	    {
			if (form.elements[i].type == "hidden" || form.elements[i].disabled)
		  		continue;
			form.elements[i].focus();
	        var bFound = true;
			break;
		}
    }
} catch(er) {} 
}

function slashdecode(str)
{
	var out = new String();
	var pos = 0;
	for ( var i = 0; i < str.length - 1; i++ )
	{
		var c = str.charAt(i);
		if( c == '\\' )
		{
			out += str.substr(pos,i-pos);
			pos = i + 2;
			var c1 = str.charAt(i+1);
			i++;
			if ( c1 == '\\' ) {
				out += "\\";
			} else if ( c1 == 'r' ) {
				out += "\r";
			} else if ( c1 == 'n') {
				out += "\n";
			} else {
				i--;
				pos-=2;
			}
		}
	}
	if ( pos < str.length )
		out += str.substr(pos);
	
	return out;
}

var zindex_max=1;

/////////////////////////////////////////////////////////////
function print_time(h, m, s, format, hIsAM, convention, am, pm)
{
	var res = format;
	var isAM = hIsAM;
	
	if(convention == 12 && h > 12)
	{
		h -= 12;
		isAM = false;
	}
	
	var hh = parseInt(h);
	if(isNaN(hh))
		hh = 0;	
	var mm = parseInt(m);
	if(isNaN(mm))
		mm = 0;	
	var ss = parseInt(s);
	if(isNaN(ss))
		ss = 0;
	if(hh < 10)
		hh = "0" + hh.toString();
	if(mm < 10)
		mm = "0" + mm.toString();
	if(ss < 10)
		ss = "0" + ss.toString();
		
	res = res.replace(/mm/, mm);
	res = res.replace(/ss/, ss);
	res = res.replace(/m/, m);
	res = res.replace(/s/, s);
	
	res = res.toLowerCase();
	if(convention == 12)
	{
		var t = isAM ? am : pm;
		res = res.replace(/hh/, hh);
		res = res.replace(/h/, h);
		res = res.replace(/t+/, t);
	}
	else
	{
		res = res.replace(/hh/, hh);
		res = res.replace(/h/, h);
	}
	
	return res;	
}
///////////////////////////////
// Unlocking record
///////////////////////////////
function UnlockRecord(page,keys,sid,func)
{
    params={
            action: 'unlock',
			keys: escape(keys),
			sid: sid,
			rndval: Math.random()
		};
    var xmlhttp = $.get(page,	params,
		function(xml)
		{
    	}
	);
	if(!func)
	    return;
    var mscount=0;
	tfunc=function()
	{
        if(mscount>=300 || xmlhttp.readyState>=2)
        {
            func();
            return;
        }
        mscount+=5;
        setTimeout(tfunc,5);
	}
	tfunc();
}

function ConfirmLock(page,table,keys,id,inline)
{
    params={
            action: 'confirm',
			keys: escape(keys),
			rndval: Math.random()
		};
    $.get(page,	params,
		function(xml)
		{
		    if(xml!='')
		    {
		        if(inline=='1')
		            inlineEditing.setInputContent('lock'+xml,id,'');
		        var arrCntrl = Runner.controls.ControlManager.getAt(table);
	            for(var i=0;i<arrCntrl.length;i++)
	                arrCntrl[i].setDisabled();
		        $('#system_div'+id).html(xml);
		        $('#center_block'+id).css('margin-top','60px');
	            $('#system_div'+id).css('visibility','visible');
        	    $('#savebutton'+id).attr('disabled','true');
	            $('#savebutton'+id).css('background-color','#dcdcdc');
			    clearInterval(window.timeid);
		    }
    	}
	);
}

function UnlockAdmin(page,table,keys,oldid,startEdit,pageid)
{
    params={
            action: 'lockadmin',
			keys: escape(keys),
			startEdit:startEdit,
			oldid: oldid,
			rndval: Math.random()
		};
    $.get(page,	params,
		function(xml)
		{
            if(xml=='lock')		        
            {
                var arrCntrl = Runner.controls.ControlManager.getAt(table);
	            for(var i=0;i<arrCntrl.length;i++)
	                arrCntrl[i].setEnabled();
	            $('#savebutton'+pageid).attr('disabled','');
	            $('#savebutton'+pageid).attr('style','');
                $('#message_block'+pageid).empty();
            }
            $('#system_div'+pageid).css('visibility','hidden');
            $('#center_block'+pageid).css('margin-top','0');
       	}
	);
}

function UnlockRecordInline(page,keys,id)
{
    //params editid1=aaa&editid2=bbb => aaa&bbb
    window.clearInterval(window["timeid"+id]);
	if(!keys)
		return;
    var tmparr = new Array();
    var key,pos;
    key="";
    tmparr=keys.split("&");
    for(var i=0;i<tmparr.length;i++)
    {
        pos=tmparr[i].indexOf("=");
        key=key+tmparr[i].substr(pos+1)+"&";
    }
    key=key.substr(0,key.length-1);
    params={
            action: 'unlock',
			keys: escape(key),
			rndval: Math.random()
		};
    $.get(page,	params,
		function(xml)
		{
    	}
	);
}


/**
  * Check validation and clone elem for contorols with type RTE
  *
  * return valRes
  */	
function checkValidSimplePage(formname,tName)
{
	var arrCntrl = Runner.controls.ControlManager.getAt(tName);
	var form = $('#'+formname);
	var valRes = true;
	var isFocused = false;
	for(var i = 0; i < arrCntrl.length; i++) 
	{
		var vRes = arrCntrl[i].validate();
		if(vRes.result) 		
		{	
			if(arrCntrl[i].getControlType() == 'RTE' && arrCntrl[i].useRTE)
			{
				var clone = arrCntrl[i].getForSubmit();	
				$(clone).prependTo(form);
			}
		}
		else{
				valRes = false;
				if(!isFocused)
				{
					arrCntrl[i].setFocus();
					isFocused = true;
				}	
			}	
	}
	return valRes;
}

/**
  * Analogue of PHP function htmlspecialchars()
  * @param str - inital text, where we do changing
  * @param quote_style - define mode for polishing symbols ' and "
  * If defult (0), then transforms only symbol "
  * If ENT_QUOTES (1), then transform symbols ' and "
  * If ENT_NOQUOTES (2), then don't transform symbols ' and " 
  * return string
  */
function htmlSpecialChars(str, quote_style)
{
	str = str.replace(/&/ig,"&amp;");
	if(!quote_style || quote_style==1)
	{
		str = str.replace(/\"/ig,"&quot;")
		if(quote_style==1)
			str = str.replace(/\'/ig,"&#039;")
	}
	str = str.replace(/\>/ig,"&gt;");
	str = str.replace(/\</ig,"&lt;");
	return str;
}

/**
 * Get table object in accordance with using layaut and resizing table
 * If use all layauts except Defaul, then #grid_block - it's div, and inside of it there is a new resizable data table
 * Else layaut Default, then #grid_block - it's table, which is new resizable data table
 * @param: 'id' - id of page,
 */
function getTableObj(id)
{
	var gBlock = getGridBlock(id);
	if(!gBlock)
		return false;
	var tag = $(gBlock).tagName();
	var table;
	if(tag == 'div')
		table = $('table:first',gBlock);
	else if(tag == 'table')
		table = $(gBlock);
	else
		return false;
	
	if(!$(table).length)
		return false;
	else
		return table;
}
/**
 * Get grid block object in accordance with using layaut and resizing table
 * If couldn't find element with id gridBlock, then find element with id resize_cell - it's usually using on default layaut
 * @param: 'id' - id of page,
 */
function getGridBlock(id)
{
	var gBlock = $("#grid_block"+id);
	if(!$(gBlock).length)
	{
		gBlock = $("#resize_cell"+id);
		if(!$(gBlock).length)	
			return false;
	}
	return gBlock;
}
/**
 * Get parent table object in accordance with using layaut and resizing table
 * If use all layauts except Defaul, then #grid_block - it's div, and it's a parent of table object
 * Else layaut Default, then #grid_block - it's table, we find parent for it
 * @param: 'id' - id of page,
 */
function getParentTableObj(id)
{
	var gBlock = getGridBlock(id);
	if(!gBlock)
		return false;
	var tag = $(gBlock).tagName();
	var parent;
	
	if(tag == 'div')
		parent = gBlock;
	else if(tag == 'table')
	{
		parent = $(gBlock).parent();
		if($(parent).tagName()!='div')
			return false;
		if($(parent).attr('id')!='grid_block'+id && $(parent).attr('id')!='resize_cell'+id)	
			return false;
	}
	else
		return false;
	
	if(!$(parent).length)
		return false;
	else
		return parent;
}

/**
 * Set event onHover for tr in main table
 * @param {object} tr  - tr, which must add event hover
 * @param {integer} id - page id
 * @param {boolean} h - use rowHighlite or not
 * @param {boolean} s - use listIcons or not
 * @param {boolean} r - use ResizeColumns or not
 */
function setHoverForTR(tr,id,h,s,r)
{
	var table = getTableObj(id);
	if(!table)
		return false;
	if(!tr)
	{	
		if(r)
			tr = 'tr[@id^=yui-rec]';
		else
			tr = 'tr[@rowid]';
	}
	$(tr,table).each(function()
	{	
		$(this).hover(function() 
		{
			if(h)
				$(this).addClass('rowhighlited');
			if(s)
				$(this).addClass('rowselected');
		},function() 
		{
			if(h)
				$(this).removeClass('rowhighlited');
			if(s)
				$(this).removeClass('rowselected');
		});
	});	
}

/**
 * Show error massage for blocking deleting records
 * @param {integer} id  - id of page,
 * @param {object} blockRecIds - array ids of blocking deleting records 
 */
function showErrorLockDelRec(id,lockRecIds)
{
	for(i=0;i<lockRecIds.length;i++)
	{
		var root = getTableObj(id);
		if(!root)
			return false;
		var span = $("span[@id^=edit"+lockRecIds[i]+"_]:eq(0)",root);
		$('input[@id=check'+id+'_'+lockRecIds[i]+']').attr('checked','checked');
		createInlineError(lockRecIds[i],span,"Record can't be delete. It's editing by another user.");
	}
}

/**
 * Create Inline Error massage
 * Use for inline edit or add and 
 * For locking, when delete records, which used another person
 * @param {integer} id  - id of page,
 * @param {object} parElem - parent element where will be show error
 * @param {object} root - data table for current page
 */
function createInlineError(id,parElem,txt)
{
	if(!parElem)
		return;
	$(parElem).children("div.error").remove();
	$(parElem).append("<div class=error><a href=# id=\"error_" + id + "\" style=\"white-space:nowrap;\">"+TEXT_INLINE_ERROR+" >></a></div>");
	$("#error_"+id)[0].onmouseover=function()
	{
		var error = $("div.inline_error");
		if(!$(error).length)
		{
			$(document.body).append("<div class=\"inline_error error\"></div>");
			error = $("div.inline_error");
		}
		$(error).html(slashdecode(txt));
		$(error).onmouseover=function()
		{
			this.show();
		}
		var coors = findPos(this);
		coors[0] += coors[2];
		coors[1] += coors[3];
		
		$(error).css("top",coors[1] + "px");
		$(error).css("left",coors[0] + "px");
		$(error).css("z-index",100);

		$(error).show();
	};
	$("#error_"+id)[0].onmouseout=function()
	{
		$("div.inline_error").hide();
	}
}

/**
 * Show or hide record controls buttons 
 * except edit selected, inline Add, Add buttons
 * Use for inlineEdit and inlineAdd or ajax mode on list page
 * @param {integer} id  - id of page,
 * @param {string} type - show or hide buttons
 */
function showHideSelectedButtons(id,type)
{
	if(!id)
		return;
	if(!$('#record_controls'+id).length)	
			return;	
	var cond = set = ''; 
	if(type == 'show'){
		cond = 'none';
		set = 'inline';
	}else if(type == 'hide'){
		cond = 'inline';
		set = 'none';
	}else
		return;
		
	var selectAllSpan = $("[@name = select_all"+id+"]").parent();
	var deleteSelectedSpan = $("[@name = delete_selected"+id+"]").parent();
	var printSelectedSpan = $("[@name = print_selected"+id+"]").parent();
	var exportSelectedSpan = $("[@name = export_selected"+id+"]").parent();	
	
	if($(selectAllSpan).length && $(selectAllSpan).css('display') == cond)
		$(selectAllSpan).css('display',set);
	
	if($(deleteSelectedSpan).length  && $(deleteSelectedSpan).css('display') == cond)
		$(deleteSelectedSpan).css('display',set);
	
	if($(printSelectedSpan).length  && $(printSelectedSpan).css('display') == cond)
		$(printSelectedSpan).css('display',set);
	
	if($(exportSelectedSpan).length  && $(exportSelectedSpan).css('display') == cond) 
		$(exportSelectedSpan).css('display',set);
}

/**
 * Update count detail's records on master page list
 * If Add new record, then increase count on 1
 * If delete records, then reduce count on count deleted records
 * @param {integer} pageid  - id of page,
 * @param {string} dTable - detail Table
 * @param {integer} recDels - count records, which were deleted
 * @param {string} type - type of operacion 'add' or 'delete'
 */
function updateCntRecsDetail(pageid,dTable,recDels,type)
{
	if(!pageid || !dTable || !type)
		return;
	var loadCont = $('#loaded_content'+pageid);
	var tdPreview = $(loadCont).parent();
	var tdId = $(tdPreview).attr('id');
	var pos = tdId.lastIndexOf('_'), pos1 = 0, pos2 = 0;
	var recId = tdId.substring(pos+1,tdId.length);
	var cntDet = $('#cntDet_'+dTable+'_'+recId), cntOld = 0, cntNew = 0;
	if($(cntDet).length)
	{
		cntOld = $(cntDet).html();
		pos1 = cntOld.indexOf('('); 
		pos2 = cntOld.indexOf(')'); 
		if(pos2 == -1 && pos2 == -1)
		{
			$(cntDet).html('(1)');
			return;
		}
		else		
			cntOld = parseInt(cntOld.substring(pos1+1,pos2));
		if(isNaN(cntOld))
			return;	
		else if(type=="add")
			cntNew = '(' + (cntOld + 1) + ')';
		else if(type=="delete" && recDels > 0)	
		{	
			cntNew = cntOld - recDels;
			if(cntNew == 0)
				cntNew = '';
			else
				cntNew = '(' + cntNew + ')';
		}
		else
			return;
		$(cntDet).html(cntNew);
	}
	else
		return;
}

/**
 * Run loading indicator on page
 * If use all layauts except Defaul, then #grid_block - it's div, and inside of it there is a new resizable data table
 * Else layaut Default, then #grid_block - it's table, which is new resizable data table
 * @param: 'id' - id of page,
 */
function runLoading(id,root,mode)
{	
	if(!root)
		return;
	if(!mode || mode==4)
	{
		var size_root = [], loadLeft = "", loadTop = "";
		$(root).prepend(createLoadingForSimplePage(id));
		var loadMain = $("#load_main"+id);
		var loadfon = $("#load_fon"+id);
		if(IsIE6())
			var frame = $("#frame_loading"+id);
		if(!mode)
		{
			size_root = getWindowDimensions();
			loadLeft = size_root[0]/2 - 75;
			loadTop = size_root[1]/2 - 25;	
			if(IsIE6())
			{
				$(frame).css("width",""+size_root[0]+"px");
				$(frame).css("height",""+size_root[1]+"px");
			}
		}
		else{
				el = getAbsolutePosition($(root)[0],1);
				$(loadfon).css("width",""+el.w+"px");
				$(loadfon).css("height",""+el.h+"px");
				if(IsIE6())
				{
					$(frame).css("width",""+el.w+"px");
					$(frame).css("height",""+el.h+"px");
				}
				loadLeft = el.w/2 + (el.l - 75);
				loadTop = el.h/2 + (el.t - 25);	
			}
		$(loadMain).css("left",""+loadLeft+"px");
		$(loadMain).css("top",""+loadTop+"px");
		$("#loading"+id)[0].className = "load_go";
	}
	else
		$(root).prepend(getLoadingBlock(id,mode));
		
}
/**
 * Stop showing loading indicator 
 */
function stopLoading(id,mode)
{
	$("#loading"+id).remove();
	if(mode)
	{
		$("#loaded_content"+id).css('position','static');
		$("#loaded_content"+id).css('left','0px');
	}
}

/**
 * Get HTML code for loading indicator on dpInline, onTheFly mode
 */
function getLoadingBlock(id,mode)
{
	return	'<div id="loading'+id+'" name="loadingDiv" align = "center">'+
			'<img src="include/img/loading.gif" align="absmiddle">&nbsp;&nbsp;<span class="load_text">Loading .....</span></div>'+
			(mode!=3 ? '<div id = "loaded_content'+id+'" name="loadedContent" style="position:absolute; left:-10000px;"></div>' : '');
}
/**
 * Get HTML code for loading indicator on simple mode
 */
function createLoadingForSimplePage(id)
{
	var loadCode =   
	'<div class="load_hide" id="loading'+id+'" name="loadingDiv">'+
	'<style>'+
	'div.load_go {display:block;}'+
	'div.load_hide {display:none;}'+
	
	'#load_fon'+id+'{'+
	'	position:absolute;'+
	'	width:100%;'+
	'	height:100%;'+
	'	z-index:3000;'+
	'	background-color:#000;'+
	'	opacity: 0.20;'+
	'	filter: alpha(opacity=20);}'+
	
	'#load_main'+id+'{'+
	'	position:absolute;'+
	'	z-index:4000;}'+
	
	'#load_main'+id+' div.load_block1{'+
	'	width:150px;'+
	'	height:50px;'+
	'	cursor:auto;}'+
	
	'#load_main'+id+' div.load_block2 {padding:12px 0;}'+
	
	'#load_main'+id+' span.load_text{'+
	'	margin-left:10px;'+
	'	font-size:12px;'+
	'	vertical-align:middle;'+
	'	font-weight:bold;}'+
	(IsIE6() ?
	'#frame_loading'+id+'{'+
	'	background:white;'+
	'	position:absolute;'+
	'	display:block;'+
	'	opacity:0.0;'+
	'	filter:alpha(opacity=0);'+
	'	z-index:1000;}' : '')+
	'</style>'+	

	'<div id = "load_main'+id+'">'+
	'	<table cellspacing="0" cellpadding="0" border="0">'+
	'		<tr>'+
	'			<td>'+
	'				<div class="load_block1" align="center">'+
	'					<div class="load_block2"><img src="include/img/loading.gif" align="absmiddle"><span class="load_text">Loading .....</span></div>'+
	'				</div>'+
	'			</td>'+
	'		</tr>'+
	'	</table>'+
	'</div>'+
	(IsIE6() ? '<iframe src="javascript:false;" id="frame_loading'+id+'" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no"> </iframe>' : '')+
	'<div id = "load_fon'+id+'"> </div>'+
	'</div>';
	return loadCode;
}

function parse_datetime(str,dmy)
{
	var re=/\d+/g;
	var arr=str.match(re);
	var dt;
	if(arr==null || arr.length<3)
		return null;
	while(arr.length<6)
		arr[arr.length]=0;
	if(dmy==1)
		dt = new Date(arr[2],arr[1]-1,arr[0],arr[3],arr[4],arr[5]);
	else if(dmy==0)
		dt = new Date(arr[2],arr[0]-1,arr[1],arr[3],arr[4],arr[5]);
	else
		dt = new Date(arr[0],arr[1]-1,arr[2],arr[3],arr[4],arr[5]);

	if(isNaN(dt))
		return null;
//	check date and month
	if(dmy==1 && (dt.getMonth()!=arr[1]-1 || dt.getDate()!=arr[0] || dt.getFullYear()!=arr[2]))
		return null;
	if(dmy==0 && (dt.getMonth()!=arr[0]-1 || dt.getDate()!=arr[1] || dt.getFullYear()!=arr[2]))
		return null;
	if(dmy==2 && (dt.getMonth()!=arr[1]-1 || dt.getDate()!=arr[2] || dt.getFullYear()!=arr[0]))
		return null;
	return dt;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Begin block section 508

var calc508column=true;
var s508td;
var s508column=-1;

function section508setEvents(pageid,focusedControl,setFocusInTD)
{
    var sObj = getTableObj(pageid);
    if(!sObj)
		return false;
    if($(sObj).attr("s508table")==undefined)
    {
        sObj.attr("s508table","true");
        setFocusFirstElement(sObj);
    }
    //  define handler functions
    var s508eventArrow = function(e)
    {
        calc508column=true;
        if(e.which==40)
        {
            calc508column=false;
            click_arrow(this,"next",s508column,sObj);
        }
        else if(e.which==38)
        {
            calc508column=false;
            click_arrow(this,"prev",s508column,sObj);
        }
        else if(e.which==9)
            calc508column=true;
        else
        {
            calc508column=true;
        }
    }
    
    var focusHandler = function()
    {
        if(calc508column)
        {
            s508column=getControlColumn(this);
        }
    }
//  assign handler functions
    sObj.find("td,th").find("input,a,textarea,select").each(function()
    {
        if(!$(this).attr("event508"))
        {
            $(this).attr("event508","true");
            $(this).bind("keydown",s508eventArrow);
            $(this).bind("focus",focusHandler);
        }
    });
//  calc focused column    
    if(focusedControl)
    {
        s508column=getControlColumn(focusedControl.spanContElem);
    }
//  set focus to visible element in current td 
//  set event alt+e to Inline Edit
    if(setFocusInTD && s508td)
    {
        setTimeout(function() {$(s508td).find("input:first,a:first,textarea:first,select:first").focus();},100);
        $(s508td).find("a[@id^=ieditlink"+pageid+"_]").bind("keydown",function(e)
        {
            if(e.altKey && e.which==69)
            {
                $(this).click();
            }
        });
    }
}

function getControlColumn(control)
{
    var parentTr = find508tr(control);
    var parentTrFound=false;
    var tdObj;
//  find parent TD
    $(control).parents().each(function(k,elem)
    {
    
        if(parentTrFound)
            return;
        if(elem===parentTr)
            parentTrFound=true;
        if($(elem).tagName()=="td")
             tdObj=elem;
    });
//  find parent TD's index
    var numberTd=0;
    var colspanAttr = "";
    var tdFound = false;
    $(parentTr).children().each(function(k,elem)
    {
        if(elem===tdObj)
            tdFound = true;
        if(tdFound)
            return;
        numberTd++;
        colspanAttr = $(elem).attr("colspan");
        if(colspanAttr)
            numberTd+=parseInt(colspanAttr)-1;
           
    });
    s508td = tdObj;
    return numberTd;
}

function find508tr(control)
{
//  find main table TR containing the control
    var trObj;
    var mainTableFound=false;
    $(control).parents().each(function(k,elem){
        if(mainTableFound)
            return;
        if($(elem).tagName()=="tr")
            trObj=elem;
        if($(elem).attr("s508table")=="true")
            mainTableFound=true;
    });
    return trObj;
}

function click_arrow(control,param,colnum,table)
{
//  get target TR    
    var tr=$(find508tr(control));
    if(param=="next")
        var trobj=$(tr).next();
    else
        var trobj=$(tr).prev();
//  skip empty and hidden TRs       
    while($(trobj).find("input,a,textarea,select").length==0 && $(trobj).get(0) || $(trobj).css("display")=="none")
        if(param=="next")
            trobj=$(trobj).next();
        else
            trobj=$(trobj).prev();
            
//  skip from THEAD to TBODY and vice versa        
    if(param=="prev" && !$(trobj).get(0) && $(tr).parents().tagName()=="tbody" && $(table).find("thead").find("tr:visible,tr:first").length>0)
        trobj=$(table).find("thead").find("tr:visible,tr:first");
    if(param=="next" && !$(trobj).get(0) && $(tr).parents().tagName()=="thead" && $(table).find("tbody").find("tr:visible,tr:first").length>0)
        trobj=$(table).find("tbody").find("tr:visible,tr:first");
        
//  find element in the target row and set focus to it
    var altElem;        
    var focusSet=false;
    if(colnum==-1)
        $(trobj).find("input:first,a:first,textarea:first,select:first").focus();
    else
    {
        $(trobj).children().each(function(k,elem){
            if($(elem).find("input:first,a:first,textarea:first,select:first").length>0 )
            {
//  find left neighbour                
                if(k<colnum && altElem || !altElem)
                    altElem=elem;
//  exact fit                    
                if(k==colnum)
                {
                    $(elem).find("input:first,a:first,textarea:first,select:first").focus();
                    focusSet=true;
                }
            }
        });
        if(!focusSet && altElem)
            $(altElem).find("input:first,a:first,textarea:first,select:first").focus();
    }
}
		
function setFocusFirst(sObj)
{
    sObj.find("td:first").find("input:first,a:first,textarea:first,select:first").focus();
}

function setFocusFirstElement(sObj)
{
    sObj.find("tbody").find("tr:first").next().find("input:first,a:first,textarea:first,select:first").focus();
}

function getURLParam(name)
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return results[1];
}

function s508pagination(pageid)
{
    var s508eventPagination = function(e)
    {
        NumberPage=getURLParam("goto");
        if(NumberPage=="")
            NumberPage=1;
        if(e.ctrlKey && e.which==37)
        {
            if(NumberPage>1)
            {
                NumberPage--;
                eval("GotoPage"+pageid+"(NumberPage);");
            }
            else
                NumberPage=1;
        }
        if(e.ctrlKey && e.which==39)
        {
            if(NumberPage<window.MaxWindowPage)
            {
                NumberPage++;
                eval("GotoPage"+pageid+"(NumberPage);");
            }
            else
                NumberPage=window.MaxWindowPage;
        }
     }
     $(document).bind("keydown",s508eventPagination);
}

function s508jumpto(pageid)
{
    var s508eventJumpTo = function(e)
    {
        if(e.altKey && e.which==70) // alt+f jump to search
        {
            $("#ctlSearchFor"+pageid).focus();
        }
        if(e.altKey && e.which==77) // alt+m jump to menu
        {
            $("#toplinks_block"+pageid).find("input:first,a:first,textarea:first,select:first").focus();
        }
    }
    $(document).bind("keydown",s508eventJumpTo);
}

function s508inlineEdit(pageid)
{
    $("a[@id^=ieditlink"+pageid+"_]").each(function()
    {
        var parent_tr=$(this).parent("td").parent("tr");
        var record_id=this.id;
        parent_tr.find("input,a,textarea,select").each(function()
        {
            $(this).bind("keydown",function(e)
            {
                if(e.altKey && e.which==69)
                {
                    $("#"+record_id).click();
                }
            });
        });
    });
}
//End block section 508
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
