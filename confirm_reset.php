<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

	require_once ('dbfunctions.php');
	require_once ('acl.php');
	include 'dbinfo.php';
	
	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	foreach($_GET as $key => $value) {
		$$key = $value;
	}
	
	if (isset($resethash) && isset($user_id)) {
		$query = "select username,email,user_active from refusertb where user_id = $user_id";
		$result = mysql_query($query) or die("Error: " . mysql_error());
		$row = mysql_fetch_assoc($result);
		
		$resethashverify = hashUserEmail($row['username'], $hashsalt,  $row['email']);
		
		if ($resethashverify != $resethash) {
			echo '<table>
				<tr><td>Password reset failed (invalid hash).  Please contact Jason</td></tr>
				</table>';
			
			require_once 'footer.php';
			die(1);
		}
		
		$password = generatePassword(8);
		$newpassword = md5($password);
		$query = "update refusertb set password = '$newpassword', password_expired = 'Y' where user_id = $user_id";
		mysql_query($query) or die ("Error: " . mysql_error());
		
		$message = "Hi $username,<br><br>Your Le Nichoir DMS password has been reset.  Your new temporary password is: <br><br> $password<br><br>"
		."Please contact <a href=\"mailto:info@lenichoir.org\">info@lenichoir.org</a> if you have problems using your new temporary password.<br><br>"
		."<a href=\"https://www2.tassweb.com/lenichoir\">Le Nichoir DMS</a>";
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From: info@lenichoir.org' . "\r\n" .
		    'Reply-To: info@lenichoir.org' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion() .
		
		
		mail($row['email'],'Le Nichoir Password Reset',$message,$headers);
		
		echo '<table>
			<tr><td>Your password has been reset and your temporary password has been emailed to '.$row['email'].'</td></tr>
			<tr><td><INPUT TYPE="button" value="Return to login" onClick="location.href=\'index.php\'" class="btn"></td></tr></table>';
	}
	else {
		$query = "select user_id, email, user_active from refusertb where username = '$username'";
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		
		if ($row['user_active'] != 'Y') {
			echo '<table>
						<tr><td>Username inactive.  Please contact Jason</td></tr>
						</table>';
		
			require_once 'footer.php';
			die(1);
		}
		
		$resethashverify = hashUserEmail($username, $hashsalt, $row['email']);
		
		$user_id = $row['user_id'];
		
		$message = "Hi $username,<br><br>A password reset for your Le Nichoir DMS username has been requested.  If you did not request this, please ignore this email.  This link is only valid for the current date.<br><br>
				To reset your Le Nichoir DMS password, please follow the link below (or copy/paste it into your browser).<br><br>
				 <a href=\"https://www2.tassweb.com/lenichoir/confirm_reset.php?user_id=$user_id&resethash=$resethashverify\">https://www2.tassweb.com/lenichoir/confirm_reset.php?user_id=$user_id&resethash=$resethashverify</a><br><br>"
		."<a href=\"https://www2.tassweb.com/lenichoir\">Le Nichoir DMS</a>";
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From: info@lenichoir.org' . "\r\n" .
		    'Reply-To: info@lenichoir.org' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion() .
		
		
		mail($row['email'],'Le Nichoir Password Reset',$message,$headers);
		
		echo '<table>
				<tr><td>Password reset request for '.$username.' has been submitted.  Please check your email to authenticate your reset request.</td></tr>
				</table>';		
	}
	require_once 'footer.php';
?>