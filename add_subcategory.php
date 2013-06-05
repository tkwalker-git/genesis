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

								
	$bc_sub_cat_name				= DBin($_POST["sub_cat_name"]);
	$bc_subcategory_description		= DBin($_POST["subcategory_description"]);
	$bc_cat_id						= $_POST['cat_id'];
	/* $bc_disease_subcategory			= DBin($_POST['disease_subcategory']); */
	
	$frmID		= $_GET["id"];
	$action1	= isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

	$action		= "save";
	$sucMessage = "";
	
	$errors = array();

if ($_POST["sub_cat_name"] == "")
	$errors[] = "Sub-Category Name can not be empty";


$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {


	if (!count($errors)){

		 if ($action1 == "save") {
			 $sql	=	"insert into `disease_subcategory` (sub_cat_name,subcategory_description,cat_id) values ('" . $bc_sub_cat_name . "','" . $bc_subcategory_description . "','" . $bc_cat_id . "')";
			$res			=	mysql_query($sql);
			$frmID			= mysql_insert_id();
			/* $bc_findings_id	= $frmID; */
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
				echo "<script>window.location.href='clinic_manager.php?p=subcategories&id=$frmID';</script>";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			
			
			/* mysql_query("DELETE from `protocol_supplements` where `condition_id`='". $frmID ."'"); */
			/* mysql_query("DELETE from `protocol_conditions` where `condition_id`='". $frmID ."'"); */

			
			$sql	=	"update `disease_subcategory` set sub_cat_name = '" . $bc_sub_cat_name . "', subcategory_description = '" . $bc_subcategory_description . "', cat_id = '" . $bc_cat_id . "' where id=". $frmID ."";
			$res			=	mysql_query($sql);

			if ($res) {
				$sucMessage = "Record Successfully updated.";
				echo "<script>window.location.href='clinic_manager.php?p=subcategories&id=$frmID';</script>";
			} else {
				$sucMessage = "Error: Please try Later.";
			} // end if res
		} // end if


		
		
		
		
	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit


$sql	=	"select * from `disease_subcategory` where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_sub_cat_name				= DBout($row["sub_cat_name"]);
		$bc_subcategory_description		= DBout($row["subcategory_description"]);
		$bc_cat_id			= DBout($row["cat_id"]);
		/* $bc_disease_subcategory			= DBout($row["disease_subcategory_id"]); */

		

		
		
	} // end if row
	$action = "edit";
} // end if 

	$meta_title	= "Add Sub-Category";

	include_once('includes/header.php');
?>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
<script>
function dynamic_Select(ajax_page, category_id,sub_category)      
	 {  
		 $.ajax({  
			type: "GET",  
			url: ajax_page,  
			data: "cat=" + category_id + "&subcat=" + sub_category + "&class=selectBig",  
			dataType: "text/html",  
			success: function(html){
			$("#subcategory_id").html(html);
			}
	   	});
	  }	
</script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />

<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>

<style>

.ev_title input{
	color: #808080;
	font-weight:normal;
	}

.new_ticket_right td{
	height:48px;
	padding:0 16px;
	}
	
.ev_new_box_center{
	margin:auto;
	width:936px;
	}
	
.ev_new_box_center .basic_box, .ev_new_box_center .featured_box, .ev_new_box_center .premium_box, .ev_new_box_center .custom_box{
	width:234px;
	height:528px;
	float:left;
	position:absolute
	}
	
	
.ev_new_box_center .basic_box ul, .ev_new_box_center .featured_box ul, .ev_new_box_center .premium_box ul, .ev_new_box_center .custom_box ul{
	padding:10px 0 0 18px;
	margin:0
}

.ev_new_box_center .basic_box ul li, .ev_new_box_center .featured_box ul li, .ev_new_box_center .premium_box ul li, .ev_new_box_center .custom_box ul li{
	list-style:circle;
	font-size:12px
}

.ev_new_box_center .basic_box{
	background:url(images/basic_box.gif) no-repeat;
	}

.ev_new_box_center .featured_box{
	background:url(images/featured_box.gif) no-repeat;
	left:234px;
	}
	
.ev_new_box_center .premium_box{
	background:url(images/premium_box.gif) no-repeat;
	left:468px;
	}
	
.ev_new_box_center .custom_box{
	background:url(images/custom_box.gif) no-repeat;
	left:702px;
	}
	
	
.ev_new_box_center .basic_box .black, .ev_new_box_center .featured_box .black, .ev_new_box_center .premium_box .black, .ev_new_box_center .custom_box .black{	
	filter:alpha(opacity=15);
	-ms-filter:alpha(opacity=15);
	-moz-opacity:0.15;
	opacity:0.15;
	background:#000000;
	width:234px;
	height:528px;
	position:absolute;
	}
	
	
.ev_new_box_center .black:hover{
	display:none;
	}
	
.ev_new_box_center .basic_box:hover > .black, .ev_new_box_center .featured_box:hover > .black, .ev_new_box_center .premium_box:hover > .black, .ev_new_box_center .custom_box:hover > .black{
	display:none;
	}
	
	
.ev_new_box_center .basic_box:hover, .ev_new_box_center .featured_box:hover, .ev_new_box_center .premium_box:hover, .ev_new_box_center .custom_box:hover{
	z-index:9999;
	-moz-box-shadow:0px 0px 7px 2px #464646;
	-webkit-box-shadow:0px 0px 7px 2px #464646;
	-khtml-box-shadow:0px 0px 7px 2px #464646;
	box-shadow:0px 0px 7px 2px #464646;
	filter: progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=0),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=90),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=180),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=270);
	}
	.ev_new_box_center .detail{
	padding:132px 10px 0;
	height:280px;
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	line-height:18px;
}
	
#showimg1,#showimg3{
	padding: 5px 0 5px 20px;
	width: 45%
	}

#showimg2,#showimg4{
	padding: 5px 0 5px 27px;
	width:45%;
	}
	

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
<script language="javascript">
	$(document).ready(function() {
		$(".fancybox2").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'width'			: 540,
			'height'		: 700,
			'type'			: 'iframe'
		});
	});
</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Add Sub-Category</div>
    <div class="clr"><?php echo $sucMessage; ?></div>
    <div class="gredBox">
 
      <form id="z_listing_event_form" action="" method="post" accept-charset="utf-8" onSubmit="return checkErr();" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="ABSOLUTE_PATH" id="ABSOLUTE_PATH" value="<?php echo ABSOLUTE_PATH; ?>" />
        <input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>
       <!--  <div class="clr"></div> -->
        <?php include('dashboard_menu_tk.php'); ?>          
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
              <div id="accordion">

                <h3><span>ADD SUB-CATEGORY INFORMATION</span></h3>
                <div id="box" class="box">
                  <div id="head">Sub-Category Name</div>
                  <div class="ev_title">
                    <input type="text" name="sub_cat_name" id="sub_cat_name" class="" value="<?php echo $bc_sub_cat_name; ?>"/>
                  </div>                                   
					
					
					                      
                <div  class="ev_fltlft" style="width:33%">
                    <div id="head" >Primary Category</div>
                    <select name="cat_id" id="cat_id" class="selectBig" onChange="dynamic_Select('admin/subcategory_tk.php', this.value, 0 );">
                      <option value="">-- Select Primary Category --</option>
                      <?php
			$res = mysql_query("select * from `disease_category` ORDER BY `cat_name` ASC");
			while($row = mysql_fetch_array($res)){?>
                      <option value="<?php echo $row['id']; ?>" <?php if ($bc_cat_id==$row['id']){ echo 'selected="selected"';} ?>><?php echo $row['cat_name']; ?></option>
                      <?php
			}
			?>
                    </select>
                  </div>
                <br /><br />
              <!--   </div> -->


					<!-- <div class="clear"></div> -->

                   <br /><br />
																				
					
					<div class="clear"></div>

                  <div id="head">Sub-Category Description</div>
                  
                  <div>
                    <textarea  name="subcategory_description" id="subcategory_description" class="bc_input" style="width:825px;height:250px;" /><?php echo $bc_subcategory_description; ?></textarea>
                  </div>
                  <div class="clear"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="create_event_submited">
            <input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" name="submit" value="Add Sub-Category" align="right" />
            <input type="hidden" name="submit" value="Add Sub-Category" />  
            <div class="clr"></div>      
        </div>
      </form>
    </div>
  </div>
</div>
<!-- <?php include_once('includes/footer.php');?> -->
<script>
	tinyMCE.init({
		// General options
		mode : "exact",
		theme : "simple",
		elements : "subcategory_description",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imagemanager",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen,|,print,|,ltr,rtl,|,styleprops,hr,removeformat,|,preview,help,code",
		theme_advanced_buttons3 : "visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
//		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "style.css",
	});
	

</script>