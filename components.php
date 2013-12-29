<?php include 'password_protect.php'; ?>
<html>
<head>
<SCRIPT SRC="combobox.js">
/* **** PAGE-SPECIFIC LOADER **** */
		function pageInit() {
			supercombo_1 = new TypeAheadCombo("components",true);
		}
		window.onload = pageInit;
</SCRIPT>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	include 'dbinfo_test.php';
	foreach($_GET as $key => $value) {$$key = $value;}

	$query = "SELECT cid, component_level_1, component_level_2, component_level_3, component_level_4 FROM dimcomponenttb WHERE current_date between from_date and to_date order by 2,3,4,5";
	$componentTb = mysql_query($query) or die("Errors: ".mysql_error());
	//$componentTb = mysql_fetch_assoc($results);
	

	echo '
		<form method="post">
		<table border=1>';
	if ($donor_id) {
		echo'<caption>Combobox test</caption>';
	}
	echo '<thead><tr>Components</tr></thead><tr>
		  <tr><td><select name="components" id="components">
		  		<option></option>';
	echo "\n";
	while ($componentArr = mysql_fetch_assoc($componentTb)) {
		$value = $componentArr["cid"];
		$componentString = $componentArr["component_level_1"] . " | "
						 . $componentArr["component_level_2"] . " | "
						 . $componentArr["component_level_3"] . " | "
						 . $componentArr["component_level_4"];
		echo '<option value="'.$value.'">'.$componentString.'</option>';
		echo "\n";
	}
	echo '
	</td></tr></table>
	</form>';
	mysql_free_result($componentTb);

	include 'footer.php';
	
	mysql_close($connection); 
?>

</body>
</html>
