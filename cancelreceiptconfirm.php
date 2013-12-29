<?php 
	include 'password_protect.php'; 
	include 'dbinfo.php';
	
	$values=array();
	$current_user = $_COOKIE['nichoir_username'];
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value; $values[]=$value;}

	$query = "SELECT receipt_no from fctdonationstb where donation_id = ".$donation_id;
	$result = mysql_query($query) or die(mysql_error());
	$receiptNum = mysql_fetch_assoc($result);
	
	if ($receiptNum['receipt_no']) {
		$query = "INSERT INTO fctcancelledreceiptstb values (null,$donation_id,'Receipt Canceled',current_timestamp,concat('$current_user','.',current_user),current_timestamp,concat('$current_user','.',current_user));";
		mysql_query($query) or die(mysql_error());
		$query = "UPDATE fctdonationstb SET donation_notes = 'Receipt Cancelled', receipt_required_ind = 'N', a_update_user = concat('$current_user','.',current_user), a_update_timestamp = current_timestamp where donation_id = $donation_id";
		mysql_query($query) or die(mysql_error());
	}
	else
	{	$query = "DELETE FROM fctdonationstb WHERE donation_id = ".$donation_id;
		mysql_query($query) or die(mysql_error());
	}
	
	mysql_close($connection);
	
	header( 'Location: index.php' ) ;
?>