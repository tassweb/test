<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	$AUDITHEADERS = array('from_date','to_date','a_create_timestamp','a_create_user','a_update_timestamp','a_update_user');
	$READONLY = false;
	
	$query = "SHOW COLUMNS FROM dimdonortb";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = array();
	while ($column = mysql_fetch_assoc($result)) {
		array_push($donorinfo, $column['Field']);
	}

	$query = "select * from refinvolvementtb order by 1;";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donor_involvement = array();
	while ($involvement = mysql_fetch_assoc($result)) {
		array_push($donor_involvement, $involvement);
	}
	
	$query = "select distinct status_id, status_code, status_desc from dimstatustb where current_date between from_date and to_date order by 3;";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$donor_status = array();
	while ($status = mysql_fetch_assoc($result)) {
		array_push($donor_status, $status);
	}
	
	// "special needs" columns
  	$nullColumns = array('donor_id','creation_date');
  	// build the table
	echo '
		<form method="post" action="donorupdate.php">
		<div style="display:block;float:left;">
		<table border=1>
		<caption>New Donor</caption>
		<thead><tr><th colspan=2>Donor Details</th></tr></thead>';
  	
	foreach ($donorinfo as $key) {
		
		$rowout = '';
		
		if (in_array($key,$AUDITHEADERS))
			continue;
		echo '<tr><td>' . str_replace("_"," ",$key) . ($key == 'donation_date' ? ' (yyyy-mm-dd)' : '') . '</td>';
		
		// if null columns, don't want to be editing them via textbox, so just surround with a simple <TD>
		if (in_array($key, $nullColumns)) {
			$rowout = 'New Donor';
		}
		elseif (in_array($key,$indColumns)) {
			$rowout = '<INPUT TYPE="hidden" name="dimdonortb|'.$key.'" value="N" /><INPUT TYPE="checkbox" NAME="dimdonortb|'.$key.'" VALUE="Y" />';
		}
		elseif (array_key_exists($key, $dropColumns)) {
			$rowout = buildGenericDropdownTable($dropColumns[$key],$rowout,$key,$value,null,'dimdonortb');
		}
		else {
			$rowout = '<INPUT TYPE="TEXT" SIZE="100" NAME="dimdonortb|'.str_replace(" ","_",$key).'">';
		}
		echo "<td>$rowout</td>";
		echo '</tr>';
	}

	echo '
	</table>
	<INPUT TYPE="SUBMIT" VALUE="Create Donor" class="btn">
    <INPUT TYPE="button" VALUE="Back" onClick="history.go(-1);return true;" class="btn">
	</div>';
	
	echo '
	<div style="display:block;float:left;margin-left: 5px;margin-top: 28px;">
	<table border=1>
	<thead><tr><th>Involvement</th></tr></thead>
	';
	foreach ($donor_involvement as $involvement) {
		echo '<tr><td><input type="checkbox" name=involvement[] value='.$involvement['involvement_id'].'>'.$involvement['involvement_name'].'</td></tr>
		';
	}
	echo '
	</table>
	</div>';
	echo '
	</form>';
    	
	mysql_free_result($result);
	
	echo '<div style="clear:both">';
	require_once 'footer.php';
	echo '</div>';
	
	mysql_close($connection); 

?>	
</body>
</html>
