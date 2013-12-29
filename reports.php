<?php 

	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	require_once ('filereader.php');
	
	include 'dbinfo.php';
	
	echo '
		<html>
		<head>
		<link href="calendar/tcal.css" rel="stylesheet" type="text/css" />
		<link href="blue.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="check.js"></script>
		<script type="text/javascript" src="calendar/tcal.js"></script>
		<script type="text/javascript" src="getCategory.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		</head>
		<body style="height:100%;width:99%">
	';
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value;}
	
	$reports = getreports();
	echo '<div style="position:relative;"><INPUT TYPE="button" value="Reports" class="btn" onClick="toggleDiv(\'menu\');"></div>';
	echo "<div id=\"menu\" style=\"float: left; height:100%\">";
	echo "<fieldset style=\"background:#EFF4FB\"><legend>Report list</legend><table>";
	echo "<thead><tr><th>Report Name</th><th>Description</th></tr></thead>";
	foreach ($reports as $report) {
		$reportname = $report['report_name'];
		$reportpath = $report['report_path'];
		$report_id = $report['report_id'];
		$report_desc = $report['report_desc'];
		echo '<tr><td><a href="#" onClick="loadReport('.$report_id.',\''.$reportname.'\',\''.$report_desc.'\',\''.$reportpath.'\')">'.$reportname.'</a></td><td>'.$report_desc.'</td></tr>';
	}
	echo "</table></fieldset>";
	echo "</div>";
	
	echo ' <div id="content" style="width:100%;height:100%;">
			<div id="search_criteria" style="width:100%;">
				<form action="pdfReport.php" method="post">
					<fieldset style="background:#EFF4FB"><legend>Search Criteria</legend>
						<div style="float:left; margin-right:10px; height:120px;">
							From:<br><INPUT TYPE="text" NAME="from_date" id="from_date" class="tcal" style="background-color:#F9F9F9"><br>
							To:<br><INPUT TYPE="text" NAME="to_date" id="to_date" class="tcal" style="background-color:#F9F9F9">
						</div>
						<div style="height:100%;width:100%">
							Filters (NOT WORKING YET):<br>
							<SELECT name="filter_name_1" style="width:200px; margin-bottom:20px;">
								<option>item 1</option>
								<option>item 2</option>
								<option>item 3</option>
							</SELECT>
							<INPUT TYPE="text" name="filter_value_1" style="width:400px"><br>
							<SELECT name="filter_name_2" style="width:200px; margin-bottom:20px;">
								<option>item 1</option>
								<option>item 2</option>
								<option>item 3</option>
							</SELECT>
							<INPUT TYPE="text" name="filter_value_2" style="width:400px"><br>
							<SELECT name="filter_name_3" style="width:200px; margin-bottom:10px;">
								<option>item 1</option>
								<option>item 2</option>
								<option>item 3</option>
							</SELECT>
							<INPUT TYPE="text" name="filter_value_3" style="width:400px"><br>
						</div>
						<div style="width:100%;">
							<div id="sqltextdiv" style="display:none;">SQL:<br>
							<TEXTAREA name="sqltext" id="sqltext" rows="5" style="width:100%">select * from dimdonortb where current_date between from_date and to_date
							</TEXTAREA><br></div>
							<INPUT TYPE="button" name="run" id="runButton" value="Run" class="btn" style="width:100px;" onClick="runReport()">
							<INPUT TYPE="submit" id="pdfButton" value="PDF" class="btn" style="width:100px;">
							<INPUT TYPE="button" id="csvButton" value="Export to CSV" class="btn" style="width:100px;" onClick="csvReport()">
							<INPUT TYPE="hidden" name="report_id" id="report_id" value="0">
							<INPUT TYPE="button" value="SQL" class="btn" onClick="toggleDiv(\'sqltextdiv\');">
						</div>
					</fieldset>
				</form>
			</div>
			<div id="grid" style="width:100%; height:6	0%">
				<fieldset style="background:#EFF4FB"><legend>Report</legend>
					<h1 id="reportCaption" align="center"></h1>
					<h2 id="reportCaption2" align="center"></h2>
					<div id="reportGrid" style="overflow: auto;">
					</div>
				</fieldset>
			</div>
		   </div>';
	
	echo '
	</body>
	</html>';
	
?>