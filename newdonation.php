<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="calendar/tcal.css" rel="stylesheet" type="text/css" />
<link href="blue.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="calendar/tcal.js"> </script>
<script type="text/javascript" src="getCategory.js" > </script>
</head>
<body onload="getRevenueStream()">
<?php
	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	$AUDITHEADERS = array('from_date','to_date','a_create_timestamp','a_create_user','a_update_timestamp','a_update_user');
	$READONLY = false;
	
	foreach($_GET as $key => $value) {$$key = $value;}
	
	$query = "SELECT * FROM dimdonortb WHERE donor_id ='$donor_id' and current_date between from_date and to_date";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_array($donor);
	
	$query = "SHOW COLUMNS FROM fctdonationstb";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donationinfo = array();
	
	while ($column = mysql_fetch_assoc($result)) {
		array_push($donationinfo, $column['Field']);
	}
	
	$query = "SHOW COLUMNS FROM fctthankyoutb";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$thankstb = array();
	
	while ($column = mysql_fetch_assoc($result)) {
		array_push($thankstb, $column['Field']);
	}
	
	// "special needs" columns
  	$nullColumns = array('donation_id','donor_id','receipt_printed_ind','receipt_no','receipt_date');
  	$cidColumns = array('component_id');
  	$catColumns = array('category_id');
  	$yearColumns = array('donation_year');
  	$ignoreColumns = array('cancellation_id','contact_id');
  	
	// build the table
	echo '
		<form method="post" action="donationupdate.php">
		<table border=1>';
	
	if ($donor_id) {
		echo'<caption>'.$donorinfo['last_name'].', '.$donorinfo['first_name'].'</caption>';
	}
	echo '<thead><tr><th colspan=2>Donation Details</th></tr></thead>';
	
	foreach ($donationinfo as $key) {
		$rowout = '';
		if (in_array($key,$ignoreColumns) || in_array($key,$AUDITHEADERS)) {
			continue;
		}
		
		echo '<tr><td>' . str_replace("ind","",str_replace("_"," ",$key)) . '</td>';
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns)) {
			$rowout = 'New Donation';
		}
		elseif (in_array($key,$catColumns)) {
			$rowout = '<table><tr><td><div id="revenueStreamArea"></div></td>
							<td><div id="typeArea"></div></td>
							<td><div id="originArea"></div></td></tr></table>';
		}
		elseif (in_array($key, $indColumns)) {
			$rowout = '<INPUT TYPE="hidden" name="fctdonationstb|'.$key.'" value="N" /><INPUT TYPE="checkbox" NAME="fctdonationstb|'.$key.'" VALUE="Y" />';
		}
		elseif (array_key_exists($key, $dropColumns)) {
			$order_column = $key == 'fund_id' ? '1' : null;
			$default_selected = $key == 'fund_id' ? 0 : null;
			$rowout = buildGenericDropdownTable($dropColumns[$key],null,$key,$default_selected,$order_column,'fctdonationstb');
		}
		elseif (in_array($key,$dateColumns)) {
			$rowout = '<INPUT TYPE="TEXT" NAME="fctdonationstb|'.str_replace(" ","_",$key).'" id="date" class="tcal" onblur="updateYear()"/>';
		}
		elseif (in_array($key,$yearColumns)) {
			$rowout = '<INPUT TYPE="TEXT" NAME="fctdonationstb|'.str_replace(" ","_",$key).'" id="year" />';
		}
		else {
			$readonly = in_array($key,$readonlyColumns) ? ' readonly' : '';
			$rowout = '<INPUT TYPE="TEXT" SIZE="50" NAME="fctdonationstb|'.str_replace(" ","_",$key).'" value="'.$rowout.'"'.$readonly.'>';
		}
		echo '<td>'.$rowout.'</td>';
		echo '</tr>';
	}

	echo '
	</table>';
	
	// Thanks
	
	echo '<br><table border=1>
   	<thead><tr><th colspan=2>Thank-yous</th></tr></thead>';
	
	foreach ($thankstb as $key) {
		$rowout = '';
		if (in_array($key,$ignoreColumns) || in_array($key,$AUDITHEADERS)) {
			continue;
		}
		
		echo '<tr><td>' . str_replace("ind","",str_replace("_"," ",$key)) . '</td>';
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns)) {
			$rowout = 'New Donation';
		}
		elseif (in_array($key, $indColumns)) {
			$rowout = '<INPUT TYPE="hidden" name="fctthankyoutb|'.$key.'" value="N" /><INPUT TYPE="checkbox" NAME="'.$key.'" VALUE="Y" />';
		}
		elseif (array_key_exists($key, $dropColumns)) {
			$order_column = $key == 'fund_id' ? '1' : null;
			$default_selected = $key == 'fund_id' ? 0 : null;
			$rowout = buildGenericDropdownTable($dropColumns[$key],null,$key,$default_selected,$order_column,'fctthankyoutb');
		}
		elseif (in_array($key,$dateColumns)) {
			$rowout = '<INPUT TYPE="TEXT" NAME="fctthankyoutb|'.str_replace(" ","_",$key).'" id="date" class="tcal" onblur="updateYear()"/>';
		}
		else {
			$readonly = in_array($key,$readonlyColumns) ? ' readonly' : '';
			$rowout = '<INPUT TYPE="TEXT" SIZE="50" NAME="fctthankyoutb|'.str_replace(" ","_",$key).'" value="'.$rowout.'"'.$readonly.'>';
		}
		echo '<td>'.$rowout.'</td>';
		echo '</tr>';
	}
	
	echo '</table>';
	echo '
	<INPUT TYPE="HIDDEN" NAME="fctdonationstb|donor_id" VALUE="'.$donor_id.'">
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
