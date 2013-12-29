<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="calendar/tcal.css" rel="stylesheet" type="text/css" />
<link href="blue.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="calendar/tcal.js"></script>
</head>
<body>
<?php
	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	$AUDITHEADERS = array('from_date','to_date','a_create_timestamp','a_create_user','a_update_timestamp','a_update_user');
	$CONTACTHEADERS = array('contact_by','followup_by');
	$READONLY = false;
	
	foreach($_GET as $key => $value) {$$key = $value;}
	
	// donation
	$query = "SELECT A.* FROM fctcontacttb A WHERE A.contact_id=$contact_id";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donation = mysql_fetch_assoc($result);

	// donor
	$donor_id = $donation['donor_id'];
	$query = "SELECT * FROM dimdonortb WHERE donor_id = $donor_id and current_date between from_date and to_date";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_assoc($donor);
	
	// "special needs" columns
  	$nullColumns = array('contact_id');
	$textColumns = array('contact_text');
	
	// build the table
	echo '
		<form method="post" action="contactupdate.php">
		<table border=1>';
	if ($contact_id) {
		echo'<caption>'.$donorinfo['last_name'].', '.$donorinfo['first_name'].'</caption>';
	}
	
	echo '<thead><tr><th colspan=2>Contact Details</th></tr></thead>';
	
	foreach ($donation as $key => $value) {
		if (in_array($key,$ignoreColumns) || in_array($key,$AUDITHEADERS)) {
			continue;
		}
		
		echo '<tr><td>' . str_replace("ind","",str_replace("_"," ",$key)) . '</td>';
		
		// default
		$rowout = $value;
		$rowout = enrichColumns('fctcontacttb',$rowout, $key, $value, $READONLY);
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns) || in_array($key,$indColumns)) {
			// do nothing
		}
		elseif (array_key_exists($key, $dropColumns)) {
			$order_column = in_array($key,$CONTACTHEADERS) ? '1' : null;
			$rowout = buildGenericDropdownTable($dropColumns[$key],$rowout,"fctcontacttb|".$key,$value,$order_column);
		}
		elseif (in_array($key, $textColumns)) {
			$rowout = '<TEXTAREA COLS="50" ROWS="5" NAME="fctcontacttb|'.str_replace(" ","_",$key).'">'.$rowout.'</TEXTAREA>';
		}
		elseif (in_array($key,$dateColumns)) {
			$rowout = '<INPUT TYPE="TEXT" NAME="fctcontacttb|'.str_replace(" ","_",$key).'" class="tcal" value="'.$rowout.'" />';
		}
		else {
			$readonly = in_array($key,$readonlyColumns) ? ' readonly' : '';
			$rowout = '<INPUT TYPE="TEXT" SIZE="50" NAME="fctcontacttb|'.str_replace(" ","_",$key).'" value="'.$rowout.'"'.$readonly.'>';
		}
		echo '<td>'.$rowout.'</td>';
		echo '</tr>';
	}
	
   	echo '
    	</table>
    	<INPUT TYPE="HIDDEN" NAME="fctcontacttb|donor_id" VALUE="'.$donor_id.'">
    	<INPUT TYPE="HIDDEN" NAME="fctcontacttb|contact_id" VALUE="'.$contact_id.'">
   	    <INPUT TYPE="SUBMIT" VALUE="Edit Contact" NAME="edit" class="btn">';
	echo '
    	<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
    	</form>';
    	
	mysql_free_result($result);
	mysql_free_result($donor);

	include_once 'footer.php';
	
	mysql_close($connection); 
?>

</body>
</html>
