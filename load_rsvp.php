<?php
	require_once('admin/database.php');
	$event_id = $_GET['event_id'];
	$type	  = $_GET['type'];
	
	if($type == 'rsvp' && $event_id){
		// Get data records from table. 
		$result=mysql_query("select * from `events_rsvp` where `event_id`='$event_id'");
		if(mysql_num_rows($result)){
			$userid 	= getSingleColumn('userid',"select * from events where id=" . $event_id);
			if($userid==$_SESSION['LOGGEDIN_MEMBER_ID'] || $_SESSION['admin_user']){
			
				// Functions for export to excel.
				function xlsBOF() { 
				echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
				return; 
				} 
				function xlsEOF() { 
				echo pack("ss", 0x0A, 0x00); 
				return; 
				} 
				function xlsWriteNumber($Row, $Col, $Value) {
				echo pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
				echo pack("d", $Value); 
				return; 
				} 
				function xlsWriteLabel($Row, $Col, $Value ) { 
				$L = strlen($Value); 
				echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L); 
				echo $Value; 
				return; 
				} 
				
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");;
				header("Content-Disposition: attachment;filename=RSVP.xls"); // orderlist
				header("Content-Transfer-Encoding: binary ");
				
				xlsBOF();
				
				/*
				Make a top line on your excel sheet at line 1 (starting at 0).
				The first number is the row number and the second number is the column, both are start at '0'
				*/
				
				//xlsWriteLabel(0,0,"Name.");
				
				// Make column labels. (at line 3)
				xlsWriteLabel(0,0,"No.");
				xlsWriteLabel(0,1,"Name");
				xlsWriteLabel(0,2,"Email");
				xlsWriteLabel(0,3,"How did hear about us");
				
				$xlsRow = 2;
				
				// Put data records from mysql by while loop.
				$i=0;
				while($row=mysql_fetch_array($result)){
					$i++;
					xlsWriteNumber($xlsRow,0,$i);
					xlsWriteLabel($xlsRow,1,$row['name']);
					xlsWriteLabel($xlsRow,2,$row['email']);
					xlsWriteLabel($xlsRow,3,$row['how_did_hear']);
					$xlsRow++;
				}
				xlsEOF();
			}
			else{?>
			<script>
			   var popup_window = window.open("", "_self");
			   popup_window.close ();
			</script>
			<?php
			}
		}
		else{
			echo "<strong>No RSVP found for this event</strong>";
		}
	} // if ($type == 'rsvp')
	elseif($type == 'ticket' && $event_id){
	
	
	$result	= mysql_query("select * from `orders` where `main_ticket_id`='$event_id' && `total_price`!='' && `type`='ticket'");
	if(mysql_num_rows($result)){
		$userid 	= getSingleColumn('userid',"select * from events where id=" . $event_id);
		if($userid==$_SESSION['LOGGEDIN_MEMBER_ID'] || $_SESSION['admin_user']){
		
			// Functions for export to excel.
			function xlsBOF() { 
				echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
			return; 
			} 
			function xlsEOF() { 
				echo pack("ss", 0x0A, 0x00); 
			return; 
			} 
			function xlsWriteNumber($Row, $Col, $Value) {
				echo pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
				echo pack("d", $Value); 
			return; 
			} 
			function xlsWriteLabel($Row, $Col, $Value ) { 
				$L = strlen($Value); 
				echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L); 
				echo $Value; 
			return; 
			} 
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");;
			header("Content-Disposition: attachment;filename= Purchased".time().".xls"); // orderlist
			header("Content-Transfer-Encoding: binary ");
			
			xlsBOF();
			
			/*
			Make a top line on your excel sheet at line 1 (starting at 0).
			The first number is the row number and the second number is the column, both are start at '0'
			*/
			
			//xlsWriteLabel(0,0,"Name.");
			
			// Make column labels. (at line 3)
			xlsWriteLabel(0,0,"No.");
			xlsWriteLabel(0,1,"Name");
			xlsWriteLabel(0,2,"Email");
			xlsWriteLabel(0,3,"Ticket Type");
			xlsWriteLabel(0,4,"Qty");
			xlsWriteLabel(0,5,"Purchase Date");
			$xlsRow = 2;
			
			// Put data records from mysql by while loop.
			$i=0;
			while($row=mysql_fetch_array($result)){
			$order_id	= $row['id'];
			$resTicket = mysql_query("select * from `order_tickets` where `order_id`='$order_id'");
				while($rowTicket=mysql_fetch_array($resTicket)){
					$qty		= $rowTicket['quantity'];
					$name		= $rowTicket['name'];
					$email		= $rowTicket['email'];
					$ticket_id	= $rowTicket['ticket_id'];
					$Type 	= getSingleColumn('title',"select * from event_ticket_price where id=" . $ticket_id);				
					
					$i++;
					xlsWriteNumber($xlsRow,0,$i);
					xlsWriteLabel($xlsRow,1,$name);
					xlsWriteLabel($xlsRow,2,$email);
					xlsWriteLabel($xlsRow,3,$Type);
					xlsWriteNumber($xlsRow,4,$qty);
					xlsWriteLabel($xlsRow,5,date('d M Y', strtotime($row['date'])));
					$xlsRow++;
				}
			}
			xlsEOF();
		}
		else{?>
			<script>
			var popup_window = window.open("", "_self");
			popup_window.close ();
			</script>
		<?php
		}
	}
	else{
	echo "<strong>No Purchase found for this event</strong>";
	}
	
	
	
	
	
	}
	?>