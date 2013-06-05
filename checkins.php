
    <style type="text/css">
strong{
    font-size:11px;
    font-family:Arial, Helvetica, sans-serif;
    }
    </style>

    <div class="yellow_bar">
        <table cellpadding="0" cellspacing="0" width="99%" align="center">
            <tr>
                <td width="25%">Patient Profile</td>

                <td width="30%">Contact Info</td>

                <td width="25%">Reason for Visit</td>

                <td width="20%" align="center">Actions</td>
            </tr>
        </table>
    </div><!-- /yellow_bar -->
    <?php
                                    $member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
                                    $today=date("Y-m-d");
                                    
                                    include("page_form.php");
                                                                    
                                    $qry2 = "select * from schedule_dates_status where cons_date='$today' && clinic_id='$member_id' && status='2' order by start_time";

                                    $rest123 = mysql_query($qry2);
                                    $totl_records=mysql_num_rows($rest123);

                                    $lim_record=10;     // records per page

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
                                    $sql = "select * from schedule_dates_status where cons_date='$today' && status='2' && clinic_id='$member_id' order by start_time LIMIT $start_record,$lim_record";

                                    $res = mysql_query($sql);
                                    $i=0;
                                    $bg = "ffffff";
                                    while($row = mysql_fetch_array($res)){
                                        $patient_id     = $row['patient_id'];
                                        $start_time     = $row['start_time'];
                                        $cons_date      = $row['cons_date'];
                                        $patient_fname  = getSingleColumn("firstname","select * from `patients` where `id`='$patient_id'"); 
                                        $patient_lname  = getSingleColumn("lastname","select * from `patients` where `id`='$patient_id'");  
                                        $patient_sex    = getSingleColumn("sex","select * from `patients` where `id`='$patient_id'");   
                                        $patient_dob    = getSingleColumn("dob","select * from `patients` where `id`='$patient_id'");
                                        $patient_email  = getSingleColumn("email","select * from `patients` where `id`='$patient_id'");
                                        $patient_phone  = getSingleColumn("phone","select * from `patients` where `id`='$patient_id'"); 
										$patient_stat   = getSingleColumn("status","select * from schedule_dates where cons_date='$today' && clinic_id='$member_id'"); 
                                        $patient_name   = $patient_fname." ".   $patient_lname;
                                        
                                        $birthDate = explode("-", $patient_dob);
                                        
                                        $patient_age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));        
                                        
                                        
                                    if($bg=='ffffff')
                                        $bg='f6f6f6';
                                    else
                                        $bg = "ffffff";
                                    
                                    ?>

    <div class="ev_eventBox" style="background:#&lt;?php echo $bg; ?&gt;">
        <table cellpadding="0" cellspacing="0" width="99%" align="center">
            <tr>
                <td width="22%" class="event_info" valign="top">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <!--   <td width="73%"><strong><?php echo $patient_name; ?></strong></strong></td> -->
                            <a href="patient.php?id=<?php echo $row['patient_id'];?>"><?php echo $patient_name; ?></a>
                        </tr>

                        <tr>
                            <td><span><?php echo $patient_age; ?>&nbsp;yr old&nbsp;<?php echo $patient_sex; ?></span></td>
                        </tr>
                    </table>
                </td>

                <td width="34%" class="event_info" valign="top">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="25%" valign="top"><strong>PHONE:</strong></td>

                            <td width="75%"><span><?php echo $patient_phone; ?></span></td>
                        </tr>

                        <tr>
                            <td><strong>EMAIL:</strong></td>

                            <td><span><?php echo $patient_email; ?></span></td>
                        </tr>
                    </table>
                </td>
                
                <td width="25%" class="event_info" valign="top">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <!--   <td width="40%" valign="top"><strong>PHONE:</strong></td> -->

                            <td width="60%"><span><?php echo $patient_phone; ?></span></td>
                        </tr>

                   <!--
     <tr>
                            <td><strong>EMAIL:</strong></td>

                            <td><span><?php echo $patient_email; ?></span></td>
                        </tr>
-->
                    </table>
                </td>

                <td width="20%" class="event_info" valign="top">
                    <a href="<?php echo ABSOLUTE_PATH; ?>create_plan.php" style="color:#0066FF; font-size:12px;">Create Patient Plan</a><br>
                    <a href="<?php echo ABSOLUTE_PATH; ?>schedule_patients.php" style="color:#0066FF; font-size:12px;">Reschedule Visit</a><br>
                    
                    
                    
                 <!--    <a href="<?php echo ABSOLUTE_PATH; ?>schedule_patients.php" style="color:#0066FF; font-size:12px;">Schedule Appointment</a> -->

                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td></td>
                        </tr>

                        <tr>
                            <td></td>
                        </tr>

                        <tr>
                            <td></td>
                        </tr>
                    </table>
                </td>

                <td width="10%" class="event_info" valign="top">&nbsp;</td>
            </tr>

            <tr>
                <td height="7" colspan="4"></td>
            </tr>

            <tr>
                <td height="7" colspan="2" style="padding-left:20px;"><?php 
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
                                                        ?><br>
                <strong style="margin-right:20px;">Next APP</strong><span><?php echo $app_date; ?> &nbsp; at <?php echo $timeapp;  ?></span>
				
	
	</td><td colspan="2" style="vertical-align:bottom;">&nbsp;</td>
				
            </tr>
        </table>
    </div><?php }?>

    <div align="center">
        <br>
        <?php include("pagination_interface.php"); ?>
    </div>

