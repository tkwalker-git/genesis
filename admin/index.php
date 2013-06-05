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
</table>

<?php require_once("footer.php"); ?>