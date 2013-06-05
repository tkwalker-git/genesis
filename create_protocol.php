<?php
	include_once('admin/database.php'); 
	include_once('site_functions.php');

	if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
		
		if($_GET["id"]){
		if (validateID($_SESSION['LOGGEDIN_MEMBER_ID'],'protocols',$_GET["id"]) =='false')
		echo "<script>window.location.href='clinic_manager.php?p=protocols';</script>";
		}

	$bc_protocol_title		= $_POST["protocol_title"];
	$bc_dietary_changes		= DBin($_POST["dietary_changes"]);
	$bc_lifestyle_changes	= DBin($_POST['lifestyle_changes']);
	$bc_suppliers			= $_POST["suppliers"];
	$bc_cost				= $_POST["cost"];
	$bc_retail_price		= $_POST["retail_price"];
	$bc_dosage				= $_POST['dosage'];
	$bc_comment				= $_POST['comment'];
	$bc_without_food		= $_POST['without_food'];
	$bc_breakfast			= $_POST['breakfast'];
	$bc_snack1				= $_POST['snack1'];
	$bc_lunch				= $_POST['lunch'];
	$bc_snack2				= $_POST['snack2'];
	$bc_dinner				= $_POST['dinner'];
	$bc_before_bed			= $_POST['before_bed'];
	$bc_clinic_id			= $_SESSION['LOGGEDIN_MEMBER_ID'];
	$bc_supplements			= $_POST['supplements'];
	$bc_disease_category	= $_POST['disease_category'];
	$bc_disease_subcategory	= $_POST['disease_subcategory'];
	
	$bc_conditions			= $_POST['conditions'];

	$frmID		= $_GET["id"];
	$action1	= isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";

	$action		= "save";
	$sucMessage = "";
	
	$errors = array();

if ($_POST["protocol_title"] == "")
	$errors[] = "Protocol Title can not be empty";
if ($_POST["dietary_changes"] == "")
	$errors[] = "Dietary Changes can not be empty";
if ($_POST["lifestyle_changes"] == "")
	$errors[] = "Lifestyle Changes can not be empty";

$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
for ($i=0;$i<count($errors); $i++) {
	$err .= '<li>' . $errors[$i] . '</li>';
}
$err .= '</ul></td></tr></table>';	

if (isset($_POST["submit"]) ) {


	if (!count($errors)){

		 if ($action1 == "save") {
			 $sql	=	"insert into `protocols` (protocol_title,disease_category_id,disease_subcategory_id,dietary_changes,lifestyle_changes,suppliers,cost,retail_price,clinic_id,dosage,comment,without_food,breakfast,snack1,lunch,snack2,dinner,before_bed) values ('" . $bc_protocol_title . "','" . $bc_disease_category . "','" . $bc_disease_subcategory . "','" . $bc_dietary_changes . "','" . $bc_lifestyle_changes . "','" . $bc_suppliers . "','" . $bc_cost . "','" . $bc_retail_price . "','" . $bc_clinic_id . "','" . $bc_dosage . "','" . $bc_comment . "','" . $bc_without_food . "','" . $bc_breakfast . "','" . $bc_snack1 . "','" . $bc_lunch . "','" . $bc_snack2 . "','" . $bc_dinner . "','" . $bc_before_bed ."')";
			$res			=	mysql_query($sql);
			$frmID			= mysql_insert_id();
			$bc_protocol_id	= $frmID;
			if ($res) {
				$sucMessage = "Record Successfully inserted.";
				echo "<script>window.location.href='clinic_manager.php?p=protocols&id=$frmID';</script>";
			} else {
				$sucMessage = "Error: Please try Later";
			} // end if res
		} // end if
		
		if ($action1 == "edit") {
			
			mysql_query("DELETE from `protocol_supplements` where `protocol_id`='". $frmID ."'");
			mysql_query("DELETE from `protocol_conditions` where `protocol_id`='". $frmID ."'");
			
			$sql	=	"update `protocols` set protocol_title = '" . $bc_protocol_title . "', disease_category_id = '" . $bc_disease_category . "', disease_subcategory_id = '" . $bc_disease_subcategory . "', dietary_changes = '" . $bc_dietary_changes . "', lifestyle_changes = '" . $bc_lifestyle_changes . "', suppliers = '" . $bc_suppliers . "', cost = '" . $bc_cost . "', retail_price = '" . $bc_retail_price . "', dosage = '" . $bc_dosage . "',comment = '" . $bc_comment . "',without_food = '" . $bc_without_food . "',breakfast = '" . $bc_breakfast . "',snack1 = '" . $bc_snack1 . "',lunch = '" . $bc_lunch . "',snack2 = '" . $bc_snack2 . "',dinner = '" . $bc_dinner . "',before_bed = '" . $bc_before_bed . "' where id=". $frmID ."";
			$res			=	mysql_query($sql);
			$bc_protocol_id	= $frmID;
			if ($res) {
				$sucMessage = "Record Successfully updated.";
				echo "<script>window.location.href='clinic_manager.php?p=protocols&id=$frmID';</script>";
			} else {
				$sucMessage = "Error: Please try Later.";
			} // end if res
		} // end if

		if($bc_supplements){
			if(is_array($bc_supplements)){
				foreach($bc_supplements as $bc_supplement_id){
					mysql_query("INSERT INTO `protocol_supplements` (`id`, `protocol_id`, `supplement_id`) VALUES (NULL, '". $bc_protocol_id ."', '". $bc_supplement_id ."');");
				}
			}
		} // end if $bc_supplements
		
		if($bc_conditions){
			if(is_array($bc_conditions)){
				foreach($bc_conditions as $bc_conditions_id){
					mysql_query("INSERT INTO `protocol_conditions` (`id`, `condition_id`, `protocol_id`) VALUES (NULL, '". $bc_conditions_id ."', '". $bc_protocol_id ."');");
				}
			}
		} // end if $bc_conditions
		
		
		
		
		
		
		
	} // end if errors

	else {
		$sucMessage = $err;
	}
} // end if submit


$sql	=	"select * from `protocols` where id=$frmID";
$res	=	mysql_query($sql);
if ($res) {
	if ($row = mysql_fetch_assoc($res) ) {
		$bc_protocol_title			= $row["protocol_title"];
		$bc_dietary_changes			= DBout($row["dietary_changes"]);
		$bc_lifestyle_changes		= DBout($row["lifestyle_changes"]);
		$bc_cost					= $row["cost"];
		$bc_retail_price			= $row["retail_price"];
		$bc_suppliers				= $row['suppliers'];
		$bc_dosage					= $row['dosage'];
		$bc_comment					= $row['comment'];
		$bc_without_food			= $row['without_food'];
		$bc_breakfast				= $row['breakfast'];
		$bc_snack1					= $row['snack1'];
		$bc_lunch					= $row['lunch'];
		$bc_snack2					= $row['snack2'];
		$bc_dinner					= $row['dinner'];
		$bc_before_bed				= $row['before_bed'];
		$bc_disease_category		= $row['disease_category_id'];
		$bc_disease_subcategory		= $row['disease_subcategory_id'];
		
		$res4	= mysql_query("select * from `protocol_supplements` where `protocol_id`='". $frmID ."'");
		while($row4 = mysql_fetch_array($res4))
			$bc_supplements[]		=	$row4['supplement_id'];
			
		$res5	= mysql_query("select * from `protocol_conditions` where `protocol_id`='". $frmID ."'");
		while($row5 = mysql_fetch_array($res5))
			$bc_conditions[]		=	$row5['condition_id'];
		
		
	} // end if row
	$action = "edit";
} // end if 

	$meta_title	= "Create Protocol";

	include_once('includes/header.php');

?>

	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>admin/tinymce/tiny_mce.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>eventDatesPicker/calendar.css" />
	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.4.2.min2.js"></script>
	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.2.6.js"></script>
	<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.formatCurrency-1.4.0.js"></script>
	<script type="text/javascript">
            // Sample 1
            $(document).ready(function()
            {
                $('#currencyButton').click(function()
                {
                    $('#cost').formatCurrency();
                    $('#retail_price').formatCurrency();
                    
                });
            });
            
            // Sample 2
            $(document).ready(function()
            {
                $('.currency').blur(function()
                {
                    $('.currency').formatCurrency();
                });
            });



</script>

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
<script>

function getsupply(val){

if(val >= 1){ 

 $.post("load_supplement.php", {centerid:val},function(data) {
	
        
     var da = data.split('|');
    
     if(da){
     $("#suppliers").val(da[0]).attr("readonly", false);
     }
	  if(da){
     $("#cost").val(da[1]).attr("readonly", false);
     }
     if(da){
     $("#retail_price").val(da[11]).attr("readonly", false);
     }
     if(da){
     $("#dosage").val(da[3]).attr("readonly", false);
     }
     if(da){
     $("#comment").val(da[10]).attr("readonly", false);
     }
     if(da){
     $("#without_food").val(da[2]).attr("checked", "checked");
     }
	 if(da){
     $("#breakfast").val(da[4]).attr("readonly", false);
     }
	 if(da){
     $("#snack1").val(da[5]).attr("readonly", false);
     }
	 if(da){
     $("#lunch").val(da[6]).attr("readonly", false);
     }
	 if(da){
     $("#snack2").val(da[7]).attr("readonly", false);
     }
	 if(da){
     $("#dinner").val(da[8]).attr("readonly", false);
     }
	  if(da){
     $("#before_bed").val(da[9]).attr("readonly", false);
     }
    }
   )
   }else {
   $("#suppliers ,#cost ,#retail_price ,#dosage,#comment ,#without_food,#breakfast,#snack1,#lunch,#snack2,#dinner,#before_bed").removeAttr("readonly");
   $("#without_food").removeAttr("checked");
   $("#suppliers ,#cost ,#retail_price ,#dosage,#comment ,#without_food,#breakfast,#snack1,#lunch,#snack2,#dinner,#before_bed").val("");
   
   }
};
</script>

<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
<div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <span id="campaign"></span>
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%">Create Protocol</div>
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
                <h3><span>ADD PROTOCOL INFORMATION</span></h3>
                <div id="box" class="box">
                  <div id="head">Protocol Title</div>
                  <div class="ev_title">
                    <input type="text" name="protocol_title" id="protocol_title" class="" value="<?php echo $bc_protocol_title; ?>"/>
                  </div>

					<?php include("disease_options.php"); ?>
                    
                    <div style="float:left; width:35%">
                        <div id="head">Add Supplements</div>
                        <div>
                            <select style="width:250px;" class="new_input" name="supplements" id="supplements" onchange="return getsupply(this.value);" >
							<option value="">Select Supplement</option>
                            <?php
							if(get_affiliate() && get_affiliate() != ""){	
							$af_id	=get_affiliate();				  		
							$res = mysql_query("select * from `supplement` where `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' || `clinic_id`='$af_id'");
							}else {
							$res = mysql_query("select * from `supplement` where `clinic_id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
							}
                            
                            while($row = mysql_fetch_array($res)){
                            ?>
	                            <option value="<?php echo $row['id']; ?>" <?php if(is_array($bc_supplements) && in_array($row['id'],$bc_supplements)){ echo 'selected="selected"'; } ?>><?php echo $row['supplement_name']; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                  </div>
                    
                    <div style="float:left; width:25%">
                        <div id="head">Supplier Name</div>
                        <div>
	                        <input type="text" name="suppliers" id="suppliers" class="new_input" value="<?php echo $bc_suppliers; ?>"/>
                        </div>
                    </div>

                    <div style="float:left; width:20%">
                        <div id="head">Cost</div>
                        <input type="text" name="cost" id="cost" class="currency" value="<?php echo $bc_cost; ?>"/>
                    </div>
                    
                     <div style="float:left; width:20%">
                        <div id="head">Retail Price</div>
                        <input type="text" name="retail_price" id="retail_price" class="currency" value="<?php echo $bc_retail_price; ?>"/>
                    </div>
                 

                     <div class="clear"></div><br>

                 <div style="float:left; width:35%">
                  <div id="head">Supplement Directions</div>
                  	<?php include("dosage.php"); ?>
                  </div>
                  <div class="clr"></div><br><br>
                   <div class="create_event_submited">
            <input type="image" src="<?php echo IMAGE_PATH; ?>submit-btn.png" name="submit" value="Create Test" align="right" />
            <input type="hidden" name="submit" value="Create Test" />   
                </div>
                
              </div>
              
            </div>
            
          </div>
          
        </div>
            
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
		elements : "dietary_changes,lifestyle_changes,condition",
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