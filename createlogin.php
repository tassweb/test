<?php 	
	require_once ('password_protect.php');
	$group_name = $_COOKIE['group_name'];
	if ($group_name != 'Administrator') {
		header( 'Location: index.php') ;
	}
?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

	include 'dbinfo.php';
	foreach($_POST as $key => $value) {$$key = $value;}
	$username = stripslashes($username);
	$password = stripslashes($password);
	$password2 = stripslashes($password2);
	
	if ($password != $password2) {
		echo 'Passwords don\'t match.  Please press the back button in your browser and try again!';
		include_once 'footer.php';
		die(1);
	}
	$password = md5($password);
	
	$query = "select max(user_id) \"user_id\" from refusertb;";
	$result = mysql_query($query);
	$myarray = mysql_fetch_assoc($result);
	$user_id = $myarray['user_id'] + 1;
	mysql_free_result($result);
	
	$query = "select username from refusertb where username = '$username'";
	$result = mysql_query($query) or die("Error: ".mysql_error());
	$numrows = mysql_num_rows($result);
	if ($numrows > 0) {
		echo "$username already exists in the database.  Please press the back button in your browser and try a new username!";
		include_once 'footer.php';
		die(1);
	}
	
	$query = "insert into refusertb values ($user_id,'$username','$password',null,'N','Y',current_timestamp,current_user,current_timestamp,current_user);";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	$query = "insert into refusergrouptb values ($user_id,(select group_id from refgrouptb where group_name = '$group_name'))";
	$result = mysql_query($query) or die("Errors: ".mysql_error());
	
	echo "User $username created with $group_name access.";
	
	mysql_free_result($result);
	
	include_once 'footer.php';
	
	mysql_close($connection); 
?>

</body>
</html>