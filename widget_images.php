<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">My Files & Images</div> </td>
      </tr>
    </table>
	<br/>
	<div align="left" style="padding-bottom:5px;"><form name="img" method="post" action="" enctype="multipart/form-data">
	Name of File or Image: &nbsp;&nbsp;<input type="text" name="f_name" />&nbsp;&nbsp;
	Upload File or Image : &nbsp;&nbsp;<input type="file" name="p_img" />&nbsp;&nbsp;
	<input type="submit" name="upload" value="Upload" style="padding:2px 8px;" /></form></div>
	
	<div class="yellow_bar">
	<div style="text-align:center; color:#FF0000; font-size:12px; padding-bottom:10px; font-family:Arial;"><?php if($succ){echo "File Successfully deleted";} ?></div>
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>		
		<td width="40%" class="topleft"><strong>File Name</strong></td>
		<td width="25%" class="topleft"><strong>Date Added</strong></td>
		<td width="25%" class="topleft"><strong>View Link</strong></td>
		<td width="10%" class="topleftright"><strong>Action</strong></td>
		</tr>
		
		<?php
		
		$sql = "select * from patient_images where Patient_ID='".$_GET['id']."' && clinic_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."' order by Added_On desc";
		$res = mysql_query($sql);
		if($res)
		{
			if(mysql_num_rows($res)>0)
			while($row = mysql_fetch_array($res))
			{?>
			<tr>
			<td class="botleft"><?php echo  $row['File_name']; ?></td>
			<td class="botleft"><?php echo date("M d, Y",strtotime($row['Added_On'])); ?></td>
			<td class="botleft"><a style="text-decoration:underline;" class="fancybox" rel="group" href="<?php echo ABSOLUTE_PATH."patient_images/".$row['File_name']; ?>">View File</a></td>
			<td class="botleftright"><a style="text-decoration:underline;" href="patient.php?id=<?php echo $_GET['id']; ?>&type=images&del=<?php echo $row['ID'];  ?>">Delete</a></td>
			</tr>
			<?php }
		}
		?>
	</table>
		
		
		
		
		
		
		
		
		
	
	</div>
  </div>
  <div class="blockerBottom"></div>
</div>


<!--end center_contents-->
<div class="clr"></div>
 <!--end blocker-->