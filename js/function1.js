function preload() 
{
	document.write('<div style="display:none;"><img src="images/y-main_top_img.jpg" /><img src="images/y-main_middle_img.jpg" /><img src="images/y-main_bottom_img.jpg" /><img src="images/g-main_top_img.jpg" /><img src="images/g-main_middle_img.jpg" /><img src="images/g-main_bottom_img.jpg" /><img src="images/p-main_bottom_img.jpg" /><img src="images/p-main_middle_img.jpg" /><img src="images/p-main_top_img.jpg" /><img src="images/o-main_top_img.jpg" /><img src="images/o-main_middle_img.jpg" /><img src="images/o-main_bottom_img.jpg" /><img src="images/gray-main_top_img.jpg" /><img src="images/gray-main_bottom_img.jpg" /><img src="images/gray-main_middle_img.jpg" /><img src="images/right_box_ro_tab_bg.jpg" /><img src="images/right_box_last_ro_tab_bg.jpg" /><img src="images/left_box_ro_tab_bg.jpg" /><img src="images/left_box_last_ro_tab_bg.jpg" /><img src="images/yellow_arrow.gif" /><img src="images/right_box_last_tab_bg.jpg" /><img src="images/left_box_last_tab_bg.jpg" /><img src="images/left_box_tab_bg.jpg" /><img src="images/right_box_tab_bg.jpg" /><img src="images/h-nipkin--bob.jpg" /><img src="images/nipkin--bob.jpg" /><img src="images/h-join-btn.jpg" /><img src="images/join_btn.jpg" /><img src="images/h-find-out-btn.jpg" /><img src="images/findout_btn.jpg" /><img src="images/get_free.jpg" /><img src="images/h_get_free.jpg" /><img src="images/right_box_last_ac_tab_bg.jpg" /><img src="images/right_box_ac_tab_bg.jpg" /><img src="images/left_box_ac_tab_bg.jpg" /><img src="images/left_box_last_ac_tab_bg.jpg" /><img src="images/button_green.jpg" /><img src="images/button_green_h.jpg" /><img src="images/button_green_ac.jpg" /><img src="images/inside_category_tab.jpg" /><img src="images/inside_category_tab_h.jpg" /><img src="images/inside_category_tab_ac.jpg" /><img src="images/software_hd.jpg" /><img src="images/by_act_btn.jpg" /><img src="images/h-buy_act_btn.jpg" /><img src="images/speak_btn.jpg" /><img src="images/h-speak_btn.jpg" /><img src="images/get_an_online_btn.jpg" /><img src="images/h-get_an_online_btn.jpg" /><img src="images/get_an_pda_btn.jpg" /><img src="images/h_get_an_pda_btn.jpg" /><img src="images/buy_act_e-store_btn.jpg" /><img src="images/h-buy_act_e-store_btn.jpg" />   <img src="images/add-to-cart-btn.jpg" /><img src="images/h-add-to-cart-btn.jpg" /><img src="images/buy_act_account_btn.jpg" /><img src="images/h-buy_act_account.jpg" /><img src="images/buy_act_add_ones_btn.jpg" /><img src="images/h-buy_act_add_ons.jpg" /><img src="images/done_btn.jpg" /><img src="images/h-done_btn.jpg" /><img src="images/ac_done_btn.jpg" /><img src="images/find_act_trainer.jpg" /><img src="images/h-find_act_trainer.jpg" /><img src="images/request-franchise-btn.jpg" /><img src="images/ac-request-franchise-btn.jpg" /><img src="images/h-request-franchise-btn.jpg" /><img src="images/franchies_info_btn.jpg" /><img src="images/h-franchies_info_btn.jpg" /><img src="images/find_act_btn.jpg" /><img src="images/h-find-act-btn.jpg" /> <img src="images/buy_act_hosting_service_btn.jpg" /><img src="images/h-buy_act_hosting_service_btn.jpg" /><img src="images/add_ac_btn.jpg" /><img src="images/add_btn.jpg" /><img src="images/h_add_btn.jpg" /> <img src="images/back_to_cart_ac.jpg" /><img src="images/back_to_cart.jpg" /><img src="images/h-back_to_cart.jpg" /><img src="images/remove_ac_btn.jpg" /><img src="images/remove_btn.jpg" /><img src="images/<img src="images/go-to-ac-btn.jpg"/> <img src="images/<img src="images/h-go-to-btn.jpg"/><img src="images/<img src="images/go-to-btn.jpg"/><img src="images/<img src="images/totalac_btn.jpg"/><img src="images/<img src="images/h-total-btn.jpg.jpg"/></div>');
}


var act='';
function show(sid,cid)
{
	if (document.getElementById(cid).className=='sel')
	{
		act=cid;
	}
	document.getElementById(sid).style.display='block';
	document.getElementById(cid).className='sel';
	
}
function hide(sid,cid)
{
	document.getElementById(sid).style.display='none';
	document.getElementById(cid).className='';
	if (act==cid)
	{
		document.getElementById(act).className='sel';
	}
}



function clearText(thefield) {
  if (thefield.defaultValue==thefield.value) { thefield.value = "" }
} 
function replaceText(thefield) {
  if (thefield.value=="") { thefield.value = thefield.defaultValue }
}


function popup_show(sid)
{
	document.getElementById(sid).style.display='block';
}
function popup_hide(sid)
{
	document.getElementById(sid).style.display='none';
}

