<?php

$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

?>
	  <div class="dash_menu">
        <table cellpadding="10" cellspacing="0" align="center" width="100%">
          <tr>
		  <?php if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){?>

            <td  align="center" valign="middle" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>dashboard.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_dashboard.png" /><br />
					Dashboard
				</a>
			</td>

            <td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>clinic_manager.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_manager.png" /><br />
					Clinic Manager
				</a>
			</td>

			<td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>my_calendar.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_calander.png" /><br />
					Calendar
				</a>
			</td>


            <td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>marketing_manager.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_promote.png" /><br />
					Marketing
				</a>
			</td>



            <td valign="bottom" class="bordr">
					<a href="<?php echo ABSOLUTE_PATH; ?>emr_manager.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_create_Event.png" /><br />
					EHR
				</a>
			</td>


			 <td valign="bottom" class="bordr">

<?php

					$clinicID = getSingleColumn('clinicid',"select * from users where id=" . $member_id);

?>
					<a target="_blank" href="<?php echo ABSOLUTE_PATH; ?>simulation.php?clinicid=<?php echo $clinicID;?>">
					<img src="<?php echo IMAGE_PATH; ?>sim.png" /><br />
					Simulation
				</a>
			</td>

<td valign="bottom">
				<a href="<?php echo ABSOLUTE_PATH; ?>settings.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_settings.png" /><br />
					Settings
				</a>
			</td>
<?php  }elseif($_SESSION['usertype']=='patient') {?>

			<td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>dashboard.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_dashboard.png" /><br />
					Dashboard
				</a>
			</td>
			<td valign="bottom" class="bordr" style="display:none;">


<?php

$sql = "select * from `patients` where  `id`='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";

$res = mysql_query($sql);
$i=0;
$bg = "ffffff";
$row = mysql_fetch_array($res);
?>

				<a href="patient-portal.php?id=<?php echo $row['id'];?>">
					<img src="<?php echo IMAGE_PATH; ?>icon_manager.png" /><br />
					My Health
				</a>
			</td>
			<!--
<td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>patient_manager.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_create_Event.png" /><br />
					Patient Manager
				</a>
			</td>
-->
			<td valign="bottom" class="bordr" style="display:none;">
				<a href="<?php echo ABSOLUTE_PATH; ?>patient_calendar.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_calander.png" /><br />
					Calendar
				</a>
			</td>

            <?php
			$subscription_name = attribValue("subsc_packages" , "name" , "where id = '". $row["subscription_type"] ."'");
			if($row["subscription_type"] > 0){
				if($row["subscription_type"] == 1){

			?>

            <td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>blood_gluco.php">
					<img src="<?php echo IMAGE_PATH; ?>blood-glucose.png" /><br />
					<?php echo $subscription_name; ?>
				</a>
			</td>

			<td valign="bottom">
				<a href="<?php echo ABSOLUTE_PATH; ?>settings.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_settings.png" /><br />
					Settings
				</a>
			</td>
            <?php }else if($row["subscription_type"] == 2){ ?>

            <td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>pt_inr.php">
					<img src="<?php echo IMAGE_PATH; ?>pt-inr.png" /><br />
					<?php echo $subscription_name; ?>
				</a>
			</td>

			<td valign="bottom">
				<a href="<?php echo ABSOLUTE_PATH; ?>settings.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_settings.png" /><br />
					Settings
				</a>
			</td>

		  <?php }else if($row["subscription_type"] == 3){
		  	$subscription_name1 = attribValue("subsc_packages" , "name" , "where id = '1'");
			$subscription_name2 = attribValue("subsc_packages" , "name" , "where id = '2'");
		  ?>

          	<td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>blood_gluco.php">
					<img src="<?php echo IMAGE_PATH; ?>blood-glucose.png" /><br />
					<?php echo $subscription_name1; ?>
				</a>
			</td>

            <td valign="bottom" class="bordr">
				<a href="<?php echo ABSOLUTE_PATH; ?>pt_inr.php">
					<img src="<?php echo IMAGE_PATH; ?>pt-inr.png" /><br />
					<?php echo $subscription_name2; ?>
				</a>
			</td>

			 <td valign="bottom" <?php if($row["subscription_type"] != 0){ ?> class="bordr" <?php } ?>>
				<a href="<?php echo ABSOLUTE_PATH; ?>search_doctor.php">
					<img src="<?php echo IMAGE_PATH; ?>doctor-search.png" /><br />
					Search Doctor
				</a>
			</td>

			<td valign="bottom">
				<a href="<?php echo ABSOLUTE_PATH; ?>settings.php">
					<img src="<?php echo IMAGE_PATH; ?>icon_settings.png" /><br />
					Settings
				</a>
			</td>

          <?php } ?>

          <?php } ?>

 <?php }?>
          </tr>
        </table>
      </div>