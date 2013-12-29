<?php

###############################################################
# Page Password Protect 2.13
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
############################################################### 
#
# Usage:
# Set usernames / passwords below between SETTINGS START and SETTINGS END.
# Open it in browser with "help" parameter to get the code
# to add to all files being protected. 
#    Example: password_protect.php?help
# Include protection string which it gave you into every file that needs to be protected
#
# Add following HTML code to your page where you want to have logout link
# <a href="http://www.example.com/path/to/protected/page.php?logout=1">Logout</a>
#
###############################################################

##################################################################
#  SETTINGS START
##################################################################

// Add login/password pairs below, like described above
// NOTE: all rows except last must have comma "," at the end of line
$LOGIN_INFORMATION = array();

// request login? true - show login and password boxes, false - password box only
define('USE_USERNAME', true);

// User will be redirected to this page after logout
define('LOGOUT_URL', 'http://www.lenichoir.org');

// time out after NN minutes of inactivity. Set to 0 to not timeout
define('TIMEOUT_MINUTES', 0);

// This parameter is only useful when TIMEOUT_MINUTES is not zero
// true - timeout time from last activity, false - timeout time from login
define('TIMEOUT_CHECK_ACTIVITY', true);

// Password reset page
define('PASSWORD_RESET_URL','password_expired.php');

##################################################################
#  SETTINGS END
##################################################################


///////////////////////////////////////////////////////
// do not change code below
///////////////////////////////////////////////////////

// show usage example
if(isset($_GET['help'])) {
  die('Include following code into every page you would like to protect, at the very beginning (first line):<br>&lt;?php include("' . str_replace('\\','\\\\',__FILE__) . '"); ?&gt;');
}

// timeout in seconds
$timeout = (TIMEOUT_MINUTES == 0 ? 0 : time() + TIMEOUT_MINUTES * 60);

// logout?
if(isset($_GET['logout'])) {
  setcookie("verify", '', $timeout, '/'); // clear password;
  header('Location: ' . LOGOUT_URL);
  exit();
}

if (isset($_COOKIE['password_expired'])) {
	header('Location: '. PASSWORD_RESET_URL);
	exit();
}

if(!function_exists('showLoginPasswordProtect')) {

// show login form
function showLoginPasswordProtect($error_msg) {
?>
 
<html>
<head>
<link href="blue.css" rel="stylesheet" type="text/css">
</head>
<body>
<form method="post">
	<p align="center">
	<img src="logo.jpg"><br>
	<table width="300">
		<thead><tr><th colspan=3>Login</th></tr></thead>
		<tr><td width="78">Username</td><td width="6">:</td><td width="294"><input name="access_login" type="text" id="access_login"></td></tr>
		<tr><td>Password</td><td>:</td><td><input name="access_password" type="password" id="access_password"></td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td><td><input type="submit" name="Submit" value="Login" class="btn"></td></tr>
		<?php
			if ($error_msg) {
				echo '<tr><td colspan=3 align="center"><font color="red">'.$error_msg.'</font></td></tr>';	
			}
		?>
	</table>
	<?php 
		if ($error_msg) {
			echo '<p align="center" style="font-size:12px;"><a href="reset_password.php">Forgot your password?</a></p>';	
		}
	?>
</form>
<?php
include_once 'footer.php';
?>
</body>
</html>
<?php
  // stop at this point
  die();
}
}

// user provided password
if (isset($_POST['access_password'])) {

  include_once 'dbinfo.php';

  $login = isset($_POST['access_login']) ? $_POST['access_login'] : '';
  $pass = $_POST['access_password'];
  $md5pass = md5($pass);
  $query = "select * from refusertb where username = '$login' and password = '$md5pass' and user_active = 'Y'";
  $result = mysql_query($query) or die("Errors: ".mysql_error());
  
  $user = mysql_fetch_assoc($result);

  if (!$user) {
    showLoginPasswordProtect("Incorrect username or password.");
  }
  elseif ($user['password_expired'] == 'Y') {
  	setcookie("nichoir_username",$login,$timeout,'/');
  	setcookie("password_expired",'Y',$timeout,'/');
  	header('Location: '. PASSWORD_RESET_URL);
  	exit();
  }
  else {
  	// get group entitlements
  	$query = "select * from refactiveusersvw where username = '$login'";
  	$result = mysql_query($query) or die("Errors: " . mysql_error());
  	$groupvw = mysql_fetch_assoc($result);
    // set cookie if password was validated
    setcookie("verify", md5($login.'%'.$pass), $timeout, '/');
	setcookie("nichoir_username",$login,$timeout,'/');
    setcookie("group_name",$groupvw['group_name'],$timeout,'/');
    // Some programs (like Form1 Bilder) check $_POST array to see if parameters passed
    // So need to clear password protector variables
    unset($_POST['access_login']);
    unset($_POST['access_password']);
    unset($_POST['Submit']);
  }
}

else {
  // check if password cookie is set
  if (!isset($_COOKIE['verify'])) {
    showLoginPasswordProtect("");
  }
}

?>
