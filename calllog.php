<?php 

	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	include 'dbinfo.php';
	
	echo '
		<html>
		<head>
		<link href="blue.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="check.js"></script>
		</head>
		<body>
	';
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value;}
	
	// -- Grab all the column headers
	$callheaders = getheaders("calls");
	
	$query = "SELECT * FROM donors WHERE (`Donor ID`='$donor_id')";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_array($donor);

	echo '
		<table border=1>
		<caption>'.$donorinfo['Last Name'].', '.$donorinfo['First Name'].'</caption>
		<thead><tr>
	';
	
	foreach ($callheaders as $header) {
		echo '<th scope="col">'.$header.'</th>';
	}
	echo '		
		</tr></thead>
	';
	
	$tabledata = mysql_query(tablequery($callheaders, "calls")) or die("Errors: ".mysql_error());
	$trstring = 'onmouseover="this.style.cursor=\'pointer\'" onclick="window.location = \'calllogedit.php?donor_id=ROWREPLACE\'"';
	$tdstring = '';
	
	createtable($tabledata, $callheaders, $donor_id, $trstring, $tdstring);
	
	echo '
		</table>
		</body>
		</html>
	';
?>