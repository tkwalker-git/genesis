<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	$err = $_SESSION['campaignErr'];
	$event_id = $_GET['event_id'];
	$res = mysql_query("select * from `events` where `id`='$event_id'");
	while($row = mysql_fetch_array($res)){
		if($row['start_campaign']!='' && $row['start_campaign']!='0000-00-00')
	  		$start	=  date('d-M-Y', strtotime($row['start_campaign']));
  		if($row['end_campaign']!='' && $row['end_campaign']!='0000-00-00')
			$end		=  date('d-M-Y', strtotime($row['end_campaign']));
  }
	
?>
<script src="/calendar/jquery.ui.core.js"></script>
<link rel="stylesheet" href="/calendar/jquery.ui.theme.css">
<link rel="stylesheet" href="/calendar/jquery.ui.datepicker.css">
<script type="text/javascript" src="/calendar/jquery.ui.datepicker.js"></script>
<script>
$(document).ready(function() {
	var dates = $("#campaign_start").datepicker({
		dateFormat: "dd-M-yy",
		changeMonth: true,
		changeYear: true	
	});

	var dates = $("#campaign_end").datepicker({
		dateFormat: "dd-M-yy",
		changeMonth: true,
		changeYear: true	
	});
});


$('#submit').click(function(){
	var campaign_s	= $('#campaign_start').val();
	var campaign_e	= $('#campaign_end').val();
	if(campaign_s==''){
		alert("Please select Start date");
		return false;
	}
	if(campaign_e==''){
		alert("Please select End date");
		return false;
	}
	$('#campaignss').val(campaign_s+'|'+campaign_e);
	$('#page-bg').css('display','none');
	$('#overlayer').css('display','none');
});

</script>
<style>
.roundedCorner
{
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	-khtml-border-radius: 10px;
	behavior:url(/border-radius.htc);
	border-radius: 10px;
	background-color:#FEFEFE;
}
.title3{
	color: #45BB96;
	float:none;
	font-weight:normal;
    font-size: 18px;
    text-align: center;
	}
.title2{
	color: #45BB96;
	float:none;
	font-weight:bold;
    font-size: 14px;
	}
.title2 input{
	margin-top:10px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-khtml-border-radius: 5px;
	border-radius: 5px;
	border: 1px solid #CCCCCC;
    height: 20px;
    padding: 2px;
	}
table td span{
	font-size:14px;
	cursor:pointer;
	}
table td span:hover{
	text-decoration:underline;
	}
</style>
<?php
if($start!='' && $end!=''){ ?>
<div style="cursor: pointer;margin-right: -26px;margin-top: 6px;position: absolute;right: 0;" title="Close">
	<img onclick="hideOverlayer();" src="<?php echo IMAGE_PATH;?>fileclose.png">
</div>
<?php
}?>
<div style="margin:auto; width:400px">
  <div class="roundedCorner" style="background:#f5f5f5; padding:35px 15px 15px 15px; text-align:left; min-height:240px; max-height:515px; overflow:auto; width:400px">
    <form method="post">
      <?php
  if($err){
	  echo '<span style="color:#ff0000">'.$err.'</span>';
  }  
  ?>
      <table cellpadding="0" cellspacing="0" width="80%" align="center">
        <tr>
          <td align="center" class="title3" colspan="2">Select your Campaign period<br>
            <br>
            <br>
          </td>
        </tr>
        <tr>
          <td width="222" class="title2">Start Date
            <input type="text" readonly="" name="campaign_start" value="<?php echo $start; ?>" id="campaign_start"></td>
          <td width="138" class="title2">End Date
            <input type="text" readonly="" name="campaign_end" value="<?php echo $end; ?>" id="campaign_end"></td>
        </tr>
        <tr>
          <td colspan="2" align="center" height="50"><br>
            <br>
            <input type="image" src="<?php echo IMAGE_PATH; ?>submit_btn.jpg" id="submit" name="addcampaign" value="submit">
            <input type="hidden" name="addcampaign" value="submit">
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<!-- onclick="hideOverlayer(); -->