<?php
	
	require_once('../admin/database.php');
	require_once('../site_functions.php');


$order_id = $_GET['order_id'];

generateTicketsPDF($order_id)

?>