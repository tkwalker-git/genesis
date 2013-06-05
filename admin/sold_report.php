<?php

require_once("database.php"); 

$type		= $_GET['type'];
$event_id	= $_GET['event_id'];

if($type=='member'){
$member_id	= $_GET['id'];
	$res = mysql_query("select * from `events` where `userid`='$member_id' && `event_type`!=0");
	if(mysql_num_rows($res)){
		while($row = mysql_fetch_array($res)){
			$event_id	= $row['id'];
			$event_name	= $row['event_name'];
			$res_order = mysql_query("select * from `orders` where `main_ticket_id`='$event_id' && `total_price`!='' && `type`='ticket'");
			if(mysql_num_rows($res_order)){
				$header = "No.,";
				$header.= "Ticket #,";
				$header.= "Name,";
				$header.= "Email,";
				$header.= "Ticket Type,";
				$header.= "Ticket Price,";
				$header.= "Promoter Fee,";
				$header.= "Customer Fee,";
				$header.= "Qty,";
				$header.= "Purchase Date,";
				$header.= "Purchase Price,";
				$header.= "Event Name,\n";
				
			
				$i=0;
				$data	= '';
				$line	= '';
				while($row_order=mysql_fetch_array($res_order)){
					$order_id	= $row_order['id'];
					$resTicket	= mysql_query("select * from `order_tickets` where `order_id`='$order_id'");
					while($rowTicket=mysql_fetch_array($resTicket)){
						$i++;
						$line.= $i.",";
						$line.= str_pad($rowTicket['ticket_number'],8,"0",STR_PAD_LEFT).",";
						$line.= $rowTicket['name'].",";
						$line.= $rowTicket['email'].",";
						
						$ticket_id	= $rowTicket['ticket_id'];
						
						$line.= $rowTicket['product_name'].",";
						
						$price		= $rowTicket['price'];
						if($price)
							$line.= "$".$price.",";
						else
							$line.= ",";
						
						$ticket_id_main		= getSingleColumn('ticket_id',"select * from event_ticket_price where id=" . $ticket_id);	
						if($ticket_id_main){
							$prometer		= getSingleColumn('prometer_event_grabber_fee',"select * from event_ticket where id=" . $ticket_id_main);
							$prometer_service		=	$price*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
							$prometer_service_fee	=	$prometer_service*$prometer/100;
							$line.= "$".$prometer_service_fee.",";
							
							$buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where id=" . $ticket_id_main);
							$buyer_service		=	$price*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
							$buyer_service_fee	=	$buyer_service*$buyer/100;
							$line.= "$".$buyer_service_fee.",";
						}
						else{
							$line.= ",,";
							}
						
						$line.= $rowTicket['quantity'].",";
						$line.= date('d M Y', strtotime($row_order['date'])).",";
						$line.= $event_name.",";
						$data.= trim( $line ) . "\n";
						$line = '';
					
					}
				}
			
			}
		}
		
		$data = str_replace( "\r" , "" , $data );
								
		if ( $data == "" )
		{
			$data = "\n(0) Records Found!\n";                        
		}
		
	
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=Purchased".time().".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		print "$header\n$data";
	}
	else{
		echo "<strong>Not Found</strong>";
		}
} // end if($type=='member')

if($type=='date' || $event_id){

if($type=='date'){
	$dateFrom	= $_GET['dateFrom'];
	$dateTo		= $_GET['dateTo'];
	$res_order = mysql_query("select * from `orders` where (`date` between '$dateFrom' and '$dateTo')  && `total_price`!='' && `type`='ticket'");
	}
elseif($event_id){
	$res_order = mysql_query("select * from `orders` where `main_ticket_id`='$event_id' && `total_price`!='' && `type`='ticket'");
	}
else{
	die('Error: Try again later.');
	}
			if(mysql_num_rows($res_order)){
				
				$header = "No.,";
				$header.= "Ticket #,";
				$header.= "Name,";
				$header.= "Email,";
				$header.= "Ticket Type,";
				$header.= "Ticket Price,";
				$header.= "Promoter Fee,";
				$header.= "Customer Fee,";				
				$header.= "Qty,";
				$header.= "Purchase Date,";
				$header.= "Event Name,";
				$header.= "Price,\n";
			
				$i=0;
				$data	= '';
				$line	= '';
				while($row_order=mysql_fetch_array($res_order)){
					$order_id	= $row_order['id'];
					$discount	= $row_order['discount'];
					$discounts	= $discounts + $discount;
					$event_id	= $row_order['main_ticket_id'];
					$event_name	= getSingleColumn("event_name","select * from `events` where `id`='$event_id'");
					
					$resTicket	= mysql_query("select * from `order_tickets` where `order_id`='$order_id'");
					$z=0;
					while($rowTicket=mysql_fetch_array($resTicket)){
						$z++;
						$i++;
						$line.= $i.",";
						$line.= str_pad($rowTicket['ticket_number'],8,"0",STR_PAD_LEFT).",";
						$line.= $rowTicket['name'].",";
						$line.= $rowTicket['email'].",";
						
						$ticket_id	= $rowTicket['ticket_id'];
						
						$line.= $rowTicket['product_name'].",";
						
						$price		= $rowTicket['price'];
						if($price)
							$line.= "$".number_format($price,2).",";
						else
							$line.= ",";
							
						
						$buyer_service_fee	= $rowTicket['buyer_fee'];
						$promtrFree			= $rowTicket['total_fee']-$buyer_service_fee;
						$promoterTot		= $promtrFree+$promoterTot;
						
						if($rowTicket['total_fee']){
							
							$line.= "$".number_format($rowTicket['total_fee'] - $buyer_service_fee,2).",";
							$line.= "$".number_format($buyer_service_fee,2).",";
						}
						else{
							$line.= ",,";
							}
							
					$tPrice		= number_format($buyer_service_fee+$price,2);
					
					$netTotal	= $price+$netTotal;
						
						$line.= $rowTicket['quantity'].",";
						$line.= date('d M Y', strtotime($row_order['date'])).",";
						$line.= "$".number_format($tPrice-$discount,2).",";
						$data.= trim( $line ) . "\n";
						$buyer_service_fee	= 0 ;
						
						if($discount!='' && $discount!=0 && mysql_num_rows($resTicket)==$z){
					//		$data.= ",,,,,,,,,Discount,$".number_format($discount,2)."" . "\n\n";
							}
						
						$line='';
					}
				}
				
				$gross = $netTotal;
				
				if($discounts)
					$netSales = $gross-$promoterTot-$discounts;
				else
					$netSales = $gross-$promoterTot;
					
					
				$data.= "\n,,,,,,,,,Gross Sale,$".$gross. "\n";
				$data.= ",,,,,,,,,Discount,$".number_format($discounts,2). "\n";
				$data.= ",,,,,,,,,Net Sales,$".$netSales. "\n";
				
				$data = str_replace( "\r" , "" , $data );
								
		if ( $data == "" )
		{
			$data = "\n(0) Records Found!\n";                        
		}
		
	
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=Purchased".time().".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		print "$header\n$data";
			
	}
	else{
		echo "<strong>Not Found</strong>";
	}

	




} // end if($type=='date')









?>