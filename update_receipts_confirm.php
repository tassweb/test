<?php 
	include 'password_protect.php'; 
	include 'dbinfo.php';
	
	$current_user = $_COOKIE['nichoir_username'];
	
	$query = "update fctdonationstb set receipt_printed_ind = 'Y' where receipt_required_ind = 'Y' and receipt_printed_ind = 'N' and receipt_no > 0";
	$result = mysql_query($query) or die(mysql_error());
	
	mysql_close($connection);
	
	header( 'Location: index.php' ) ;
?>