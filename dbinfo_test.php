<?php
$host = "localhost";
$user = "lenichoir";
$pass = "lenichoir";
$db = "lenichoir_test";
$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect");
mysql_select_db($db) or die ("Can't connect to DB");

?>