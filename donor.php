<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	require_once 'acl.php';
	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	$current_group = $_COOKIE['group_name'];
	foreach($_GET as $key => $value) {$$key = $value;}
	
	//"special needs" columns
	$readonlyColumns = array('receipt_no');
	$nullColumns = array('donation_id');
	$ignoreColumns = array('cancelled_ind','donor_id','notes');
	$cidColumns = array('category_id');
	$fundColumns = array('fund_id');
	$originColumns = array('origin_id');
	$thanksColumns = array('Thanks');
	$receiptClumns = array('receipt_no','receipt_printed_ind','receipt_required_ind');
	
	$query = "SELECT * FROM dimdonortb WHERE donor_id=$donor_id and current_date between from_date and to_date";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_assoc($donor);
	
	$company_layout = $donorinfo['contact_type'] != 'Individual' ? true : false;
	$caption = $company_layout ? $donorinfo['company_name'] : $donorinfo['last_name'].', '.$donorinfo['first_name'];
	//----------------------------- Donor table ---------------------------------
	
	echo '
		<table border=1>
		<caption>'.$caption.'</caption>
		<thead><tr>';
  	$query = "SHOW COLUMNS FROM dimdonortb";
  	$donorheadersTb = mysql_query($query) or die ("Error: " . $mysql_error());
  	
	while ($column_names = mysql_fetch_assoc($donorheadersTb)) {
		if ($company_layout && !in_array($column_names['Field'],$defaultCompanyColumns)) continue;
		if (!$company_layout && !in_array($column_names['Field'],$defaultDonorColumns)) continue;
		if (in_array($column_names['Field'],$AUDITHEADERS) || in_array($column_names['Field'],$ignoreColumns)) continue;
		echo '<th scope="col">'.str_replace("_"," ",$column_names['Field']).'</th>';
	}

    echo '
    </tr></thead>
    <tr onmouseover="this.style.cursor=\'pointer\'" onclick="window.location = \'donoredit.php?donor_id='.$donorinfo['donor_id'].'\'">';
    foreach ($donorinfo as $key => $value)
    {
    	if ($company_layout && !in_array($key,$defaultCompanyColumns)) continue;
		if (!$company_layout && !in_array($key,$defaultDonorColumns)) continue;
		if (in_array($key,$AUDITHEADERS) || in_array($key,$ignoreColumns)) continue;
		echo "<td>";
		$rowout = enrichColumns('dimdonortb',$value,$key,$value,true);
		echo $rowout.'</td>';
    }
    echo '    
        </tr>
    </table>
    <Br><br>';
    
    //--------------------------------- Donations table --------------------------------
    if (getWritePerm($current_group)) {
    	echo '
    <table border=1>
	<thead><tr>';
    	
	$query = getTableSQL('donor.php.donations','and fct.donor_id = ' . $donor_id);
	$donationstb = mysql_query($query) or die("Error: " . mysql_error());
	
	$donationsFound = false;
	
	$componentMap = array();
	$numericindex = array();
	$dateindex = array();
	$dollarindex = array();
	$columns = array();
  	$cidindex = array();
	$donationstb_cols = array();
	
  	$i=0;
  	$totaldons = 0;
  	
  	$column_names = mysql_fetch_assoc($donationstb);
	$donationstb_cols = array_keys($column_names);
	
	foreach ($donationstb_cols as $col)
	{
		if (in_array($col,$AUDITHEADERS) || in_array($col,$ignoreColumns))
			continue;
		echo '<th scope="col">'.str_replace("_"," ",$col).'</th>';
		$i++;
	}
	
	echo '<th scope="col">Receipt request</th></tr></thead>';
	
	while ($donationinfo = mysql_fetch_assoc($donationstb)) {
		$donationsFound = true;
		echo '<tr onmouseover="this.style.cursor=\'pointer\'" onclick="window.location = \'donationedit.php?donation_id='.$donationinfo['donation_id'].'\'">';
		foreach ($donationinfo as $key => $value)
		{
			if (in_array($key,$AUDITHEADERS) || in_array($key,$ignoreColumns))
				continue;
			
			$rowout = enrichColumns('fctdonationstb',$value,$key,$value,true);
						
			if (in_array($key,$cidColumns)) {
				$rowout = $componentMap[$rowout];
			}
			elseif (in_array($key,$fundColumns)) {
				$rowout = $fundMap[$rowout];
			}
			elseif (in_array($key,$originColumns)) {
				$rowout = $originMap[$rowout];
			}
			elseif (in_array($key,$amtColumns)) {
				$totaldons += $value;
			}
			elseif (in_array($key,$thanksColumns)) {
				if (strcmp($rowout,"Thanked")) {
					$rowout = '<p style="color:red;font-weight:bold;">'.$rowout.'</p>';
				}
				else {
					$rowout = '<p style="color:green;font-weight:bold;">'.$rowout.'</p>';
				}
			}
			echo '<td>'.$rowout.'</td>';
		}
		if ($donationinfo['cancelled_ind'] == 'Y') {
			echo '<td bgcolor="#D9D9D9"><p align="center">Receipt Canceled</p></td>';
		}
		elseif ($donationinfo['receipt_required_ind'] != 'Y') {
			echo '<td bgcolor="#D9D9D9"><p align="center">No Receipt</p></td>';
		}
		else {
			$paramstring = empty($donationinfo['receipt_no']) ? 
				'?donation_id='.$donationinfo['donation_id'] :
				'?receipt_no='.$donationinfo['receipt_no'];
			$receiptstring = empty($donationinfo['receipt_no']) ?
				'Generate' :
				'View';
			echo '
			<td><form action="tax_receipt.php'.$paramstring.'" method="post">
			<input type="Submit" name="Submit" value="'.$receiptstring.' Receipt" class="btn">
			</form></td>';
		}
		echo '</tr>';
	}
	if (!$donationsFound) {
		echo '<tr><td colspan="100%">No donations found</td></tr>';
	}
	else {
		echo '<tr><tfoot><th colspan=3>Lifetime donations:</th><th align="left"><b>$'.number_format($totaldons, 2, '.', ',').'</b></th><th colspan="100%"></th></tfoot></tr>';
	}
	echo "</table>";
	
	echo '
		<form>
		<INPUT TYPE="button" VALUE="New Donation" class="btn" onClick="location.href=\'newdonation.php?donor_id='.$donor_id.'\'"> | 
		<INPUT TYPE="button" VALUE="Merge" class="btn" onClick="location.href=\'donormerge.php?donor_id='.$donor_id.'\'"> | 
		<INPUT TYPE="button" VALUE="Back" class="btn" onClick="location.href=\'index.php\'">
		</form>
		';
    }
    
	//--------------------------------------- Contact table ---------------------------------
	
	$query = 'SHOW COLUMNS FROM fctcontacttb';
	$callsheadersTb = mysql_query($query) or die('Error: ' . mysql_error());
	$query = 'select * from fctcontacttb where donor_id = ' . $donor_id;
	$callsTb = mysql_query($query) or die('Error: ' . mysql_error());
	$callsFound = false;
	
	echo '<br>';
	echo '<table border=1>
		  <tr><thead>';
	
	while ($callsheaderArr = mysql_fetch_assoc($callsheadersTb)) {
		if (in_array($callsheaderArr['Field'],$AUDITHEADERS) || in_array($callsheaderArr['Field'],$ignoreColumns))
			continue;
		$rowout = str_replace('_',' ',$callsheaderArr['Field']);
		echo '<th scole="col">' . $rowout . '</th>';
	}
	
	echo '</thead></tr>';
	
	while ($callsArr = mysql_fetch_assoc($callsTb)) {
		$callsFound = true;
		echo '<tr onmouseover="this.style.cursor=\'pointer\'" onclick="window.location = \'contactedit.php?contact_id='.$callsArr['contact_id'].'\'">';
		foreach ($callsArr as $key => $value) {
			if (in_array($key,$AUDITHEADERS) || in_array($key,$ignoreColumns))
				continue;
			$rowout = enrichColumns('fctcontacttb',$value,$key,$value,true);
			echo '<td>' . $rowout . '</td>';
		}
		echo '</tr>';
	}
	
	if (!$callsFound) {
		echo '<tr><td colspan="100%">No contacts found</td></tr>';
	}
	echo '</table>';

	echo '
			<form>
			<INPUT TYPE="button" VALUE="New Contact" class="btn" onClick="location.href=\'newcontact.php?donor_id='.$donor_id.'\'"> | 
			<INPUT TYPE="button" VALUE="Back" class="btn" onClick="location.href=\'index.php\'">
			</form>
			';
	mysql_free_result($donor);
	mysql_free_result($donationstb);
	mysql_free_result($callsheadersTb);
	mysql_free_result($callsTb);
	
	mysql_close($connection);
	
	require_once 'footer.php';

?>

</body>
</html>
