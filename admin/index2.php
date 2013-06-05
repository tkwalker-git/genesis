<?php

require_once("database.php");

require_once("header.php");

?>
<script language="JavaScript" type="text/javascript" src="js/cal2.js"></script>

<script>
function check(){
	if($('#valid_from').val()==''){
		alert("Please select date form");
		$('#valid_from').focus();
		return false;
		}

	if($('#expiration_date').val()==''){
		alert("Please select date to");
		$('#expiration_date').focus();
		return false;
		}
}
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
  <tr class="bc_heading">
    <td height="10" colspan="2">Select Action From The Left Menu</td>
  </tr>
  <tr>
    <td valign="top" width="70%"><br />
<br />
	<form method="post" onsubmit="return check();">
		<table width="95%" align="center" border="0" cellspacing="0" cellpadding="0" style="padding:10px; background:#efefef; box-shadow: 0 0 5px 5px #C3C3C3;">
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
          <td width="76"><input type="submit" name="submit" value="Submit" />
          </td>
        </tr>
         <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
      </table>
	 </form> 
	 <br />
<br />
</td>
<td><br />
		<div style="border:#e5e5e5 solid 1px; padding:5px; float:right; line-height:20px; margin-right:50px;">
			<strong>A</strong> = Members<br>
			<strong>B</strong> = Promoters<br>
			<strong>C</strong> = Members Events<br>
			<strong>D</strong> = Promoters Events<br>
			<strong>E</strong> = Active Events<br>
			<strong>F</strong> = Sold Tickets<br>
		</div>
	</td>
  </tr>
<!--<tr>
	<td colspan="2">
		<div style="border:#e5e5e5 solid 1px; padding:5px; float:right; line-height:20px; margin-right:50px;">
			<strong>A</strong> = Members<br>
			<strong>B</strong> = Promoters<br>
			<strong>C</strong> = Members Events<br>
			<strong>D</strong> = Promoters Events<br>
			<strong>E</strong> = Active Events<br>
			<strong>F</strong> = Sold Tickets<br>
		</div>
	</td>
</tr>-->
<?php
		
		$firstOfMonth	= date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
		$today			= date("Y-m-d");
		
		if(date('D')!=='Mon')
			$thisWeek = date('Y-m-d',strtotime('last Monday'));
		else
			$thisWeek = date('Y-m-d');
		
		$SPM = date("Y-m-1", strtotime("-1 month") ) ;
		$startPrevMonth = date('Y-m-d', strtotime($SPM));
		$EPM = date("Y-m-t", strtotime("-1 month") ) ;
		$endPrevMonth = date('Y-m-d', strtotime($EPM));
?>
<tr>
	<td colspan="2" align="center">
<?php
	if($_POST['submit']){ ?>
	
		<img src="graph.php?dateFrom=<?php echo $_POST['dateFrom']; ?>&dateTo=<?php echo $_POST['dateTo'];?>&title=Search Result (<?php
		echo date('d M Y', strtotime($_POST['dateFrom']))." to ".date('d M Y', strtotime($_POST['dateTo']));?>)" />
<?php
	}
?>

	</td>
</tr>
  <tr>
    <td align="center" colspan="2"><br><br>
	<img src="graph2.php?dateFrom=<?php echo $firstOfMonth; ?>&dateTo=<?php echo $today;?>&title=This Month" />
	<img src="graph2.php?dateFrom=<?php echo $today; ?>&dateTo=<?php echo $today;?>&title=Today" />
	<img src="graph2.php?dateFrom=<?php echo $thisWeek; ?>&dateTo=<?php echo $today;?>&title=This Week" />
	<img src="graph2.php?dateFrom=<?php echo $startPrevMonth; ?>&dateTo=<?php echo $endPrevMonth;?>&title=Previous Month" />
	<img src="graph2.php?title=Total" />
	</td>
  </tr>
</table>
<?php require_once("footer.php"); ?>
