<?php include 'password_protect.php'; ?>

<?php

	include 'dbinfo.php';
	
	$values=array();
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value; $values[]=$value;}

	
	mysql_query("UPDATE dimdonortb SET to_date = current_date-1, a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user) WHERE current_date between from_date and to_date and donor_id = ".$donor_id) or die(mysql_error());
	mysql_query("UPDATE dimdonorinvolvementtb SET to_date = current_date-1, a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user) WHERE donor_id = ".$donor_id) or die(mysql_error());
	mysql_close($connection);
	
	header( 'Location: index.php' ) ;

?>