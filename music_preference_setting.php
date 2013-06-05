<?php
	
	$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	
	
	
	if ( isset($_POST['continue1_x']) || isset($_POST['continue1']) )
	{
		if ( $_POST['evwall'] != 1 )
			mysql_query("delete from member_music_pref where member_id='". $member_id ."'");
		foreach ($_POST as $key => $value) {
			
			if ( substr($key,0,4) == 'mg1_' ) {	
				$subid = substr($key,4);
				
				if ( $subid > 0 ) {
					$res = mysql_query("insert into member_music_pref (member_id,music_genre,selection) VALUES ('". $member_id ."','". $subid ."','". $value ."')");
				if($res)
					$sucMessage = 'Profile Updated Successfully';
				else
					$sucMessage = 'Error: Try again later';
				
				}
			}
			
		}
	}
	
	$rs = mysql_query("select * from member_music_pref where member_id='". $member_id ."'");
	while ( $ro = mysql_fetch_assoc($rs) ) {
		$pref_data[$ro['music_genre']] = $ro['selection'];
	}
	
?>
<style>
.eventMdlMain {
    margin: auto;
    width: 898px;
}
.recBox .yellow_bar{
	padding-left:0;
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
          <div class="nfBox">I prefer these types of music...</div>
          <div class="nsBox" style="padding-top:0">Never</div>
          <div class="nsBox" style="padding-top:0">Sometimes</div>
          <div class="nsBox" style="padding-top:0">Often</div>
          <div class="clr"></div>
        </div>
		<?php if ( $sucMessage != '' ) { ?>
	  		<font color='green' style="padding:10px; text-align:center; display:block"><strong><?php echo $sucMessage;?> </strong></font>
		<?php } ?>
		<div class="nfBox">&nbsp;</div>
          <div class="nsBox"><br /><input type="radio" name="selAll" id="selAdd1" value="1" onclick="selectAll(1)" /></div>
          <div class="nsBox"><br /><input type="radio" name="selAll" id="selAdd2" value="2" onclick="selectAll(2)" /></div>
          <div class="nsBox"><br /><input type="radio" name="selAll" id="selAdd3" value="3" onclick="selectAll(3)" /></div>
          <div class="clr"></div>
        <br />
        <?php  
								
								$sqlMusic = "SELECT * FROM music";
								$resMusic = mysql_query($sqlMusic);
								
								$i='0';
								while($rowMusic = mysql_fetch_assoc($resMusic))
	        			        {
									$music_id = $rowMusic['id']; 
								 if( ($i%2) == 0)
									   $class='class="preferenceWhtBox"';
								 else
									  $class='class="preferenceBlueBox"';
							?>
        <div <?=$class?>>
          <div class="nfBox">
            <?=$rowMusic['name']?>
          </div>
          <div class="nsBox">
            <input name="mg1_<?=$music_id?>" id="mg1_<?=$music_id?>" type="radio" value="N" class="radio"  <?php if($pref_data[$music_id]=='N') {?>checked="checked" <?php } ?>/>
            <span class="blcST" ></span></div>
          <div class="nsBox">
            <input name="mg1_<?=$music_id?>" id="mg1_<?=$music_id?>" type="radio" value="S" class="radio"  <?php if($pref_data[$music_id]=='S') {?>checked="checked" <?php } ?>/>
            <span class="blcST" ></span></div>
          <div class="nsBox">
            <input name="mg1_<?=$music_id?>" id="mg1_<?=$music_id?>" type="radio" value="O" class="radio"  <?php if($pref_data[$music_id]=='O') {?>checked="checked" <?php } ?> />
            <span class="blcST" ></span></div>
          <div class="clr"></div>
        </div>
        <?php $i++; } ?>
        <input type="hidden" name="save" id="save" value="" />
        <div align="right">
			<input name="continue1" id="continue1" type="image" src="images/save_setting_btn_new.gif" class="vAlign" vspace="10" hspace="10"/>
        </div>
      </div>
    </div>
  </form>
<script>

function selectAll(pr)
{
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