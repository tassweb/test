<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="confirm_reset.php" method="POST">
		<table>
		<thead><tr><th colspan=2>Reset password?</th></tr></thead>
		<tr><td>Confirm username</td><td><INPUT TYPE="TEXTBOX" name="username" size="50"></td></tr>
		<tr><td colspan=2><INPUT TYPE="submit" class="btn" value="Reset password"></td></tr>
		</table>
</form>
<?php 	
	require_once 'footer.php';
?>
</body></html>