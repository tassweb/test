<?php require 'password_protect.php';
	require 'dbinfo.php';
	
	$values=array();
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value; $values[]=$value;}
	
	if (!$invalidation = validate($receipt_no))
	{
		mail('jason@tassweb.com', 'Receipt request: '.$receipt_no, 'Please reprint receipt number:  '.$receipt_no, "From: receiptrequest@tassweb.com");
		$query = "UPDATE fctdonationstb A SET receipt_printed_ind = 'N' WHERE A.receipt_no='$receipt_number';";
		$result = mysql_query($query) or die(mysql_error());
		$output = 'Receipt reprint requested for receipt number '.$receipt_number;
	}
	else
	{
		$output = 'Receipt reprint denied.  Reason: ' . $invalidation;
	}
	echo '
	<html>
	<body>
	<head><link href="blue.css" rel="stylesheet" type="text/css"></head>
	'.$output.'.<br>
	<FORM><INPUT TYPE="button" VALUE="Back" class="btn" onClick="location.href=\'index.php\'"></form>
	</body>
	</html>
	';
	
	function validate($receipt_number)
	{
		
		$query = "SELECT * FROM fctdonationstb WHERE receipt_no='$receipt_number';";
		$result = mysql_query($query) or die(mysql_error());
		
		if (!mysql_num_rows($result))
		{
			return "No receipt found!";
		}
		if (!$receipt = mysql_fetch_array($result))
		{
			return "No specific receipt found!";
		}
		if (strcmp($receipt['receipt_required_ind'],'Y'))
		{
			return "Receipt not required!";
		}
		return NULL;
	}
?>