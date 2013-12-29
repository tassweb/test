<?php

function getsqlfromfile($filename_) {
	$file_handle = fopen("$filename_", "r") or DIE("File not Found!");
	$sql = '';
	while (!feof($file_handle)) {
	   $sql .= fgets($file_handle);
	}
	fclose($file_handle);
	return $sql;
}

function createfilefromsql($filename_, $sql_) {
	$file_handle = fopen("reports/$filename_", "w");
	fwrite($file_handle, $sql_);
	fclose($file_handle);
}

function writefiletodb($filename_, $reportname_, $id_ = null) {
	if ($id_) {
		$sql = "UPDATE refreporttb SET report_name = '$reportname_', report_path = '$filename_' where report_id = $id_;";
	}
	else {
		$sql = "INSERT into refreporttb values (null,'$reportname_','$filename_','Y');";
	}
	$result = mysql_query($sql) or die ("Error: " . mysql_error());
}

function getreports() {
	$query = "SELECT report_id, report_name, report_path, report_desc from refreporttb where active_ind = 'Y';";
	$reporttb = mysql_query($query) or die ("Error: " . mysql_error());
	$reportArr = array();
	
	while ($report = mysql_fetch_assoc($reporttb)) {
		array_push($reportArr, $report);
	}
	
	return $reportArr;
}

?>