<?php
$host = 'localhost';
$username = 'root';
$password = ''; // default for XAMPP
$dbname = 'photography_website'; // Replace with your actual DB name from phpMyAdmin

$conn = mysqli_connect($host, $username, $password, $dbname);

// Check if connection works
if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
}
?>


