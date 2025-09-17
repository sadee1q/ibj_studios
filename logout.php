<?php
session_start();

session_start();
session_destroy();
header("Location: gallery_access.php");

// Destroy session to log out the user
session_unset();
session_destroy();

header("Location: login.php"); // Redirect to login page
exit;

