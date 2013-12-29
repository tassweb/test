<?php include 'password_protect.php'; ?>

<?php

	include 'dbinfo.php';
	
	$values=array();
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value; $values[]=$value;}

	$sqls = count($_POST['sqls']) ? $_POST['sqls'] : array();
	
	echo '
		<html>
		<head>
		<link href="blue.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="check.js"></script>
		</head><body>
		<table>
		<caption>SQLs</caption>
		<thead><th>ETL SQL</th></thead>
		';
	
	foreach ($sqls as $sql) {
		echo '<tr><td>'.$sql.'</td></tr>';
	}
	
	echo '</table>';
	
	mysql_close($connection);
	
	include "footer.html";
	
	echo '
		</body>
		</html>
	';
	
	//header( 'Location: index.php' ) ;

?>