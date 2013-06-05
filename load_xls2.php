<?php
	require_once('admin/database.php');
	require_once('site_functions.php');
	$event_id	= $_GET['event_id'];
	$type		= $_GET['type'];
	$member_id	= $_SESSION['LOGGEDIN_MEMBER_ID'];
	
	if($type == 'rsvp' && $event_id){
		$result=mysql_query("select * from `events_rsvp` where `event_id`='$event_id'");
		if(mysql_num_rows($result)){
			$userid 	= getSingleColumn('userid',"select * from events where id=" . $event_id);
			if($userid==$_SESSION['LOGGEDIN_MEMBER_ID'] || $_SESSION['admin_user']){
			
				$header = "No.,";
				$header.= "Name,";
				$header.= "Email,";
				$header.= "How did hear about us,\n";
				
				$i=0;
				$data = '';
				$line = '';
				while($row=mysql_fetch_array($result)){
					$i++;
					$line.= $i.",";
					$line.= $row['name'].",";
					$line.= $row['email'].",";
					$line.= $row['how_did_hear'].",";
					$data.= trim( $line ) . "\n";
					$line='';
				}
				
				$data = str_replace( "\r" , "" , $data );
							
				if ( $data == "" )
				{
					$data = "\n(0) Records Found!\n";                        
				}
				
	
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=RSVP.csv");
				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
				
			}
		}
		else{
			echo "<b>No RSVP found for this event</b>";
		}
	}
	
	elseif($type == 'ticket' && $event_id){
		$netTotal = 0;
		$result	= mysql_query("select * from `orders` where `main_ticket_id`='$event_id' && `total_price`!='' && `type`='ticket'");
		if(mysql_num_rows($result)){
			$userid 	= getSingleColumn('userid',"select * from events where id=" . $event_id);
			if($userid==$_SESSION['LOGGEDIN_MEMBER_ID'] || $_SESSION['admin_user']){
			
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
			$header.= "Purchase Price,\n";
			
			$i=0;
				$data = '';
				$line = '';
				while($row=mysql_fetch_array($result)){
					$discount	= $row['discount'];
					$discounts	= $discounts + $discount;
					$order_id	= $row['id'];
					$resTicket = mysql_query("select * from `order_tickets` where `order_id`='$order_id'");
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
							$line.= "$".$price.",";
						else
							$line.= ",";
							
						
						$buyer_service_fee	= $rowTicket['buyer_fee'];
						$promtrFree			= $rowTicket['total_fee'] - $rowTicket['buyer_fee'];
						$promoterTot		= $promtrFree+$promoterTot;
						
						if($rowTicket['total_fee']){
							$line.= "$".$promtrFree.",";
							$line.= "$".$buyer_service_fee.",";
						}
						else{
							$line.= ",,";
							}
							
					$tPrice		= $buyer_service_fee+$price;
					
					$netTotal	= $price+$netTotal;
						
						$line.= $rowTicket['quantity'].",";
						$line.= date('d M Y', strtotime($row['date'])).",";
						$line.= "$".$tPrice-$discount.",";
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
				$data.= ",,,,,,,,,Discount,$".$discounts. "\n";
				$data.= ",,,,,,,,,Net Sales,$".$netSales. "\n";
				
				$data = str_replace( "\r" , "" , $data );
				
				$data = str_replace( "\n" , "<br />" , $data );
							
				if ( $data == "" )
				{
					$data = "\n(0) Records Found!\n";                        
				}
				
		//		header("Content-type: application/octet-stream");
		//		header("Content-Disposition: attachment; filename=Purchased".time().".csv");
		//		header("Pragma: no-cache");
		//		header("Expires: 0");
				print "$header\n$data";
				exit();
			}
		}
		echo "<b>Not Found</b>";
	}
	
	elseif($type == 'nba' && $_SESSION['admin_user']){
	
	$result	= mysql_query("select * from `subcribe_nba` ORDER BY `id` ASC");
		if(mysql_num_rows($result)){			
			$header = "No.,";
			$header.= "Email,";
			$header.= "Verified Email,\n";
			
			$i=0;
				$data = '';
				$line = '';
				while($row=mysql_fetch_array($result)){
					$i++;
					$line.= $i.",";
					$line.= $row['email'].",";
					if($row['status']==1)
						$line.= "Yes,";
					else
						$line.= "No,";
					$data.= trim( $line ) . "\n";
					$line='';
				}
				
				$data = str_replace( "\r" , "" , $data );
							
				if ( $data == "" )
				{
					$data = "\n(0) Records Found!\n";                        
				}
				
	
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=NBA.csv");
				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
		}
	}
	
	elseif($type == 'user'){
		$sql = "select o.main_ticket_id, ot.price, ot.product_name, o.id, ot.ticket_id from orders o, order_tickets ot where o.total_price!='' && ot.order_id=o.id && o.type='ticket' && o.promoter_id='$member_id' GROUP BY ot.ticket_id";
	
		$res = mysql_query($sql);
		$i=0;
			$header = "No.,";
			$header.= "Event Name,";
			$header.= "Ticket Type,";
			$header.= "Sold,";
			$header.= "Gross Sale,";
			$header.= "Fees,";
			$header.= "Net Profit,\n";
			
			$data = '';
			$line = '';
			
			while($row = mysql_fetch_array($res)){
				$i++;
				
				$order_id		= $row['id'];
				$event_id		= $row['main_ticket_id'];
				$product_name	= $row['product_name'];
				$ticket_id		= $row['ticket_id'];
				$price			= $row['price'];
				
				$event_url		= getEventURL($event_id);
				
				$sold		= getSingleColumn("tot","select count(*) as tot from order_tickets ot, orders o where ot.ticket_id='$ticket_id' && o.id=ot.order_id && o.total_price!=''");
				$totalSold 	= $sold + $totalSold;
				
				$total_fee			= getSingleColumn('total_fee',"select SUM(total_fee) as total_fee from order_tickets where `ticket_id`='".$ticket_id."'");
				$buyer_fee			= getSingleColumn('buyer_fee',"select SUM(buyer_fee) as buyer_fee from order_tickets where `ticket_id`='".$ticket_id."'");
				
				$fees				= $total_fee - $buyer_fee;
				$totFees			= $fees + $totFees;
				
				$newGrs			= $row['price'] * $sold;
				
				$grossSale			= getTicketGross($ticket_id,'ticket');
				$totalGrossSale		= $newGrs + $totalGrossSale;
				$netProfit			= $grossSale - $fees;
				$totalNetProfit		= $netProfit + $totalNetProfit;
	
				$line.= $i.",";
					
				$line.= getSingleColumn("event_name","select * from `events` where `id`='$event_id'").",";
				$line.= $product_name.",";
				$line.= $sold.",";
				$line.= '$'.$newGrs.",";
				$line.= '$'.$fees.",";
				$line.= '$'.$netProfit.",";
				$data.= trim( $line ) . "\n";
				$line='';
			}
		
			$data.= "\n\n,Totals,,".$totalSold.",$".$totalGrossSale.",$".$totFees.",$".$totalNetProfit."\n";
				
				$data = str_replace( "\r" , "" , $data );
				
				if ( $data == "" ){
					$data = "\n(0) Records Found!\n";
				}
				
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=Sale.csv");
				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
	}
	
?>