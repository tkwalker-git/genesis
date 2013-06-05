<?php

require_once("database.php"); 
require_once("header.php"); 




if(isset($_POST['by_event_id'])){
	$event_id = $_POST['event_id'];
/*	echo "<script>window.location.href='../load_xls.php?event_id=".$event_id."&type=ticket'</script>"; */
	echo "<script>window.location.href='sold_report.php?event_id=".$event_id."'</script>";
	
}


if(isset($_POST['by_date'])){
	$dateFrom	= date('Y-m-d', strtotime(str_replace('-','/', $_POST['dateFrom'])));
	$dateTo		= date('Y-m-d', strtotime(str_replace('-','/', $_POST['dateTo'])));
	echo "<script>window.location.href='sold_report.php?type=date&dateFrom=".$dateFrom."&dateTo=".$dateTo."'</script>";
}
?>
<script>
function checkDate(){
	if($('#valid_from').val()==''){
		alert("Please enter date from");
		$('#valid_from').focus();
		return false;
	}

	if($('#expiration_date').val()==''){
		alert("Please enter date to");
		$('#expiration_date').focus();
		return false;
	}
}

function checkEventId(){
	if($('#event_id').val()==''){
		alert("Please enter Event Id");
		$('#event_id').focus();
		return false;
	}
}

</script>

<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">

<tr class="bc_heading">
<td colspan="2" align="left" >Tickets Report</td>
</tr>
<tr><td colspan="2" align="center" >&nbsp;</td></tr>
<?php if ($sucMessage) { ?>
<tr>
<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
</tr>
<?php } ?>

<tr>
<td colspan="2" valign="top" align="center"><h2>By Date</h2>
<form method="post" onsubmit="return checkDate();">
	<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0" style="padding:10px; background:#efefef; box-shadow: 0 0 5px 5px #C3C3C3;">
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        <tr>
          <td width="8">&nbsp;</td>
          <td width="135">Date Form:</td>
          <td width="182"><input name="dateFrom" size="15" id="valid_from" readonly="readonly" value="<?php echo $_POST['dateFrom'];?>" class="dateFrom" />
          </td>
          <td width="103">Date To:</td>
          <td width="253">
		  <input name="dateTo" size="15" id="expiration_date" readonly="readonly" value="<?php echo $_POST['dateTo']; ?>" class="dateTo"  />
          </td>
		  </tr>
		  <tr>
          <td  colspan="7" align="center"><br /><br /><input type="submit" name="by_date" value="Export" />
          </td>
        </tr>
      </table>
	</form>
	 </td>
</tr>

<tr>
	<td colspan="2" align="center">
<br />
<br />

		<h2>By Event Id</h2>
		<form method="post" onsubmit="return checkEventId();">
	<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0" style="padding:10px; background:#efefef; box-shadow: 0 0 5px 5px #C3C3C3;">
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        <tr>
          <td width="135" align="right">Event Id: &nbsp; &nbsp; </td>
          <td width="182"><input name="event_id" size="15" onKeyUp="extractNumber(this,2,true);" id="event_id" value="" class="" />
          </td>
          
		  </tr>
		  <tr>
          <td  colspan="7" align="center"><br /><br /><input type="submit" name="by_event_id" value="Export" />
          </td>
        </tr>
      </table>
	 </form>
	</td>
</tr>
</table>
<?php 
	require_once("footer.php"); 
?>