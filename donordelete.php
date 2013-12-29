<?php include 'password_protect.php'; ?>

<?php

	include 'dbinfo.php';
	
	$values=array();
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value; $values[]=$value;}

	$sql = "SELECT * FROM fctdonationstb WHERE donor_id = ".$_POST['dimdonortb|donor_id'];
	$results = mysql_query($sql);
	
	$hasdonation = 0;
	while ($line = mysql_fetch_array($results)){
		$hasdonation = 1;	
	}
	echo '
	<html>
	<head>
	<link href="blue.css" rel="stylesheet" type="text/css">
	</head>
	<body>';
	
	$query = "SELECT * FROM dimdonortb WHERE donor_id = ".$_POST['dimdonortb|donor_id']." and current_date between from_date and to_date";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_assoc($result);
	
	echo '<form method="post" action="deleteconfirm.php">
		  <table border=1>';
	if ($donor_id) {
		echo'<caption>'.$donorinfo['last_name'].', '.$donorinfo['first_name'].'</caption>';
	}
	
	if ($hasdonation){

		echo '<tr><td>Donor has donations.  Cannot delete.</td></tr></table>';
		echo '
		    	<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
    			</form>';
	}
	else
	{
		echo '<tr><td>Confirm Delete? (This action cannot be undone)</td></tr></table>';
		echo '
		    	<INPUT TYPE="HIDDEN" NAME="donor_id" VALUE="'.$_POST['dimdonortb|donor_id'].'">
		    	<INPUT TYPE="SUBMIT" VALUE="Delete Donor" NAME="submit" class="btn">
		    	<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
    			</form>';
	}
	
	echo '</body></html>';
	
	mysql_close($connection);
?>