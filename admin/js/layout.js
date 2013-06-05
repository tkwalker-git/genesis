(function($){
	var initLayout = function() {
		var hash = window.location.hash.replace('#', '');
		var currentTab = $('ul.navigationTabs a')
							.bind('click', showTab)
							.filter('a[rel=' + hash + ']');
		if (currentTab.size() == 0) {
			currentTab = $('ul.navigationTabs a:first');
		}
		showTab.apply(currentTab.get(0));
		$('#colorpickerHolder').ColorPicker({flat: true});
		$('#colorpickerHolder2').ColorPicker({
			flat: true,
			color: '#00ff00',
			onSubmit: function(hsb, hex, rgb) {
				$('#colorSelector2 div').css('backgroundColor', '#' + hex);
			}
		});
		$('#colorpickerHolder2>div').css('position', 'absolute');
		var widt = false;
		$('#colorSelector2').bind('click', function() {
			$('#colorpickerHolder2').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});
		$('#top_pannel_color, #menu_color, #site_bg_color').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});
		$('#colorSelector').ColorPicker({
			color: '#0000ff',
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#colorSelector div').css('backgroundColor', '#' + hex);
			}
		});
	};
	
	var showTab = function(e) {
		var tabIndex = $('ul.navigationTabs a')
							.removeClass('active')
							.index(this);
		$(this)
			.addClass('active')
			.blur();
		$('div.tab')
			.hide()
				.eq(tabIndex)
				.show();
	};
	
	EYE.register(initLayout, 'init');
})(jQuery)



	function writ(value,id){
	  document.getElementById('fld_'+id).value=value;
	  }
	function rmv(id){
	  document.getElementById('fld_'+id).value='';
	  document.getElementById('costum_price'+id).value='';
	  document.getElementById('fld_'+id).checked=false;
	  }
	function slct(id){
	  document.getElementById('fld_'+id).checked=true;
	  document.getElementById('free'+id).checked=false;
	  }
	function fldDisabledFalse(id){
	  document.getElementById('free'+id).checked=false;
	  document.getElementById('costum_price'+id).focus();
	  }
	function removeRow(id,del){
	  document.getElementById("title"+id).value='';
	  document.getElementById("costum_price"+id).value='';
	  if(del=='yes'){
	  document.getElementById("del_"+id).value='yes';
	  }else{
		  document.getElementById("del_"+id).value='no';
		  }
//	  document.getElementById("fld_"+id).checked=false;
//	  document.getElementById("free"+id).checked=false;
	  document.getElementById("addrow"+id).style.display='none';
	  }


