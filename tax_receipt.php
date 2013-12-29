<?php
	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	require_once ('build_tax_receipt.php');
	include 'mpdf/mpdf.php';
	include 'dbinfo.php';
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value;}
	
	$current_user = $_COOKIE['nichoir_username'];
	
//	echo 'Receipt: ' . $receipt_no;
//	echo 'Donation: ' . $donation_id;

	if (isset($donation_id)) {
		$query = 'select receipt_no from fctdonationstb where donation_id = '.$donation_id;
		$result = mysql_query($query) or die('Error: '.mysql_error());
		$receipt_check = mysql_fetch_assoc($result);
		if (empty($receipt_check['receipt_no'])) {
			$query = 'select max(receipt_no) as "receipt_no" from fctdonationstb';
			$result = mysql_query($query) or die('Error: '.mysql_effor());
			$row = mysql_fetch_assoc($result);
			$max_receipt = $row['receipt_no'];
			$receipt_no = $max_receipt + 1;
			$query = 'update fctdonationstb set receipt_no = ' . $receipt_no . ', receipt_date = current_date, a_update_timestamp = current_timestamp, a_update_user = concat(\''.$current_user.'\',\'.\',current_user) where donation_id = '. $donation_id;
			$result = mysql_query($query) or die ("Error: ". mysql_error());
		}
		else {
			$receipt_no = $receipt_check['receipt_no'];
		}
	}
	
	$html = buildSingleTaxReceipt(null, $receipt_no);

	$mpdf=new mPDF('c','letter','','' , 10 , 10 , 10 , 10 , 0 , 0);
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
	$mpdf->SetProtection(array('copy','print'), '', 'BirdRx2012');
	$mpdf->WriteHTML($html);
	$mpdf->Output();
?>