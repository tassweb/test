<?php include 'password_protect.php'; ?>

<?php
	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	$ignoreColumns = array('submit','involvement');
	$auditUserColumns = array('a_create_user','a_update_user');
	$auditTimestampColumns = array('a_create_timestamp','a_update_timestamp');
	
	$current_user = $_COOKIE['nichoir_username'];
	$new_donor = false;
	
	if ($_POST['delete']) {
		header ('Location: donordelete.php?donor_id='.$_POST['dimdonortb|donor_id']);
	}
	
	if (isset($_POST['dimdonortb|donor_id']))
	{
		//Expire current donor row
		$updstr = 'UPDATE dimdonortb SET to_date = current_date-1, ';
		$updstr .= "a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user)";
		$updstr .= ' WHERE donor_id =\''.$_POST['dimdonortb|donor_id'].'\' and current_date between from_date and to_date';
		mysql_query($updstr) or DIE ("Error: " . mysql_error());
		
		//Clean up "dead" rows (where from_date = to_date)
		$query = "DELETE from dimdonortb where from_date >= to_date and donor_id = " . $_POST['dimdonortb|donor_id'];
		mysql_query($query) or DIE ("Error: " . mysql_error());
	}
	else {
		$_POST['dimdonortb|donor_id'] = 'null';
		$_POST['dimdonortb|creation_date'] = date("Y-m-d");
		$new_donor = true;
	}

	//Insert new donor row
	$updstr = 'INSERT INTO dimdonortb (';
	foreach ($_POST as $table_key => $value) {
		$keysubstr = explode("|",$table_key);
		if (strcmp($keysubstr[0],"dimdonortb")) {
			continue;
		}
		else {
			$key = $keysubstr[1];
		}
		if (in_array($key,$ignoreColumns)) {
			continue;
		}
		$updstr .= $key . ',';
	}
	$updstr .= 'from_date,to_date,a_create_timestamp,a_create_user,a_update_timestamp,a_update_user';
	$updstr .= ') VALUES (';
	foreach ($_POST as $key => $value) {
		if (in_array($key,$ignoreColumns)) {
			continue;
		}
		if ($value == 'null')
			$updstr .= "null,";
		else
			$updstr .= "'".addslashes($value)."', ";
	}
	$updstr = substr($updstr,0,strlen($updstr)-2);
	$updstr .= ",current_date,'2099-01-01',current_timestamp,concat('$current_user','.',current_user),current_timestamp,concat('$current_user','.',current_user));";

	mysql_query($updstr) or die(mysql_error());
	
	if ($new_donor) {
		$_POST['dimdonortb|donor_id'] = mysql_insert_id();
	}

	//Expire current involvement rows
	$query = "UPDATE dimdonorinvolvementtb SET to_date = current_date-1, a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user) where donor_id = ".$_POST['dimdonortb|donor_id']." and current_date between from_date and to_date;";
	mysql_query($query) or DIE("Error: " . mysql_error());

	//Clean up "dead" rows (where from_date = to_date)
	$query = "DELETE from dimdonorinvolvementtb where from_date >= to_date and donor_id = " . $_POST['dimdonortb|donor_id'];
	mysql_query($query) or DIE ("Error: " . mysql_error());
		
	//Insert new involvement rows
	foreach ($_POST['involvement'] as $key => $involvement_id) {
		$query = "INSERT into dimdonorinvolvementtb values (".$_POST['dimdonortb|donor_id'].", $involvement_id, current_date, '2099-01-01', current_timestamp, concat('$current_user','.',current_user), current_timestamp, concat('$current_user','.',current_user));";
		mysql_query($query) or DIE("Error: " . mysql_error());
	}

	mysql_close($connection);
	
	header( 'Location: donor.php?donor_id='.$_POST['dimdonortb|donor_id']) ;
	
?>