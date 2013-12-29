<?php

/* -----------------------------------

array getheaders(string tablename)

	returns an array populated by the column titles of a given table

string tablequery(array headers, string table, string sort)

	returns an SQL string that searches a table for specific headers and a specific sort
		
createtable(data sqldata, array headers, string filter, string trstring, string tdstring)

	creates a table listing of data fed by sqldata with a specific list of headers (or * if headers is blank), filtered by filter
	must be inside <table></table> quotes
	handles special tr, td code as a string
	
------------------------------- */

$amtColumns = array('donation_amt');
$dateColumns = array('donation_date','receipt_date','creation_date','contact_date','thanks_date','tribute_sent_date');
$indColumns = array('receipt_required_ind','receipt_printed_ind','no_solicit_ind','verified_ind','etaxreceipt_ind','enewsletter_ind','multi_household_ind','followup_ind');
$defaultDonorColumns = array('donor_id','last_name','first_name','address','address_2','city','postal_code','home_phone','email_address','no_solicit_ind');
$defaultCompanyColumns = array('donor_id','company_name','address','address_2','city','postal_code','home_phone','email_address','no_solicit_ind');

$fieldsMap['dimcategorytb'] = array('revenue_stream','type','origin');
$fieldsMap['dimfundtb'] = array('fund_name');
$fieldsMap['dimcomponenttb'] = array('component_level_1','component_level_2','component_level_3','component_level_4');
$fieldsMap['refcontacttypetb'] = array('contact_type');
$fieldsMap['dimstatustb'] = array('status_component_1','status_component_2','status_component_3');
$fieldsMap['refstatustb'] = array('status_name');
$fieldsMap['reflanguagetb'] = array('language_name');
$fieldsMap['refcontacttb'] = array('contact_name');
$fieldsMap['refgendertb'] = array('gender_name');
$fieldsMap['reftypetb'] = array('type_name');
$fieldsMap['dimorigintb'] = array('origin_name');
$fieldsMap['refgrouptb'] = array('group_name');
$fieldsMap['refthanksbytb'] = array('thanks_by_name');
$fieldsMap['reftributebytb'] = array('tribute_sent_by_name');
$fieldsMap['refthanksmethodtb'] = array('thanks_method_name');
$fieldsMap['dimcompreftb'] = array('com_pref_name');

$dropColumns = array(
							'category_id' => 'dimcategorytb', 
							'component_id' => 'dimcomponenttb', 
							'fund_id' => 'dimfundtb', 
							'type_name' => 'reftypetb', 
							'origin_id' => 'dimorigintb',
							'contact_type' => 'refcontacttypetb', 
  							'status' => 'refstatustb', 
  							'language' => 'reflanguagetb', 
  							'gender' => 'refgendertb',
							'contact_by' => 'refcontacttb',
							'followup_by' => 'refcontacttb',
							'group_name' => 'refgrouptb',
							'thanks_by_name' => 'refthanksbytb',
							'tribute_sent_by_name' => 'reftributebytb',
							'thanks_method_name' => 'refthanksmethodtb',
							'com_pref_id' => 'dimcompreftb'
);

$AUDITHEADERS = array('from_date','to_date','a_create_timestamp','a_create_user','a_update_timestamp','a_update_user');

$hashsalt = 'le_nichoir';

$SQL = array(
		'donor.php.donations' => "SELECT fct.donation_id, fct.donation_date, fct.donation_year, fct.donation_amt, dim1.fund_name,
	fct.type_name, dim2.origin_name, fct.receipt_required_ind, dim3.receipt_printed_ind, dim3.receipt_no, dim3.receipt_date,
	fct.donation_notes, dim3.cancelled_ind, case when (fct3.donation_id is not null) then 'Thanked' else 'Not Thanked' end \"Thanks\" 
	from fctdonationstb fct
	left outer join dimfundtb dim1 on fct.fund_id = dim1.fund_id and current_date between dim1.from_date and dim1.to_date 
	left outer join dimorigintb dim2 on fct.origin_id = dim2.origin_id and current_date between dim2.from_date and dim2.to_date 
	left outer join dimreceiptstb dim3 on fct.donation_id = dim3.donation_id and current_date between dim3.from_date and dim3.to_date 
	left outer join fctthankyoutb fct3 on fct.donation_id = fct3.donation_id
	WHERE 1=1 
	@CONSTRAINT@"
);

function getTableSQL ($key, $constraint = null) {
	global $SQL;
	$rawSQL = $SQL[$key];
	if ($constraint) {
		$rawSQL = str_replace('@CONSTRAINT@',$constraint,$rawSQL);
	}
	return $rawSQL;
	
}

function getheaders($table) {
	$AUDITHEADERS = array('from_date','to_date','a_create_timestamp','a_create_user','a_update_timestamp','a_update_user');
	$headers = array();
	$columns = mysql_query("SHOW COLUMNS FROM ".$table);
	$i=0;
	while ($column_names = mysql_fetch_array($columns))
	{
		if (in_array($column_names[0],$AUDITHEADERS))
			continue;
		$headers[$i] = $column_names[0];
		$i++;
	}
	
	return $headers;
}

function getadhocheaders($sql) {
	$headers = array();
	$result = mysql_query($sql);
	$i=0;
	while ($i < mysql_num_fields($result))
	{
		$meta = mysql_fetch_field($result,$i);
		$headers[$i] = $meta->name;
		$i++;
	}
	
	return $headers;
}

function tablequery($headers = null, $table, $sort = null, $searchkey = null, $active_ind = null, $selected_ind = null, $sort_type = null) {
	$current_user = $_COOKIE['nichoir_username'];
	$query = 'SELECT ';
	if (empty($headers) || is_null($headers)) {
		$query .= '*';
	}
	else
	{
		foreach ($headers as $header) $query .= '`'.$header.'`,';
		$query = substr($query,0,strlen($query)-1);
	}
	
	$active = $active_ind == 'true' ? " and status in ('active','focus','prospect')" : "";
	$selected = $selected_ind == 'true' ? " and donor_id in (select distinct donor_id from refselecteddonortb where username = '$current_user')" : "";
	$query .= ' FROM '.$table . ' WHERE current_date between from_date and to_date ';
	if ($searchkey) {
		$query .= " and (donor_id like '%$searchkey%'"
				. " OR last_name like '%$searchkey%'"
				. " OR first_name like '%$searchkey%' "
				. " OR company_name like '%$searchkey%'"
				. " OR address like '%$searchkey%'"
				. " OR address_2 like '%$searchkey%'"
				. " OR home_phone like '%$searchkey%'"
				. " OR city like '%$searchkey%'"
				. " OR email_address like '%$searchkey%'"
				. " OR postal_code like '%$searchkey%')";
	}
	
	$query .= $active . $selected;
	$sort_type = ($sort_type) ? $sort_type : '';
	if ($sort || !(is_null($sort))) $query .=' ORDER BY '.$sort.' '.$sort_type;
	
	return $query;	
}

function createtable($sqldata, $headers, $filter = null, $trstring = null, $tdstring = null, $rows = null, $selectedArr = null) {
	$indColumns = array('no_solicit_ind','verified_ind','etaxreceipt_ind','enewsletter_ind','multi_household_ind');
	$current_user = $_COOKIE['nichoir_username'];
	
	if (mysql_num_rows($sqldata) > 0) {
		
	    $j=0;

	    while ($row = mysql_fetch_assoc($sqldata)) {
	    	$temparr = array_map(strtolower, $row);
	    	if (!in_array(strtolower($filter), $temparr) && $filter != NULL) { continue; }
	    	$tdstringdata = str_replace('ROWREPLACE',$row['donor_id'],$tdstring);
	        echo '<tr>';
	        $selected = (in_array($row['donor_id'],$selectedArr)) ? ' checked="yes"' : "";
	        echo '<td><INPUT id="selected_'.$row['donor_id'].'" value= '.$row['donor_id'].' type="checkbox"'.$selected.' onclick="saveSelected(\''.$row['donor_id'].'\',\''.$current_user.'\')" /></td>';
	        foreach ($row as $key => $value)
	        {
	        	echo '<td '.$tdstringdata.'>';
				//$rowout = $row[$i];
				$rowout = $value;
	        	if (in_array($key,$indColumns)) {
					$checked = $value == 'Y' ? ' checked' : '';
					echo '<INPUT TYPE="checkbox" NAME="'.$key.'" VALUE="Y" '.$checked.' disabled="disabled" /></td>';
				}
				else {
					echo $rowout.'</td>';
				}	
				
	        }
	        echo '</tr>
	        ';
	        $j++;
	        if ($j>=$rows && $rows != -1) {break;}
	    }
	}
	if(!$j) { echo '<tr><td colspan="100%">No results found!</td></tr>'; }
}

function createTableFromSql($sql_) {
	$first_row = true;
	$result = mysql_query($sql_) or die("Errors: ".mysql_error());
	echo "<table>";
	while ($row = mysql_fetch_assoc($result)) {
		if ($first_row) {
			echo "<tr><thead>";
			foreach ($row as $key => $value) {
				echo "<th>".str_replace("_"," ",$key)."</th>";
			}
			echo "</thead></tr>";
			$first_row = false;
		}
		echo "<tr>";
		foreach ($row as $key => $value) {
			echo "<td>";
			echo utf8_encode($value);
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

function createGroupedTableFromSql($sql_,$report_id) {
	$clickthrough_string = ' onclick="window.open(\'donor.php?donor_id=ROWREPLACE\')" onmouseover="this.style.cursor=\'pointer\'"';
	$current_group = null;
	$end_table = true;
	$new_table = true;
	$sumValue = 0;
	$countValue = 0;
	$avgValue = 0;
	
	$reportDetails = getReportDetails($report_id);
	$groupHeader = $reportDetails['GROUP_HEADER'];
	$sumFooter = $reportDetails['SUM_FOOTER'];
	$countFooter = $reportDetails['COUNT_FOOTER'];
	$avgFooter = $reportDetails['AVERAGE_FOOTER'];
	$rptTitle = $reportDetails['REPORT_TITLE'];
	
	$sumData = array();
	$countData = array();
	$avgData = array();
	
	$report_html = "<DIV align=\"CENTER\"><H1>".$rptTitle."</H1></DIV>";
	
	if ($groupHeader) {
		//make sure we're in order, else it's gonna be trouble
		if (!stristr($sql_,'order by'))
			$sql_ .= " order by " . $groupHeader;
	}
	
	$result = mysql_query($sql_) or die("Errors: ".mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		if (in_array('donor_id',array_keys($row))) {
			$donor_id = $row['donor_id'];
			$donor_row = true;
		}
		else {
			$donor_row = false;
		}
		if (strcmp($current_group,$row[$groupHeader])) {
			if ($end_table) {
				$report_html .= closeTable(($sumFooter ? $sumFooter : null), ($sumFooter ? $sumValue : null), ($countFooter ? $countValue : null), ($avgFooter ? $avgValue : null));
				$sumValue = 0;
				$countValue = 0;
				$avgValue = 0;
			}
			else {
				$end_table = true;
			}
			$new_table = true;
			$current_group = $row[$groupHeader];
		}
		if ($new_table) {
			$report_html .= "<table>";
			if ($current_group) {
				$report_html .= "<caption>".str_replace("_"," ",$groupHeader).": ".$current_group."</caption>";
			}
			$report_html .= buildHeader($row,$groupHeader);
			$report_html .= "</thead></tr>";
			$new_table = false;
		}
		$trstrreplace = $donor_row == true ? str_replace('ROWREPLACE',$donor_id,$clickthrough_string) : "blah";

		$report_html .= "<tr".$trstrreplace.">";
		$countValue++;
		foreach ($row as $key => $value) {
			if (strcmp($groupHeader,$key)) {
				$rowout = $value;
				if (stristr($key,"_amt")) {
					$rowout = '$'.number_format($rowout, 2, '.', ',');
				}
				elseif (stristr($key,"_ind")) {
					$checked = $value=='Y' ? 'checked' : '';
					$rowout = '<INPUT TYPE="checkbox" disabled="disabled" '.$checked.'>';
				}
				$report_html .= "<td>";
				$report_html .= utf8_encode($rowout);
				$report_html .= "</td>";
			}
			if (!strcmp($sumFooter, $key)) {
				$sumValue += $value;
			}
			if (!strcmp($countFooter, $key)) {
				$countValue++;
			}
			if (!strcmp($avgFooter,$key)) {
				$avgValue += $value;
			}
		}
		$report_html .= "</tr>";
	}
	$report_html .= closeTable(($sumFooter ? $sumFooter : null), ($sumFooter ? $sumValue : null), ($countFooter ? $countValue : null), ($avgFooter ? $avgValue : null));
	$report_html .= "</table>";
	return $report_html;
}

function closeTable($sumFooter = null, $sumValue = null, $countValue = null, $avgValue = null) {
	if ($sumFooter) {
		$sumData[$sumFooter] = $sumValue;
		$countData[$countFooter] = $countValue;
		$avgData[$avgFooter] = $avgValue;
		$report_html = buildFooter(($sumValue ? $sumData : null),($countValue ? $countData : null),($avgValue ? $avgData : null));
	}
	$report_html .= "</table><br><br>";
	return $report_html;
	
}

function getReportDetails($report_id) {
	$query = "select key_text, value_text from refreportdetailtb where report_id = ". $report_id;
	$results = mysql_query($query) or die("Errors: ".mysql_error());
	$map = array();
	while ($row = mysql_fetch_assoc($results)) {
		$key = $row['key_text'];
		$value = $row['value_text'];
		$map[$key] = $value;
	}
	return $map;
}

function buildHeader($row_,$skipHeader_) {
	$report_html = "<tr><thead>";
	foreach ($row_ as $key => $value) {
		if (strcmp($skipHeader_,$key))
			$report_html .= "<th>".str_replace("_"," ",$key)."</th>";
	}
	return $report_html;
}

function buildFooter($sumData = null, $countData = null, $avgData = null) {
	$report_html = "<tr><tfoot>";
	foreach ($sumData as $key => $value) {
		$rowout = $value;
		if (stristr($key,"_amt")) {
			$rowout = '$'.number_format($rowout, 2, '.', ',');
		}
		$report_html .= "<td>SUM</td>";
		$report_html .= "<td>" . str_replace("_"," ",$key) . "</td>";
		$report_html .= "<td colspan=\"100%\"><p align=\"right\">" . $rowout . "</td>";
		$report_html .= "</tfoot></tr>";
		return $report_html;
	}
	foreach ($countData as $key => $value) {
		$report_html .= "<td>COUNT</td>";
		$report_html .= "<td>" . str_replace("_"," ",$key) . "</td>";
		$report_html .= "<td colspan=\"100%\">" . $value . "</td>";
		$report_html .= "</tfoot></tr>";
		return $report_html;
		
	}
	foreach ($avgData as $key => $value) {
		$report_html .= "<td>AVERAGE</td>";
		$report_html .= "<td>" . str_replace("_"," ",$key) . "</td>";
		$report_html .= "<td colspan=\"100%\">" . $value . "</td>";
		$report_html .= "</tfoot></tr>";
		return $report_html;
	}
}

function enrichColumns($tablename_, $rowout_=null, $key_=null, $value_=null, $readonly_=true) {
	global $amtColumns, $dateColumns, $indColumns;
	
	// convert numerics to dollars
	if (in_array($key_,$amtColumns)) {
		$rowout = '$'.$rowout_;
	}
	// convert datetimes to dates
	elseif (in_array($key_,$dateColumns)) {
		$rowout = empty($rowout_) ? null : date('Y-m-d',strtotime($rowout_));
	}
	// convert y/n indicators to checkboxes	
	elseif (in_array($key_, $indColumns)) {
		$checked = $value_ == 'Y' ? ' checked' : '';
		$disabled = $readonly_ ? ' disabled="disabled"' : '';
		$rowout = '<INPUT TYPE="hidden" name="'.$tablename_.'|'.$key_.'" value="N" /><INPUT TYPE="checkbox" NAME="'.$tablename_.'|'.$key_.'" VALUE="Y" '.$checked.' '.$disabled .' />';
	}
	
	else {
		$rowout = $rowout_;
	}
	
	return $rowout;
}

function buildGenericDropdownTable($tablename_=null, $rowout_=null, $key_=null, $value_=null, $order_column=null,$sourcetb_=null) {
	$sourcetb_ .= is_null($sourcetb_) ? "" : "|";
	$query = "SELECT * from $tablename_ where 1=1";
	$query .= substr($tablename_,0,3)=='dim' ? " and current_date between from_date and to_date " : "";
	$query .= " order by ";
	global $fieldsMap;
	$keyFields = $fieldsMap[$tablename_];
	if (isset($order_column)) {
		$query .= $order_column;
	}
	else {
		foreach ($keyFields as $value) {
			$query .= $value.", ";
		}
		$query = substr($query,0,strlen($query)-2);
	}
	
	$dropdownTb = mysql_query($query) or die ("Errors: " . mysql_error());
	$rowout = '<select name="'.$sourcetb_.$key_.'" id="'.$sourcetb_.$key_.'">
				   <option></option>
				   ';
	while ($dropdownArr = mysql_fetch_assoc($dropdownTb)) {
		$dropdownArrKeys = array_keys($dropdownArr);
		$indexCol = $dropdownArrKeys[0];
		$value = $dropdownArr[$key_];
		$dropdownString = '';
		foreach ($keyFields as $value) {
			$dropdownString .= $dropdownArr[$value] . " | ";
		}
		$dropdownString = substr($dropdownString,0,strlen($dropdownString)-3);
		$optionValue = substr($tablename_,0,3)=='dim' ? $dropdownArr[$indexCol] : $dropdownString;
		$selected = $optionValue == $value_ ? ' selected' : '';
		$rowout .= '<option value="'.$optionValue.'" '. $selected . '>' . $dropdownString.'</option>
			';
	}
	return $rowout;
}

function buildGetString($_GET, $togglevar_ = null, $sort_type_ = null) {
	$retString = '';
	foreach ($_GET as $key => $value) {
		if ($key == $togglevar_) {
			$value = $value=='true' ? 'false' : 'true';
		}
		if ($key == $sort_type_) {
			$value = $value=='ASC' ? 'DESC' : 'ASC';
		}
		$retString .= $key . '=' . $value . '&';
	}
	
	return $retString;
}

function generatePassword ($length = 8)
{

	$password = "";
	$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	$maxlength = strlen($possible);

	if ($length > $maxlength) {
		$length = $maxlength;
	}

	$i = 0;

	while ($i < $length) {

		$char = substr($possible, mt_rand(0, $maxlength-1), 1);

		if (!strstr($password, $char)) {
			$password .= $char;
			$i++;
		}

	}
	return $password;

}

function hashUserEmail ($username = null, $hashsalt = null, $email = null) {
	if (isset($username) && isset($hashsalt) && isset($email)) {
		// password salted with current date (only good for today)
		$string = $username . $hashsalt . date("mdy") . $email;
		return md5($string);
	}
	else {
		return null;
	}
}
?>