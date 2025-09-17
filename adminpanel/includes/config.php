

<?php
// DB
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "photography_website";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// SMTP (optional centrally here; keep secure)
$smtp_host   = 'smtp.gmail.com';
$smtp_port   = 465;               // or 587 for TLS
$smtp_secure = 'ssl';            // 'ssl' for 465, 'tls' for 587
$smtp_user   = 'ajibrilla294@gmail.com';
$smtp_pass   = 'dkosguovcajsuoyg';  // <--- use app password; keep this file secure!
$admin_email = 'ajibrilla294@gmail.com'; // who should receive frontend notifications
