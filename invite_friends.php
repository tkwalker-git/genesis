<?php
	
	require_once('admin/database.php'); 
	
	if ( $_SESSION['LOGGEDIN_MEMBER_ID'] > 0 ) {
	
?>
<br />
<br />
<br />
<a href="javascript:void(0)" onclick="windowOpener(525,625,'Terms and Conditions','cimport/invite_friends.php')">Invite Friends</a>
<?php } else echo 'Login Before you invite'; ?>

<script>
function windowOpener(windowHeight, windowWidth, windowName, windowUri)
{
    var centerWidth = (window.screen.width - windowWidth) / 2;
    var centerHeight = (window.screen.height - windowHeight) / 2;

    newWindow = window.open(windowUri, windowName, 'scrollbars=1,width=' + windowWidth + 
        ',height=' + windowHeight + 
        ',left=' + centerWidth + 
        ',top=' + centerHeight);

    newWindow.focus();
    return newWindow.name;
}
</script>