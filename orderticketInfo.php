<table cellpadding="10" cellspacing="0" width="100%">
			<tr bgcolor="#d1e5c0">
                  <td width="24%"><strong>Date</strong></td>
                  <td width="47%"><strong>Product</strong></td>
                  <td width="15%" align="center"><strong>Amount</strong></td>
                  <td width="14%" align="center"><strong>Price</strong></td>
                </tr>
			<?php
			
				
				$qtys	= explode(",",$_SESSION['ticketOrder']['ticket_qty']);
				$ids	= explode(",",$_SESSION['ticketOrder']['ticket_id']);
				$dates	= explode(",",$_SESSION['ticketOrder']['ticket_date']);
			
				$service_charge = 1.00;
				
				for($i=0;$i<count($qtys);$i++){
					if($qtys[$i]!=0 && $qtys[$i]!=''){
					
					$ticketDetail = getTicketDetail($ids[$i]);
					//print_r($ticketDetail);
					
					?>
					 <tr bgcolor="#f3f6ea">
					  <td style="border-bottom:#c2c5bb solid 1px;"><?php echo date('d M Y', strtotime(getDateById($dates[$i]))); ?></td>
					  <td style="border-bottom:#c2c5bb solid 1px;"><?php echo $ticketDetail['title']; ?></td>
					  <td align="center" style="border-bottom:#c2c5bb solid 1px;"><?php echo $qtys[$i]; ?></td>
					  <td align="center" style="border-bottom:#c2c5bb solid 1px;">$<?php
					  $buyer = getSingleColumn('buyer_event_grabber_fee',"select * from event_ticket where event_id=$event_id");
			
				$buyer_service_free_after_percent		=	$ticketDetail['price']*TICKET_FEE_PERSENT/100+TICKET_FEE_PLUS;
				$buyer_service_free_after_percent		=	$buyer_service_free_after_percent*$buyer/100;
				$finalServiceCharges	=	number_format($buyer_service_free_after_percent, 2,'.','');
				
				$price = ($finalServiceCharges+$ticketDetail['price'])*($qtys[$i]);
				echo number_format($price, 2,'.','');
				$finalPrice = $price+$finalPrice+$service_charge;
			?></td>
					</tr>
					<?php
					}
				}
			?>
               <?php
			   if($service_charge!=0){
			   ?>
                <tr bgcolor="#f3f6ea">
                  <td></td>
                  <td class="new_blue">Service charge</td>
				  <td></td>
				  <td align="center">$<?php echo number_format($service_charge, 2,'.',''); ?></td>
				  </tr>
				 <?php } ?>
				 <tr bgcolor="#f3f6ea">
				 <td></td>
				  <td class="new_blue">
                    Total (excl. transaction costs) </td>
                  <td>&nbsp;</td>
                  <td align="center">
                    $<?php echo number_format($finalPrice, 2,'.','');?></td>
                </tr>
              </table>