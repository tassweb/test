<?php 

	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	include 'dbinfo.php';
	
	foreach ($_GET as $key => $value) {
		$$key = $value;	
	}
	
	$query = "select category_id from dimcategorytb where revenue_stream = '$revenue_stream' and type = '$type' and origin = '$origin' and current_date between from_date and to_date";
	$result = mysql_query($query) or die ("Error: " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$category_id = $row['category_id'];

?>