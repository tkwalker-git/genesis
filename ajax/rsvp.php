<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');

$event_id = $_GET['event_id'];

	if($_POST['firstname']){
		$fname_rsvp		= $_POST['firstname'];
		$lname_rsvp		= $_POST['lastname'];
		$email_rsvp		= $_POST['email'];
		$how_did_rsvp	= $_POST['how_did'];
		$event_id		= $_POST['event_id'];
		$how			= $_POST['how_did'];
		$event_name		= getSingleColumn('event_name','select * from `events` where id='.$event_id);
		
		if($how_did_rsvp=='Other')
			$how_did_rsvp= $_POST['how_other'];
		if($how_did_rsvp=='Type here')
			$how_did_rsvp= '';
		$name = $fname_rsvp." ".$lname_rsvp;
		$rs = mysql_query("select * from `events_rsvp` where `event_id`='$event_id' && `email`='$email_rsvp'");
		if(!mysql_num_rows($rs)){
			$rz = mysql_query("INSERT INTO `events_rsvp` (`rsvp_id`, `event_id`, `name`, `email`, `how_did_hear`) VALUES (NULL, '$event_id', '$name', '$email_rsvp', '$how_did_rsvp')");
			if($rz){
				$msgs='yes';
				$msg = "Thank you for confirming your RSVP for the '".$event_name."' event. <br /><br />
Also, don't forget to go to <a target='_blank' href='".ABSOLUTE_PATH."signup.php' style='color:#0033FF'>www.eventgrabber.com</a> and signup to get your personalized event recommendations!";
				sendMail($email_rsvp, "EventGrabber <info@eventgrabber.com>", "RSVP - ".$event_name."", $msg );
				}
			else{
			$msg = "Error: Try again later";
			}
		}
		else{
			$msg = "Error: You already submitted RSVP for this event";
		}
	}
		
?>
<script>
function checkRsvp(){
	if($('#firstname').val()==''){
		alert("Enter First name");
		$('#firstname').focus();
		return false;}
	if($('#lastname').val()==''){
		alert("Enter Last name");
		$('#lastname').focus();
		return false;}
	if($('#email').val()==''){
		alert("Enter Email address");
		$('#email').focus();
		return false;}
	var str = $('#email').val();
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	if (filter.test(str))
		testresults=true;
	else{
		alert("Invalid Email address!");
		return false;
		}
		
		var firstname	= $('#firstname').val();
		var lastname 	= $('#lastname').val();
		var email 		= $('#email').val();
		var how_did		= $('#how_did').val();
		var event_id	= $('#event_id').val();
		if(how_did=='Other')
			how_did		= $('#how_other').val();
		
		
		 $.ajax({  
			type: "POST",  
			url: "/ajax/rsvp.php",
			data: "firstname=" + firstname + "&lastname=" + lastname + "&email=" + email + "&how_did=" + how_did + "&event_id=" + event_id,
			dataType: "text/html",  
			beforeSend: function()
			{
				$('#rsvp_submit').html('<img src="/images/loading.gif">');
			},
			success: function(html){
				$('#ds').html(html);
			},
			complete: function()
			{
				$('#rsvp_submit').html('<input type="button" name="submit_rsvp" value="RSVP" class="submit_btn" onClick="return checkRsvp();" align="left" />');
			}
	   	});
}


</script>
<style>
		/* Overlay */
	#simplemodal-overlay {
		background-color:#000;
		cursor:wait;
	}
	/* Container */
	#simplemo {
		min-height:250px;
		width:400px;
		color:#000;
		-moz-border-radius:10px;
		border-radius:10px;
		-webkit-border-radius:10px;
		-moz-box-shadow:0 1px 3px #777;
		-webkit-box-shadow:0 2px 3px #777;
		box-shadow:0 2px 3px #777;
		background: #E7FCFF;
		padding:12px;
		behavior: url(http://www.eventgrabber.com/css/PIE.htc);
	}

	#simplemo .simplemodal-data {
		padding:8px;
	}
	#simplemo code {
		background:#141414;
		border-left:3px solid #65B43D;
		color:#bbb;
		display:block;
		font-size:12px;
		margin-bottom:12px;
		padding:4px 6px 6px;
	}
	#simplemo a {
		color:#ddd;
	}
	#close {
		width:25px;
		height:29px;
		display:inline;
		z-index:3200;
		position:absolute;
		top:-15px;
		right:-16px;
		cursor:pointer;
	}
	#simplemo h3 {
		background: none repeat scroll 0 0 #F2F2F2;
		border-left: 1px solid #CCCCCC;
		border-right: 1px solid #CCCCCC;
		border-top: 1px solid #CCCCCC;
		color: #84B8D9;
		margin: 0;
		padding: 10px;
		text-align:left
	}
	.formfield{
		overflow:hidden;
		padding-top: 8px;
		}
	.formfield label{
		float: left;
		color: #666666;
		font-weight: bold;
		padding-right: 10px;
		text-align: right;
		width:115px;
		}
	.formfield input,.formfield select{
		width:232px;
		border: 1px solid #BDC7D8;
		height:18px;
		font-size: 11px;
		}
		
	.formfield select{
		height: 22px;
		padding: 2px;
		width: 234px;
		}
	.rsvpBox{
		border: 1px solid #C1C1C1;
		background:#FFFFFF;
		min-height:201px;
		padding-top:10px;
		}
</style>
<div style="margin:0 auto; width:400px" id="ds">
  <div id="simplemo" class="simplemo" style="position: fixed; z-index: 1002; min-height: 250px; width: 400px; top: 87.5px;">
    <div id="basic-modal-content" class="basic-modal-content">
      <div id="close"><img src="<?php echo IMAGE_PATH; ?>x.png" onClick="hideOverlayer()" /></div>
      <div id="rsvp">
        <h3>RSVP for this event.</h3>
        <!-- SET THE ACTION URL -->
        <div class="rsvpBox">
		<?php
		$close	= getSingleColumn("close","select * from `event_rsvp_close` where `event_id`='$event_id'");
		if($close==1){
			echo "<div style='padding: 0 10px;text-align: left;'>".DBout(getSingleColumn("note","select * from `event_rsvp_close` where `event_id`='$event_id'"))."</div>";
		}
		else{
		
			if($msg!='Error: Try again later' && $msg!='Error: You already submitted RSVP for this event' && $msgs=='yes'){
				echo '<div style=" padding: 14px 10px 0; text-align: left;">'.$msg.'</div>';
			}
			else{
				echo '<span style="color:#ff0000">'.$msg.'</span>';
			?>
          <div class="formfield">
            <label for="firstname">First name:</label>
            <input type="text" name="firstname" value="<?php echo $fname_rsvp; ?>" id="firstname" />
          </div>
          <div class="formfield">
            <label for="lastname">Last name:</label>
            <input type="text" name="lastname" value="<?php echo $lname_rsvp; ?>" id="lastname" />
          </div>
          <div class="formfield">
            <label for="email">Email address:</label>
            <input type="text" name="email" value="<?php echo $email_rsvp; ?>" id="email" />
          </div>
          <div class="formfield">
            <label for="email">How did you hear of this Event:</label>
            <select id="how_did" name="how_did" onChange="if($(this).val()=='Other'){$('#how_other').show();}else{$('#how_other').hide();}">
              <option value="The EventGrabber.com Website" <?php if ($how_did_rsvp=='The EventGrabber.com Website'){ echo 'selected="selected"'; } ?>>The EventGrabber.com Website</option>
              <option value="RYSE Magazine"<?php if ($how_did_rsvp=='RYSE Magazine'){ echo 'selected="selected"'; } ?>>RYSE Magazine</option>
              <option value="STG"<?php if ($how_did_rsvp=='STG'){ echo 'selected="selected"'; } ?>>STG</option>
              <option value="Member"<?php if ($how_did_rsvp=='Member'){ echo 'selected="selected"'; } ?>>Member</option>
              <option value="A Friend"<?php if ($how_did_rsvp=='A Friend'){ echo 'selected="selected"'; } ?>>A Friend</option>
              <option value="Facebook"<?php if ($how_did_rsvp=='Facebook'){ echo 'selected="selected"'; } ?>>Facebook</option>
              <option value="Twitter"<?php if ($how_did_rsvp=='Twitter'){ echo 'selected="selected"'; } ?>>Twitter</option>
              <option value="A Newspaper/Magazine Article"<?php if ($how_did_rsvp=='A Newspaper/Magazine Article'){ echo 'selected="selected"'; } ?>>A Newspaper/Magazine Article</option>
              <option value="Other"<?php if ($how=='Other'){ echo 'selected="selected"'; } ?>>Other</option>
            </select>
            <br />
            <input type="text" id="how_other" name="how_other" <?php if ($how!='Other'){ echo 'style="display:none;"  value="Type here"';} else{ echo 'value="'.$how_did_rsvp.'"';}?> onFocus="if(this.value=='Type here'){this.value='';}" onBlur="if(this.value==''){this.value='Type here';}" />
          </div>
          <input type="hidden" name="event_id" id="event_id" value="<?php echo $event_id; ?>" />
          <div align="center" style="padding:10px 0" id="rsvp_submit">
            <input type="button" name="submit_rsvp" value="RSVP" class="submit_btn" onClick="return checkRsvp();" align="left" />
          </div>
		  <?php
		  }
		} // end else (if RSVP not close)
		  ?>
		  
		  
        </div>
      </div>
    </div>
  </div>
</div>