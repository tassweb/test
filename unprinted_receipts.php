<?php
	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	require_once ('build_tax_receipt.php');
	include 'mpdf/mpdf.php';
	include 'dbinfo.php';
	
	$html = '';
	$donations = array();
	
	$query = "select distinct donation_id from fctdonationstb where receipt_required_ind = 'Y' and receipt_printed_ind = 'N' and receipt_no > 0";
	$result = mysql_query($query) or die ("Error: " . mysql_error());
	while ($donation = mysql_fetch_assoc($result)) {
		array_push($donations,$donation['donation_id']);
	}
	
	$html = buildMultiTaxReceiptFromDonation($donations);
	
	$mpdf=new mPDF('c','letter','','' , 10 , 10 , 10 , 10 , 0 , 0);
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
	$mpdf->WriteHTML($html);
	$mpdf->Output();
?>