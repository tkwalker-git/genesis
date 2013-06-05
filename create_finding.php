<?php
	include_once('admin/database.php'); 
	include_once('site_functions.php');

	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
	/*
	if($_GET["id"]){
		if (validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'findings_category',$_GET["id"]) =='false')
		echo "<script>window.location.href='clinic_manager.php?p=findings';</script>";
		}
*/

								
	$bc_finding_name			= DBin($_POST["finding_name"]);
	$bc_finding_description		= DBin($_POST["finding_description"]);
	$bc_disease_category		= DBin($_POST['disease_category']);
	$bc_disease_subcategory		= DBin($_POST['disease_subcategory']);
	
	$frmID		= $_GET["id"];
	$action1	= isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

	$action		= "save";
	$sucMessage = "";
	
	$errors = array();

if ($_POST["finding_name"] == "")
	$errors[] = "Findings Name can not be empty";


$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {


	if (!count($errors)){

		 if ($action1 == "save") {
			 $sql	=	"insert into `findings_category` (finding_name,finding_description,disease_category_id,disease_subcategory_id) values ('" . $bc_finding_name . "','" . $bc_finding_description . "','" . $bc_disease_category . "','" . $bc_disease_subcategory . "')";
			$res			=	mysql_query($sql);
			$frmID			= mysql_insert_id();
			/* $bc_findings_id	= $frmID; */
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
				echo "<script>window.location.href='clinic_manager.php?p=findings&id=$frmID';</script>";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			
			
			/* mysql_query("DELETE from `protocol_supplements` where `condition_id`='". $frmID ."'"); */
			/* mysql_query("DELETE from `protocol_conditions` where `condition_id`='". $frmID ."'"); */

			
			$sql	=	"update `findings_category` set finding_name = '" . $bc_finding_name . "', finding_description = '" . $bc_finding_description . "', disease_category_id = '" . $bc_disease_category . "', disease_subcategory_id = '" . $bc_disease_subcategory . "' where id=". $frmID ."";
			$res			=	mysql_query($sql);
			/* $bc_findings_id	= $frmID; */
			if ($res) {
				$sucMessage = "Record Successfully updated.";
				echo "<script>window.location.href='clinic_manager.php?p=findings&id=$frmID';</script>";
			} else {
				$sucMessage = "Error: Please try Later.";
			} // end if res
		} // end if


		
		
		
		
	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit


$sql	=	"select * from `findings_category` where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_finding_name			= DBout($row["finding_name"]);
		$bc_finding_description		= DBout($row["finding_description"]);
		$bc_disease_category		= DBout($row["disease_category_id"]);
		$bc_disease_subcategory		= DBout($row["disease_subcategory_id"]);

		

		
		
	} // end if row
	$action = "edit";
} // end if 

	$meta_title	= "Create Findings";

	include_once('includes/header.php');

?>

	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />
	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>

<style>
	#accordion h3 {
		border-bottom: 1px solid #89C76F;
		border-radius: 5px 5px 5px 5px;
		color: #FFFFFF;
		cursor: pointer;
		background: none repeat scroll 0 0 #43BB9A;
		font-size: 18px;
		font-weight: bold;
		margin: 0;
		padding: 10px;
	}
</style>
<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
<div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Create Findings</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="on">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
        <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
        <div class="clr"></div>
        <?php include('dashboard_menu_tk.php'); ?>
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">
                <h3><span>ADD FINDINGS INFORMATION</span></h3>
                <div id="box" class="box">
                  <div id="head">Findings Name</div>
                  <div class="ev_title">
                    <input type="text" name="finding_name" id="finding_name" class="" value="<?php echo $bc_finding_name; ?>"/>
                  </div>                                   

					<?php include("disease_options_findings.php"); ?>
																				
					
					<div class="clear"></div>
					<div id="head">Finding Description</div>
                  <div>
                    <textarea name="finding_description" id="finding_description" class="bc_input" style="width:825px; height:250px"><?php echo $bc_finding_description; ?></textarea>
                  </div>                                                     
                     <div class="clear"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
            <input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" name="submit" value="Create Test" align="right" />
            <input type="hidden" name="submit" value="Create Test" />        
        </div>
      </form>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php');?>
<script>
	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "simple",
		elements : "finding_description",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect",
		theme_advanced_buttons2 : "bullist,numlist,|,link,unlink,image,|,forecolor,backcolor",
		theme_advanced_buttons3 :  "styleselect,formatselect,fontsizeselect",
		// "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "style.css",
	});
	

</script>