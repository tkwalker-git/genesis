<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
///////////////////////  $user_id  loggedin member id

?>

<script>
var span	=	$('span','.voting_person');
$(span).click(function(){
var id		=	$(this).attr('id');

var sp = id.split('-');

var entry_id	=	sp[0];
var poll_id		=	sp[1];
var team		=	sp[2];
$.ajax({  
			type: "GET",  
			url: "ajax/vote.php",  
			data: "entry_id=" + entry_id + "&poll_id=" + poll_id + "&team=" + team,  
			dataType: "text/html",  
				beforeSend: function()
			{
				showOverlayer('ajax/loader.php');
			},
			success: function(html){
			var a = html.split('-');
			
			$("#teamA").html(a[0]+' Votes');
			$("#teamB").html(a[1]+' Votes');
			
			}, 

			complete: function()
			{
				hideOverlayer();
			},
	   	});



});
	</script>
	
	<?php
		$poll_id = $_GET['poll_id'];
		$session_id = session_id();
			   $r = mysql_query("select * from `poll_match` where `poll_id`='$poll_id'");
			   if(mysql_num_rows($r)){
			 		while($ro = mysql_fetch_array($r)){
			  ?>
                <?php $result = getEntry($ro['teamA_id']);
				  $poll_match_id = $result['id'];
				  $rs = mysql_query("select * from `person_voted` where `session_id`='$session_id' && `poll_match_id`='$poll_match_id'");
				  if(mysql_num_rows($rs)){
				  
				  $votes = gettotalVotes($ro['id']);
				  
				  ?>
                <div class="voting_person">
                  <ul>
                    <li><img src="<?php echo IMAGE_PATH; ?>pic.png" width="183" height="177" border="0" /></li>
                  </ul>
                  
				   <div id="teamA"><?php echo $votes['a'];?> Votes</div>
				   
				   </div>
                <!-- end voting_person -->
                <div class="voting_person">
                  <ul>
                    <li><img src="<?php echo IMAGE_PATH; ?>pic2.png" width="183" height="177" border="0" /></li>
                  </ul>
				  <div id="teamB"><?php echo $votes['b'];?> Votes</div>
				  
				  </div>
				  <?php
				  }
				  else{
				   ?>
				   <div class="voting_person">
                  <ul>
                    <li><img src="<?php echo IMAGE_PATH; ?>pic.png" width="183" height="177" border="0" /></li>
                  </ul>
                  <div id="teamA">
				  <span id="<?php echo $result['id']; ?>-<?php echo $ro['poll_id'];?>-a">
                  <?php
				  echo $result['name'];
				  ?>
                  Win</span></div> </div>
                <!-- end voting_person -->
                <div class="voting_person">
                  <ul>
                    <li><img src="<?php echo IMAGE_PATH; ?>pic2.png" width="183" height="177" border="0" /></li>
                  </ul>
                  <?php $result = getEntry($ro['teamB_id']); ?>
				  <div id="teamB">
                  <span id="<?php echo $result['id']; ?>-<?php echo $ro['poll_id'];?>-b">
                  <?php
				  echo $result['name'];
				  ?>
                  Win</span>
				  </div></div>
				  <?php } ?>
                <!-- end voting_person -->
                <div class="clr"></div>
				<?php }
				
				}
				else{
				
				echo "<div style='padding:30px'><strong>No record found.</strong></div>";
				} ?>