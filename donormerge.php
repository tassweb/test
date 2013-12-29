<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<SCRIPT>
function submitConfirm (form,sourceValue) {
  var targetValue = form.target_donor_id.value;
  if(confirm('Are you sure you want to merge Donor ID: '+sourceValue+' into Donor ID: '+targetValue+'? ('+sourceValue+' will be lost)')) {
	form.submit();
  }
}
  </SCRIPT>
<?php
	include 'dbinfo.php';
	foreach($_GET as $key => $value) {$$key = $value;}

	$query = "SELECT * FROM dimdonortb WHERE donor_id = '$donor_id' and current_date between from_date and to_date";
	$donor = mysql_query($query) or die("Errors: ".mysql_error());
	$donorinfo = mysql_fetch_array($donor);
	$columns = array();

	echo '
		<form method="post" action="donormergeupdate.php">
		<table border=1>';
	if ($donor_id) {
		echo'<caption>'.$donorinfo['last_name'].', '.$donorinfo['first_name'].' (Donor ID '.$donorinfo['donor_id'].' will disappear)</caption>';
	}

	
  	echo '<tr><td>Enter Target Donor ID (we\'ll keep this guy ->):
    	<INPUT TYPE="HIDDEN" NAME="donor_id" VALUE="'.$donor_id.'">
    	<INPUT TYPE="TEXTBOX" NAME="target_donor_id">
    	<INPUT TYPE="button" VALUE="Merge" NAME="merge" class="btn" onClick="return submitConfirm(this.form,'.$donor_id.')">
    	<INPUT TYPE="button" VALUE="Back" class="btn" onClick="history.go(-1);return true;"> 
    	</form></td></tr>
		<tr><td>Note: This will merge the current donor with the target donor, deleting the current donor and moving all donations under the target donor.  Ties will be resolved using the target donor details.
    	</td></tr>
		<tr><td><b>THIS CANNOT BE UNDONE!</b></td></tr></table>';
    	mysql_free_result($donor);

    require_once 'footer.php';
    	
	mysql_close($connection); 
?>

</body>
</html>
