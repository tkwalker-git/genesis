<?php
	
	require_once('admin/database.php');
	require_once('site_functions.php');

	
	$direction 		= $_POST['direction'];
	$page			= $_POST['page'];
	
	$sql = "select * from hangout_group ";
	$rsd = mysql_query($sql);
	
	$total_rec		= mysql_num_rows($rsd);
	$total_pages 	= ceil($total_rec/1);
	
	$pagenum = (int) $page;
	
	if ($direction == 'next') {
		$start = $pagenum * 1 ; 
		$pagenum++;
	} else {
		$pagenum--;
		$start = ($pagenum-1) * 1 ; 
	}
			
	$limit = ' LIMIT '. $start . ' , 1';
	
	$sql = "select * from `hangout_group` where `member_id`='$user_id' order by `id` ASC " . $limit;
	$rsd = mysql_query($sql);
	while($row=mysql_fetch_array($rsd))
	{
	$bc_group_id	=	$row['id'];
	$bc_group_name	=	$row['name'];
	}
	?>
    <div class="wallTitle"><?php echo $bc_group_name; ?></div>
    <div class="flayerCenter" style="float:left; width:auto">
      <div class="menu" style="width:542px">
        <ul>
          <li class="firstOver" id="first"> <a class="flayerMenuActive" href="#">Group Members</a> </li>
          <li><a href="#">Suggest Event</a></li>
          <li><a href="#">Suggest Deal</a></li>
          <li class="last" id="last2"><a onMouseOver="document.getElementById('last2').className='lastOver';" onMouseOut="document.getElementById('last2').className='last';" href="#">Needs Your Response <span class="color2">(2)</span></a></li>
        </ul>
      </div>
      <div class="clr" style="height:14px">&nbsp;</div>
    </div>
    <div class="clr"></div>
    <div class="frndBoxTop">
      <div class="frndBoxBottom">
        <div class="frndBoxMiddle">
          <div class="groupMembers" id="groupMembers">
            <div><b>Click Member To See Profile</b></div>
            <div class="members" id="members">
              <?php getGroupMembers($bc_group_id); ?>
            </div>
          </div>
          <div class="memberProfile"> <img src="images/leftArrow.png" style="left: 23px;position: absolute;top: 41px;">
            <div class="inr" id="inr">
              <?php
			  $bc_first_member_id = attribValue("group_members","member_id","where group_id='".$bc_group_id . "' ORDER BY `id` ASC LIMIT 0,1");
			  getGroupMemberProfile($bc_first_member_id);
			  ?>
            </div>
            <div align="right"><br>
			
			
			<?php if ($pagenum > 1) { ?>
			 <a href="javascript:void(0)" onclick="loadGroups('<?php echo ABSOLUTE_PATH;?>','prev',<?php echo $pagenum;?>)"><img src="<?= IMAGE_PATH; ?>prev.png"></a>
		<?php } else { ?>
			<img src="<?= IMAGE_PATH; ?>prevdisable.png">
		<?php } ?> &nbsp; &nbsp;
		
		
		<?php if ( $pagenum < $total_pages ) { ?>
		<a href="javascript:void(0)" onclick="loadGroups('<?php echo ABSOLUTE_PATH;?>','next',<?php echo $pagenum; ?>)"><img src="<?= IMAGE_PATH; ?>nxt.png"></a>
		<?php } else { ?>
			<img src="<?= IMAGE_PATH; ?>nxtdisable.png">
		<?php } ?>
		
		
			 </div>
          </div>
		  
          <div class="clr"></div>
        </div>
      </div>
    </div>
	