<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	include 'dbinfo.php';

	$query = "SHOW COLUMNS FROM donors";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = array();

	while ($column = mysql_fetch_assoc($result)) {
		array_push($donorinfo, $column['Field']);
	}
	// "special needs" columns
  	$nullColumns = array('Donor ID');
	
	// build the table
	echo '
		<form method="post" action="donorupdate.php">
		<table border=1>
		<caption>New Donor</caption>';
  	
	foreach ($donorinfo as $key) {
		echo '<tr><td>' . $key . ($key == 'Donation Date' ? ' (yyyy-mm-dd)' : '') . '</td>';
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns)) {
			echo '<td>New Donor</td>';
		}
		else {
			echo '<td><INPUT TYPE="TEXT" SIZE="100" NAME="'.str_replace(" ","_",$key).'"></td>';
		}
		echo '</tr>';
	}

	echo '
	</table>
	<INPUT TYPE="SUBMIT" VALUE="Create Donor" class="btn">
    <INPUT TYPE="button" VALUE="Back" onClick="history.go(-1);return true;" class="btn"> 	
	</form>';
    	
	mysql_free_result($result);
	
	include_once 'footer.php';
	
	mysql_close($connection); 

?>	
</body>
</html>
