	<style>
	strong{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	}
	</style>	
								<div class="yellow_bar">
									<table cellpadding="0"  cellspacing="0" width="99%" align="center">
										<tr>
											<td width="25%">Patient Profile</td>
											<td width="30%">Current Plan</td>
											<td width="35%">Patient Payments</td>
											<td width="10%" align="center">Actions</td>
										</tr>
									</table>
								</div> <!-- /yellow_bar -->

								<?php
								$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
								$today=date("Y-m-d");
								
								include("page_form.php");
																
								$qry2 = "select * from schedule_dates where cons_date='$today' && clinic_id='$member_id' order by start_time";

								$rest123 = mysql_query($qry2);
								$totl_records=mysql_num_rows($rest123);

								$lim_record=10;		// records per page

								$total_pages=ceil($totl_records/$lim_record);     // ceil rounds to ceil number(4.2 to 5)
								$page_num=0; 
								if(isset($_REQUEST['page']))
								{
									$page_num=$_REQUEST['page']; // from pagination_interface.php file..........
								}
								else
								{
									$page_num=1;
								}
								if($page_num==1)
								{
									$start_record=0;  // As we know In mysql database records index starts from 0 
								}
								else
								{
									$start_record= $page_num*$lim_record - $lim_record;
								}
								$sql = "select * from schedule_dates where cons_date='$today' && clinic_id='$member_id' order by start_time LIMIT $start_record,$lim_record";

								$res = mysql_query($sql);
								$i=0;
								$bg = "ffffff";
								while($row = mysql_fetch_array($res)){
									$patient_id		= $row['patient_id'];
									$start_time		= $row['start_time'];
									$cons_date		= $row['cons_date'];
									$patient_fname	= getSingleColumn("firstname","select * from `patients` where `id`='$patient_id'");	
									$patient_lname	= getSingleColumn("lastname","select * from `patients` where `id`='$patient_id'");	
									$patient_sex	= getSingleColumn("sex","select * from `patients` where `id`='$patient_id'");	
									$patient_dob	= getSingleColumn("dob","select * from `patients` where `id`='$patient_id'");	
									$patient_name	= $patient_fname." ".	$patient_lname;
									
									$birthDate = explode("-", $patient_dob);
									
									$patient_age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));		
									
									
								if($bg=='ffffff')
									$bg='f6f6f6';
								else
									$bg = "ffffff";
								
								?>
								<div class="ev_eventBox" style="background:#<?php echo $bg; ?>">
									<table cellpadding="0" cellspacing="0" width="99%" align="center">
										<tr>						  
										  
										  <td width="25%" class="event_info" valign="top">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td width="27%" valign="top"><strong>NAME:</strong></td>
														<td width="73%"><span><?php echo $patient_name; ?></span></td>
													</tr>
													<tr>
														<td><strong>AGE:</strong></td>
														<td><span> <?php echo $patient_age; ?> </span></td>
													</tr>
													<tr>
														<td><strong>SEX:</strong></td>
														<td><span> <?php echo $patient_sex; ?> </span></td>
													</tr>
													
											  </table>											  											  
											 
											<!--
<br />
											<div class="event_name">Next Consultation:  
											<?php if ($row['event_status'] == 1){?>
												<span style="color:#289701">Active </span>
											<?php }
											else{?>
												<span style="color:#a80233">Not Active</span>
											<?php }

											if($row['type']=='draft'){?>
												<font color="red"> (Draft)</font>
											<?php } ?>
											
											<a href="<?php echo ABSOLUTE_PATH; ?>create_event.php?type=private">(Create New Appt)</a>
											</div> 
-->
											  
										  </td>
										  
										
										  
										  
										  
											<td width="30%" class="event_info" valign="top">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td width="40%" valign="top"><strong>PROTOCOL1:</strong></td>
														<td width="60%"><span><?php   ?></span></td>
													</tr>
													<tr>
														<td><strong>PROTOCOL2:</strong></td>
														<td><span><?php   ?></span>
														</td>
													</tr>
													<tr>
														<td><strong>PROTOCOL3:</strong></td>
														<td><span><?php   ?></span></td>
													</tr>
													
											  </table>
										  </td>
										  
										  
											
										<td width="35%" class="event_info" valign="top">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td width="50%" valign="top"><strong>VIEW PATIENT PLAN:</strong></td>
														<td width="50%"><span><?php   ?></span></td>
													</tr>
													<tr>
														<td><strong>VIEW ASSESSMENT:</strong></td>
														<td><?php   ?></td>
													</tr>
													<tr>
														<td><strong>VIEW REPORTS:</strong></td>
														<td><?php   ?></td>
													</tr>
													
											  </table>
										  </td>	
											
											
										<td width="10%" class="event_info" valign="top">&nbsp;
												
										  </td>											
											
										</tr>
										<tr>
										<td height="7" colspan="4"></td>
										</tr>
										<tr>
										<td height="7" colspan="4" style="padding-left:20px;">
										<?php 
										$tt=explode(':',$start_time); 
										$ss=$tt[0];
										if($ss >=12){
										 $timeapp=($ss%12).":".$tt[1].":".$tt[2]." "."PM";
										}else {
										 $timeapp=$start_time." "."AM";
										}
										
										if($cons_date==$today){
										$app_date="Today";
										}else {
										$app_date=$cons_date;
										}
										?>
										<strong style="margin-right:8px;">Next APP</strong><span><?php echo $app_date; ?> &nbsp;<?php echo $timeapp;  ?></span></td>
										</tr>
									</table>
								</div>
								<?php }?>
								<div align="center">
								<br />
									<?php include("pagination_interface.php"); ?>
								</div>