// JavaScript Document

function loadDeals(abs_url,direction,pagenum)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loaddeals.php",  
			data:"direction="+direction+"&page="+pagenum,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#deals").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function getMyEventwall(abs_url,value){
	$.ajax({  
			type: "POST",
			url: abs_url+"ajax/loadeventwall.php",
			data:"value="+value,
			
			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#eventwall").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function getPages(abs_url,loadPage,responsId,event_id)
{

	$.ajax({  
			type: "POST",
			url: abs_url+"ajax/"+loadPage,
			data:"event_id="+event_id,
			
			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#"+responsId).html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}




function getFlayer(abs_url,event_id,type)
{
	if(type=='annual'){
		var path = abs_url+"ajax/loadflayerannual.php";
		}
	else if(type=='fb' || type=='fbs'){
		var path = abs_url+"ajax/loadflayerFB.php";
		}
	else{
		var path = abs_url+"ajax/loadflayer.php";
		}
	if(type=='fbs'){
		var nb = "nba";
		}
	$.ajax({  
			type: "POST",
			url: path,  
			data:"event_id="+event_id+"&e_type="+nb,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#flayermain").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function loadGroups(abs_url,direction,pagenum)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"loadgrounp.php",  
			data:"direction="+direction+"&page="+pagenum,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#groupRecord").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}

function loadPurchasedDeals(abs_url,direction,pagenum)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadpurchaseddeals.php",  
			data:"direction="+direction+"&page="+pagenum,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#purchasedpeals").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function loadPurchased(abs_url)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"loadpurchased.php",  
			data:"",

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#dealsarea").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}

function findDeals(abs_url)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadfinddeals.php",  
			data:"",  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#dealsarea").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}

function searchDeals(abs_url,text)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadsearchdeal.php",  
			data:"text="+text,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#showSearchDealsResult").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}

function loadsearchdel(abs_url,direction,pagenum,text)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadsearchdelpage.php",
			data:"direction="+direction+"&page="+pagenum+"&text="+text,

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#showSearchDealsResult").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
	}


function loadDeals2(abs_url,direction,pagenum)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loaddeals2.php",  
			data:"direction="+direction+"&page="+pagenum,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#deals").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}



function getHangoutGroups(abs_url)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadHangoutGroups.php",  
			data:"abs_url="+abs_url,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
			
				$("#record1").html(resp);

				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function getMyFriends(abs_url)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadMyFriends.php",  
			data:"abs_url="+abs_url,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{
			$("#record1").html(resp);	
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function getGroupMemberProfile(abs_url,member_id,img)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadGroupMemberProfile.php",  
			data:"member_id="+member_id+"&img="+img,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{
			
			
			$('.roundImg').attr('src',abs_url+'images/frame.gif');
			$('#roundImg'+member_id).attr('src',abs_url+'images/frameOver.gif');
			$('#bigImgss').css("background-image", "url("+img+")");
			
			
			
				$("#inr").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function loadDetail(abs_url,event_id)
{
	//	var event_id = 3047;
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loaddetail.php",  
			data:"event_id="+event_id,  

			beforeSend: function()
			{
		//		showOverlayer('ajax/loader.php');
			},
			success: function(resp)
			{  
			$("#showPages").html(resp);

				
			}, 

			complete: function()
			{
			//	hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}


function loadNextSubCategory(abs_url,category_id,sub_category_id,direction,pagenum)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadevents.php",  
			data:"category_id="+category_id+"&sub_category_id="+sub_category_id+"&direction="+direction+"&page="+pagenum,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#sbc"+sub_category_id).html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}

function showNextRecomeEvents(abs_url,category_id,title,direction,pagenum,tot,list1)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/recomended_events_cat.php",  
			data:"category_id="+category_id+"&title="+title+"&direction="+direction+"&page="+pagenum+"&tot="+tot+"&list="+list1,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#recomended"+category_id).html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}

function loadNextRecEvent(abs_url,venue_id,event_id,direction,pagenum)
{
	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadrecevents.php",  
			data:"venue_id="+venue_id+"&event_id="+event_id+"&direction="+direction+"&page="+pagenum,  

			beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(resp)
			{  
				$("#locationeventscontainer").html(resp);
				
			}, 

			complete: function()
			{
				hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}

function showHowWeDetermine(abs_url)
{
	showOverlayer(abs_url + 'ajax/recommened_determine.php',400);	
}

function addToEventWall(abs_url,event_id)
{
	showOverlayer(abs_url + 'ajax/addtowall_confirmation.php?event_id='+event_id,400);	
}

function showReviewBox(abs_url,event_id1,ty1)
{	
	showOverlayer(abs_url + 'ajax/review.php?event_id='+event_id1+'&type='+ty1,600);
}

function showHintbox(){

		showOverlayer(abs_url + 'ajax/review.php',600);

	}


function hideOverlayer(rl){
	
	var obg = document.getElementById('page-bg');
	var ovr = document.getElementById('overlayer');
	
	
    $("#page-bg").fadeOut(500);
	$("#overlayer").fadeOut(500, "linear");
/*	document.getElementById('ajax_loader_gif').style.display = 'none'; */
	 $("#ajax_loader_gif").hide();
	 $('#overlayer').hide();


	if ( rl == 1 )
		window.location.href = window.location.href;
	
	//ovr.style.display = 'none';
	//obg.style.display = 'none';

}

function showOverlayer(url,twidth){
	$("#overlayer").html('');
	if ( twidth == '' || twidth == null )
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
	$("#overlayer").fadeIn(500, "linear");
    $("#page-bg").fadeIn(500);
	
	var winh = $(window).height();
	winh = winh/2;
	mid = ($(document).scrollTop());
	mid = mid + ( (winh/2) - 100);
	
	var sizes = getPageSize();
	var left = parseInt((sizes[0] - twidth) / 2) ;
	ovr.style.left = left + 'px'; 
	$("#overlayer").css("top",mid+'px' );

}

function getPageScroll(){
     var scrolly = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;   
     var scrollx = typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement.scrollLeft;   
	
	var arrayScroll = new Array(scrollx, scrolly);
	return arrayScroll;	
}

function getPageSize() {
	var xScroll, yScroll;

	if (window.innerHeight && window.scrollMaxY) {
		xScroll = window.innerWidth + window.scrollMaxX;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}

	var windowWidth, windowHeight;

	if (self.innerHeight) {	// all except Explorer
		if(document.documentElement.clientWidth){
			windowWidth = document.documentElement.clientWidth;
		} else {
			windowWidth = self.innerWidth;
		}
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}

	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else {
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){
		pageWidth = xScroll;
	} else {
		pageWidth = windowWidth;
	}

	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
	return arrayPageSize;
}


function postComment(abs_url)
{
	var id1	 	= $("#id1").val();
	var userid 	= $("#userid").val();		
	var ty 		= $("#ty").val();
	var reviews = $("#reviews").val();
	var rating 	= $('input:radio[name=rating]:checked').val();

	$.ajax({  
	   
			type: "POST",  
			url: abs_url+"ajax/submit_review.php",  
			data:"id1="+id1+"&userid="+userid+"&ty="+ty+"&reviews="+reviews+"&rating="+rating,  
			//dataType:"json",

			beforeSend: function()
			{
				if (reviews == '' && rating == '' )
					$("#errordiv").html('Please enter Review OR Rate the event. Both fields cannot be empty.');	
				else	
					$("#errordiv").html('Submitting Request...');
			},
			success: function(resp)
			{  
				if ( resp == '-1' ) {
					$("#errordiv").html('You have already submited review.');
					$("#maincdiv").html('');
				} else if ( resp == '1' ) {
					$("#errordiv").html('');
					$("#maincdiv").html('Review is submitted successfuly.');
				} else
					$("#errordiv").html('There is some error. Please try again.');
			}, 

			complete: function()
			{
				//$("#login_error").html('');
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
	
}

function deleteEventFromWall(url)
{
	var con = confirm("Are you sure to delete this event?")

	if (con) 
		window.location.href = url;	
}

function reviewHelpFull(abs_url,review_id,state)
{
	url = abs_url + 'ajax/helpfull_status.php?review_id=' + review_id + '&status=' + state;
	showOverlayer(url ,400);	
	
	//window.location.href=url;
}

function windowOpener(windowHeight, windowWidth, windowName, windowUri)
{
	var centerWidth = (window.screen.width - windowWidth) / 2;
    var centerHeight = (window.screen.height - windowHeight) / 2;
    newWindow = window.open(windowUri, 'windowName', 'scrollbars=1,width=' + windowWidth + ',height=' + windowHeight + ',left=' + centerWidth + ',top=' + centerHeight);

    newWindow.focus();
    return newWindow.name;
}

//function loadOrderTicket(abs_url,event_id,ids,qtys){
//	 {  
//		 $.ajax({  
//			type: "GET",  
//			url: abs_url+"ajax/load_order_tickets.php",
//			data: "event_id=" + event_id + "&ids=" + ids + "&qtys=" +qtys,  
//			dataType: "text/html",  
//			success: function(html){
//			$("#showdata").html(html);
//			}
//	   	});
//}
	
function showimage(abs_url,image_url){
	
	showOverlayer(abs_url+'ajax/load_images.php?image_url='+encodeURI(image_url),'496');
//	showOverlayer(abs_url+'ajax/load_images.php?image_url='+image_url);
	
	}
	
	
	
function showText(abs_url,table,field,id){
	showOverlayer(abs_url+'ajax/load_text.php?table='+table+'&field='+field+'&id='+id,'400');
	}
	
	
function showPage(abs_url,loadpage,event_id){
	if(event_id!=''){
	event_id = '?event_id='+event_id;
		}
	showOverlayer(abs_url+'ajax/campaign.php'+event_id,'400');
	}
	
	
	
function rsvp(abs_url,event_id){
	showOverlayer(abs_url+'ajax/rsvp.php?event_id='+event_id,'400');
	}
	


function loadActvEvnt(abs_url,direction,pagenum){

	$.ajax({  
			type: "POST",  
			url: abs_url+"ajax/loadactvevnt.php",  
			data:"direction="+direction+"&page="+pagenum,  

			beforeSend: function()
			{
			//	showOverlayer(abs_url+'ajax/loader.php');
			$('#img').html('<img src="'+abs_url+'images/ajax-loader2.gif" style="padding-top:3px">');
			
			},
			success: function(resp)
			{  
				$("#activeEvent").html(resp);
				
			}, 

			complete: function()
			{
			//	hideOverlayer();
			},
 
			error: function(e)
			{  
				//alert('Error: ' + e);  
			}  
	});
}
