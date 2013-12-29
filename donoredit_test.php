<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	include 'dbinfo.php';
	foreach($_GET as $key => $value) {$$key = $value;}

	$query = "SELECT * FROM dimdonortb WHERE donor_id ='$donor_id' and current_date between from_date and to_date";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_assoc($result);

	// "special needs" columns
	$numericColumns = array('no_solicit_flag','verified_flag','etax_receipt_flag','enewsletter_flag');
	$dateColumns = array();
	$dollarColumns = array();
	$readonlyColumns = array();
  	$nullColumns = array('donor_id');
	
	// build the table
	echo '
		<form method="post" action="donorupdate.php">
		<table border=1>';
	if ($donor_id) {
		echo'<caption>'.$donorinfo['last_name'].', '.$donorinfo['first_name'].'</caption>';
	}
  	
	foreach ($donorinfo as $key => $value) {
		echo '<tr><td>' . $key . ($key == 'donation_date' ? ' (yyyy-mm-dd)' : '') . '</td>';
		
		// default
		$rowout = $value;
		
		// convert numerics into yes/no
		if (in_array($key,$numericColumns)) {
			if ($rowout) { 
				$rowout = "Yes"; 
			}
			else { 
				$rowout = "No"; 
			}
		}
		// convert numerics to dollars
		elseif (in_array($key,$dollarColumns)) {
			$rowout = '$'.$rowout;
		}
		// convert datetimes to dates
		elseif (in_array($key,$dateColumns)) {
			$rowout = date('Y-m-d',strtotime($rowout));
		}
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns)) {
			echo '<td>'.$rowout.'</td>';
		}
		else if (in_array($key,$readonlyColumns)) {
			echo '<td><INPUT TYPE="TEXT" SIZE="100" NAME="'.str_replace(" ","_",$key).'" value="'.$rowout.'" readonly></td>';
		}
		else {
			echo '<td><INPUT TYPE="TEXT" SIZE="100" NAME="'.str_replace(" ","_",$key).'" value="'.$rowout.'"></td>';
		}
		echo '</tr>';
	}

	echo '
	</table>
	<INPUT TYPE="HIDDEN" NAME="donor_id" VALUE="'.$donor_id.'">
	<INPUT TYPE="SUBMIT" VALUE="Edit Donor" NAME="submit" class="btn">
	<INPUT TYPE="SUBMIT" VALUE="Delete Donor" NAME="delete" class="btn">
	<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
	</form>';
    	
	mysql_free_result($result);
	
	include_once 'footer.php';
	
	mysql_close($connection); 

?>	
</body>
</html>
