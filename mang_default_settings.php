<?php

if(isset($_POST['save_settings'])){

	$bc_venue_id			= $_POST['venue_id'];
	$bc_category_id			= $_POST['category_id'];
	$bc_subcategory_id		= $_POST['subcategory_id'];
	$bc_added_by			= $_POST['added_by'];
	$bc_min_age_allow		= $_POST['min_age_allow'];
	$bc_men_preferred_age	= $_POST['men_preferred_age'];
	$bc_women_preferred_age	= $_POST['women_preferred_age'];
	$bc_event_music	=	array();
	$bc_event_music			= $_POST['event_music'];
	$bc_tickets_quantity	= $_POST['tickets_quantity'];
	$bc_cut_off_ticket		= $_POST['cut_off_ticket'];
	$bc_ticket_sales_type	= $_POST['ticket_sales_type'];
	$bc_ticket_fees			= $_POST['ticket_fees'];
	
	
	if($bc_event_music){
		$i = 0;
		$bc_event_music_val	= '';
		foreach($bc_event_music as $bc_event_music_value){
			$i++;
			if($i!=count($bc_event_music))
				$coma = ',';
			else
				$coma = '';
			$bc_event_music_val .= $bc_event_music_value.$coma;
		}
	}
	
	
	$alReady	= getSingleColumn("id","select * from `event_default_settings` where `user_id`='$member_id'");

	if($alReady){
		$qry	= "UPDATE `event_default_settings` SET `venue` = '$bc_venue_id', `category` = '$bc_category_id', `sub_category` = '$bc_subcategory_id', `host_name` = '$bc_added_by', `min_age` = '$bc_min_age_allow', `men_age` = '$bc_men_preferred_age', `women_age` = '$bc_women_preferred_age', `music_genres` = '$bc_event_music_val', `tickets_quantity` = '$bc_tickets_quantity', `cut_off_ticket` = '$bc_cut_off_ticket', `ticket_sales_type` = '$bc_ticket_sales_type', `ticket_fees` = '$bc_ticket_fees', `user_id` = '$member_id' WHERE `user_id` = '$member_id'";
	
	$res	= mysql_query($qry);
	if($res)
			$sucMessage = "Settings Successfully updates";
		else
			$sucMessage = "Error: Please try Later";
			
	} // end if($alReady)
	else{
		$qry	= "INSERT INTO `event_default_settings` (`id`, `venue`, `category`, `sub_category`, `host_name`, `min_age`, `men_age`, `women_age`, `music_genres`, `tickets_quantity`, `cut_off_ticket`, `ticket_sales_type`, `ticket_fees`, `user_id`) VALUES (NULL, '$bc_venue_id', '$bc_category_id', '$bc_subcategory_id', '$bc_added_by', '$bc_min_age_allow', '$bc_men_preferred_age', '$bc_women_preferred_age', '$bc_event_music_val', '$bc_tickets_quantity', '$bc_cut_off_ticket', '$bc_ticket_sales_type', '$bc_ticket_fees', '$member_id');";
		
		$res	= mysql_query($qry);
		if($res)
			$sucMessage = "Settings Successfully inserted";
		else
			$sucMessage = "Error: Please try Later";
	
	} // end else
}



$qry	= "select * from `event_default_settings` where `user_id`='$member_id'";
$res	= mysql_query($qry);
while($row = mysql_fetch_array($res)){

	$bc_venue_id			= $row['venue'];
	$bc_category_id			= $row['category'];
	$bc_subcategory_id		= $row['sub_category'];
	$bc_added_by			= $row['host_name'];
	$bc_min_age_allow		= $row['min_age'];
	$bc_men_preferred_age	= $row['men_age'];
	$bc_women_preferred_age	= $row['women_age'];
	$bc_event_music			= $row['music_genres'];
	$bc_tickets_quantity	= $row['tickets_quantity'];
	$bc_cut_off_ticket		= $row['cut_off_ticket'];
	$bc_ticket_sales_type	= $row['ticket_sales_type'];
	$bc_ticket_fees			= $row['ticket_fees'];

	$bc_event_musics = explode(",",$bc_event_music);
	
}


$sql = "select * from `venues` where `id`='$bc_venue_id'";
	$r	=	mysql_query($sql);
	while($ro = mysql_fetch_array($r)){
	$bc_venue_address	=	$ro['venue_address'];
	$bc_venue_name		=	$ro['venue_name'];
	$bc_venue_city		=	$ro['venue_city'];
	$bc_venue_zip		=	$ro['venue_zip'];
	}
	
?>
<script type='text/javascript' src='<?php echo ABSOLUTE_PATH; ?>admin/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>admin/css/jquery.autocomplete.css" />
<script>
function dynamic_Select(ajax_page, category_id,sub_category){  
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

$(document).ready(function() {
			$("#venue_name").autocomplete("<?php echo ABSOLUTE_PATH; ?>get_venue_list.php", {
				formatItem: function(data) {
					return data[1];
				},
				formatResult: function(data) {
					return data[1];
				}
			}).result(function(event, data) {
				if (data) {
					if(data[0]){
					$("#venue_id").attr("value", data[0]);
					$('#venue_id').css('color', '#000');
					}
					if(data[2]){
					$("#ev_address1").attr("value", jQuery.trim(data[2]));
					$('#ev_address1').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
					if(data[3]){
					$("#ev_city").attr("value", jQuery.trim(data[3]));
					$('#ev_city').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
					if(data[4]){
					$("#ev_zip").attr("value", jQuery.trim(data[4]));
					$('#ev_zip').attr('readonly', 'readonly');
					$('#venue_id').css('color', '#000');
					}
				}
			}).setOptions({
				max: '100%'
		});
		
		var unique = $('input.unique');
		unique.click(function(){ 
			unique.removeAttr('checked');
			$(this).attr('checked', true);
		});

});
</script>
<style>
.whiteRound{
	padding:10px;
	border:#cecece solid 1px;
	background:#fff;
	width:90%;
	margin:0 auto;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	-khtml-border-radius: 10px;
	border-radius: 10px;
	font-size:13px;
	}

.head{
	font-size:18px;
	width:93%;
	margin:0 auto;
	padding-bottom:5px;
	}

.recBox .yellow_bar{
	padding-left:10px;
	}
	
.new_input{
	background:#fff;
	border:#B8B8B8 solid 1px;
	color:#000;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	-khtml-border-radius:5px;
	border-radius:5px;
	font-size: 13px;
    padding: 5px 3px;
	}
	
strong{
	font-size:15px;
	}
	
.stpBox {
	width:282px;
	padding:10px;
	}

</style>
<?php
	$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
?>
<form method="post">
  <div class="yellow_bar">Event Default Settings</div>
  <!-- /yellow_bar -->
  <br>
  <div class="clr" style="padding-left:5px"><strong><?php echo $sucMessage; ?></strong></div>
  <table cellpadding="0" cellspacing="0" width="95%" align="center">
    <tr>
      <td colspan="3" height="30"><strong>Default Venue</strong></td>
    </tr>
    <tr>
      <td colspan="3"><input type="text" name="venue_name" id="venue_name" class="new_input" value="<?php if ($bc_venue_name){ echo $bc_venue_name; }else{ echo "Start Typing Location Name"; } ?>" onFocus="removeText(this.value,'Start Typing Location Name','venue_name');" onBlur="returnText('Start Typing Location Name','venue_name');" style="margin-bottom:2px; width:170px" />
        <input type="hidden" name="venue_id" id="venue_id" value="<?php echo $bc_venue_id; ?>" />
        &nbsp;&nbsp;
        <input type="text" name="address1" disabled="disabled" id="ev_address1" class="new_input" value="<?php if ($bc_venue_address){ echo $bc_venue_address; } else{ echo 'Address'; } ?>"  onFocus="removeText(this.value,'Address','ev_address1');" onBlur="returnText('Address','ev_address1');" style="width:170px">
        &nbsp;&nbsp;
        <input type="text" name="city" disabled="disabled" id="ev_city" class="new_input" value="<?php if ($bc_venue_city){ echo $bc_venue_city; } else{ echo 'City'; } ?>"  onFocus="removeText(this.value,'City','ev_city');" onBlur="returnText('City','ev_city');" style="width:144px">
        &nbsp;&nbsp;
        <input type="text" name="zip" id="ev_zip" disabled="disabled" class="new_input" value="<?php if ($bc_venue_zip){ echo $bc_venue_zip; } else{ echo 'Zip / Postal Code'; } ?>"  onFocus="removeText(this.value,'Zip / Postal Code','ev_zip');" onBlur="returnText('Zip / Postal Code','ev_zip');" style="width:130px">
        <br>
        <br>
        <br>
      </td>
    </tr>
    <tr>
      <td height="30"><strong>Default Category</strong></td>
      <td><strong>Default Sub-Category</strong></td>
      <td><strong>Default Host Name</strong></td>
    </tr>
    <tr>
      <td width="33%" valign="top"><select name="category_id" id="category_id" class="selectBig" onChange="dynamic_Select('admin/subcategory.php', this.value, 0 );">
          <option value="">-- Select Primary Category --</option>
          <?php
					$res = mysql_query("select * from `categories` ORDER BY `name` ASC");
					while($row = mysql_fetch_array($res)){?>
          <option value="<?php echo $row['id']; ?>" <?php if ($bc_category_id==$row['id']){ echo 'selected="selected"';} ?>><?php echo $row['name']; ?></option>
          <?php
					}
					?>
        </select>
      </td>
      <td width="33%" valign="top"><span id="subcategory_id">
        <select name="subcategory_id" class="selectBig">
          <option value="">-- Select Secondary Category --</option>
          <?php
						if($bc_subcategory_id && $bc_category_id){
							$res = mysql_query("select * from `sub_categories` where `categoryid`='$bc_category_id'");
							while($row = mysql_fetch_array($res)){?>
          <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $bc_subcategory_id){ echo 'selected="selected"'; }?>><?php echo DBout($row['name']); ?></option>
          <?php
							}
						}
						?>
        </select>
        </span> </td>
      <td valign="top"><input type="text" class="new_input" name="added_by" value="<?php echo $bc_added_by; ?>" style="height: 20px; width: 208px;" />
        <br>
        <br>
        <br>
        <br>
      </td>
    </tr>
    <tr>
      <td colspan="3"><div class="stpBox">
          <div><strong>Default Age Requirement</strong></div>
          <br>
          <br>
          <div class="data"><b>Minimum Age Allowed:</b>
            <div id="info1" class="info" title="The no kidding minimum age allowed into your event"></div>
            <div class="age">
              <?php
						if($_POST['min_age_allow']){
							$bc_event_age_suitab = $_POST['min_age_allow'];
						}
						 $sqlAge = "SELECT name,id FROM age";
						$resAge = mysql_query($sqlAge);
						$totalAge= mysql_num_rows($resAge);
						while($rowAge = mysql_fetch_array($resAge))
						{	
						?>
              <div style="float:left; width:50%;padding: 3px 0;"> &nbsp;
                <input name="min_age_allow" class="unique" type="checkbox" value="<?php echo $rowAge['id']; ?>" <?php if($rowAge['id']==$bc_min_age_allow)
							{ echo 'checked="checked"'; }?>>
                <?php echo $rowAge['name']; ?> </div>
              <?php } ?>
              <div class="clr"></div>
              <b>Preferred Age Demographic:</b>
              <div class="info" id="info2" title="Despite your minimum age requirement, what age group are you primarily targeting."></div>
            </div>
            <div class="preferredAge"> <span>Men</span>
              <select name="men_preferred_age" style="width:104px" id="event_age_suitab">
                <option value="">-Select age-</option>
                <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
                <option value="<?php echo $rowAge['id']?>" <?php if($rowAge['id']==$bc_men_preferred_age)
							{ echo 'selected="selected"'; }?>> <?php echo $rowAge['name']?> </option>
                <?php } ?>
              </select>
            </div>
            <div class="preferredAge"> <span>Women</span>
              <select name="women_preferred_age" style="width:104px" id="event_age_suitab">
                <option value="">-Select age-</option>
                <?php $sqlAge = "SELECT name,id FROM age";
							$resAge = mysql_query($sqlAge);
							$totalAge= mysql_num_rows($resAge);
							while($rowAge = mysql_fetch_array($resAge))
							{	
							?>
                <option value="<?php echo $rowAge['id']?>"
						  <?php if($rowAge['id'] == $bc_women_preferred_age){
						  echo 'selected="selected"'; }?>><?php echo $rowAge['name']; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="clr"></div>
          </div>
          <div>
            <div class="clr"></div>
          </div>
        </div>
        <div class="stpBox" style="float:right; width:342px">
          <div><strong>Default Music Genres</strong></div>
          <div class="data">
            <ul style="list-style:none; margin:0px; padding:0 0 0 6px">
              <?php 
				$sqlMusic = "SELECT name,id FROM music";
				$resMusic = mysql_query($sqlMusic);
				$totalMusic= mysql_num_rows($resMusic);
				$no = 0;
				
				if ( !is_array($bc_event_music) )
					$bc_event_music = array();
				
				while($rowMusic = mysql_fetch_array($resMusic))
				{
					if ( in_array($rowMusic['id'],$bc_event_musics) )
						$che = 'checked="checked"';
					else
						$che = '';		
				?>
              <li style="width:33%; float:left; padding:3px 0">
                <label for="<?php echo $no; ?>">
                <input <?php echo $che;?> id="<?php echo $no; ?>" type="checkbox" style="float:left" name="event_music[]" value="<?php echo $rowMusic['id']?>"   />
                <div style="float:left; margin-right:5px"> <?php echo $rowMusic['name']?> </div>
                </label>
              </li>
              <?php $no++;} ?>
            </ul>
            <div class="clr"></div>
          </div>
          <div>
            <div class="clr"></div>
          </div>
        </div></td>
    </tr>
  </table>
  <br>
  <br>
  <div class="yellow_bar">Ticket Default Settings</div>
  <br>
  <table cellpadding="0" cellspacing="0" width="94%" align="center">
    <tr>
      <td height="34" colspan="2">Default Quantity of Tickets &nbsp;
        <input type="text" name="tickets_quantity" class="new_input" style="width:22px; text-align:center" value="<?php echo $bc_tickets_quantity; ?>"></td>
    </tr>
    <tr>
      <td height="34" colspan="2">Cut off Ticket Sales &nbsp;
        <input type="text" class="new_input" name="cut_off_ticket" style="width:22px; text-align:center" value="<?php echo $bc_cut_off_ticket; ?>">
        &nbsp; hours before the event starts</td>
    </tr>
    <tr>
      <td width="26%" height="34">Ticket Sales are:</td>
      <td width="74%"><label>
        <input type="radio" name="ticket_sales_type" <?php if($bc_ticket_sales_type == 'Refundable'){ echo 'checked="checked"'; } ?> value="Refundable">
        Refundable</label>
        &nbsp; &nbsp;
        <label>
        <input name="ticket_sales_type" type="radio" <?php if($bc_ticket_sales_type == 'Non-Refundable'){ echo 'checked="checked"'; } ?> value="Non-Refundable">
        Non-Refundable</label>
      </td>
    </tr>
    <tr>
      <td height="34">Ticket fees will be paid by:</td>
      <td><label>
        <input name="ticket_fees" type="radio" <?php if ($bc_ticket_fees == 1){ echo 'checked="checked"'; } ?> value="1">
        Customer</label>
        &nbsp; &nbsp;
        <label>
        <input name="ticket_fees" type="radio" <?php if ($bc_ticket_fees == 2){ echo 'checked="checked"'; } ?> value="2">
        Promoter</label>
        &nbsp; &nbsp;
        <label>
        <input name="ticket_fees" type="radio" <?php if ($bc_ticket_fees == 3){ echo 'checked="checked"'; } ?> value="3">
        Split</label></td>
    </tr>
  </table>
  <br>
  <div align="right" style="width:95%">
    <input type="image" src="<?php echo IMAGE_PATH; ?>save_setting_btn_new.gif" name="save_settings" value="Save Settings">
    <input type="hidden" name="save_settings" value="Save Settings">
    <br>
    &nbsp; </div>
</form>
