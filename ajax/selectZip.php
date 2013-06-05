<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
	
	$err	= $_GET['err'];
	

?>
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
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-khtml-border-radius: 5px;
	border-radius: 5px;
	border: 1px solid #CCCCCC;
    height: 20px;
	width:200px;
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
}?><br>
<br>
<br>
<br>
<br>

<div style="margin:auto; width:200px">
  <div class="roundedCorner" style="background:#f5f5f5; padding:15px; text-align:left; min-height:162px; max-height:515px; overflow:auto;  width: 324px;">
    <form method="post">
  
      <table cellpadding="0" cellspacing="0" width="90%" align="center">
	  <tr>
	  	<td><img src="<?php echo IMAGE_PATH; ?>logo_transparent.png" width="200" /></td>
	</tr>
        <tr>
          <td class="title3" style="text-align:left;padding:21px 0 5px 0" colspan="2">Please Enter Your Zipcode</td>
        </tr>
        <tr>
          <td width="" class="title2" align="left">
            <input type="text" name="zip" value="" id="zip">
		</td>
		<td>
			   <input type="image" src="<?php echo IMAGE_PATH; ?>continue_btn2.png" id="submit" name="addzip" value="Continue" onClick="if($('#zip').val()==''){ alert('Please enter Zipcode'); return false}">
            <input type="hidden" name="addzip" value="submit">
		</td>
        </tr>
		<tr>
			<td colspan="2"><br />
			<?php
			if($err==1){?>
				<div style="font-size:12px;">Sorry,this location is coming soon, <a href="<?php echo ABSOLUTE_PATH; ?>locations.php" style="color:#0066CC">click here</a> to see what cities we currently support</div>
			<?php } ?>
			</td>
		</tr>
        <tr>
			<td valign="bottom"colspan="2" height="40">
				<a href="javascript:void(0)" onClick="document.zipReqAll.submit();" style="color:#0066FF">Show me all events in the site</a>
			</td>
        </tr>
      </table>
    </form>
	<form method="post" name="zipReqAll">
		<input type="hidden" name="zipReq" value="all">
	</form>
  </div>
</div>
<!-- onclick="hideOverlayer(); -->