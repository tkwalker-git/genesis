function get() 
	{
		
	var flag='';
	var ErrorMess = "<ul>";
	if(document.getElementById('fname').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the First Name.</li>";
	 flag='1';
	
	}
	
	if(document.getElementById('lname').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the Last Name.</li>";
	 flag='1';
	}
	if(document.getElementById("email").value=="")
	{
	
	ErrorMess = ErrorMess + "<li>Please enter the Email.</li>";
	flag='1';
	}
	if(document.getElementById("email").value!='')
	{
	var Email=document.getElementById("email").value;	   
		if((/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z.]{2,5}$/).exec(Email)==null)
		{
		ErrorMess = ErrorMess + "<li>Please enter the Valid Email.</li>";
		flag='1';	
		}
	}
	if(document.getElementById("username").value=="")
	{
	
	ErrorMess = ErrorMess + "<li>Please enter the Username.</li>";
	flag='1';
	}
	if(document.getElementById("password").value=="")
	{
	
	ErrorMess = ErrorMess + "<li>Please enter the Password.</li>";
	flag='1';
	}
	if(document.getElementById("password").value!="")
	{
		if(document.getElementById("password").value.length < 6)
	    {
			ErrorMess = ErrorMess + "<li>Password must contain at least six characters.</li>";
			flag='1';
	        //document.getElementById("password").focus();
			 //return false; 
	   } 
	   if(document.getElementById("password").value.length >10)
	    {
			ErrorMess = ErrorMess + "<li>Password must contain at least ten characters.</li>";
			flag='1';
	        //document.getElementById("password").focus();
			 //return false; 
	   } 
	
	
	}
	if(document.getElementById("cpassword").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the Confirm Password.</li>";
	flag='1';
	}
	
	if(document.getElementById("cpassword").value!=document.getElementById("password").value)
	{
	ErrorMess = ErrorMess + "<li>Please enter the correct Confirm Password.</li>";
	flag='1';
	document.getElementById("cpassword").value='';
	}
	if(document.getElementById("zipcode").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the ZipCode.</li>";
	flag='1';
	}
	if(document.getElementById("gender").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the Gender Name.</li>";
	flag='1';
	}
	if(document.getElementById("txtCaptcha").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the Captch Code.</li>";
	flag='1';
	}
	
	
	if(!document.signupform.agree.checked)
				{
				ErrorMess = ErrorMess + "<li>Please check the terms and conditions.</li>";
	            flag='1';
		        } 

	ErrorMess = ErrorMess + "</ul>";
	
	if(flag=='1')
	{
		//alert('kumar');
	document.getElementById("myspan").innerHTML = ErrorMess;
	return false;
	}
	
	}
	function removeSpaces(str) 
{
  str = this != window? this : str;
  return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}
	function getXmlHttpRequestObject() {
 if (window.XMLHttpRequest) {
    return new XMLHttpRequest(); //Mozilla, Safari ...
 } else if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP"); //IE
 } else {
    //Display our error message
    alert("Your browser doesn't support the XmlHttpRequest object.");
 }
}

//Our XmlHttpRequest object
var receiveReq = getXmlHttpRequestObject();

//Initiate the AJAX request
function makeRequest(url, param) {
//If our readystate is either not started or finished, initiate a new request
 if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
   //Set up the connection to captcha_test.html. True sets the request to asyncronous(default) 
   receiveReq.open("POST", url, true);
   //Set the function that will be called when the XmlHttpRequest objects state changes
   receiveReq.onreadystatechange = updatePage; 

   receiveReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   receiveReq.setRequestHeader("Content-length", param.length);
   receiveReq.setRequestHeader("Connection", "close");

   //Make the request
   receiveReq.send(param);
 }   
}
function IsNumericTime(strString)
   //  check for valid numeric strings	
   {
   var strValidChars = "0123456789:.";
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
//Called every time our XmlHttpRequest objects state changes
function updatePage() {
 //Check if our response is ready
 if (receiveReq.readyState == 4) {
    if(receiveReq.responseText=='2')
	 {
  document.getElementById('continue1').disabled = false;
  document.getElementById('result').style.display='none';

  //document.getElementById('result').innerHTML ='Please enter the Correct Code';
	 }
	 else
	 {
		 document.getElementById('continue1').disabled = true;
        document.getElementById('result').style.display='block';
        document.getElementById('result').innerHTML ='Please enter the Correct Code';
		 //Get a reference to CAPTCHA image
   img = document.getElementById('imgCaptcha'); 
   //Change the image
   img.src = 'create_image.php?' + Math.random();
	 }
  
 }
}

//Called every time when form is perfomed
function getParam(theForm) {
	//alert('hi');
	//alert(value);
 //Set the URL
 var url = 'captcha.php';
 //Set up the parameters of our AJAX call
 var postStr = theForm.txtCaptcha.name + "=" + encodeURIComponent( theForm.txtCaptcha.value );
 //alert(postStr);
 //Call the function that initiate the AJAX request
makeRequest(url, postStr);
}
/* Begin validation for singnup second step */
function getPromoter() 
	{
		//alert('+++++');
		var flag='';
	var ErrorMess = "<ul>";
	if(document.getElementById('business_name').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the Business Name.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('promoter_role').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the Promoter Role.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('category').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please Select The Category Name.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('breifbio').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the brief bio.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('website').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the web site Name.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('busines_phone').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the Business Phone.</li>";
	 flag='1';	 	
	}
	
	
	if(flag=='1')
	{
	document.getElementById("myspan").innerHTML = ErrorMess;
	return false;
	}
	
	}
	/* End validation for singnup second step */

	/* Begin validation for login page */
function getLogin() 
	{
		alert('+++++');
		var flag='';
	var ErrorMess = "<ul>";
	if(document.getElementById('username').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the User Name.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('password').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the Password.</li>";
	 flag='1';	 	
	}	
	if(flag=='1')
	{
	document.getElementById("myspan").innerHTML = ErrorMess;
	return false;
	}
	
	}
	/* End validation for login page */
	/* Begin validation for Add Event Page */
function getEventvalidation() 
	{
		

		
	 if(removeSpaces(document.getElementById('eventname').value)=="")
		{
			alert('Please enter the event name');
			document.getElementById('eventname').focus();
			return false;	
		}
		else if(removeSpaces(document.getElementById('categoryname').value)=="")
		{
			alert('Please enter the event type');
			document.getElementById('categoryname').focus();
			return false;
		}
		else if(removeSpaces(document.getElementById('musicgenere').value)=="")
		{
			alert('Please enter the Music Genere');
			document.getElementById('musicgenere').focus();
			return false;
		}
		else if(removeSpaces(document.getElementById('eventdes').value)=="")
		{
			alert('Please enter the event description');
			document.getElementById('eventdes').focus();
			return false;
		}else if(removeSpaces(document.getElementById('cost').value)=="")
		{
			alert('Please enter the Cost');
			document.getElementById('cost').focus();
			return false;
		}else if(IsNumericTime(document.getElementById('cost').value) == false) 
		{	 
			 alert('Please enter cost in valid format');
			 document.getElementById('cost').focus();
			 return false;
	
		}else if(removeSpaces(document.getElementById('filename1').value)=="")
		{
			alert('Please uplaod  the event image');
			document.getElementById('filename1').focus();
			return false;
		}else if(removeSpaces(document.getElementById('event_age_suitab').value)=="")
		{
			alert('Please enter the Age');
			document.getElementById('event_age_suitab').focus();
			return false;
		}
		else if(removeSpaces(document.getElementById('venuname').value)=="")
		{
			alert('Please enter the Venu name');
			document.getElementById('venuname').focus();
			return false;
		}
		else if(removeSpaces(document.getElementById('eventtime').value)=="")
		{
			alert('Please enter the event start time');
			document.getElementById('eventtime').focus();
			return false;
		}
		else if(IsNumericTime(document.getElementById('eventtime').value) == false) 
		{	 
			 alert('Please enter time in valid format');
			 document.getElementById('eventtime').focus();
			 return false;
	
		}
		else if(removeSpaces(document.getElementById('eventtime1').value)=="")
		{
			alert('Please enter the event end time');
			document.getElementById('eventtime1').focus();
			return false;
		}
		else if(IsNumericTime(document.getElementById('eventtime1').value) == false) 
		{	 
			 alert('Please enter time in valid format');
			 document.getElementById('eventtime1').focus();
			 return false;
	
		}
		
		document.addevent.action="eventsubmit.php";
		document.addevent.target="";		
		document.addevent.submit();

	
	}
	/* End validation for Add Event Pgae */
   /*Begin contactus page*/
	function getContctvalidation() 
	{
		//alert('++++');
		
		
	var flag='';
	var ErrorMess = "<ul>";
	if(document.getElementById('fname').value=="")
	{
		//alert('+++++');
	 ErrorMess = ErrorMess + "<li>Please Enter the First Name.</li>";
	 flag='1';	 	
	}
	
	if(document.getElementById('lname').value=="")
	{
		//alert('+++++');
	 ErrorMess = ErrorMess + "<li>Please Enter the Last Name.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('email').value=="")
	{
		//alert('+++++');
	 ErrorMess = ErrorMess + "<li>Please Enter the email.</li>";
	 flag='1';	 	
	}
	if(document.getElementById("email").value!='')
	{
	var Email=document.getElementById("email").value;	   
		if((/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z.]{2,5}$/).exec(Email)==null)
		{
		ErrorMess = ErrorMess + "<li>Please enter the Valid Email.</li>";
		flag='1';	
		}
	}
	if(document.getElementById('message').value=="")
	{
		//alert('+++++');
	 ErrorMess = ErrorMess + "<li>Please Enter the Message.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('website').value=="")
	{
		//alert('+++++');
	 ErrorMess = ErrorMess + "<li>Please Enter the website.</li>";
	 flag='1';	 	
	}
	if(document.getElementById("website").value!='')
	{
		var website=document.getElementById("website").value;	 
		var tomatch= /http:\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/   
		if(!tomatch.test(website))
		{
		ErrorMess = ErrorMess + "<li>Please enter the Valid url.</li>";
		flag='1';	
		}
	}	
	if(flag=='1')
	{
	document.getElementById("myspan").innerHTML = ErrorMess;
	return false;
	}
	
	}
	/* End contact us page */