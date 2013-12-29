<?php 	
	require_once ('password_protect.php');
	$group_name = $_COOKIE['group_name'];
	if ($group_name != 'Administrator') {
		header( 'Location: index.php') ;
	}
?>
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

	require_once ('dbfunctions.php');
	require_once ('acl.php');
	include 'dbinfo.php';
	
	$query = 'select * from refgrouptb';
	echo '
		<table>
		<form action="createlogin.php" method="POST">
		<thead><tr><th colspan=2>New login</th></tr></thead>
		<tr><td>New Username</td><td><INPUT TYPE="textbox" name="username" size="50"></td></tr>
		<tr><td>New Password</td><td><INPUT TYPE="password" name="password" size="50"></td></tr>
	    <tr><td>Confirm Password</td><td><INPUT TYPE="password" name="password2" size="50"></td></tr>';
	echo '<tr><td>Access</td><td>' . buildGenericDropdownTable($dropColumns['group_name'],'','group_name') . '</td></tr>';
	echo '<tr><td colspan=2><INPUT TYPE="submit" class="btn"></td></tr>';
	echo '</form>
		  </table>
	';
?>