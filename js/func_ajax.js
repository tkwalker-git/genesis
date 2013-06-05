function callAjax( elemid, url, options )
{
  var params = options.params || "";
  var meth = options.meth || "post";
  var async = options.mode || true;
  var append = options.append || false;
  var startfunc = options.startfunc || "";
  var endfunc = options.endfunc || "";
  var errorfunc = options.errorfunc || "";
  var xmlreturn = options.xmlreturn || false;
  var msgpopup = options.msgpopup || false;
  if( startfunc != "" )
    eval( startfunc );
  var url_with_param = url+( params != "" ? "?"+params : "" );
  loadXMLDoc();
	//----------------------------------------------------------------
	var xmlhttp
	function loadXMLDoc()
	{
		// code for Mozilla, etc.
			if (window.XMLHttpRequest)
			{
			  xmlhttp=new XMLHttpRequest()
			  xmlhttp.onreadystatechange=xmlhttpChange
			  if(meth=="post")
			  {
			 /* xmlhttp.open(meth,url_with_param,async);
			  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=iso-8859-1");
			  //xmlhttp.overrideMimeType('text/xml; charset=iso-8859-1'); //NO CHARACTER PROBLEM IN MOZILLA
			  xmlhttp.send(params);*/
			    xmlhttp.open(meth,url_with_param,async);
			  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			  xmlhttp.send(params);
			  }
			  else
			  {
			  xmlhttp.open(meth,url_with_param,async)
			  xmlhttp.send(null)
			  }
			}
			else if (window.ActiveXObject)
		    {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
					if (xmlhttp)
					{
						xmlhttp.onreadystatechange=xmlhttpChange
						if(meth=="post")
						{
						/* xmlhttp.open(meth,url_with_param,async);
						//xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=iso-8859-1");
						xmlhttp.overrideMimeType('text/xml; charset=iso-8859-1'); //NO CHARACTER PROBLEM IN MOZILLA
						 xmlhttp.send(params);*/
						  xmlhttp.open(meth,url_with_param,async);
						 xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
						 xmlhttp.send(params);
						}
						else
						{
						xmlhttp.open(meth,url_with_param,async)
						xmlhttp.send(null)
						}
						return false;
				  }
				  else
				  {
						alert( "Your browser cannot perform the requested action. "+
							 "Either your security settings are too high or your "+
							 "browser is outdated. Try the newest version of "+
							 "Internet Explorer or Mozilla Firefox." );
						return false;
				  }
		  }
	}

	function xmlhttpChange()
	{
	// if xmlhttp shows "loaded"
	if (xmlhttp.readyState==4)
	  {
		  if (xmlhttp.status==200)
		  {
			 //alert(xmlhttp.responseText);
			 if(xmlreturn==true)
			 {
				response=xmlhttp.responseXML;
				x=response.documentElement.childNodes;
				var len=x.length;
				for(i=0;i<len;i++)
				{	
					id=x.item(i).nodeName;
					len1=x.item(i).childNodes.length;
					type=x.item(i).childNodes.item(0).childNodes[0].nodeValue;
					val=x.item(i).childNodes.item(1).childNodes[0].nodeValue;
					if(type=="id")
					{
						if(document.getElementById(id)!=null && document.getElementById(id)!="undefined")
						document.getElementById(id).value=val;
					}
					else
					{
						if(type=="divid")
						{
						if(document.getElementById(id)!=null && document.getElementById(id)!="undefined")
							document.getElementById(id).value=x.item(i).childNodes.item(2).childNodes[0].nodeValue;
						if(document.getElementById("div_"+id)!=null && document.getElementById("div_"+id)!="undefined")
							{
								if(val!="$0.00")
									document.getElementById("div_"+id).parentNode.style.display='';
								else
									document.getElementById("div_"+id).parentNode.style.display='none';
								document.getElementById("div_"+id).innerHTML=val;

							}
						}
						else
						{
							if(document.getElementById("div_"+id)!=null && document.getElementById("div_"+id)!="undefined")
							{
								document.getElementById("div_"+id).innerHTML=val;
								if(val!="$0.00")
									document.getElementById("div_"+id).parentNode.style.display='';
								else
									document.getElementById("div_"+id).parentNode.style.display='none';
							}
						}
					}
				}
			 }
			 else
			 {
			 var objXML1 = xmlhttp.responseText;
			 if(elemid!='')
			 {
				 if(msgpopup)
				 {
					show_conf_msg(objXML1);
				 }
				 else if(append)
				 {
					var myElement = document.createElement('DIV');
					myElement.innerHTML += objXML1;
					document.getElementById(elemid).appendChild(myElement);
				 }
				else
				 {
					document.getElementById(elemid).innerHTML = "";
					document.getElementById(elemid).innerHTML = objXML1;
				}
			 }
			 }
			 if( endfunc != "" )
				eval( endfunc );
		  }
		  else
			{
				//alert("Problem retrieving XML data")
				if( endfunc != "" )
					eval( endfunc );
			  if( errorfunc != "" )
					eval( errorfunc );
				  return false;
			}
		}
	}
}
//END OF AJAX FUNCTIONS.

// Start Check Login Ajax Function
function CheckLoginAjax( elemid, url, options )
{
  var params = options.params || "";
  var meth = options.meth || "post";
  var async = options.mode || true;
  var startfunc = options.startfunc || "";
  var endfunc = options.endfunc || "";
  var errorfunc = options.errorfunc || "";
  if( startfunc != "" )
    eval( startfunc );

  var url_with_param = url+( params != "" ? "?"+params : "" );

	 //alert(url_with_param);

  loadXMLDoc();
	//----------------------------------------------------------------
	var xmlhttp
	function loadXMLDoc()
	{
		// code for Mozilla, etc.
			if (window.XMLHttpRequest)
			{
			  xmlhttp=new XMLHttpRequest()
			  xmlhttp.onreadystatechange=xmlhttpChange
			  if(meth=="post")
			  {
			  xmlhttp.open(meth,url_with_param,async);
			  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			  xmlhttp.send(params);
			  }
			  else
			  {
			  xmlhttp.open(meth,url_with_param,async)
			  xmlhttp.send(null)
			  }
			}
			else if (window.ActiveXObject)
		    {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
					if (xmlhttp)
					{
						xmlhttp.onreadystatechange=xmlhttpChange
						if(meth=="post")
						{
						 xmlhttp.open(meth,url_with_param,async);
						 xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
						 xmlhttp.send(params);
						}
						else
						{
						xmlhttp.open(meth,url_with_param,async)
						xmlhttp.send(null)
						}
						return false;
				  }
				  else
				  {
						alert( "Your browser cannot perform the requested action. "+
							 "Either your security settings are too high or your "+
							 "browser is outdated. Try the newest version of "+
							 "Internet Explorer or Mozilla Firefox." );
						return false;
				  }
		  }
	}

	function xmlhttpChange()
	{
	// if xmlhttp shows "loaded"
	if (xmlhttp.readyState==4)
	  {
		  if (xmlhttp.status==200)
		  {
			 var objXML = xmlhttp.responseXML;
			 var objXML1 = xmlhttp.responseText;
			 if(Trim(objXML1)==1)
			 {
					document.login_frm.action="login_submit.php";
					document.login_frm.submit();
					return false;
			 }
			 if(elemid!='')
			 {
				//alert(objXML1);
			 	document.getElementById(elemid).innerHTML = objXML1;
			 }
			 
			 if( endfunc != "" )
				eval( endfunc );
		  }
		  else
			{
				if( endfunc != "" )
					eval( endfunc );
			  if( errorfunc != "" )
					eval( errorfunc );
				  return false;
			}
		}
	}
}
// End Check Login Ajax Function
// return Ajax Value
function ReturnAjaxValueWithElement( element,url, options )
{
  var params = options.params || "";
  var meth = options.meth || "post";
  var async = options.mode || true;
  var startfunc = options.startfunc || "";
  var endfunc = options.endfunc || "";
  var errorfunc = options.errorfunc || "";
  if( startfunc != "" )
    eval( startfunc );

  var url_with_param = url+( params != "" ? "?"+params : "" );

	 //alert(url_with_param);

  loadXMLDoc();
	//----------------------------------------------------------------
	var xmlhttp
	function loadXMLDoc()
	{
		// code for Mozilla, etc.
			if (window.XMLHttpRequest)
			{
			  xmlhttp=new XMLHttpRequest()
			  xmlhttp.onreadystatechange=xmlhttpChange
			  if(meth=="post")
			  {
			  xmlhttp.open(meth,url_with_param,async);
			  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			  xmlhttp.send(params);
			  }
			  else
			  {
			  xmlhttp.open(meth,url_with_param,async)
			  xmlhttp.send(null)
			  }
			}
			else if (window.ActiveXObject)
		    {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
					if (xmlhttp)
					{
						xmlhttp.onreadystatechange=xmlhttpChange
						if(meth=="post")
						{
						 xmlhttp.open(meth,url_with_param,async);
						 xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
						 xmlhttp.send(params);
						}
						else
						{
						xmlhttp.open(meth,url_with_param,async)
						xmlhttp.send(null)
						}
						return false;
				  }
				  else
				  {
						alert( "Your browser cannot perform the requested action. "+
							 "Either your security settings are too high or your "+
							 "browser is outdated. Try the newest version of "+
							 "Internet Explorer or Mozilla Firefox." );
						return false;
				  }
		  }
	}

	function xmlhttpChange()
	{
	// if xmlhttp shows "loaded"
	if (xmlhttp.readyState==4)
	  {
		  if (xmlhttp.status==200)
		  {
			 var objXML = xmlhttp.responseXML;
			 var objXML1 = xmlhttp.responseText;
			
					if(element)
					{
						var elementId = document.getElementById(element);	
						elementId.value = objXML1;
						
					}
					
					
			 if( endfunc != "" )
				eval( endfunc );

			 if(objXML1!='success')
				return false;
			else
				return true;
		  }
		  else
			{
				alert("Problem retrieving XML data")
				if( endfunc != "" )
					eval( endfunc );
			  if( errorfunc != "" )
					eval( errorfunc );
				  return false;
			}
		}
	}
}
// return Ajax Value
function ReturnAjaxValue( element,url, options )
{
  var params = options.params || "";
  var meth = options.meth || "post";
  var async = options.mode || true;
  var startfunc = options.startfunc || "";
  var endfunc = options.endfunc || "";
  var errorfunc = options.errorfunc || "";
  if( startfunc != "" )
    eval( startfunc );

  var url_with_param = url+( params != "" ? "?"+params : "" );

	 //alert(url_with_param);

  loadXMLDoc();
	//----------------------------------------------------------------
	var xmlhttp
	function loadXMLDoc()
	{
		// code for Mozilla, etc.
			if (window.XMLHttpRequest)
			{
			  xmlhttp=new XMLHttpRequest()
			  xmlhttp.onreadystatechange=xmlhttpChange
			  if(meth=="post")
			  {
			  xmlhttp.open(meth,url_with_param,async);
			  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			  xmlhttp.send(params);
			  }
			  else
			  {
			  xmlhttp.open(meth,url_with_param,async)
			  xmlhttp.send(null)
			  }
			}
			else if (window.ActiveXObject)
		    {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
					if (xmlhttp)
					{
						xmlhttp.onreadystatechange=xmlhttpChange
						if(meth=="post")
						{
						 xmlhttp.open(meth,url_with_param,async);
						 xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
						 xmlhttp.send(params);
						}
						else
						{
						xmlhttp.open(meth,url_with_param,async)
						xmlhttp.send(null)
						}
						return false;
				  }
				  else
				  {
						alert( "Your browser cannot perform the requested action. "+
							 "Either your security settings are too high or your "+
							 "browser is outdated. Try the newest version of "+
							 "Internet Explorer or Mozilla Firefox." );
						return false;
				  }
		  }
	}

	function xmlhttpChange()
	{
	// if xmlhttp shows "loaded"
	if (xmlhttp.readyState==4)
	  {
		  if (xmlhttp.status==200)
		  {
			 var objXML = xmlhttp.responseXML;
			 var objXML1 = xmlhttp.responseText;
			 //alert(objXML1);
					if(element)
					{
						var elementId = document.getElementById(element);	
						elementId.value = objXML1;
					}
					
			 if( endfunc != "" )
				eval( endfunc );
		  }
		  else
			{
				alert("Problem retrieving XML data")
				if( endfunc != "" )
					eval( endfunc );
			  if( errorfunc != "" )
					eval( errorfunc );
				  return false;
			}
		}
	}
}
// End Check Login Ajax Function
// return Ajax Value
function CallAjaxWithElement( element,divName,url, options )
{
  var params = options.params || "";
  var meth = options.meth || "post";
  var async = options.mode || true;
  var startfunc = options.startfunc || "";
  var endfunc = options.endfunc || "";
  var errorfunc = options.errorfunc || "";
  if( startfunc != "" )
    eval( startfunc );

  var url_with_param = url+( params != "" ? "?"+params : "" );

	 //alert(url_with_param);

  loadXMLDoc();
	//----------------------------------------------------------------
	var xmlhttp
	function loadXMLDoc()
	{
		// code for Mozilla, etc.
			if (window.XMLHttpRequest)
			{
			  xmlhttp=new XMLHttpRequest()
			  xmlhttp.onreadystatechange=xmlhttpChange
			  if(meth=="post")
			  {
			  xmlhttp.open(meth,url_with_param,async);
			  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			  xmlhttp.send(params);
			  }
			  else
			  {
			  xmlhttp.open(meth,url_with_param,async)
			  xmlhttp.send(null)
			  }
			}
			else if (window.ActiveXObject)
		    {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
					if (xmlhttp)
					{
						xmlhttp.onreadystatechange=xmlhttpChange
						if(meth=="post")
						{
						 xmlhttp.open(meth,url_with_param,async);
						 xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
						 xmlhttp.send(params);
						}
						else
						{
						xmlhttp.open(meth,url_with_param,async)
						xmlhttp.send(null)
						}
						return false;
				  }
				  else
				  {
						alert( "Your browser cannot perform the requested action. "+
							 "Either your security settings are too high or your "+
							 "browser is outdated. Try the newest version of "+
							 "Internet Explorer or Mozilla Firefox." );
						return false;
				  }
		  }
	}

	function xmlhttpChange()
	{
	// if xmlhttp shows "loaded"
	if (xmlhttp.readyState==4)
	  {
		  if (xmlhttp.status==200)
		  {
			 var objXML = xmlhttp.responseXML;
			 var objXML1 = xmlhttp.responseText;
						if(Trim(objXML1)==1 || Trim(objXML1)==2 || Trim(objXML1)==3 || Trim(objXML1)==4 || Trim(objXML1)==5)
						{
							var elementId = document.getElementById(element);	
							elementId.value = objXML1;
							//alert(elementId+"="+objXML1);
						}
						else
						{
							 if(divName!='')
							 {
								//alert(objXML1);
								document.getElementById(divName).innerHTML = objXML1;
							 }
						}
						 if( endfunc != "" )
							eval( endfunc );
		  }
		  else
			{
				alert("Problem retrieving XML data")
				if( endfunc != "" )
					eval( endfunc );
			  if( errorfunc != "" )
					eval( errorfunc );
				  return false;
			}
		}
	}
}