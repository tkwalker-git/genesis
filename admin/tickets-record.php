<?php

require_once("database.php"); 
require_once("header.php");

?>

<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
  <tr class="bc_heading">
    <td colspan="2" align="left" >Tickets Records</td>
  </tr>
  <tr>
    <td colspan="2" align="center" >&nbsp;</td>
  </tr>
  <?php if ($sucMessage) { ?>
  <tr>
    <td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="2" valign="top">
		<table cellpadding="8" border="0" cellspacing="0" style="border:#3e342a solid 1px;" width="100%" align="center">
        <tr bgcolor="#3e342a" height="40" style="color:#fff; font-size:14px">
          <td width="31%"><strong>Event Name</strong></td>
          <td width="26%"><strong>Email</strong></td>
          <td width="27%"><strong>Order Date</strong></td>
          <td width="16%" align="center"><strong>Download</strong></td>
        </tr>
        <?php
	$sql = "select p.email, tr.file_name, o.main_ticket_id, o.date FROM paymeny_info p, tickets_record tr,  orders o where p.order_id=o.id && p.order_id=tr.order_id && p.email!='' GROUP BY tr.id ORDER BY o.date DESC";
	$res = mysql_query($sql);
	$bg	= '#efefef';
	while($row = mysql_fetch_array($res)){
	if($bg=='#efefef')
		$bg = '#ffffff';
	else
		$bg = '#efefef';

	$main_ticket_id	= $row['main_ticket_id'];
	$event_name		= getSingleColumn("event_name","select * from `events` where `id`='$main_ticket_id'");		
	?>
        <tr height="30" bgcolor="<?php echo $bg; ?>">
          <td><?php echo $event_name; ?></td>
          <td><?php echo $row['email'];?></td>
          <td><?php echo date ('d M Y', strtotime($row['date']));?></td>
          <td align="center"><a href="download.php?file=<?php echo base64_encode($row['file_name']);?>"><img src="images/icon_download.png" title="Download Tickets" /></a></td>
        </tr>
        <?php
	}
	?>
      </table></td>
  </tr>
</table>
<?php 
	require_once("footer.php"); 
?>