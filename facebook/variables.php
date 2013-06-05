<?php
/*_______________________________________________________________________
Created By	: Programmer web2
Created On	: 11-Jan-2007
Modified By :Programmer web2
Modified On :08-Aug-2009
Description : This file consists all the config varibales and settings used in the site
_________________________________________________________________________
*/
session_start();

/****************  DATABASE CONFIG AREA STARTS  **********************************************/
$DATABASE_TYPE="mysql";
$DATABASE_NAME="eventgra_brg7live";
$HOST="localhost";
$USERNAME="eventgra_bbrgra";
$PASSWORD="JTbbZVqPTW]L";

/****************  DATABASE CONFIG AREA ENDS  **********************************************/

/****************  GOOGLE MAPS DATA  **************************************************/
define('GMAP_KEY', 'ABQIAAAAgHNvxt25j0ktteCvUSV6jRR1nDDNS_uTtSiVkuYcyVi8hfidJxT1IiTa_1pyoKzM76phHrAKxZVKsg');
/****************  GOOGLE MAPS DATA END  **************************************************/

/****************  FACEBOOK DATA  **************************************************/
//define('FACEBOOK_APP_ID','147946741892206');
//define('FACEBOOK_SECRET','35d0ba775757c080586fddd6cdeb580a');

define('FACEBOOK_APP_ID','129834663751946');
define('FACEBOOK_SECRET','58e36a5c672c0aa978ad8cf1786b89e9');
/****************  FACEBOOK DATA END  **************************************************/

/****************  Some commn settings starts *********************************************/
$granite = "/eventgrabber/";

define("DOCUMENTROOT",$_SERVER['DOCUMENT_ROOT'].$granite);
//define("DOCUMENTROOT","/home/granite.com/public_html/");
define("INC",DOCUMENTROOT."codelibrary/inc/");
define("CLASSES",DOCUMENTROOT."codelibrary/classes/");
define("CSS",DOCUMENTROOT."codelibrary/css/");
define("JS",DOCUMENTROOT."codelibrary/js/");
define("ADMIN_CSS","../codelibrary/css/");
define("ADMIN_JS","../codelibrary/js/");
define("FACEBOX","facebox/");
define("ADMIN_FACEBOX","../facebox/");
define("ADMIN_IMAGE_PATH", "../images_site/");
define("SITE_TITLE","eventgrabber.com");
define("SITE_ADMIN_TITLE","Admin Area");
define("SITE_ADMIN_FOOTER","Copyright &copy; 2010-2011 Eventgrabber. All Rights Reserved&nbsp;");
define("NOIMAGE","icon_noimage.gif");
$domainName = "eventgrabber.com";
$site_url="http://".$_SERVER['HTTP_HOST'].$granite;
$secure_url="http://".$_SERVER['HTTP_HOST'].$granite;
define("site_url",$site_url);
$s_path=$site_url;
$imageTitle = "eventgrabber.com";
$AdminEmail = "admin@eventgrabber.com";
$gblfromEmail = "admin@eventgrabber.com";
/****************  Some commn settings starts *******************/

/****************   Mail Setting Variables starts **********/
$SiteLink = $site_url;
$SiteName = "eventgrabber.com";
define("MAIL_SITE_NAME",$SiteName);
define("MAIL_SITE_HOME_LINK",$site_url);
define("MAIL_SITE_LOGIN_LINK",$site_url);

define("MAIL_SITE_TEAM","The Eventgrabber Team");

define("SITE_EMAIL_ADDRESS","info@eventgrabber.com");
define("SITE_MAILING_ADDRESS","The Eventgrabber Company Address");
define("COMPANY_NAME","eventgrabber.com");
/****************   Mail Setting Variables starts **************/
############## SITE CONFIGURATION ENDS
define("NUMBEROFRECORD","5");
define("MYEVENTPAGING","3");

####### GLOBAL VARIABLES ##################

/****************  Following code will be for the Cross Site Scripting Starts **************/
if (!empty($_POST)) {
	reset($_POST);
	while (list($k,$v)=each($_POST)) {
	if(!is_array($_POST{$k}))
		$_POST{$k}=htmlentities($v,ENT_QUOTES);
	}
}
if (!empty($_GET)) {
	reset($_GET);
	while (list($k,$v)=each($_GET))
	{
		if(!is_array($_GET{$k}))
			$_GET{$k}=htmlentities($v,ENT_QUOTES);
	}
}
if (!empty($_REQUEST)) {
	reset($_REQUEST);
	while (list($k,$v)=each($_REQUEST))
	{
		if(!is_array($_REQUEST{$k}))
			$_REQUEST{$k}=htmlentities($v,ENT_QUOTES);
	}
}
/****************  Following code will be for the Cross Site Scripting ends **************/

/****************  Error Handling Code starts *******************************************/
define ("FATAL","E_USER_ERROR");
define ("ERROR","E_USER_WARNING");
define ("WARNING","E_USER_NOTICE");
//error_reporting (FATAL | ERROR | WARNING);
error_reporting ("E_ALL");
/****************  Error Handling Code starts *******************************************/

define("PAGING_SIZE",15);
define("EVEN_PAGING_SIZE",20);
define("TOTAL_RATING_SIZE",1);

$admin_paging = 15;              //5 by default
$page_name=basename($_SERVER["PHP_SELF"]);
/******************** Different Status used in the site **********************/
$GL_active=1;
$GL_not_active=0; //deactive status
/******************** Different Status used in the site *************************************/
/******************** Admin tables ********/
$admin_login_table="tbl_admin_login_table";
$admin_ip_tracking="tbl_admin_ip_tracking";

/******************** Admin tables **********************************************************/
/******************** variables to store table names start **********************************/
$link=mysql_connect($HOST,$USERNAME,$PASSWORD) or die(mysql_error());
mysql_select_db($DATABASE_NAME,$link) or  die("couldn't connect to database");
$sqlTabel="select * from database_table";
$tabelResult=mysql_query($sqlTabel) or die(mysql_error());
while($tableData=mysql_fetch_array($tabelResult))
{
	$table1="var_".$tableData['variable_name'];
	$$table1=$tableData['table_name'];
}
define("IMAGE_PATH", "images_site/");
define("CSS_PATH","codelibrary/css/");
/******************** variables to store table names ends ****************/




/****************************************/
$tdClass1="evenRow";   // Class name of style sheet for listing page
$tdClass2="oddRow";	// Class name of style sheet for listing page

$frmtdClass1="evenRow";   // Class name of style sheet for listing page
$frmtdClass2="oddRow";	// Class name of style sheet for listing page

$frmtdClassExt1="evenRowext";   // Class name of style sheet for listing page extra for admin
$frmtdClassExt2="oddRowext";	// Class name of style sheet for listing page extra for admin

/*************************************************/

/**************************  Activate/Deavtivate Image variables **************************/
$ad_active_image="<img src=\"".ADMIN_IMAGE_PATH."active.gif\">";
$ad_deactive_image="<img src=\"".ADMIN_IMAGE_PATH."deactive.gif\">";
$imageUrl=$site_url.IMAGE_PATH; // images URL

$admin_editor_upload_image = IMAGE_PATH."adminEditorUploadImage.png";
/**************************  Activate/Deavtivate Image variables **************************/

$cntInkColor=1;
$min_cntInkColor=1;
$max_cntInkColor=10;

/****************************** paging array******************************/
$arr_user_paging[5]="5 Per Page";
$arr_user_paging[10]="10 Per Page";
$arr_user_paging[15]="15 Per Page";
$arr_user_paging[20]="20 Per Page";
$arr_user_paging[50]="50 Per Page";

/* ---------------  Array Section Start--------------------------------------------*/
//left category array
$leftIncRequired = array();
$showLeftCategory =array();
$showLeftMyAccount =array();
$arr_page_without_session =array();
$arr_page_with_myaccount_session =array();
$arr_page_without_top_banner =array();
$arr_page_without_bottom_banner =array();

//error message/message array
$arr_error_msg=Array();
$arr_error_msg[0]="Error on saving data.";
$arr_error_msg[1]="Invalid Username/Password.";
$arr_error_msg[2]="This email address already exists for other user";
$arr_error_msg[3]="Invalid old password.";

//confirmation message array
$arr_con_msg=Array();
$arr_con_msg[0]="";
$arr_con_msg[1]="You have been logout successfully.";
$arr_con_msg[2]="Email address changed successfully.";
$arr_con_msg[3]="Password has been changed Successfully.";
$arr_con_msg[8]="Line Limit has been added successfully.";
$arr_con_msg[9]="Line Limit has been updated successfully.";

$user_paging  = 15;

$gblerror_page = 'error_page.php';

?>