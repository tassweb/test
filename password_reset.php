<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
	require_once 'dbinfo.php';
	
	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	$username = $_COOKIE['nichoir_username'];
	$old_password = stripslashes($old_password);
	$new_password = stripslashes($new_password);
	$new_password2 = stripslashes($new_password2);
	
	$query = "select password from refusertb where username = '$username'";
	$result = mysql_query($query) or die ("Errors: ".mysql_error());
	$row = mysql_fetch_assoc($result);
	
	if ($row['password'] != md5($old_password)) {
		echo 'Incorrect password.  Please press the back button in your browser and try again.';
		include_once 'footer.php';
		die(1);
	}
	if ($new_password != $new_password2) {
		echo 'New passwords don\'t match.  Please press the back button in your browser and try again.';
		include_once 'footer.php';
		die(1);
	}
	if (strlen($new_password) < 6 || strlen($new_password2) < 6) {
		echo 'Passwords must be at least 6 characters long.  Please press the back button in your browser and try again.';
		include_once 'footer.php';
		die(1);
	}
	$new_password = md5($new_password);
	
	$query = "update refusertb set password = '$new_password', password_expired = 'N' where username = '$username'";
	mysql_query($query) or die("Error: ".mysql_error());
	
	echo 'Password reset successful.  Returning to login screen.';
	include_once 'footer.php';
	
	sleep(3);
	header('Location: logout.php');
?>