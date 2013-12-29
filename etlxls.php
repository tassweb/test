<?php 

	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	include 'dbinfo.php';
	
		echo '
		<html>
		<head>
		<link href="blue.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="check.js"></script>
		</head><body>';
		
		echo '
		<form enctype="multipart/form-data" action="etl.php" method="POST">
		<table>
		<caption>ETL file upload</caption>
		<tr><td>
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			Choose a file to upload: <input name="uploadedfile" type="file" /><br />
			<input type="submit" value="Upload File" class="btn"/>
			</td></tr>
			</table>
		</form>
';
	
	echo '
		</body>
		</html>
	';

?>