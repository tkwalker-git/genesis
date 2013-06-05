<?php 
if($_GET['id'] && $_GET['delimg']){
$delqry="delete from patient_images where Patient_ID='".$_GET['id']."' && ID='".$_GET['delimg']."'";
mysql_query($delqry);
}
?>
<div class="blocker">
  <div class="blockerTop"></div>
  <!--end blockerTop-->
  <div class="blockerRepeat">
 <?php  $pstatus = getSingleColumn("status","select * from `patients` where `id`='".$_SESSION['LOGGEDIN_MEMBER_ID']."'"); 
 $pdo = getSingleColumn("clinicid","select * from `patients` where `id`='".$_SESSION['LOGGEDIN_MEMBER_ID']."'");
?>
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">My Files & Images</div> </td>
      </tr>
    </table>
	<br/>
	
	
	<div align="right" style="padding-bottom:5px; display:none;"><form name="img" method="post" action="" enctype="multipart/form-data">
	File Name : &nbsp;&nbsp;<input type="text" name="f_name" />&nbsp;&nbsp;
	Image : &nbsp;&nbsp;<input type="file" name="p_img" />&nbsp;&nbsp;
	<input type="submit" name="upload" value="Upload" style="padding:2px 8px;" /></form></div>
	<div class="yellow_bar">
	
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>		
		<td width="40%" class="topleft"><strong>File Name</strong></td>
		<td width="25%" class="topleft"><strong>Date Added</strong></td>
		<td width="25%" class="topleft"><strong>View Link</strong></td>
		<td width="10%" class="topleftright"><strong>Action</strong></td>
		</tr>
		
		<?php
		
		
		if($pstatus==4){
		$sql = "select * from patient_images where Patient_ID='".$_SESSION['LOGGEDIN_MEMBER_ID']."' && status='1' && clinic_id='".$pdo."' order by Added_On desc";
		}else {
		$sql = "select * from patient_images where Patient_ID='".$_SESSION['LOGGEDIN_MEMBER_ID']."' && status='0' && clinic_id='".$pdo."' order by Added_On desc";
		}
		
		
		
		
		$res = mysql_query($sql);
		if($res)
		{
			if(mysql_num_rows($res)>0)
			while($row = mysql_fetch_array($res))
			{?>
			<tr>
			<td class="botleft"><?php echo  $row['File_name']; ?></td>
			<td class="botleft"><?php echo $row['Added_On']; ?></td>
			<td class="botleft"><a style="text-decoration:underline;" class="fancybox" rel="group" href="<?php echo ABSOLUTE_PATH."patient_images/".$row['File_name']; ?>">View File</a></td>
			<td class="botleftright"><a style="text-decoration:underline;" href="patient.php?id=<?php echo $_GET['id']; ?>&type=images&del=<?php echo $row['ID'];  ?>">Delete</a></td>
			</tr>
			<?php }
		}
		?>
	</table>
		
		
		
		<br /><br />
		<?php  if($pstatus==4){
 ?>
  <table cellspacing="0" cellpadding="0" border="0" width="100%"> 
      <tr>
        <td align="left" height="" valign="top" style="font-size:13px" ><div class="ew-heading">Notes</div> </td>
      </tr>
	  </table>
	<br />
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="topleft" width="20%"><strong>Comment Date</strong></td>
			<td class="topleftright"><strong>Comment </strong></td>
		<!-- 	<td class="topleftright"><strong>Notes</strong></td> -->
		</tr>
		<?php
			 $sqlt="select * from patient_comments where patient_id='".$_SESSION['LOGGEDIN_MEMBER_ID']."' && clinic_id='".$pdo."' && status != 1";
			$dfre=mysql_query($sqlt);
			while($get_co_id=mysql_fetch_array($dfre)){?>
			
					 
					
					
					<tr>
					<td class="botleft"><?php echo $get_co_id['comment_date']; ?></td>
					<td class="botleftright"><?php echo $get_co_id['comment']; ?></td>
					</tr>					
					<!-- <tr><td class="botleftright"><?php echo $get_co_id['comment']; ?></td></tr> -->
					<?php } 	?>
	</table>



<br />
<?php  } ?>
		
		
		
		
		
	
	</div>
	
  </div>
  <div class="blockerBottom"></div>
</div>


<!--end center_contents-->
<div class="clr"></div>
 <!--end blocker-->