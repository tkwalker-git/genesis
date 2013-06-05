<?php
	

	if ( $_POST['page_id'] != "" ) {
		$occupiedAppsArr = array();
		$fb_page_id = $_POST['page_id'];
		$sql = "select * from fanpages where member_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' AND fb_page_id='". $fb_page_id ."'";
		$res = mysql_query($sql);
		while ( $pages  = mysql_fetch_assoc($res ) ) 
			//echo $pages['fb_app_id'];
			$occupiedAppsArr[] = attribValue('fb_apps', 'id', "where app_id = '". $pages['fb_app_id'] ."'"); ;

		if ( count( $occupiedAppsArr) > 0 ) 
			$occupiedApps = implode("-",$occupiedAppsArr);
		
		?>
		<script>
			window.location.href='promote_step3.php?already=<?php echo $occupiedApps;?>&pageid=<?php echo $fb_page_id;?>';
		</script>
		<?php
			
	}
	
?>

<style>
.whiteMiddle .evField {
    color: #00ABDF;
    float: left;
    font-size: 12px;
    font-weight: bold;
    padding: 11px 7px 11px 0;
	}
.grayRoundBox {
    width: 909px;
	}
.integrate{
	background:#c0c0c0;
	color:#FFFFFF;
	padding:10px 30px;
	font-size:20px;
}
.dash_menu{
	position:relative;
	height:67px;
	}
	
.dash_menu table{
	}
	
.dash_menu td{
	padding:0 12px;
	height:64px;
	text-align:center;
	vertical-align:text-bottom;
	}
	
.dash_menu .bordr{
	border-right:#c1c1c1 solid 1px;
	}
	
.dash_menu td a{
	float:left;
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
	background:url(../images/dashB_bar.gif) repeat-x;
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
	
.recBox .rBox{
	padding:17px 10px 10px 26px
}
</style>



			
					<div class="recBox">
						<div class="rBox">
							
							<table width="100%" align="center" cellpadding="5" cellspacing="0" >
								<tr>
									<td width="50%" valign="top">
										<div class="evField" style="width:100%; text-align:center; font-size:20px!important">Select Fan Page</div><br />
										Below dropdown will show your available Fan pages on Facebook. Please select the page where you want to add E-Flyer and Press next button. You are almost there...
										<br /><br />
										<a href="promote_step1.php" style="color:#0066FF"><strong>Click Here</strong></a> if the Fan Page list is not updated OR you do not see your fan page in the list below.</a>
										<br /><br />
										<div class="evField" style="width:100%; text-align:left; font-size:15px!important"></div><br />
										
										<?php
											
										$suc = false;
										
										$sql1 = "select * from fb_user_pages where member_id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'";
										$rs1 = mysql_query($sql1);
										if ( mysql_num_rows ($rs1 ) > 0 ) {
											
										?>
											<form method="post" enctype="multipart/form-data" name="paeSelector" >
											<strong>Available Fan Pages: </strong><br /><br />
											<select name="page_id" id="page_id" style="width:300px" >
											<option selected="selected" value="-1">--Select--</option>
											<?php
											while ($r1 = mysql_fetch_assoc($rs1) ) {
											?>
												<option value="<?php echo $r1['fb_page_id'] ;?>"><?php echo $r1['fb_page_name'] ;?></option>
											<?php
											}
											?>
											</select>
											<br><br />
											<input type="image" src="../images/singup-next.png" />

											</form>
										<?php	
										} else {
										?>		
										<div class="evField" style="width:100%; text-align:left; font-size:25px!important; color:#FF0000; padding:30px">OOPS. You don't have any Fan Page. Click <a href="promote_step1.php" style="color:#0066FF"><strong>here</strong></a> to fetch your Fab Pages</div><br />
										<?php
										}
										?>
									</td>
								</tr>
							</table>
							</div>
						</div>
           

<div class="clr"></div>
	