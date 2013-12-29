<?php
	
	setcookie("verify", $_COOKIE['verify'], time() - 3600, "/");
	setcookie("nichoir_username", $_COOKIE['nichoir_username'], time() - 3600, "/");
    setcookie("group_name", $_COOKIE['group_name'], time() - 3600, "/");
    setcookie("password_expired", 'Y', time() - 3600, "/");
    header( 'Location: index.php') ;
?>