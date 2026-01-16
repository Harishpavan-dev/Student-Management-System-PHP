<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to login
header("Location: ../../Login/hod_login.php");
exit;
