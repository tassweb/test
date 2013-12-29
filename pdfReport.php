<?php

	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	require_once 'mpdf/mpdf.php';
	
	foreach($_GET as $key => $value) {
		$$key = $value;
	}
	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	
	$sqltext = str_replace('@FROM_DATE@',"'$from_date'",$sqltext);
	$sqltext = str_replace('@TO_DATE@',"'$to_date'",$sqltext);
	
	$query = "select key_text, value_text from refreportdetailtb where report_id = " . $report_id;
	$reportDetailTb = mysql_query($query) or die("Error: " . mysql_error());
	$reportDetails = array();
	
	while ($reportDetailArr = mysql_fetch_assoc($reportDetailTb)) {
		$key = $reportDetailArr['key_text'];
		$value = $reportDetailArr['value_text'];
		$reportDetails[$key] = $value;
	}
	
	if (isset($sqltext)) {
		if (isset($reportDetails['GROUP_HEADER'])) {
			$html = '<html><head><link href="blue.css" rel="stylesheet" type="text/css"></head><body>';
			$html .= createGroupedTableFromSql($sqltext,$report_id);
			$html .= '</body></html>';
			//echo $html;
			ini_set("pcre.backtrack_limit","1000000");
			$mpdf=new mPDF('c','letter-L','','' , 10 , 10 , 10 , 10 , 0 , 0);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
			$mpdf->SetProtection(array('copy','print'), '', 'BirdRx2012');
			$stylesheet = file_get_contents('blue.css');
			$mpdf->WriteHTML($stylesheet,1);
			$html = wordwrap($html, 10000, "|");
			$htmlArr = explode("|",$html);
			foreach ($htmlArr as $htmlToken) {
				$mpdf->WriteHTML($htmlToken,2);
				//echo $htmlToken;
			}
			$mpdf->Output();
		}
		else {
			createTableFromSql($sqltext);
		}
	}
?>