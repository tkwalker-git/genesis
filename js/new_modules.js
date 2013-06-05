function showOverlayer(url,twidth){
	var twr = twidth;
	$("#overlayer").html('');
	if ( twidth == '' || twidth == null || twidth=='rsvp' )
		twidth = 400;

	$('#overlayer').load(url);
	
	var size = getPageSize();
	var scroll = getPageScroll();

	var obg = document.getElementById('page-bg');
	obg.style.width = size[0]+'px';
	obg.style.height = size[1]+'px';
	
	var ovr = document.getElementById('overlayer');
	
	//obg.style.display = 'block';
	//ovr.style.display = 'block';
	$("#page-bg").css('filter', 'alpha(opacity=70)');
	$("#overlayer").fadeIn(200, "linear");
    $("#page-bg").fadeIn(200);
	
	var winh = $(window).height();
	winh = winh/2;
	mid = ($(document).scrollTop());
	mid = mid + ( (winh/2) - 100);
	
	var sizes = getPageSize();
	
/*if($.browser.msie && parseInt($.browser.version, 10) == 7 && twr=='rsvp')
	var left = parseInt(-700);
else*/
	var left = parseInt((sizes[0] - twidth) / 2);
	
	ovr.style.left = left + 'px'; 
	$("#overlayer").css("top",mid+'px' );

}
function hideOverlayer(){
	
	var obg = document.getElementById('page-bg');
	var ovr = document.getElementById('overlayer');
	
	
    $("#page-bg").fadeOut(500);
	$("#overlayer").fadeOut(500, "linear");
	$("#ajax_loader_gif").hide();
	$('#overlayer').hide();
}

function hide(){
	hideOverlayer();
}