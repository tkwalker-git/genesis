<?php require_once('admin/database.php');?>
<?php require_once('includes/header.php');
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";
?>
<div class="topContainer">
  <div class="welcomeBox"></div>
  <script language="javascript">
	function submitform(){
	document.forms["searchfrmdate"].submit();
	}
	</script>
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="eventMdlBg">
      <div class="eventMdlMain">
		  <span class="viewevents_title">
	  		My <strong>Products</strong></span>
        <div class="featuresBoxNew">
          <div class="featuresBotBgNew">
            <div class="featuresTopBgNew">
		<div class="creatProduct">
				<?php 
				if ($_REQUEST['msg']){?>
				  <span style="font-size:16px; line-height:25px">
		<strong><?php echo $_REQUEST['msg']; ?></strong></span><br><br>
		<?php } ?>
		
		<a href="creat_product.php"><u><strong>Add New Product</strong></u></a><br /><br />
		<?php
		$res = mysql_query("select * from `products` where `user_id`='$user_id'");
		while($row = mysql_fetch_array($res)){
		echo $row["name"].' &nbsp; <a href="creat_product.php?id='.$row["id"].'" style="color:#5048FB">View / Edit</a><br><br>';
		}
		?>
			  </div>
			
			</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once('includes/footer.php');?>