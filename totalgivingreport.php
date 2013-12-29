<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
include 'dbinfo.php';

foreach($_GET as $key => $value) {$$key = $value;}
foreach($_POST as $key => $value) {$$key = $value;}

$columns = mysql_query("SHOW COLUMNS FROM donors");
$i=0;
while ($column_names = mysql_fetch_array($columns))
{
	$donorheaders[$i] = $column_names[0];
	$i++;
}

if (!$sort) {$sort=0;}

$donations = array();
$totaldons = array();
$totaldons07 = array();
$totaldons08 = array();
$totaldons09 = array();

$query = "SELECT donors.`Donor ID` AS id, SUM(donations.`Donation Amount`) AS totaldonations FROM donors INNER JOIN donations ON donors.`Donor ID`=donations.`Donors` GROUP BY donors.`Donor ID`;";
$result = mysql_query($query) or die("Errors: ".mysql_error());
while ($donations = mysql_fetch_array($result))
{
	$totaldons[$donations['id']] = $donations['totaldonations'];
}


$query = "SELECT donors.`Donor ID` AS id, SUM(donations.`Donation Amount`) AS totaldonations FROM donors INNER JOIN donations ON donors.`Donor ID`=donations.`Donors` WHERE donations.`Donation Date` BETWEEN '2007/01/01' AND '2007/12/31' GROUP BY donors.`Donor ID`;";
$result = mysql_query($query) or die("Errors: ".mysql_error());
while ($donations = mysql_fetch_array($result))
{
	$totaldons07[$donations['id']] = $donations['totaldonations'];
}

$query = "SELECT donors.`Donor ID` AS id, SUM(donations.`Donation Amount`) AS totaldonations FROM donors INNER JOIN donations ON donors.`Donor ID`=donations.`Donors` WHERE donations.`Donation Date` BETWEEN '2008/01/01' AND '2008/12/31' GROUP BY donors.`Donor ID`;";
$result = mysql_query($query) or die("Errors: ".mysql_error());
while ($donations = mysql_fetch_array($result))
{
	$totaldons08[$donations['id']] = $donations['totaldonations'];
}

$query = "SELECT donors.`Donor ID` AS id, SUM(donations.`Donation Amount`) AS totaldonations FROM donors INNER JOIN donations ON donors.`Donor ID`=donations.`Donors` WHERE donations.`Donation Date` BETWEEN '2009/01/01' AND '2009/12/31' GROUP BY donors.`Donor ID`;";
$result = mysql_query($query) or die("Errors: ".mysql_error());
while ($donations = mysql_fetch_array($result))
{
	$totaldons09[$donations['id']] = $donations['totaldonations'];
}

$query = "SELECT * FROM donors ORDER BY `$donorheaders[$sort]` ASC";
$result = mysql_query($query) or die("Errors: ".mysql_error());

if (mysql_num_rows($result) > 0) {

    echo "<table border=1>";
    echo "<caption>Donors</caption>";
    echo '
    <tr><thead>';
    
    $columns = mysql_query("SHOW COLUMNS FROM donors");
    $i=0;
    while ($column_names = mysql_fetch_array($columns))
    {
    	echo '
    	<th scope="col" onmouseover="this.style.cursor=\'pointer\'" onclick="window.location = \'totalgivingreport.php?sort='.$i.'\'">'.$column_names[0].'</th>';
    	$i++;
    }
    
    // total donations column
    echo '
    <th scope="col">Total Donations 07</th>';
    echo '
    <th scope="col">Total Donations 08</th>';
    echo '
    <th scope="col">Total Donations 09</th>';
    echo '
    <th scope="col">Total Donations</th>';
    
    echo '
    </tr></thead>
    <tr>
    <td colspan="100%"><form method="post" action="totalgivingreport.php">
    Search: <input type="textbox" name="searchkey" value="'.$searchkey.'">
    <input type="submit" value="Search" class="btn">
    </form></td></tr>
    ';
    $j=0;
    while ($row = mysql_fetch_array($result)) {
    	$temparr = array_map(strtolower, $row);
    	if (!array_search(strtolower($searchkey), $temparr) && $searchkey) { continue; }
        echo '<tr onmouseover="this.style.cursor=\'pointer\'" onclick="window.location = \'donor.php?donor_id='.$row[0].'\'">';

        for ($i=0; $i<count($row)/2; $i++)
        {
        	echo "<td>";
		$rowout = $row[$i];
        	if (is_numeric($row[$i]) && ($i)) {
			if ($rowout) { $rowout = "<b>Yes</b>"; }
			else { $rowout = "No"; }
		}	
		echo $rowout.'</td>';
        }
        if (!($rowout = $totaldons07[$row[0]]))
        	$rowout = 0;
	$rowout = number_format($rowout,2);
        echo '<td>$'.$rowout.'</td>';
        if (!($rowout = $totaldons08[$row[0]]))
        	$rowout = 0;
	$rowout = number_format($rowout,2);
        echo '<td>$'.$rowout.'</td>';
        if (!($rowout = $totaldons09[$row[0]]))
        	$rowout = 0;
	$rowout = number_format($rowout,2);
        echo '<td>$'.$rowout.'</td>';
        if (!($rowout = $totaldons[$row[0]]))
        	$rowout = 0;
	$rowout = number_format($rowout,2);
        echo '<td><Strong>$'.$rowout.'</strong></td>';
        echo '</tr>
        ';
        $j++;
    }
}
if(!$j) { echo '<tr><td colspan="100%">No results found!</td></tr>'; }
echo "</table>";


mysql_free_result($result);
?>

<p align=center><table border=1>
 <tr>
    <td><div align="center"><span class="copyright">© 2009 Le Nichoir<br>
  Wild Bird Rehabilitation<br>
  637 Main Road, Hudson, QC 450 458 2809<br>
    <a href="mailto:info@lenichoir.org" class="copyright">info@lenichoir.org</a><br>
</td>
  </tr>
</table></p>



<?php
	mysql_close($connection); 
?>

</body>
</html>
