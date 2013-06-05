<?php if(!$_REQUEST['page']){
$_REQUEST['page']=1;
}
?>
<form name="mdl" action="" method="post">
<input type="hidden" name="page"  />
</form>
<!--/////// pagination_interface ////////////-->
<form name="back" action="" method="post">

<input type="hidden" name="page"  value="<?php echo $_REQUEST['page']-1;?>" />


</form>

<form name="next" action="" method="post">
<input type="hidden" name="page"  value="<?php echo $_REQUEST['page']+1;?>" />
</form>
