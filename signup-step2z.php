<?php 
	/*session_start();
	if($_SESSION['page_ref'] != 'signup.php'){
		header ("Location: index.php");
		
	} */
	if(isset($_SESSION['LOGGEDIN_MEMBER_ID']) && $_SESSION['LOGGEDIN_MEMBER_ID'] != ''){
		$mem_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
		$u_pref_q = "select * from member_prefrences where member_id = '$mem_id'";
		$u_pref_res = mysql_query($u_pref_q);
	}

?>
<?php
require_once('admin/database.php');
?>

<?php 
$ParentCatQuery="select * from categories";
$ParentCatQueryExec=mysql_query($ParentCatQuery);
$total_parent_cat = mysql_num_rows($ParentCatQueryExec);
$parent_id = "";
	if($total_parent_cat > 0){
		$i=0;							
		while($Parentdata=mysql_fetch_array($ParentCatQueryExec)){
					$parent_id[$i]=$Parentdata['id'];
					$i++;
		}
	}	
$ParentCatQueryExec=mysql_query($ParentCatQuery);
$MusicGenreQuery = "select id, name from music";
$MusicGenreQueryExec = mysql_query($MusicGenreQuery);
$total_music_genre =  mysql_num_rows($MusicGenreQueryExec);

$crowdQuery = "select * from crowd";
$crowdQueryExec = mysql_query($crowdQuery);
$total_crowd_behavior =  mysql_num_rows($crowdQueryExec);

?>
<?php
//echo "parent----".$total_parent_cat;


$insertid = (isset($_SESSION['insertid']) && $_SESSION['insertid'] != '' ? $_SESSION['insertid'] : $_SESSION['LOGGEDIN_MEMBER_ID']) ;	
	//echo $insertid; 
	$music_genre = DBin($_POST['music_genre']);
	$crowd 		= DBin($_POST['crowd']);
	
	if ( isset($_POST['submitted']) && $_POST['submitted'] == 'yes' ) {
	
	
		
		if ( trim($music_genre) == '' )
			$errors[] = 'Please Select Music Genre';
		if ( trim($crowd) == '' )
			$errors[] = 'Please Select Crowd type';
					
		if ( count( $errors) > 0 ) {
		
			$err = '<table border="0" width="90%"><tr><td class="error" ><ul>';
			for ($i=0;$i<count($errors); $i++) {
				$err .= '<li>' . $errors[$i] . '</li>';
			}
			$err .= '</ul></td></tr></table>';	
		} else {
			
			// write code here to insert data to the database.
		/*	$user_insert = "insert into members (name, email, username, password) values ('$name', '$email', '$username', '$password') ";
			mysql_query($user_insert) or die("unable to insert member info"); */
			//header ("Location: signup-step2.php");
			 
				for( $j=0; $j<$total_parent_cat; $j++){	
				
					$input_field = 'nl1_'.$parent_id[$j];				
					$option_value  = $_POST[$input_field];
					
					$sql_insert="insert into member_prefrences set selection = '".$option_value."',member_id='".$insertid."',prefrence_type='".$parent_id[$j]."'";	
					mysql_query($sql_insert) or die("sql_insert"); 		
					
				}
		
					
					$sql_insert2="insert into member_misc 
						set music_genre = '".$music_genre."',member_id='".$insertid."',crowd='".$crowd."'";
						mysql_query($sql_insert2)or die("sql_insert2");
					
					$_SESSION['insertid'] = '';
					$_SESSION['page_ref'] = '';
					$_SESSION['page_ref'] = 'signup-step2.php';
					header ("Location: signup-step3.php");
					
								
		}	
	}
	if ( isset($_POST['submitted']) && $_POST['submitted'] == 'skip' ){
			
			$_SESSION['insertid'] = '';
			$_SESSION['page_ref'] = '';
			$_SESSION['page_ref'] = 'signup-step2.php';
			header ("Location: signup-step3.php");
	}
	
?>
<style>

.addEInput
{
	width:225px!important;
	height:30px!important;
}

.error
{
	text-align:left;
	float:left;
	width:100%;
}

.error ul
{
	border:#CC8968 solid 1px;
	background-color:#FFFFCC;
	padding:10px;
	background-image:url(images/error.png);
	background-repeat:no-repeat;
	background-position:5px 5px;
	padding-top:40px;
	padding-left:10px;
}

.error ul li
{
	margin-left:40px;
	color:#990000;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}

</style>

<?php 
require_once('includes/header.php');
?>




<!--<script>
  $(document).ready(function(){
    $("#signfrm").validate();
  });
</script>-->

	

		<!--Start Hadding -->
	
<!--<div id="main"> -->
<div id="middleContainer">
	<div id="signup-main">
				
			<div id="signup-main-top"></div>
				<div id="signup-main-middle">
					<div id="signup-head">
						<div id="heading">Help us get started recommending events for you</div>
						
					</div><!--signup-head -->
					<div id="shadow"></div>
					<div class="error"><?php echo $err; ?></div>
					<!-- signup2_submit.php -->
					<form name='signfrm' id='signfrm' method='post' action=''>
					
						

						<div id="form">
						<div id="form-col-l">
							<div class="input-row">
									<div class="field-title">How often do you prefer</div>
							</div><!-- input-row -->	
						<?php 
							
							
							if($total_parent_cat > 0){
									
								while($Parentdata=mysql_fetch_array($ParentCatQueryExec)){
									//$pcid=$Parentdata['id'];
									$pcname = DBout($Parentdata['name']);
									
									echo '<div class="input-row">
												<div class="input-label">'.$pcname." Events".'</div>
										  </div><!-- input-row -->';
									
								}
							}	
						?>
						 
								<div class="input-row" style="margin-top: 28px;">
										<div class="input-label2">Favorite Music Genre?</div>
								</div><!-- input-row -->
								<div class="input-row">
										<div class="input-label3">I mainly enjoy crowds that are:</div>
								</div><!-- input-row -->
							</div><!-- form-col-l -->
							<div id="form-col-r">
								<div class="input-row title-row">
									<div class="input-label">
										<div class="field-title1">Never</div>
										<div class="field-title2">Sometimes</div>
										<div class="field-title3">Often</div>
									</div><!-- label -->
								</div><!-- input-row -->
								
								<!-- changes starts from here 13-05-2011 -->
								<div class="input-row">
						<?php 
							$arrayData = array();	
							if($total_parent_cat > 0)
							{   $i=0;
								$ParentCatQueryExec=mysql_query($ParentCatQuery);
								while($Parentdata=mysql_fetch_array($ParentCatQueryExec))
								{
									$i++;
									$pcid = DBout($Parentdata['id']);
							
						?>	
						
								
										<div class="input-field">
													
													<label class="label_radio" for="nl1_<?php echo $pcid;?>" id="radio1">
														<span class="radio1">
															<input name="nl1_<?php echo $pcid;?>" id="radio-<?php echo $i; ?>" value="N" type="radio" style="height:17px; width:13px;" />
														</span>
													</label>
													<label class="label_radio" for="nl1_<?php echo $pcid;?>" id="radio2">
														<span class="radio2">
															<input name="nl1_<?php echo $pcid;?>" id="radio-<?php echo $i+1; ?>" value="S" type="radio" style="height:17px; width:13px;" />
														</span>	
													</label>
													
													<label class="label_radio" for="nl1_<?php echo $pcid;?>" id="radio3">
														<span class="radio3">
															<input name="nl1_<?php echo $pcid;?>" id="radio-<?php echo $i+2; $i = $i+2; ?>" value="O"  checked="checked" type="radio" style="height:17px; width:13px;" />
														</span>
													</label>
												
											
										</div><!-- input-field -->
								
						
						<?php
								
								}
							}	
						?>				
						</div><!-- input-row -->		
							<!-- end changes -->	
									
								<div class="input-row">
									<div class="input-select">
									<select name="music_genre" id="genre" class="required" style="height:40px;">
									
										<option value="">-- choose one --</option>
										<?php 
											if ($total_music_genre > 0){
												while($Genredata=mysql_fetch_array($MusicGenreQueryExec)){
															$genreId = DBout($Genredata['id']);
															$genreName = DBout($Genredata['name']);
														
										?>
										<option value="<?php echo $genreId;?>"><?php echo $genreName; ?></option>
										
										<?php
										
											}
										  }	
										?>
										
									</select>
									</div><!-- input-field -->
								</div><!-- input-row -->
								<div class="input-row">
									<div class="input-select">
										<select name="crowd" id="crowd" class="required" style="height:40px;">
										
										
											<option value="">-- choose one --</option>
											<?php 
											if ($total_crowd_behavior > 0){
												while($crowdData= mysql_fetch_array($crowdQueryExec)){
															$crowdId = DBout($crowdData['id']);
															$crowdBehavior = DBout($crowdData['behavior']);
														
										?>
											<option value="<?php echo $crowdId;?>"><?php echo $crowdBehavior; ?></option>
										
										<?php
										
											}
										  }	
										?>
										
										</select>
									</div><!-- input-field -->
								</div><!-- input-row -->
								
							</div><!-- form-col-r -->
							<div class="form-submit">
								<div class="skipstep"><span><a onclick='skipfunc();' style='cursor:pointer;' class="">Skip this step</a></span></div>
								<div class="sign-btn"><input name="continue1" id="continue1" type="image" src="<?php echo IMAGE_PATH;?>signup-done.png" class="" vspace="10" hspace="10"/></div>
							</div>	
						</div><!-- form -->
						
						<!--<input type='hidden' name='insertid' value='<?php //echo $_POST['id'];?>' /> -->
						<input type='hidden' name='submitted' id='submitted' value='yes' />
					</form>
				</div><!-- signup-main-middle-->
			<div id="signup-main-bottom"></div>	
		
	</div><!-- signup-main -->
</div><!--top-container -->

<div class="clr"></div>	


<script>

var d = document;
var safari = (navigator.userAgent.toLowerCase().indexOf('safari') != -1) ? true : false;
var gebtn = function(parEl,child) { return parEl.getElementsByTagName(child); };
onload = function() {

    var body = gebtn(d,'body')[0];
    body.className = body.className && body.className != '' ? body.className + ' has-js' : 'has-js';

    if (!d.getElementById || !d.createTextNode) return;
    var ls = gebtn(d,'label');
    for (var i = 0; i < ls.length; i++) {
        var l = ls[i];
        if (l.className.indexOf('label_') == -1) continue;
        var inp = gebtn(l,'input')[0];
       /* if (l.className == 'label_check') {
            l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
            l.onclick = check_it;
        };*/
        if (l.className == 'label_radio') {
            l.className = (safari && inp.checked == true || inp.checked) ? 'label_radio r_on' : 'label_radio r_off';
            l.onclick = turn_radio;
        };
    };
};
/*var check_it = function() {
    var inp = gebtn(this,'input')[0];
    if (this.className == 'label_check c_off' || (!safari && inp.checked)) {
        this.className = 'label_check c_on';
        if (safari) inp.click();
    } else {
        this.className = 'label_check c_off';
        if (safari) inp.click();
    };
}; */
var turn_radio = function() {
    var inp = gebtn(this,'input')[0];
    if (this.className == 'label_radio r_off' || inp.checked) {
        var ls = gebtn(this.parentNode,'label');
        for (var i = 0; i < ls.length; i++) {
            var l = ls[i];
            if (l.className.indexOf('label_radio') == -1)  continue;
            l.className = 'label_radio r_off';
        };
        this.className = 'label_radio r_on';
        if (safari) inp.click();
    } else {
        this.className = 'label_radio r_off';
        if (safari) inp.click();
    };
};

</script>
	



<?php require_once('includes/footer.php');?>






<script>
function skipfunc(){

document.getElementById('submitted').value='skip';
document.signfrm.submit();
}

</script>