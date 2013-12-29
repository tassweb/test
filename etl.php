<?php 

	require_once ('password_protect.php');
	require_once ('dbfunctions.php');
	require_once ('Excel/excel_reader2.php');
	include 'dbinfo.php';
	
	$target_path = "uploads/";
	$filename = basename ($_FILES['uploadedfile']['name']);
	$target_path = $target_path . $filename; 

	if(!move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    	echo "There was an error uploading the file, please try again!";
    	exit(0);
	}
	
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP1251');
	$data->read($target_path);
	
	$donorheaders = getheaders("donors");
	$etlheaders = array();
	$err = 0;
	$keyedheaders[][] = array(array());
	$keyedindex[][] =  array(array());
	$keys[][] = array(array("Last Name", "First Name"),array("Company"));
	
	for ($i=1; $i<=$data->sheets[0]['numCols']; $i++) {
		$etlheader = $data->sheets[0]['cells'][1][$i];
		array_push($etlheaders,$etlheader);
		if (!in_array($etlheader, $donorheaders)) {
			echo $etlheader . ' not a column in Donors<br>';
			$err = 1;
		}
		else {
			$j=0;
			foreach ($keys[][] as $key[]) {
				if (in_array($etlheader,$key)) {
					array_push($keyedheaders[$j],$etlheader);
					array_push($keyedindex[$j],$i);	
				}
				$j++;
			}
		} 
		
	}

	if (!$err) {
	
		echo '
		<html>
		<head>
		<link href="blue.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="check.js"></script>
		</head><body>
		<form method="post" name="sqls" id="sqls" action="processetl.php">
		<table>
		<caption>'.$filename.'</caption>
		<thead>
		';
		foreach ($etlheaders as $etlheader) {
			$th = '';
			if (in2darray($etlheader,$keyedheaders)) {
				$th = ' style="font-weight:bold"';
			}
			echo '<th'.$th.'>'.$etlheader.'</th>';
		}
		
		echo '</thead>';
		
		for ($i=2; $i<=$data->sheets[0]['numRows']; $i++) {
			$values = array();
			for ($j=1; $j<=$data->sheets[0]['numCols']; $j++) {
				array_push($values,$data->sheets[0]['cells'][$i][$j]);
			}
			$sql = "SELECT * FROM donors WHERE 1=1";
			$j=0;
			foreach ($keyedheaders[][] as $keyedheader[]) {
				$sql .= "(";
				foreach ($keyedheader as $key=>$value)
				{
					$sql .= " and `".$keyedheaders[$j][$key]."` = '".$data->sheets[0]['cells'][$i][$keyedindex[$j][$key]]."'";
				}
				$sql .= ") OR ";
				$j++;
			}
			
			$sql = substr($sql, 0, strlen($sql)-4) . ';';
			$results = mysql_query($sql);
			
			echo '<tr>';
			
			foreach ($values as $value) {
				echo '<td>'.$value.'</td>';
			}
			
			if (mysql_num_rows($results)) {
				$dbstatus = "UPDATE";
				$sql = "UPDATE donors SET";
				
				foreach ($values as $key=>$value) {
					if ($values[$key] && !in2darray($keyedheaders,$etlheaders[$key])) {
						$sql .= ' `'.$etlheaders[$key].'` = \''.$values[$key].'\',';
					}
				}
				
				$sql = substr($sql, 0, strlen($sql)-1);
				$sql .= " WHERE 1=1";
				
				foreach ($keyedheaders as $key=>$value)
				{
					$sql .= " and `".$keyedheaders[$key]."` = '".$data->sheets[0]['cells'][$i][$keyedindex[$key]]."'";
				}
				$sql .= ';';
			}
			else {
				$dbstatus = "INSERT";
				$sql = "INSERT INTO donors VALUES";
				
				foreach ($values as $key=>$value) {
					if ($values[$key]) {
						$sql .= ' `'.$etlheaders[$key].'` = \''.$values[$key].'\',';
					}
				}
				$sql = substr($sql, 0, strlen($sql)-1) . ';';
			}
			echo '<td>'.$dbstatus.'</td><INPUT TYPE="HIDDEN" NAME="sqls[]" VALUE="'.$sql.'">';
			echo '</tr>';
		}
		echo '<tfoot><th>Keys:</th>';
		foreach ($keyedheaders as $key) {
			echo '<th>'.$key.'</th>';
		}
		echo '</tfoot></table>
		<INPUT TYPE="SUBMIT" VALUE="Process ETL" NAME="submit" class="btn">
		<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
		</form>';
	}

	include "footer.html";
	
	echo '
		</body>
		</html>
	';

	function in2dArray($array, $search)
	{
		foreach($array as $value)
		{
			if(is_array($value))
			{
				if(inMyArray($value, $search))
				{
					turn(TRUE);
				}
			}
			elseif($value == $search)
			{
				return(TRUE);
			}
		}
		return(FALSE);
	}
?>