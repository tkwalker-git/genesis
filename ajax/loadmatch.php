<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');
	
///////////////////////  $user_id  loggedin member id

	$type = $_GET['type'];
?>

<script>

var span	=	$('span','.voting_person');
$(span).click(function(){
var id		=	$(this).attr('id');
var abs_url = $('#absolute').val();

var sp = id.split('-');

var team_id		=	sp[0];
var poll_id		=	sp[1];


$.ajax({  
			type: "GET",  
			url: abs_url+"ajax/vote.php",  
			data: "team_id=" + team_id + "&poll_id=" + poll_id ,
			dataType: "text/html",  
				beforeSend: function()
			{
				showOverlayer(abs_url+'ajax/loader.php');
			},
			success: function(html){
			
			$('#showmatch').html(html);
			/*var a = html.split('-');
			
			$("#teamA").html(a[0]+' Votes');
			$("#teamB").html(a[1]+' Votes');*/
			
			}, 

			complete: function()
			{
				hideOverlayer();
			}
	   	});



});
</script>
	<input type="hidden" value="<?php echo ABSOLUTE_PATH; ?>" name="absolute" id="absolute" />
<?php
			$poll_id = $_GET['poll_id'];
	echo	getPollTeams($poll_id,$type);
?>