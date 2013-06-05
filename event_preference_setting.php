<?php
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	$sql = "select * from users where id=" . $member_id;
	$res = mysql_query($sql);
	if ( $row = mysql_fetch_assoc($res) ) {
		
		$name 	= DBout($row['firstname']);
		$email  = DBout($row['email']);
		
		$image	= DBout($row['image_name']);
		
		if ($image != '' && file_exists(DOC_ROOT . 'images/members/' . $image ) ) {
			$img = returnImage( ABSOLUTE_PATH . 'images/members/' . $image,211,253 );
			$img = '<img align="center" '. $img .' />';	
		} else
			$img = '<img src="' . IMAGE_PATH . 'user_awatar.png" height="253" width="211" border="0" />';	
		
		$total_events 	= getSingleColumn('tot',"select count(*) as tot from events where userid=" . $member_id);
		
		$total_events_grabbed = 0;
	}

	
	if ( isset($_POST['continue1_x']) || isset($_POST['continue1']) )
	{
		if ( $_POST['evwall'] != 1 )
			mysql_query("delete from member_prefrences where member_id='". $member_id ."'");
		foreach ($_POST as $key => $value) {
			
			if ( substr($key,0,4) == 'nl1_' ) {	
				$subid = substr($key,4);
				
				if ( $subid > 0 ) {
					mysql_query("insert into member_prefrences (member_id,prefrence_type,selection) VALUES ('". $member_id ."','". $subid ."','". $value ."')");
				}
			}
			
			$sucMessage = 'Profile Updated Successfully';
			
		}
	}
	
	$rs = mysql_query("select * from member_prefrences where member_id='". $member_id ."'");
	while ( $ro = mysql_fetch_assoc($rs) ) {
		$pref_data[$ro['prefrence_type']] = $ro['selection'];
	}

?>
<style>
.eventMdlMain {
    margin: auto;
    width: 896px;
}

.recBox .yellow_bar{
	padding-left:10px;
	}
	
.nfBox {
    width: 280px;
	}
	
.eventMdlMain {
	width:100%;
	}
	
.nsBox {
    width: 142px;
	padding-top:5px
	}
</style>
<form name="signfrm" id="signfrm" method="post" action="" enctype="multipart/form-data">
  <!-- Start Middle-->
  <?php if ( $sucMessage != '' ) { ?>
  <font color='green' style="padding:10px; text-align:center; display:block"><strong><?php echo $sucMessage;?> </strong></font>
  <?php } ?>
  <div id="middleContainer">
    <div class="eventMdlMain">
      <div class="yellow_bar">
        <div class="nfBox" style="padding:0">I prefer these events...</div>
        <div class="nsBox" style="padding-top:0">Never</div>
        <div class="nsBox" style="padding-top:0">Sometimes</div>
        <div class="nsBox" style="padding-top:0">Often</div>
		<div class="clr"></div>
      </div>
	  <br />
	   <div class="nfBox" style="padding-top:0">&nbsp;</div>
       <div class="nsBox" style="padding-top:0"><input type="radio" name="selAll" id="selAdd1" value="1" onclick="selectAll(1)" /></div>
       <div class="nsBox" style="padding-top:0"><input type="radio" name="selAll" id="selAdd2" value="2" onclick="selectAll(2)" /></div>
       <div class="nsBox" style="padding-top:0"><input type="radio" name="selAll" id="selAdd3" value="3" onclick="selectAll(3)" /></div>
	   <div class="clr"></div>
	  <br />
	  
	  <?php 
	  if($err_msg != '' || $msg != '') {?>
      <div id="div_msg" style="height:40px;"> <span>
	  	<?php
			if($err_msg != '')
				echo '<div id="error_message" style="width:370px;position:absolute;top:-60px;left:180px;">'.$err_msg.'</div>';
			elseif($msg != '') 
				echo '<div id="success_message" style="width:370px;position:absolute;top:-60px;left:180px;">'.$msg.'</div>';
		?>
        </span>
        <div class="clr"></div>
      </div>
      <?php
	  }
								$query = "select * from categories "; 
								$res = mysql_query($query);
								while ($r = mysql_fetch_assoc($res)) {
									$category_id = $r['id'];
							?>
      <div class="yellow_bar"><?php echo $r['name'];?></div>
      <?php
								
			$sql2 	= "select * from sub_categories where categoryid='". $category_id ."' " ;
			$res2	= mysql_query($sql2);
			while ($rows = mysql_fetch_assoc($res2) ) {
				$sub_cat_id			= $rows['id'];
				$sub_cat_name 		= DBout($rows['name']);
			
		?>
      <div class="preferenceWhtBox">
        <div class="nfBox"><?php echo $sub_cat_name;?></div>
        <div class="nsBox">
          <input name="nl1_<?php echo $sub_cat_id;?>" type="radio" value="N" class="radio"  <?php if( $pref_data[$sub_cat_id]=='N') {?>checked="checked" <?php } ?>/>
        </div>
        <div class="nsBox">
          <input name="nl1_<?php echo $sub_cat_id;?>" type="radio" value="S" class="radio" <?php if($pref_data[$sub_cat_id]=='S') {?>checked="checked" <?php } ?> />
        </div>
        <div class="nsBox">
          <input name="nl1_<?php echo $sub_cat_id;?>" type="radio" value="O"  <?php if($pref_data[$sub_cat_id]=='O') {?>checked="checked" <?php } ?> class="radio" />
        </div>
        <div class="clr"></div>
      </div>
      <?php } } ?>
    </div>
  </div>
  <div align="right">
    <input name="continue1" id="continue1" type="image" src="images/save_setting_btn_new.gif" class="vAlign" vspace="10" hspace="10"/>
  </div>
</form>
<script>

function selectAll(pr){
	var inputTags = document.getElementsByTagName('input');
	for(var i=0;i<inputTags.length;i++) {
		if ( pr == 1) {
			if( inputTags[i].value == 'N' )
				inputTags[i].checked = true;
		} else if ( pr == 2) {
			if( inputTags[i].value == 'S' )
				inputTags[i].checked = true;
		}  else if ( pr == 3) {
			if( inputTags[i].value == 'O' )
				inputTags[i].checked = true;
		}
	}
}
</script>