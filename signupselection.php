<?php

require_once('admin/database.php');
require_once('site_functions.php');

include('includes/header.php'); 

?>

<div class="homeBanner">
		<div align="center" class="msgclass"><a href="<?php  echo ABSOLUTE_PATH; ?>signup.php"><img src="<?php echo IMAGE_PATH; ?>login_icon.gif" alt="" /> Member signup</a> | <a href="<?php  echo ABSOLUTE_PATH; ?>signup.php?type=p"><img src="<?php echo IMAGE_PATH; ?>signup_icon.gif" alt="" /> Promoter Signup</a></div>
</div>
			
<?php

include('includes/footer.php');
?>