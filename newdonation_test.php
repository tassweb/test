<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	include 'dbinfo.php';
	foreach($_GET as $key => $value) {$$key = $value;}

	$query = "SELECT * FROM donors WHERE (`Donor ID`='$donor_id')";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_array($donor);
	
	$query = "SHOW COLUMNS FROM donations";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donationinfo = array();

	while ($column = mysql_fetch_assoc($result)) {
		array_push($donationinfo, $column['Field']);
	}
	// "special needs" columns
  	$nullColumns = array('Donation Number','Donors','Receipt printed?','Receipt Number');
	
	// build the table
	echo '
		<form method="post" action="donationupdate.php">
		<table border=1>';
	
	if ($donor_id) {
		echo'<caption>'.$donorinfo['Last Name'].', '.$donorinfo['First Name'].'</caption>';
	}
	
	foreach ($donationinfo as $key) {
		echo '<tr><td>' . $key . ($key == 'Donation Date' ? ' <b>(yyyy-mm-dd)</b>' : '') . ($key == 'Receipt required?' ? ' <b>(yes/no)</b>' : '') . '</td>';
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns)) {
			echo '<td>New Donation</td>';
		}
		else {
			echo '<td><INPUT TYPE="TEXT" SIZE="50" NAME="'.str_replace(" ","_",$key).'"></td>';
		}
		echo '</tr>';
	}

	echo '
	</table>
	<INPUT TYPE="HIDDEN" NAME="donor_id" VALUE="'.$donor_id.'">
    <INPUT TYPE="SUBMIT" VALUE="Create Donation" class="btn">
    <INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
	</form>';
    	
	mysql_free_result($result);
	mysql_free_result($donor);
	
	include_once 'footer.php';
	
	mysql_close($connection); 

?>	
</body>
</html>
