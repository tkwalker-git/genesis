<?php
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	

	
	
	if ( isset($_POST['continue1_x']) || isset($_POST['continue1']) )
	{
		if ( is_array( $_POST['age_group']) )
			$cages = implode(",",$_POST['age_group']);
		else
			$cages = $_POST['age_group'];
		$misc_id = attribValue("member_misc","id","where member_id='".$member_id . "'");
		if ( $_POST['have_kids'] == 'N')
			$cages = '';
			
		if ( $misc_id > 0 )
			$sq2 = "update member_misc set have_kids='". $_POST['have_kids'] ."', age_group='". $cages ."', crowd='". $_POST['crowd'] ."' where member_id='".$member_id . "'";
		else
			$sq2 = "insert into member_misc (member_id,have_kids,age_group,crowd) VALUES ('".$member_id . "','". $cages . "','".$_POST['age_group'] . "','".$_POST['crowd'] . "')";
	
		if(mysql_query($sq2))
			$sucMessage = 'Profile Updated Successfully';
		else
			$sucMessage = 'Error: Try again later';
		
		if ( $_POST['evwall'] != 1 )
			mysql_query("delete from member_age_pref where member_id='". $member_id ."'");
		foreach ($_POST as $key => $value) {
			
			if ( substr($key,0,4) == 'ag1_' ) {	
				$subid = substr($key,4);
				
				if ( $subid > 0 ) {
					mysql_query("insert into member_age_pref (member_id,age_id,selection) VALUES ('". $member_id ."','". $subid ."','". $value ."')");
				}
			}
			
		}
	}
	
	$rs = mysql_query("select * from member_age_pref where member_id='". $member_id ."'");
	while ( $ro = mysql_fetch_assoc($rs) ) {
		$pref_data[$ro['age_id']] = $ro['selection'];
	}
	
?>
<style>
.eventMdlMain {
    margin: auto;
    width: 896px;
}
.recBox .yellow_bar{
	padding-left:0;
	}
	
.nfBox {
    width: 293px;
	}
	
.eventMdlMain {
	width:100%;
	}
	
.nsBox {
    width: 168px;
	}
	
.preferenceBlueBox {
    background:#baecff;
	}
</style>
<form action="" method="post" name="signupform2" id="signupform2">

  <!-- Start Middle-->
  <font color='green'><?php echo $_POST['msg'];?> </font>
  <div id="middleContainer">
    <div class="eventMdlMain">
      
      <div class="yellow_bar">
        <div class="nfBox">I prefer events with crowds that are...</div>
        <div class="nsBox">Never</div>
        <div class="nsBox">Sometimes</div>
        <div class="nsBox" style="width:72px">Often</div>
        <div class="clr"></div>
      </div>
	  <?php if ( $sucMessage != '' ) { ?>
	  		<font color='green' style="padding:10px; text-align:center; display:block"><strong><?php echo $sucMessage;?> </strong></font>
		<?php }
		
		$sqlMusic = "SELECT * FROM age";
		$resMusic = mysql_query($sqlMusic);
		
		$i='0';
		while($rowMusic = mysql_fetch_assoc($resMusic))
		{
			$age_id = $rowMusic['id']; 
		 if( ($i%2) == 0)
			   $class='class="preferenceWhtBox"';
		 else
			  $class='class="preferenceBlueBox"';
		?>
      <div <?php echo $class?>>
        <div class="nfBox"><?php echo $rowMusic['name']?></div>
        <div class="nsBox">
          <input name="ag1_<?php echo $age_id?>" id="ag1_<?php echo $age_id?>" type="radio" value="N" class="radio"  <?php if($pref_data[$age_id]=='N') {?>checked="checked" <?php } ?>/>
          <span class="blcST" ></span></div>
        <div class="nsBox">
          <input name="ag1_<?php echo $age_id?>" id="ag1_<?php echo $age_id?>" type="radio" value="S" class="radio"  <?php if($pref_data[$age_id]=='S') {?>checked="checked" <?php } ?>/>
          <span class="blcST" ></span></div>
        <div class="nsBox" style="width:72px">
          <input name="ag1_<?php echo $age_id?>" id="ag1_<?php echo $age_id?>" type="radio" value="O" class="radio"  <?php if($pref_data[$age_id]=='O') {?>checked="checked" <?php } ?> />
          <span class="blcST" ></span></div>
        <div class="clr"></div>
      </div>
      <?php $i++; } ?>
	<script>
		function CAge(act){
		if (act == 1)
			$("#hidecage").show();
		else
			$("#hidecage").hide();	
		}
	</script>
      <div class="grayBor">
        <div class="nfBox">Do You have children?</div>
        <?php $have_kids = attribValue("member_misc","have_kids","where member_id='".$member_id . "'"); ?>
        <div class="nsBox">
          <input onclick="CAge(1)" name="have_kids" id="have_kids" type="radio" value="Y" class="radio" <?php if($have_kids == 'Y') {?>checked="checked" <?php } ?> />
          Yes</div>
        <div class="nsBox">
          <input onclick="CAge(0)" name="have_kids" id="have_kids" type="radio" value="N" class="radio" <?php if($have_kids == 'N') {?>checked="checked" <?php } ?> />
          No</div>
        <div class="clr"></div>
      </div>
      <?php
								if ( $have_kids == 'Y' )
									$disp = '';
								else
									$disp = 'style="display:none"';	
							?>
      <div class="grayBor" <?php echo $disp;?> id='hidecage'>
        <div class="nfBox">What age group do they fall under? </div>
        <div class="nsBox">
		<?php 
			$age_group  = array();
			$age_group1 = attribValue("member_misc","age_group","where member_id='".$member_id . "'"); 
			$age_group  = explode(",",$age_group1);
									?>
          <input name="age_group[]" id="age_group1" type="checkbox" value="1" class="radio" <?php if(in_array(1,$age_group)) {?>checked="checked" <?php } ?>/>
          Toddler (1 - 4yrs)<br />
          <br />
          <input name="age_group[]" id="age_group3" type="checkbox" value="3" class="radio" <?php if(in_array(3,$age_group)) {?>checked="checked" <?php } ?>/>
          Middle school (10-14yrs) </div>
        <div class="nsBox">
          <input name="age_group[]" id="age_group2" type="checkbox" value="2" class="radio"  <?php if(in_array(2,$age_group)) {?>checked="checked" <?php } ?>/>
          Elementary Age (5 - 9yrs)<br />
          <br />
          <input name="age_group[]" id="age_group4" type="checkbox" value="4" class="radio"  <?php if(in_array(4,$age_group)) {?>checked="checked" <?php } ?>/>
          High School (14-18yrs) </div>
        <div class="clr"></div>
      </div>
      <div class="grayBor"></div>
      <div align="right">
        <input name="continue1" id="continue1" type="image" src="images/save_setting_btn_new.gif" class="vAlign" vspace="10" hspace="10"/>
      </div>
    </div>
  </div>
</form>