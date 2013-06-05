<?php
require_once('admin/database.php');
require_once('site_functions.php');


if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";


$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

if($_GET["id"]){
	$patient_id = attribValue("invitations" , "patient_id" , "where id = '". $_GET["id"] ."'");
	$sql = "SELECT p.id , p.firstname , p.lastname , p.email , p.sex , p.address , p.city , p.state , p.country , p.phone , i.user_id , i.eligibility_began , i.deductible  , i.plan_coverage , i.max_out_of_pocket , i.plan_year , i.yearly_maximum , i.lifetime_maximum  FROM `patients` AS `p` , insurance AS `i` WHERE  p.id = '". $patient_id ."' AND i.user_id = '". $patient_id. "'";
	$res = mysql_query($sql);
	
	$patient = mysql_fetch_assoc($res);	
	
}

if(isset($_GET["invite"])){

		$sql = "INSERT INTO `invitations` (`patient_id` , `doctor_id` , `status`) VALUES ('". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' , '". $_GET["invite"] ."' , 0)";
		$res = mysql_query($sql);
		
		if($res){
			
			/*$dr_email = attribValue("doctors" , "email" , "where id = '". $_GET["invite"] ."'");
			$dr_name = attribValue("doctors" , "CONCAT(`first_name` , ' ' , `last_name`)" , "WHERE `id` = '". $_GET["invite"] ."'");
			$pat_name = attribValue("patients" , "CONCAT(`firstname` , ' ' , `lastname`)" , "WHERE `id` = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
			$pat_name = attribValue("patients" , "CONCAT(`firstname` , ' ' , `lastname`)" , "WHERE `id` = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
			$subject = "Invitation from a patient";
			$message = 'Dear '. $dr_name .' ,<br><br>';
			$message .= 'You have recieved a invitation as a caretaker from '. $pat_name .'<br>';
			$message .= 'Please login to your account by click the url below. <br><br> <a href="'. ABSOLUTE_PATH  .'">'. ABSOLUTE_PATH  .'</a><br><br>';
			$message .= 'Message footer';
			
			
			$semi_rand = md5(time()); 
    		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
			 
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=iso-8859-1";
			$headers[] = "From: ". $pat_name ." <". $pat_name .">";
			$headers[] = "Reply-To: Recipient Name <no-reply@example.com>";
			$headers[] = "Subject: {$subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();
			
			
			$ok = @mail($dr_email, $subject, $message, implode("\r\n", $headers));
			
			if($ok){
				$sucmess = 1;	
			}else{
			
				$sucmess = 0;*/	
				$sucmess = 1;
				
			//}
			
		}else
			$sucmess = 0;
	
}


if(isset($_GET["accept"])){

	$sql = "UPDATE `invitations` SET `status` = 1 WHERE `id` = '". $_GET["accept"] ."'";
	$res = mysql_query($sql);
	
	if($res){
	
		$err = "Invitation accepted successfully!";	
		
	}	
	
}

if(isset($_GET["reject"])){

	$sql = "DELETE FROM `invitations` WHERE `id` = '". $_GET["reject"] ."'";
	$res = mysql_query($sql);
	
	if($res){
	
		$err = "Invitation rejected successfully!";	
		
	}	
	
}


$meta_title	= 'Dashboard';
include_once('includes/header.php');

?>

<style>
.dash_menu{
	position:relative;
	height:67px;
	}

.dash_menu table{
	}

.dash_menu td{
	padding:0 25px;
	height:64px;
	text-align:center;
	vertical-align:text-bottom;
	}

.dash_menu .bordr{
	border-right:#c1c1c1 solid 1px;
	}

.dash_menu td a{
	/*float:left;*/
	font-size:11px;
	font-weight:bold
	}

.dash_menu td a:hover{
	text-decoration:none;
	color:#0598fa
	}

.dash_menu td a img{
	margin-bottom:3px;
	}

.head_new{
	font-size:18px;
	background:url(images/dashB_bar.gif) repeat-x;
	padding:8px 12px;
	color:#ffffff;
	border-left:solid 1px #cbcbcb;
	border-right:solid 1px #cbcbcb;
	}

.recBox{
	border:#cecece solid 1px;
	border-top:none;
	background:#f6f6f6;
	}
	

.recBox .yellow_bar{
	background: url("images/yellow_bar.gif") repeat-x scroll 0 0 transparent;
    border-bottom: 1px solid #CBCBCB;
    color: #231F20;
    font-size: 14px;
    font-weight: bold;
    height: 25px;
    padding:12px 0 0;
	}

.eventManger_left{
	background:url(images/eventManger_leftBg.gif) repeat-x #1c6722;
	width:186px;
	float:left;
	min-height:597px;
	padding-top:40px;
	}

.eventManger_right{
	width:719px;
	float:left;
	border-left:#CBCBCB solid 1px;
	min-height:636px
	}

.eventManger_left ul{
	margin:0;
	padding:0;
	}

.eventManger_left ul li{
	list-style:none;
	}

.eventManger_left ul .icon_myEvents{
	background:url(images/icon_myEvents.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_venues{
	background: url("images/icon_venues.png") no-repeat scroll 9px 2px transparent;
	line-height: 30px;
	}

.eventManger_left ul .icon_manageteam{
	background:url(images/icon_manageTeam.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_contact{
	background:url(images/icon_contact.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_reports{
	background:url(images/icon_reports.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul .icon_promotions{
	background:url(images/icon_promotions.png) no-repeat scroll 9px 2px transparent;
	line-height:30px;
	}

.eventManger_left ul li a{
	color:#231f20;
	font-size:14px;
	padding:4px 0 4px 38px;
	display:block;
	font-weight:bold;
	}

.eventManger_left ul li a:hover{
	text-decoration:none;
	}

.eventManger_left ul .border{
	border-bottom: 1px solid #AEDAB0;
    border-top: 1px solid #38893A;
    margin:6px 0 12px;
	height:0px;
	line-height:0;
	padding:0;
	}

.eventManger_left ul ul{
/*	padding-left:10px; */
	}

.eventManger_left ul ul a:hover, .eventManger_left ul ul .active{
	background:#558c58;
	display:block;
	}

.eventManger_left ul ul li a{
	padding:8px 0 8px 45px;
	font-size:12px;
	line-height:normal
	}

.ev_eventBox{
	border-bottom:#c1c1c1 solid 1px;
	padding:13px 10px;
	}

.event_name{
	font-size:12px;
	font-weight:bold
	}

.event_name a{
	color:#006695;
	text-decoration:none;
	}

.event_name a:hover{
	text-decoration:underline;
	}

.event_info{
	line-height:19px
	}

.ev_eventBox span{
	color:#6d6e71
	}

.ev_eventBox ul{
	margin:0;
	padding:4px 0 0 13px;
	line-height:16px;
	color:#6d6e71
	}

.sales{
	color:#289701;
	font-weight:bold
	}

.eventManger_right table a{
	color:#006695;
	text-decoration:underline

	}

.eventManger_right table a:hover{
	text-decoration:none;
	}

table .dele{
	line-height:16px;
	}

.event_name div{
	color:#000000}
	
#result_box{}

#result_box ul{
	background: none repeat scroll 0 0 #D7D7D7;
	box-shadow:0 0 5px -2px inset;
    margin-bottom: 10px;
    padding: 10px;}

#result_box ul li{
	background-color:#FFFFFF;
	margin-bottom: 1px;}

#result_box ul li.odd{
	background-color: #E5E5E5;}

#result_box ul li.even{
	background-color: #E5E5E5;}

.result_list div.doc_info{
	float: left;
    padding: 3px;
    width: 712px;}

.result_list div.doc_invit{
	float: left;
    padding: 3px;}
	
input[name=search_doctor]{
	background: url("images/search_btn_smll.gif") no-repeat scroll 0 0 transparent;
    border: medium none;
    cursor: pointer;
    font-size: 0;
    padding: 11px 30px;}

</style>


<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>js/jquery-1.4.min.js"></script>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">

		$(document).ready(function() {

				$("#various").fancybox({
					'autoScale'			: true,
					'transitionIn'	: 'elastic',
					'transitionOut'	: 'elastic',
					'type'				: 'iframe'
				});

		});

	$(document).ready(function() {
		$("a#profileImage").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});

	$('.video').click(function() {
		var vSrc	= $(this).attr('src');
		var vName	= vSrc.split('/hqdefault.jpg');
		vName		= vName[0].split('vi/');
		vName		= vName[1];
		$('#videoArea').slideDown(1000);
		$("#videoArea span").html('<iframe width="824" height="540" src="http://www.youtube.com/embed/'+vName+'" frameborder="0" allowfullscreen></iframe>');
		$('html,body').animate({
			        scrollTop: $("#videoArea").offset().top},
        			'slow');
	});

	$('.closeVd').click(function() {
		$('#videoArea').slideUp(1000);
	});

});	// end $(document).ready
</script>

<link href="<?php echo ABSOLUTE_PATH; ?>dashboard1.css" rel="stylesheet" type="text/css">
<div class="topContainer">
  <div class="welcomeBox"></div>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="clr"></div>
    <div class="gredBox">
      <?php include('dashboard_menu_tk.php'); ?>
      <div class="whiteTop">
        <div class="whiteBottom">
          <div class="whiteMiddle" style="padding-top:7px;">
          
          	<div class="head_new">PATIENT PROFILE</div>
            
            	<div class="recBox">
                    
                    <div style="padding:0 10px;"><br />
                    
					<div class="error" style="color:red; font-size:18px; text-align:center;"><?php echo $err; ?></div>
	

					<div class="ew-heading">PERSONAL INFORMATIONS </div>

                    <table width="100%" border="0" cellspacing="10" cellpadding="0" style="border-top:1px solid #e5e5e5">
                      <tr>
                        <td width="150"><strong>First Name:</strong></td>
                        <td><?php echo $patient["firstname"]; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Last Name:</strong></td>
                        <td><?php echo $patient["lastname"]; ?></td>
                      </tr>
                      <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo $patient["email"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo $patient["phone"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Gender:</strong></td>
                        <td><?php echo $patient["sex"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Address:</strong></td>
                        <td><?php echo $patient["address"]; ?></td>
                      </tr>
                      <tr>
                        <td><strong>City:</strong></td>
                        <td><?php echo $patient["city"]; ?></td>
                      </tr>
                      <tr>
                        <td><strong>State:</strong></td>
                        <td><?php echo $patient["State"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Country:</strong></td>
                        <td><?php echo $patient["country"]; ?></td>
                      </tr>
                      
                    </table>
                    
                    
                    <div class="ew-heading">INSURANCE INFORMATIONS </div>
                    
                    <table width="100%" border="0" cellspacing="10" cellpadding="0" style="border-top:1px solid #e5e5e5">
                      <tr>
                        <td width="150"><strong>Eligibility Began:</strong></td>
                        <td><?php echo $patient["eligibility_began"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Deductible:</strong></td>
                        <td><?php echo $patient["deductible"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Plan Coverage:</strong></td>
                        <td><?php echo $patient["plan_coverage"]; ?></td>
                      </tr>
                      
                       <tr>
                        <td><strong>Max Out Of Pocket:</strong></td>
                        <td><?php echo $patient["max_out_of_pocket"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Plan Year:</strong></td>
                        <td><?php echo $patient["plan_year"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Yearly Maximum:</strong></td>
                        <td><?php echo $patient["yearly_maximum"]; ?></td>
                      </tr>
                      
                      <tr>
                        <td><strong>Lifetime Maximum:</strong></td>
                        <td><?php echo $patient["lifetime_maximum"]; ?></td>
                      </tr>
                      
                    </table>
                    
                    
                    <table width="100%" border="0" cellspacing="10" cellpadding="0" style="border-top:1px solid #e5e5e5">
                      <tr>
                        <td>
                        	<?php
							
$invitation_id = attribValue("invitations" , "COUNT(*)" , "WHERE `id` = '". $_GET["id"] ."'  AND `status` = 0");

								if($invitation_id == 1){
							?>
                        <a href="<?php echo ABSOLUTE_PATH; ?>patient_profile_for_doctors.php?id=<?php echo $_GET["id"]; ?>&accept=<?php echo $_GET["id"]; ?>"><strong>Click To Accept Inviation</strong></a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo ABSOLUTE_PATH; ?>patient_profile_for_doctors.php?id=<?php echo $_GET["id"]; ?>&reject=<?php echo $_GET["id"]; ?>"><strong>Click To Reject Inviation</strong></a>
                        
                        <?php }?>
                        </td>
                      </tr>
                      
                    </table>

                    
                    
				</div>
                    
                    
			</div>
            
		 

			

			
			<!-- /videos_boxs -->
			<br class="clear" />
			
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>

<script type="text/javascript">

function sendInvitationDoctor(abs_path , doctor_id){

	$.ajax({
	
		type : 'POST',
		url : abs_path + 'ajax/common.php',
		data : 'action=send_invitation_to_doctor&doctor_id='+doctor_id,
		success : function(data){
		
			if(data == 1){
				
				$("#invite_id_"+ doctor_id).html("Invited");
				$("#invite_id_"+ doctor_id).attr('onclick','').unbind('click');
				
			}else{}
			
		}	
		
	});	
	
}

</script>