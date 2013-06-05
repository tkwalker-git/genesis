<?php

	

	require_once('../admin/database.php');

	require_once('../site_functions.php');

	

	$active='buy';



	$order_id	=	$_POST['order_id'];





	$res = mysql_query("select * from `orders` where `id`='$order_id'");

	while($row = mysql_fetch_array($res)){

		$total_price	= $row['total_price'];

		$date			= $row['date'];

		$ticket_id 		= $row['main_ticket_id'];	

		$user_id 		= $row['user_id'];

	}





	$res = mysql_query("select * from `users` where `id`='$user_id'");

	while($row = mysql_fetch_array($res)){

		$name			= $row['name'];

		$lname			= $row['lname'];

	}



	$event_id		= $_SESSION['event_id'];

	$event_image	= getSingleColumn('event_image',"select * from `events` where `id`='$event_id'");

	$event_name		= getSingleColumn('event_name',"select * from `events` where `id`='$event_id'");

	$address		= getSingleColumn('address',"select * from `billing_information` where `order_id`='$order_id'");

	

	$dowlodUrlPath		= getSingleColumn('file_name',"select * from `tickets_record` where `order_id`='$order_id'");

	

	$dowlodUrlPath = base64_encode($dowlodUrlPath);



	include("../flayerMenu.php");

	

	?>

<script>

$(document).ready(function(){

	$('#download').click(function(){

	window.open("/download.php?fn=<?php echo $dowlodUrlPath; ?>");

	});

});

</script>



<div class="inrDiv">

  <div class="progresbar4"></div>

  <br />

  <br />

  <table cellpadding="0" cellspacing="0" width="100%">

    <tr>

      <td colspan="3" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="85%" align="center">

          <tr>

            <td width="40%" valign="top"><div class="new_flayer_title"><?php echo $event_name; ?></div>

              <div class="new_flayer_date">Nov 19, 2011</div>

              <div class="e_tick_type">e-ticket</div>

              Admit one person only <small>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam cursus. Morbi ut mi.</small> </td>

            <td width="8%" align="center" valign="top"><img src="<?php echo IMAGE_PATH; ?>codeBar.gif" /></td>

            <td width="42%" valign="top"><table cellpadding="0" cellspacing="0" width="100%">

                <tr>

                  <td colspan="2" valign="top"><img src="<?php echo IMAGE_PATH; ?>codeBar2.gif" /></td>

                </tr>

                <tr>

                  <td colspan="2" valign="top"><small>

                    <table cellpadding="0" cellspacing="0" width="100%">

                      <tr>

                        <td width="26%">Buyer:</td>

                        <td width="74%"><?php echo $name." ".$lname; ?></td>

                      </tr>

                      <tr>

                        <td>Date:</td>

                        <td>Jan 13, 2011 </td>

                      </tr>

                      <tr>

                        <td>Venue:</td>

                        <td><?php $venue	= getEventLocations($event_id);

							  echo $venue[1]['venue_name']; ?></td>

                      </tr>

                      <tr>

                        <td>Address:</td>

                        <td><?php echo $address; ?></td>

                      </tr>

                      <tr>

                        <td>Time:</td>

                        <td>10 pm - 2 am</td>

                      </tr>

                      <tr>

                        <td>Price:</td>

                        <td>$<?php echo $total_price; ?></td>

                      </tr>

                      <tr>

                        <td>Type:</td>

                        <td>Normal</td>

                      </tr>

                    </table>

                    </small> </td>

                </tr>

              </table></td>

          </tr>

          <tr>

            <td colspan="3" align="center"><br />

              <?php

			echo	getFlayerImage($event_image,'','','403');

			?>

            </td>

          </tr>

        </table></td>

    </tr>

    <tr>

      <td colspan="4" align="center"><br />

        <img src="<?php echo IMAGE_PATH; ?>img3.gif" /><br />

        <img src="<?php echo IMAGE_PATH; ?>img4.gif" /><br />

        &nbsp; </td>

    </tr>

    <tr bgcolor="#e4f0d8">

      <td>&nbsp;</td>

      <td width="25%" colspan="2" align="right" valign="bottom" style="padding:5px"><img src="<?php echo IMAGE_PATH; ?>new_flayer_downloadButton.png" id="download" style="cursor:pointer" align="right" /> </td>

    </tr>

  </table>

  <div align="right"><br />

    <a href="<?php echo ABSOLUTE_PATH; ?>"><img src="<?php echo IMAGE_PATH; ?>powered_by.png" width="225" height="26" border="0" /></a></div>

</div>