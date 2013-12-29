<?php 	
	require_once 'password_protect.php'; 
	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	$group_name = $_COOKIE['group_name'];
	if ($group_name == 'Read') {
		header( 'Location: index.php') ;
	}
?>
<html>
<head>
<link href="calendar/tcal.css" rel="stylesheet" type="text/css" />
<link href="blue.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="calendar/tcal.js"></script> 
<script type="text/javascript" src="getCategory.js"></script>
</head>
<?php

	$AUDITHEADERS = array('from_date','to_date','a_create_timestamp','a_create_user','a_update_timestamp','a_update_user');
	$READONLY = false;
	
	foreach($_GET as $key => $value) {$$key = $value;}
	
	// donation
	$query = "SELECT A.*, B.cancellation_id FROM fctdonationstb A left outer join fctcancelledreceiptstb B on (A.donation_id = B.donation_id) WHERE (A.donation_id=$donation_id)";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donationtb = mysql_fetch_assoc($result);

	// donor
	$donor_id = $donationtb['donor_id'];
	$query = "SELECT * FROM dimdonortb WHERE donor_id = $donor_id and current_date between from_date and to_date";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfotb = mysql_fetch_assoc($donor);
	
	// thanks
	$query = "SELECT * from fctthankyoutb WHERE donation_id = $donation_id";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$thankstb = mysql_fetch_assoc($result);
	$thanks_donation_id = $thankstb['donation_id'];
	
	if (empty($thankstb)) {
		$headers = getheaders('fctthankyoutb');
		foreach ($headers as $header) {
			$thankstb[$header] = '';
		}
		$checkboxchecked = '';
	}
	else {
		$checkboxchecked = 'checked';
	}
	
	// "special needs" columns
	$readonlyColumns = array('receipt_no');
  	$nullColumns = array('donation_id');
	$ignoreColumns = array('cancellation_id','contact_id');
	$cidColumns = array('component_id');
	$catColumns = array('category_id');
	$yearColumns = array('donation_year');
	
  	if ($donationtbinfo['receipt_required_ind']=="Y") {
  		array_push($readonlyColumns, 'receipt_required_ind');
	}
	
	// get category id
	if (isset($donationtb['category_id'])) {
		$category_id = $donationtb['category_id'];
		$date = isset($donationtb_date) ? $donationtb_date : 'current_date';
		$query = 'select distinct revenue_stream, type, origin from dimcategorytb where category_id = '.$category_id.' and '.$date.' between from_date and to_date';
		$result = mysql_query($query) or die("Error: ". mysql_error());
		$row = mysql_fetch_assoc($result);
		$revenue_stream = $row['revenue_stream'];
		$type = $row['type'];
		$origin = $row['origin'];
		
		echo '<body onload="loadCategories(\''.$revenue_stream.'\',\''.$type.'\',\''.$origin.'\')">';
	}
	else {
		echo '<body onload="getRevenueStream()">';
	}
	
	// build the table
	echo '
		<form method="post" name="donationfrm" action="donationupdate.php">
		<table border=1>';
	if ($donation_id) {
		echo'<caption>'.$donorinfotb['last_name'].', '.$donorinfotb['first_name'].'</caption>';
	}
	
	echo '<thead><tr><th colspan=2>Donation Details</th></tr></thead>';
	
	foreach ($donationtb as $key => $value) {
		if (in_array($key,$ignoreColumns) || in_array($key,$AUDITHEADERS)) {
			continue;
		}
		
		echo '<tr><td>' . str_replace("ind","",str_replace("_"," ",$key)) . '</td>';
		
		// default
		$rowout = $value;
		$rowout = enrichColumns('fctdonationstb',$rowout, $key, $value, $READONLY);
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns) || in_array($key,$indColumns)) {
			// do nothing
		}
		elseif (in_array($key,$catColumns)) {
			$rowout = '<table><tr><td><div id="revenueStreamArea"></div></td>
					<td><div id="typeArea"></div></td>
					<td><div id="originArea"></div></td></tr></table>';
		}
		elseif (array_key_exists($key, $dropColumns)) {
			$order_column = $key == 'fund_id' ? '1' : null;
			$rowout = buildGenericDropdownTable($dropColumns[$key],$rowout,$key,$value,null,"fctdonationstb");
		}
		elseif (in_array($key,$dateColumns)) {
			$rowout = '<INPUT TYPE="TEXT" NAME="fctdonationstb|'.str_replace(" ","_",$key).'" id="date" class="tcal" value="'.$rowout.'" onblur="updateYear()"/>';
		}
		elseif (in_array($key,$yearColumns)) {
			$rowout = '<INPUT TYPE="TEXT" NAME="fctdonationstb|'.str_replace(" ","_",$key).'" id="year" value="'.$rowout.'"/>';
		}
		
		else {
			$readonly = in_array($key,$readonlyColumns) ? ' readonly' : '';
			$rowout = '<INPUT TYPE="TEXT" SIZE="50" NAME="fctdonationstb|'.str_replace(" ","_",$key).'" value="'.$rowout.'"'.$readonly.'>';
		}
		echo '<td>'.$rowout.'</td>';
		echo '</tr>';
	}

	if ($donationtb['cancellation_id']){
		echo '<tr><td bgcolor="#D9D9D9" colspan=2>Receipt cancelled</td></tr>';
	}
	
   	echo '
    	</table>
    	<INPUT TYPE="HIDDEN" NAME="fctdonationstb|donor_id" VALUE="'.$donor_id.'">
    	<INPUT TYPE="HIDDEN" NAME="fctdonationstb|donation_id" VALUE="'.$donation_id.'">';
   	if (!is_null($thanks_donation_id)) {
   		echo '
   		<INPUT TYPE="HIDDEN" NAME="fctthankyoutb|donation_id" VALUE="'.$thanks_donation_id.'">';
   	}
   	
   	// Thanks
   	echo '<br><table border=1>
   	<thead><tr><th colspan=2>Thank-yous</th></tr></thead>
   	<tr><td>Save Thank You</td><td><INPUT TYPE="CHECKBOX" NAME="thankyou_checkbox" VALUE="thankyou_checkbox" '.$checkboxchecked.'></td></tr>';
   	
   	foreach ($thankstb as $key => $value) {
   		if (in_array($key,$ignoreColumns) || in_array($key,$AUDITHEADERS)) {
   			continue;
   		}
   		
   		echo '<tr><td>' . str_replace("ind","",str_replace("_"," ",$key)) . '</td>';
   		
   		// default
   		$rowout = $value;
   		$rowout = enrichColumns('fctthankyoutb',$rowout, $key, $value, $READONLY);
   		
   		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
   		if (in_array($key, $nullColumns) || in_array($key,$indColumns)) {
   			// do nothing
   		}
   		elseif (array_key_exists($key, $dropColumns)) {
   			$order_column = $key == 'fund_id' ? '1' : null;
   			$rowout = buildGenericDropdownTable($dropColumns[$key],$rowout,$key,$value,null,'fctthankyoutb');
   		}
   		elseif (in_array($key,$dateColumns)) {
   			$rowout = '<INPUT TYPE="TEXT" NAME="fctthankyoutb|'.str_replace(" ","_",$key).'" id="date" class="tcal" value="'.$rowout.'" onblur="updateYear()"/>';
   		}
   		elseif (in_array($key,$yearColumns)) {
   			$rowout = '<INPUT TYPE="TEXT" NAME="fctthankyoutb|'.str_replace(" ","_",$key).'" id="year" value="'.$rowout.'"/>';
   		}
   		
   		else {
   			$readonly = in_array($key,$readonlyColumns) ? ' readonly' : '';
   			$rowout = '<INPUT TYPE="TEXT" SIZE="50" NAME="fctthankyoutb|'.str_replace(" ","_",$key).'" value="'.$rowout.'"'.$readonly.'>';
   		}
   		echo '<td>'.$rowout.'</td>';
   		echo '</tr>';
   	}
   	
   	echo '</table>';
   	
	if (!$donationtb['cancellation_id']) {
		echo '
    	<br><INPUT TYPE="SUBMIT" VALUE="Edit Donation" NAME="edit" class="btn">';
		if ($donationtb['receipt_no']) {
			echo '<INPUT TYPE="SUBMIT" VALUE="Cancel Receipt" NAME="cancel" class="btn">';
		}
		else {
			echo '<INPUT TYPE="SUBMIT" VALUE="Cancel Donation" NAME="cancel" class="btn">';
		}
	}
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
