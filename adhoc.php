<?php 

	require_once ('password_protect.php');
	//require_once ('dbfunctions.php');
	//include 'dbinfo.php';
	
	echo '
		<html>
		<head>
		<link href="blue.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="check.js"></script>
		</head>
		<body>
	';
	
	echo '
		<form method="post" action="index.php">
	';
	
	echo '
		<table>
		<caption>SQL Query</caption>
		<thead><tr><th>Query</th></tr></thead>
		<tr><td><TEXTAREA name="adhocsql" value="adhocsql" rows="5" cols="50"></TEXTAREA></td></tr>
		<tr><td><INPUT TYPE="SUBMIT" VALUE="Submit!" class="btn"></tr></td>
		</table>
	';
	
	include "footer.html";

	echo '
		<INPUT TYPE="HIDDEN" NAME="rows" VALUE="-1">
		</form>
		</body>
		</html>
	';

?>