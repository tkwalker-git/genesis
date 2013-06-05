<?php

require_once('admin/database.php');
require_once('site_functions.php');
require_once('qrcode/qrlib.php'); 

$order_id = $_GET['order_id'];

echo generateTicketsPDF($order_id);

function generateTicketsPDF($order_id) {

	$sql1 = "select t.ticket_id,t.price,t.quantity,t.name as tname,t.email as temail,t.ticket_number,t.date,TIME_FORMAT( t.t_time, '%r' ) as t_time,p.* from order_tickets t, paymeny_info p where p.order_id = '". $order_id ."' AND p.order_id=t.order_id";
	$res1 = mysql_query($sql1);
	
	ob_start();
	
	while ( $rows1 = mysql_fetch_assoc($res1) ) {
		
		$ticket_package_id 	= $rows1['ticket_id'];
		$ticket_price		= $rows1['price'];
		$ticket_quantity 	= $rows1['quantity'];
		$ticket_date	 	= $rows1['date'];
		$ticket_time	 	= $rows1['t_time'];
		
		$tname			 	= $rows1['tname'];
		$temail			 	= $rows1['temail'];
		$tnumber		 	= str_pad($rows1['ticket_number'],8,"0",STR_PAD_LEFT);
		
		$fname			 	= $rows1['f_name'];
		$lname			 	= $rows1['l_name'];
		$address		 	= $rows1['address1'];
		$city			 	= $rows1['city'];
		$zip			 	= $rows1['zip'];
		
		$ticket_id 			= attribValue('event_ticket_price', 'ticket_id', "where id='$ticket_package_id'");
		$ticket_title		= attribValue('event_ticket_price', 'title', "where id='$ticket_package_id'");
		$event_id 			= attribValue('event_ticket', 'event_id', "where id='$ticket_id'");
		$event_name			= attribValue('events', 'event_name', "where id='$event_id'");
		$venue_id 			= attribValue('venue_events', 'venue_id', "where event_id='$event_id'");
		$venue_name			= attribValue('venues', 'venue_name', "where id='$venue_id'");
		$venue_addr			= attribValue('venues', 'venue_address', "where id='$venue_id'");
		$venue_city			= attribValue('venues', 'venue_city', "where id='$venue_id'");
		$image				= attribValue('events', 'event_image', "where id='$event_id'");
		
		$h = 115;
		$w = 92;
		
		$event_dateT		= getEventStartDateFB($event_id);
		
		if ( substr($image,0,7) != 'http://' && substr($image,0,8) != 'https://' ) {
		
			if ( $image != '' && file_exists(DOC_ROOT . 'event_images/th_' . $image ) ) {
				$fle = cropImage($image);
				$img = returnImage( ABSOLUTE_PATH . 'pdf/tc_' . $image,$w,$h );
				//$img = ' src="' . ABSOLUTE_PATH . 'pdf/tc_' . $image . '" ';
				$img = '<img align="left" '. $img .' />';	
			} else
				$img = '<img src="' . IMAGE_PATH . 'imgg.png" align="left" width="'. $w .'"/>';	
		} else {
			if ( $source != "EventFull")
				$img_params 	= returnImage($image,$w,$h);
			else {
				if ( strtolower(substr($image,-4,4)) != '.gif')
					$image = str_replace("/medium/","/large/",$image);
				$img_params		= 'src="'. $image .'" width="'. $w .'" height="' . $h .'"';	
			}	
			
			$img 			= '<img align="left" '. $img_params .' />';	
		}
		
		$qrCode = generateQRCode($tnumber);
		$qrCode = 'temp_qrcodes' . DIRECTORY_SEPARATOR . $qrCode;
	?>
	
	<table width="840" border="0" cellspacing="0" cellpadding="0" align="center" >
	<tr><td style="padding:10px; border:#ECECEC solid 1px" >
		<table width="820" border="0" cellspacing="0" cellpadding="0" align="center" >
		  <tr>
			<td width="380" align="left" valign="top">
				
				<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
				  <tr><td valign="bottom">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" >
						<tr>
						<td class="imag_ticket" valign="top" height="126">
							<div class="divInner" >
								<?php echo $img ;?>
							</div>	
						</td>
						
						<td align="left" valign="top" style="padding-left:10px">
							<span class="event_name"><?php echo $event_name ;?></span>
							<br>
							<span class="event_date"><?php echo date("M d, Y",strtotime($event_dateT[0]));?></span>
						</td>
						</tr></table>	
				  </td></tr>
				  <tr>
					<td align="left" class="ticket">e-Ticket</td>
				  </tr>
				  <tr><td align="left" class="ticket_sub">Admit one person only</td></tr>
				  <tr><td align="left" >
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td width="87%" class="ticket_sub1">Without ticket there is no guaranteed access.<br>
						Barcodes are unique. Copying is useless.</td>
						<td width="46"><img src="<?php echo  $qrCode;?>" width="46" height="46"></td>
					  </tr>
					</table>
		
				  </td></tr>
				</table>  
			</td>
			<td align="center" valign="top"><img src="images/barcode_vertical.jpg" width="34" height="264"></td>
			<td width="390" align="right" valign="top">
				<table width="95%" border="0" cellspacing="0" cellpadding="0" align="right">
				  <tr>
					<td align="center" >
						<!-- <img src="ticket_images/barcode.jpg" > -->
						<barcode code="<?php echo $tnumber;?>" type="C39E" class="barcode" />
						<br>
						<span style="font-size:18px;">eTicket # <strong><?php echo $tnumber;?></strong></span>
					</td>
				  </tr>
				  <tr>
					<td >&nbsp;</td>
				  </tr>
				  <tr>
					<td ><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td width="109" class="labels">Buyer:</td>
						<td width="275" class="labels"><?php echo $tname ;?></td>
					  </tr>
					  <tr>
						<td class="labels">Date:</td>
						<td class="labels"><?php echo $ticket_date ;?></td>
					  </tr>
					  <tr>
						<td class="labels">Venue:</td>
						<td class="labels"><?php echo $venue_name ;?></td>
					  </tr>
					  <tr>
						<td class="labels">Address:</td>
						<td class="labels"><?php echo $venue_addr . ' ' . $venue_city ;?></td>
					  </tr>
					  <tr>
						<td class="labels">Time:</td>
						<td class="labels"><?php echo $ticket_time; ?></td>
					  </tr>
					  <tr>
						<td class="labels">Price:</td>
						<td class="labels">$ <?php echo number_format($ticket_price,2) ;?></td>
					  </tr>
					  <tr>
						<td class="labels">Type:</td>
						<td class="labels"><?php echo $ticket_title;?></td>
					  </tr>
					</table></td>
				  </tr>
				</table>
			</td>
		  </tr>
		</table>
	</td></tr>
	<tr><td style="background-image:url(images/ticket_cutter.jpg); background-repeat:no-repeat; height:33px">&nbsp;</td></tr>
	</table>
	
	<?php
	
	}
	
	$html = ob_get_contents();
	ob_clean();
	
	$filename = 'eTicket_'. time() . '.pdf';
	
	include("mpdf/mpdf.php");
	
	$mpdf=new mPDF('c','A4-L');
	$mpdf->list_indent_first_level = 0;
	$stylesheet = file_get_contents('ticket_css.css');
	$mpdf->WriteHTML($stylesheet,1);
	$mpdf->WriteHTML($html);
	$mpdf->Output('pdf/'.$filename, 'F');
	
	if ( $fle != '' )
		@unlink($fle);
	@unlink($qrCode);
	return 'pdf/'.$filename;
}

function cropImage($bc_image)
{
	
	makeThumbnail($bc_image, 'event_images/', 'pdf/', 92, 150,'tic_');

	$filename = 'pdf/' . $bc_image;
	$filename1 = 'pdf/tc_' . $bc_image;
 
	list($current_width, $current_height) = getimagesize($filename);
	 
	$left = 0;
	$top = 0;
	 
	$crop_width = 92;
	$crop_height = 115;
	 
	$canvas 		= imagecreatetruecolor($crop_width, $crop_height);
	$current_image 	= imagecreatefromjpeg($filename);
	imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
	imagejpeg($canvas, $filename1, 100);
	@unlink($filename);
	return $filename1;
}

function generateQRCode($code)
{
	$PNG_TEMP_DIR = 'temp_qrcodes' . DIRECTORY_SEPARATOR;
	$file = time() . '.png';
	$filename = $PNG_TEMP_DIR . $file ;
	$errorCorrectionLevel = 'L'; //  L, M, Q, H
	$matrixPointSize = 5;
	
	//$filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
	QRcode::png($code, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
	
	return $file;
}

?>