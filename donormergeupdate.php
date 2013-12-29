<?php include 'password_protect.php'; ?>

<?php
	include 'dbinfo.php';
	$values=array();
	$current_user = $_COOKIE['nichoir_username'];
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value; $values[]=$value;}

	
	//Verify donor exists
	$sql = "select count(*) 'count' from dimdonortb where current_date between from_date and to_date and donor_id = ".$donor_id;
	$query = mysql_query($sql) or die("Errors: ".mysql_error());
	$donorcount = mysql_fetch_array($query);
	$count = $donorcount['count'];
	
	if ($count < 1) {
		echo 'Donor ID not found';
		exit;
	}

	
	//Verify target donor exists
	$sql = "select count(*) 'count' from dimdonortb where current_date between from_date and to_date and donor_id = ".$target_donor_id;
	$query = mysql_query($sql) or die("Errors: ".mysql_error());
	$donorcount = mysql_fetch_array($query);
	$count = $donorcount['count'];
	
	if ($count < 1) {
		echo 'Target Donor ID not found';
		exit;
	}

	
	//Move donations from donor to target
	$sql = "update fctdonationstb set donor_id = $target_donor_id where donor_id = $donor_id";
	$query = mysql_query($sql) or die("Errors: ".mysql_error());

	
	//Merge donor details into target details
	$sql = "select * from dimdonortb where donor_id = $donor_id and current_date between from_date and to_date";
	$query = mysql_query($sql) or die("Errors: ".mysql_error());
	$source_donor = mysql_fetch_array($query, MYSQL_ASSOC);
	
	$sql = "select * from dimdonortb where donor_id = $target_donor_id and current_date between from_date and to_date";
	$query = mysql_query($sql) or die("Errors: ".mysql_error());
	$target_donor = mysql_fetch_array($query, MYSQL_ASSOC);
	
	foreach ($target_donor as $value) {
		if (empty($target_donor[key($target_donor)]) && !empty($source_donor[key($target_donor)])) {
			$target_donor[key($target_donor)] = $source_donor[key($target_donor)];
		}
		next($target_donor);
	}
	
	$sql = "SHOW COLUMNS FROM dimdonorstb";
	$query = mysql_query($sql);
	$column_names = mysql_fetch_array($query);	
	
	$updstr = 'UPDATE dimdonortb SET ';
	while ($column_names = mysql_fetch_array($query))
	{
		if ($target_donor[$column_names[0]] != NULL) {
			$updstr .= '`'.$column_names[0].'`=\''.addslashes($target_donor[$column_names[0]]).'\', ';
		}
	}

	$updstr .= "a_update_user = concat('$current_user','.',current_user), a_update_timestamp = current_timestamp WHERE donor_id ='$target_donor_id' and current_date between from_date and to_date";
	$query = mysql_query($updstr);

	//Make note in new and old donor about the merge
	$sql = "update dimdonortb set notes = concat(notes,'; Merged donor ".$donor_id."') where donor_id = ".$target_donor_id." and current_date between from_date and to_date;";
	mysql_query($sql) or die("Errors: ".mysql_error());
	
	$sql = "update dimdonortb set notes = concat(notes,'; Merged into donor ".$target_donor_id."') where donor_id = ".$donor_id." and current_date between from_date and to_date;";
	mysql_query($sql) or die("Errors: ".mysql_error());
	
	//Delete original donor
	$sql = "update dimdonortb set to_date = current_date-1, a_update_timestamp = current_timestamp, a_update_user = concat('$current_user','.',current_user) where donor_id = $donor_id and current_date between from_date and to_date";
	$query = mysql_query($sql);
	
	mysql_close($connection);
	
	header( 'Location: donor.php?donor_id='.$target_donor_id ) ;
	
?>