<?php
$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

	if(isset($_FILES["csv_file"]) && !empty($_FILES["csv_file"]["tmp_name"]) && $_FILES["csv_file"]["type"] == 'application/vnd.ms-excel'){
					$csv_file_name = $_FILES['csv_file']['name'];
					//move_uploaded_file($_FILES['csv_file']['tmp_name'], "patient_csv/".$csv_file_name);
					
					 $handle  = fopen($_FILES['csv_file']['tmp_name'], "r");
					 $ab=1; $ac=0; $alr=array(); $errin=array();
					while (($data = fgetcsv($handle)) !== FALSE) {	
					$em = $data[13];
					$check	=	mysql_query("select * from `patients` where `email`='$em'");
					$havech	= 	mysql_num_rows($check);
					
					if($ab != 1){
					if(!$havech){
					
					$dob_ch = date("Y-m-d",strtotime(str_replace("/","-",$data[6]))); 
					$st_id	= getSingleColumn("id","select * from `usstates` where `abv`='".$data[10]."'");		
						
						if($data[2] == "-1" || $data[3] == "-1"){
						
						$bc_genensys_user_id = addpatient('pangeafinal2',$data[5],$data[4],$data[7],$dob_ch,$data[8],$data[9],$data[10],$data[11],$data[12],$data[13],$data[0],$data[1]);
						
 $novi_id = get_novi_id('admin','Meghal123',1,$data[5],$data[4],$data[0],$data[13],$data[1]);
 if($novi_id != '' && $novi_id != 'nologin'){
$bc_novi_id	=	$novi_id;
}
 
 					if($bc_genensys_user_id != "-1" && $bc_novi_id != "-1"){
 
						 $pqry = "insert into `patients` set  `username`='".$data[0]."',`password`='".$data[1]."',`genensysuserid`='".$bc_genensys_user_id."',`novi_id`='".$bc_novi_id."',`clinicid`='".$member_id."',`lastname`='".$data[4]."',`firstname`='".$data[5]."',`dob`='".$dob_ch."',`sex`='".$data[7]."',`address`='".$data[8]."',`city`='".$data[9]."',`state`='".$st_id."',`zip`='".$data[11]."',`phone`='".$data[12]."',`email`='".$data[13]."',`enabled`='1'";
						
						
						}
						
						}else {
							 $pqry = "insert into `patients` set  `username`='".$data[0]."',`password`='".$data[1]."',`genensysuserid`='".$data[2]."',`novi_id`='".$data[3]."',`clinicid`='".$member_id."',`lastname`='".$data[4]."',`firstname`='".$data[5]."',`dob`='".$dob_ch."',`sex`='".$data[7]."',`address`='".$data[8]."',`city`='".$data[9]."',`state`='".$st_id."',`zip`='".$data[11]."',`phone`='".$data[12]."',`email`='".$data[13]."',`enabled`='1'";
							
							}							
						
						$res = mysql_query($pqry);
						
						if($res){
						$ac++;
						}else {
						$errin[]=$ab;
						}
						
					}
					else {
					
					$alr[]=$ab;
					}
					}
					$ab++;
					}
					
				}
				
				
				
				$ids="";
				 $alrexist	=	count($alr);
				if($alrexist >=1){
					foreach($alr as $key => $value){
					$ids=$ids.$value.",";
					}
				}
				
				$idsn="";
				 $nr	=	count($errin);
				if($nr >=1){
					foreach($errin as $key => $value){
					$idsn=$idsn.$value.",";
					}
				}
		
?>
                            	<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="left">
									
										<tr>
											<td style="padding-left:15px;">Upload Multiple Patients </td>
											
										</tr>
									</table>
								</div> <!-- /yellow_bar -->
						<div>
								<div style="width:400px; margin:50px auto;">
								<div style="color:#FF0000; padding-bottom:10px; text-align:center; font-family:Arial; font-size:12px;">
								<?php if($res){echo $ac." Patients successfully inserted";} ?><br />
								<?php if($alrexist >= 1){echo "Row number ".$ids." users Already exist";} ?><br />
								<?php if($nr >= 1){echo "EMR and Novi not Responding for these rows ".$idsn;} ?><br />
								</div>
								
								<form name="csv" method="post" action="" enctype="multipart/form-data">
								<strong>Select File</strong>&nbsp;&nbsp;<input type="file" name="csv_file" />&nbsp;&nbsp;<input type="submit" name="upload" value="Upload" />
								</form>
								<div align="center" style="padding-top:15px;"><strong><a style="text-decoration:underline;" href='<?php ABSOLUTE_PATH ?>patient_csv/sample.csv'>Download Sample Format for Uploading Multiple Patients</a></strong></div>
								</div>
								</div>
								

                               </div>