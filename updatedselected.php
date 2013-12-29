<?php

	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	foreach($_GET as $key => $value) {
		$$key = $value;
	}
	
	$query = (isset($delete)) ? 
		"DELETE FROM refselecteddonortb where donor_id = $donor_id and username = '$username'":
		"INSERT INTO refselecteddonortb VALUES ($donor_id,'$username')";
	mysql_query($query) or die("Error: ". mysql_error());
	mysql_close($connection);
	
?>