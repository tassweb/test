<?php include 'password_protect.php'; 

	include 'dbinfo.php';
	include 'dbfunctions.php';
	
	$numericColumns = array('donor_id','receipt_no','component_id','fund_id','category_id','contact_id');
	$ignoreColumns = array('edit','contact_id');
	$auditUserColumns = array('a_create_user','a_update_user');
	$auditTimestampColumns = array('a_create_timestamp','a_update_timestamp');
	
	$current_user = $_COOKIE['nichoir_username'];
	
	if ($_POST['cancel']) {
		header ('Location: contactcancel.php?contact_id='.$_POST['contact_id']);
	}
	
	else if (isset($_POST['fctcontacttb|contact_id']))
	{
		$updstr = 'UPDATE fctcontacttb SET ';
		
		foreach ($_POST as $table_key => $value)
		{
			$keysubstr = explode("|",$table_key);
			if (strcmp($keysubstr[0],"fctcontacttb")) {
				continue;
			}
			else {
				$key = $keysubstr[1];
			}
			
			$rowout = $value;
			
			if (in_array($key,$ignoreColumns)) {
				continue;
			}
			elseif (in_array($key,$currencyColumns)) {
				$rowout = trim($rowout,"$");
			}
			elseif (!in_array($key,$numericColumns)) {
				$rowout = "'".addslashes($rowout)."'";
			}
			elseif (in_array($key,$numericColumns)) {
				if ($value=='') {
					$rowout = 'null';
				}
			}
			
			$updstr .= $key . " = " . $rowout. ", "; 
		}
		
		$updstr .= "a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user)";
		$updstr .= ' WHERE contact_id = '.$_POST['fctcontacttb|contact_id'].'';

		//echo $updstr; exit(1);
	}
	else
	{
		$updstr = 'INSERT INTO fctcontacttb VALUES (';
		
		$query = "SHOW COLUMNS FROM fctcontacttb";
		$result = mysql_query($query) or die("Errors: ".mysql_error());
	
		while ($column = mysql_fetch_assoc($result)) {
			if (isset($_POST['fctcontacttb|'.$column['Field']])) {
				if (in_array($column['Field'],$ignoreColumns)) {
					continue;
				}
				elseif (!in_array($column['Field'],$numericColumns) && !in_array($column['Field'],$currencyColumns)) {
					$updstr .= "'".addslashes($_POST['fctcontacttb|'.$column['Field']])."', ";
				}
				elseif (in_array($column['Field'],$dateColumns) && $_POST['fctcontacttb|'.$column['Field']]=='') {
					$updstr .= "null, ";
				}
				else {
					$updstr .= $_POST['fctcontacttb|'.$column['Field']].", ";
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
		
	}
	
	mysql_query($updstr) or die(mysql_error());
	
	mysql_close($connection);	
	
	header( 'Location: donor.php?donor_id='.$_POST['fctcontacttb|donor_id']) ;
	
?>