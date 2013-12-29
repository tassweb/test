<?php 

	require_once 'dbfunctions.php';
	require_once 'dbinfo.php';
	
	function get_revenue_stream() {
		$resultArr = array();
		$query = 'select distinct revenue_stream from dimcategorytb where current_date between from_date and to_date';
		$resultsTb = mysql_query($query) or DIE("Error: " . mysql_error());
		while ($row = mysql_fetch_assoc($resultsTb)) {
			array_push($resultArr,$row['revenue_stream']);
		}
		return $resultArr;
	}
	
	function get_type($revenue_stream_) {
		$resultArr = array();
		$query = 'select distinct type from dimcategorytb where current_date between from_date and to_date and revenue_stream = \'' . $revenue_stream_ . '\'';
		$resultsTb = mysql_query($query) or DIE("Error: " . mysql_error());
		while ($row = mysql_fetch_assoc($resultsTb)) {
			array_push($resultArr,$row['type']);
		}
		return $resultArr;
	}
	
	function get_origin($revenue_stream_, $type_) {
		$resultArr = array();
		$query = 'select distinct origin from dimcategorytb where current_date between from_date and to_date and revenue_stream = \'' . $revenue_stream_ . '\' and type = \'' . $type_ . '\'';
		$resultsTb = mysql_query($query) or DIE("Error: " . mysql_error());
		while ($row = mysql_fetch_assoc($resultsTb)) {
			array_push($resultArr,$row['origin']);
		}
		return $resultArr;
	}
	
	foreach ($_GET as $key => $value) {
		$$key = $value;
	}
	
	$preset = isset($preset) ? true : false;
	
	if ((isset($type) && isset($revenue_stream) && !$preset) || (isset($type) && isset($revenue_stream) && isset($origin) && $preset)){
		$originArr = get_origin($revenue_stream,$type);
		echo '<select name="origin" id="origin">
								<option></option>
						';
		foreach ($originArr as $value) {
			$selected = ($preset && ($value == $origin)) ? 'selected="selected"' : '';
			echo '<option value='.$value.' '.$selected.'>' . $value . '</option>';
		}
		echo '</select>';
	}
	elseif ((isset($revenue_stream) && !$preset) || (isset($type) && isset($revenue_stream) && $preset)){
		$typeArr = get_type($revenue_stream);
		echo '<select name="type" id="type" onchange="getOrigin(\''.$revenue_stream.'\',this.value);">
						<option></option>
				';
		foreach ($typeArr as $value) {
			$selected = ($preset && ($value == $type)) ? 'selected="selected"' : '';
			echo '<option value='.$value.' '.$selected.'>' . $value . '</option>';
		}
		echo '</select>';
	}
	else {
		$revenueStreamArr = get_revenue_stream();
		echo '<select name="revenue_stream" id="revenue_stream" onchange="getType(this.value);">
				<option></option>
		';
		
		foreach ($revenueStreamArr as $value) {
			$selected = ($preset && ($value == $revenue_stream)) ? 'selected="selected"' : '';
			echo '<option value='.$value.' '.$selected.'>' . $value . '</option>';
		}
		echo '</select>';
	}
?>