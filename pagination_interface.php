<?php if(!isset($_REQUEST['page'])){
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




	<table  border="0" cellspacing="0" cellpadding="0" style="font-size:14px;">
<tr>
	<?php
	if($page_num <=1)
	{?> 
	<?php }
	else
	{ ?>
<td height="18" align="right" valign="middle"><a style="cursor:pointer;" onclick="back.submit();">Back</a>&nbsp;&nbsp;</td>                                 
	<?php }?>
		<td height="18" >
		<table  border="0" cellspacing="5" cellpadding="0">
     	<tr>
         	<td height="18" align="center"  valign="middle"><b><?php if($total_pages<=5){for ($i=1;$i<=$total_pages;$i++){?>
			
			
				<a onclick="document.mdl.page.value=<?php echo $i;?>; mdl.submit();" style="cursor:pointer;">
						  <?php if($page_num==$i){echo "<font color='#009734'><b>".$i."</b></font>";}
						  else{echo $i;}?> 
				</a>
					<?php echo "&nbsp;|&nbsp;"; } }else{ 
				if($page_num-5<1){$pno=1;}else{$pno=$page_num-5;}
				if($page_num+4>$total_pages){$epno=$total_pages;}else{$epno=$page_num+4;}
				for ($i=$pno;$i<=$epno;$i++){?>
				
				<a onclick="document.mdl.page.value=<?php echo $i;?>; mdl.submit();" style="cursor:pointer;">
					<?php if($page_num==$i){echo "<font color='#009734'><b>".$i."</b></font>";}else{echo $i;}?>
				 </a>
				 <?php echo "&nbsp;|&nbsp;"; } }?></b></td>
		</tr> 
    </table></td>
	<?php
		if($page_num >= $total_pages)
		{?>
	<?php }
		else
		{?>
<td  align="left" valign="middle">&nbsp;&nbsp;<a  style="cursor:pointer;" onclick="next.submit();">Next</a></td> 
<?php } ?>
</tr>
</table>
<br/>
