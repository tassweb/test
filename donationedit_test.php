<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	include 'dbinfo.php';
	foreach($_GET as $key => $value) {$$key = $value;}
	
	// get the donation
	$query = "SELECT * FROM fctdonationstb WHERE (donation_id='$donation_id')";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donation = mysql_fetch_assoc($result);
	
	$donor_id = $donation['donor_id'];
	
	$query = "SELECT * FROM dimdonortb WHERE (`Donor ID`='$donor_id') and current_date between from_date and to_date";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_assoc($donor);
		
		
	$columns = array();
	$dateColumns = array('donation_date');
	$dollarColumns = array('donation_amt');
	$readonlyColumns = array('receipt_no');
  	$nullColumns = array('donation_id');
  	if ($donationinfo['receipt_required_ind']=="Y") {
  		array_push($readonlyColumns, 'receipt_required_ind');
	}
		
	echo '
		<form method="post" action="donationupdate.php">
		<table border=1>';
	if ($donation_id) {
		echo'<caption>'.$donorinfo['last_name'].', '.$donorinfo['first_name'].'</caption>';
	}
	echo '<thead><tr>';
	
	foreach ($donation as $key => $value) {
		echo '<tr><td>' . $key . ($key == 'donation_date' ? ' (yyyy-mm-dd)' : '') . '</td>';
		
		// default
		$rowout = $value;
		
		// convert numerics to dollars
		if (in_array($key,$dollarColumns)) {
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
		else if (in_array($i,$readonlyindexes)) {
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
    	<INPUT TYPE="HIDDEN" NAME="donation_id" VALUE="'.$donation_id.'">
    	<INPUT TYPE="SUBMIT" VALUE="Edit Donation" NAME="edit" class="btn">
    	<INPUT TYPE="SUBMIT" VALUE="Cancel Donation" NAME="cancel" class="btn">
    	<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
    	</form>';
    	
	mysql_free_result($donor);
	mysql_free_result($donation);
	
	include_once 'footer.php';
	
	mysql_close($connection); 
?>

</body>
</html>
