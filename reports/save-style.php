<?php
/*
 * report_style_id - record ID
 * type - style type ('table', 'field', 'group', 'cell')
 * field - column number
 * group - group number in appropriate with constants
 * repname - report name
 * uniq - unique field
 * style_str - style string
 * styletype - stype of style change
 */
	
	ini_set("display_errors","1");
	ini_set("display_startup_errors","1");
	include("include/dbcommon.php");	
	header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

	include("include/reportfunctions.php");
	
	
	$conn=db_connect();

	$xml = new xml();
	
	if (!postvalue('str_xml')){
		echo 'Error: Script didn\'t get data. Try once again.';
		exit(0);
	}
	
	$arr = $xml->xml_to_array(postvalue('str_xml'));
	
	$repname = postvalue('repname');

	if ($_POST['str_xml'] == "del_all"){
		
			$strSQL = 'DELETE FROM webreport_style WHERE repname=\''.db_addslashes($repname).'\'';
			$rsReport = db_exec($strSQL,$conn);
			die;
		
	}
		
	$arrayer = array();
	foreach ($arr as $key => $style_record){
	
		if ($style_record['type'] == "table"){
		
			$strSQL = 'DELETE FROM webreport_style WHERE (repname=\''.db_addslashes($repname).'\' AND styletype=\''.$style_record['params']['styleType'].'\')';
			$rsReport = db_exec($strSQL,$conn);
		
		}
		if ($style_record['type'] == "group"){
		
			if ($style_record['params']['groupName'] != 0){
				$strSQL = 'DELETE FROM webreport_style WHERE ('.AddFieldWrappers("group").' = '.(0+$style_record['params']['groupName']).' AND repname=\''.db_addslashes($repname).'\' AND styletype=\''.$style_record['params']['styleType'].'\' AND ('.AddFieldWrappers("type").'=\'cell\' OR '.AddFieldWrappers("type").'=\'group\'))';
				$rsReport = db_exec($strSQL,$conn);
			}
		
		}
		if ($style_record['type'] == "field"){
		
			$strSQL = 'DELETE FROM webreport_style WHERE ('.AddFieldWrappers("field").' = '.($style_record['params']['fieldName']+0).' AND repname=\''.db_addslashes($repname).'\' AND styletype=\''.$style_record['params']['styleType'].'\')';
			$rsReport = db_exec($strSQL,$conn);
		
		}
		if ($style_record['type'] == "cell"){
		
			$style_record['params']['uniq'] = (int)$style_record['params']['uniq'];
			$strSQL = 'DELETE FROM webreport_style WHERE ('.AddFieldWrappers("type").' = \''.$style_record['type'].'\' AND '.AddFieldWrappers("field").' = '.($style_record['params']['fieldName']+0).' AND '.AddFieldWrappers("group").' = '.(0+$style_record['params']['groupName']).' AND '.AddFieldWrappers("uniq").'='.(int)$style_record['params']['uniq'].' AND repname=\''.db_addslashes($repname).'\' AND styletype=\''.$style_record['params']['styleType'].'\')';
			$rsReport = db_exec($strSQL,$conn);
		
		}
		
		$strSQL = "INSERT INTO webreport_style (".AddFieldWrappers("type").",".AddFieldWrappers("field").",".AddFieldWrappers("group").",style_str,".AddFieldWrappers("uniq").",repname,styletype) VALUES ('".$style_record['type']."',".db_addslashes($style_record['params']['fieldName']).",".$style_record['params']['groupName'].",'".db_addslashes($style_record['params']['styleStr'])."',".$style_record['params']['uniq'].",'".db_addslashes($repname)."','".$style_record['params']['styleType']."')";
		$rsReport = db_exec($strSQL,$conn);
	}

	echo 'OK';
?>