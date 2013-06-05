function clearText(thefield) {
  if (thefield.defaultValue==thefield.value) { thefield.value = "" }
} 
function replaceText(thefield) {
  if (thefield.value=="") { thefield.value = thefield.defaultValue }
}


function popup_show(d,b)
{
	 document.getElementById(d).style.display='block';
	 document.getElementById(b).style.zIndex='2';
}
function popup_hide(d,b)
{
	 document.getElementById(d).style.display='none';
	  document.getElementById(b).style.zIndex='1';
}


function fp_show(tab,cls) 
{
	i=1;
	
	while (document.getElementById("tab_"+i))
	 {
	 document.getElementById("tab_"+i).style.display='none';
	 document.getElementById("a"+i).className='';
	 i++;
	 
	 }
	 document.getElementById(tab).style.display='block';
	 document.getElementById(cls).className='active';
}



function form_detail(tab,img)
{
	if(document.getElementById(tab).style.display=='block')
	{
		document.getElementById(tab).style.display='none';
		document.getElementById(img).src='images/expand.gif';
	}
	else
	{
		document.getElementById(tab).style.display='block';
		document.getElementById(img).src='images/collapse.gif';
	}
}

