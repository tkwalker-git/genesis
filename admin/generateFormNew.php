<?php

error_reporting(E_ALL ^ E_NOTICE);

$phpFileName = isset($_POST['php_file_name']) ? $_POST['php_file_name'] : time();
$table_name  = $_POST['table_name'];

$form_title = $_POST['form_title'];

$e_label 	= $_POST['e_label'];
$e_name 	= $_POST['e_name'];
$e_object	= $_POST['e_object'];
$e_req 		= $_POST['e_req'];
$e_field	= $_POST['e_field'];
$e_extra	= $_POST['e_extra'];

$element = '';
$phpVars = '';

$errors  = '$errors = array();'. "\n";
$errorList = '$err = \'<table border="0" width="90%"><tr><td class="error" ><ul>\';' . "\n";
$errorList .= 'for ($i=0;$i<count($errors); $i++) {' . "\n";
$errorList .= "\t" . '$err .= \'<li>\' . $errors[$i] . \'</li>\';'. "\n";
$errorList .= '}' . "\n";
$errorList .= '$err .= \'</ul></td></tr></table>\';	'. "\n";

for ($i=0;$i<count($e_field); $i++ )
{
	if ($e_req[$i] == "yes" )
	{
		$errors  .= 'if ($_POST["'.$e_name[$i].'"] == "")' . "\n";
		$errors  .=  "\t" . '$errors[] = "'. $e_label[$i] .' can not be empty";' . "\n";
	}
}


$filedList = '"insert into '. $table_name ;
$filedList 	.= ' (' . implode(",",$e_field) . ') values (';
for ($i=0;$i<count($e_field); $i++ )
	$filedList	.= '\'" . ' . '$bc_'.$e_name[$i] . ' . "\',';

$filedList = substr($filedList,0,-1);
$filedList .= ')";';	

$phpGetID = '$frmID'. "\t" . '='. "\t" .'$_GET["id"];' . "\n";

/* DATABASE HANDLING BASED ON ID*/
$form .= '<form method="post" name="bc_form" enctype="multipart/form-data" action=""  >'. "\n";
$form .= '<input type="hidden" name="bc_form_action" class="bc_input" value="<?php echo $action; ?>"/>' . "\n";
$form .= '<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">' . "\n";
$form .= '<tr class="bc_heading">' . "\n";
$form .= '<td colspan="2" align="left" >'. $form_title .'</td>' . "\n";
$form .= ' </tr>' . "\n";

$form .= '<tr>' . "\n";
$form .= '<td colspan="2" align="center" class="success" ><?php echo $sucMessage; ?></td>' . "\n";
$form .= '</tr>' . "\n";


$phpVars = '';

for ($i=0; $i<count($e_label);$i++)
{
	/* PHP VARIABLES*/
	if ($e_object[$i] == 'file' || $e_object[$i] == 'image' ) {
		$phpVars .= '$bc_'. $e_name[$i] . "\t" . '='. "\t" .'$_FILES["' . $e_name[$i] . '"]["name"];' . "\n";
		
		$fileUpd .= "\t" . "\t" .'if ($_FILES["' . $e_name[$i] . '"]["name"] != "") {' . "\n";
		$fileUpd .= "\t" . "\t" . "\t"  .'$bc_'. $e_name[$i].'  = time() . "_" . $_FILES["' . $e_name[$i] . '"]["name"] ;' . "\n";
		$fileUpd .= "\t" . "\t" . "\t" . 'if ($action1 == "edit") '. "\n";
		$fileUpd .= "\t" . "\t" . "\t" . "\t"  . 'deleteImage($frmID,"'.$table_name.'","'.$e_field[$i].'");'. "\n";
		$fileUpd .= "\t" . "\t" . "\t" . 'move_uploaded_file($_FILES["' . $e_name[$i] . '"]["tmp_name"], '. $e_extra[$i] .' .$bc_'. $e_name[$i].');'. "\n";
		
		$fileUpd .= "\t" . "\t" . "\t" . '$bci_'.$e_name[$i] . ' = \',' .  $e_field[$i] . ' = "\' . '. '$bc_'.$e_name[$i] . ' . \'"' . '\';' . "\n";
		
		$fileUpd .= "\t" . "\t" .'} else {' . "\n" ;
		$fileUpd .= "\t" . "\t" . "\t" . '$bci_'.$e_name[$i] . ' = "";' . "\n";
		$fileUpd .= "\t" . "\t" .'}' . "\n" . "\n";
		
//		$fileUpd .= "\t" . "\t" .'if ($_FILES["' . $e_name[$i] . '"]["name"] == "") ' . "\n";
	}
	else {
		if ($e_object[$i] == 'checkbox') {
			$phpVars .= '$bc_'. $e_name[$i] . "\t" . '='. "\t" .'implode(",",is_array($_POST["'. $e_name[$i] .'"]) ? $_POST["'. $e_name[$i] .'"] : array() );' . "\n";
			$phpVars .= '$bc_tmp_arr_'. $e_name[$i] . ' = array();' ;
			
		} 
		else if ($e_object[$i] == 'date') 
			$phpVars .= '$bc_'. $e_name[$i] . "\t" . '='. "\t" .'date("m/d/Y",strtotime(isset($_POST["' . $e_name[$i] . '"]) ? $_POST["' . $e_name[$i] . '"] : date("m/d/Y") ));' . "\n";
		else
			$phpVars .= '$bc_'. $e_name[$i] . "\t" . '='. "\t" .'$_POST["' . $e_name[$i] . '"];' . "\n";
	}
	
	if ($e_extra[$i] == 'Array')
		$phpVars .= '$bc_arr_'. $e_name[$i] . "\t" . '='. "\t" .'array("Please fill the key,value pair here");' . "\n";
	else if (substr($e_extra[$i],0,2) == 'T:')	{
		$tmparray = explode(":",$e_extra[$i]);
		
		$phpVars .=  "\n" .'$bc_arr_'. $e_name[$i] . "\t" . '='. "\t" .'array();' . "\n";
		$phpVars .= '$arrRES = mysql_query("select '. $tmparray[2] .','. $tmparray[3] .' from '. $tmparray[1] .'");' . "\n";
		$phpVars .= 'while ($bc_row = mysql_fetch_assoc($arrRES) )' . "\n";
		$phpVars .=  "\t" . '$bc_arr_'. $e_name[$i] . '[$bc_row["'. $tmparray[2] .'"]] = $bc_row["'. $tmparray[3] .'"];' . "\n" . "\n";
	} else if (substr($e_extra[$i],0,4) == 'SQL:')	{
		$tmparray = explode(":",$e_extra[$i]);
		
		$phpVars .=  "\n" .'$bc_arr_'. $e_name[$i] . "\t" . '='. "\t" .'array();' . "\n";
		$phpVars .= '$arrRES = mysql_query("'. $tmparray[1] .'");' . "\n";
		$phpVars .= 'while ($bc_row = mysql_fetch_assoc($arrRES) )' . "\n";
		$phpVars .=  "\t" . '$bc_arr_'. $e_name[$i] . '[$bc_row["id"]] = $bc_row["value"];' . "\n" . "\n";
	}
		
	/* PHP VARIABLES*/
	
	/* HTML CONTENTS */
	
	$form .= '<tr>'. "\n";
	$form .= '<td align="right" class="bc_label">';
	$form .= $e_label[$i];
	$form .= '</td>' . "\n";
	$form .= '<td align="left" class="bc_input_td">'. "\n";
	
	if ($e_object[$i] == 'text')
		$element = '<input type="text" name="'. $e_name[$i] .'" id="'. $e_name[$i] .'" class="bc_input" value="<?php echo $bc_'. $e_name[$i] .'; ?>"/>'. "\n";
	else if ($e_object[$i] == 'textarea')
		$element = '<textarea  name="'. $e_name[$i] .'" id="'. $e_name[$i] .'" class="bc_input" style="width:550px;height:200px;" /><?php echo $bc_'. $e_name[$i] .'; ?></textarea>'. "\n";
	else if ($e_object[$i] == 'date') {
		$element  = "\n". '<script type="text/javascript">
						<!--
						$(function() {
							$(\'#'. $e_name[$i] .'\').datepick();
						});
						-->
					</script>' . "\n";
		$element .= '<input type="text" name="'. $e_name[$i] .'" id="'. $e_name[$i] .'" readonly="true" class="bc_input" value="<?php echo $bc_'. $e_name[$i] .'; ?>"/>'. "\n";
		
	}
	else if ($e_object[$i] == 'select') {
		$element = '<select name="'. $e_name[$i] .'" id="'. $e_name[$i] .'" class="bc_input" >' . "\n";
		$element .= '<?php ' . "\n" ;
		$element .=	'foreach($bc_arr_'. $e_name[$i] .' as $key => $val)' . "\n";
		$element .=	'{' . "\n";
		$element .=	'	if ($key == $bc_'. $e_name[$i] .')' . "\n";
		$element .=	'		$sel = "selected";' . "\n";
		$element .=	'	else' . "\n";
		$element .=	'		$sel = "";	' . "\n";
		$element .=	'?>' . "\n";
		$element .= '<option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $val; ?> </option>'. "\n";
		$element .= '<?php } ?>'. "\n";
		$element .= ' </select>'. "\n";
	}
	else if ($e_object[$i] == 'radio') {
		$element  = '<?php ' . "\n" ;
		$element .=	'foreach($bc_arr_'. $e_name[$i] .' as $key => $val)' . "\n";
		$element .=	'{' . "\n";
		$element .=	'	if ($key == $bc_'. $e_name[$i] .')' . "\n";
		$element .=	'		$sel = "selected";' . "\n";
		$element .=	'	else' . "\n";
		$element .=	'		$sel = "";	' . "\n";
		$element .=	'?>' . "\n";
		$element .= '<input name="'. $e_name[$i] .'[]" id="'. $e_name[$i].$i .'" type="radio" <?php echo $sel; ?> value="<?php echo $key; ?>" /><?php echo $val; ?><br>'. "\n";
		$element .= '<?php } ?>'. "\n";
		$element .= ' </select>'. "\n";
	}
	else if ($e_object[$i] == 'checkbox') {
		$element  = '<?php ' . "\n" ;
		$element .=	'foreach($bc_arr_'. $e_name[$i] .' as $key => $val)' . "\n";
		$element .=	'{' . "\n";
		$element .=	'	if (in_array($key,$bc_tmp_arr_'. $e_name[$i] .') ) ' . "\n";
		$element .=	'		$sel = "checked";' . "\n";
		$element .=	'	else' . "\n";
		$element .=	'		$sel = "";	' . "\n";
		$element .=	'?>' . "\n";
		$element .= '<input type="checkbox" name="'. $e_name[$i] .'[]" id="'. $e_name[$i].$i .'" <?php echo $sel; ?> value="<?php echo $key; ?>" /><?php echo $val; ?><br>'. "\n";
		$element .= '<?php } ?>'. "\n";
	}
	else if ($e_object[$i] == 'file')	{
		$element ='<?php echo showFileName($frmID,"'.$table_name.'","'.$e_field[$i].'"); ?>'. "\n";
		$element .= '<input type="file" name="'. $e_name[$i] .'" id="'. $e_name[$i] .'" /><br>'. "\n";
		$element .= '<!-- removeFileAdmin(TABLE_NAME,FIELD_NAME,FILE_PATH,ROW_ID)  -->' . "\n";
		$element .= '<img src="images/remove_img.png" class="delImg" onclick="removeFileAdmin(\''.$table_name.'\',\''.$e_field[$i].'\',\''. $e_extra[$i] .'\',<?php echo $frmID;?>)" style="cursor:pointer"  />' . "\n"; 
	}
	else if ($e_object[$i] == 'image')	{
		$element ='<?php' . "\n";
		$element .= ' if( $bc_'.$e_field[$i].' != \'\' ) { '. "\n";
		$element .= "\t" . 'echo \'<img src="'. $e_extra[$i] .'\'. $bc_'.$e_field[$i].' .\'" class="dynamicImg" id="delImg_image" width="75" height="76" />\';' . "\n"; 
		$element .= "\t" . '$image_del = \'<img src="images/remove_img.png" class="delImg" id="\'.$frmID.\'" style="cursor:pointer" rel="'.$table_name.'|'.$e_field[$i].'|\'. $bc_'.$e_field[$i].' .\'|'. $e_extra[$i] .'" />\';' . "\n"; 
		$element .=  '} else {' . "\n"; 
		
		$element .= "\t" . 'echo \'<img src="images/no_image.png" class="dynamicImg"width="75" height="76" />\';' . "\n"; 
		$element .= '}' . "\n"; 
		$element .= '?>' . "\n"; 

		$element .= '<input type="file" name="'. $e_name[$i] .'" id="'. $e_name[$i] .'" /><br>'. "\n";
		$element .= '<?=$image_del?>' . "\n";
	}	
	else if ($e_object[$i] == 'password')		
		$element = '<input type="password" name="'. $e_name[$i] .'" id="'. $e_name[$i] .'" class="bc_input" value="<?php echo $bc_'. $e_name[$i] .'; ?>" />'. "\n";

	$form .= $element;
	$form .= '</td>'.  "\n" .'</tr>' . "\n" . "\n";
}

$form .= '<tr>'. "\n";
$form .= '<td>&nbsp;</td><td align="left">' . "\n";
$form .= '<input name="submit" type="submit" value="Save" class="bc_button" />' . "\n"; 
$form .= '</td>' . "\n" . '</tr>'. "\n";


$form .= '</table>' . "\n";
$form .= '</form>' . "\n";
	/* HTML CONTENTS */	

/* DATABASE HANDLING BASED ON ID*/
$dbDnading .= '$action1 = isset($_POST["bc_form_action"]) ? $_POST["bc_form_action"] : "";' . "\n". "\n";
$dbDnading .= '$action = "save";'. "\n" ;
$dbDnading .= '$sucMessage = "";'. "\n" ;
$dbDnading .= "\n";
$dbDnading .= $errors . "\n";
$dbDnading .= $errorList . "\n";
$dbDnading .= 'if (isset($_POST["submit"]) ) {' . "\n" . "\n";
$dbDnading .= "\t" .'if (!count($errors)) {' . "\n" . "\n";
$dbDnading .= $fileUpd;
$dbDnading .= "\t" ."\t" .' if ($action1 == "save") {' . "\n" ;
$dbDnading .= "\t" ."\t" ."\t" .'$sql'. "\t" . '='. "\t" . $filedList . "\n";
$dbDnading .= "\t" ."\t" ."\t" .'$res'. "\t" . '='. "\t" .'mysql_query($sql);' . "\n";
$dbDnading .= "\t" ."\t" ."\t" .'$frmID = mysql_insert_id();' . "\n";
$dbDnading .= "\t" ."\t" ."\t" .'if ($res) {' . "\n";
$dbDnading .= "\t" ."\t" ."\t" . "\t" . '$sucMessage = "Record Successfully inserted.";'. "\n";
$dbDnading .= "\t" ."\t" ."\t" .'} else {' . "\n" ;
$dbDnading .= "\t" ."\t" ."\t" . "\t" . '$sucMessage = "Error: Please try Later";'. "\n";
$dbDnading .= "\t" ."\t" ."\t" .'} // end if res' . "\n" ;
$dbDnading .= "\t" ."\t" .'} // end if' . "\n" ;
$dbDnading .= "\t" ."\t" ."\n";

$filedList = '"update '. $table_name . ' set ' ;
for ($i=0;$i<count($e_field); $i++ ) {
	if ($e_object[$i] == 'file' || $e_object[$i] == 'image')
		$filedList	.= '" . $bci_'.$e_name[$i] . ' . "\', ';
	else
		$filedList	.= $e_field[$i] . ' = \'" . ' . '$bc_'.$e_name[$i] . ' . "\', ';	
}	
$filedList = substr($filedList,0,-2);
$filedList .= ' where id=$frmID";';	

$dbDnading .= "\t" ."\t" .'if ($action1 == "edit") {' . "\n" ;
$dbDnading .= "\t" ."\t" ."\t" .'$sql'. "\t" . '='. "\t" . $filedList . "\n";
$dbDnading .= "\t" ."\t" ."\t" .'$res'. "\t" . '='. "\t" .'mysql_query($sql);' . "\n";
$dbDnading .= "\t" ."\t" ."\t" .'if ($res) {' . "\n";
$dbDnading .= "\t" ."\t" ."\t" . "\t" . '$sucMessage = "Record Successfully updated.";'. "\n";
$dbDnading .= "\t" ."\t" ."\t" .'} else {' . "\n" ;
$dbDnading .= "\t" ."\t" ."\t" . "\t" . '$sucMessage = "Error: Please try Later";'. "\n";
$dbDnading .= "\t" ."\t" ."\t" .'} // end if res' . "\n" ;
$dbDnading .= "\t" ."\t" .'} // end if' . "\n" ;
$dbDnading .= "\n";

$dbDnading .= "\t" .'} // end if errors'. "\n" . "\n" ;
$dbDnading .= "\t" .'else {' . "\n";
$dbDnading .= "\t" ."\t" .'$sucMessage = $err;' . "\n";
$dbDnading .= "\t" .'}' . "\n";

$dbDnading .= '} // end if submit' . "\n" ;
$dbDnading .= '$sql'. "\t" . '='. "\t" .'"select * from '. $table_name .' where id=$frmID";' . "\n";
$dbDnading .= '$res'. "\t" . '='. "\t" .'mysql_query($sql);' . "\n";
$dbDnading .= 'if ($res) {' . "\n";
$dbDnading .= "\t" .'if ($row = mysql_fetch_assoc($res) ) {' . "\n";

for ($i=0;$i<count($e_field); $i++ ) {
	if ($e_object[$i] == 'date') 
		$dbDnading .= "\t" ."\t" . '$bc_'.$e_name[$i] . "\t" . '=' . "\t" . 'date("m/d/Y",strtotime($row["'. $e_field[$i] .'"]));' . "\n" ;
	else
		$dbDnading .= "\t" ."\t" . '$bc_'.$e_name[$i] . "\t" . '=' . "\t" . '$row["'. $e_field[$i] .'"];' . "\n" ;
		
	if ($e_object[$i] == 'checkbox') 
		$dbDnading .= "\t" ."\t" . '$bc_tmp_arr_'.$e_name[$i] . "\t" . '=' . "\t" . 'explode(",",$bc_'. $e_name[$i] .');' . "\n" ;
}

$dbDnading .=  "\t" .'} // end if row'. "\n" ;
$dbDnading .= "\t" .'$action = "edit";'. "\n" ;
$dbDnading .= '} // end if '. "\n" ;

/* WRITE PHP FILE */

$filecontents .= '<?php' . "\n" . "\n";
$filecontents .= 'require_once("database.php"); ' . "\n" ;
$filecontents .= 'require_once("header.php"); ' . "\n" . "\n" ;
$filecontents .= $phpVars . "\n" ;
$filecontents .= $phpGetID . "\n" ;
$filecontents .= $dbDnading . "\n" ;

$filecontents .= '?>' . "\n";
$filecontents .= $form . "\n";

$filecontents .=  '<?php ' . "\n" . 'require_once("footer.php"); ' . "\n" . '?>' ;

//$phpFileName =  $phpFileName . '.php';
$phpFileName =  'newGeneratedFile.php';

if (!$handle = fopen($phpFileName, 'w')) {
	 echo "Cannot open file ($phpFileName)";
	 exit;
}

if (fwrite($handle, $filecontents) === FALSE) {
	echo "Cannot write to file ($phpFileName)";
	exit;
}

fclose($handle);

/* WRITE PHP FILE */


// Quick check to verify that the file exists
if( !file_exists($phpFileName) ) die("File not found");

// Force the download
header("Content-Disposition: attachment; filename=\"" . basename($phpFileName) . "\"");
header("Content-Length: " . filesize($phpFileName));
header("Content-Type: application/octet-stream;");
readfile($phpFileName);

?>