<?php 

	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	require_once ('acl.php');
	include 'dbinfo.php';
	
	echo '
		<html>
		<head>
		<link href="blue.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="check.js"></script>
		<script type="text/javascript" src="getCategory.js"></script>
		</head>
		<body>
	';
	
	$current_user = $_COOKIE['nichoir_username'];
	$current_group = $_COOKIE['group_name'];
	
	$params_str = '';
	foreach($_GET as $key => $value) {$$key = $value;}	
	foreach($_POST as $key => $value) {$$key = $value;}
	$headerlist = $_POST['headers'];
	if (!isset($active_ind)) {
		$active_ind = 'true';
	}
	if (!isset($selected_ind)) {
		$selected_ind = 'false';
	}
	if (!isset($sort_type)) {
		$sort_type = 'ASC';
	}
	
	// -- Grab all the column headers
	
	if ($adhocsql)
	{
		$adhocsql = str_replace("[","`",$adhocsql);
		$adhocsql = str_replace("]","`",$adhocsql);
		$donorheaders = getadhocheaders($adhocsql);
		$headerlist = $donorheaders;
	}
	else
	{
		$donorheaders = getheaders("dimdonortb");
	}
	//echo $adhocsql;
	if (!$rows) {$rows=100;}
	if (!$sort) {$sort='donor_id';}
	if (empty($headerlist))
	{	
			$headerlist = $defaultDonorColumns;
	}
	
	echo '
		<form method="post" name="headerlist" id="headerlist">
		<span id="default" name="default">
		<span id="toplayer" name="toplayer" style="position:relative; height:100px; z-index:2; display:none">
	';
	
	// -- Create the Checkbox list	
	echo '
		<table border=1><tr>
	';
	foreach ($donorheaders as $header) {
		echo '<td>'.str_replace("_"," ",$header).' <input type="checkbox" name="headers[]" value="'.$header.'"';
		if (empty($headerlist) || array_search($header,$headerlist) || $header=="donor_id") echo " CHECKED";
		echo '></td>
		';
	}
	echo '
		</tr></table>
		<input type="button" value="Update" class="btn" onClick="SubmitForm(document.headerlist, null);"> | 
		<input type="button" name="checkall" value="Check All" onClick="select_all(\'headers\', \'1\');" class="btn"> 
		<input type="button" name="uncheckall" value="Uncheck All" onClick="select_all(\'headers\', \'0\');" class="btn">
		</span>
	';
	
	if ($adhocsql) {
		$token = explode(' ',trim($adhocsql));
		if (strtolower($token[0]) == 'select') {
			$tabledata = mysql_query($adhocsql);
		}
	}
	else if ($searchkey) {
		$tabledata = mysql_query(tablequery($headerlist, "dimdonortb", $sort, $searchkey, $active_ind, $selected_ind, $sort_type)) or die("Errors: ".mysql_error());
	}
	else {
		$tabledata = mysql_query(tablequery($headerlist, "dimdonortb", $sort, null, $active_ind, $selected_ind, $sort_type)) or die("Errors: ".mysql_error());
	}
	
	echo '
		    <table border=1>
		    <caption>Donors | <input type="button" name="options" id="options" onClick="showLayer(\'toplayer\');" value="Options" class="btn">';
	if (getWritePerm($current_group)) {
		echo '| <input type="button" value="Reports" class="btn" onClick="window.open(\'reports.php\')">
			  | <input type="button" value="Create Login" class="btn" onClick="window.location=\'newlogin.php\'">
			  ';
	}
	echo '| <input type="button" value="Logout" class="btn" onClick="window.location=\'logout.php\'">
		  </caption><thead><tr><th></th>
	';
	    
	$prev_sort = $_GET['sort'];
	$_GET['sort_type'] = $sort_type;
	foreach ($headerlist as $key => $header) {
		$_GET['sort'] = $header;
		echo '<th scope="col" onmouseover="this.style.cursor=\'pointer\'" onclick="SubmitForm(document.headerlist,\''.buildGetString($_GET,null,'sort_type').'\')">'.str_replace("_"," ",$header).'</th>
		';		
	}
	if ($prev_sort) {
		$_GET['sort'] = $prev_sort;
	}
	else {
		unset($_GET['sort']);
	}
	    
	$checked = $active_ind=='true' ? 'checked="yes"' : '';
	$_GET['active_ind'] = $active_ind;
	$selected_checked = $selected_ind=='true' ? 'checked="yes"' : '';
	$_GET['selected_ind'] = $selected_ind;
	
	echo '
		    </tr></thead>
		    <tbody>
		    <tr>
		    <td colspan="100%"> Rows: <input type="textbox" name="rows" value="'.$rows.'" style="width:30px"> | Search: <input type="textbox" name="searchkey" value="'.$searchkey.'">
		    <input type="button" value="Search" class="btn" onClick="SubmitForm(document.headerlist, null);"> 
			';
	if (getWritePerm($current_group)) {
		echo '<input type="button" value="SQL Query" class="btn" onClick="window.location=\'adhoc.php\'"> |'; 
	}
	echo '  <input type="button" value="New Donor" class="btn" onClick="window.location=\'newdonor.php\'"> |';
	if (getWritePerm($current_group)) {
		echo '
		    <input type="button" value="View Unprinted Receipts" class="btn" onClick="window.location=\'unprinted_receipts.php\'">
		    <input type="button" value="Update Unprinted Receipts" class="btn" onClick="window.location=\'update_receipts.php\'"> |';
	}
	echo '
		    <input type="checkbox" value="active_ind" id="active_ind" '.$checked.' onclick="SubmitForm(document.headerlist,\''.buildGetString($_GET,'active_ind').'\')">Active Only</input> 
		    <input type="checkbox" value="selected_ind" id="selected_ind" '.$selected_checked.' onclick="SubmitForm(document.headerlist,\''.buildGetString($_GET,'selected_ind').'\')">Selected Only</input>
		    </td>
		    </tr>
	';
		
	$tdstring = 'onmouseover="this.style.cursor=\'pointer\'" onclick="window.open(\'donor.php?donor_id=ROWREPLACE\')"';
	$trstring = '';
	
	$selectedArr = array();
	$query = "select donor_id from refselecteddonortb where username = '$current_user'";
	$result = mysql_query($query) or die ("Error: " . mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		array_push($selectedArr,$row['donor_id']);
	}
	
	createtable($tabledata, $headerlist, null, $trstring, $tdstring, $rows, $selectedArr);
	
	echo '
		   </tbody>
		   <tfoot><tr align="left"><td colspan="100%" align="left">
		   <p align="left"><INPUT TYPE="button" class="btn" onclick="clearAndReload(\''.$current_user.'\',\''.buildGetString($_GET).'\')" value="Clear Selected" /></p>
		   </td></tr></tfoot>
		   </table>
	';
		
	
	
	include "footer.html";
	
	mysql_free_result($result);
	mysql_close($connection); 
	
	echo '
		</span>
		</form>
		</body>
		</html>
	';

?>