<?php include 'password_protect.php'; 

	include 'dbinfo.php';
	include 'dbfunctions.php';
	
	$group_name = $_COOKIE['group_name'];
	if ($group_name == 'Read') {
		header( 'Location: index.php') ;
	}
	
	$numericColumns = array('donor_id','receipt_no','component_id','fund_id','category_id');
	$ignoreColumns = array('donation_id','edit');
	$auditUserColumns = array('a_create_user','a_update_user');
	$auditTimestampColumns = array('a_create_timestamp','a_update_timestamp');
	$categoryColumns = array('revenue_stream','type','origin');
	
	$current_user = $_COOKIE['nichoir_username'];
	
	if (isset($_POST['cancel'])) {
		header ('Location: donationcancel.php?donation_id='.$_POST['fctdonationstb|donation_id']);
		exit(1);
	}
	
	// Update Donation
	
	if (isset($_POST['fctdonationstb|'.'donation_id']))
	{
		$updstr = 'UPDATE fctdonationstb SET ';
		
		foreach ($_POST as $table_key => $value)
		{
			$keysubstr = explode("|",$table_key);
			if (strcmp($keysubstr[0],"fctdonationstb")) {
				continue;
			}
			else {
				$key = $keysubstr[1];
			}
			
			$rowout = $value;
			
			if (in_array($key,$ignoreColumns) || in_array($key,$categoryColumns)) {
				continue;
			}
			elseif (in_array($key,$dateColumns)) {
				$rowout = ($value=='' || empty($value)) ? 'null' : "'".addslashes($rowout)."'";
			}
			elseif (in_array($key,$amtColumns)) {
				$rowout = trim($rowout,"$");
			}
			elseif (!in_array($key,$numericColumns)) {
				$rowout = "'".addslashes($rowout)."'";
			}
			elseif (in_array($key,$numericColumns)) {
				if ($value=='' || empty($value)) {
					$rowout = 'null';
				}
			}

			if (!strcmp($key,'receipt_required_ind') && ($value=="Y") && !isset($_POST['fctdonationstb|'.'receipt_no']))
			{
				$newquery = "SELECT MAX(receipt_no) FROM fctdonationstb";
				echo $newquery;
				exit(1);
				$result = mysql_query($newquery) or die(mysql_error());
				$row = mysql_fetch_array($result);
				$_POST['fctdonationstb|'.'receipt_no'] = $row['MAX(receipt_no)'] + 1;
				$receiptsupdated = 1;
				$_POST['fctdonationstb|'.'receipt_date'] = date("Y-m-d");
			}
			
			$updstr .= $key . " = " . $rowout. ", "; 
		}
		
		$updstr .= "a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user)";
		$updstr .= ' WHERE donation_id =\''.$_POST['fctdonationstb|'.'donation_id'].'\'';
		
		$receiptnum = $_POST['fctdonationstb|'.'receipt_no'];
		
		mysql_query($updstr) or die(mysql_error());
		
	}
	
	// New donation
	
	else
	{
		$updstr = 'INSERT INTO fctdonationstb VALUES (';
		
		if ($_POST['fctdonationstb|'.'receipt_required_ind']=='Y')
		{
			$newquery = "SELECT MAX(receipt_no) FROM fctdonationstb";
			$result = mysql_query($newquery) or die(mysql_error());
			$row = mysql_fetch_array($result);
			$_POST['fctdonationstb|'.'receipt_no'] = $row['MAX(receipt_no)'] + 1;
			$_POST['fctdonationstb|'.'receipt_date'] = date("Y-m-d");
		}
		
		$query = "SHOW COLUMNS FROM fctdonationstb";
		$result = mysql_query($query) or die("Errors: ".mysql_error());
	
		while ($column = mysql_fetch_assoc($result)) {
			if (isset($_POST['fctdonationstb|'.$column['Field']])) {
				if (in_array($column['Field'],$ignoreColumns)) {
					continue;
				}
				elseif (!in_array($column['Field'],$numericColumns) && !in_array($column['Field'],$amtColumns)) {
					$updstr .= "'".addslashes($_POST['fctdonationstb|'.$column['Field']])."', ";
				}
				elseif (in_array($column['Field'],$dateColumns) && $_POST['fctdonationstb|'.$column['Field']]=='') {
					$updstr .= "null, ";
				}
				else {
					$updstr .= $_POST['fctdonationstb|'.$column['Field']].", ";
				}
			}
			elseif (in_array($column['Field'], $auditTimestampColumns)){
				$updstr .= "current_timestamp, ";
			}
			elseif (in_array($column['Field'], $auditUserColumns)) {
				$updstr .= "concat('$current_user','.',current_user), ";
			}
			else {
				$updstr .= "null, ";
			}
		}
		
		$updstr = substr($updstr,0,strlen($updstr)-2);
		$updstr .= ')';
		mysql_query($updstr) or die(mysql_error());
	}
	
	// Update Thanks
	if (isset($_POST['thankyou_checkbox'])) {
		if (isset($_POST['fctthankyoutb|'.'donation_id']))
		{
			$updstr = 'UPDATE fctthankyoutb SET ';
		
			foreach ($_POST as $table_key => $value)
			{
				$keysubstr = explode("|",$table_key);
				if (strcmp($keysubstr[0],"fctthankyoutb")) {
					continue;
				}
				else {
					$key = $keysubstr[1];
				}
		
				$rowout = $value;
		
				if (in_array($key,$ignoreColumns) || in_array($key,$categoryColumns)) {
					continue;
				}
				elseif (in_array($key,$dateColumns)) {
					$rowout = ($value=='' || empty($value)) ? 'null' : "'".addslashes($rowout)."'";
				}
				elseif (in_array($key,$amtColumns)) {
					$rowout = trim($rowout,"$");
				}
				elseif (!in_array($key,$numericColumns)) {
					$rowout = "'".addslashes($rowout)."'";
				}
				elseif (in_array($key,$numericColumns)) {
					if ($value=='' || empty($value)) {
						$rowout = 'null';
					}
				}
		
				$updstr .= $key . " = " . $rowout. ", ";
			}
		
			$updstr .= "a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user)";
			$updstr .= ' WHERE donation_id =\''.$_POST['fctdonationstb|'.'donation_id'].'\'';
		
			mysql_query($updstr) or die(mysql_error());
		}
		
		// New Thank You
		
		else
		{
			$updstr = 'INSERT INTO fctthankyoutb VALUES ('.$_POST['fctdonationstb|donation_id'].',';
		
			$query = "SHOW COLUMNS FROM fctthankyoutb";
			$result = mysql_query($query) or die("Errors: ".mysql_error());
		
			while ($column = mysql_fetch_assoc($result)) {
				if (isset($_POST['fctthankyoutb|'.$column['Field']])) {
					if (in_array($column['Field'],$ignoreColumns)) {
						continue;
					}
					elseif (in_array($column['Field'],$dateColumns) && $_POST['fctthankyoutb|'.$column['Field']]=='') {
						$updstr .= "null, ";
					}
					elseif (!in_array($column['Field'],$numericColumns) && !in_array($column['Field'],$amtColumns)) {
						$updstr .= "'".addslashes($_POST['fctthankyoutb|'.$column['Field']])."', ";
					}
					else {
						$updstr .= $_POST['fctthankyoutb|'.$column['Field']].", ";
					}
				}
				elseif (in_array($column['Field'],$ignoreColumns)) {
					continue;
				}
				elseif (in_array($column['Field'], $auditTimestampColumns)){
					$updstr .= "current_timestamp, ";
				}
				elseif (in_array($column['Field'], $auditUserColumns)) {
					$updstr .= "concat('$current_user','.',current_user), ";
				}
				else {
					$updstr .= "null, ";
				}
			}
		
			$updstr = substr($updstr,0,strlen($updstr)-2);
			$updstr .= ')';

			mysql_query($updstr) or die(mysql_error());
		}	
	}
	
	
	
	
	// Sanitize receipts printed column
	mysql_query("UPDATE fctdonationstb SET receipt_printed_ind = 'N' WHERE receipt_printed_ind IS NULL AND receipt_required_ind = 'Y'") or die(mysql_error());
	
	// Make donor active (in case it wasn't)
	$query = 'SELECT status FROM dimdonortb WHERE donor_id = '.$_POST['fctdonationstb|donor_id'].' and current_date between from_date and to_date';
	$result = mysql_query($query) or die ('Error: ' . mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row['status'] != 'active') {
		// Call Donor activation Sproc
		$query = 'CALL lnactivatedonorsp ('.$_POST['fctdonationstb|donor_id'].',\''.$_POST['fctdonationstb|donation_date'].'\',\''.$current_user.'\')';
		mysql_query($query) or die ('Error: ' . mysql_error());
	}
	else {
		// Call Donor backdating sproc
		$query = 'CALL lnbackdatedonorsp ('.$_POST['fctdonationstb|donor_id'].',\''.$_POST['fctdonationstb|donation_date'].'\')';
		mysql_query($query) or die ('Error: ' . mysql_error());
	}
	
	mysql_close($connection);	
	header( 'Location: donor.php?donor_id='.$_POST['fctdonationstb|donor_id']) ;
	
?>