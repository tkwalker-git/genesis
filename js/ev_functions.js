// JavaScript Document

function removeText(value,text,id){
		if(value==text){
			document.getElementById(id).value='';
			document.getElementById(id).style.color='#000';
			}
		}

function returnText(text,id){
	if(document.getElementById(id).value==''){
	document.getElementById(id).value=text;
	document.getElementById(id).style.color='#555';
	}
	}

function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57)){
		 alert ("This is not a number! Please enter a valid number");
		 return false;
		 }
         return true;
}

function timeBeforeEventStart(value){
	if(value!='specify_day'){
		document.getElementById('start_sales_date').value='';
		document.getElementById('start_sales_date').disabled=true;
		document.getElementById('start_sales_hrs').disabled=true;
		document.getElementById('start_sales_min').disabled=true;
		document.getElementById('start_sales_ampm').disabled=true;
		document.getElementById('start_sales_before_days').disabled=false;
		document.getElementById('start_sales_before_hrs').disabled=false;
		document.getElementById('start_sales_before_min').disabled=false;
	}else{
		document.getElementById('start_sales_date').disabled=false;
		document.getElementById('start_sales_hrs').disabled=false;
		document.getElementById('start_sales_min').disabled=false;
		document.getElementById('start_sales_ampm').disabled=false;
		document.getElementById('start_sales_before_days').disabled=true;
		document.getElementById('start_sales_before_days').value='';
		document.getElementById('start_sales_before_hrs').disabled=true;
		document.getElementById('start_sales_before_hrs').value='';
		document.getElementById('start_sales_before_min').disabled=true;
		document.getElementById('start_sales_before_min').value=''
	}}
	
	
function timeBeforeEventEnd(value){
	if(value!='specify_day2'){
		document.getElementById('end_sales_date').value='';
		document.getElementById('end_sales_date').disabled=true;
		document.getElementById('end_sales_hrs').disabled=true;
		document.getElementById('end_sales_min').disabled=true;
		document.getElementById('end_sales_ampm').disabled=true;
		document.getElementById('end_sales_before_days').disabled=false;
		document.getElementById('end_sales_before_hrs').disabled=false;
		document.getElementById('end_sales_before_min').disabled=false;
	}else{
		document.getElementById('end_sales_date').disabled=false;
		document.getElementById('end_sales_hrs').disabled=false;
		document.getElementById('end_sales_min').disabled=false;
		document.getElementById('end_sales_ampm').disabled=false;
		document.getElementById('end_sales_before_days').disabled=true;
		document.getElementById('end_sales_before_days').value='';
		document.getElementById('end_sales_before_hrs').disabled=true;
		document.getElementById('end_sales_before_hrs').value='';
		document.getElementById('end_sales_before_min').disabled=true;
		document.getElementById('end_sales_before_min').value=''
	}}
	
function advOptHidSho(){
	var status = document.getElementById('st').innerHTML;
	if(status=='Hide'){
		document.getElementById('advance_options').style.display='none';
		document.getElementById('st').innerHTML='Show<br><br><br><br><br><br><br><br><br><br><br><br>';
		}
		else{
		document.getElementById('advance_options').style.display='block';
		document.getElementById('st').innerHTML='Hide';			
			}
	}
	

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
	  

function updatseTable(){
var costum_price1st	=	$('#costum_price1st').val();
var mainTitle		=	$('#mainTitle').val();


$('#priceTable').append('<div><div class="ev_fltlft" style="width:240px; padding-right:10px">Test Ticket</div><div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center">$15.00</div><div class="ev_fltlft" style="width:120px; padding-right:10px; text-align:center">$0.89</div><div class="ev_fltlft" style="width:164px; padding-right:10px; text-align:center">$15.89</div><div class="clr" style="height:5px"></div></div>');
}


