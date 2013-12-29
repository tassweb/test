<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

	include 'dbinfo.php';
	
	$values=array();
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value; $values[]=$value;}

	$sql = "SELECT receipt_no FROM fctdonationstb WHERE donation_id = ".$donation_id;
	$results = mysql_query($sql);
	
	$hasreceipt = 0;
	while ($line = mysql_fetch_array($results)){
		if ($line[0] > 0 ) $hasreceipt = 1;	
	}
	
	$query = "SELECT * FROM fctdonationstb WHERE donation_id=$donation_id";
	$donation = mysql_query($query) or die("Errors: ".mysql_error());
	$donationinfo = mysql_fetch_array($donor);
	$columns = array();

	echo '
		<form method="post" action="cancelreceiptconfirm.php">
		<table border=1>';
	if ($donor_id) {
		echo'<caption>'.$donationinfo['donation_id'].'</caption>';
	}
	if ($hasreceipt) {
		echo '<tr><td>Cancel Receipt?</td></tr></table>';
	}
	else {
		echo '<tr><td>Delete Donation?</td></tr></table>';
	}
	echo '
			<INPUT TYPE="HIDDEN" NAME="donor_id" VALUE="'.$donor_id.'">
			<INPUT TYPE="HIDDEN" NAME="donation_id" VALUE="'.$donation_id.'">
			<INPUT TYPE="SUBMIT" VALUE="Confirm" NAME="submit" class="btn">
			<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
			</form>';
	
	echo '</body></html>';
	
	mysql_close($connection);
?>