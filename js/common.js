var sInvalidChars
sInvalidChars="1234567890";
var iTotalChecked=0;
var funcOnDeletion='';
//variables added to allow spaces and plus sign validation for phone and faxes
var iAllowPlus  = 0;
var iAllowSpace = 0;

function objChecked(obj)
{
	if(obj.checked)
		iTotalChecked = iTotalChecked + 1
	else
		iTotalChecked = iTotalChecked - 1
}
//function to print current window
function printFunction(obj)
{	
	document.getElementById(obj).innerHTML="";
	window.print();
	window.close();
}
function fnRemoveSpaces(sFldval)
{
	var sTemp=sFldval;
  var sNewval=sTemp;
  //remove spaces from the front
  for(var i=0;i<sTemp.length;i++)
  {	
		if(sTemp.charAt(i)!=" ")
			break;
		else
			sNewval = sTemp.substring(i+1);
	}
	return sNewval;
}
//Purpose	: This function is used to remove spaces. //Arguments : text field object value
function fnFixSpace(sFldval)
{
	
	var sTemp=sFldval;
	  var sReversedString="";
	  var sTemp1;
	  
	  //remove spaces from the front
	  sNewval = fnRemoveSpaces(sTemp);
	  
	  // reverse n remove spaces from the front
	  for(var i=sNewval.length-1;i>=0;i--)
		sReversedString = sReversedString + sNewval.charAt(i);
	sTemp1 = fnRemoveSpaces(sReversedString);
	//reverse again
	sReversedString="";
	for(var i=sTemp1.length-1;i>=0;i--)
		sReversedString = sReversedString + sTemp1.charAt(i);
	sNewval = sReversedString;
	return sNewval;
}
function allValidChars(email) {
  var parsed = true;
  var validchars = "abcdefghijklmnopqrstuvwxyz0123456789@.-_";
  var notfirst = "0123456789.-_";
  for (var i=0; i < email.length; i++) 
  {
  var letter = email.charAt(i).toLowerCase();
  if(i == 0)
   {
   var valid_check = notfirst.indexOf(letter);
   if(valid_check == -1)
    {
		continue;     
    }
    else
    {
	   parsed = false;
       break;
    }
   }
    
    if (validchars.indexOf(letter) != -1)
   {
     continue;
   }
    parsed = false;
    break;
  }
  return parsed;
}
//Purpose	: This function is used to validate email. //Arguments : Email object
function ValidateEMail(objName)
{
	var iobjLength;
	email=objName;
	iobjLength=email.length;

	var email = objName;
	if (iobjLength!=0)
	{
		if (! allValidChars(email)) 
		{  // check to make sure all characters are valid
			return false;
		}
		if (email.indexOf("@") < 1) 
		{ //  must contain @, and it must not be the first character
			return false;
		} 
		else if (email.lastIndexOf(".") <= email.indexOf("@")) 
		{  // last dot must be after the @
			return false;
		} 
		else if (email.indexOf("@") == email.length) 
		{  // @ must not be the last character
			return false;
		} 
		else if (email.indexOf("..") >=0) 
		{ // two periods in a row is not valid
			 return false;
		} 
		else if (email.indexOf(".") == email.length) 
		{  // . must not be the last character
			 return false;
		}
		else
		{
			return true;
		}	
	}
	else
	{
		return false;
	}
}		



function validate_Frm()
{
	flag = true;
	for(i=1;i<16;i++)
	{
	
			elm_object = document.shareFrm.elements[i].name;	
			elm_name= elm_object.substring(0,5);			
			if(elm_name=="email")
			{
				
				elm_object1 = document.shareFrm.elements[i].value;
				if(elm_object1!="")
				{
					if(ValidateEMail(elm_object1))
					{;}		
					else
					{
						alert("invalid email");
						document.getElementById(elm_object).focus();
						flag = false;
						break;
					}
				}
			
		}
	}

	if(flag==true)
		document.shareFrm.submit();
	else
		return false;
}



function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function FormatDate(d)
{
		var dd,mm;
		var l;
		l=d.indexOf("/");
		dd=d.substring(0,l);
		d=d.substring(l+1);
		l=d.indexOf("/");
		mm=d.substring(0,l);
		yy=d.substring(l+1);
		
		if (parseInt(dd) < 10)
			dd="0" + dd;
		if (parseInt(mm) < 10)
			mm="0" + mm;
		d= dd + "/" + mm + "/" + yy
		return d;
}

function ValidateImg(objImg, isRequired)
{
	if(isRequired ==1 && objImg.value=='')
	{
		alert("Please enter image.");
		objImg.focus();
		return false;
	}
	if(objImg.value.length!=0)
	{
		if(objImg.value.length<5)
		{
			alert("Please enter valid image.");
			objImg.focus();
			objImg.select();
			return false;
		}
		var iPos = objImg.value.lastIndexOf(".")
		var sExt = objImg.value.substring(iPos);
		if((sExt.toUpperCase()=='.JPEG') || (sExt.toUpperCase()=='.JPG') || (sExt.toUpperCase()=='.GIF') || (sExt.toUpperCase()=='.BMP') )
		{
			return true;
		}
		else
		{
			alert("Please enter valid image.");
			objImg.focus();
			objImg.select();
			return false;
		}
	}
	return true;
}
function isURL(argvalue,urlname)
{
    if (argvalue.indexOf(" ") != -1)
	{
		alert("Spaces not allowed in "+ urlname +"!");
	    return false;
	}
	else if (argvalue.indexOf("http://") == -1)
    {
		alert(urlname +" must begin with a http://");
	    return false;
	}
	else if (argvalue == "http://")
    {
		alert("Please enter complete "+ urlname +"!");
	    return false;
	}
	else if (argvalue.indexOf("http://") == -1 )
    {
		alert("http:// must come in the beginning of a "+ urlname);
	    return false;
	}

	argvalue = argvalue.substring(7, argvalue.length);
	if (argvalue.indexOf(".") == -1)
	{
		alert("Please enter an extension like .com, .edu(etc) for "+ urlname +"!");
	    return false;
	}
	else if (argvalue.indexOf(".") == 0)
	{
		alert("Please enter correct "+ urlname +"!");
	    return false;
	}
	else if (argvalue.charAt(argvalue.length - 1) == ".")
    {
		alert("Please enter an extension after . like com, edu(etc) for "+ urlname +"!");
	    return false;
	}

	if (argvalue.indexOf("/") != -1) 
	{
		argvalue = argvalue.substring(0, argvalue.indexOf("/"));
		if (argvalue.charAt(argvalue.length - 1) == ".")
		{
			alert("Please enter correct "+ urlname +"!");
			return false;
		}
	}

	if (argvalue.indexOf(":") != -1) 
	{
		if (argvalue.indexOf(":") == (argvalue.length - 1))
		{
			alert("Please enter correct "+ urlname +"!");
		    return false;
		}
	    else if (argvalue.charAt(argvalue.indexOf(":") + 1) == ".")
		{
			alert("Please enter correct "+ urlname +"!");
			return false;
		}
		argvalue = argvalue.substring(0, argvalue.indexOf(":"));
		if (argvalue.charAt(argvalue.length - 1) == ".")
		{
			alert("Please enter correct "+ urlname +"!");
			return false;
		}
	}
	return true;
}
function imageExist(obj)
{
	var iPos = obj.value.lastIndexOf(".")
	var sExt = obj.value.substring(iPos);
	if((sExt.toUpperCase()=='.JPEG') || (sExt.toUpperCase()=='.JPG') || (sExt.toUpperCase()=='.GIF') || (sExt.toUpperCase()=='.BMP') )
	{
		return true;
	}
	else
	{
		alert("Please enter valid image.");
		obj.focus();
		obj.select();
		return false;
	}
	return true;
}
function checkImageSize(obj)
{
	var vWidth=100;
	var vHeight=80;

	img = new Image();
	img.src = obj.value;
	var imWidth = img.width;
	var imHeight = img.height;
	if (imWidth == 0 || imHeight == 0) 
	{
		//return validate(document.frmBan);
		return false;
	}
	if((imWidth!=vWidth) || (imHeight!=vHeight))
	{
			alert("Please check the size of image with that you have selected.\n It should be "+vWidth+"x"+vHeight+" and your image size is "+imWidth+"x"+imHeight);
			return false;		
	}
	else
	{
		return true;
	}
	return false;
}
//Description: This Function checks that the character entered is only character
function onlychar()
{
	if((event.keyCode >= 65 && event.keyCode <= 90) || (event.keyCode >= 97 && event.keyCode <= 122) || event.keyCode == 32 )
	{
	}
	else
	{
		event.returnValue = false;
	}
}
//-------**********trim function **************--------------------
function LTrim(str)
{
	for (var i=0; str.charAt(i)==" "; i++);
	return str.substring(i,str.length);
 }
function RTrim(str)
 {
	for (var i=str.length-1; str.charAt(i)==" "; i--);
	return str.substring(0,i+1);
 }
 function Trim(str)
 {
	return LTrim(RTrim(str));
 }

 function replaceSubstring(inputString, fromString, toString) {
   // Goes through the inputString and replaces every occurrence of fromString with toString
   var temp = inputString;
   if (fromString == "") {
      return inputString;
   }
   if (toString.indexOf(fromString) == -1) { // If the string being replaced is not a part of the replacement string (normal situation)
      while (temp.indexOf(fromString) != -1) {
         var toTheLeft = temp.substring(0, temp.indexOf(fromString));
         var toTheRight = temp.substring(temp.indexOf(fromString)+fromString.length, temp.length);
         temp = toTheLeft + toString + toTheRight;
      }
   } else { // String being replaced is part of replacement string (like "+" being replaced with "++") - prevent an infinite loop
      var midStrings = new Array("~", "`", "_", "^", "#");
      var midStringLen = 1;
      var midString = "";
      // Find a string that doesn't exist in the inputString to be used
      // as an "inbetween" string
      while (midString == "") {
         for (var i=0; i < midStrings.length; i++) {
            var tempMidString = "";
            for (var j=0; j < midStringLen; j++) { tempMidString += midStrings[i]; }
            if (fromString.indexOf(tempMidString) == -1) {
               midString = tempMidString;
               i = midStrings.length + 1;
            }
         }
      } // Keep on going until we build an "inbetween" string that doesn't exist
      // Now go through and do two replaces - first, replace the "fromString" with the "inbetween" string
      while (temp.indexOf(fromString) != -1) {
         var toTheLeft = temp.substring(0, temp.indexOf(fromString));
         var toTheRight = temp.substring(temp.indexOf(fromString)+fromString.length, temp.length);
         temp = toTheLeft + midString + toTheRight;
      }
      // Next, replace the "inbetween" string with the "toString"
      while (temp.indexOf(midString) != -1) {
         var toTheLeft = temp.substring(0, temp.indexOf(midString));
         var toTheRight = temp.substring(temp.indexOf(midString)+midString.length, temp.length);
         temp = toTheLeft + toString + toTheRight;
      }
   } // Ends the check to see if the string being replaced is part of the replacement string or not
   return temp; // Send the updated string back to the user
} // Ends the "replaceSubstring" function
//Converts the First letter of each word to upper case and rest of the letters to lower case
function changeCase(frmObj) 
{
	var index;
	var tmpStr;
	var tmpChar;
	var preString;
	var postString;
	var strlen;
	tmpStr = frmObj.value.toLowerCase();
	strLen = tmpStr.length;
	if (strLen > 0)  
	{
		for (index = 0; index < strLen; index++)  
		{
			if (index == 0)  
			{
				tmpChar = tmpStr.substring(0,1).toUpperCase();
				postString = tmpStr.substring(1,strLen);
				tmpStr = tmpChar + postString;
			}
			else 
			{
				tmpChar = tmpStr.substring(index, index+1);
				if (tmpChar == " " && index < (strLen-1))  
				{
					
					tmpChar = tmpStr.substring(index+1, index+2).toUpperCase();
					preString = tmpStr.substring(0, index+1);
					postString = tmpStr.substring(index+2,strLen);
					tmpStr = preString + tmpChar + postString;
		        }
			}
		}
	}
	frmObj.value = tmpStr;
}
//Checks the text in text area has exceeded the Maximum length allowed for the field
function checkLength(control,maxLength)
{
	if(control.type=='textarea')
	{
		var str = control.value;
		var len = str.replace(/\r\n/g,'').length;
		var sChangedName = control.name.substring(3);
		sChangedName = getFormattedmsg(sChangedName);
		if(len>maxLength)
		{
			alert("Please enter less than "+maxLength+" characters for "+sChangedName);
			control.focus();
			return false;
		}
		else
			return true;
	}
	else
		return false;
}
function checkdatetime(start,end, cd, sf, ef)
{

	if(start.value == "")
	{
		alert("Please Select Start Date");
		start.focus();
		return false;
	}
	if(end.value == "")
	{
		alert("Please Select End Date");
		end.focus();
		return false;
	}
	if((start.value != "") && (end.value != ""))
	{
		var star = start.value;
		var start_arr = star.split(" ");
		var startdate = start_arr[0];
		var starttime = start_arr[1];
		var start_date_split = startdate.split("-");
		var start_time_split = starttime.split(":");
		var start_year = start_date_split[0];
		var start_month = start_date_split[1] - 1;
		var start_date = start_date_split[2];
		var start_hour = start_time_split[0];
		var start_min = start_time_split[1];
		var start_sec = start_time_split[2];

		var en = end.value;
		var end_arr = en.split(" ");
		var enddate = end_arr[0];
		var endtime = end_arr[1];
		var end_date_split = enddate.split("-");
		var end_time_split = endtime.split(":");
		var end_year = end_date_split[0];
		var end_month = end_date_split[1] - 1;
		var end_date = end_date_split[2];
		var end_hour = end_time_split[0];
		var end_min = end_time_split[1];
		var end_sec = end_time_split[2];

		var currt = cd;
		var currt_arr = currt.split(" ");
		var currtdate = currt_arr[0];
		var currttime = currt_arr[1];
		var currt_date_split = currtdate.split("-");
		var currt_time_split = currttime.split(":");
		var currt_year = currt_date_split[0];
		var currt_month = currt_date_split[1] - 1;
		var currt_date = currt_date_split[2];
		var currt_hour = currt_time_split[0];
		var currt_min = currt_time_split[1];
		var currt_sec = currt_time_split[2];

		var s=new Date(start_year,start_month,start_date,start_hour,start_min,start_sec);
		var e=new Date(end_year,end_month,end_date,end_hour,end_min,end_sec);
		var c=new Date(currt_year,currt_month,currt_date,currt_hour,currt_min,currt_sec);
		if(sf == 0){
			if(c > s)
			{
				alert("Start Date & time should be greater than current date & time");
				start.focus();
				return false;
			}
		}
		if(ef == 0){
			if(c > e)
			{
				alert("End Date & time should be greater than current date & time ");
				end.focus();
				return false;
			}
		}
		if(s >= e)
		{
			alert("Start Date & time should be less than end date & time");
			start.focus();
			return false;
		}
		return true;
	}
	return true;
} // eof function
function notyping(obj)
{
	if(obj.value.length>0)
	{
		alert("You are not allowed to type.Please choose file by clicking Browse.");
		obj.value = "";
		obj.focus();
		obj.select();
		return false;
	}
	
}
//function check invalid character  //@param: string value //@return: boolean value if invalid character find return true else return false 
function checkInvalidChar(fldVal)
{
	var ln = fldVal.length;
	for(i=0;i<ln;i++)
	{
		if(fldVal.charAt(i)==' ' || fldVal.charAt(i)=='@' || fldVal.charAt(i)=='#' || fldVal.charAt(i)==';' || fldVal.charAt(i)==':' || fldVal.charAt(i)=='$' || fldVal.charAt(i)=='%' || fldVal.charAt(i)=='^' || fldVal.charAt(i)=='"' || fldVal.charAt(i)=="'" || fldVal.charAt(i)=='(' || fldVal.charAt(i)==')' || fldVal.charAt(i)=='=' || fldVal.charAt(i)=='+' || fldVal.charAt(i)=='|' || fldVal.charAt(i)=='\\' || fldVal.charAt(i)=='/' || fldVal.charAt(i)=='{' || fldVal.charAt(i)=='}' || fldVal.charAt(i)=='[' || fldVal.charAt(i)==']' || fldVal.charAt(i)=='?' || fldVal.charAt(i)=='<' || fldVal.charAt(i)=='>' || fldVal.charAt(i)==',' || fldVal.charAt(i)=='!' || fldVal.charAt(i)=='~' || fldVal.charAt(i)=='`' || fldVal.charAt(i)=='*' || fldVal.charAt(i)=='&')
		{
			return true;
			break;
		}
		
	}
	return false;
}
// Created on 14-Dec-2007  // following code is used to show paging on any page by ajax 
var whichpage=1;
function processingdiv(cond)
{
 document.getElementById("processingdiv").style.visibility=(cond?'visible':'hidden');
}

function AjaxPaging(divName,url,page,parameters)
{
	if(page>0)
	{
		whichpage=page; //processingdiv(true)            //processingdiv(false)
	}
	callAjax( divName, url, {
	params:"page="+whichpage+"&"+parameters+"&rand="+Math.random(),
	meth:"post",
	async:true,
    startfunc:"",
    endfunc:"",
    errorfunc:"ajaxError()" }
	);
	return false;
}
function AjaxPaging_PerPage(newPaging,divName,url,parameters)
{
	whichpage=1;
	parameters+="&paging="+newPaging;//+"&rand="+Math.random();
	AjaxPaging(divName,url,whichpage,parameters);
}
function AjaxPaging_SortBy(sortBy,divName,url,parameters)
{
	parameters+="&sortBy="+sortBy;
	//alert(parameters);
	AjaxPaging(divName,url,whichpage,parameters);
	//return false;
}
function SubmitFormByEnter(e,myform,funcName) 
{
	var obj=window.event? event : e;
	var key=obj.charCode? obj.charCode : obj.keyCode;
  if (key==13)
  {
	if(funcName!="")
	{	
	 eval(funcName);
	 return false;
	}
	else
	{
	 myform.submit();
	 return false;
	}
  }  
  else  
    return true;
}
//function to showing popup
function show_popup(url,isAlert)
{
	if(isAlert)
		jQuery.facebox(url);
	else
		jQuery.facebox({ ajax:url}); 
}
function hide_popup()
{
	jQuery.facebox.close();
}

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
	  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
		document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
	  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
	}
	MM_reloadPage(true);

	function MM_findObj(n, d) { //v4.01
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	  if(!x && d.getElementById) x=d.getElementById(n); return x;
	}

	function MM_showHideLayers() { //v6.0
	  var i,p,v,d,obj,args=MM_showHideLayers.arguments;
	  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
		//if (obj.style) { obj=obj.style; d=(d=='show')?'':(d=='hide')?'none':d; }
		if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
		//obj.display=d;
		obj.visibility=v;
		}
	}
//function to show remove button
function show_remove_button(Counter,minCounter,removeDiv)
{
	//alert(Counter+'>'+minCounter);
	if(Counter>minCounter)
	document.getElementById(removeDiv).style.display="";
}
//function to remove self div
function remove_div_self(eleId,counterName,addDiv,removeDiv)
{
	
	var Counter=document.getElementById(counterName).value;
	//alert(eleId+"_"+Counter);
	var ele=document.getElementById( eleId+"_"+Counter );
	ele.parentNode.removeChild( ele );
	Counter--;
	document.getElementById(counterName).value=Counter;
	//show add more button
	var maxCounter=document.getElementById("max_"+counterName).value;
	var minCounter=document.getElementById("min_"+counterName).value;
	//alert(Counter+'<'+maxCounter+'='+minCounter);
	if(Counter<maxCounter){
		//alert(Counter+'<'+maxCounter+'='+addDiv);
		document.getElementById(addDiv).style.display='';
	}
	//hide remove button
	if (minCounter==Counter)
		document.getElementById(removeDiv).style.display='none';
}//function-------------------------------------------------------------------------------------
//show static divs
function show_static_div(divName,url,parameters,retFunc)
{
	if(retFunc)
		var retFunction=retFunc;
	else
		var retFunction="";

	callAjax( divName, url, {
	params:"page=0"+"&"+parameters+"&rand="+Math.random(),
	meth:"post",
	async:true,
    startfunc:""+retFunction+"",
    endfunc:"",
    errorfunc:"" }
	);
}
//show static divs
function show_hide_other(mainDiv,otherDiv,act)
{
	if(act=="show")
	{
		document.getElementById("div_"+otherDiv).style.display="";
		//document.getElementById(mainDiv).removeAttribute("required");
		document.getElementById(mainDiv).disabled=true;
		document.getElementById(otherDiv).disabled=false;
		//document.getElementById(otherDiv).setAttribute("required","Empty");
		document.getElementById(otherDiv).value="";
		document.getElementById("txt_"+mainDiv).className="";
		//SHOW LAYER
			toggleBox('layer_'+otherDiv,1);
	}
	else if(act=="hide")
	{
		
		//document.getElementById(mainDiv).setAttribute("required","Motor");
		//document.getElementById(otherDiv).removeAttribute("required");
		document.getElementById(mainDiv).disabled=false;
		document.getElementById(otherDiv).disabled=true;
		document.getElementById(otherDiv).value="";
		//alert(document.getElementById(otherDiv).getAttribute('required'));
		document.getElementById("txt_"+otherDiv).className="";
		document.getElementById("div_"+otherDiv).style.display="none";
		//hide layer
			toggleBox('layer_'+otherDiv,0);
	}
}
function toggleBox(szDivID, iState) // 1 visible, 0 hidden
{
   if(document.getElementById(szDivID))
	{
		if(document.layers)	   //NN4+
		{
		   document.layers[szDivID].style.display = iState ? "block" : "none";
		}
		else if(document.getElementById)	  //gecko(NN6) + IE 5+
		{
			var obj = document.getElementById(szDivID);
			obj.style.display = iState ? "block" : "none";
		}
		else if(document.all)	// IE 4
		{
			document.all[szDivID].style.display= iState ? "block" : "none";
		}
	}
}
//functiion to clear form fields
function reset_form(formIdent)
{
	var elems=eval("document."+formIdent+".elements");
	for(var i=0;i<elems.length;i++)
	{
		if(elems[i].type=='text')
		{
			 //reset color field of color picker
			 if(elems[i].getAttribute('bgcolor'))
			{
//				alert(document.getElementById(elems[i].name).style.backgroundColor);
				document.getElementById(elems[i].name).style.backgroundColor='';
			}
			//end of reset color field of color picker

			elems[i].value ='';
		}//end if
		else if(elems[i].type=='hidden')
		{
			if(elems[i].name!="parent_cat_key")
			{
				//alert(elems[i].name);
				elems[i].value ='';
			}
		}//end if
		else if(elems[i].type=='select-one')
		{
			elems[i].options.selectedIndex=0;
		}//end if
		else if(elems[i].type=='radio')
		{
			var radioVal = '';
			
			var ele = elems[i].name;
			
		  
		   var len = eval("document."+formIdent+"."+ele+".length");
		   for (var j=0; j <len; j++)
		   {
				 frm_ele=eval("document."+formIdent+"."+ele+"["+j+"]");
				 if(frm_ele.name!="search_at")
			     {
					frm_ele.checked=false;
				 }
		   }//for
			
		}//else if
		else if(elems[i].type=='checkbox')
		{	
		   var ele = elems[i].name; 
		   var arr=ele.substring(ele.length-2,ele.length);
		   var chks = document.getElementsByName(ele);
			
			if(arr=="[]")
			{
				ele=ele.substring(0,ele.length-2);
				var  checkboxVal = "";
				for (var j=0; j <chks.length; j++)
				{
					frm_ele = eval("document."+formIdent+"."+ele);
					chks[j].checked=false;

				}
			}
			else
				chks.checked=false;

		 //  var frm_ele=eval("document."+formIdent+"."+ele);
		   //frm_ele.checked=false;
		   
		}//else if
	}//for
	if(document.getElementById('view_all'))
		document.getElementById('view_all').innerHTML = ""

	var pEle=eval("document."+formIdent+".paging"); 
	if(pEle && defaultAdminPaging)
	{
		pEle.options[0].value = defaultAdminPaging;
	//	alert(pEle.value+'---'+defaultAdminPaging);

	}
	
}//function
function show_error_msg(msg)
{
	var  error_container, conf_container, inner_err_container;
	if(document.getElementById('err_container'))
		error_container = eval("document.getElementById('err_container')");
	if(document.getElementById('inner_err_container'))
		inner_err_container = eval("document.getElementById('inner_err_container')")
	if(document.getElementById('conf_container'))
	{
		conf_container = eval("document.getElementById('conf_container')");
		conf_container.style.display='none';
	}
	//inner_err_container.innerHTML = 'Please choose at least one field for <span class="error_red_txt">search</span>.';
	inner_err_container.innerHTML = msg;
	error_container.style.display='';
	window.scrollTo(0,0);
}

function hide_error_msg()
{
	var  error_container;
	error_container = eval("document.getElementById('err_container')");
	if(error_container);
	error_container.style.display='none';
}

function show_conf_msg(msg)
{
	var  error_container, conf_container, inner_err_container,inner_conf_container;
	if(document.getElementById('err_container'))
		error_container = eval("document.getElementById('err_container')");
	if(document.getElementById('inner_conf_container'))
		inner_conf_container = eval("document.getElementById('inner_conf_container')")
	if(document.getElementById('conf_container'))
		conf_container = eval("document.getElementById('conf_container')");
	error_container.style.display='none';
	//inner_err_container.innerHTML = 'Please choose at least one field for <span class="error_red_txt">search</span>.';
	inner_conf_container.innerHTML = msg;
	conf_container.style.display='';
	window.scrollTo(0,0);
}

function hide_conf_msg()
{
	var  conf_container;
	conf_container = eval("document.getElementById('conf_container')");
	if(conf_container)
	conf_container.style.display='none';
}

function hide_msg()
{
	var  error_container;
	error_container = eval("document.getElementById('err_container')");
	error_container.style.display='none';
	
	var  conf_container;
	conf_container = eval("document.getElementById('conf_container')");
	error_container.style.display='none';
}
//function to cancel form
function cancel(url,frm_name)
{
	if(!frm_name)
		window.location=url;
	else
	{
		var frm=eval("document."+frm_name);
		frm.action=url;
		frm.submit();
	}

}
function OpenNewWindow(url,name,parameters)
{
		if(url!="")
		{
			window.open(url,name,parameters);
		}
		return false;
}
//********************************************************************************************************
function showLoading(divid)
{	
	document.getElementById(divid).innerHTML='<div align="center" id="loading" style="height:100px;" ><img border="0" src="images_site/loading.gif" align="middle" /></div>';
	
}
function showLoadingAdmin(divid)
{
	document.getElementById(divid).innerHTML='<div align="center" id="loading" style="height:100px;" ><img border="0" src="../images_site/loading.gif" align="middle" /></div>';
}
function checkCardDetail(frm,msg)
{
		var class_name_val = "msgcontainer1";
		var newMsg="";
		var cardNumber=frm.card_number1.value+frm.card_number2.value+frm.card_number3.value+frm.card_number4.value;
		if(frm.name_on_card.value=="")
		{
			document.getElementById('txt_name_on_card').className = class_name_val;
			if(!newMsg)
				newMsg="Please enter name on card.";
		}
		else
			document.getElementById('txt_name_on_card').className = "";

		if(isNaN(cardNumber) || cardNumber.length<15)
		{
			if(!newMsg)
				newMsg="Please enter valid credit card number.";
			document.getElementById('txt_card_number').className = class_name_val;
		}
		else
		{
			if(frm.card_type.value!="" && frm.card_type.value!="Amex" && cardNumber.length<16)
			{
				if(!newMsg)
					newMsg="Please enter valid credit card number.";
				document.getElementById('txt_card_number').className = class_name_val;
			}
			else
				document.getElementById('txt_card_number').className = "";
		}

		 if(frm.card_type.value=="")
		{
			if(!newMsg)
				newMsg="Please select card type.";
			document.getElementById('txt_card_type').className = class_name_val;
		}
		else
			document.getElementById('txt_card_type').className = "";

		 if(frm.expiry_month.value=="" || frm.expiry_year.value=="")
		{
			if(!newMsg)
				newMsg="Please select expiry month and year.";
			document.getElementById('txt_card_expiry').className = class_name_val;
		}
		else
			document.getElementById('txt_card_expiry').className = "";


		if(frm.expiry_month.value!="" && frm.expiry_year.value!="")
		{
			var d=new Date();
			var curMonth =d.getMonth()+1;
			var curYear= d.getFullYear();
			cardMonth=frm.expiry_month.value;
			cardYear=frm.expiry_year.value;
			//alert(cardYear+'=='+curYear +'&&'+ cardMonth+'<'+curMonth);
			if(cardYear<curYear || (cardYear==curYear && cardMonth<curMonth))
			{
				if(!newMsg)
				newMsg="Please select valid expiry month and year.";
				document.getElementById('txt_card_expiry').className = class_name_val;
			}
			else
				document.getElementById('txt_card_expiry').className = "";
		}


		if(newMsg)
		{
			if(msg)
				show_error_msg(msg);
			else
				show_error_msg(newMsg);

			return false;
		}

		return true;
}
var f_iDelTotalChecked=0//intialize value of (total delete checked) variable
//function manage (total delete checked) variable
function objDelChecked(chk)
{
	if(chk.checked==true)
		f_iDelTotalChecked=f_iDelTotalChecked+1
	else
		f_iDelTotalChecked=f_iDelTotalChecked-1
}//end of function

//-------------------Function for checking if any one checkbox is selected for deleting
function ConfirmDelChoice(chkSource,objFrm,pg,funcOnDeletion) 
{ 
	if(f_iDelTotalChecked==0)
	{
		alert("Please select at least one record to delete.");
		return false; 
	}
	else
	{
		f_answer = confirm("Are you sure you want to Delete the selected Records ?");

		if (f_answer !=0) 
		{ 
			 
			//passing comma seperated value(delete ids) and set action Delete
			//var selectObject = document.forms[objFrm].elements[chkSource];
			var selectObject = document.getElementsByName(chkSource);
			var selectCount  = selectObject.length;
			var dr_del="";
			
			if(typeof(selectCount)=='undefined')
				if(selectObject.checked)
				  dr_del = selectObject.value+",";				
					
			 if(selectCount){
			  for (var i = 0; i < selectCount; i++) {
			   if(selectObject[i].checked)
				  {
					   dr_del +=selectObject[i].value;
					   dr_del +=",";
				  }
			  } // end for
				
			 }//end if
			 dr_del=dr_del.substring(0,dr_del.length-1);
			 if(document.forms[objFrm].del_id)
				 document.forms[objFrm].del_id.value=dr_del;
			// alert(dr_del);
			if(document.forms[objFrm].cAction)
				 document.forms[objFrm].cAction.value="Delete";
			if(funcOnDeletion)
				eval(funcOnDeletion);
			else
				page_list(pg);
			 return true; 
		} 
		else
		{
			return false; 
		}
	}
}//end of function
//function to check all 
function CheckAll(chkSource,objFrm)
	{
		
		var selectObject = document.forms[objFrm].elements[chkSource];
		var selectCount  = selectObject.length; 
		 if(selectCount){
		  for (var i = 0; i < selectCount; i++) {
		   selectObject[i].checked = true;
		  } // end for
		  f_iDelTotalChecked=selectObject.length;
		 }
		 else{
		  selectObject.checked = true;
		  f_iDelTotalChecked=1;
		 }
		
	}//end of function
//functio to uncheck all
function unCheckAll(chkSource,objFrm)
	{
		 var selectObject = document.forms[objFrm].elements[chkSource];
			var selectCount  = selectObject.length; 
		 if(selectCount){
		  for (var i = 0; i < selectCount; i++) 
			  {
		   selectObject[i].checked = false;
		  } // end for
		 }
		 else{
		  selectObject.checked = false;
		 }
		 f_iDelTotalChecked=0;
	}
//function to sorting records------------------------------------------------------------------/*/
function sort_page(frm_name, sortorder,sortcriteria,pg)
	{
		var w=eval("document."+frm_name+".cAction")
		var x=eval("document."+frm_name+".sortcrt");
		var y=eval("document."+frm_name+".sortorder")
		var z=eval("document."+frm_name+".msg")
		
		if(w)
			w.value='None';
		if(x)
			x.value=sortcriteria;
		if(y)
			y.value=sortorder;
		if(z)
			z.value='';
		page_list(pg);
	}
//----------------------------------------------------------------------------------------------------*/
//function reset inner message on performing any action on change paging after editing record -------------------------/*/
function change_paging(pg,frm_name)
	{
		if(!frm_name)
			frm_name='frmSearch';
		
		var w=eval("document."+frm_name+".cAction")
		var z=eval("document."+frm_name+".msg")
		
		if(w)
			w.value='None';
		if(z)
			z.value='';
		page_list(pg);
	}
//Function for Activating Deactivating project
function changeStatus(frm_name,id,stats,pg)
{
	var rset=eval("document."+frm_name);
	//if(rset)
	//	rset.reset();
	var x=eval("document."+frm_name+".edit_key");
	var y=eval("document."+frm_name+".change_status");
	var z=eval("document."+frm_name+".cAction")
	if(x)
		x.value=id;
	if(y)
		y.value=stats;
	if(z)
		z.value='Status';
	page_list(pg);
	}
//function to open detail page in facebook style
function viewDetailPage(pageNameWithParameters)
{
	show_popup(pageNameWithParameters);
}
// validate textarea maxlength
function ismaxlength(obj){
	var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
	if (obj.getAttribute && obj.value.length>mlength)
	obj.value=obj.value.substring(0,mlength)
}

function editProd(frm_name,id,cur_action)
{

	eval("document."+frm_name+".msg.value=''");
	var x=eval("document."+frm_name+".edit_key");
	var y=eval("document."+frm_name)
	var z=eval("document."+frm_name+".cAction")
	if(x)
		x.value=id;
	if(z)
		z.value='Edit';
	if(y)
		y.action=cur_action;//'category-edit.php';
	y.submit();
}
function disp_sub_cat(frm_name,id,cur_action)
{

	eval("document."+frm_name+".msg.value=''");
	var x=eval("document."+frm_name+".parent_cat_key");
	var y=eval("document."+frm_name)
	var z=eval("document."+frm_name+".cAction")
	if(x)
		x.value=id;
	if(z)
		z.value='Edit';
	if(y)
		y.action=cur_action;//'category-edit.php';
	y.submit();
}


//Function for approval/disapproval
function approval_status(frm_name,id,stats,pg)
	{

		//alert(frm_name+","+id+","+stats+","+pg);
	
		var rset=eval("document."+frm_name);
		//if(rset)
		//	rset.reset();
		var x=eval("document."+frm_name+".edit_key");
		var y=eval("document."+frm_name+".approvalStatus")
		var z=eval("document."+frm_name+".cAction")
		if(x)
			x.value=id;
		if(y)
			y.value=stats;
		if(z)
			z.value='approval';
		page_list(pg);
	}
	
function tooLong(strTest,maxLength)
{
	if(strTest.value.length > maxLength)
	{
		alert("Maximum charecter limit is "+maxLength)
		strTest.value = strTest.value.substr(0,maxLength);
	}
}
function  validateNumeric( strValue ) {
/*****************************************************************
DESCRIPTION: Validates that a string contains only valid numbers.

PARAMETERS:
   strValue - String to be tested for validity

RETURNS:
   True if valid, otherwise false.
******************************************************************/

  var objRegExp  =  /(^-?[0-9]*\.[0-9]*$)|(^-?[0-9]*$)|(^-?\.[0-9]*$)/;

  //check for numeric characters
  return objRegExp.test(strValue);
}
function validateInteger( strValue ) {
/************************************************
DESCRIPTION: Validates that a string contains only
    valid integer number.

PARAMETERS:
   strValue - String to be tested for validity

RETURNS:
   True if valid, otherwise false.
**************************************************/
  var objRegExp  = /(^-?\d\d*$)/;

  //check for integer characters
  return objRegExp.test(strValue);
}
function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}
function php_serialize(obj)
{
    var string = '';

    if (typeof(obj) == 'object') {
        if (obj instanceof Array) {
            string = 'a:';
            tmpstring = '';
            count = 0;
            for (var key in obj) {
                tmpstring += php_serialize(key);
                tmpstring += php_serialize(obj[key]);
                count++;
            }
            string += count + ':{';
            string += tmpstring;
            string += '}';
        } else if (obj instanceof Object) {
            classname = obj.toString();

            if (classname == '[object Object]') {
                classname = 'StdClass';
            }

            string = 'O:' + classname.length + ':"' + classname + '":';
            tmpstring = '';
            count = 0;
            for (var key in obj) {
                tmpstring += php_serialize(key);
                if (obj[key]) {
                    tmpstring += php_serialize(obj[key]);
                } else {
                    tmpstring += php_serialize('');
                }
                count++;
            }
            string += count + ':{' + tmpstring + '}';
        }
    } else {
        switch (typeof(obj)) {
            case 'number':
                if (obj - Math.floor(obj) != 0) {
                    string += 'd:' + obj + ';';
                } else {
                    string += 'i:' + obj + ';';
                }
                break;
            case 'string':
                string += 's:' + obj.length + ':"' + obj + '";';
                break;
            case 'boolean':
                if (obj) {
                    string += 'b:1;';
                } else {
                    string += 'b:0;';
                }
                break;
        }
    }

    return string;
}

function array2json(arr) {
    var parts = [];
    var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');

    for(var key in arr) {
    	var value = arr[key];
        if(typeof value == "object") { //Custom handling for arrays
            if(is_list) parts.push(array2json(value)); /* :RECURSION: */
            else parts[key] = array2json(value); /* :RECURSION: */
        } else {
            var str = "";
            if(!is_list) str = '"' + key + '":';

            //Custom handling for multiple data types
            if(typeof value == "number") str += value; //Numbers
            else if(value === false) str += 'false'; //The booleans
            else if(value === true) str += 'true';
            else str += '"' + value + '"'; //All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Functions?)

            parts.push(str);
        }
    }
    var json = parts.join(",");
    
    if(is_list) return '[' + json + ']';//Return numerical JSON
    return '{' + json + '}';//Return associative JSON
}

/*begin ajax  category */
			function Subcategory(catid,subcatid)
			{
				
			
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="subcat.php";
			url=url+"?catid="+catid+"&subcatid="+subcatid;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedCat;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedCat() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					//alert(xmlHttp.responseText);	
					document.getElementById("subcatmsg").innerHTML=xmlHttp.responseText;				
				}
			}
          /*end ajax  category function */
			function GetXmlHttpObject()
			{
			if (window.XMLHttpRequest)
			  {
			  // code for IE7+, Firefox, Chrome, Opera, Safari
			  return new XMLHttpRequest();
			  }
			if (window.ActiveXObject)
			  {
			  // code for IE6, IE5
			  return new ActiveXObject("Microsoft.XMLHTTP");
			  }
			return null;
			}

			function edit(action1,id,deletename)
	{
		//alert('++++++');
		
		if(deletename!='')
		{
			if(deletename=='DELETE')
			{
			var agree=confirm("Are you sure you wish to continue?");
			if(agree)
				{
				document.getElementById('action_new').value=deletename;
				document.getElementById('id').value=id;
				document.editform.action=action1;
				document.editform.submit();
				}			
			 }
			 else
			{
				 //alert(deletename);
				 document.getElementById('action_new').value=deletename;
				 //alert(document.getElementById('action_new').value);
				document.getElementById('id').value=id;
				document.editform.action=action1;
				document.editform.submit();
			}
			
		}
		else
		{
			//alert(action1);
		document.getElementById('id').value=id;
		document.editform.action=action1;
        document.editform.submit();
		}
     }

	 function paging(page){

document.getElementById('page').value=page;
document.addcourse.submit();

}

function getposoffset(overlay, offsettype)
{
var totaloffset = (offsettype == "left")? overlay.offsetLeft : overlay.offsetTop;
var parentEl = overlay.offsetParent;
while (parentEl!= null)
{
totaloffset = (offsettype == "left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
parentEl = parentEl.offsetParent;
}
return totaloffset;
}

// function for open div
function overlay(curobj,userid)
{
//alert(userid);
var subobjstr='showdiv';
var opt_position='left';

if (document.getElementById)
{
//document.getElementById('useid').value=userid;
var subobj = document.getElementById(subobjstr);
subobj.style.display = (subobj.style.display!= "block")? "block" : "none";
var xpos = getposoffset(curobj, "left");
var ypos = getposoffset(curobj, "top");
subobj.style.left = xpos + "px";
subobj.style.top = ypos + "px";

return false;
}
else return true;
}

function overlayclose()
{
document.getElementById('showdiv').style.display = "none";
}
/*Begin subcatgory Ajax function page */
function Subcate(catid)
			{
				document.getElementById('parentid').value=catid;
			
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="subcatevent.php";
			url=url+"?catid="+catid;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedCat1;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedCat1() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					//alert(xmlHttp.responseText);	
					document.getElementById("subcatevent").innerHTML=xmlHttp.responseText;				
				}
			}
/*End subcatgory Ajax function page */
/*Begin subcatgory Ajax function page */
function catsub(catid,childid)
			{
				//alert(catid);
			document.getElementById('parentid').value=catid;
			document.getElementById('subcatid').value=childid;
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="subcatevent2.php";
			url=url+"?catid="+catid+"&childid="+childid;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedCatInner;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedCatInner() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					//alert(xmlHttp.responseText);	
					document.getElementById("innersubcat").innerHTML=xmlHttp.responseText;				
				}
			}
/*End subcatgory Ajax function page */
/*Begin subcatgory Ajax function page */
function tagEvent(userid,eventid)
			{
				//alert(userid);
			
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="tagevent.php";
			url=url+"?userid="+userid+"&eventid="+eventid;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedtag;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedtag() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					//alert(xmlHttp.responseText);	
					document.getElementById("tag").innerHTML=xmlHttp.responseText;				
				}
			}
/*End subcatgory Ajax function page */

function CatPaging(pageno,catid,childid)
			{
				//alert(pageno);
			
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="subcatevent2.php";
			url=url+"?catid="+catid+"&childid="+childid+"&page="+pageno;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedpage;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedpage() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					//alert(xmlHttp.responseText);	
					document.getElementById("innersubcat").innerHTML=xmlHttp.responseText;				
				}
			}
/*End subcatgory Ajax function page */
/*Begin this function use for tab selection */
function selectedTab(tab_type,tabno,totalno)
{
	//alert(tabno);

	//alert(tab_type);
   
	for(var i=0;i < totalno;i++)
	{
		var tabname=tab_type+i;
		//alert(tabname);
	if(document.getElementById(tabname))
	{
		var tab1  = document.getElementById(tabname);
		
		if(tabno==i)
		{
			//alert(tabno);
			//alert(i);
			
			tab1.className = "sel";
		}
		else
		{
			tab1.className = "";
		}
	}
		
	}
  return false;
}
/*End this function use for tab selection */
/* Begin for Category Search */
function search()
{
	//alert('++++++');
	document.forms["categorysearch"].submit();
}
/*End for Category Search*/

function setvalue(RadioValue,RadioName,perno)
{
	//alert('+++++');
	
	//alert(displayName);
	var display='';
	if(RadioValue=='O')
	{
	display='Often';	
	}
	if(RadioValue=='S')
	{
		display='Sometimes';
	}
	if(RadioValue=='N')
	{
		display='Never';
	}
	if(perno=='1')
	{
	var displayName='disp'+perno+'_'+RadioName;
	
	
	var displayName1='disp2_'+RadioName;
	var displayName2='disp3_'+RadioName;
	document.getElementById(displayName).innerHTML =display;
	document.getElementById(displayName1).innerHTML ='';
	document.getElementById(displayName2).innerHTML ='';;
	}
	if(perno=='2')
	{
	var displayName='disp'+perno+'_'+RadioName;
	var displayName1='disp1_'+RadioName;
	var displayName2='disp3_'+RadioName;
	document.getElementById(displayName).innerHTML =display;
	document.getElementById(displayName1).innerHTML ='';
	document.getElementById(displayName2).innerHTML ='';
	}
	if(perno=='3')
	{
	var displayName='disp'+perno+'_'+RadioName;
	var displayName1='disp1_'+RadioName;
	var displayName2='disp2_'+RadioName;
	
	//alert(displayName);
	document.getElementById(displayName).innerHTML =display;
	document.getElementById(displayName1).innerHTML ='';
	document.getElementById(displayName2).innerHTML ='';
	}
	
	
}

function save(value)
{
	
	if(value=='s')
	{
		document.getElementById('save').value='s';
	}
	document.forms["signupform2"].submit();
}

/*Begin Publish Event*/
function publishEvent(eventid)
			{
				alert(eventid);
			
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="Publishevent.php";
			url=url+"?eventid="+eventid;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedevent;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedevent() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					alert(xmlHttp.responseText);	
					//document.getElementById("innersubcat").innerHTML=xmlHttp.responseText;				
				}
			}
/*End Publish Event*/
/*End subcatgory Ajax function page */

function eventwallpage(pageno,record)
			{
				//alert(pageno);
				//alert(record);
			
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="myeventpaging.php";
			url=url+"?page="+pageno+'&record='+record;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedpage;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedpage() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					//alert(xmlHttp.responseText);	
					document.getElementById("tab_1").innerHTML=xmlHttp.responseText;				
				}
			}



	function stateChangedpreview() 
	{
		if (xmlHttp.readyState==4)
		{  
			
			//alert(xmlHttp.responseText);	
			document.getElementById("preview").innerHTML=xmlHttp.responseText;				
		}
	}

	function IsNumericTime(strString)
		//  check for valid numeric strings	
		{
		var strValidChars = "0123456789:";
		var strChar;
		var blnResult = true;

		if (strString.length == 0) return false;

		//  test strString consists of valid characters listed above
		for (i = 0; i < strString.length && blnResult == true; i++)
		  {
		  strChar = strString.charAt(i);
		  if (strValidChars.indexOf(strChar) == -1)
			 {
			 blnResult = false;
			 }
		  }
		return blnResult;
	}

	

	function addevent(eventid)
			{
				//alert(pageno);
				//alert(record);
			
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			  {
			  alert ("Your browser does not support AJAX!");
			  return;
			  } 
			var url="addeventfav.php";
			url=url+"?eventid="+eventid;
			//alert(url);
			xmlHttp.onreadystatechange=stateChangedfav;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			} 
			
			function stateChangedfav() 
			{
				if (xmlHttp.readyState==4)
				{  
					
					//alert(xmlHttp.responseText);	
					document.getElementById("addfav").innerHTML='aaaa';				
				}
			}
			
	/* image validation*/		
function checkExtension(fname)
    {
   	//alert(fname.value);
        // for mac/linux, else assume windows
        
        if (navigator.appVersion.indexOf('Mac') != -1 || navigator.appVersion.indexOf('Linux') != -1)
            var fileSplit = '/';
        else
            var fileSplit = '\\';
            
        			var fileTypes = new Array('.jpg','.jpeg','.gif'); // valid filetypes
        		
        		   
			var typestr='';		        		
        		
        var fileName      = fname.value; //document.getElementById('app_form').value; // current value
        var extension     = fileName.substr(fileName.lastIndexOf('.'), fileName.length);
		  var filenameonly     = fileName.substr(fileName.lastIndexOf(fileSplit), fileName.length);
        var valid = 0;
     
        for(var i in fileTypes)
        {
        	typestr = typestr + ', '+fileTypes[i].toLowerCase();
          if(fileTypes[i].toLowerCase() == extension.toLowerCase())
            {
           
                valid = 1;
                break;
               
            }
       
        }
       
        if(!valid == 1)
			{
				fname.value='';	
				fname.focus();
				alert('Please upload following format file '+typestr);		
		  }
       
    }
	/*end image validation */


