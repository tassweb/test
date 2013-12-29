<?php

	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	foreach($_GET as $key => $value) {
		$$key = $value;
	}
	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	if (isset($report_id)) {
		$query = "select key_text, value_text from refreportdetailtb where report_id = " . $report_id;
		$reportDetailTb = mysql_query($query) or die("Error: " . mysql_error());
		$reportDetails = array();
		
		while ($reportDetailArr = mysql_fetch_assoc($reportDetailTb)) {
			$key = $reportDetailArr['key_text'];
			$value = $reportDetailArr['value_text'];
			$reportDetails[$key] = $value;
		}
	}
	
	if (isset($sql)) {
		if (isset($reportDetails['GROUP_HEADER'])) {
			$html = createGroupedTableFromSql($sql,$report_id);
			echo $html;
		}
		else {
			createTableFromSql($sql);
		}
	}
?>