<?php

/**
 * @author Mahfooz ul haq
 * @copyright 2010
 */
require_once("database.php");

$tbl = $_GET['tbl'];
$fld = 'sort_order';

foreach ($_GET['li'] as $pos => $id) {
    mysql_query("UPDATE `$tbl` SET `$fld`='$pos' WHERE `id` = '$id' limit 1") or die(mysql_error());
}
echo '<font color="#FF0000">List Updated...</font>';

?>