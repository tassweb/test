<?php

	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	foreach($_GET as $key => $value) {
		$$key = $value;
	}
	
	$query = "DELETE FROM refselecteddonortb where username = '$username'";
	mysql_query($query) or die("Error: ". mysql_error());
	mysql_close($connection);

?>