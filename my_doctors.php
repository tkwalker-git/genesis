<?php

if(isset($_GET["remove"])){

	$sql = "DELETE FROM `invitations` WHERE `id` = '". $_GET["remove"] ."'";
	$res = mysql_query($sql);
	
	if($res){
	
		$err = "You have removed your caretaker successfully!";	
		
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
    
    
<div class="yellow_bar"> &nbsp; CHECK YOU CARETAKER</div>
<!-- /yellow_bar -->
<div style="padding:0 10px;"><br />
<div class="error"><?php echo $err; ?></div>
	

	<div class="ew-heading">Your caretaker list is below.</div>
	
    <table width="100%" border="0" cellspacing="1" cellpadding="10" style="border: 1px solid #B6BEBB;">
    	<thead style="line-height:10px; background-color: #49BA8D;">
        	<tr>
        		<td><strong>Patient Name</strong></td>
            	<td width="16%" align="center"><strong>Action</strong></td>
            </tr>
        </thead>
        <tbody>
        	
       
      
    
	
    <?php 
	
		$sql = "SELECT * FROM `invitations` WHERE `patient_id` = '". $member_id ."' AND `status` = 1";
		$res = mysql_query($sql);
		$cou = mysql_num_rows($res);
		
		if($cou > 0){
			$no = 1;
			while($pat_invi = mysql_fetch_assoc($res)){
				
				$doctor_name = attribValue("doctors" , "CONCAT(`first_name` , ' ' , `last_name`)" , "WHERE `id` = '". $pat_invi["doctor_id"] ."'");
				
				if($no%2 == 0)
					$color = '#aee3ce';
				else
					$color = '#89c5ad';
				
	?>
    
    		<tr style="line-height:10px; background-color:<?php echo $color; ?>;">
            	<td><?php echo $doctor_name; ?></td>
            	<td style="background-color:#fff">
                	<a href="<?php echo ABSOLUTE_PATH ?>doctor_profile.php?id=<?php echo $pat_invi["doctor_id"]; ?>">View</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo ABSOLUTE_PATH ?>settings.php?p=my_doctors&remove=<?php echo $pat_invi["id"]; ?>">Remove</a>
                </td>
          	</tr>
    
    <?php $no++;}} ?>
    	 </tbody>
    </table>
    
    
    
</div>