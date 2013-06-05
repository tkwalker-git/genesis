<?php


if(isset($_GET["accept"])){

	$sql = "UPDATE `invitations` SET `status` = 1 WHERE `id` = '". $_GET["accept"] ."'";
	$res = mysql_query($sql);
	
	if($res){
	
		$err = "Invitation accepted successfully!";	
		
	}	
	
}

if(isset($_GET["reject"])){

	$sql = "DELETE FROM `invitations` WHERE `id` = '". $_GET["reject"] ."'";
	$res = mysql_query($sql);
	
	if($res){
	
		$err = "Invitation rejected successfully!";	
		
	}	
	
}

?>
<style>
.whiteMiddle .evField {
	
	}

.whiteMiddle .evField {
	text-align:left;
	font-size:15px;
	width:134px;
	}
	
.evLabal{
	font-size:15px;
	}
	
.evInput{
	font-size:14px;
	}
	
</style>

<style type="text/css">
.ew-heading{
	color: #49BA8D;
    font-size: 24px;}
	
.ew-heading a{
	color: #FF7A57;
    float: right;
    font-size: 14px;
	text-decoration:underline;}

.ew-heading-behind{
	color: #6EB432;
    font-size: 24px;}

.ew-heading-behind span{}

.ew-heading-a{
	color: #212121;
    font-size: 20px;}

</style>
    
    
<div class="yellow_bar"> &nbsp; CHECK YOU INVITATIONS</div>
<!-- /yellow_bar -->
<div style="padding:0 10px;"><br />
<div class="error"><?php echo $err; ?></div>
	

	<div class="ew-heading">Your invitation list is below.</div>
	
    <table width="100%" border="0" cellspacing="1" cellpadding="10" style="border: 1px solid #B6BEBB;">
    	<thead style="line-height:10px; background-color: #49BA8D;">
        	<tr>
        		<td><strong>Patient Name</strong></td>
            	<td width="22%" align="center"><strong>Action</strong></td>
            </tr>
        </thead>
        <tbody>
        	
       
      
    
	
    <?php 
	
		$sql = "SELECT * FROM `invitations` WHERE `doctor_id` = '". $member_id ."' AND `status` = 0";
		$res = mysql_query($sql);
		$cou = mysql_num_rows($res);
		
		if($cou > 0){
			$no = 1;
			while($pat_invi = mysql_fetch_assoc($res)){
				
				$patient_name = attribValue("patients" , "CONCAT(`firstname` , ' ' , `lastname`)" , "WHERE `id` = '". $pat_invi["patient_id"] ."'");
				
				if($no%2 == 0)
					$color = '#aee3ce';
				else
					$color = '#89c5ad';
				
	?>
    
    		<tr style="line-height:10px; background-color:<?php echo $color; ?>;">
            	<td><?php echo $patient_name; ?></td>
            	<td style="background-color:#fff">
                	<a href="<?php echo ABSOLUTE_PATH ?>patient_profile_for_doctors.php?id=<?php echo $pat_invi["id"]; ?>">View</a>&nbsp;&nbsp;|
                	<a href="<?php echo ABSOLUTE_PATH ?>settings.php?p=dr_invitations&accept=<?php echo $pat_invi["id"]; ?>">Accept</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo ABSOLUTE_PATH ?>settings.php?p=dr_invitations&reject=<?php echo $pat_invi["id"]; ?>">Reject</a>
                </td>
          	</tr>
    
    <?php $no++;}} ?>
    	 </tbody>
    </table>
    
    
    
</div>