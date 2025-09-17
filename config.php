<?php
// Database credentials
define('DB_SERVER', 'localhost');  // Change to your DB server
define('DB_USERNAME', 'root');     // Change to your DB username
define('DB_PASSWORD', '');         // Change to your DB password
define('DB_NAME', 'photography_website'); // Change to your database name

// Establish a database connection
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>

<?php
$mysqli = new mysqli('localhost', 'root', '', 'photography_website');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
