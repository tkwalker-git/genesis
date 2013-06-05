<?php
error_reporting(E_ALL ^ E_NOTICE);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<script language="javascript">
function addMore()
{
	content = '<table width="622" border="0" cellspacing="2" cellpadding="0">  <tr>    <td width="102" height="30" align="left"><input type="text"  class="textbox"   name="e_label[]" /></td>    <td width="102" align="left"><input type="text"  class="textbox" name="e_name[]" /></td>    <td width="100" align="left"><select name="e_object[]" class="textbox">      <option value="text" selected="selected">Text</option>      <option value="textarea" >Text Area</option>      <option value="select" >Combo Box</option><option value="radio" >Radio Button</option>      <option value="checkbox" >Check Box</option>      <option value="date" >Date</option>      <option value="file" >File</option><option value="image" >Image</option><option value="password" >Password</option>    </select>    </td>    <td width="100" align="left"><select name="e_req[]" class="textbox">      <option value="yes">Yes</option>      <option value="no" selected="selected">No</option>    </select>    </td>    <td width="102" align="left"><input type="text"  class="textbox" name="e_extra[]" /></td>    <td width="280" align="left"><input type="text"  class="textbox" name="e_field[]" /></td>  </tr></table>';
	 
	var tbl = document.getElementById('content');
  	var lastRow = tbl.rows.length;
  	var iteration = lastRow+1;
  	var row = tbl.insertRow(lastRow);
	var cell = row.insertCell(0);
	cell.align="left";
	cell.innerHTML = content;
	
}
</script>
<style type="text/css">
<!--
body{
font:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
}
.textbox{
	width:100px;
	border:#333333 solid 1px;
	height:18px;
	font-size:12px;
	z-index:1;
}
.style1 {color: #FF0000}
.style10 {font-size: 12px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
.style11 {color: #FF0000; font-weight: bold; }
-->
</style>
</head>

<body>

<form id="form1" name="form1" method="post" action="generateFormNew.php">
<table width="625" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="30" colspan="6" align="left">
	<p class="style1"><strong>The extra field will be used only with combo box, check box and radio button. It will recive 3 type of parameters. </strong></p>
    <p class="style1">1. Array - We will fill the array in the source file.</p>
  <p class="style1">2. T<strong>:</strong>TABLE_NAME<strong>:</strong>ID_COLUMN_NAME<strong>:</strong>VALUE_COLUMN_NAME - It will pupolate the data based on the given colum of given table. </p>
  <p class="style1">3. SQL: sql with id and value fields (Example select name as value from table) Syantx= SQL:select a as id, b as value from c </p>
  <p class="style11">Extra For Image Field </p>
  <p class="style1">4. Put the save image path relative to the admin for example if you want to store image in images/articles folder at root then put path &quot;../images/articles/&quot; </p></td>
    </tr>
  <tr>
    <td height="30" colspan="2" align="left">File Name: 
      <input type="text"  class="textbox" name="php_file_name" value="no extension please" />   </td>
    <td height="30" colspan="4" align="left">Table Name: 
      <input type="text"  class="textbox" name="table_name" value="<?php  echo $_GET['table'];?>" /></td>
    </tr>
   <tr>
    <td height="30" colspan="6" align="left">Page Title: 
      <input type="text"  class="textbox" name="form_title" style="width:400px" />   </td>
    </tr>
  <tr>
    <td width="17%" align="left" bgcolor="#CCCCCC" height="22"><span class="style10">Label</span></td>
    <td width="17%" height="22" align="left" bgcolor="#CCCCCC"><span class="style10">Name</span></td>
    <td width="16%" height="22" align="left" bgcolor="#CCCCCC"><span class="style10">Element</span></td>
    <td width="17%" height="22" align="left" bgcolor="#CCCCCC"><span class="style10">Required</span></td>
    <td width="16%" height="22" align="left" bgcolor="#CCCCCC"><span class="style10">Extra</span></td>
    <td width="17%" height="22" align="left" bgcolor="#CCCCCC"><span class="style10">Table Field </span></td>
  </tr>
  <tr>
    <td align="left" colspan="6" >
	
	<table width="78%" border="0" cellspacing="0" cellpadding="0" id="content">
  	
	<?php
		if (isset($_GET['table']) ) {
			
			$db 		= $_GET['db'];
			$table		= $_GET['table'];
			
			$hostname = "localhost";
			$username = "root";
			$password = "";
			$database = $db;
			
			$Conn_db = mysql_connect($hostname, $username, $password) or die(mysql_error());
			mysql_select_db($database, $Conn_db);
			
			$sql = "desc " . $table;
			$res = mysql_query($sql);
			
			$notAllowed = array("id","cim_customer_id","cim_payment_profile_id","canceled","password","date_added","access_to_steps");
			
			while (	$row = mysql_fetch_assoc($res) ) {
			
				if (! in_array($row['Field'],$notAllowed)) {
	?>
		
		<tr>
  		<td>
  
			<table width="622" border="0" cellspacing="2" cellpadding="0">
				<tr><td width="102" height="30" align="left"> 
					<input type="text"  class="textbox"   name="e_label[]" value="<?=ucwords($row['Field'])?>:" />
				</td>
				<td width="102" align="left"> 
					<input type="text"  class="textbox" name="e_name[]" value="<?=$row['Field']?>" />
				</td>
				<td width="100" align="left">
					<select name="e_object[]" class="textbox">
					<option value="text" selected="selected">Text</option>	
					<option value="textarea" >Text Area</option>	
					<option value="select" >Combo Box</option>	
					<option value="radio" >Radio Button</option>	
					<option value="checkbox" >Check Box</option>	
					<option value="date" >Date</option>	
					<option value="file" >File</option>	
					<option value="image" >Image</option>	
					<option value="password" >Password</option>	
					</select>      
				</td>
				<td width="100" align="left">
					<select name="e_req[]" class="textbox">
					<option value="yes">Yes</option>
					<option value="no" selected="selected">No</option>	
					</select>	 
				</td>
				<td width="102" align="left">
					<input type="text"  class="textbox" name="e_extra[]" />
				</td>
				<td width="280" align="left">
					<input type="text"  class="textbox" name="e_field[]" value="<?=$row['Field']?>" />
				</td>
				</tr>
			</table>	 
		</td>
		</tr>
		
	<?php	
				}
			}
		} else {
	?>
	
	<tr>
  		<td>
  
			<table width="622" border="0" cellspacing="2" cellpadding="0">
				<tr><td width="102" height="30" align="left"> 
					<input type="text"  class="textbox"   name="e_label[]" />
				</td>
				<td width="102" align="left"> 
					<input type="text"  class="textbox" name="e_name[]" />
				</td>
				<td width="100" align="left">
					<select name="e_object[]" class="textbox">
					<option value="text" selected="selected">Text</option>	
					<option value="textarea" >Text Area</option>	
					<option value="select" >Combo Box</option>	
					<option value="radio" >Radio Button</option>	
					<option value="checkbox" >Check Box</option>	
					<option value="date" >Date</option>	
					<option value="file" >File Field</option>	
					<option value="image" >Image</option>
					<option value="password" >Password</option>	
					</select>      
				</td>
				<td width="100" align="left">
					<select name="e_req[]" class="textbox">
					<option value="yes">Yes</option>
					<option value="no" selected="selected">No</option>	
					</select>	 
				</td>
				<td width="102" align="left">
					<input type="text"  class="textbox" name="e_extra[]" />
				</td>
				<td width="280" align="left">
					<input type="text"  class="textbox" name="e_field[]" />
				</td>
				</tr>
			</table>	 
		</td>
	</tr>
	<?php
	}
	?>
	
	</table>	</td>
  </tr>
  <tr>
    <td  height="30" colspan="6" align="right">
      <input type="button" name="more" value="Add More Field" onclick="addMore()" />    </td>
    </tr>
  <tr>
    <td  height="30" colspan="6" align="center">
      <input type="submit" name="Submit" value="Generate Form" />    </td>
  </tr>
</table>
</form>
</body>
</html>
