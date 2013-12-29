<?php 

	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	require_once ('filereader.php');
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value;}
	$sql = getsqlfromfile($path);
	echo $sql;
	return $sql;
	//createGroupedTableFromSql($sql,$report_id);
	
?>