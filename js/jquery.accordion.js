	$(document).ready(function () {
			 var container			=	$('#accordion');
			 var h3					=	$('h3', container);
			 var box				=	$('#box', container);
			 var h3_size			=	h3.size();
		
		
		$(container).css('min-height','380px');
		$(h3).css('cursor','pointer');
		$(box).css('display','none');
		var i=1;
		var bg = '';
		
		
		$(h3).each(function() {
			 if(i==1){
			bg = '#43bb9a';
			}
		else if(i==2){
			bg = '#55b86e';
			}
		else if(i==3){
			bg = '#66b645';
			}
		else if(i==4){
			bg = '#73b426';
			}
		else{
			bg = '#73b426';
			}
		 	$(this).attr('id',i);
			$(this).css('background',bg);
		 	i++;
		});
		
		var i=1;
		$(box).each(function() {
			$(this).attr('id',"box"+i);
		 	i++;
		});
			
		$('#box1').css('display','block');	 
		$(h3).click(function(){
			var showId = $(this).attr("id");
			var checkThisShow	=	$('#box'+showId).css("display");
				if(checkThisShow!='block'){
					$(box).slideUp(1000);
					$('#box'+showId).slideDown(1000);
				}
		});
		});