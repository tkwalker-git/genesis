var xmlHttp1;
var gelem;
function ajaxRequest(uri,elem)
{ 
	gelem = elem;
	xmlHttp1=GetXmlHttpObject()
	if (xmlHttp1==null)
	 {
		 alert ("Browser does not support HTTP Request");
		 return;
	 } 
	var url=uri;
	//alert(url);
	xmlHttp1.onreadystatechange=stateChanged ;
	xmlHttp1.open("GET",url,true);
	xmlHttp1.send(null);
}
function stateChanged() 
{ 
	if (xmlHttp1.readyState==4 || xmlHttp1.readyState=="complete")
		document.getElementById(gelem).innerHTML = xmlHttp1.responseText;
}

function GetXmlHttpObject()
{
	var xmlHttp1=null;
	try
 	{
	 // Firefox, Opera 8.0+, Safari
	 	xmlHttp1=new XMLHttpRequest();
	 }
	catch (e)
	 {
	 //Internet Explorer
		try
		{
			xmlHttp1=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp1=new ActiveXObject("Microsoft.XMLHTTP");
		}
	 }
	return xmlHttp1;
}
