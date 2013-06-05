<?php
	include_once('admin/database.php'); 
	include_once('site_functions.php');
	include("nba_packages.php");




$id = $_GET['id'];
$id = $id-1;


?>
<style>



/* Container */
#simplemodal-container{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	width:400px;
	}
	
#simplemodal-container .simplemodal-data {
	
	padding:8px;
}
#simplemodal-container code {
	background:#141414;
	border-left:3px solid #65B43D;
	color:#bbb;
	display:block;
	font-size:12px;
	margin-bottom:12px;
	padding:4px 6px 6px;
}
#simplemodal-container h3 {
	background: none repeat scroll 0 0 #F2F2F2;
    border-left: 1px solid #CCCCCC;
    border-right: 1px solid #CCCCCC;
    border-top: 1px solid #CCCCCC;
    color: #84B8D9;
    margin: 0;
    padding: 10px;
}
.formfield{
	overflow:hidden;
	padding:3px 5px;
	
	line-height:24px;
	}

.rsvpBox{
	border: 1px solid #C1C1C1;
	background:#FFFFFF;
	min-height:150px;
	padding-top:10px;
	}
</style>
<script>
function check(){
	var value = '';
	jQuery.each(jQuery("input[name='package[]']"), function() {
		if(jQuery(this).attr('checked')){
			value = (jQuery(this).val());
		}
	});
	if(value==''){
		alert("Please select a Package");
		return false;
	}
	else{
	window.location.href='buy-package.php?v='+value+'';
	
	}

}




</script>
<div id="simplemodal-container">
<div id="basic-modal-content" class="basic-modal-content">
	
	<div id="rsvp">
		<h3><?php echo $reservations[$id]['name']; ?></h3>
		<!-- SET THE ACTION URL -->
	<div class="rsvpBox">
		
		<?php
		$s = 0;
		foreach ($reservations[$id]['packages']	as $name =>	$price){
		$s++;?>
		
			<div class="formfield">
				<input type="radio" name="package[]" value="<?php echo base64_encode($name."[price]".$price); ?>"> <?php echo $name." &nbsp;($".$price.")"; ?>
			</div>
		
		<?php
		}
		?>
		
		<div align="center" style="padding:10px 0">
		<input type="button" name="SubmitPackage" value="Submit" class="submit_btn" onClick="return check();" />		
		</div>
		
		</div>
	</div>
</div>
</div>