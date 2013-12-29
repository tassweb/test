<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
	require_once ('dbfunctions.php');
	
	echo '
			<table>
			<form action="password_reset.php" method="POST">
			<thead><tr><th colspan=2>Password reset</th></tr></thead>
			<tr><td colspan=2>Your password has expired.  Please create a new password.</td></tr>
			<tr><td>Old Password</td><td><INPUT TYPE="password" name="old_password" size="50"></td></tr>
			<tr><td>New Password</td><td><INPUT TYPE="password" name="new_password" size="50"></td></tr>
		    <tr><td>Confirm Password</td><td><INPUT TYPE="password" name="new_password2" size="50"></td></tr>';
	echo '<tr><td colspan=2><INPUT TYPE="submit" value="Submit" class="btn"><input type="button" value="Logout" class="btn" onClick="window.location=\'logout.php\'"></td></tr>';
	echo '</form>
			  </table>
		';
	
	require_once 'footer.php';
	
?>
	
	