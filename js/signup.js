function get() 
	{
		
	var flag='';
	
	var ErrorMess = "<ul>";
	if(document.getElementById('fname').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the First Name.</li>";
	 flag='1';
	
	}
	if(document.getElementById('fname').value!="")
	{
	re = /^\w+$/;
	  if(!re.test(document.getElementById("fname").value))
	   {
		   ErrorMess = ErrorMess + "<li>First Name must contain only letters, numbers.</li>";
	   
		flag='1';
		 }
	}
	
	if(document.getElementById('lname').value=="")
	{
	 ErrorMess = ErrorMess + "<li>Please enter the Last Name.</li>";
	 flag='1';
	}
	if(document.getElementById('lname').value!="")
	{
	re = /^\w+$/;
	  if(!re.test(document.getElementById("lname").value))
	   {
		   ErrorMess = ErrorMess + "<li>Last Name must contain only letters, numbers.</li>";
	   
		flag='1';
		 }
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
	re = /^\w+$/;
	  if(!re.test(document.getElementById("username").value))
	   {
		   ErrorMess = ErrorMess + "<li>Username must contain only letters, numbers.</li>";
	   
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
	/*if(document.getElementById("cpassword").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the Confirm Password.</li>";
	flag='1';
	}
	if(document.getElementById("cpassword").value!="")
	{
	if(document.getElementById("cpassword").value!=document.getElementById("password").value)
	{
	ErrorMess = ErrorMess + "<li>Please enter the correct Confirm Password.</li>";
	flag='1';
	document.getElementById("cpassword").value='';
	}
	}
	if(document.getElementById("zipcode").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the ZipCode.</li>";
	flag='1';
	}
	if(isNaN(document.getElementById("zipcode").value)!="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the ZipCode Number only.</li>";
	flag='1';
	}
	if(document.getElementById("gender").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the Gender Name.</li>";
	flag='1';
	}
	
	var month=document.getElementById("month").value;
  var date=document.getElementById("date").value;
  var year=document.getElementById("year").value;
if(month=='' || date=='' || year=='')
{
ErrorMess = ErrorMess + "<li>Please enter the Date of birth.</li>";
flag='1';
}
 
if(month!='' && date!='' && year!='')
{

var dateStr=month+'/'+date+'/'+year;
var datePat = /^(\d{1,2})(\/|-)(\d{1,2})\2(\d{2}|\d{4})$/;

// To require a 4 digit year entry, use this line instead:
// var datePat = /^(\d{1,2})(\/|-)(\d{1,2})\2(\d{4})$/;

var matchArray = dateStr.match(datePat); // is the format ok?
if (matchArray == null) {
	ErrorMess = ErrorMess + "<li>Date is not in a valid format.</li>";
flag='1';

}
month = matchArray[1]; // parse date into variables
day = matchArray[3];
year = matchArray[4];
if (month < 1 || month > 12) { // check month range
ErrorMess = ErrorMess + "<li>Month must be between 1 and 12.</li>";
flag='1';
}
if (day < 1 || day > 31) {

ErrorMess = ErrorMess + "<li>Day must be between 1 and 31.</li>";
flag='1';
}
if ((month==4 || month==6 || month==9 || month==11) && day==31) {

ErrorMess = ErrorMess + "<li>Month  doesn't have 31 days!</li>";
flag='1';
}
if (month == 2) { // check for february 29th
var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
if (day>29 || (day==29 && !isleap)) {

ErrorMess = ErrorMess + "<li>Month  doesn't have" + day + " days!</li>";
flag='1';
   }
}
//return true;  // date is valid
}
	if(document.getElementById("txtCaptcha").value=="")
	{
	ErrorMess = ErrorMess + "<li>Please enter the Captch Code.</li>";
	flag='1';
	}
	
	*/
	
	if(document.signupform.agree.checked== false)
				{
				ErrorMess = ErrorMess + "<li>Please check the terms and conditions.</li>";
	            flag='1';
		        } 

	ErrorMess = ErrorMess + "</ul>";
	
	if(flag=='1')
	{
		//alert('kumar');
	document.getElementById('errmsg').style.display='block';
	document.getElementById("myspan").innerHTML = ErrorMess;
	return false;
	}
	
	}
	
/*	
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
		 
		 //get();
      //document.getElementById('continue1').disabled = false;
     document.getElementById('result').style.display='none';
	 document.getElementById('errmsg').style.display='none';

  //document.getElementById('result').innerHTML ='Please enter the Correct Code';
	 }
	 else
	 {
		 document.getElementById("txtCaptcha").value='';
		  get();
		 //document.getElementById('continue1').disabled = true;
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
*/
/* Begin validation for singnup second step */
function getPromoter() 
	{
		//alert('+++++');
		/*phone number validation */
var digits = "0123456789";
// non-digit characters which are allowed in phone numbers
var phoneNumberDelimiters = "()- ";
// characters which are allowed in international phone numbers
// (a leading + is OK)
var validWorldPhoneChars = phoneNumberDelimiters + "+";
// Minimum no of digits in an international phone no.
var minDigitsInIPhoneNumber = 10;

function isInteger(s)
{   var i;
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}
function trim(s)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not a whitespace, append to returnString.
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (c != " ") returnString += c;
    }
    return returnString;
}
function stripCharsInBag(s, bag)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function checkInternationalPhone(strPhone){
var bracket=3
strPhone=trim(strPhone)
if(strPhone.indexOf("+")>1) return false
if(strPhone.indexOf("-")!=-1)bracket=bracket+1
if(strPhone.indexOf("(")!=-1 && strPhone.indexOf("(")>bracket)return false
var brchr=strPhone.indexOf("(")
if(strPhone.indexOf("(")!=-1 && strPhone.charAt(brchr+2)!=")")return false
if(strPhone.indexOf("(")==-1 && strPhone.indexOf(")")!=-1)return false
s=stripCharsInBag(strPhone,validWorldPhoneChars);
return (isInteger(s) && s.length >= minDigitsInIPhoneNumber);
}
/*End phone number validation */
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
	if(document.getElementById('busines_phone').value!="")
	{
		if (checkInternationalPhone(document.getElementById('busines_phone').value)==false){
			//alert("Please Enter a Valid Phone Number")
			document.getElementById('busines_phone').value='';
			//Phone.focus()
			ErrorMess = ErrorMess + "<li>Please enter the Valid Business Phone Number.</li>";
			flag='1';
		}
	}
	
	
	if(flag=='1')
	{
	document.getElementById('errmsg').style.display='block';
	document.getElementById("myspan").innerHTML = ErrorMess;
	return false;
	}
	
	}
	/* End validation for singnup second step */

	/* Begin validation for login page */
function getLogin() 
	{		
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
	document.getElementById('errmsg').style.display='block';
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
		var re= /^[a-zA-Z0-9\'\:\s]{2,}$/;
		//re = /^([\w]+(\s)?[\w]+)+$/;
		//re = /^\w+(\s)?\w+]+$/;
		//re = /^\w+(\s)?\w+(\s)?\w+(\s)?\w+$/;		
	  if(!re.test(document.getElementById('eventname').value))
	   {
			alert('Please enter valid event name');
			document.getElementById('eventname').focus();
			return false;	   
	   }
	
	 
	 
	/* else if(document.getElementById('eventname').value){ 
	 
	 
	
	 var numaric = document.getElementById('eventname').value;
	 
	 for(var j=0; j<numaric.length; j++)
		{
		  var alphaa = numaric.charAt(j);
		  var hh = alphaa.charCodeAt(0);
		
			  if((hh > 47 && hh<58) || (hh > 64 && hh<91) || (hh > 96 && hh<123 || hh==32))
			  {
			  }
			else	{
						 alert("Please enter valid event name");
				
				document.getElementById('eventname').focus();
				return false;
			  }
 		}
	 
   }*/
	 
	 
	 
	 
	 if(removeSpaces(document.getElementById('categoryname').value)=="")
		{
			alert('Please enter the event type');
			document.getElementById('categoryname').focus();
			return false;
		}
		if(removeSpaces(document.getElementById('subcat').value)=="")
		{
			alert('Please enter the Subcategory');
			document.getElementById('subcat').focus();
			return false;
		}

		/* if(removeSpaces(document.getElementById('musicgenere').value)=="")
		{
			alert('Please enter the Music Genere');
			document.getElementById('musicgenere').focus();
			return false;
		}*/
		 if(removeSpaces(document.getElementById('eventdes').value)=="")
		{
			alert('Please enter the event description');
			document.getElementById('eventdes').focus();
			return false;
		}
		if(removeSpaces(document.getElementById('cost').value)=="")
		{
			alert('Please enter the Cost');
			document.getElementById('cost').focus();
			return false;
		}
		
		re = /^\d+(\.[0-9]{2})?$/;
	  if(!re.test(document.getElementById('cost').value))
	   {
			alert('Please enter numeric value for the event cost');
			document.getElementById('cost').focus();
			return false;	   
	   }

		if(removeSpaces(document.getElementById('imageName').value)=="")
		{
				alert('Please upload  the event image');
				document.getElementById('filename1').focus();
				return false;
			}

		
		if(removeSpaces(document.getElementById('event_age_suitab').value)=="")
		{
			alert('Please enter the age');
			document.getElementById('event_age_suitab').focus();
			return false;
		}
		if(removeSpaces(document.getElementById('venuname').value)=="")
		{
			alert('Please enter the venue name');
			document.getElementById('venuname').focus();
			return false;
		}
		if(removeSpaces(document.getElementById('eventtime').value)=="")
		{
			alert('Please enter the event start time');
			document.getElementById('eventtime').focus();
			return false;
		}
		
	/*  re = /^[01][0-9]:[0-6][0-9]?$/;
	  if(!re.test(document.getElementById('eventtime').value))
	   {
			alert('Please enter the correct value of start time');
			document.getElementById('eventtime').focus();
			return false;
	   
	   }*/
		if(removeSpaces(document.getElementById('eventtime1').value)=="")
		{
			alert('Please enter the event end time');
			document.getElementById('eventtime1').focus();
			return false;
		}
	/*re = /^[01][0-9]:[0-6][0-9]?$/;
	  if(!re.test(document.getElementById('eventtime1').value))
	   {
			alert('Please enter the correct value of end time');
			document.getElementById('eventtime1').focus();
			return false;
	   
	   }
	   */
	 
	   if(removeSpaces(document.getElementById('date3').value)=="")
		{	alert('Please select event date using calendar');
			return false;	
		}
		
	   if(removeSpaces(document.getElementById('radiotext').value)=="")
		{	alert('Please select venue location');
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
	if(document.getElementById('fname').value!="")
	{
	re = /^\w+$/;
	  if(!re.test(document.getElementById("fname").value))
	   {
		   ErrorMess = ErrorMess + "<li>First Name must contain only letters, numbers.</li>";
	   
		flag='1';
		 }
	}
	
	if(document.getElementById('lname').value=="")
	{
		//alert('+++++');
	 ErrorMess = ErrorMess + "<li>Please Enter the Last Name.</li>";
	 flag='1';	 	
	}
	if(document.getElementById('lname').value!="")
	{
	re = /^\w+$/;
	  if(!re.test(document.getElementById("lname").value))
	   {
		   ErrorMess = ErrorMess + "<li>Last Name must contain only letters, numbers.</li>";
	   
		flag='1';
		 }
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
	if(flag=='1')
	{
	document.getElementById('errmsg').style.display='block';
	document.getElementById("myspan").innerHTML = ErrorMess;
	return false;
	}
	document.getElementById('submit_form').value='yes';
	}
	/* End contact us page */
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
	/*Begin Select and unselect all option value */
function selectall()
{
//alert('++');
var myselect=document.getElementById("category")

for (var i=1; i<myselect.options.length; i++)
{

myselect.options[i].selected=true;	
}

}
function Unselectall()
{
//alert('++');
var myselect=document.getElementById("category")

for (var i=1; i<myselect.options.length; i++)
{

myselect.options[i].selected=false;	
}

}
 /*End Select and unselect all option value */