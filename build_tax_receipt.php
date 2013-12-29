<?php

	function buildSingleTaxReceipt($donation_id = null, $receipt_no = null) {
		$html  = buildTaxReceiptHeader();
		$html .= buildTaxReceiptBody($donation_id, $receipt_no);
		$html .= buildTaxReceiptFooter();
		return $html;
	}
	
	function buildMultiTaxReceiptFromDonation($donations) {
		$firstpage = true;
		$html  = buildTaxReceiptHeader();
		foreach ($donations as $donation_id) {
			$html .= (!$firstpage) ? addPageBreak() : '';
			$html .= buildTaxReceiptBody($donation_id, null);
			$firstpage = false;
		}	
		$html .= buildTaxReceiptFooter();
		return $html;
	}
	
	function buildMultiTaxReceiptFromReceipt($receipts) {
		$firstpage = true;
		$html  = buildTaxReceiptHeader();
		foreach ($receipts as $receipt_id) {
			$html .= (!$firstpage) ? addPageBreak() : '';
			$html .= buildTaxReceiptBody(null, $receipt_id);
			$firstpage = false;
		}
		$html .= buildTaxReceiptFooter();
		return $html;
	}
	
	function buildTaxReceiptFooter() {
		$html .= '
					</body>
					</html>';
		return $html;
	}
	
	function buildTaxReceiptHeader() {
		$html = '<html>
					<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
				<head>
					<style media="screen" type="text/css">
						.h1, .p, .a, .body, .div, .table { 
							font-family:Cambria,\'Palatino Linotype\',\'Book Antiqua\',\'URW Palladio L\',serif; 
							font-size: 14;
							}
					</style>
					<style type="text/css">
						table.outlined {
							border-width: thin;
							border-spacing: 2px;
							border-style: none;
							border-color: gray;
							border-collapse: separate;
							background-color: white;
						}
						table.outlined th {
							border-width: thin;
							padding: 0px;
							border-style: inset;
							border-color: gray;
							background-color: white;
							-moz-border-radius: ;
						}
						table.outlined td {
							border-width: thin;
							padding: 0px;
							border-style: inset;
							border-color: gray;
							background-color: white;
							-moz-border-radius: ;
						}
						div.outlined {
							border-width: thin;
							border-spacing: 0px;
							border-style: inset;
							border-color: gray;
							border-collapse: separate;
							background-color: white;
							text-align: right;
						}
						hr.dashed {
							border-top: 3px dotted #000000;
							margin-top: 5px;
						}
						
				</style>
				</head>
				<body>';
		
		return $html;
	}
	
	function buildTaxReceiptBody($donation_id = null, $receipt_no = null) {
		if (isset($donation_id)) {
			$query_where = "where B.donation_id = $donation_id";
		}
		elseif (isset($receipt_no)) {
			$query_where = "where B.receipt_no = $receipt_no";
		}
		else {
			$query_where = "where 1=2";
		}
		
		$query = "select * from fctdonationstb B ";
		$query .= $query_where;
		$result = mysql_query($query) or die("Error: ".mysql_error());
		$resultRow = mysql_fetch_assoc($result);
		if ($resultRow['receipt_date'] === NULL || $resultRow['receipt_printed_ind'] != 'Y')
			$version_date = 'current_date';
		else
			$version_date = "'".$resultRow['receipt_date']."'";

		$query = "select * from dimdonortb A inner join fctdonationstb B on (A.donor_id = B.donor_id and $version_date between A.from_date and A.to_date) ";
		$query .= $query_where;
		$result = mysql_query($query) or die("Error: ".mysql_error());
		$resultRow = mysql_fetch_assoc($result);
		
		$html .= '
			<div>
			<table>
			<tr valign="top">
			<td><img src="images/logo.jpg" height=125 width=143 /></td>
			<td>
				<table>
				<tr height=40><td width=500><p><strong>OFFICIAL DONATION RECEIPT FOR INCOME TAX PURPOSES<br />REÇU OFFICIEL DE DONS AUX FINS DE L’IMPÔTS SUR LE REVENU</strong></p></td></tr>
				<tr height=40 valign="bottom"><td align="right"><p align="right" style="font-size:10;"><br>Charitable Business Number<br /> Numéro d’organisation de bienfaisance</p></td></tr>
				</table>
			</td>
			<td>
			
				<table style="text-align:right;">
				<tr valign="top" height=40><td width=140>';
		$html .= $resultRow['receipt_no'];
		$html .= '</td></tr>
				<tr height=40 valign="bottom"><td><br>89411 2358 RR0001</td></tr>
				</table>
			</td>
			</tr>
			</table>
			
			<table>
				<tr valign="top"><td valign="top">
					<table>
					<tr height=100 valign="top"><td width=275 valign="top"><i>Wild Bird Rehabilitation Centre<br>Centre de Réhabilitation d’oiseaux sauvages</i></td></tr>
					<tr height=100 valign="top"><td width=275 valign="top"><i>637 Main Road<br>Hudson, QC<br>J0P 1H0</i></td></tr>
					</table>
				</td>
				<td>
				<br>
					<table class="outlined">
					<tr><td colspan=2 width=500>';
		if (!empty($resultRow['first_name']))
		$html .= utf8_encode($resultRow['first_name']) . ' ';
		if (!empty($resultRow['last_name']))
		$html .= utf8_encode($resultRow['last_name']) . ' ';
		if (!empty($resultRow['company_name']))
		$html .= utf8_encode($resultRow['company_name']);
		$html .= '&nbsp;</td></tr>
					<tr><td colspan=2 width=500>';
		$html .= utf8_encode($resultRow['address']);
		$html .= '&nbsp;</td></tr>
					<tr><td width=250>';
		$html .= utf8_encode($resultRow['city']);
		$html .= '&nbsp;</td><td width=250>';
		$html .= utf8_encode($resultRow['province']);
		$html .= '&nbsp;</td></tr>
					<tr><td width=250>';
		$html .= utf8_encode($resultRow['country']);
		$html .= '&nbsp;</td><td width=250>';
		$html .= utf8_encode($resultRow['postal_code']);
		$html .= '&nbsp;</td></tr>
					</table>
					<table width=500>
					<tr height=100><td width=150><p align="right">Amount / Montant:</p></td><td width=125><div style="font-size:26;" align="right">$';
		$html .= utf8_encode($resultRow['donation_amt']);
		$html .= '</div></td><td width=100><p align="right">Donation Date: </p></td><td><div align="left">';
		$html .= utf8_encode($resultRow['donation_date']);
		$html .= '</div></td></tr>
					</table>
				</td>
				</tr>
			</table>
			<table align="center">
			<tr><td><p style="font-size:12;font-weight:bold;color:black;">'.$resultRow['receipt_comment'].'</p></td>
			<td rowspan="2" align="center"><p style="text-align:center;"><img src="images/sig.jpg" width=268 height=104><br>Lindsay D\'Aoust, Présidente/President<br />Date issued: '.$resultRow['receipt_date'].' - Hudson, QC</p><br></td>
			</tr>
			<tr><td><p style="font-size:18;font-weight:bold;color:red;">Thank you for your donation. Merci pour votre don.</p></td></tr>
			<tr><td colspan=2 align="center"><p style="color:#E8967E;"><br>Canada Revenue Agency / Agence du revenue du Canada www.cra.gc.ca/charities</td></tr>
			</table>
			</div>';
		$html .= '<br><hr class="dashed">';
		$html .= '
			<div>
			<table>
			<tr valign="top">
			<td><img src="images/logo.jpg" height=125 width=143 /></td>
			<td>
				<table>
				<tr height=40><td width=500><p><strong>OFFICIAL DONATION RECEIPT FOR INCOME TAX PURPOSES<br />REÇU OFFICIEL DE DONS AUX FINS DE L’IMPÔTS SUR LE REVENU</strong></p></td></tr>
				<tr height=40 valign="bottom"><td align="right"><p align="right" style="font-size:10;"><br>Charitable Business Number<br /> Numéro d’organisation de bienfaisance</p></td></tr>
				</table>
			</td>
			<td>
			
				<table style="text-align:right;">
				<tr valign="top" height=40><td width=140>';
		$html .= utf8_encode($resultRow['receipt_no']);
		$html .= '</td></tr>
				<tr height=40 valign="bottom"><td><br>89411 2358 RR0001</td></tr>
				</table>
			</td>
			</tr>
			</table>
			
			<table>
				<tr valign="top"><td valign="top">
					<table>
					<tr height=100 valign="top"><td width=275 valign="top"><i>Wild Bird Rehabilitation Centre<br>Centre de Réhabilitation d’oiseaux sauvages</i></td></tr>
					<tr height=100 valign="top"><td width=275 valign="top"><i>637 Main Road<br>Hudson, QC<br>J0P 1H0</i></td></tr>
					</table>
				</td>
				<td>
				<br>
					<table class="outlined">
					<tr><td colspan=2 width=500>';
		if (!empty($resultRow['first_name']))
		$html .= utf8_encode($resultRow['first_name']) . ' ';
		if (!empty($resultRow['last_name']))
		$html .= utf8_encode($resultRow['last_name']) . ' ';
		if (!empty($resultRow['company']))
		$html .= utf8_encode($resultRow['company']);
		$html .= '&nbsp;</td></tr>
					<tr><td colspan=2 width=500>';
		$html .= utf8_encode($resultRow['address']);
		$html .= '&nbsp;</td></tr>
					<tr><td width=250>';
		$html .= utf8_encode($resultRow['city']);
		$html .= '&nbsp;</td><td width=250>';
		$html .= utf8_encode($resultRow['province']);
		$html .= '&nbsp;</td></tr>
					<tr><td width=250>';
		$html .= utf8_encode($resultRow['country']);
		$html .= '&nbsp;</td><td width=250>';
		$html .= utf8_encode($resultRow['postal_code']);
		$html .= '&nbsp;</td></tr>
					</table>
					<table width=500>
					<tr height=100><td width=150><p align="right">Amount / Montant:</p></td><td width=125><div style="font-size:26;" align="right">$';
		$html .= utf8_encode($resultRow['donation_amt']);
		$html .= '</div></td><td width=100><p align="right">Donation Date: </p></td><td><div align="left">';
		$html .= utf8_encode($resultRow['donation_date']);
		$html .= '</div></td></tr>
					</table>
				</td>
				</tr>
			</table>
			<table align="center">
			<tr><td><p style="font-size:12;font-weight:bold;color:black;">'.$resultRow['receipt_comment'].'</p></td>
			<td rowspan="2" align="center"><p style="text-align:center;"><img src="images/sig.jpg" width=268 height=104><br>Lindsay D\'Aoust, Présidente/President<br />Date issued: '.$resultRow['receipt_date'].' - Hudson, QC</p><br></td>
			</tr>
			<tr><td><p style="font-size:18;font-weight:bold;color:red;">Thank you for your donation. Merci pour votre don.</p></td></tr>
			<tr><td colspan=2 align="center"><p style="color:#E8967E;"><br>Canada Revenue Agency / Agence du revenue du Canada www.cra.gc.ca/charities</td></tr>
			</table>
			</div>';
		
		return $html;
	}
	
	function addPageBreak() {
		$html = '<pagebreak />';
		return $html;
	}
?>