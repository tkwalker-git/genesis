<?php

/////////////////////////////////////////////////////////////////////////////
// PHPRunner connect proxy
//
define('RUNNER_VERSION', '5.1');
/////////////////////////////////////////////////////////////////////////////

ini_set("display_errors","1");
ini_set("display_startup_errors","1");

$host = refine(@$_REQUEST["host"]);
$login = refine(@$_REQUEST["login"]);
$pwd = refine(@$_REQUEST["pwd"]);
$port = refine(@$_REQUEST["port"]);
$db = refine(@$_REQUEST["db"]);
$todo = refine(@$_REQUEST["todo"]);

if($todo=="connect" || $todo=="schema")
{
	if((integer)$port)
		$host=$host.":".(integer)$port;
	$conn=@mysql_connect($host,$login,$pwd);
	if(!$conn)
	{
		echo "<div><font color=red>".mysql_error()."</font></div>";
		$todo="";
	}
}

if($todo=="schema")
{
	if(!mysql_select_db($db,$conn))
	{
		echo "<div><font color=red>".mysql_error()."</font></div>";
		$todo="connect";
	}
	else
	{
		show_schema();
		exit();
	}
		
}

if(!$todo || $todo=="connect")
{
?>
<html><body>
<form method="get" name="mainform" action="phprunner.php">
<input type="Hidden" name="todo" value="connect">
<table cellspacing="2" cellpadding="2" border="0">
<tr>
  <td nowrap>Server address</td>
  <td><input type="Text" name="host" value="<?php if(!$host) echo "localhost"; else echo htmlspecialchars($host);?>"></td>
</tr>
<tr>
  <td nowrap>Username</td>
  <td><input type="Text" name="login" value="<?php echo htmlspecialchars($login);?>"></td>
</tr>
<tr>
  <td nowrap>Password</td>
  <td><input type="Text" name="pwd" value="<?php echo htmlspecialchars($pwd);?>"></td>
</tr>
<tr>
  <td nowrap>Port (if not 3306)</td>
  <td><input type="Text" name="port" value="<?php echo htmlspecialchars($port);?>"></td>
</tr>
<tr>
  <td></td>
  <td><input type="button" value="Connect" onclick="mainform.submit();"></td>
</tr>
<?php 
if($todo=="connect") 
{
?>
<tr>
  <td nowrap>Database</td>
  <td>
<?php 
	$dblist=@mysql_list_dbs($conn);
	if($dblist && $row=mysql_fetch_array($dblist,MYSQL_ASSOC))
	{
?>
  <select name="db">
<?php
    	while(true)
		{
			echo "<option value=\"".htmlspecialchars($row["Database"])."\">".htmlspecialchars($row["Database"])."</option>";
			if(!($row=mysql_fetch_array($dblist,MYSQL_ASSOC)))
				break;
		}
?>  
  </select>
<?php
	}
	else
	{
?>  
	<input type="Text" name="db" size=30 maxlength=100 value="<?php echo htmlspecialchars($db);?>">
<?php 
	} 
?>
</td>
</tr>
<?php
	
?>
<tr>
  <td></td>
  <td><input type="button" value="Show schema" onclick="if(this.form.db.tagName=='INPUT' && this.form.db.value=='') {alert('Enter your database name first.'); return false;} this.form.todo.value='schema'; this.form.submit();"></td>
</tr>
<?php
  }
?>
</table>
</form>
</body></html>
<?php
  return;
}

//	process PHPRunner 5.1 commands

if($todo=='version')
{
	echo RUNNER_VERSION;
	return;
}
if($todo=="exec5")
{
	exec5();
	return;
}
if($todo=="query5")
{
	query5();
	return;
}
if($todo=="testconnect5")
{
	testconnect5();
	return;
}

//	process PHPRunner 5.0 commands

if((integer)$port)
	$host=$host.":".(integer)$port;
$conn=@mysql_connect($host,$login,$pwd);
if(!$conn)
{
	echo mysql_error();
	exit();
}

if($todo=="testconnect")
{
  echo "connected ok";
  return;
}
if($todo=="exec")
{
  if($db && !mysql_select_db($db,$conn))
  {
    echo mysql_error();
    return;
  }
  $res = mysql_query(refine($_REQUEST["sql"]),$conn);
  if(!$res)
    echo mysql_error();
  else
    echo "1";
  return;
}
header("Content-type: text/xml");


if($todo=="dbs")
{
	$dblist=mysql_list_dbs($conn);
	echo '<?xml version="1.0" standalone="yes" ?>';
	echo "<databases>";
	while($row=mysql_fetch_array($dblist,MYSQL_ASSOC))
		echo "<database name=\"".htmlspecialchars($row["Database"])."\" />";
	echo "</databases>";
}
else if($todo=="queryfields")
{
	mysql_select_db($db,$conn) or showerror();
	$sql = refine(@$_REQUEST["sql"]);
	if(!$sql)
		return;
	$sql.= " limit 0,0";
	$res = mysql_query($sql,$conn);
	echo '<?xml version="1.0" standalone="yes" ?>';
	echo "<fields>";
	for($i=0;$i<mysql_num_fields($res);$i++)
	{
		$flags=strtolower(mysql_field_flags($res,$i));
		$type=mysql_field_type($res,$i);
		if($type=="blob" && strpos($flags,"binary")===false)
			$type="text";
		echo "<field name=\"".htmlspecialchars(mysql_field_name($res,$i))."\" type=\"".htmlspecialchars($type)."\" size=\"".htmlspecialchars(mysql_field_len($res,$i))."\" ";
		if(!(strpos($flags,"auto_increment")===false))
		echo "auto_increment=\"auto_increment\" ";
		if(!(strpos($flags,"primary_key")===false))
			echo "key=\"PRI\" ";
		if(!(strpos($flags,"not_null")===false))
			echo "null=\"\" ";
		else
			echo "null=\"YES\" ";
		echo " />";
	}
	echo "</fields>";
}
else if($todo=="tables")
{
	mysql_select_db($db,$conn) or showerror();
	$tablist=mysql_list_tables($db,$conn);
	echo '<?xml version="1.0" standalone="yes" ?>';
	echo "<tables>";
	while($row=mysql_fetch_array($tablist,MYSQL_NUM))
		echo "<table name=\"".htmlspecialchars($row[0])."\" />";
	echo "</tables>";
}
else if($todo=="tablefields")
{
	$table = refine(@$_REQUEST["table"]);
	if(!$table)
		return;
	mysql_select_db($db,$conn) or showerror();
	echo '<?xml version="1.0" standalone="yes" ?>';
	showtablefields($table);
}
else if($todo=="queryvalues")
{
	mysql_select_db($db,$conn) or showerror();
	$sql = refine(@$_REQUEST["sql"]);
	if(!$sql)
		return;
	$sql.=" limit 0,200";
	$res = mysql_query($sql);
	if(!$res)
	{
		echo mysql_error();
		return;
	}
	echo '<?xml version="1.0" standalone="yes" ?>';
	if(mysql_num_fields($res)==1)
	{
		echo "<values>";
		while($row=mysql_fetch_array($res,MYSQL_NUM))
			echo "<value>".htmlspecialchars($row[0])."</value>";
		echo "</values>";
	}
	else
	{
		echo "<rows>\r\n";
		while($row=mysql_fetch_array($res,MYSQL_NUM))
		{
			echo "<row>";
			for($i=0;$i<mysql_num_fields($res);$i++)
			echo "<value>".htmlspecialchars($row[$i])."</value>";
			echo "</row>\r\n";
		}
		echo "</rows>";
	}
}
else if($todo=="queryvaluesraw")
{
	mysql_select_db($db,$conn) or showerror();
	$sql = refine(@$_REQUEST["sql"]);
	if(!$sql)
		return;
	$res = mysql_query($sql);
	if(!$res)
	{
		echo mysql_error();
		return;
	}
	echo '<?xml version="1.0" standalone="yes" ?>';
	echo "<rows>\r\n";
	echo "<row>\r\n";
	for($i=0;$i<mysql_num_fields($res);$i++)
		echo "<value>".htmlspecialchars(mysql_field_name($res,$i))."</value>\r\n";
	echo "</row>\r\n";
	while($row=mysql_fetch_array($res,MYSQL_NUM))
	{
		echo "<row>\r\n";
		for($i=0;$i<mysql_num_fields($res);$i++)
		{
			echo "<value>".htmlspecialchars($row[$i])."</value>\r\n";
		}
		echo "</row>\r\n";
	}
	echo "</rows>\r\n";
}
else if($todo=="queryvaluesstr")
{
	mysql_select_db($db,$conn) or showerror();
	$sql = refine(@$_REQUEST["sql"]);
	if(!$sql)
		return;
	$res = mysql_query($sql);
	if(!$res)
	{
		echo mysql_error();
		return;
	}
	$binfields = array();
	for($i=0;$i<mysql_num_fields($res);$i++)
	{
		$flags=strtolower(mysql_field_flags($res,$i));
		if(strpos($flags,"binary")!==false)
			$binfields[]=$i;
	}
	echo '<?xml version="1.0" standalone="yes" ?>';
	echo "<rows>\r\n";
	echo "<row>\r\n";
	for($i=0;$i<mysql_num_fields($res);$i++)
		echo "<value>".htmlspecialchars(mysql_field_name($res,$i))."</value>\r\n";
	echo "</row>\r\n";

	while($row=mysql_fetch_array($res,MYSQL_NUM))
	{
		echo "<row>\r\n";
		for($i=0;$i<mysql_num_fields($res);$i++)
		{
			$ret=array_search($i,$binfields);
			if($ret===FALSE || $ret===NULL)
				echo "<value>".htmlspecialchars($row[$i])."</value>\r\n";
			else
				if (strlen($row[$i]) == 0)
					echo "<value>NULL</value>\r\n";
				else
					echo "<value>0x".bin2hex($row[$i])."</value>\r\n";
		}
		echo "</row>\r\n";
	}
	echo "</rows>\r\n";
}

function refine($str)
{
	if(get_magic_quotes_gpc())
		$ret=stripslashes($str);
	else
		$ret=$str;
	return html_special_decode($ret);
}



function html_special_decode($str)
{
	$ret=$str;
	$ret=str_replace("&gt;",">",$ret);
	$ret=str_replace("&lt;","<",$ret);
	$ret=str_replace("&quot;","\"",$ret);
	$ret=str_replace("&#039;","'",$ret);
	$ret=str_replace("&amp;","&",$ret);
	return $ret;
}

function showtablefields($table)
{
	global $conn;
	$fields=mysql_query("SHOW fields FROM `".$table."`",$conn);
	if(!$fields)
	{
		echo mysql_error();
		exit();
	}
	echo "<fields>";
	while($field=mysql_fetch_array($fields,MYSQL_ASSOC))
	{
		$attr=array();
		$attr["name"]=$field["Field"];
		$type=$field["Type"];
//  remove type modifiers
		if(substr($type,0,4)=="tiny") $type=substr($type,4);
		else if(substr($type,0,5)=="small") $type=substr($type,5);
		else if(substr($type,0,6)=="medium")  $type=substr($type,6);
		else if(substr($type,0,3)=="big") $type=substr($type,3);
		else if(substr($type,0,4)=="long")  $type=substr($type,4);
		if(substr($type,0,4)=="enum")
        {
          $attr["values"]=substr($type,5,strlen($type)-6);
          $attr["type"]="enum";
        }
        else if(substr($type,0,3)=="set")
        {
          $attr["values"]=substr($type,4,strlen($type)-5);
          $attr["type"]="set";
        }
        else
        {
          if($pos=strpos($type," "))
            $type=substr($type,0,$pos);
//  parse field sizes
          if($pos=strpos($type,"("))
          {
            if($pos1=strpos($type,",",$pos))
            {
              $attr["size"]=(integer)substr($type,$pos+1,$pos1-$pos-1);
              $attr["scale"]=(integer)substr($type,$pos1+1,strlen($type)-$pos1-2);
            }
            else
            {
              $attr["size"]=(integer)substr($type,$pos+1,strlen($type)-$pos-2);
              $attr["scale"]=0;
            }
            $type=substr($type,0,$pos);
          }
          $attr["type"]=$type;
        }
        if(!(strpos($field["Extra"],"auto_increment")===false))
          $attr["auto_increment"]="auto_increment";
        $attr["key"]=$field["Key"];
        $attr["default"]=$field["Default"];
        $attr["null"]=$field["Null"];

        echo '<field ';
        foreach($attr as $key=>$value)
          echo $key.'="'.htmlspecialchars($value).'" ';
        echo '/>';
      }
      echo "</fields>";
}

function showerror()
{
	echo mysql_error();
	exit();
}

function show_schema()
{
	global $conn;
	header("Content-type: text/xml");
	$phpversion=phpversion();
//  determine mysql version
	$mysqlversion = "unknown";
	$res = mysql_query("SHOW VARIABLES LIKE 'version'",$conn) or showerror();
	if($row=mysql_fetch_array($res,MYSQL_ASSOC))
		$mysqlversion = $row["Value"];
	echo '<?xml version="1.0" standalone="yes" ?>';
?>
<phprunner phpversion="<?php echo htmlspecialchars($phpversion);?>" mysqlversion="<?php echo htmlspecialchars($mysqlversion);?>">
<tables>
<?php
	$tables=mysql_query("SHOW TABLES",$conn) or showerror();
	if(!$tables)
	{
		echo mysql_error();
		exit();
	}
	while($table=mysql_fetch_array($tables,MYSQL_NUM))
	{
?>
<table name="<?php echo htmlspecialchars($table[0]);?>">
<?php
      showtablefields($table[0]);
?>    
</table>
    <?php
    }
?>
</tables>
</phprunner>
<?php
}

function refine5($str)
{
	if(get_magic_quotes_gpc())
		$ret=stripslashes($str);
	else
		$ret=$str;
	return base64_decode($ret);
}

function connect5()
{
	$login=refine5(@$_REQUEST["login"]);
	$pwd=refine5(@$_REQUEST["pwd"]);
	$host=refine5(@$_REQUEST["host"]);
	$port=@$_REQUEST["port"];
	$db=refine5(@$_REQUEST["db"]);
	if((integer)$port)
		$host=$host.":".(integer)$port;
	$conn=@mysql_connect($host,$login,$pwd);
	if($conn && strlen($db))
	{
		if(!mysql_select_db($db,$conn))
		{
			mysql_close($conn);
			return false;
		}
	}
	return $conn;
}

function testconnect5()
{
	echo "start-script-output";
	if($conn=connect5())
	{
		echo "ok";
		mysql_close($conn);
	}
	else
		echo mysql_error();
	echo "end-script-output";
	exit();
}

function exec5()
{
	$query=refine5(@$_REQUEST["query"]);
	echo "start-script-output";
	if(!($conn=connect5()))
	{
		echo mysql_error();
		echo "end-script-output";
		exit();
	}
	if(mysql_query($query,$conn))
		echo "ok";
	else
		echo mysql_error();
	echo "end-script-output";
	mysql_close($conn);
	exit();
}

function query5()
{
	$query=refine5(@$_REQUEST["query"]);
	$reccount=refine(@$_REQUEST["reccount"])+0;
	$skip=refine(@$_REQUEST["skip"])+0;
	echo "start-script-output";
	if(!($conn=connect5()) || !($rs=mysql_query($query,$conn)))
	{
		echo mysql_error();
		echo "end-script-output";
		exit();
	}
	$bfields=array();
	echo "<output>";
//	display fields info
	echo "<fields>";
	for($i=0;$i<mysql_num_fields($rs);$i++)
	{
		$flags=strtolower(mysql_field_flags($rs,$i));
		$type=mysql_field_type($rs,$i);
		if($type=="blob" && strpos($flags,"binary")===false)
			$type="text";
		echo "<field name=\"".xmlencode(mysql_field_name($rs,$i))."\" type=\"".xmlencode($type)."\"";
		if(strpos($flags,"binary")!==false)
		{
			$bfields[]=true;
			echo ' binary="true"';
		}
		else
			$bfields[]=false;
		echo " />";
	}
	echo "</fields>";
//	display query data
	echo "<data>";
	$recno=0;
	while($data=mysql_fetch_array($rs,MYSQL_NUM))
	{
		$recno++;
		if($recno<=$skip)
			continue;
		if($reccount>=0 && $recno+$skip>$reccount)
			break;
		echo "<row>";
		foreach($data as $i=>$val)
		{
			if(is_null($val))
				echo '<field null="true" />';
			else if($bfields[$i])
				echo '<field>0x'.bin2hex($val).'</field>';
			else
				echo '<field>'.xmlencode($val).'</field>';
		}
		echo "</row>";
	}
	echo "</data>";
	echo "</output>";
	echo "end-script-output";
	mysql_close($conn);
	exit();
}

function xmlencode($str)
{
	$str = str_replace("&","&amp;",$str);
	$str = str_replace("<","&lt;",$str);
	$str = str_replace(">","&gt;",$str);
	$str = str_replace("\"","&quot;",$str);

	$out="";
	$len=strlen($str);
	$ind=0;
	for($i=0;$i<$len;$i++)
	{
		if(ord($str[$i])>=128)
		{
			if($ind<$i)
				$out.=substr($str,$ind,$i-$ind);
			$out.="&#".ord($str[$i]).";";
			$ind=$i+1;
		}
	}
	if($ind<$len)
		$out.=substr($str,$ind);
	return str_replace("'","&apos;",$out);
}

?>