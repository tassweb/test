<?php 
	require_once 'password_protect.php'; 
	require_once 'dbinfo.php';
?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

$values=array();
	
	echo '
		<form method="post" action="update_receipts_confirm.php">
		<table border=1>';
	echo'<caption>Update Printed Receipts</caption>
	<tr><td>Set all unprinted receipts as printed?</td></tr></table>';

	echo '
			<INPUT TYPE="SUBMIT" VALUE="Confirm" NAME="submit" class="btn">
			<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
			</form>';
	
	echo '</body></html>';
	
	mysql_close($connection);
?>