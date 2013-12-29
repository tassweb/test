<?php
	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	require_once ('build_tax_receipt.php');
	include 'mpdf/mpdf.php';
	include 'dbinfo.php';
	
	foreach($_GET as $key => $value) {$$key = $value;}
	foreach($_POST as $key => $value) {$$key = $value;}
	
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
			$query = 'update fctdonationstb set receipt_no = ' . $receipt_no . ' where donation_id = '. $donation_id;
			$result = mysql_query($query);
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
	$content = $mpdf->Output('', 'S');

	$content = chunk_split(base64_encode($content));
	$mailto = 'jason@tassweb.com';
	$from_name = 'Jason Dubsky';
	$from_mail = 'admin@tassweb.com';
	$replyto = 'admin@tassweb.com';
	$uid = md5(uniqid(time()));
	$subject = 'Receipt';
	$message = 'Receipt attached';
	$filename = 'receipt.pdf';
	
	$header = "From: ".$from_name." <".$from_mail.">\n";
	$header .= "Reply-To: ".$replyto."\n";
	$header .= "MIME-Version: 1.0\n";
	$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\n\n";
	$header .= "This is a multi-part message in MIME format.\n";
	$header .= "--".$uid."\n";
	$header .= "Content-type:text/plain; charset=iso-8859-1\n";
	$header .= "Content-Transfer-Encoding: 7bit\n\n";
	$header .= $message."\n\n";
	$header .= "--".$uid."\n";
	$header .= "Content-Type: application/pdf; name=\"".$filename."\"\n";
	$header .= "Content-Transfer-Encoding: base64\n";
	$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\n\n";
	$header .= $content."\n\n";
	$header .= "--".$uid."--";
	mail($mailto, $subject, "", $header);

	header( 'Location: index.php' ) ;
?>