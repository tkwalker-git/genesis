<?php 
session_start();
include('../gateways/paypal_direct_payment.php');
include('../gg_site_functions.php');

if(isset($_GET['action'])):
	
	$action = $_GET['action'];

else:
	
	$action = $_POST['action'];
	
endif;

switch($action):

	case 'login_procesor':
	
		
		$sql_1 = "SELECT id, username, password , fname , lname FROM gigger WHERE username = '". $_POST['username'] ."' and password = '". $_POST['password'] ."' ";
		$sql_2 = "SELECT id, username, password , fname , lname FROM gopher WHERE username = '". $_POST['username'] ."' and password = '". $_POST['password'] ."' ";
		
		$run_sql_1 = mysql_query($sql_1);
		$run_sql_2 = mysql_query($sql_2);
		$sql_1_count = mysql_num_rows($run_sql_1);
		$sql_2_count = mysql_num_rows($run_sql_2);
		
		if($sql_1_count > 0){
			$giggerInfo = mysql_fetch_assoc($run_sql_1);
			$_SESSION['SESSIONED_USER_PROTYPE'] = 'gigher_pro';
			$_SESSION['SESSIONED_USER_USERNAME'] = $giggerInfo['username'];
			$_SESSION['SESSIONED_USER_FIRSTNAME'] = $giggerInfo['fname'];
			$_SESSION['SESSIONED_USER_LASTNAME'] = $giggerInfo['lname'];
			$_SESSION['SESSIONED_USER_LOGINID'] = $giggerInfo['id'];
			
			echo 'gigger';
			
		}else if($sql_2_count > 0){
		
			$giggerInfo = mysql_fetch_assoc($run_sql_2);
			$_SESSION['SESSIONED_USER_PROTYPE'] = 'gigher_pro';
			$_SESSION['SESSIONED_USER_USERNAME'] = $giggerInfo['username'];
			$_SESSION['SESSIONED_USER_FIRSTNAME'] = $giggerInfo['fname'];
			$_SESSION['SESSIONED_USER_LASTNAME'] = $giggerInfo['lname'];
			$_SESSION['SESSIONED_USER_LOGINID'] = $giggerInfo['id'];
			
			echo 'gopher';
		
		
		}else{
		
			echo 0;
		}
	
	break; // end case of login_procesor
	
	
	case 'post_gig_step_one' :
		
		$_SESSION['POST_GIG'] = array();
		
		foreach($_POST as $_post_index => $_post_values):
		
			if($_post_index == 'post_gig_activefor'):
				$todayDate = date('Y-m-d');
				$exprDate = date('Y-m-d' , strtotime($todayDate . '+ '. $_POST['post_gig_activefor'] .''));
				$sessionvars[$_post_index] = $exprDate;
			else:
				$sessionvars[$_post_index] = $_post_values;
			endif;

			

		endforeach;
		
		$_SESSION['POST_GIG'] = $sessionvars;
		//print_r($_SESSION['POST_GIG']);
		
		echo 1;
		
	break; //end case of post_gig_step_one 
	
	case 'post_gig_final' :
	
		//$paymentType = urlencode($_POST['Authorization']);				// or 'Sale'
		$paymentType =	'Sale';
		$firstName =urlencode($_SESSION['SESSIONED_USER_FIRSTNAME']); //$_SESSION['REGISTER']['gig-first-name']
		$lastName = urlencode($_SESSION['SESSIONED_USER_LASTNAME']);
		$creditCardType = urlencode('Visa');
		$creditCardNumber = urlencode($_POST['creditCardNumber']);
		$expDateMonth = $_POST['expDateMonth'];
		// Month must be padded with leading zero
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
		
		$expDateYear = urlencode($_POST['expDateYear']);
		$cvv2Number = urlencode($_POST['cvv2Number']);
		$address1 = urlencode($_POST['address1']);
		$address2 = '';
		$city = urlencode($_POST['city']);
		$state = urlencode($sql_giggerInfo['state']);
		$zip = urlencode($_POST['zip']);
		$country = 'US';	// US or other valid country code
		$amount = $_POST['amount'];	//actual amount should be substituted here
		$currencyID = 'USD';// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

// Add request-specific fields to the request string.
$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
			"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
			"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";

// Execute the API operation; see the PPHttpPost function above.
$httpParsedResponseAr = PPHttpPost('DoDirectPayment', $nvpStr);
//print_r($httpParsedResponseAr);

//exit;


		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			$a = urldecode($httpParsedResponseAr['TIMESTAMP']);
			$todayDateTime = date("Y-m-d H:i:s",strtotime($a));
			$todayDate		= date("Y-m-d");
			
			$sql_giggerInfo = "SELECT id, state FROM gigger WHERE id = '". $_SESSION['SESSIONED_USER_LOGINID'] ."' ";
			$run_sql_giggerInfo = mysql_query($sql_giggerInfo);
			$sql_giggerInfo = mysql_fetch_assoc($run_sql_giggerInfo);
		
			$gig_time = $_SESSION['POST_GIG']['post_gig_anytime'];
		
			$save_gig_sql = "INSERT INTO gigs ( `gg_id` , `gg_type` ,`headline` , `the_gist` , `cost` , `post_date` , `keep_active` , `any_date` , `gig_date` , `any_time` , `gig_time_start` , `gig_time_end` , `gig_address` , `gig_city` , `gig_state` , `gig_zip` , `any_location` , `within_miles` , `categories` , `gophers_needed` , `gig_status`) VALUES ('". $sql_giggerInfo['id'] ."' , 'will' , '". $_SESSION['POST_GIG']['post_gig_headline'] ."' , '". $_SESSION['POST_GIG']['post_gig_gist'] ."' , '". $_SESSION['POST_GIG']['post_gig_will_pay'] ."' , '". $todayDateTime ."' , '". $_SESSION['POST_GIG']['post_gig_activefor'] ."' , '". $_SESSION['POST_GIG']['post_gig_anydate'] ."' , '". $todayDate ."' , '". $_SESSION['POST_GIG']['post_gig_anytime'] ."' , '". $_SESSION['POST_GIG']['starttime'] ."' , '". $_SESSION['POST_GIG']['endtime'] ."' , '". $_SESSION['POST_GIG']['search_loc_street'] ."' , '". $_SESSION['POST_GIG']['search_loc_city'] ."' , '". $_SESSION['POST_GIG']['loc_state'] ."' , '". $_SESSION['POST_GIG']['post_gig_loczip'] ."' , '". $_SESSION['POST_GIG']['any_location'] ."' , '". $_SESSION['POST_GIG']['loc_miles'] ."' , '". $_SESSION['POST_GIG']['cat'] ."' , '". $_SESSION['POST_GIG']['multi_gop_no'] ."' , '1' )";
		
			$run_save_gig_sql = mysql_query($save_gig_sql);
		
			if($run_save_gig_sql){
				
				echo $messageResponse = 1;
				
			}else{
			
				echo $messageResponse = 2;
			}
		
		}else{
		
			echo $messageResponse = 3;
		
		}
	
	break; // end case post_gig_final
	
	
	case 'empty_session' :
	
		$_SESSION['POST_GIG'] = "";
		
		echo 1;
	
	break; //end case empty_session
	

	case 'gig_post_home' :
	
		if($_SESSION['SESSIONED_USER_LOGINID'] != ''):
	
			$_SESSION['POST_GIG'] = array();
			
			$_SESSION['POST_GIG']['post-gig-headline'] = $_POST['post-gig-headline'];
			$_SESSION['POST_GIG']['post-gig-will-pay'] = $_POST['post-gig-will-pay'];

		else:
		
			echo 0;
		
		endif;
		
		
	
	break; // end case gig_post_home
	
	
	case 'search_gigs' :
		
		$gig_date_cond 		= '';
		$gig_time_cond 		= '';
		$gig_loc_cond		= '';
		$cash_limit_cond 	= '';
		$category_cond		= '';
		
		
			
		if($_POST['search_any_date'] == 'No' )
			$gig_date_cond  = "gig_date > 2008-01-01  ";
		
		if($_POST['search_any_time'] == 'No' ){
		
			if($gig_date_cond != '')
				$opr_time	= 'AND';
				
			$strtime = substr($_POST['search_start_time_input'] , 0 , 2 );
			$endtime = substr($_POST['search_end_time_input'] , 0 , 2 );
			
			$gig_time_cond =  " ". $opr_time ." gig_time_start >=  '". $strtime ."' AND gig_time_end <= '". $endtime ."' ";
		}
		
		
		if($_POST['search_loc_any'] == 'No'){ 
				
			if(isset($_POST['search_loc_city']) and $_POST['search_loc_city'] != 'City')
				$gig_loc_cond .= " AND gig_city = '". $_POST['search_loc_city'] ."' ";
			
			if(isset($_POST['search_loc_street']) and $_POST['search_loc_street'] != 'Street Address')
				$gig_loc_cond .=  " AND gig_address = '". $_POST['search_loc_street'] ."' ";
			
			if(isset($_POST['search_loc_state_input']))
				$gig_loc_cond .=  " AND gig_state = '". $_POST['search_loc_state_input'] ."' ";
				
			if(isset($_POST['search_loc_zip']) and $_POST['search_loc_zip'] != 'Zip')
				$gig_loc_cond .=  " AND gig_zip = '". $_POST['search_loc_zip'] ."' ";
				
			if(isset($_POST['search_loc_dist_input'])){
				$substr_miles = substr($_POST['search_loc_dist_input'] , 0 , 2 );
				$gig_loc_cond .=  " AND within_miles <= '". $substr_miles ."' ";
			}
			
			
			
		}
		
		
		
			
		if($_POST['cash_limit'] != ''){
			if($gig_date_cond != '' or $gig_time_cond != '')
				$opr_cash	= 'AND';
				
			$cash_limit_cond = " ". $opr_cash ." cost >= ". $_POST['cash_limit'] ." ";
		}
			
			
		if($_POST['cat'] != ''){
		$categories =  explode(',' , $_POST['cat']);
		

		
		$count_cats = count($categories);
		
		$category_cond = " AND ";
		
			foreach($categories as $indexs => $values){
				
				$opr = 'OR';
				
				if($indexs == $count_cats - 1)
					$opr = '';
				$category_cond .= ' categories LIKE "%'. $values .'%" '. $opr .' ';
				
			}
		$category_cond .= "";
		}else{
		
			$category_cond = "";	
		
		} //end if isset cat
		
		
		
		
		
		if($gig_date_cond == '' and $gig_time_cond == '' and $gig_loc_cond == '' and  $cash_limit_cond == '' and $category_cond == '')
			$where = '';
		else
			$where = 'WHERE';
			
		
		
		if($_POST['mode'] == '' || $_POST['mode'] == 'undefined' )
			$sql = "SELECT * FROM gigs ORDER BY id DESC ";
		else
			$sql = "SELECT * FROM gigs ". $where ." ". $gig_date_cond . $gig_time_cond . $gig_loc_cond . $cash_limit_cond . $category_cond . "  ORDER BY id DESC ";
			
			
		$run_sql = mysql_query($sql);
		
		include('../pagination.php');
		
		
		$count_gigs = mysql_num_rows($run_sql_limit);
		
		
		
		if($count_gigs > 0):
			while($gigInfo = mysql_fetch_assoc($run_sql_limit)):
			
			$gigDetail = strlen($gigInfo['the_gist']);
			
			
			if($gigDetail > 150){
				
				$gigDetailsub = substr($gigInfo['the_gist'] , 0 , 150) . '...';
				
				
			}else{
			
				$gigDetailsub = $gigInfo['the_gist'];
				
			}
			
			$postedBy = attribValue("gigger","CONCAT(fname , ' ' , lname)"," WHERE id = '". $gigInfo['gg_id'] ."' ");
			
			?>
				
				<li>
					<a href="#"><img src="<?php echo returnProfileImage('gigger' , $gigInfo['gg_id']); ?>" alt="gig img" class="thumb" /></a>
					  <h4><?php echo $gigInfo['headline']; ?></h4>
					  <p><?php echo $gigDetailsub; ?></p>
					  <div class="gig-detail gig_block">
						<div class="left">
								<p class="posted"><span>Posted by:</span><?php echo $gigInfo['gig_address']; ?></p>
								  <p class="place"><span><?php echo $postedBy; ?>:</span> <?php echo RelativeTime( $gigInfo['post_date'] );?></p>
							</div><!-- end of left div -->
							<div class="right">
								  <p>Start Time: 12:00 pm</p>
								  <p>Finish Time: 12:00 pm</p>
							</div>
							<div class="control-bar">
								<div class="rate">
									<span class="on">&nbsp;</span>
									<span class="on">&nbsp;</span>
										<span class="on">&nbsp;</span>
									<span class="on">&nbsp;</span>
										<span class="off">&nbsp;</span>
										<p>Reviews: <span>6</span></p>
								  </div>
								<ul class="spects">
									<li class="tTip"  title="<?php echo $gigInfo['gig_time_start'] . ' To ' . $gigInfo['gig_time_end']; ?>"><a href="javascript:void(0);" class="time">&nbsp;</a></li>
									<li class="tTip"  title="<?php echo date('d F, Y' , strtotime($gigInfo['keep_active'])); ?>"><a href="javascript:void(0);" class="date">&nbsp;</a></li>
									<li><a href="javascript:void(0);" class="flag">&nbsp;</a></li>
								  </ul>
								  
								  <div class="ajaxResponder responder_<?php echo $gigInfo['id']; ?>" style="display:none;">Please Wait!</div>
								  
								  <div class="btns">
									<span class="price"><?php echo $gigInfo['cost']; ?></span>
										<a href="javascript:void(0)" rel="<?php echo $gigInfo['id']; ?>" class="get-gig gig-btn">Get Gig</a>
								  </div>
							</div><!-- end of right div -->
					  </div><!-- end of gig-detail div -->
				</li>
				
				
			<?
			endwhile;
			
			echo $pagination;
			
			
		else:
		
			?>
			
			<li>No Gig Found!</li>
			
			<?php 
			
		
		endif;
			
	
	break; // end case search_gigs
	
	case 'get_gig':
	
	  if($_SESSION['SESSIONED_USER_LOGINID'] != ''){
	        
		$already = attribValue("gopher_apps" , "gopher_id" , " WHERE gig_id = '". $_GET['gig_id'] ."' AND gopher_id = '". $_SESSION['SESSIONED_USER_LOGINID'] ."' ");
		$owngig = attribValue("gigs" , "gg_id" , " WHERE id = '". $_GET['gig_id'] ."'");
		
		if($owngig == $_SESSION['SESSIONED_USER_LOGINID']){
			echo 'own';
			exit;
		}
			
			if($already == $_SESSION['SESSIONED_USER_LOGINID']){
			
			 echo 0;
			
			}else{
			
			
				$sql = "SELECT * FROM gigs WHERE id = '". $_GET['gig_id'] ."' ";
				$run_sql = mysql_query($sql);
				$count = mysql_num_rows($run_sql);
				
				if($count > 0):
				
					$gigInfo = mysql_fetch_assoc($run_sql);
					
					$getGig = $_SESSION['GET_GEG'];
					
					$_SESSION['GET_GEG'] = array();
					$gigSessionVar = array();
					
					foreach($gigInfo as $fieldName => $fieldValue):
					
						$gigSessionVar[$fieldName] = $fieldValue;
					
					endforeach;
					
					$_SESSION['GET_GEG'] = $gigSessionVar;
					
					echo 1;
					
				
				else:
				
					echo 2;
				
				endif;
			
			
			}
		
		}else{
		
		echo 3;
		
		}
		
	
	break;  //end case get_gig
	
	
	
	case 'get_gig_final' :
	
		if($_SESSION['SESSIONED_USER_LOGINID'] != ''){
	        
			$already = attribValue("gopher_apps" , "gopher_id" , " WHERE gig_id = '". $_SESSION['GET_GEG']['id'] ."' AND gopher_id = '". $_SESSION['SESSIONED_USER_LOGINID'] ."' ");
			
			if($already == $_SESSION['SESSIONED_USER_LOGINID']){
			
			 echo 0;
			
			}else{
	
				$todayDateTime = date('Y-m-d h:i:s');
				
				$sql = "INSERT INTO gopher_apps ( `gig_id` , `gopher_id` , `date` ) VALUES ( '". $_SESSION['GET_GEG']['id'] ."' ,  '". $_SESSION['SESSIONED_USER_LOGINID'] ."' , '". $todayDateTime ."' )";
				$run_sql = mysql_query($sql);
				
				if($run_sql){
					echo 1;
				}else{
					echo 2;
				}
			} //end if $already
			
		}//end if not login
	
	break; //end case get_gig_final
	
	case 'post_offer_sessions' :
	
		$_SESSION['POST_OFFER'] = array();
		
		foreach($_POST as $_post_index => $_post_values):
			
			if($_post_index == 'post_offer_activefor'):
				$todayDate = date('Y-m-d');
				$exprDate = date('Y-m-d' , strtotime($todayDate . '+ '. $_POST['post_gig_activefor'] .''));
				$sessionvars[$_post_index] = $exprDate;
			else:
				$sessionvars[$_post_index] = $_post_values;
			endif;

		endforeach;
		
		$_SESSION['POST_OFFER'] = $sessionvars;
		//print_r($_SESSION['POST_GIG']);
		
		
		echo 1;
	
	break; //end case post_offer_sessions
	
	
	case 'post_offer_final' :
	
		$todayDateTime = date('Y-m-d h:i:s');
	
		$sql = "INSERT INTO offers ( `gg_id` , `headline` , `the_gist` , `cost` , `post_date` , `keep_active` , `any_date` , `offer_date` , `any_time` , `offer_time_start` , `offer_time_end` , `offer_address` , `offer_city` , `offer_state` , `offer_zip` , `any_location` , `within_miles` , `categories` , `offer_status` ) VALUES ( '".$_SESSION['SESSIONED_USER_LOGINID']."' , '".$_SESSION['POST_OFFER']['post_offer_headline']."' , '".$_SESSION['POST_OFFER']['post_offer_gist']."' , '".$_SESSION['POST_OFFER']['post_offer_will_pay']."' , '".$todayDateTime."' , '".$_SESSION['POST_OFFER']['post_offer_activefor']."' , '".$_SESSION['POST_OFFER']['post_offer_anydate']."' , '' , '".$_SESSION['POST_OFFER']['post_offer_anytime']."' , '".$_SESSION['POST_OFFER']['offer_start_by']."' , '".$_SESSION['POST_OFFER']['offer_end_by']."' , '".$_SESSION['POST_OFFER']['post_offer_loc_street']."' , '".$_SESSION['POST_OFFER']['post_offer_loc_city']."' , '".$_SESSION['POST_OFFER']['offer_loc_state']."' , '".$_SESSION['POST_OFFER']['post_offer_loc_zip']."' , '".$_SESSION['POST_OFFER']['post_offer_locany']."' , '".$_SESSION['POST_OFFER']['offer_loc_miles']."' , '".$_SESSION['POST_OFFER']['cat']."' , '1'  )";
		
		$run_sql = mysql_query($sql);
		
		if($run_sql){
			echo 1;
		}else{
			echo 0;
		}
	
	break; // end case post_offer_final
	
	
	case 'paginat_offers' :
	
		$sql = "SELECT * FROM offers ORDER BY id DESC";
		
		$run_sql = mysql_query($sql);
		
		$adjacents = 2;

		$count = mysql_num_rows($run_sql);
		///pagination code start
		$total_pages = $count;
		
		/* Setup vars for query. */
		$limit = 5; 
		
		if(!(isset($_POST['offer']))){
			$page = 1;
		}else{
			$page = $_POST['offer'];
		}
										//how many items to show per page
		if($page) 
			$start = ($page - 1) * $limit; 			//first item to display on this page
		else
			$start = 0;	
										//if no page var is given, set start to 0
										
		$sql_limit = "SELECT * FROM offers ORDER BY id DESC LIMIT $start, $limit ";
		
			
		
		$run_sql_limit = mysql_query($sql_limit);
	
		/* Setup page vars for display. */
		if ($page == 0) $page = 1;					//if no page var is given, default to 1.
		$prev = $page - 1;							//previous page is page - 1
		$next = $page + 1;							//next page is page + 1
		$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;						//last page minus 1
		
		
		/* 
			Now we apply our ABSOLUTE_PATHles and draw the pagination object. 
			We're actually saving the code to a variable in case we want to draw it more than once.
		*/
		$pagination = "";
		if($lastpage > 1)
		{	
			$pagination .= "<div class=\"pagination\">";
			//previous button
			if ($page > 1) 
				$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$prev.")\" href=\"javascript:void(0)\"><< previous</a>";
			else
				$pagination.= "<span class=\"disabled\"><< previous</span>";	
			
			//pages	
			if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\">$counter</a>";					
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
			{
				//close to beginning; only hide later pages
				if($page < 1 + ($adjacents * 2))		
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\">$counter</a>";					
					}
					$pagination.= "...";
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$lpml.")\" href=\"javascript:void(0)\">$lpm1</a>";
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$lastpage.")\" href=\"javascript:void(0)\">$lastpage</a>";		
				}
				//in middle; hide some front and some back
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\">1</a>";
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\">2</a>";
					$pagination.= "...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\">$counter</a>";					
					}
					$pagination.= "...";
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$lpml.")\" href=\"javascript:void(0)\" >$lpm1</a>";
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$lastpage.")\" href=\"javascript:void(0)\">$lastpage</a>";		
				}
				//close to end; only hide early pages
				else
				{
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\">1</a>";
					$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\" >2</a>";
					$pagination.= "...";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$counter.")\" href=\"javascript:void(0)\" >$counter</a>";					
					}
				}
			}
			
			//next button
			if ($page < $counter - 1) 
				$pagination.= "<a onclick=\"searchOffers('". ABSOLUTE_PATH ."' ,".$next.")\" href=\"javascript:void(0)\" >next >></a>";
			else
				$pagination.= "<span class=\"disabled\">next >></span>";
			$pagination.= "</div>\n";		
		}
		//pagination code end
		
		$count_gigs = mysql_num_rows($run_sql_limit);



		if($count_gigs > 0):
			while($offerInfo = mysql_fetch_assoc($run_sql_limit)):
			
			$gigDetail = strlen($offerInfo['the_gist']);
			
			
			if($gigDetail > 150){
				
				$gigDetailsub = substr($offerInfo['the_gist'] , 0 , 150) . '...';
				
				
			}else{
			
				$gigDetailsub = $offerInfo['the_gist'];
				
			}
			
		$postedBy = attribValue("gopher","CONCAT(fname , ' ' , lname)"," WHERE id = '". $offerInfo['gg_id'] ."' ");
		
		?>
		
		<li class="pro-user">
		<a href="#"><img src="<?php echo returnProfileImage('gopher' , $offerInfo['gg_id']); ?>" alt="gig img" class="thumb" /></a>
		  <h4><?php echo $offerInfo['headline']; ?></h4>
		  <p><?php echo $offerInfo['the_gist']; ?></p>
		  <div class="gig-detail">
			<div class="left">
					<p class="posted"><span>Posted by:</span><?php echo $offerInfo['offer_address']; ?></p>
					  <p class="place"><span><?php echo $postedBy; ?>:</span> <?php echo RelativeTime( $offerInfo['post_date'] );?></p>
				</div><!-- end of left div -->
				<div class="right">
					  <p>Start Time: <?php echo $offerInfo['offer_time_start']; ?> pm</p>
					  <p>Finish Time: <?php echo $offerInfo['offer_time_end']; ?> pm</p>
				</div>
				<div class="control-bar">
					<div class="rate">
						<span class="on">&nbsp;</span>
						<span class="on">&nbsp;</span>
							<span class="on">&nbsp;</span>
						<span class="on">&nbsp;</span>
							<span class="off">&nbsp;</span>
							<p>Reviews: <span>6</span></p>
					  </div>
					<ul class="spects">
						<li class="tTip"  title="<?php echo $offerInfo['offer_time_start'] . ' To ' . $offerInfo['offer_time_end']; ?>"><a href="javascript:void(0);" class="time">&nbsp;</a></li>
						<li  class="tTip" title="<?php echo date('d F, Y' , strtotime($offerInfo['keep_active'])); ; ?>"><a href="javascript:void(0);" class="date">&nbsp;</a></li>
						<li><a href="javascript:void(0);" class="flag">&nbsp;</a></li>
					  </ul>
					  <div class="btns">
						<span class="price"><?php echo $offerInfo['cost']; ?></span>
							<a href="javascript:void(0)" class="gig-btn hire-me">Hire Me</a>
							<?php include('../main/hire-me-tab.php'); ?>
					  </div>
				</div><!-- end of right div -->
		  </div><!-- end of gig-detail div -->
	</li>
		
		<?php
		
		endwhile;
									
		echo $pagination;
		
		endif;
	
		
	
	break; // end case paginat_offers
	
	
	case 'signup_gopher' :
	
		
		//$paymentType = urlencode($_POST['Authorization']);				// or 'Sale'
		$paymentType =	'Sale';
		$firstName =urlencode($_SESSION['REGISTER']['gig-first-name']); //$_SESSION['REGISTER']['gig-first-name']
		$lastName = urlencode($_SESSION['REGISTER']['gig-last-name']);
		$creditCardType = urlencode($_POST['creditCardType']);
		$creditCardNumber = urlencode($_POST['creditCardNumber']);
		$expDateMonth = $_POST['expDateMonth'];
		// Month must be padded with leading zero
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
		
		$expDateYear = urlencode($_POST['expDateYear']);
		$cvv2Number = urlencode($_POST['cvv2Number']);
		$address1 = urlencode($_POST['address1']);
		$address2 = '';
		$city = urlencode($_POST['city']);
		$state = urlencode($_POST['state']);
		$zip = urlencode($_POST['zip']);
		$country = 'US';	// US or other valid country code
		$amount = $_POST['amount'];	//actual amount should be substituted here
		$currencyID = 'USD';// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		
		// Add request-specific fields to the request string.
		$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
					"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
					"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";

		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = PPHttpPost('DoDirectPayment', $nvpStr);
		//print_r($httpParsedResponseAr);

		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			$a = urldecode($httpParsedResponseAr['TIMESTAMP']);
			$todayDateTime = date("Y-m-d H:i:s",strtotime($a));
			$todayDate		= date("Y-m-d");
			
			$sql = "INSERT INTO gopher (
							`profile_type` , 
							`username` , 
							`password` , 
							`fname` , 
							`lname` , 
							`bdate` , 
							`picture` , 
							`address` , 
							`city` , 
							`state` , 
							`zip` , 
							`ssn` , 
							`email` , 
							`phone` , 
							`hometown` , 
							`relationship` , 
							`school_attended` , 
							`have_car` , 
							`education_lavel` , 
							`gpa` , 
							`hobbies` , 
							`about_text` , 
							`expect_text` , 
							`skills` , 
							`other_skills` , 
							`reg_date` 
							) VALUES ( 
							'". $_SESSION['REGISTER']['gopher-pro'] ."' , 
							'". $_SESSION['REGISTER']['gig-fb-username'] ."' , 
							'". $_SESSION['REGISTER']['gig-fb-password'] ."' , 
							'". $_SESSION['REGISTER']['gig-first-name'] ."' , 
							'". $_SESSION['REGISTER']['gig-last-name'] ."' ,
							'". $_SESSION['REGISTER']['inputDate'] ."' , 
							'". $_SESSION['REGISTER']['profile_image'] ."', 
							'". $_SESSION['REGISTER']['gig-street-address'] ."' , 
							'". $_SESSION['REGISTER']['gig-city'] ."' , 
							'". $_SESSION['REGISTER']['gig-state'] ."' , 
							'". $_SESSION['REGISTER']['gig-zip-code'] ."' , 
							'". $_SESSION['REGISTER']['social-security-no'] ."' , 
							'". $_SESSION['REGISTER']['gig-email-addres'] ."' , 
							'". $_SESSION['REGISTER']['gig-telephone'] ."' , 
							'". $_SESSION['REGISTER']['the-hometown'] ."' , 
							'". $_SESSION['REGISTER']['relationship'] ."' , 
							'". $_SESSION['REGISTER']['school-attended'] ."' , 
							'". $_SESSION['REGISTER']['gop-car'] ."' , 
							'". $_SESSION['REGISTER']['edu-level'] ."' , 
							'". $_SESSION['REGISTER']['aqu-gpa'] ."' , 
							'". $_SESSION['REGISTER']['gop-hobbies'] ."' , 
							'". $_SESSION['REGISTER']['gig-about-me'] ."' , 
							'". $_SESSION['REGISTER']['gig-expection'] ."' , 
							'". $_POST['skill_str'] ."' , 
							'". $_SESSION['REGISTER']['other-skills'] ."' , 
							'". $todayDateTime ."' 
							)";
			
			
			$run_sql = mysql_query($sql);
			$currentInsertID = mysql_insert_id();
	
			if($run_sql){
			
				$srcDir = '../admin/'.TEMP; 
				makeThumbnail($_SESSION['REGISTER']['profile_image'], $srcDir , '../admin/'. MEMBER_PROFILE_IMAGES .'' , 76 , 76 );
				
				unlink('../admin/'.TEMP.$_SESSION['REGISTER']['profile_image']);
				
				if($_POST['keep_cc_info'] == 'true'){
				
				$sql_payment_info = "INSERT INTO payment_profiles (`gg_id` , `gg_type` , `cc_number` , `cc_exp_month` , `cc_exp_year` , `billing_address` , `billing_city` , `billing_state` , `billing_zip` , `cvv` , `date_added` , `date_updated` , `date_last_used`) VALUES ( '". $currentInsertID ."' , '". $_SESSION['REGISTER']['gopher-pro'] ."' , '". $_POST['creditCardNumber'] ."' , '". $_POST['expDateMonth'] ."' , '". $_POST['expDateYear'] ."' , '". $_POST['address1'] ."' , '". $_POST['city'] ."' , '". $_POST['state'] ."' , '". $_POST['zip'] ."' , '". $_POST['cvv2Number'] ."' , '". $todayDate ."' , '". $todayDate ."' , '". $todayDate ."' )";
				
				$run_payment_info = mysql_query($sql_payment_info);
				}
				
					foreach($_SESSION['REGISTER'] as $session_keys => $session_val){
					
						if( $session_keys == 'gig-fb-username' || $session_keys == 'gig-first-name' || $session_keys == 'gig-last-name'){
						
						
						$_SESSION['SESSIONED_USER_PROTYPE'] = 'gopher_pro';
						$_SESSION['SESSIONED_USER_USERNAME'] = $_SESSION['REGISTER']['gig-fb-username'];
						$_SESSION['SESSIONED_USER_FIRSTNAME'] = $_SESSION['REGISTER']['gig-first-name'];
						$_SESSION['SESSIONED_USER_LASTNAME'] = $_SESSION['REGISTER']['gig-last-name'];
						$_SESSION['SESSIONED_USER_LOGINID'] = $currentInsertID;
						
						}else{
							$_SESSION['REGISTER'][$session_keys] = '';
						}
					} //end foreach
					
					
					echo 1;
					
				}else{
				
					echo $messageResponse = "Error: Please Try again later!";
				} //end if run_sql
				
					
			
		} else  {
		
			echo $messageResponse = "Error Payment Process: Please Try again later!";
		}
	
	
	break; // end case signup_gopher
	
	
	case 'signup_gigger' :
	
		//$paymentType = urlencode($_POST['Authorization']);				// or 'Sale'
		$paymentType =	'Sale';
		$firstName =urlencode($_SESSION['REGISTER']['gig-first-name']); //$_SESSION['REGISTER']['gig-first-name']
		$lastName = urlencode($_SESSION['REGISTER']['gig-last-name']);
		$creditCardType = urlencode($_POST['creditCardType']);
		$creditCardNumber = urlencode($_POST['creditCardNumber']);
		$expDateMonth = $_POST['expDateMonth'];
		// Month must be padded with leading zero
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
		
		$expDateYear = urlencode($_POST['expDateYear']);
		$cvv2Number = urlencode($_POST['cvv2Number']);
		$address1 = urlencode($_POST['address1']);
		$address2 = '';
		$city = urlencode($_POST['city']);
		$state = urlencode($_POST['state']);
		$zip = urlencode($_POST['zip']);
		$country = 'US';	// US or other valid country code
		$amount = $_POST['amount'];	//actual amount should be substituted here
		$currencyID = 'USD';// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		
		// Add request-specific fields to the request string.
		$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
					"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
					"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";
		
		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = PPHttpPost('DoDirectPayment', $nvpStr);
		//print_r($httpParsedResponseAr);

		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			$a = urldecode($httpParsedResponseAr['TIMESTAMP']);
			$todayDateTime = date("Y-m-d H:i:s",strtotime($a));
			$todayDate		= date("Y-m-d");
			
			$sql = "INSERT INTO gigger (
							`username` , 
							`password` , 
							`fname` , 
							`lname` , 
							`bdate` , 
							`picture` , 
							`address` , 
							`city` , 
							`state` , 
							`zip` , 
							`email` , 
							`phone` , 
							`about_text` , 
							`expect_text` ,
							`hobbies` ,
							`languages` ,  
							`reg_date` 
							) VALUES ( 
							'". $_SESSION['REGISTER']['gig-fb-username'] ."' , 
							'". $_SESSION['REGISTER']['gig-fb-password'] ."' , 
							'". $_SESSION['REGISTER']['gig-first-name'] ."' , 
							'". $_SESSION['REGISTER']['gig-last-name'] ."' ,
							'". $_SESSION['REGISTER']['inputDate'] ."' , 
							'".$_SESSION['REGISTER']['profile_image']."', 
							'". $_SESSION['REGISTER']['gig-street-address'] ."' , 
							'". $_SESSION['REGISTER']['gig-city'] ."' , 
							'". $_SESSION['REGISTER']['gig-state'] ."' , 
							'". $_SESSION['REGISTER']['gig-zip-code'] ."' , 
							'". $_SESSION['REGISTER']['gig-email-addres'] ."' , 
							'". $_SESSION['REGISTER']['gig-telephone'] ."' , 
							'". $_SESSION['REGISTER']['gig-about-me'] ."' , 
							'". $_SESSION['REGISTER']['gig-expection'] ."' ,
							'". $_SESSION['REGISTER']['gig-hobbies'] ."' ,
							'". $_SESSION['REGISTER']['gig-languages'] ."' ,  
							'". $todayDateTime ."' 
							)";
			
			
			$run_sql = mysql_query($sql);
			$currentInsertID = mysql_insert_id();
			
			if($run_sql){
			
				$srcDir = '../admin/'.TEMP; 
				makeThumbnail($_SESSION['REGISTER']['profile_image'], $srcDir , '../admin/'. MEMBER_PROFILE_IMAGES .'' , 76 , 76 );
				
				unlink('../admin/'.TEMP.$_SESSION['REGISTER']['profile_image']);
				
				if($_POST['keep_cc_info'] == 'true'){
				
				$sql_payment_info = "INSERT INTO payment_profiles (`gg_id` , `gg_type` , `cc_number` , `cc_exp_month` , `cc_exp_year` , `billing_address` , `billing_city` , `billing_state` , `billing_zip` , `cvv` , `date_added` , `date_updated` , `date_last_used`) VALUES ( '". $currentInsertID ."' , '". $_SESSION['REGISTER']['gopher-pro'] ."' , '". $_POST['creditCardNumber'] ."' , '". $_POST['expDateMonth'] ."' , '". $_POST['expDateYear'] ."' , '". $_POST['address1'] ."' , '". $_POST['city'] ."' , '". $_POST['state'] ."' , '". $_POST['zip'] ."' , '". $_POST['cvv2Number'] ."' , '". $todayDate ."' , '". $todayDate ."' , '". $todayDate ."' )";
				
				$run_payment_info = mysql_query($sql_payment_info);
				}
				
				
					foreach($_SESSION['REGISTER'] as $session_keys => $session_val){
					
						if( $session_keys == 'gig-fb-username' || $session_keys == 'gig-first-name' || $session_keys == 'gig-last-name'){
						
						
						$_SESSION['SESSIONED_USER_PROTYPE'] = 'gigher_pro';
						$_SESSION['SESSIONED_USER_USERNAME'] = $_SESSION['REGISTER']['gig-fb-username'];
						$_SESSION['SESSIONED_USER_FIRSTNAME'] = $_SESSION['REGISTER']['gig-first-name'];
						$_SESSION['SESSIONED_USER_LASTNAME'] = $_SESSION['REGISTER']['gig-last-name'];
						$_SESSION['SESSIONED_USER_LOGINID'] = $currentInsertID;
						
						}else{
							$_SESSION['REGISTER'][$session_keys] = '';
						}
					
					} // end foreach
					
					echo 1;
					
				}else{
				
					echo $messageResponse = "Error : Please Try again later!";
				} //end if $run_sql
				
					
			
		} else  {
		
			echo $messageResponse = "Error Payment Process: Please Try again later!";
		}//end if SUCCESS
	
	break ; //end case signup_gigger
	
	
	case 'showoffers':
		$sql = "SELECT *,(SELECT headline FROM gigs WHERE id = '". $_POST['gig_id'] ."') as headline FROM gopher_apps WHERE gig_id = '". $_POST['gig_id'] ."' ";
		$run_sql = mysql_query($sql);
		$count_offers = mysql_num_rows($run_sql);
		?>
		<ul class="offer-for round2-list">
		<?php
		if($count_offers > 0):
			$no = 0;
			while($offerData = mysql_fetch_assoc($run_sql)):
				
				if($no == 0)
				  echo '<h3>Offers for: '. $offerData['headline'] .' </h3>';
			?>
			
				<li>
					<div class="info">
						<img src="<?php echo returnProfileImage('gopher' , $offerData['gopher_id']); ?>" alt="Gig Image" class="gig-img" />
						<p class="posted">Posted by:</p>
						<p class="author">Brock French</p>
						<p class="rate">
							  <span class="on">&nbsp;</span>
							  <span class="on">&nbsp;</span>
							  <span class="on">&nbsp;</span>
							  <span class="on">&nbsp;</span>
							  <span class="off">&nbsp;</span>
						</p>
						<p class="reviews">Reviews: <span>6</span></p>
					</div><!-- end of info div -->
						
						<h5>Message</h5>
						<p>Hey Rod, I am the perfect guy for the job. I work hard and fast. I will make sure you are 100% satisfied before I leave.</p>
						<h5>My Latest Review</h5>
						<p>Brock is awesome. He was on time, worked hard and got the job done. I will hire him again.</p>
						<div class="ajaxResponder resp_<?php echo $offerData['gopher_id']; ?>" style="display:none;">Please Wait!</div>
						<p class="btns">
							<a href="#" class="profile">Profile</a>
							  <a href="javascript:void(0);" onclick="hireMe('<?php echo ABSOLUTE_PATH;?>' , <?php echo $offerData['gig_id']; ?> , <?php echo $offerData['gopher_id'];?>);" class="pick">Pick Me</a>
						</p>
			 </li>
		 
			<?php 
			$no++;
			endwhile;
		
		else:
			?>
				<li style="font-size: 36px;width: 833px;text-align: center;">No Offer posted against this Gig Yet!</li>
			<?php 
		endif;
		
		?>
		</ul>
		<?php 
		  
	break; //end case showoffers
	
	case 'hire_process':
		$todayDate = date('Y-m-d');
		
		
		$gopher_needed_check = attribValue("gigs" , "gophers_needed" , "WHERE id = '".$_POST['gig_id']."' ");
		$count_gophers = attribValue("hire_list" , "COUNT(*)" , "WHERE gig_id = '".$_POST['gig_id']."' ");
		
		if($count_gophers < $gopher_needed_check):
		
			$chekallready = attribValue("hire_list" , "id" , "WHERE gig_id = '".$_POST['gig_id']."' AND gg_id =  '".$_SESSION['SESSIONED_USER_LOGINID']."' AND gopher_id = '".$_POST['gopher_id']."' ");
			
			if($chekallready > 0):
				echo 0;
				exit;
			endif;
		
			$sql = "INSERT INTO hire_list (`gig_id` , `gg_id` , `gopher_id` , `hire_date`) VALUES ('".$_POST['gig_id']."','".$_SESSION['SESSIONED_USER_LOGINID']."','".$_POST['gopher_id']."','".$todayDate."')";
			$run_sql = mysql_query($sql);
			
			if($run_sql):
			
				echo 1;
				
			endif;
			
		else:
			
			echo 2;
			
		endif;
	
	break; //end case hire_process
	
	case 'repost_gig':
		$toDayDate = date('Y-m-d');
		$keep_active_date = attribValue("gigs" , "keep_active" , "WHERE id = '". $_POST['gig_id'] ."' ");
		if($toDayDate > $keep_active_date){
			$extend_a_week = date('Y-m-d' , strtotime($keep_active_date . " +1 week"));
			$sql = "UPDATE gigs SET keep_active = '". $extend_a_week ."' WHERE id = '". $_POST['gig_id'] ."' ";
			$run_sql = mysql_query($sql);
				if($run_sql){
					echo 1;
					}
		}else
			echo 0;
	
	break; //end case repost_gig
	

	

endswitch;

?>