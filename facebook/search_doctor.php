<?php
require_once('admin/database.php');
require_once('site_functions.php');


if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";


$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];

if($_SESSION['usertype']=='clinic' || $_SESSION['usertype']=='doctor'){
$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
}else {
$member_full_name = attribValue('patients', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
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
          
          	<div class="head_new">SEARCH YOUR CARETAKER</div>
            
            	<div class="recBox">
                    
                    <div style="padding:0 10px;"><br />
                    
					<div class="error" style="color:red; font-size:18px; text-align:center;"><?php echo $err; ?></div>
	

					<div class="ew-heading">SEARCH BY NAME OR SPECIALIZATION </div>

                    <form action="" method="post" name='profrm' enctype="multipart/form-data">
                
                      <div class="clr"></div>
                      
                        <div class="editProox">
                      
                     
                            <div style="padding: 20px 45px; margin-bottom:25px; border:1px solid #E5E5E5;">
                        
                                    
                                <div class="editProox">
                                    <div class="evField" style="width:109px;">Enter your search</div>
                                    <div class="evLabal" style="width:450px;">
                                    <input type="text" name="keywords" class="evInput" style="width:300px; height:20px" value='<?php echo $keywords;?>' />
                                    <input type="submit" name="search_doctor" value="search_doctor" />
                                    </div>
                                    <div class="clr"></div>
                                
                                </div>
                                    
                                <div style="line-height:30px; width:75%; float:left">
                                    <input style="margin:0 5px;" type="checkbox" name="search_by[]" value="1" />
                                    <strong>Search By Doctor Name</strong>
                                </div>
                                    
                                <div style="line-height:30px; width:75%; float:left">
                                    <input style="margin:0 5px;" type="checkbox" name="search_by[]" value="2" />
                                    <strong>Search By Doctor Specialization</strong>
                                </div>
                                
                                <div class="clr"></div>
                            </div>
                            
                            
                            
                        <div class="clr"></div>
                        
                        <?php 
							if(isset($_POST["search_doctor"])){
						?>
                        <div id="result_box">
                        
                        	<ul>
                            	<?php 
									
										//print_r($_POST);
										$sql = "SELECT * FROM `doctors` WHERE `first_name` LIKE '%". $_POST["keywords"] ."%' OR `last_name` LIKE '%". $_POST["keywords"] ."%'";
										$res = mysql_query($sql);
										$cou = mysql_num_rows($res);
										$list = 1;
										if($cou > 0){
											while($doctors = mysql_fetch_assoc($res)){
												
										
	

										
										if($list&1)
											$class = 'odd';
										else
											$class = 'even';
								?>
                                <li class="<?php echo $class; ?>">
                                
                                	<div class="result_list">
                                    
                                    	<div class="doc_info"><?php echo $doctors["first_name"]; ?> <?php echo $doctors["last_name"]; ?></div>
                                        
                                        <div class="doc_invit"><a href="<?php echo ABSOLUTE_PATH .'doctor_profile.php?id='. $doctors["id"]; ?>">View Profile</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="sendInvitationDoctor(<?php echo $doctors["id"]; ?>)" id="invite_id_<?php echo $doctors["id"]; ?>">Send Invite</a></div>
                                        
                                        <div class="clr"></div>
                                    
                                    </div>
                                
                                </li>
                                
                                <?php $list++;}}else{ ?>
									
								 <li class="odd" style="text-align:center">No one Doctor found by "<?php echo $_POST["keywords"]; ?>"</li>	
								<?php } ?>
                                
                            </ul>
                        	<?php } ?>
                        </div> <!--end result_box-->
                        
                        
                    </div> <!--end editProox-->
                 
                    
                    
                    </form>
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