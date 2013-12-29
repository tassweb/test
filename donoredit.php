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
	require_once 'acl.php';
	
	$AUDITHEADERS = array('from_date','to_date','a_create_timestamp','a_create_user','a_update_timestamp','a_update_user');
	$READONLY = false;
	
	$query = "SELECT * FROM dimdonortb WHERE donor_id = ".$_GET['donor_id']." and current_date between from_date and to_date;";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_assoc($result);
	
	$company_layout = $donorinfo['contact_type'] != 'Individual' ? true : false;
	$caption = $company_layout ? $donorinfo['company_name'] : $donorinfo['last_name'].', '.$donorinfo['first_name'];
	
	$query = "select * from refinvolvementtb order by 1;";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donor_involvement = array();
	while ($involvement = mysql_fetch_assoc($result)) {
		array_push($donor_involvement, $involvement);
	}
	
	$query = "select involvement_id from dimdonorinvolvementtb where donor_id = ".$_GET['donor_id']." and current_date between from_date and to_date;";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$current_involvement_ids = array();
	while ($involvement = mysql_fetch_assoc($result)) {
		array_push($current_involvement_ids, $involvement['involvement_id']);
	}
	
	// "special needs" columns
  	$nullColumns = array('donor_id');
  	
	// build the table
	echo '
		<form method="post" action="donorupdate.php">
		<div style="display:block;float:left;">
		<table border=1 name="inner_table">';
	if ($_GET['donor_id']) {
		echo'<caption>'.$caption.'</caption>';
		
	}
  	echo '<thead><tr><th colspan=2>Edit Donor</th></tr></thead>';
  	
	foreach ($donorinfo as $key => $value) {
		if (in_array($key,$AUDITHEADERS))
    		continue;
		
		echo '<tr><td>' . str_replace("_"," ",$key) . ($key == 'donation_date' ? ' (yyyy-mm-dd)' : '') . '</td>';
		// default
		$rowout = $value;
		$rowout = enrichColumns('dimdonortb',$rowout,$key,$value,$READONLY);
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns) || in_array($key,$indColumns)) {
			// do nothing;
		}
		elseif (array_key_exists($key, $dropColumns)) {
			$order_column = $key == 'status' ? '1' : null;
			$rowout = buildGenericDropdownTable($dropColumns[$key],$rowout,$key,$value,$order_column,'dimdonortb');
		}
		elseif (in_array($key,$dateColumns)) {
			$rowout = '<INPUT TYPE="TEXT" NAME="dimdonortb|'.str_replace(" ","_",$key).'" class="tcal" value="'.$rowout.'" />';
		}
		else {
			$readonly = in_array($key,$readonlyColumns) ? ' readonly' : '';
			$rowout = '<INPUT TYPE="TEXT" SIZE="50" NAME="dimdonortb|'.str_replace(" ","_",$key).'" value="'.$rowout.'"'.$readonly.'>';
		}
		echo '<td>'.$rowout.'</td>';
		echo '</tr>
		';
	}

	echo '
	</table>
	<INPUT TYPE="HIDDEN" NAME="dimdonortb|donor_id" VALUE="'.$_GET['donor_id'].'">
	<INPUT TYPE="SUBMIT" VALUE="Edit Donor" NAME="submit" class="btn">';
	if (getWritePerm($current_group)) {
		echo '<INPUT TYPE="SUBMIT" VALUE="Delete Donor" NAME="delete" class="btn">
		';
	}
	
	echo '<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;">
	</div>';
	
		echo '
	<div style="display:block;float:left;margin-left: 5px;margin-top: 28px;">
	<table border=1>
	<thead><tr><th>Involvement</th></tr></thead>
	';
	foreach ($donor_involvement as $involvement) {
		echo '<tr><td><input type="checkbox" name=involvement[] value='.$involvement['involvement_id'];
		if (in_array($involvement['involvement_id'],$current_involvement_ids)) {
			echo ' checked';
		}
		echo '>'.$involvement['involvement_name'].'</td></tr>
		';
	}
	echo '
	</table>
	</div>';
	echo '
	</form>';
    	
	mysql_free_result($result);
	echo '<div style="clear:both">';
	include_once 'footer.php';
	echo '</div>';
	mysql_close($connection); 

?>	
</body>
</html>
