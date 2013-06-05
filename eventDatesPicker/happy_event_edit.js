
(function(jQuery){jQuery.each(['backgroundColor','borderBottomColor','borderLeftColor','borderRightColor','borderTopColor','color','outlineColor'],function(i,attr){jQuery.fx.step[attr]=function(fx){if(fx.state==0){fx.start=getColor(fx.elem,attr);fx.end=getRGB(fx.end);}
fx.elem.style[attr]="rgb("+[Math.max(Math.min(parseInt((fx.pos*(fx.end[0]-fx.start[0]))+fx.start[0]),255),0),Math.max(Math.min(parseInt((fx.pos*(fx.end[1]-fx.start[1]))+fx.start[1]),255),0),Math.max(Math.min(parseInt((fx.pos*(fx.end[2]-fx.start[2]))+fx.start[2]),255),0)].join(",")+")";}});function getRGB(color){var result;if(color&&color.constructor==Array&&color.length==3)
return color;if(result=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
return[parseInt(result[1]),parseInt(result[2]),parseInt(result[3])];if(result=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
return[parseFloat(result[1])*2.55,parseFloat(result[2])*2.55,parseFloat(result[3])*2.55];if(result=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
return[parseInt(result[1],16),parseInt(result[2],16),parseInt(result[3],16)];if(result=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
return[parseInt(result[1]+result[1],16),parseInt(result[2]+result[2],16),parseInt(result[3]+result[3],16)];return colors[jQuery.trim(color).toLowerCase()];}
function getColor(elem,attr){var color;do{color=jQuery.curCSS(elem,attr);if(color!=''&&color!='transparent'||jQuery.nodeName(elem,"body"))
break;attr="backgroundColor";}while(elem=elem.parentNode);return getRGB(color);};var colors={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0]};})(jQuery);(function($,Z){YAHOO.namespace("Zvents.dragDropList");var Dom=YAHOO.util.Dom;var Event=YAHOO.util.Event;var DDM=YAHOO.util.DragDropMgr;var DDListItem=function(id,ddl_settings,parent_list){this.ddl_settings=ddl_settings;this.parent_list=parent_list;arguments.callee.superclass.constructor.call(this,id);this.logger=this.logger||YAHOO;var el=this.getDragEl();Dom.setStyle(el,"opacity",0.67);this.goingUp=false;this.lastY=0;};YAHOO.extend(DDListItem,YAHOO.util.DDProxy,{itemIndex:function(li){var i=0;var items=$('li',this.parent_list);while(i<items.length){if(li==items[i])return i;i++;}
return-1;},startDrag:function(x,y){this.logger.log(this.id+" startDrag");var dragEl=this.getDragEl();var clickEl=this.getEl();Dom.setStyle(clickEl,"visibility","hidden");dragEl.innerHTML=clickEl.innerHTML;Dom.setStyle(dragEl,"color",Dom.getStyle(clickEl,"color"));Dom.setStyle(dragEl,"backgroundColor",Dom.getStyle(clickEl,"backgroundColor"));Dom.setStyle(dragEl,"borderColor","gray");this.start_index=this.itemIndex(clickEl);},endDrag:function(e){var srcEl=this.getEl();var proxy=this.getDragEl();Dom.setStyle(proxy,"visibility","");var a=new YAHOO.util.Motion(proxy,{points:{to:Dom.getXY(srcEl)}},0.2,YAHOO.util.Easing.easeOut)
var proxyid=proxy.id;var thisid=this.id;a.onComplete.subscribe(function(){Dom.setStyle(proxyid,"visibility","hidden");Dom.setStyle(thisid,"visibility","");});a.animate();if(this.start_position!=this.itemIndex(srcEl)){this.ddl_settings.change_callback(this.parent_list);}
if(this.ddl_settings.trash_id){$('#'+this.ddl_settings.trash_id).css('border','none');}},onDragDrop:function(e,id){if(DDM.interactionInfo.drop.length===1){if(id==this.ddl_settings.trash_id){Dom.setStyle(this.ddl_settings.trash_id,"border","none");if(this.ddl_settings.trash_callback){this.ddl_settings.trash_callback(this.parent_list,this.getEl());}
$("#"+this.getEl().id).remove();if(this.ddl_settings.list_changed_callback){this.ddl_settings.change_callback(this.parent_list);}}}},onDrag:function(e){var y=Event.getPageY(e);if(y<this.lastY){this.goingUp=true;}else if(y>this.lastY){this.goingUp=false;}
this.lastY=y;if(this.ddl_settings.trash_id){$('#'+this.ddl_settings.trash_id).css('border','solid 1px white');}},onDragOver:function(e,id){var srcEl=this.getEl();var destEl=Dom.get(id);if(destEl.nodeName.toLowerCase()=="li"){var orig_p=srcEl.parentNode;var p=destEl.parentNode;if(this.goingUp){p.insertBefore(srcEl,destEl);}else{p.insertBefore(srcEl,destEl.nextSibling);}
DDM.refreshCache();}else if(destEl.id==this.ddl_settings.trash_id){$('#'+this.ddl_settings.trash_id).css('border','solid 1px red');}}});$.fn.dragDropList=function(config){var settings={trash_id:'trash',trash_callback:null,change_callback:null};$.extend(settings,config);this.each(function(){var self=this;new YAHOO.util.DDTarget(self);new YAHOO.util.DDTarget(settings.trash_id);$('li',self).each(function(){new DDListItem(this.id,settings,self);});});};})($ZJQuery,Zvents);(function($){$.fn.tabs=function(config){var settings={panel_selector:null,panels:null,start_tab:0,current_class:'z-current'};$.extend(settings,config);var tabs=this;var panels=settings.panels;function setTab(i)
{panels.hide();panels.eq(i).show();tabs.removeClass(settings.current_class);tabs.eq(i).addClass(settings.current_class);}
this.each(function(i){var self=this;function onClick(){setTab(i);return false;}
$(self).click(onClick);$('a',this).click(onClick);});setTab(settings.start_tab);}})($ZJQuery);(function($,Z){$.fn.layerSelect=function(){this.each(function(){var self=$(this);var all_layer_selector='';self.find('option').each(function(){if(all_layer_selector.length)all_layer_selector+=', ';all_layer_selector+='#'+this.value;});self.change(function(){$(all_layer_selector).hide();$('#'+this.value).show();self.trigger('layer-changed',this.value);});});return this;}})($ZJQuery,Zvents);(function($){var OVERLAY_ID="zOverlay";var OVERLAY_SELECTOR="#"+OVERLAY_ID;$.fn.underlay=function(){this.each(function(){var self=$(this);var z_index=self.css('z-index');$(OVERLAY_SELECTOR).remove();$("select,.z-underlay-hidden").css('visibility','hidden');var overlay_height=($(document).height()>$(window).height())?$(document).height():$(window).height();var overlay_width=($(document).width()>$(window).width())?$(document).width():$(window).width();self.before(["<div id='",OVERLAY_ID,"' style='display:none'></div>"].join(''));var overlay=$(OVERLAY_SELECTOR);overlay.css({zIndex:z_index,position:'absolute',height:overlay_height+"px",width:overlay_width+"px"}).docMove({top:0,left:0}).click(function(){self.trigger('underlay-click')}).show();});return this;};$.fn.underlay_remove=function(){$(OVERLAY_SELECTOR).remove();$("select,.z-underlay-hidden").css('visibility','visible');return this;};})($ZJQuery);(function($,Z){$.fn.event_form=function(config){var settings={listing_class:null,listing_id:null,session_user:false};var timer=null;$.extend(settings,config);this.each(function(){var self=$(this);function _repaint()
{self.addClass('z-repaint').removeClass('z-repaint');}
function _panelInformation()
{var panel=$('#z_listing_event_form_information');panel.find('.z-tabs li').tabs({panels:panel.find('.z-tab-content')});self.find('#queued_event_partner_specific_description, #queued_event_description').keyup(function(){self.find("#remove_desc").removeAttr('checked');});}
function _panelOccurrences()
{var panel=$('#z_listing_event_form_occurrences');panel.find('.z-tabs li').tabs({panels:panel.find('.z-tab-content')});panel.find('.z-event-occurrences tbody').eventOccurrenceList();}
function _panelLocation()
{$('#z_listing_event_form_location').eventLocationFinder();}
function _panelPremium()
{$('#z_promote_section').eventBudgeter({listing_class:settings.listing_class,listing_id:settings.listing_id,is_premium_listing:settings.is_premium_listing,is_published_event:settings.is_published_event,paid_for:settings.promoted_paid_for});$('#z_enhance_section').eventEnhancer({listing_class:settings.listing_class,listing_id:settings.listing_id,internal_user:settings.internal_user,paid_for:settings.enhanced_paid_for});}
function _onFinishSubmit()
{_ajaxSave(function(){if($("#queued_event_id").val()){var error_fields=$(".z-error-field");if(error_fields.length==0){href_url="/listings/publish_event/"+$("#queued_event_id").val()+"?remove_description="+$("#remove_desc").attr('checked');if($("#campaign").length>0){href_url+='&campaign='+$("#campaign").attr('value');}
if($("#skip_campaign_redirect").length>0){href_url+='&skip_campaign_redirect='+$("#skip_campaign_redirect").attr('value');}
document.location.href=href_url;}else{var msg_array=[];error_fields.each(function(){msg_array.push('<li><a href="#'+this.id.replace('z_field_','z_a_')+'">'+this.getAttribute('display')+'</a></li>')});$("#z_review_errors ul.z-error-list").html(msg_array.join(''));$("#z_review_errors").fadeIn();}}else{alert("Oops!  Your event must have all the required information before proceeding.");};});return false;}
function _onFinishSave()
{_ajaxSave(function(){var href;if(settings.session_user){$("#z_status").html("Loading ...").show();if($("#campaign").length!=0){href="/sales/campaigns/"+$("#campaign").val();}
else{href="/my_listings";}}
else{href="/user/login?return_to=%2Flistings%2Fupdate_creator_id%2F"+
$("#queued_event_id").val();}
document.location.href=href;});return false;}
function _onFinishCancel()
{href_url="/listings/delete_queued_event?id="+$("#queued_event_id").val();if($("#campaign").length>0){href_url+='&campaign='+$("#campaign").attr('value');}
if($("#skip_campaign_redirect").length>0){href_url+='&skip_campaign_redirect='+$("#skip_campaign_redirect").attr('value');}
document.location.href=href_url;return false;}
function _onFinishUpdate()
{_ajaxSave(function(){document.location.href="/partners/event_queue/"+settings.partner_id+"?update_from_new_form=true";});return false;}
function _onFinishReject()
{self.attr('action','/listings/reject_queued_event').submit();return false;}
function _onOpenPreview(e,preview_url)
{_ajaxSave(function(){if($('#z_preview_section a').length>0){var w=window.open(preview_url,"zventspreview");w.focus();}});return false;}
function _panelFinish()
{self.find('#z_button_submit').click(_onFinishSubmit);self.find('#z_button_save').click(_onFinishSave);self.find('#z_button_update_and_publish').click(_onFinishSubmit);self.find('#z_button_update').click(_onFinishUpdate);self.find('#z_button_cancel').click(_onFinishCancel);self.find('#z_button_reject').click(_onFinishReject);self.bind('open-preview',_onOpenPreview);}
function _beforeSaveSubmit(formArray,jqForm)
{self.find('.z-occurrence-end-time-layer:hidden :text').val("");$("#z_status").fadeIn();}
function _updateValidatedStatus(key,error)
{if(typeof error!='undefined'){self.find('#z_feedback_'+key).html(error).show().parent('.z-field-container').addClass("z-error-field");}
else{self.find('#z_feedback_'+key).hide().html("").parent('.z-field-container').removeClass("z-error-field");}}
function _handleSaveResponse(json,callback)
{$("#z_status").fadeOut();$("#z_queued_event_id").val(json.id);if(json.preview_link===undefined){$('#z_preview_section').html('');}else{$('#z_preview_section').html(json.preview_link);}
$('.z-form-feedback').each(function(){var key=$(this).id().replace('z_feedback_','');if(typeof json.errors!='undefined'){_updateValidatedStatus(key,json.errors[key]);}});if((typeof json.errors!='undefined')&&json.errors.general_issue){alert(json.errors.general_issue);}
if(Z.Object.isEmpty(json.errors)){$("#z_review_errors").fadeOut();}
if(callback){callback();}
_repaint();}
function _ajaxSave(callback)
{
	var ABSOLUTE_PATH	=	$("#ABSOLUTE_PATH").val();
	self.ajaxSubmit({
					url:ABSOLUTE_PATH+'checkDate.php',
	//				beforeSubmit:_beforeSaveSubmit,
					iframe:false,
					success:
						function(response){
							if(response!=''){
							$("#z_review_errors").fadeIn('slow');
							$("#check_errors").val('1');
							$("#z_review_errors").html(response);
							$("#dErrors").val(response);
							
							}
							else{
								$("#z_review_errors").fadeOut('slow');
								$("#check_errors").val('0');
								$("#dErrors").val('');
								}
							}
																																				   //_handleSaveResponse(response,callback);

});}



function _onDataChanged(src)
{if(timer)clearTimeout(timer);timer=setTimeout(function(){_ajaxSave();timer=null;},3000);_updateSummary();}
function _onNewVenue()
{_ajaxSave(function(){if($("#campaign").attr('value')){window.location.href="/venues/new?qe="+settings.listing_id+"&campaign="+$("#campaign").attr('value');}else{window.location.href="/venues/new?qe="+settings.listing_id;}});}
function _onCostChanged(){if(!settings.is_premium_listing&&!settings.is_published&&($('#z_promote_checkbox').attr('checked')||$('#z_enhance_checkbox').attr('checked'))){$("#z_button_submit").html("<img src='/images/form/tick.png' alt=''/>Checkout");$("#z_tos_type").html("Checkout");}
else{$("#z_button_submit").html("<img src='/images/form/tick.png' alt=''/>Submit Event");$("#z_tos_type").html("Submit Event");}
$.getJSON(["/listings/update_cost_summary_detail?id=",$("#queued_event_id").val(),"&budget=",$("#queued_event_budget").val(),"&budget_period=",$(".z-budget-period-block input:checked").val(),"&nocache=",Math.uuid(10)].join(''),function(json){$("#z_status").fadeOut();$("#z_cost_summary_detail").html(json['html']);self.trigger('data-changed',[self]);});}
function _updateSummary()
{var name=$("#queued_event_name").val();var category=$("#z_event_first_category :selected").text();var location_name=$("#z_selected_venue_name").html();var location_address=$("#z_selected_venue_address").html();var time="No future event times";if(category=="None"){category="No category";}
var occurrences=$("table.z-event-occurrences tr");if(occurrences.length){time=occurrences.eq(0).find('.z-occurrence-date-cell').text();time=time+" (total "+occurrences.length+" occurrences)";}
$("#z_listing_summary_name").html(name?name:"No event name");$("#z_listing_summary_category").html(category);$("#z_listing_summary_location").html(location_name?[location_name,'<br />',location_address].join(''):"No location");$("#z_listing_summary_times").html(time);}
self.bind('cost-changed',_onCostChanged);self.bind('data-changed',_onDataChanged);self.bind('new-venue',_onNewVenue);self.find('.z-validate-me').change(function(){self.trigger('data-changed',this)});_panelInformation();_panelOccurrences();_panelLocation();_panelPremium();_panelFinish();_updateSummary();});};})($ZJQuery,Zvents);(function($,Z){LAYER_ID_TO_REPEAT_TYPE={z_repeat_once_layer:'once',z_repeat_daily_layer:'daily',z_repeat_weekly_layer:'weekly',z_repeat_monthly_layer:'monthly'}
$.fn.eventRepeatDatePicker=function(config){this.each(function(){var self=$(this);function _popupSelectHandler(type,args,self){var date=Z.Date.formatMdyDate(this.toDate(args[0][0]));if(this.id=="z_popup_start_date"){	
																																														 var da = date.split('/');
 var da1 = da[1].split('/');		
 var dy = Number(da1[0])+1;
 if(dy==32){
	dy = 1;
	da[0] = Number(da[0])+1;
 }
 if(da[0]=='2' && dy == '30' && da[2] == '2012'){
		da[0]	= '3';
		dy		= '1';
		}
	else if(da[0]=='2' && dy == '29'){
		da[0]	= '3';
		dy		= '1';
	}
	var ev_dati = da[0]+'/'+dy+'/'+da[2];
	
 $("#z_start_date_advanced").val(ev_dati); 
 
 }
else if(this.id=="z_popup_end_date"){
	
	 var da = date.split('/');
	 var da1 = da[1].split('/');		
	 var dy = Number(da1[0])+1;
	 if(dy==32){
		dy = 1;
		da[0] = Number(da[0])+1;
 	}
 if(da[0]=='2' && dy == '30' && da[2] == '2012'){
		da[0]	= '3';
		dy		= '1';
		}
	else if(da[0]=='2' && dy == '29'){
		da[0]	= '3';
		dy		= '1';
	}
	var ev_dati = da[0]+'/'+dy+'/'+da[2];
	 $("#z_end_date_advanced").val(ev_dati);
	}
this.hide();};function _getWeeklyDays()
{var weekly_days=[];$('#z_weekly_repeat_days input:checked').each(function(){weekly_days.push(parseInt(this.value))});return weekly_days;}
function _validateDateRange(start_date_str,end_date_str)
{try{var start_date=new Date(Date.parse(start_date_str));}
catch(e){alert('Oops! The start date is not valid!');return null;}
try{var end_date=new Date(Date.parse(end_date_str));}
catch(e)
{alert('Oops! The end date is not valid!');return null;}
if(end_date<=start_date){alert("Oops! Please select a start date that is before the end date.");return null;}
return[start_date,end_date];}
function _repeatDailyDates(start_date_str,end_date_str,interval)
{var dates=[];var range=_validateDateRange(start_date_str,end_date_str);if(!range)return;if(!(interval>0)){alert("Oops! Please select a daily repeat interval.");return;}
var date=range[0];
while(date<=range[1]){dates.push(Z.Date.formatMdyDate(date));date.setDate(date.getDate()+interval);}


var ev_dates = dates.toString().split(',');
var ev_p = 0;
var ev_dates2 = new Array();
	
	jQuery.each(jQuery(ev_dates), function() {
		var da = ev_dates[ev_p].split('/');
		var da1 = da[1].split('/');
		var dy = Number(da1[0])+1;
		if(dy==32)
			dy = 1;
		var ev_th = da[0]+'/'+dy+'/'+da[2];
		ev_dates2.push(ev_th);
		ev_p++;
	});


dates = ev_dates2;

self.trigger('add-repeat',[dates]);}
function _repeatWeeklyDates(start_date_str,end_date_str,interval,days)
{var dates=[];var range=_validateDateRange(start_date_str,end_date_str);if(!range)return;if(!days.length){alert('Oops!  You must pick one or more days of the week you would like this event to occur on.');return;}
var date=new Date();date.setTime(range[0].valueOf());var day_offset=date.getDay();var week_diff=0;while(date<=range[1]){if(week_diff%interval==0){if(days.indexOf(date.getDay())!=-1){dates.push(Z.Date.formatMdyDate(date));}}
day_offset+=1;if(day_offset==7){week_diff+=1;day_offset=0;}
date.setDate(date.getDate()+1);}
var ev_dates = dates.toString().split(',');
var ev_p = 0;
var ev_dates2 = new Array();
	
	jQuery.each(jQuery(ev_dates), function() {
		var da = ev_dates[ev_p].split('/');
		var da1 = da[1].split('/');
		var dy = Number(da1[0])+1;
		if(dy==32)
			dy = 1;
		var ev_th = da[0]+'/'+dy+'/'+da[2];
		ev_dates2.push(ev_th);
		ev_p++;
	});


dates = ev_dates2;
self.trigger('add-repeat',[dates]);}
function _repeatMonthlyOnDay(start_date_str,end_date_str,day_of_month)
{var range=_validateDateRange(start_date_str,end_date_str);if(!range)return;if(day_of_month<1){alert("Oops!  Please select a day of the month.");return;}
var dates=[];var date=new Date();date.setTime(range[0].valueOf());date.setDate(day_of_month);while(date<=range[1]){if(date>=range[0]){dates.push(Z.Date.formatMdyDate(date));}

date.setMonth(date.getMonth()+1);}

var ev_dates = dates.toString().split(',');
var ev_p = 0;
var ev_dates2 = new Array();
	
	jQuery.each(jQuery(ev_dates), function() {
		var da = ev_dates[ev_p].split('/');
		
		var da1 = da[1].split('/');
		var dy = Number(da1[0])+1;
		if(da[0]==2 && dy==30){
			dy = 1;
			}
		if(dy==32)
			dy = 1;
		
		var ev_th = da[0]+'/'+dy+'/'+da[2];
		ev_dates2.push(ev_th);
		ev_p++;
	});

dates = ev_dates2;

self.trigger('add-repeat',[dates]);}
function _repeatMonthlyPattern(start_date_str,end_date_str,period,day_of_week)
{function calcDateInMonth(current_date){var new_date=new Date();new_date.setTime(current_date.valueOf());new_date.setDate(1);while(new_date.getDay()!=day_of_week){new_date.setDate(new_date.getDate()+1);}
new_date.setDate(new_date.getDate()+(7*period));return new_date;};var range=_validateDateRange(start_date_str,end_date_str);if(!range)return;var dates=[];var date=calcDateInMonth(range[0]);while(date<=range[1]){if(date>=range[0]){dates.push(Z.Date.formatMdyDate(date));}
date.setMonth(date.getMonth()+1);date=calcDateInMonth(date);}

var ev_dates = dates.toString().split(',');
var ev_p = 0;
var ev_dates2 = new Array();
	
	jQuery.each(jQuery(ev_dates), function() {
		var da = ev_dates[ev_p].split('/');
		var da1 = da[1].split('/');
		var dy = Number(da1[0])+1;
		if(dy==32){
			dy = 1;
		}
		var ev_th = da[0]+'/'+dy+'/'+da[2];
		ev_dates2.push(ev_th);
		ev_p++;
	});


dates = ev_dates2;


self.trigger('add-repeat',[dates]);}
function _onRepeatTypeLayerChanged(event,layer_id)
{if(LAYER_ID_TO_REPEAT_TYPE[layer_id]=='once'){$('#z_end_date_block').hide();}
else{$('#z_end_date_block').show();}}
function _addRepeatHandler()
{var type=LAYER_ID_TO_REPEAT_TYPE[$('#z_occurrence_repeat_type_select').val()];var start_date=$('#z_start_date_advanced').val();var end_date=$("#z_end_date_advanced").val();var dates=[];switch(type){case'daily':_repeatDailyDates(start_date,end_date,parseInt($('#daily_repeat_interval').val()));break;case'weekly':_repeatWeeklyDates(start_date,end_date,$('#z_weekly_repeat_interval').val(),_getWeeklyDays());break;case'monthly':var monthly_type=self.find('.z-monthly-repeat-type:checked').val();switch(monthly_type){case'day':_repeatMonthlyOnDay(start_date,end_date,parseInt($('#z_monthly_day_of_month').val()));break;case'pattern':_repeatMonthlyPattern(start_date,end_date,parseInt($('#z_monthly_pattern_period').val()),parseInt($('#z_monthly_pattern_day').val()));break;}
break;default:break;}}
var min_date=new Date();var max_date=new Date();max_date.setFullYear(max_date.getFullYear()+3);var today=Z.Date.formatMdyDate(min_date);$("#z_start_date_advanced").val(today);$("#z_end_date_advanced").val(today);var popup_start_date=new YAHOO.widget.Calendar("z_popup_start_date","z_popup_start_date_container",{title:"Choose a start date:",close:true,mindate:min_date,maxdate:max_date});popup_start_date.selectEvent.subscribe(_popupSelectHandler,popup_start_date,true);popup_start_date.render();var popup_end_date=new YAHOO.widget.Calendar("z_popup_end_date","z_popup_end_date_container",{title:"Choose an end date:",close:true,mindate:min_date,maxdate:max_date});popup_end_date.selectEvent.subscribe(_popupSelectHandler,popup_end_date,true);popup_end_date.render();YAHOO.util.Event.addListener("z_show_popup_start_date","click",popup_start_date.show,popup_start_date,true);YAHOO.util.Event.addListener("z_start_date_advanced","click",popup_start_date.show,popup_start_date,true);YAHOO.util.Event.addListener("z_show_popup_end_date","click",popup_end_date.show,popup_end_date,true);YAHOO.util.Event.addListener("z_end_date_advanced","click",popup_end_date.show,popup_end_date,true);layer_select=self.find('#z_occurrence_repeat_type_select').layerSelect();layer_select.bind("layer-changed",_onRepeatTypeLayerChanged);self.find('#z_add_repeat_date').click(_addRepeatHandler);$(window).one('unloading',function(){popup_start_date.selectEvent.unsubscribe();popup_end_date.selectEvent.unsubscribe();YAHOO.util.Event.removeListener("z_show_popup_start_date");YAHOO.util.Event.removeListener("z_start_date_advanced");YAHOO.util.Event.removeListener("z_show_popup_end_date");YAHOO.util.Event.removeListener("z_end_date_advanced");});});return this;}})($ZJQuery,Zvents);(function($,Z)
{function _onSelectDate(type,args,self){if(!self.data('silent')){var date_array=args[0][0];var year=date_array[0];var month=date_array[1];var day=date_array[2];self.trigger('date-selected',[month,day,year].join('/'));}}
$.fn.eventDatePicker=function(config){this.each(function(){var self=$(this);var now=new Date();var max_date=new Date();max_date.setFullYear(max_date.getFullYear()+3);var settings={min_date:now,max_date:max_date,id:'z_event_date_picker_calendar'};settings=$.extend(settings,config);YAHOO.widget.Calendar.prototype.deselectCell=YAHOO.widget.Calendar.prototype.selectCell;var calendar=new YAHOO.widget.CalendarGroup(settings.id,this,{pages:3,multi_select:true,mindate:settings.min_date,maxdate:settings.max_date});calendar.selectEvent.subscribe(_onSelectDate,self);calendar.render();self.data('settings',settings);self.data('calendar',calendar);self.data('silent',false);$(window).one('unloading',function(){calendar.selectEvent.unsubscribe();});});return this;};$.fn.eventDatePicker_select=function(date,silent){if(typeof silent=='undefined')silent=false;this.each(function(){var self=$(this);self.data('silent',silent);var calendar=self.data('calendar');calendar.select(date);calendar.render();self.data('silent',false);});return this;};$.fn.eventDatePicker_removeDate=function(date){this.each(function(){var self=$(this);var calendar=self.data('calendar');calendar.deselect(date);calendar.render();});return this;};$.fn.eventDatePicker_clear=function(){this.each(function(){var self=$(this);var calendar=self.data('calendar');calendar.deselectAll();calendar.render();});return this;};})($ZJQuery,Zvents);(function($,Z)
{function Occurrence(date,start_time,start_am_pm,end_time,end_am_pm,date_type,occurrence_id,unique_id)
{function _dateToLocaleString(dt){if(dt){var wStr=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"][dt.getDay()];var dStr=dt.getDate();var mStr=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"][dt.getMonth()];var yStr=dt.getFullYear();return[wStr,", ",dStr," ",mStr,", ",yStr].join('');}
else{return'';}};function _amPmString(ampm)
{return ampm?'AM':'PM';}
if(!unique_id){unique_id=Z.String.randomString();}
if(start_time){this.start_date_time=new Date(Date.parse(date+' '+start_time+' '+_amPmString(start_am_pm)));}
else{this.start_date_time=new Date(Date.parse(date));}
if(end_time){this.end_date_time=new Date(Date.parse(date+' '+end_time+' '+_amPmString(end_am_pm)));}
else{this.end_date_time=null;}
if(this.end_date_time&&this.end_date_time<this.start_date_time){this.end_date_time.setDate(this.end_date_time.getDate()+1);}
this.occurrence_id=occurrence_id;this.date=date;this.display_date=_dateToLocaleString(this.start_date_time);this.start_time=start_time;this.start_am_pm=start_am_pm;this.end_time=end_time;this.end_am_pm=end_am_pm;this.date_type=date_type;this.unique_id=unique_id;this.not_rendered=true;this.compareTo=function(occurrence){if(this.start_date_time>occurrence.start_date_time){return 1;}
else if(this.start_date_time<occurrence.start_date_time){return-1;}
else{if(this.end_date_time>occurrence.end_date_time){return 1;}
else if(this.end_date_time<occurrence.end_date_time){return-1;}
else{return 0;}}};}
Occurrence.compare=function(a,b){return a.compareTo(b);}
function _extractOccurrencesFromRows(self,rows)
{var occurrences=[];var dates=[];rows.each(function(){var row=$(this);var occurrence=new Occurrence(row.find("input.z-occurrence-date").val(),row.find("input.z-occurrence-start-time").val(),row.find("select.z-occurrence-start-am-pm").val(),row.find("input.z-occurrence-end-time").val(),row.find("select.z-occurrence-end-am-pm").val(),row.find("select.z-occurrence-type").val(),row.find("input.z-occurrence-id").val(),row.id().slice("z_occurrence_row_".length));occurrences.push(occurrence);dates.push(occurrence.start_date_time);});self.data('date_picker').eventDatePicker_select(dates,true);self.data('occurrences',occurrences)}
function _renderOccurrence(self,occurrence)
{return self.data('occurrence_template')(occurrence);}
function _renderOccurrences(self,occurrences)
{var rendering_array=[];for(var i=0;i<occurrences.length;i++){rendering_array.push(_renderOccurrence(self,occurrences[i]));}
self.html(rendering_array.join(''));}
function _updateOccurrenceCount(self)
{var occurrences=self.data('occurrences');$("#z_total_occurrences").html(occurrences.length);}
function _isOccurrenceDateUnique(i,occurrences)
{if(i>0&&occurrences[i-1].date==occurrences[i].date){return false;}
else if((i+1)<occurrences.length&&occurrences[i+1].date==occurrences[i].date){return false;}
return true;}
function _bindHandlersToRows(self,rows)
{rows.each(function(i){var row=$(this);row.find('a.z-occurrence-remove').unbind().click(function(){var occurrences=self.data('occurrences');var date_picker=self.data('date_picker');if(_isOccurrenceDateUnique(i,occurrences)){date_picker.eventDatePicker_removeDate(occurrences[i].date);}
row.remove();occurrences.splice(i,1);_bindHandlersToRows(self,self.find('tr'));_updateOccurrenceCount(self);self.formTrigger('data-changed');});row.find('a.z-end-time-toggle').unbind().click(function(){row.find('div.z-occurrence-end-time-layer').toggle();});});rows.find(':input').unbind().change(function(){self.formTrigger('data-changed');})}
function _newOccurrenceEffect(selector){$(selector).addClass('z-new-occurrence-effect');var new_cells=$('tr.z-new-occurrence-effect td').css({backgroundColor:'#aeca97'});setTimeout(function(){new_cells.animate({backgroundColor:'#d1e5c0'},1000);$('tr.z-new-occurrence-effect').removeClass('z-new-occurrence-effect');},1000);}
function _addOccurrence(self,date,start_time,start_am_pm,end_time,end_am_pm,date_type,occurrence_id,unique_id)
{var occurrences=self.data('occurrences');var new_occurrence=new Occurrence(date,start_time,start_am_pm,end_time,end_am_pm,date_type,occurrence_id,unique_id);var index=Z.Array.insertSorted(occurrences,new_occurrence);if(occurrences.length>(index+1)){self.find('tr').eq(index).before(_renderOccurrence(self,new_occurrence));}
else{self.append(_renderOccurrence(self,new_occurrence));}
_bindHandlersToRows(self,self.find('tr'));_newOccurrenceEffect('#z_occurrence_row_'+new_occurrence.unique_id);_updateOccurrenceCount(self);self.formTrigger('data-changed');}
function _addMultipleOccurrences(self,dates,start_time,start_am_pm,end_time,end_am_pm)
{var occurrences=self.data('occurrences');var date_picker=self.data('date_picker');var new_selector='';for(var i=0;i<dates.length;i++){var date=dates[i];var new_occurrence=new Occurrence(date,start_time,start_am_pm,end_time,end_am_pm);occurrences.push(new_occurrence);if(dates.length<20){if(new_selector.length)new_selector+=',';new_selector+='#z_occurrence_row_'+new_occurrence.unique_id;}}
occurrences.sort(Occurrence.compare);_renderOccurrences(self,occurrences);if(dates.length<20){_newOccurrenceEffect(new_selector);}
date_picker.eventDatePicker_select(dates.join(', '),true);_bindHandlersToRows(self,self.find('tr'));_updateOccurrenceCount(self);self.formTrigger('data-changed');}
function _clearOccurrences(self)
{self.data('date_picker').eventDatePicker_clear();self.data('occurrences',[]);self.html('');_updateOccurrenceCount(self);self.formTrigger('data-changed');}
$.fn.eventOccurrenceList=function(config)
{this.each(function(){var self=$(this);function _onDateSelected(event,date_str){_addOccurrence(self,date_str,$('#z_event_start_time').val(),$('#z_event_start_am_pm').val(),$('#z_event_end_time').val(),$('#z_event_end_am_pm').val());}
function _onAddRepeat(event,dates){_addMultipleOccurrences(self,dates,$('#z_event_start_time').val(),$('#z_event_start_am_pm').val(),$('#z_event_end_time').val(),$('#z_event_end_am_pm').val());}
function _onClearOccurrences(event){_clearOccurrences(self);return false;}
function _timeBlur(){var self=$(this);if(self.val()!=''){var time_pieces=self.val().split(':');var hour=parseInt(time_pieces[0]);var minutes=time_pieces.length==1?0:parseInt(time_pieces[1]);if(hour>=0&&hour<13&&minutes>=0&&minutes<61){minutes=((minutes<10)?"0"+minutes:minutes);formatted_time=[hour,minutes];self.val(formatted_time.join(':'));}else{self.val('');}}}
var settings={}
$.extend(settings,config);var date_picker=$('#z_calendar_container').eventDatePicker();date_picker.bind('date-selected',_onDateSelected);var repeat_date_picker=$('#z_tab_advanced_view').eventRepeatDatePicker();repeat_date_picker.bind('add-repeat',_onAddRepeat)
var rows=$(this).children('tr');self.data('settings',settings);self.data('occurrence_template',Z.String.template('occurrence_template'));self.data('date_picker',date_picker);_extractOccurrencesFromRows(self,rows);$('#z_event_start_time, #z_event_end_time').blur(_timeBlur);$('#z_clear_occurrences').click(_onClearOccurrences);_bindHandlersToRows(self,rows);});return this;}})($ZJQuery,Zvents);(function($,Z){$.fn.eventLocationFinder=function(config)
{function _onChangeLocation(self)
{self.find('.z-layer-select-venue').toggle();return false;}
function _chooseVenue(self,venue_id,index)
{$('#z_input_venue_id').val(venue_id);var row=self.find('#z_venue_list_layer tbody tr').eq(index);var name=row.find('.z-venue-name a:first').text();var address=row.find('p.z-venue-address').text();self.find('.z-layer-select-venue').hide();$('#z_selected_venue').html(Z.String.template('venue_info_template')({name:name,address:address})).show().find('.z-change-location').click(function(){return _onChangeLocation(self);});self.parents('form').trigger('data-changed',self);}
function _bindVenueListHandlers(self)
{self.parents('form').bind('venue-choose',function(e,venue_id,index){_chooseVenue(self,venue_id,index);return false;});self.parents('form').bind('venue-search',function(e,index){_search(self,index);return false;});}
this.each(function(){var settings={};$.extend(settings,config);var self=$(this);self.find('#z_event_button_lookup_venue').click(function(){_search(self,0);return false;});self.find('.z-change-location').click(function(){return _onChangeLocation(self);});_bindVenueListHandlers(self);});return this;}})($ZJQuery,Zvents);(function($,Z){var MINIMUM_BUDGET=30;var DEFAULT_BUDGET=30;$.fn.eventBudgeter=function(config)
{var settings={};$.extend(settings,config);this.each(function(){var self=$(this);function _updateBudgetSummary(){var budget=$('#queued_event_budget').val();self.find("#z_preview_cost").html('$'+budget);self.find("#z_preview_clicks").html(parseInt(budget));}
function _setBudgetField(budget){$('#queued_event_budget').val(budget.toFixed(2));_updateBudgetSummary();}
function _onBudgetChange(budget)
{var upgraded=(budget>0);_updateBudgetSummary();self.formTrigger('cost-changed',[self]);$("#z_status").show();}
function _onPeriodChange(event)
{if(this.checked){_onBudgetChange(_getBudgetAmount());}}
function _onUpgradeOptionChanged(){var checkbox=$(this);checkbox.attr('disabled','disabled');if(checkbox.attr('checked')){_initializeNewBudget();$.post("/listings/enable_promotion",{"listing_class":settings.listing_class,"listing_id":settings.listing_id,"budget":DEFAULT_BUDGET,"enable":true},function(retVal){checkbox.removeAttr('disabled');self.formTrigger('cost-changed',[self]);return false;});_enableControls();}
else{$(this).attr('disabled','disabled');$.post("/listings/enable_promotion",{"listing_class":settings.listing_class,"listing_id":settings.listing_id,"budget":0,"enable":false},function(retVal){checkbox.removeAttr('disabled');self.formTrigger('cost-changed',[self]);return false;});_disableControls();}
return true;}
function _enableControls(){self.find(".z-premium-option-overlay,.z-premium-option-marketing,").hide();self.find(".z-premium-option-controls *").removeAttr('disabled');}
function _disableControls(){self.find(".z-premium-option-overlay,.z-premium-option-marketing,").show();self.find(".z-premium-option-controls *").attr('disabled','disabled');}
self.find('#queued_event_budget').blur(_onBudgetBlur);self.find('.z-budget-period-block input').change(_onPeriodChange);$('#z_promote_checkbox').click(_onUpgradeOptionChanged);self.find('.z-premium-option-marketing,.z-premium-option-overlay').click(function(){$('#z_promote_checkbox').attr('checked',true).click();});if(settings.paid_for||$('#z_promote_checkbox').attr('checked')){_enableControls();}
else{_setBudgetField(DEFAULT_BUDGET);_disableControls();}});return this;};})($ZJQuery,Zvents);(function($,Z){function _close(self,fade)
{$("select").css('visibility','visible');if(fade){self.fadeOut();}
else{self.hide();}
self.data('cleanup')();}
$.fn.eventPopup=function(config){$("select").css('visibility','hidden');this.each(function(){var self=$(this);var settings={fade:false,underlay_close:true,submit_button:null};function _interceptReturn(e)
{if(e.which==13){if(settings.submit_button){$(settings.submit_button).click();}
else{$(this).filter(':submit, :button').click();}
return false;}}
$.extend(settings,config);var scroll_top=$(document).scrollTop();var form_offset=$("#wrapper").offset();var underlay=self.underlay({on_click:_close}).docMove({top:scroll_top+100,left:form_offset.left+100}).bind('underlay-click',function(){if(settings.underlay_close)_close(self);});self.find('select').css('visibility','visible');self.find(':input').keypress(_interceptReturn);if(settings.fade){self.fadeIn();}
else{self.show();}
self.data('cleanup',function(){self.find(':input').unbind('keypress',_interceptReturn);underlay.underlay_remove();self.trigger('popup-closed');});});return this;}
$.fn.eventPopup_close=function(config){this.each(function(){var self=$(this);var settings={fade:false};$.extend(settings,config);_close(self,settings.fade);});return this;}})($ZJQuery,Zvents);(function($,Z){var MINIMUM_BUDGET=30;var DEFAULT_BUDGET=30;$.fn.eventBudgeter=function(config)
{var settings={};$.extend(settings,config);this.each(function(){var self=$(this);function _updateBudgetSummary(){var budget=$('#queued_event_budget').val();self.find("#z_preview_cost").html('$'+budget);self.find("#z_preview_clicks").html(parseInt(budget));}
function _setBudgetField(budget){$('#queued_event_budget').val(budget.toFixed(2));_updateBudgetSummary();}
function _onBudgetChange(budget)
{var upgraded=(budget>0);_updateBudgetSummary();self.formTrigger('cost-changed',[self]);$("#z_status").show();}
function _initializeNewBudget(){$('#queued_event_budget').val(DEFAULT_BUDGET.toFixed(2)).addClass('z-default');_onBudgetChange(DEFAULT_BUDGET);}
function _getBudgetAmount()
{var budget_value=self.find('#queued_event_budget').val();var budget=parseFloat(budget_value);if(!budget){budget=0};return budget;}
function _onBudgetBlur()
{$('#queued_event_budget').removeClass('z-default');_onBudgetChange(_getBudgetAmount());}
function _onPeriodChange(event)
{if(this.checked){_onBudgetChange(_getBudgetAmount());}}
function _onUpgradeOptionChanged(){var checkbox=$(this);checkbox.attr('disabled','disabled');if(checkbox.attr('checked')){_initializeNewBudget();$.post("/listings/enable_promotion",{"listing_class":settings.listing_class,"listing_id":settings.listing_id,"budget":DEFAULT_BUDGET,"enable":true},function(retVal){checkbox.removeAttr('disabled');self.formTrigger('cost-changed',[self]);return false;});_enableControls();}
else{$(this).attr('disabled','disabled');$.post("/listings/enable_promotion",{"listing_class":settings.listing_class,"listing_id":settings.listing_id,"budget":0,"enable":false},function(retVal){checkbox.removeAttr('disabled');self.formTrigger('cost-changed',[self]);return false;});_disableControls();}
return true;}
function _enableControls(){self.find(".z-premium-option-overlay,.z-premium-option-marketing,").hide();self.find(".z-premium-option-controls *").removeAttr('disabled');}
function _disableControls(){self.find(".z-premium-option-overlay,.z-premium-option-marketing,").show();self.find(".z-premium-option-controls *").attr('disabled','disabled');}
self.find('#queued_event_budget').blur(_onBudgetBlur);self.find('.z-budget-period-block input').change(_onPeriodChange);$('#z_promote_checkbox').click(_onUpgradeOptionChanged);self.find('.z-premium-option-marketing,.z-premium-option-overlay').click(function(){$('#z_promote_checkbox').attr('checked',true).click();});if(settings.paid_for||$('#z_promote_checkbox').attr('checked')){_enableControls();}
else{_setBudgetField(DEFAULT_BUDGET);_disableControls();}});return this;};})($ZJQuery,Zvents);(function($,Z){$.fn.eventEnhancer=function(config)
{var settings={};$.extend(settings,config);this.each(function(){var self=$(this);function _repaint()
{self.addClass('z-repaint').removeClass('z-repaint');}
function _clearFileInput(input)
{input.val('');input.replaceWith(input.clone());}
function _getImageIdFromItem(item_elem)
{return item_elem.id.slice(5);}
function _showBusyPopup(heading,message)
{$('#z_busy_heading').html(heading);$('#z_busy_message').html(message);$("#z_busy_popup").eventPopup({underlay_close:false});}
function _getImageIdsFromList()
{var items=self.find('#z_images_list > li');var ids=[];items.each(function(){ids.push(_getImageIdFromItem(this));});return ids;}
function _getImageInfoById(id)
{var li=self.find("#z_li_"+id);var credit=li.find(".z-credit").text();var caption=li.find(".z-full-image-caption").text();return{credit:li.find(".z-credit").text(),caption:li.find(".z-full-image-caption").text()};}
function _bindImageListHandlers()
{self.find('#z_images_list').dragDropList({trash_id:'z_trash',trash_callback:_onImageTrash,change_callback:_onImageChange});self.find(".z-edit-photo").click(_onImageEdit);}
function _closePopup(fade)
{$('.z-popup:visible').eventPopup_close({fade:fade});}
function _onImageTrash(list_elem,item_elem)
{setTimeout(function(){_updateImagePreviewDisplay();},1000);var image_id=_getImageIdFromItem(item_elem);var image_ids=_getImageIdsFromList(list_elem);var queued_event_id=$("#queued_event_id").val();$.getJSON(["/listings/remove_image_inline?image_id=",image_id,"&queued_event[id]=",queued_event_id,"&queued_event[image_id_list]=",image_ids.join(',')].join(''),function(json){if(json['success']){$('#queued_event_image_id_list').val(json['image_ids']);}
else{alert("Error removing image");}});}
function _onImageChange(list_elem)
{$('#queued_event_image_id_list').val(_getImageIdsFromList(list_elem).join(','));$('#z_images_list li').removeClass('z-first').eq(0).addClass('z-first');self.parents('form').trigger('data-changed',self);}
function _onImageUpload()
{characterCount('image_credit','image_credit_count',150);characterCount('image_caption','image_caption_count',250);$("#z_image_uploader").eventPopup({submit_button:"#z_add_photos_add"});return false;}
function _onImageEditSubmit()
{$("#z_image_upgrade .z-uploading-indicator").show();$("#z_listing_event_form").ajaxSubmit({url:'/listings/edit_image',success:_onImageSubmitSuccess,iframe:false,dataType:'json'});return false;}
function _onImageEdit()
{var id=this.id.slice(11);var image_info=_getImageInfoById(id);$('#edit_image_id').val(id);$('#edit_image_credit').val(image_info.credit);$('#edit_image_caption').val(image_info.caption);characterCount('edit_image_credit','edit_image_credit_count',150);characterCount('edit_image_caption','edit_image_caption_count',250);$("#z_image_editor").eventPopup({submit_button:"#z_edit_photo"});return false;}
function _updateImagePreviewDisplay()
{if($("#z_images_list li").length>0){$("#z_image_preview").show();}else{$("#z_image_preview").hide();}
_repaint();}
function _onImageSubmitSuccess(json)
{_closePopup(true);if(json['success']){$('#z_image_display').html(json['image_html']);$('#queued_event_image_id_list').val(json['image_ids']);_bindImageListHandlers();}else{alert(json['errors']);}
_clearFileInput($('#image_upload'));$('#image_credit').val('');$('#image_caption').val('');$('#edit_image_credit').val('');$('#edit_image_caption').val('');_updateImagePreviewDisplay();self.parents('form').trigger('data-changed',self);}
function _onImageSubmit()
{if($ZJQuery("#image_upload").val().length==0){alert("Oops!  Please select an image.");return false;}
_closePopup();_showBusyPopup('Uploading Photo','Please wait while your photo is uploaded.');$("#z_listing_event_form").ajaxSubmit({url:'/listings/upload_image',success:_onImageSubmitSuccess,iframe:false,dataType:'json'});return false;}
function _onLogoUpload()
{$("#z_logo_uploader").eventPopup({submit_button:"#z_add_logo_add"});return false;}
function _onLogoSubmitSuccess(json)
{_closePopup();if(json['success']){$('#z_logo_display').html(json['image_html']);}else{alert(json['errors']);}
_clearFileInput($('#enhancements_logo_image'));$('#z_logo_preview').show();_repaint();self.parents('form').trigger('data-changed',self);}
function _onLogoSubmit()
{if($("#enhancements_logo_image").val().length==0){alert("Oops!  Please select an image.");return false;}
_closePopup();_showBusyPopup('Uploading Logo','Please wait while your logo uploaded.');$("#z_listing_event_form").ajaxSubmit({url:'/listings/upload_logo',success:_onLogoSubmitSuccess,iframe:true,dataType:'json'});return false;}
function _onLogoRemoveSuccess(json)
{if(json['success']){$('#z_logo_display').html('');}else{alert(json['errors']);}
$('#z_logo_preview').hide();_repaint();self.parents('form').trigger('data-changed',self);}
function _onLogoRemove()
{$("#z_listing_event_form").ajaxSubmit({url:'/listings/remove_logo',success:_onLogoRemoveSuccess,iframe:false,dataType:'json'});return false;}
function _clearLinkInfo()
{$("#url_id").val('');$("#url_url_type").val('1');$("#url_anchor_text").val('');$("#url_location").val('');$("#url_price_range").val('');$("#url_provider").val('');$("#url_offer").val('');$('#z_price_range').hide();$('#z_provider_offer').hide();$('#z_presale_dates').hide();}
function _onUpgradeOptionChanged(){var checkbox=$(this);checkbox.attr('disabled','disabled');enabled=checkbox.attr('checked');if(enabled){$.post("/listings/enable_enhancements",{"listing_class":settings.listing_class,"listing_id":settings.listing_id,"enable":true},function(retVal){checkbox.removeAttr('disabled');self.formTrigger('cost-changed',[self]);return false;});_enableControls();}
else{$(this).attr('disabled','disabled');$.post("/listings/enable_enhancements",{"listing_class":settings.listing_class,"listing_id":settings.listing_id,"enable":false},function(retVal){checkbox.removeAttr('disabled');self.formTrigger('cost-changed',[self]);return false;});_disableControls();}
return true;}
function _onLinkUpload()
{$("#z_link_uploader").eventPopup({submit_button:"#z_add_link_submit"}).one('popup-closed',function(){_clearLinkInfo();});return false;}
function _onLinkSubmitSuccess(json)
{_closePopup();if(json['success']==true){$("#z_links_display").html(json['url_html']);$('#z_links_preview').show();_repaint();}else{alert(json['error_message']);}}
function _onLinkSubmit()
{var id=$("#url_id").val();var location=$("#url_location").val();var text=$("#url_anchor_text").val();var url_type=$("#url_url_type").val();if(location==""||text==""){alert("Oops!  Please supply a location and text for your URL.  For example: http://www.example.com");return false;};if(id){$('#z_listing_event_form').ajaxSubmit({url:'/listings/edit_link',success:_onLinkSubmitSuccess,iframe:false,dataType:'json'});}
else{$('#z_listing_event_form').ajaxSubmit({url:'/listings/upload_link',success:_onLinkSubmitSuccess,iframe:false,dataType:'json'});}
return false;}
function _onLinkRemoveSuccess(json)
{if(json['success']==true){$("#z_links_display").html(json['url_html']);if($("#z_links_display ul.z-special-links li").length==0){$("#z_links_preview").hide();_repaint();}}
else{alert("Oops!  There was a problem deleting your link.");}}
function _onLinkRemove(e,url_id)
{$('#z_listing_event_form').ajaxSubmit({url:'/listings/remove_link?url_id='+url_id,success:_onLinkRemoveSuccess,iframe:false,dataType:'json'});return false;}
function _onLinkTypeChange()
{var ticket_type='4';var url_type=$('#url_url_type').val();if(url_type==ticket_type){$('#z_price_range').show();$('#z_provider_offer').show();$('#z_presale_dates').show();}else{$('#z_price_range').hide();$('#z_provider_offer').hide();$('#z_presale_dates').hide();}}
function _onLinkEdit(e,id,url_type,anchor_text,location,price_range,provider,offer,presale_start_date,presale_end_date)
{$("#url_id").val(id);$("#url_url_type").val(url_type);$("#url_anchor_text").val(anchor_text);$("#url_location").val(location);$("#url_price_range").val(price_range);$("#url_provider").val(provider);$("#url_offer").val(offer);$("#url_offer").val(offer);$("#url_presale_start_date").val(presale_start_date);$("#url_presale_end_date").val(presale_end_date);_onLinkTypeChange();$("#z_link_uploader").eventPopup({submit_button:"#z_add_link_submit"}).one('popup-closed',function(){_clearLinkInfo()
_onLinkTypeChange();});return false;}
function _onNewsUpload()
{$("#z_news_uploader").eventPopup({submit_button:"#z_news_submit"});return false;}
function _onNewsSubmit()
{_closePopup();var news=$('#input_news').val();$('#z_news_display').html(news);if(news&&news.length){$('#z_news_preview').show();}
else{$('#z_news_preview').hide();}
_repaint();$('#z_listing_event_form').trigger('data-changed');return false;}
function _onVideoUpload()
{$("#z_video_uploader").eventPopup({submit_button:"#z_upload_button"});return false;}
function _updateVideoPreviewDisplay()
{if($("#z_video_display").children().length==0){$("#z_video_preview").hide();}else{$("#z_video_preview").show();}
_repaint();}
function _onVideoUploaded(e,retval)
{$('#z_video_display').html(retval);_closePopup();_updateVideoPreviewDisplay();$('#z_listing_event_form').trigger('data-changed');}
function _onVideoRemove()
{$.post("/listings/delete_video",{listing_class:settings.listing_class,listing_id:settings.listing_id},function(responseText){$('#z_video_display').html(responseText);$("#complete").hide();_updateVideoPreviewDisplay();$('#z_listing_event_form').trigger('data-changed');});return false;}
function _onCloseOverlay()
{_closePopup();return false;}
function _enableControls(){self.find(".z-premium-option-overlay,.z-premium-option-marketing,").hide();self.find(".z-premium-option-controls *").removeAttr('disabled');$("#z_input_event_description_count").hide();_onImageChange($("#z_images_list").get(0));}
function _disableControls(){self.find(".z-premium-option-overlay,.z-premium-option-marketing,").show();self.find(".z-premium-option-controls *").attr('disabled','disabled');$("#z_input_event_description_count").show();$('#queued_event_image_id_list').val('');}
_bindImageListHandlers();$('.z-close-overlay').click(_onCloseOverlay);$('#z_upload_photo').click(_onImageUpload);$('#z_upload_logo').click(_onLogoUpload);$('#z_upload_links').click(_onLinkUpload);$('#z_upload_news').click(_onNewsUpload);$('#z_upload_video').click(_onVideoUpload);$('#z_add_photos_add').click(_onImageSubmit);$('#z_edit_photo').click(_onImageEditSubmit);$('#z_add_logo_add').click(_onLogoSubmit);$('#z_add_link_submit').click(_onLinkSubmit);$('#url_url_type').change(_onLinkTypeChange);$('#z_news_submit').click(_onNewsSubmit);$('#z_listing_event_form').bind('remove-logo',_onLogoRemove);$('#z_listing_event_form').bind('remove-link',_onLinkRemove);$('#z_listing_event_form').bind('edit-link',_onLinkEdit);$('#z_listing_event_form').bind('video-uploaded',_onVideoUploaded);$('#z_listing_event_form').bind('remove-video',_onVideoRemove);$('#z_enhance_checkbox').click(_onUpgradeOptionChanged);self.find('.z-premium-option-marketing,.z-premium-option-overlay').click(function(){$('#z_enhance_checkbox').attr('checked',true).click();});if(settings.internal_user||settings.paid_for||$('#z_enhance_checkbox').attr('checked')){_enableControls();}
else{_disableControls();}});return this;};})($ZJQuery,Zvents);(function($,Z){$.fn.datePicker=function(config){var settings={popup_container:"z_popup_start_date_container",popup_id:"z_popup_start_date",date_input:"z_start_date_advanced",popup_trigger:"z_show_popup_start_date",min_date:new Date(),max_date:new Date()};settings=$.extend(settings,config);function _popupSelectHandler(type,args,self){var date=Z.Date.formatMdyDate(this.toDate(args[0][0]));if(this.id==settings.popup_id){$('#'+settings.date_input).val(date);}
this.hide();};this.each(function(){var self=$(this);var min_date=settings.min_date;var max_date=settings.max_date;max_date.setFullYear(max_date.getFullYear()+3);var today=Z.Date.formatMdyDate(min_date);var popup_date=new YAHOO.widget.Calendar(settings.popup_id,settings.popup_container,{title:"Choose a date:",close:true,mindate:min_date,maxdate:max_date});popup_date.selectEvent.subscribe(_popupSelectHandler,popup_date,true);popup_date.render();YAHOO.util.Event.addListener(settings.popup_trigger,"click",popup_date.show,popup_date,true);YAHOO.util.Event.addListener(settings.date_input,"click",popup_date.show,popup_date,true);$(window).one('unloading',function(){popup_date.selectEvent.unsubscribe();YAHOO.util.Event.removeListener(settings.popup_trigger);YAHOO.util.Event.removeListener(settings.date_input);});});return this;}})($ZJQuery,Zvents);(function($,Z){function _close(self,fade)
{if(fade){self.fadeOut();}
else{self.hide();}
self.data('cleanup')();}
$.fn.popup=function(config){this.each(function(){var self=$(this);var settings={fade:false,underlay_close:true,remote:false,close_on_form_submit:true,submit_button:null};function _interceptReturn(e)
{if(e.which==13){if(settings.submit_button){$(settings.submit_button).click();}
else{$(this).filter(':submit, :button').click();}
return false;}}
function _closeOnCloseIcon(){self.find(".close-icon-img").click(function(){_close(self,settings.fade);});}
function _closeOnFormSubmitIfNecessary(){if(settings.close_on_form_submit){self.find('form').submit(function(){$(this).ajaxSubmit();_close(self);return false;});}}
function _loadRemoteIfNecessary(){if(settings.remote){self.load(settings.url,function(){_closeOnFormSubmitIfNecessary();_showPopup();});}}
function _showPopup(){self.find('select,.z-underlay-hidden').css('visibility','visible');self.find(':input').keypress(_interceptReturn);if(settings.fade){self.fadeIn();}
else{self.show();}}
$.extend(settings,config);var scroll_top=$(document).scrollTop();var form_offset=$("#wrapper").offset();var underlay=self.underlay({on_click:_close}).docMove({top:scroll_top+100,left:form_offset.left+200}).bind('underlay-click',function(){if(settings.underlay_close)_close(self);});_loadRemoteIfNecessary();_closeOnCloseIcon();self.find('select,.z-underlay-hidden').css('visibility','visible');self.data('cleanup',function(){self.find(':input').unbind('keypress',_interceptReturn);underlay.underlay_remove();self.trigger('popup-closed');});});return this;}
$.fn.popup_close=function(config){this.each(function(){var self=$(this);var settings={fade:false};$.extend(settings,config);_close(self,settings.fade);});return this;}})($ZJQuery,Zvents);




