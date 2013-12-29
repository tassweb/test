<?php include 'password_protect.php'; ?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
include 'dbinfo.php';

echo 'ok';

?>
<?php
	mysql_close($connection); 
?>

</body>
</html>
