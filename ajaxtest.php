<?php 

	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	foreach($_GET as $key => $value) {
		$$key = $value;
	}
	
	$query = 'select * from dimdonortb where donor_id = ' . $donor_id;
	createTableFromSql($query);

?>